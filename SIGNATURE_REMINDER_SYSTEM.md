# Signature Reminder System

## Overview
This system provides in-app visual reminders to callers who haven't signed completed repair requests within specific timeframes.

## How It Works

### Timeline

1. **0-24 Hours (Blue Alert - First Reminder)**
   - **Icon:** ℹ️ Information icon
   - **Title:** "📝 Signature Required"
   - **Message:** Gentle reminder that the repair is awaiting signature
   - **Action:** Caller should review and sign

2. **24-48 Hours (Yellow Alert - Second Reminder)**
   - **Icon:** ⏰ Clock icon
   - **Title:** "⏰ Signature Reminder"
   - **Message:** Repair completed over 24 hours ago, still awaiting signature
   - **Action:** Caller should review and sign soon

3. **48-72 Hours (Orange Alert - Supervisor Notified)**
   - **Icon:** 🔔 Bell icon
   - **Title:** "🔔 Supervisor Notified"
   - **Message:** Unsigned for 48+ hours, supervisors notified, will auto-approve in 24 hours
   - **Action:** Caller has final 24 hours to review and sign or request rework

4. **72+ Hours (Red Alert - Auto-Approval Imminent)**
   - **Icon:** ⚠️ Warning icon
   - **Title:** "⚠️ Auto-Approval Imminent"
   - **Message:** Over 72 hours without signature, will be auto-approved soon
   - **Action:** Last chance to review before automatic approval

### Visual Design

Each alert includes:
- **Color-coded border** (left border: 4px)
- **Background color** matching severity level
- **Icon** representing the alert type
- **Bold title** with emoji for quick recognition
- **Clear message** explaining the situation
- **Timestamp** showing when repair was completed and hours elapsed

### User Experience

**For Callers:**
- Alerts appear at the top of the repair details page
- Only visible to the caller who requested the repair
- Alerts are persistent until signature is provided
- Progressive urgency through color coding

**For Technicians:**
- No alerts shown (they've already completed their part)
- Can see signature status in the signatures section

**For Supervisors:**
- Can see all repair statuses in admin dashboard
- Notified at 48-hour mark about unsigned repairs

## Technical Implementation

### Frontend (repair-details.blade.php)
- Alert container: `#signatureReminderAlert`
- Function: `checkSignatureReminder(request)`
- Calculates hours since completion
- Displays appropriate alert based on timeframe
- Only shows for callers viewing their own requests

### Data Requirements
- `completed_at` - When repair was completed
- `caller_signed_at` - When caller signed (null if unsigned)
- `user_id` - Request creator (caller)
- Current user ID for comparison

### Alert Conditions
```javascript
if (completed_at && !caller_signed_at && isCallerView) {
    hoursSinceCompletion = now - completed_at
    
    if (hoursSinceCompletion >= 72) → Red Alert
    else if (hoursSinceCompletion >= 48) → Orange Alert
    else if (hoursSinceCompletion >= 24) → Yellow Alert
    else → Blue Alert
}
```

## Benefits

1. **No Email Required** - All reminders are in-app notifications
2. **Real-Time** - Alerts update based on current time when page loads
3. **Progressive Urgency** - Visual escalation encourages timely action
4. **Clear Timeline** - Users know exactly when auto-approval will occur
5. **Non-Intrusive** - Only shown to relevant users (callers)
6. **Automatic** - No manual intervention or scheduled tasks needed

## Future Enhancements (Optional)

- Add browser notifications for urgent reminders
- Email notifications (when email system is available)
- SMS notifications for critical timeframes
- Dashboard widget showing all unsigned repairs
- Supervisor override to extend deadline
- Configurable timeframes per urgency level
