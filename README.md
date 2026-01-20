# Property Inquiry Management SaaS

A complete multi-tenant SaaS web application for Real Estate Builders to manage property inquiries, projects, and customer interactions.

## Features

### Core Functionality

1. **Multi-Tenant Architecture**
   - Each registered company acts as an independent tenant
   - Complete data isolation between companies
   - Single database with tenant_id-based separation

2. **Company Registration**
   - Builder/company registration with company details
   - Logo upload support
   - Automatic admin user creation
   - Default role setup (Admin, Manager, Sales Executive)

3. **Project Management**
   - Create and manage multiple projects per company
   - Project details: name, location, description, start date, status
   - Project logo upload
   - Automatic QR code generation for inquiry forms

4. **Customer Inquiry System**
   - Public inquiry forms (no login required)
   - Unique QR code for each project
   - Inquiry fields: customer name, phone, email, budget, flat type, message
   - Inquiry status tracking: new, contacted, qualified, booked, rejected
   - Assignment to sales executives

5. **Project Brochure System**
   - PDF brochure upload per project
   - Unique QR code generation for brochure downloads
   - Direct download links (no login required)

6. **Admin Dashboard**
   - Project-wise statistics
   - Total inquiries and bookings
   - Recent inquiries list
   - Project-wise inquiry breakdown
   - Advanced filtering: search by phone, name, date range, project, status

7. **User & Role Management**
   - Multiple user creation per company
   - Role-based access control (RBAC)
   - Three default roles:
     - **Admin**: Full access to all features
     - **Manager**: View and limited actions (projects, inquiries, brochures)
     - **Sales Executive**: Only inquiry management

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL/SQLite
- **QR Code Generation**: JavaScript (qrcode.js library)
- **File Storage**: Laravel Storage (local/public)

## Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js and npm
- Database (MySQL/PostgreSQL/SQLite)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd property-inquiry-saas-new
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=property_inquiry
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Build Assets**
   ```bash
   npm run build
   # Or for development:
   npm run dev
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Usage

### Company Registration

1. Navigate to `/register`
2. Fill in company information:
   - Company name, email, phone, address
   - Company logo (optional)
   - Admin user credentials
3. Upon registration, you'll be automatically logged in

### Creating Projects

1. Log in to your account
2. Navigate to "Projects" in the navigation
3. Click "Add New Project"
4. Fill in project details and upload a logo (optional)
5. A QR code for the inquiry form will be automatically generated

### Managing Inquiries

1. **Public Inquiry Submission**:
   - Customers can scan the QR code or visit the inquiry form URL
   - Fill in the inquiry form (no login required)
   - Inquiry is automatically saved under the project

2. **Admin Inquiry Management**:
   - View all inquiries in the "Inquiries" section
   - Filter by project, status, date range, or search
   - Update inquiry status and assign to users
   - View detailed inquiry information

### Uploading Brochures

1. Navigate to "Brochures"
2. Click "Upload Brochure"
3. Select a project and upload a PDF file
4. A QR code for brochure download will be automatically generated
5. Customers can scan the QR code to download the brochure

### User Management (Admin Only)

1. Navigate to "Users"
2. Click "Add New User"
3. Fill in user details and assign a role
4. Users can be edited or deleted (except your own account)

## Database Schema

### Tables

- `companies` - Company/tenant information
- `users` - User accounts with company and role association
- `roles` - Role definitions per company
- `projects` - Project information
- `inquiries` - Customer inquiries
- `brochures` - Project brochures

### Multi-Tenant Isolation

All tenant-specific tables include a `company_id` foreign key:
- `users.company_id`
- `projects.company_id`
- `inquiries.company_id`
- `brochures.company_id`
- `roles.company_id`

The `HasTenant` trait automatically scopes queries to the authenticated user's company.

## Security Features

- Multi-tenant data isolation
- Role-based access control (RBAC)
- CSRF protection
- Password hashing
- File upload validation
- Authorization policies for resource access

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── CompanyRegistrationController.php
│   │   ├── DashboardController.php
│   │   ├── ProjectController.php
│   │   ├── InquiryController.php
│   │   ├── BrochureController.php
│   │   └── UserController.php
│   └── Middleware/
│       ├── EnsureTenant.php
│       └── CheckRole.php
├── Models/
│   ├── Company.php
│   ├── User.php
│   ├── Project.php
│   ├── Inquiry.php
│   ├── Brochure.php
│   └── Role.php
├── Policies/
│   ├── ProjectPolicy.php
│   ├── InquiryPolicy.php
│   ├── BrochurePolicy.php
│   └── UserPolicy.php
└── Traits/
    └── HasTenant.php

database/migrations/
├── 2024_01_01_000001_create_companies_table.php
├── 2024_01_01_000002_add_tenant_to_users_table.php
├── 2024_01_01_000003_create_roles_table.php
├── 2024_01_01_000004_create_projects_table.php
├── 2024_01_01_000005_create_inquiries_table.php
└── 2024_01_01_000006_create_brochures_table.php

resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
│   └── login.blade.php
├── company/
│   └── register.blade.php
├── dashboard/
│   └── index.blade.php
├── projects/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── inquiries/
│   ├── index.blade.php
│   └── show.blade.php
├── brochures/
│   ├── index.blade.php
│   └── create.blade.php
├── users/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── public/
    └── inquiry-form.blade.php
```

## API Routes

### Public Routes
- `GET /inquiry/{project}` - Public inquiry form
- `POST /inquiry/{project}` - Submit inquiry
- `GET /brochure/{brochure}/download` - Download brochure

### Authenticated Routes
- `GET /dashboard` - Dashboard
- `GET /projects` - List projects
- `POST /projects` - Create project
- `GET /inquiries` - List inquiries (with filters)
- `GET /brochures` - List brochures
- `POST /brochures` - Upload brochure
- `GET /users` - List users (Admin only)
- `POST /users` - Create user (Admin only)

## Default Roles & Permissions

### Admin
- Full access to all features
- User management
- Project management
- Inquiry management
- Brochure management

### Manager
- View projects
- Create/edit projects
- View inquiries
- Edit inquiries
- View brochures
- Create brochures

### Sales Executive
- View inquiries
- Edit inquiries

## QR Code Generation

QR codes are generated client-side using the `qrcode.js` library:
- Inquiry form QR codes: Generated on project show page
- Brochure QR codes: Generated on brochure listing page

QR codes contain direct URLs to:
- Inquiry forms: `/inquiry/{project_id}`
- Brochure downloads: `/brochure/{brochure_id}/download`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please open an issue on the repository.
