# Asset Management System

A web-based asset management system built with Laravel and Tailwind CSS.

## Features

- Asset tracking with QR code generation
- Category management
- User management with role-based access
- Asset status monitoring
- QR code batch export
- Asset location tracking

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and NPM

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `npm install`
4. Copy `.env.example` to `.env`
5. Configure your database in `.env`
6. Run `php artisan key:generate`
7. Run `php artisan migrate --seed`
8. Run `php artisan serve`
9. Visit `http://localhost:8000` in your browser

## Database Setup

Configure your `.env` file with these settings:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

## Default Login

- Username: admin
- Password: password

## Features Guide

### Asset Management
- Add, edit, and delete assets
- Generate QR codes for each asset
- Track asset status and location
- Export QR codes in batch

### User Management
- Role-based access control
- User activity tracking
- Group management

### Category Management
- Organize assets by categories
- Hardware, Software, Network, and Peripherals

## License

This project is licensed under the MIT License.