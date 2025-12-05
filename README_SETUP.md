# Setup Guide - Message System & Admin Panel

This guide will help you set up the message system with Gmail integration and admin panel.

## Prerequisites

- Node.js (v18 or higher)
- npm or yarn
- A Gmail account

## Installation

1. Install all dependencies:
```bash
npm install
```

## Gmail Setup

To enable email notifications, you need to set up Gmail App Password:

1. Go to your [Google Account settings](https://myaccount.google.com/)
2. Enable **2-Step Verification** (if not already enabled)
3. Go to **Security** → **2-Step Verification** → **App passwords**
4. Select **Mail** as the app and **Other** as the device
5. Generate a new app password
6. Copy the 16-character password

## Environment Configuration

1. Create a `.env` file in the `server` directory:
```bash
cd server
cp .env.example .env
```

2. Edit `server/.env` and add your configuration:
```env
PORT=3001
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
GMAIL_USER=your-email@gmail.com
GMAIL_APP_PASSWORD=your-16-character-app-password
```

3. Create a `.env` file in the root directory for the frontend:
```env
VITE_API_URL=http://localhost:3001/api
```

**Important:** 
- Use your Gmail App Password, NOT your regular Gmail password
- Change the JWT_SECRET to a random string in production
- The default admin credentials are: `username=admin`, `password=admin123`
- **Change the default admin password after first login!**

## Running the Application

### Option 1: Run Frontend and Backend Separately

Terminal 1 - Frontend:
```bash
npm run dev
```

Terminal 2 - Backend:
```bash
npm run dev:server
```

### Option 2: Run Both Together (Recommended)

```bash
npm run dev:all
```

This will start:
- Frontend on `http://localhost:8080`
- Backend API on `http://localhost:3001`

## Access Points

- **Portfolio Website**: http://localhost:8080
- **Admin Login**: http://localhost:8080/admin/login
- **Admin Dashboard**: http://localhost:8080/admin
- **API Health Check**: http://localhost:3001/api/health

## Default Admin Credentials

- **Username**: `admin`
- **Password**: `admin123`

**⚠️ IMPORTANT:** Change the default password immediately after first login!

## Features

### Message System
- Contact form sends messages to the database
- Automatic email notification to your Gmail
- Messages stored in SQLite database
- Admin can view, mark as read, and delete messages

### Admin Panel
- **Messages Management**: View all contact form messages, mark as read/unread, delete messages
- **Projects Management**: Add, edit, and delete portfolio projects
- **Authentication**: Secure login with JWT tokens

## Database

The SQLite database (`server/database.sqlite`) is automatically created on first run. It contains:
- `messages` table - Contact form submissions
- `projects` table - Portfolio projects
- `admin_users` table - Admin user accounts

## Troubleshooting

### Email Not Sending
1. Verify your Gmail App Password is correct
2. Make sure 2-Step Verification is enabled
3. Check that `GMAIL_USER` matches your Gmail address exactly
4. Check server console for error messages

### Cannot Access Admin Panel
1. Make sure backend server is running
2. Check that `VITE_API_URL` in root `.env` matches your backend URL
3. Clear browser localStorage and try logging in again

### CORS Errors
- Make sure backend is running on port 3001
- Check that `VITE_API_URL` is correctly set

## Production Deployment

Before deploying to production:

1. Change `JWT_SECRET` to a strong random string
2. Change default admin password
3. Set up proper environment variables on your hosting platform
4. Use HTTPS for both frontend and backend
5. Consider using a production database (PostgreSQL, MySQL) instead of SQLite
6. Set up proper CORS configuration for your domain

## Security Notes

- Never commit `.env` files to version control
- Use strong passwords and JWT secrets
- Enable HTTPS in production
- Regularly update dependencies
- Consider rate limiting for API endpoints

