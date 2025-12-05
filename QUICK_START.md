# Quick Start Guide

## 1. Install Dependencies
```bash
npm install
```

## 2. Set Up Gmail App Password
1. Go to [Google Account → Security → App Passwords](https://myaccount.google.com/apppasswords)
2. Generate a new app password for "Mail"
3. Copy the 16-character password

## 3. Configure Environment Variables

**Create `server/.env`:**
```env
PORT=3001
JWT_SECRET=change-this-to-random-string
GMAIL_USER=your-email@gmail.com
GMAIL_APP_PASSWORD=your-16-char-app-password
```

**Create `.env` in root:**
```env
VITE_API_URL=http://localhost:3001/api
```

## 4. Start the Application

Run both frontend and backend:
```bash
npm run dev:all
```

Or separately:
- Frontend: `npm run dev` (port 8080)
- Backend: `npm run dev:server` (port 3001)

## 5. Access the Application

- **Portfolio**: http://localhost:8080
- **Admin Login**: http://localhost:8080/admin/login
- **Default Credentials**: 
  - Username: `admin`
  - Password: `admin123`

## Features

✅ Contact form sends messages to database  
✅ Automatic Gmail notifications  
✅ Admin panel to view messages  
✅ Admin panel to manage projects  
✅ Secure authentication with JWT  

## Next Steps

1. Test the contact form on your portfolio
2. Check your Gmail for notifications
3. Log into admin panel and change default password
4. Add your projects through the admin panel

For detailed setup instructions, see `README_SETUP.md`

