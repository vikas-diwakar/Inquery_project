# 🏢 Real Estate CRM & Property Lead Automation - Installation & Deployment Guide

This software is an **independent, self-hosted real estate CRM and property inquiry automation system**. It is designed to be installed on any standard web hosting (cPanel, Plesk, VPS, AWS, DigitalOcean, or Localhost via Laragon/XAMPP).

---

## 📋 System Requirements

- **PHP**: `8.2` or higher (PHP 8.2 or 8.3 recommended)
- **Database**: MySQL `5.7+` or MariaDB `10.3+`
- **Extensions**: `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO`, `PDO_MySQL`, `Tokenizer`, `XML`, `GD` or `Imagick`
- **Composer**: `2.x`
- **Node.js & NPM**: (Optional, only needed if you wish to recompile frontend assets)

---

## 🚀 Quick Step-by-Step Installation

### Step 1: Upload Files & Set Directory Permissions
Extract or clone the project files to your server directory (e.g., `/public_html` or Laragon `www/` directory).

Make sure the following directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

### Step 2: Configure Environment Variables
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```
Edit `.env` and set your database connection and application URL:
```env
APP_NAME="Apex Real Estate CRM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 3: Install PHP Dependencies & Generate App Key
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
```

### Step 4: Run Database Migrations & Initial Setup Seeder
Run the following command to create the database schema and populate default roles, initial agency settings, and the super administrator account:
```bash
php artisan migrate --seed
```

### Step 5: Link Storage for Uploads
Ensure project brochures and company logos are publicly accessible:
```bash
php artisan storage:link
```

---

## 🔑 Default Administrator Credentials

Immediately upon running `php artisan migrate --seed`, you can log in to the portal:

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@example.com` | `password` |
| **Sales Agent** | `agent@example.com` | `password` |

> 🔒 **Security Notice:** Please log in as Super Admin and change the password, or create your own personal Admin account under **Users** and edit your company name/logo under **Company Settings**.

---

## ⚙️ Key Configuration & Features

### 1. Agency Branding & Settings
- Navigate to **Company Settings** (`/settings/company`) in the top navigation.
- Update your Agency/Company Name, Support Email, Phone Number, and Office Address.
- Upload your company logo (PNG, JPG, WebP, or SVG).
- Choose your **Lead Allocation Strategy**:
  - **Manual Assignment**: Admins/Managers assign leads.
  - **Auto Round-Robin**: New incoming leads automatically rotate evenly between all active sales agents.

### 2. WhatsApp Cloud API Integration
- Navigate to **WhatsApp API** under project settings (`/settings/whatsapp`).
- Connect your official **Meta WhatsApp Cloud API**:
  - Enter your **Phone Number ID**, **WhatsApp Business Account (WABA) ID**, and **Permanent System User Access Token**.
- Test WhatsApp sending directly from the dashboard.
- Customize the automated instant welcome message template sent to prospective buyers with project brochure download links.

### 3. Automated Lead Drip Sequences & Cron Job
To automatically dispatch drip messages and follow-up reminders, add the Laravel scheduler to your server crontab (e.g., via cPanel Cron Jobs or Linux crontab):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

To run background queues (for email notifications and WhatsApp delivery):
```bash
php artisan queue:work --tries=3
```
*(On cPanel or VPS, you can use Supervisor or a cron job running `php artisan queue:work --stop-when-empty` every minute).*

---

## 🛠️ Folder Structure & Production Notes

- **`routes/web.php`**: All application routes, inquiry capture forms, and webhooks.
- **`app/Http/Controllers/`**: Business logic controllers.
- **`resources/views/`**: Clean Blade UI templates with modern, responsive styling.
- **`public/storage/`**: Symlinked directory holding uploaded brochures, project images, and logos.

---

## 💡 Support & Customization
Since this is an **independent, 100% unlocked codebase**, you have full liberty to white-label, customize, extend models, add custom fields to inquiries, or deploy for individual client projects with no recurring subscription fees.
