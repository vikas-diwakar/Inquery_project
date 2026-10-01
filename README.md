# 🏢 Real Estate Property & Inquiry Management System (Standalone Edition)

An independent, self-hosted, enterprise-grade Real Estate CRM and Lead Automation Web Application built with **Laravel 11**, **Tailwind CSS**, and **Alpine.js**.

Designed specifically for real estate developers, agencies, and builders to manage property portfolios, unit inventory, public QR inquiry captures, brochure distribution, and automated WhatsApp lead drip sequences.

---

## ✨ Features Overview

### 1. 🏗️ Property & Inventory Portfolio
- **Unlimited Projects**: Create, edit, and organize residential and commercial property developments.
- **Unit Inventory & Stacking Chart**: Interactive floor-by-floor unit stacking matrix with live status color indicators (Available, Booked, On Hold, Sold).
- **Batch Unit Generator**: Rapidly generate hundreds of units with customizable pricing, carpet areas, unit types (1BHK, 2BHK, 3BHK, Penthouses), and floor ranges.
- **Brochure Management**: Upload PDF brochures with secure storage and direct public download links.

### 2. 🎯 Smart Lead Capture & Inquiries
- **Dynamic QR Inquiry Forms**: Instant project-specific QR codes for offline site visits, billboards, print ads, and expos.
- **Public Inquiry Pages & Embeddable Widgets**: Embed iframe widgets or share direct inquiry links with zero login friction.
- **Facebook Lead Ads & Webhooks**: Integrated webhook endpoint to receive leads instantly from Facebook Ads or external landing pages.
- **Comprehensive Inquiry Details**: Customer name, phone, email, budget range, selected unit type, source attribution, and custom notes.
- **Status Lifecycle Tracking**: Move leads through stages: `New` &rarr; `Contacted` &rarr; `Qualified` &rarr; `Site Visit` &rarr; `Negotiation` &rarr; `Booked` &rarr; `Lost`.
- **Excel & CSV Export**: Instant export of filtered leads for offline analysis.

### 3. 💬 WhatsApp Cloud API & Automated Drip Campaigns
- **Meta WhatsApp Cloud API Integration**: Connect official WhatsApp business accounts directly.
- **Instant Welcome Auto-Replies**: Deliver project brochures and personalized welcome messages the second an inquiry is submitted.
- **Lead Drip Automation**: Design automated multi-step sequences (e.g. Day 1: Welcome & Brochure, Day 3: Virtual Tour & Amenities, Day 7: Limited Price Offer).
- **One-Click Resend**: Re-trigger WhatsApp brochures and updates with a single click from the inquiry details screen.

### 4. 📅 Follow-up Management & Reminders
- Schedule upcoming calls, meetings, and site visits for each lead.
- Filter by Today's follow-ups, Overdue, and Completed.
- Calendar & agenda views with detailed interaction logs.

### 5. 👥 Team & Role-Based Access Control (RBAC)
- **Super Administrator**: Full control over system settings, company profile, users, and projects.
- **Sales Managers**: Oversight of projects, unit allocations, and team inquiries.
- **Sales Agents**: Focused view on assigned leads, calls, and follow-ups.
- **Lead Allocation**: Supports both **Manual Assignment** and **Automated Round-Robin** distribution.

### 6. 🎨 Agency White-Label & Settings
- Fully customizable company branding: upload agency logo, set office address, phone, and inquiry support email.
- Runs on any domain, subdomain, or local development server with zero multi-tenant or licensing friction.

---

## 🚀 Quick Start & Installation

Please refer to the detailed [Installation & Deployment Guide](INSTALLATION_GUIDE.md).

```bash
# 1. Clone repository
git clone <repository-url>

# 2. Install dependencies
composer install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Migrate and seed initial admin data
php artisan migrate --seed

# 5. Link storage
php artisan storage:link
```

### Default Sign-in:
- **Email**: `admin@example.com`
- **Password**: `password`

---

## 📄 License & Sale Rights
This software is provided with full source code for private deployment and commercial client usage.
