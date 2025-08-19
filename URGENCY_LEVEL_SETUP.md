# Repair Request Urgency Level System

## Overview

The urgency level system automatically categorizes repair requests based on their priority:

- **Level 1 (Highest)**: Requests with ongoing class/event
- **Level 2 (Medium)**: Requests that are over a week old
- **Level 3 (Lowest)**: New requests within the week

## Features

### Automatic Calculation
- Urgency levels are automatically calculated when creating new repair requests
- The system checks for ongoing activities and request age
- Admins can manually override urgency levels

### Automatic Updates (Multiple Methods)
1. **Real-time**: Calculated automatically when creating new requests
2. **Session-based**: Automatically updates when admins visit the repair status page (once per session)
3. **Manual**: Admins can trigger updates via the "Update Urgency" button
4. **Scheduled**: Daily automatic updates via cron job
5. **Command-line**: Manual execution via artisan command

**Note**: Urgency levels are only calculated for active requests. Completed, cancelled, and pulled out requests are excluded from urgency calculations.

### Scheduled Updates
- The system can automatically update urgency levels daily
- Configured to run at 8:00 AM daily
- Can be set up via cron job or Windows Task Scheduler

## Setup Instructions

### 1. Database Migration
The required database fields have been added:
- `urgency_level` (integer, default: 3)
- `ongoing_activity` (enum: 'yes', 'no', default: 'no')

### 2. Automatic Updates Setup

#### Option A: Server Cron Job (Recommended for Production)
Add this to your server's crontab to run Laravel's scheduler every minute:

```bash
# Run Laravel scheduler every minute
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

#### Option B: Windows Task Scheduler (For XAMPP/Windows)
1. Open Task Scheduler
2. Create a new Basic Task
3. Set it to run daily at 8:00 AM
4. Action: Start a program
5. Program: `C:\xampp\php\php.exe`
6. Arguments: `artisan repair:update-urgency-levels`
7. Start in: `C:\xampp\htdocs\Capstone`

#### Option C: Session-based Updates (Already Active)
- Urgency levels automatically update when admins visit the repair status page
- Updates once per session to avoid performance issues
- No additional setup required

### 3. Manual Testing
Test the urgency level calculation:

```bash
php artisan repair:update-urgency-levels
```

## Usage

### For Users
1. When creating a repair request, toggle "Class/Event Ongoing" if applicable
2. The urgency level will be automatically calculated
3. Level 1 requests will be prioritized

### For Admins
1. View urgency levels on repair request cards
2. Edit urgency levels in the update modal
3. Use the "Update Urgency" button to recalculate all pending requests
4. Monitor urgency level changes in the logs

## Visual Indicators

- **Level 1**: Red badge with lightning icon
- **Level 2**: Orange badge with lightning icon  
- **Level 3**: Blue badge with lightning icon

## Logging

Urgency level updates are logged to:
- Console output when running manually
- `storage/logs/urgency-levels.log` for scheduled runs
- Application logs for manual updates via web interface 