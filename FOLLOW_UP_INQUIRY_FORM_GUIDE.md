# Follow-up Integration in Inquiry Form

## Overview
The inquiry creation form now includes two new fields for managing follow-ups:

1. **Assign To** - Select which user/team member should handle this inquiry
2. **Next Follow-up Date** - Set when the follow-up should occur

## Form Fields

### Assign To
- **Type**: Dropdown select
- **Options**: List of all users associated with the selected project
- **Default**: Unassigned
- **Purpose**: Assign the inquiry to a specific team member for follow-up
- **Display Format**: Shows name and email (e.g., "John Doe (john@example.com)")

### Next Follow-up Date
- **Type**: Date and time picker (datetime-local)
- **Default**: 3 days from now at 10:00 AM
- **Validation**: Must be after current time
- **Format**: YYYY-MM-DD HH:MM (e.g., 2026-02-12 14:30)
- **Purpose**: Schedule when this inquiry should be followed up

## How It Works

### Creating an Inquiry with Follow-up

1. Go to **Inquiries → Add New Inquiry**
2. Fill in customer details (name, phone, email, etc.)
3. Scroll to **Assign To** section
4. Select a team member from the dropdown or leave blank for unassigned
5. Set the **Next Follow-up Date** 
   - Use the date/time picker or quick buttons
   - Default is 3 days from now
6. Click **Create Inquiry**

### Quick Follow-up Date Options
The form doesn't show these in the current implementation, but you can add quick buttons:
- Today (10:00 AM)
- Tomorrow (10:00 AM)
- 3 Days (10:00 AM)
- 1 Week (10:00 AM)

### Data Storage
When an inquiry is created:
- **assigned_to** → Stored in inquiries table
- **next_follow_up_date** → Stored as datetime in inquiries table

## Database Schema
```sql
-- Updated columns in inquiries table:
- assigned_to (foreign key to users table)
- next_follow_up_date (datetime, nullable)
- last_follow_up_date (datetime, nullable)
- follow_up_notes (text, nullable)
```

## Backend Code

### Controller: `app/Http/Controllers/InquiryController.php`

```php
// In create() method:
$projectUsers = $project->users()->get();
return view('inquiries.create', compact('project', 'projectUsers'));

// In store() method validation:
'assigned_to' => 'nullable|exists:users,id',
'next_follow_up_date' => 'nullable|date_format:Y-m-d\TH:i|after:now',

// Data creation:
$followUpDate = null;
if ($validated['next_follow_up_date']) {
    $followUpDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['next_follow_up_date']);
}

Inquiry::create([
    // ... other fields
    'assigned_to' => $validated['assigned_to'] ?? null,
    'next_follow_up_date' => $followUpDate,
    'status' => 'new',
]);
```

### Form: `resources/views/inquiries/create.blade.php`

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Assign To Field -->
    <div>
        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
        <select
            id="assigned_to"
            name="assigned_to"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
            <option value="">-- Unassigned --</option>
            @foreach($projectUsers as $user)
                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Next Follow-up Date Field -->
    <div>
        <label for="next_follow_up_date" class="block text-sm font-medium text-gray-700">Next Follow-up Date</label>
        <input
            id="next_follow_up_date"
            name="next_follow_up_date"
            type="datetime-local"
            value="{{ old('next_follow_up_date', now()->addDays(3)->format('Y-m-d\TH:i')) }}"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
    </div>
</div>
```

## Usage Examples

### Example 1: Create Inquiry with Follow-up Assigned to John
1. Fill customer details
2. Select "John Doe (john@example.com)" in Assign To
3. Set Next Follow-up Date to "2026-02-12 14:00"
4. Click Create - inquiry is created and John is assigned

### Example 2: Create Unassigned Inquiry
1. Fill customer details
2. Leave Assign To as "-- Unassigned --"
3. Set Next Follow-up Date
4. Click Create - inquiry is created without assignment
5. Can assign later from inquiry detail page

### Example 3: Using Default Follow-up Date
1. Fill customer details
2. Leave Next Follow-up Date as default (3 days from now)
3. Fill assign to field
4. Click Create

## Validation Rules

```php
// Assigned To
- Must be nullable (can be empty)
- If provided, must exist as a user
- Validates using 'exists:users,id'

// Next Follow-up Date
- Must be nullable (can be empty)
- If provided, must be in format Y-m-d\TH:i (2026-02-12 14:30)
- Must be after current time ('after:now')
```

## View the Inquiry

After creating an inquiry with a follow-up date:

1. Go to **Inquiries** list
2. Click on the inquiry
3. In the detail view you'll see:
   - **Follow-up Scheduler** component - Shows next follow-up date and can schedule new ones
   - **Follow-up History** component - Shows all past follow-ups

## Connecting to Follow-up Management

The follow-up date set here feeds into:
- Dashboard reminders (/follow-ups)
- Notification system
- Follow-up history tracking
- Overdue follow-up alerts

## Tips & Best Practices

1. **Always set a follow-up date** - Even if unassigned, schedule a follow-up
2. **Assign to the right person** - Assign to the user who will handle follow-up
3. **Set realistic dates** - Consider working hours and capacity
4. **3-5 days** - Typical follow-up window for initial inquiry
5. **Check dashboard** - Go to Follow-ups dashboard to see all pending follow-ups

## Troubleshooting

### Form shows error "Assigned To must exist"
- The selected user doesn't have access to this project
- Make sure the user is added to the project first

### Form shows error "Next Follow-up Date must be after now"
- The selected date/time is in the past
- Make sure to pick a future date and time

### Project Users dropdown is empty
- No users have been assigned to this project yet
- Add users to the project in Project Settings

## Customization

To customize the default follow-up date (currently 3 days):

```php
// In InquiryController create() method:
// Change: now()->addDays(3)
// To: now()->addDays(5)  // for 5 days
// To: now()->addWeeks(1) // for 1 week
// To: now()->addHours(2) // for 2 hours
```

To customize the default time (currently 10:00 AM):

```blade
// In the form:
<!-- Change this line: -->
value="{{ old('next_follow_up_date', now()->addDays(3)->format('Y-m-d\TH:i')) }}"

<!-- To: -->
value="{{ old('next_follow_up_date', now()->addDays(3)->setHour(14)->setMinute(30)->format('Y-m-d\TH:i')) }}"
<!-- This sets default to 2:30 PM -->
```
