# MMDA Revenue Collection System - Ghana

A complete web-based system for Metropolitan, Municipal, and District Assemblies (MMDAs) in Ghana to collect revenue via Mobile Money (MoMo).

## Features
- **Authentication**: Role-based access (Admin, Collector, Citizen).
- **Citizen Dashboard**: View unpaid bills, simulate MoMo payments, view/print receipts.
- **Collector Dashboard**: Search citizens, initiate MoMo payments, record offline cash payments.
- **Admin Dashboard**: Revenue overview, manage users, create bills, generate revenue reports (CSV export).
- **Security**: PDO prepared statements, password hashing, role-based access control.

## Setup Instructions (XAMPP/Apache)

1. **Clone/Copy Project**:
   Place the `mmda-revenue` folder into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\mmda-revenue`).

2. **Database**:
   - The system uses SQLite. The database file is already located at `mmda-revenue/database/revenue.sqlite`.
   - Ensure the `database` folder has write permissions for the web server.

3. **Accessing the System**:
   - Open your browser and go to `http://localhost/mmda-revenue/public/index.php`.

4. **Test Accounts**:
   - **Admin**:
     - Phone: `0241111111`
     - Password: `password123`
   - **Collector**:
     - Phone: `0242222222`
     - Password: `password123`
   - **Citizen**:
     - Phone: `0243333333`
     - Password: `password123`

## Directory Structure
- `/config`: Database connection (PDO).
- `/includes`: Core functions, authentication, header/footer.
- `/public`: Login, Registration, Logout.
- `/admin`: Admin-specific pages (Revenue reports, User/Bill management).
- `/collector`: Collector-specific pages (Search, Offline payments).
- `/user`: Citizen dashboard and MoMo simulation.
- `/database`: SQLite database and schema.
- `/assets`: CSS/JS (uses Bootstrap 5 CDN).
