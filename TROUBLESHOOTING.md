# Troubleshooting Guide

## "Failed to fetch" or "Login failed" Error

If you're getting a "Failed to fetch" error when trying to login, follow these steps:

### Step 1: Check if Backend Server is Running

The backend server must be running for the admin panel to work.

**Check if server is running:**
- Open a terminal/command prompt
- Navigate to your project directory
- Run: `npm run dev:server`
- You should see: `Server running on http://localhost:3001`

**If server is not running:**
```bash
npm run dev:server
```

### Step 2: Check Backend Port

Make sure the backend is running on port 3001 (default).

**Check server console output:**
- Should show: `Server running on http://localhost:3001`

**If port is different:**
- Update `.env` file in root directory:
  ```
  VITE_API_URL=http://localhost:YOUR_PORT/api
  ```

### Step 3: Verify Environment Variables

**Create `.env` file in root directory:**
```env
VITE_API_URL=http://localhost:3001/api
```

**Create `server/.env` file:**
```env
PORT=3001
JWT_SECRET=your-secret-key-change-this
GMAIL_USER=your-email@gmail.com
GMAIL_APP_PASSWORD=your-app-password
```

### Step 4: Test Backend Connection

**Test if backend is accessible:**
1. Open browser
2. Go to: `http://localhost:3001/api/health`
3. Should see: `{"status":"ok","message":"Server is running"}`

**If this doesn't work:**
- Backend server is not running
- Port might be blocked
- Check firewall settings

### Step 5: Check Browser Console

**Open browser developer tools (F12):**
1. Go to Console tab
2. Look for error messages
3. Common errors:
   - `Failed to fetch` = Backend not running
   - `CORS error` = Backend CORS not configured (should be fixed)
   - `404` = Wrong API URL

### Step 6: Restart Both Servers

**Stop all running servers (Ctrl+C), then:**

**Option A: Run both together**
```bash
npm run dev:all
```

**Option B: Run separately (2 terminals)**

Terminal 1:
```bash
npm run dev
```

Terminal 2:
```bash
npm run dev:server
```

### Step 7: Verify Dependencies

**Make sure all packages are installed:**
```bash
npm install
```

**Check if backend dependencies are installed:**
```bash
npm list express sqlite3 nodemailer multer
```

### Step 8: Check Database

**The database should be created automatically:**
- Location: `server/database.sqlite`
- Created on first server start
- If missing, restart server

### Common Issues

#### Issue: "Cannot connect to server"
**Solution:** Backend server is not running. Start it with `npm run dev:server`

#### Issue: "CORS error"
**Solution:** Backend has CORS enabled, but if you see this error, check that backend is running

#### Issue: "404 Not Found"
**Solution:** Check `VITE_API_URL` in `.env` file matches your backend URL

#### Issue: "Port already in use"
**Solution:** 
- Change port in `server/.env`: `PORT=3002`
- Update `VITE_API_URL` in root `.env`: `VITE_API_URL=http://localhost:3002/api`

### Quick Fix Checklist

- [ ] Backend server is running (`npm run dev:server`)
- [ ] Frontend server is running (`npm run dev`)
- [ ] `.env` file exists in root with `VITE_API_URL=http://localhost:3001/api`
- [ ] `server/.env` file exists with `PORT=3001`
- [ ] Can access `http://localhost:3001/api/health` in browser
- [ ] No firewall blocking port 3001
- [ ] All dependencies installed (`npm install`)

### Still Not Working?

1. **Check server logs** - Look for error messages in terminal
2. **Check browser console** - Look for detailed error messages
3. **Verify ports** - Make sure ports 3001 and 8080 are not in use
4. **Restart everything** - Stop all processes and restart

### Default Admin Credentials

- **Username:** `admin`
- **Password:** `admin123`

These are created automatically when the server starts for the first time.

