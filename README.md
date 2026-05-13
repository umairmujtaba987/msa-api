# Mansehra Sport Arena Backend API

A comprehensive Laravel-based REST API for the Mansehra Sport Arena management system. This backend provides complete functionality for managing sports facilities, bookings, user accounts, and administrative operations.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)
- [Environment Variables](#environment-variables)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

Mansehra Sport Arena Backend API is a robust REST API built with Laravel framework, designed to manage all backend operations for the Mansehra Sport Arena platform. It handles user management, facility bookings, payments, and administrative tasks with a focus on scalability, security, and performance.

## ✨ Features

- **User Management**: Complete authentication and authorization system
- **Facility Management**: Create, update, and manage sports facilities
- **Booking System**: Advanced booking management with availability checking
- **Payment Integration**: Secure payment processing
- **User Profiles**: Comprehensive user profile management
- **Admin Dashboard**: Full administrative control panel
- **Reports & Analytics**: Detailed reports and analytics data
- **API Documentation**: Well-documented RESTful API endpoints
- **Security**: Built-in security features including CORS, rate limiting, and input validation

## 🛠️ Technology Stack

- **Framework**: Laravel 8.x / 9.x / 10.x
- **Language**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Web Server**: Apache / Nginx
- **Cache**: Redis (optional)
- **Queue**: Database / Redis (optional)
- **Authentication**: Laravel Sanctum / JWT

## 📦 System Requirements

Before you begin, ensure you have the following installed on your system:

- **PHP**: 8.0 or higher
- **Composer**: Latest version
- **MySQL**: 8.0 or higher
- **Node.js**: 14.x or higher (optional, for frontend assets)
- **NPM**: 6.x or higher (optional)
- **Git**: Latest version

### Recommended Setup

- **OS**: Ubuntu 20.04 LTS / macOS 10.15+ / Windows 10+
- **RAM**: 2GB minimum (4GB recommended)
- **Disk Space**: 500MB minimum
- **Internet**: Stable connection for package downloads

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/umairmujtaba987/msa-api.git
cd msa-api
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

If you encounter any issues, try clearing Composer's cache:

```bash
composer clear-cache
composer install
```

### Step 3: Create Environment File

```bash
cp .env.example .env
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

This will generate a random application encryption key and set it in your `.env` file.

### Step 5: Install Node Dependencies (Optional)

If the project uses frontend assets:

```bash
npm install
npm run dev
```

For production:

```bash
npm run build
```

## ⚙️ Configuration

### Database Configuration

Edit the `.env` file and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=msa_api
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Application Configuration

Update the following in `.env`:

```env
APP_NAME="Mansehra Sport Arena"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@msa.com
MAIL_FROM_NAME="Mansehra Sport Arena"
```

### Additional Services (Optional)

Configure payment gateway:

```env
STRIPE_PUBLIC_KEY=your_stripe_public_key
STRIPE_SECRET_KEY=your_stripe_secret_key
```

Configure file storage:

```env
FILESYSTEM_DISK=public
```

## 🗄️ Database Setup

### Step 1: Create Database

```bash
mysql -u root -p
```

Inside MySQL shell:

```sql
CREATE DATABASE msa_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: Seed Database (Optional)

If seeders are available for sample data:

```bash
php artisan db:seed
```

Or seed specific tables:

```bash
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=FacilitiesTableSeeder
```

### Step 4: Link Storage

```bash
php artisan storage:link
```

This creates a symbolic link from `storage/app/public` to the web-accessible `public/storage`.

## ▶️ Running the Application

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

To specify a different port:

```bash
php artisan serve --port=3000
```

### Using Nginx (Production-like Setup)

1. Configure Nginx virtual host
2. Point document root to the `public` directory
3. Restart Nginx:

```bash
sudo systemctl restart nginx
```

### Run Queue Worker (If Applicable)

If your application uses queues:

```bash
php artisan queue:work
```

### Run Scheduled Tasks (If Applicable)

```bash
php artisan schedule:run
```

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication

Most endpoints require authentication using Bearer tokens:

```bash
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Sample Endpoints

#### Get All Facilities
```bash
GET /api/facilities
Authorization: Bearer YOUR_TOKEN
```

#### Create Booking
```bash
POST /api/bookings
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "facility_id": 1,
  "start_time": "2026-05-14 10:00:00",
  "end_time": "2026-05-14 12:00:00"
}
```

#### User Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

For complete API documentation, refer to the `docs/` directory or use tools like Postman to explore endpoints.

## 📁 Project Structure

```
msa-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Middleware/
│   ├── Models/
│   ├── Services/
│   └── Exceptions/
├── config/
│   ├── app.php
│   ├── database.php
│   └── ...
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Unit/
│   └── Feature/
├── public/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

## 🔐 Environment Variables

Create a `.env` file in the root directory. Here's a template with all essential variables:

```env
# Application
APP_NAME="Mansehra Sport Arena"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=msa_api
DB_USERNAME=root
DB_PASSWORD=

# Cache
CACHE_DRIVER=file
CACHE_REDIS_HOST=127.0.0.1
CACHE_REDIS_PASSWORD=null
CACHE_REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=database

# Session
SESSION_DRIVER=file

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@msa.com

# File Storage
FILESYSTEM_DISK=public

# Authentication
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost
```

## 🐛 Troubleshooting

### Common Issues and Solutions

#### 1. Composer Dependency Issues

**Problem**: `composer install` fails

**Solution**:
```bash
composer clear-cache
composer install --no-interaction
```

#### 2. Permission Denied Error

**Problem**: Storage directory permission issues

**Solution**:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### 3. Database Connection Error

**Problem**: "SQLSTATE[HY000] [2002] Connection refused"

**Solution**:
- Ensure MySQL is running
- Check database credentials in `.env`
- Verify database name exists

```bash
mysql -u root -p -e "SHOW DATABASES;"
```

#### 4. Key Generation Error

**Problem**: App key already exists or key generation fails

**Solution**:
```bash
php artisan key:generate --force
```

#### 5. Migration Errors

**Problem**: Migration fails due to existing tables

**Solution**:
```bash
php artisan migrate:reset
php artisan migrate
```

#### 6. Storage Link Issues

**Problem**: Public files not accessible

**Solution**:
```bash
php artisan storage:link --force
```

#### 7. Port Already in Use

**Problem**: `The port 8000 is already in use`

**Solution**:
```bash
php artisan serve --port=8001
```

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m 'Add your feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Open a Pull Request

### Code Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Include tests for new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For issues, questions, or suggestions:

- Open an issue on GitHub: [Issues](https://github.com/umairmujtaba987/msa-api/issues)
- Email: umairmujtaba987@gmail.com

## 🎓 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [RESTful API Best Practices](https://restfulapi.net/)

---

**Last Updated**: May 13, 2026

**Maintainer**: [umairmujtaba987](https://github.com/umairmujtaba987)
