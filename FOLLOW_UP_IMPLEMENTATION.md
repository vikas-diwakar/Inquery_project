# Follow-up Customer Management System

This implementation adds comprehensive follow-up management to your property inquiry SaaS application.

## Features Implemented

### 1. **Set Next Follow-up Date for Each Inquiry**
   - Added `next_follow_up_date` field to inquiries table
   - Added `last_follow_up_date` field to track when the last follow-up was completed
   - Service method to schedule follow-ups with configurable dates
   - Automatic follow-up scheduling based on outcome (e.g., auto-schedule in 2 days for "no response")

### 2. **Dashboard Reminders**
   - Centralized follow-up dashboard at `/follow-ups` route
   - Real-time statistics showing:
     - **Overdue**: Follow-ups past their scheduled date
     - **Today**: Follow-ups scheduled for today
     - **Next 7 Days**: Upcoming follow-ups
     - **Total Pending**: Combined count of all pending follow-ups
   - Color-coded tables for quick visual identification of priority
   - Links to view and manage inquiries directly

### 3. **Notification Alerts**
   - **FollowUpReminder Notification**: Sends email and database notifications for scheduled follow-ups
   - **OverdueFollowUpAlert Notification**: Urgent alerts for overdue follow-ups with red flag
   - Scheduled via console command `app:send-follow-up-reminders`
   - Can send reminders for overdue, upcoming, or all follow-ups

### 4. **Database Schema**

#### New columns added to `inquiries` table:
```sql
- next_follow_up_date (datetime, nullable)
- last_follow_up_date (datetime, nullable)
- follow_up_notes (text, nullable)
```

#### New `follow_ups` table tracks follow-up history:
```sql
- id (primary key)
- inquiry_id (foreign key)
- company_id (foreign key)
- follow_up_by (user_id, who performed the follow-up)
- type (enum: call, email, sms, visit, message)
- notes (text)
- outcome (enum: interested, not_interested, no_response, callback_requested, other)
- scheduled_date (when this follow-up was scheduled)
- timestamps
```

## Models & Relationships

### Inquiry Model
```php
// New relationship
$inquiry->followUps() // Get all follow-ups
```

**Scopes for queries:**
- `$inquiry->outdueFollowUps()` - Get inquiries with overdue follow-ups
- `$inquiry->upcomingFollowUps()` - Get inquiries with follow-ups in next 7 days
- `$inquiry->needsFollowUp()` - Get inquiries due for follow-up today or earlier
```

### FollowUp Model
New model (`App\Models\FollowUp`) with relationships:
- `belongsTo(Inquiry)`
- `belongsTo(Company)`
- `belongsTo(User, 'follow_up_by')` - User who performed follow-up

## Services

### FollowUpService (`App\Services\FollowUpService`)

Key methods:
- `scheduleFollowUp(Inquiry, ?Carbon, ?string, ?int)` - Schedule a follow-up
- `completeFollowUp(FollowUp, string, ?string, ?Carbon)` - Mark follow-up as complete
- `getOverdueFollowUps($companyId)` - Get list of overdue follow-ups
- `getUpcomingFollowUps($companyId)` - Get upcoming follow-ups (next 7 days)
- `getTodayFollowUps($companyId)` - Get today's follow-ups
- `getFollowUpStats($companyId)` - Get dashboard statistics
- `bulkScheduleFollowUp(array, Carbon)` - Schedule multiple inquiries at once

## Controllers

### FollowUpController (`App\Http\Controllers\FollowUpController`)

Endpoints:
- `GET /follow-ups` - Display follow-up dashboard
- `POST /inquiries/{inquiry}/follow-ups` - Schedule follow-up
- `POST /inquiries/{inquiry}/follow-ups/complete` - Complete follow-up
- `POST /follow-ups/bulk-schedule` - Bulk schedule follow-ups
- `GET /api/follow-ups/stats` - Get stats as JSON for widgets

## Console Commands

### SendFollowUpReminders Command
```bash
php artisan app:send-follow-up-reminders           # Send all reminders
php artisan app:send-follow-up-reminders --overdue # Only overdue
php artisan app:send-follow-up-reminders --upcoming # Only upcoming today
```

Schedule this in your `app/Console/Kernel.php`:
```php
$schedule->command('app:send-follow-up-reminders')->dailyAt('09:00');
$schedule->command('app:send-follow-up-reminders --overdue')->hourly();
```

## Usage Examples

### Schedule a Follow-up
```php
$service = app(\App\Services\FollowUpService::class);
$inquiry = Inquiry::find(1);

// Schedule for 3 days from now
$followUp = $service->scheduleFollowUp(
    $inquiry,
    now()->addDays(3),
    'Customer interested, waiting for family approval'
);
```

### Complete a Follow-up with Auto-schedule
```php
$followUp = $inquiry->followUps()->latest()->first();

$service->completeFollowUp(
    $followUp,
    'interested', // outcome
    'Customer interested, arranging site visit',
    now()->addDays(5) // next follow-up date
);
```

### Get Dashboard Stats
```php
$stats = $service->getFollowUpStats(auth()->user()->company_id);
// Returns:
// [
//     'overdue' => 3,
//     'today' => 5,
//     'upcoming' => 12,
//     'total_pending' => 20,
// ]
```

### Query Inquiries
```php
// Get all overdue follow-ups
$overdue = Inquiry::overdueFollowUps()->get();

// Get upcoming in next 7 days
$upcoming = Inquiry::upcomingFollowUps()->get();

// Get those needing follow-up today
$today = Inquiry::needsFollowUp()->get();
```

## Routes to Add

Add the following routes to your `routes/web.php`:

```php
Route::middleware(['auth'])->group(function () {
    // Follow-up routes
    Route::get('/follow-ups', [\App\Http\Controllers\FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/inquiries/{inquiry}/follow-ups', [\App\Http\Controllers\FollowUpController::class, 'store'])->name('follow-ups.store');
    Route::post('/inquiries/{inquiry}/follow-ups/complete', [\App\Http\Controllers\FollowUpController::class, 'complete'])->name('follow-ups.complete');
    Route::post('/follow-ups/bulk-schedule', [\App\Http\Controllers\FollowUpController::class, 'bulkSchedule'])->name('follow-ups.bulk-schedule');
    Route::get('/api/follow-ups/stats', [\App\Http\Controllers\FollowUpController::class, 'getStats'])->name('follow-ups.stats');
});
```

## Notifications

### Email Templates
Both notifications send HTML emails with:
- Customer details
- Inquiry information
- Scheduled/overdue dates
- Action buttons to view/update

### Database Notifications
Stored in `notifications` table for in-app alerts and can be displayed in notification center.

## Dashboard Widget Example

To add a follow-up widget to your main dashboard:

```blade
<div class="grid grid-cols-4 gap-4">
    <div id="follow-up-widget" class="bg-white rounded-lg shadow">
        <div class="p-4">
            <h3 class="font-semibold">Follow-ups</h3>
            <div id="stats" class="mt-4 space-y-2">
                <div class="flex justify-between">
                    <span>Overdue:</span>
                    <span class="font-bold text-red-600" id="overdue">0</span>
                </div>
                <div class="flex justify-between">
                    <span>Today:</span>
                    <span class="font-bold text-blue-600" id="today">0</span>
                </div>
            </div>
            <a href="{{ route('follow-ups.index') }}" class="mt-4 btn btn-primary">View All</a>
        </div>
    </div>
</div>

<script>
    fetch('/api/follow-ups/stats')
        .then(r => r.json())
        .then(data => {
            document.getElementById('overdue').textContent = data.overdue;
            document.getElementById('today').textContent = data.today;
        });
</script>
```

## Next Steps

1. Run migrations: `php artisan migrate`
2. Add routes to `routes/web.php`
3. Schedule the console command in `app/Console/Kernel.php`
4. Update inquiry views to include "Schedule Follow-up" button
5. Customize notification templates as needed
6. Add follow-up widget to dashboard

## Policy Authorization

Make sure to add follow-up authorization checks in your `InquiryPolicy`:

```php
public function viewFollowUps(User $user, Inquiry $inquiry)
{
    return $user->company_id === $inquiry->company_id || $user->id === $inquiry->assigned_to;
}
```
