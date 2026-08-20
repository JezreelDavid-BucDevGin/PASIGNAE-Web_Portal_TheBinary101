# PASIGNAE — Diocese of Pasig Church Web Portal

A modernized diocesan information system for managing sacraments, parishes, schedules, payments, and user administration across the Diocese of Pasig.

## Tech Stack

- **Backend:** PHP 8+ (Clean Architecture — Controllers, Services, Repositories)
- **Frontend:** Vanilla HTML, CSS, JavaScript + TailwindCSS (CDN)
- **Database:** MySQL/MariaDB (normalized 3NF schema)
- **Email:** PHPMailer (optional, falls back to log file)
- **Server:** XAMPP (Apache + MySQL)

## Quick Start (XAMPP)

### 1. Prerequisites

- XAMPP with Apache and MySQL running
- PHP 8.0 or higher
- Composer (optional, for PHPMailer)

### 2. Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Import the schema file: `database/schema.sql`
3. This creates the `pasignae` database with seed data

### 3. Configuration

Edit `config/database.php` if your MySQL credentials differ from XAMPP defaults:

```php
'host' => 'localhost',
'username' => 'root',
'password' => '',
'database' => 'pasignae',
```

Edit `config/app.php` to set your base URL:

```php
'url' => 'http://localhost/Pasignae',
```

### 4. Install PHPMailer (Optional)

```bash
cd c:\xampp\htdocs\Pasignae
composer install
```

Without Composer, OTP emails are logged to `storage/logs/mail.log`.

### 5. Enable mod_rewrite

Ensure Apache `mod_rewrite` is enabled in XAMPP. The included `.htaccess` handles clean URLs.

### 6. Access the Application

Open: **http://localhost/Pasignae**

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@pasignae.local | password |
| Diocese Admin | diocese@pasignae.local | password |
| Parish Admin | parish@pasignae.local | password |
| Parishioner | parishioner@pasignae.local | password |

## Project Structure

```
Pasignae/
├── app/
│   ├── Controllers/     # HTTP request handlers
│   ├── Services/        # Business logic layer
│   ├── Repositories/    # Data access layer
│   ├── Middleware/      # Auth & access guards
│   ├── Core/            # Router, Database, Session, Validator
│   └── Helpers/         # Utility functions & Mailer
├── config/              # App & database configuration
├── database/            # SQL schema & seeds
├── public/              # CSS, JS assets
├── routes/              # Route definitions
├── storage/             # Logs & uploads
├── views/               # PHP templates (Tailwind UI)
├── index.php            # Front controller
└── .htaccess            # URL rewriting
```

## Features

- User registration, login, forgot password, email OTP verification
- Role-based access control (7 roles)
- Sacrament applications: Baptism, Matrimony, Funeral
- Centralized `persons` table (no duplicate parent/sponsor data)
- Schedule management with slot booking
- GCash payment recording & admin confirmation
- Diocese & Parish dashboards with statistics
- Parish & Vicariate management
- Audit logging

## Email Configuration

To send real emails, configure SMTP in `config/app.php`:

```php
'mail' => [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'your-email@gmail.com',
    'password' => 'your-app-password',
    ...
],
```

## License

Capstone project — Diocese of Pasig.
