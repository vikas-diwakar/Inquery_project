# Implementation Plan - Game-Changer Features (Sequential Roadmap)

We will implement the requested 3 game-changer features one by one:

1. **Feature 1**: Automated WhatsApp Business Integration & Instant Brochure Delivery
2. **Feature 2**: Interactive 360° Virtual Tour & Unit Availability Map (Stacking Chart)
3. **Feature 3**: Automated Lead Nurturing Drip Sequences (Day 1, 3, 7, 14)

---

## Phase 1: Feature 1 - Automated WhatsApp Integration & Instant Brochure Delivery

### Problem & Objective
When a customer scans a project QR code or submits an inquiry form, they expect instant gratification on WhatsApp. Currently, inquiries are recorded in the database, but no instant WhatsApp message or digital brochure link is automatically sent to the buyer.

### Technical Implementation

#### 1. Database & Configuration Updates
- Add WhatsApp settings to `companies` table or a dedicated settings table:
  - `whatsapp_provider` (`none`, `twilio`, `ultramsg`, `meta_cloud`, `webhook`)
  - `whatsapp_api_key`, `whatsapp_phone_number_id`, `whatsapp_instance_id`
  - `whatsapp_auto_send` (boolean)
  - `whatsapp_welcome_template` (custom text with merge tags like `{customer_name}`, `{project_name}`, `{brochure_url}`, `{executive_name}`)
- Add `whatsapp_sent_at` and `whatsapp_status` columns to `inquiries` table.

#### 2. Service Layer Implementation ([`NEW`] [`WhatsAppService.php`](file:///c:/laragon/www/property-inquiry-saas-new/app/Services/WhatsAppService.php))
- Create `WhatsAppService` to encapsulate dispatching WhatsApp messages:
  - Supports API dispatch and simulated webhooks for local testing.
  - Composes template variables (`{customer_name}`, `{project_name}`, `{brochure_url}`).
  - Dispatches instant message containing welcome greeting + PDF brochure download URL when an inquiry is created.

#### 3. Event Listener / Controller Integration
- Hook into `PublicInquiryController@store` and `InquiryController@store`.
- Automatically trigger `WhatsAppService::sendInstantBrochure($inquiry)` upon lead creation.

#### 4. UI Integrations
- **Company Settings Page** ([`NEW`] `resources/views/settings/whatsapp.blade.php`): Configuration form to set API Keys, toggle Auto-Send, and customize the message template.
- **Inquiry Details View** ([`MODIFY`] `resources/views/inquiries/show.blade.php`): Display a "WhatsApp Delivered" status badge with timestamp & a manual "Resend WhatsApp Brochure" button.

---

## Proposed Changes

### Database & Models

#### [NEW] [2026_09_02_000001_add_whatsapp_fields_to_companies_and_inquiries_table.php](file:///c:/laragon/www/property-inquiry-saas-new/database/migrations/2026_09_02_000001_add_whatsapp_fields_to_companies_and_inquiries_table.php)
Migration adding WhatsApp API credentials & auto-send templates to `companies` and `whatsapp_sent_at` to `inquiries`.

#### [MODIFY] [Company.php](file:///c:/laragon/www/property-inquiry-saas-new/app/Models/Company.php)
Add fillable fields for WhatsApp settings.

#### [MODIFY] [Inquiry.php](file:///c:/laragon/www/property-inquiry-saas-new/app/Models/Inquiry.php)
Add `whatsapp_sent_at`, `whatsapp_status` attributes and `hasWhatsAppBeenSent()` helper method.

---

### Services & Controllers

#### [NEW] [WhatsAppService.php](file:///c:/laragon/www/property-inquiry-saas-new/app/Services/WhatsAppService.php)
Service class for compiling WhatsApp template text, attaching PDF brochure links, and sending messages.

#### [NEW] [WhatsAppSettingController.php](file:///c:/laragon/www/property-inquiry-saas-new/app/Http/Controllers/WhatsAppSettingController.php)
Controller for updating company WhatsApp credentials & testing connection.

#### [MODIFY] [PublicInquiryController.php](file:///c:/laragon/www/property-inquiry-saas-new/app/Http/Controllers/PublicInquiryController.php)
Trigger `WhatsAppService` after customer submits QR form.

---

### Views & Navigation

#### [NEW] [whatsapp.blade.php](file:///c:/laragon/www/property-inquiry-saas-new/resources/views/settings/whatsapp.blade.php)
WhatsApp integration settings page with live preview of the message template.

#### [MODIFY] [app.blade.php](file:///c:/laragon/www/property-inquiry-saas-new/resources/views/layouts/app.blade.php)
Add "WhatsApp API" link to navbar dropdown or settings.

#### [MODIFY] [show.blade.php](file:///c:/laragon/www/property-inquiry-saas-new/resources/views/inquiries/show.blade.php)
Add WhatsApp delivery status card and "Resend WhatsApp Brochure" button.

---

## Verification Plan

### Automated / Manual Testing
1. **Migration & Settings Test**: Run `php artisan migrate` and configure WhatsApp credentials & template in Company Settings.
2. **Public Inquiry Form Submission**: Submit a lead on `http://127.0.0.1:8000/inquiry/{project_id}`.
3. **Delivery Verification**: Verify `whatsapp_sent_at` timestamp is updated on the inquiry record and verify formatted message containing brochure URL.
4. **UI Verification**: Inspect `inquiries.show` page to see the green WhatsApp status badge and test the "Resend WhatsApp Brochure" button.
