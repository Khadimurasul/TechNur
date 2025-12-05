import express from 'express';
import cors from 'cors';
import sqlite3 from 'sqlite3';
import { promisify } from 'util';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';
import nodemailer from 'nodemailer';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import multer from 'multer';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({ path: path.join(__dirname, '.env') });

const app = express();
const PORT = process.env.PORT || 3001;
const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key-change-this-in-production';
const DEFAULT_ADMIN_USERNAME = process.env.ADMIN_USERNAME || 'Khadimurasul';
const DEFAULT_ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || 'YardKing@1';

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Database setup
const dbPath = path.join(__dirname, 'database.sqlite');
const db = new sqlite3.Database(dbPath);

// Uploads directory setup
const uploadsDir = path.join(__dirname, 'uploads');
if (!fs.existsSync(uploadsDir)) {
  fs.mkdirSync(uploadsDir, { recursive: true });
}

// Multer configuration for CV uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, uploadsDir);
  },
  filename: (req, file, cb) => {
    // Always save as cv.pdf (replace if exists)
    cb(null, 'cv.pdf');
  },
});

const upload = multer({
  storage: storage,
  limits: {
    fileSize: 10 * 1024 * 1024, // 10MB limit
  },
  fileFilter: (req, file, cb) => {
    // Only allow PDF files
    if (file.mimetype === 'application/pdf') {
      cb(null, true);
    } else {
      cb(new Error('Only PDF files are allowed'), false);
    }
  },
});

// Promisify database methods
const dbRun = promisify(db.run.bind(db));
const dbGet = promisify(db.get.bind(db));
const dbAll = promisify(db.all.bind(db));

// Initialize database tables
async function initDatabase() {
  // Messages table
  await dbRun(`
    CREATE TABLE IF NOT EXISTS messages (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      email TEXT NOT NULL,
      message TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      read BOOLEAN DEFAULT 0
    )
  `);

  // Projects table
  await dbRun(`
    CREATE TABLE IF NOT EXISTS projects (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      title TEXT NOT NULL,
      description TEXT NOT NULL,
      tech TEXT NOT NULL,
      repoUrl TEXT,
      liveUrl TEXT,
      imageUrl TEXT,
      featured BOOLEAN DEFAULT 0,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Admin users table
  await dbRun(`
    CREATE TABLE IF NOT EXISTS admin_users (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      username TEXT UNIQUE NOT NULL,
      password TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // CV table
  await dbRun(`
    CREATE TABLE IF NOT EXISTS cv_files (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      filename TEXT NOT NULL,
      original_filename TEXT NOT NULL,
      file_size INTEGER NOT NULL,
      uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
  `);

  // Ensure default admin user exists with configured credentials
  const desiredUser = await dbGet('SELECT id FROM admin_users WHERE username = ?', [DEFAULT_ADMIN_USERNAME]);
  const hashedPassword = await bcrypt.hash(DEFAULT_ADMIN_PASSWORD, 10);
  if (!desiredUser) {
    const existingAdmin = await dbGet('SELECT id FROM admin_users WHERE username = ?', ['admin']);
    if (existingAdmin) {
      await dbRun('UPDATE admin_users SET username = ?, password = ? WHERE id = ?', [DEFAULT_ADMIN_USERNAME, hashedPassword, existingAdmin.id]);
    } else {
      await dbRun('INSERT INTO admin_users (username, password) VALUES (?, ?)', [DEFAULT_ADMIN_USERNAME, hashedPassword]);
    }
    console.log(`Default admin ensured: username=${DEFAULT_ADMIN_USERNAME}`);
  } else {
    // Update password if environment provides one
    await dbRun('UPDATE admin_users SET password = ? WHERE id = ?', [hashedPassword, desiredUser.id]);
  }
}

// Email transporter setup
const createTransporter = () => {
  return nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.GMAIL_USER,
      pass: process.env.GMAIL_APP_PASSWORD, // Use App Password, not regular password
    },
  });
};

// Middleware to verify JWT token
const authenticateToken = (req, res, next) => {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    return res.status(401).json({ error: 'Access token required' });
  }

  jwt.verify(token, JWT_SECRET, (err, user) => {
    if (err) {
      return res.status(403).json({ error: 'Invalid or expired token' });
    }
    req.user = user;
    next();
  });
};

// Routes

// Health check
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', message: 'Server is running' });
});

// Submit message
app.post('/api/messages', async (req, res) => {
  try {
    const { name, email, message } = req.body;

    if (!name || !email || !message) {
      return res.status(400).json({ error: 'Name, email, and message are required' });
    }

    // Save to database
    const result = await dbRun(
      'INSERT INTO messages (name, email, message) VALUES (?, ?, ?)',
      [name, email, message]
    );

    // Send email notification
    try {
      const transporter = createTransporter();
      const mailOptions = {
        from: process.env.GMAIL_USER,
        to: process.env.GMAIL_USER, // Your email
        subject: `New Message from ${name} - Portfolio Contact Form`,
        html: `
          <h2>New Message Received</h2>
          <p><strong>Name:</strong> ${name}</p>
          <p><strong>Email:</strong> ${email}</p>
          <p><strong>Message:</strong></p>
          <p>${message.replace(/\n/g, '<br>')}</p>
          <hr>
          <p><small>Received at: ${new Date().toLocaleString()}</small></p>
        `,
      };

      await transporter.sendMail(mailOptions);
    } catch (emailError) {
      console.error('Email sending failed:', emailError);
      // Don't fail the request if email fails
    }

    res.json({ 
      success: true, 
      message: 'Message sent successfully',
      id: result.lastID 
    });
  } catch (error) {
    console.error('Error submitting message:', error);
    res.status(500).json({ error: 'Failed to send message' });
  }
});

// Admin login
app.post('/api/admin/login', async (req, res) => {
  try {
    const { username, password } = req.body;

    if (!username || !password) {
      return res.status(400).json({ error: 'Username and password are required' });
    }

    const user = await dbGet('SELECT * FROM admin_users WHERE username = ?', [username]);
    
    if (!user) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const isValidPassword = await bcrypt.compare(password, user.password);
    
    if (!isValidPassword) {
      return res.status(401).json({ error: 'Invalid credentials' });
    }

    const token = jwt.sign({ id: user.id, username: user.username }, JWT_SECRET, {
      expiresIn: '24h',
    });

    res.json({ success: true, token, username: user.username });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ error: 'Login failed' });
  }
});

// Get all messages (admin only)
app.get('/api/admin/messages', authenticateToken, async (req, res) => {
  try {
    const messages = await dbAll('SELECT * FROM messages ORDER BY created_at DESC');
    res.json({ success: true, messages });
  } catch (error) {
    console.error('Error fetching messages:', error);
    res.status(500).json({ error: 'Failed to fetch messages' });
  }
});

// Mark message as read (admin only)
app.patch('/api/admin/messages/:id/read', authenticateToken, async (req, res) => {
  try {
    await dbRun('UPDATE messages SET read = 1 WHERE id = ?', [req.params.id]);
    res.json({ success: true });
  } catch (error) {
    console.error('Error updating message:', error);
    res.status(500).json({ error: 'Failed to update message' });
  }
});

// Delete message (admin only)
app.delete('/api/admin/messages/:id', authenticateToken, async (req, res) => {
  try {
    await dbRun('DELETE FROM messages WHERE id = ?', [req.params.id]);
    res.json({ success: true });
  } catch (error) {
    console.error('Error deleting message:', error);
    res.status(500).json({ error: 'Failed to delete message' });
  }
});

// Get all projects (public)
app.get('/api/projects', async (req, res) => {
  try {
    const projects = await dbAll('SELECT * FROM projects ORDER BY created_at DESC');
    res.json({ success: true, projects });
  } catch (error) {
    console.error('Error fetching projects:', error);
    res.status(500).json({ error: 'Failed to fetch projects' });
  }
});

// Get single project (public)
app.get('/api/projects/:id', async (req, res) => {
  try {
    const project = await dbGet('SELECT * FROM projects WHERE id = ?', [req.params.id]);
    if (!project) {
      return res.status(404).json({ error: 'Project not found' });
    }
    res.json({ success: true, project });
  } catch (error) {
    console.error('Error fetching project:', error);
    res.status(500).json({ error: 'Failed to fetch project' });
  }
});

// Create project (admin only)
app.post('/api/admin/projects', authenticateToken, async (req, res) => {
  try {
    const { title, description, tech, repoUrl, liveUrl, imageUrl, featured } = req.body;

    if (!title || !description || !tech) {
      return res.status(400).json({ error: 'Title, description, and tech are required' });
    }

    const techString = Array.isArray(tech) ? tech.join(',') : tech;
    const result = await dbRun(
      'INSERT INTO projects (title, description, tech, repoUrl, liveUrl, imageUrl, featured) VALUES (?, ?, ?, ?, ?, ?, ?)',
      [title, description, techString, repoUrl || null, liveUrl || null, imageUrl || null, featured ? 1 : 0]
    );

    res.json({ success: true, id: result.lastID });
  } catch (error) {
    console.error('Error creating project:', error);
    res.status(500).json({ error: 'Failed to create project' });
  }
});

// Update project (admin only)
app.put('/api/admin/projects/:id', authenticateToken, async (req, res) => {
  try {
    const { title, description, tech, repoUrl, liveUrl, imageUrl, featured } = req.body;

    const techString = Array.isArray(tech) ? tech.join(',') : tech;
    await dbRun(
      'UPDATE projects SET title = ?, description = ?, tech = ?, repoUrl = ?, liveUrl = ?, imageUrl = ?, featured = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
      [title, description, techString, repoUrl || null, liveUrl || null, imageUrl || null, featured ? 1 : 0, req.params.id]
    );

    res.json({ success: true });
  } catch (error) {
    console.error('Error updating project:', error);
    res.status(500).json({ error: 'Failed to update project' });
  }
});

// Delete project (admin only)
app.delete('/api/admin/projects/:id', authenticateToken, async (req, res) => {
  try {
    await dbRun('DELETE FROM projects WHERE id = ?', [req.params.id]);
    res.json({ success: true });
  } catch (error) {
    console.error('Error deleting project:', error);
    res.status(500).json({ error: 'Failed to delete project' });
  }
});

// Upload CV (admin only)
app.post('/api/admin/cv/upload', authenticateToken, upload.single('cv'), async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ error: 'No file uploaded' });
    }

    // Delete old CV record if exists
    await dbRun('DELETE FROM cv_files');

    // Save new CV record
    await dbRun(
      'INSERT INTO cv_files (filename, original_filename, file_size) VALUES (?, ?, ?)',
      [req.file.filename, req.file.originalname, req.file.size]
    );

    res.json({
      success: true,
      message: 'CV uploaded successfully',
      filename: req.file.filename,
      originalFilename: req.file.originalname,
      fileSize: req.file.size,
    });
  } catch (error) {
    console.error('Error uploading CV:', error);
    res.status(500).json({ error: error.message || 'Failed to upload CV' });
  }
});

// Get CV info (admin only)
app.get('/api/admin/cv/info', authenticateToken, async (req, res) => {
  try {
    const cvInfo = await dbGet('SELECT * FROM cv_files ORDER BY uploaded_at DESC LIMIT 1');
    res.json({ success: true, cv: cvInfo || null });
  } catch (error) {
    console.error('Error fetching CV info:', error);
    res.status(500).json({ error: 'Failed to fetch CV info' });
  }
});

// Download CV (public)
app.get('/api/cv/download', async (req, res) => {
  try {
    const cvInfo = await dbGet('SELECT * FROM cv_files ORDER BY uploaded_at DESC LIMIT 1');
    
    if (!cvInfo) {
      return res.status(404).json({ error: 'CV not found' });
    }

    const filePath = path.resolve(uploadsDir, cvInfo.filename);
    
    if (!fs.existsSync(filePath)) {
      return res.status(404).json({ error: 'CV file not found' });
    }

    res.setHeader('Content-Type', 'application/pdf');
    res.setHeader('Content-Disposition', `attachment; filename="${cvInfo.original_filename}"`);
    res.sendFile(filePath);
  } catch (error) {
    console.error('Error downloading CV:', error);
    res.status(500).json({ error: 'Failed to download CV' });
  }
});

// Get CV info (public - just to check if CV exists)
app.get('/api/cv/info', async (req, res) => {
  try {
    const cvInfo = await dbGet('SELECT filename, original_filename, uploaded_at FROM cv_files ORDER BY uploaded_at DESC LIMIT 1');
    res.json({ success: true, cv: cvInfo || null });
  } catch (error) {
    console.error('Error fetching CV info:', error);
    res.status(500).json({ error: 'Failed to fetch CV info' });
  }
});

// Start server
initDatabase()
  .then(() => {
    app.listen(PORT, () => {
      console.log(`Server running on http://localhost:${PORT}`);
      console.log(`Default admin username: ${DEFAULT_ADMIN_USERNAME}`);
      console.log('Please change the default password after first login!');
    });
  })
  .catch((error) => {
    console.error('Failed to initialize database:', error);
    process.exit(1);
  });

export default app;

