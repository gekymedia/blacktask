# 🔔 BLACKTASK Notification System Guide

## Overview

BLACKTASK features a comprehensive multi-channel notification system that allows users to receive task reminders through various platforms including browser notifications, email, WhatsApp, SMS, GeKyChat, push notifications, and Telegram.

## Supported Notification Channels

### 1. 🖥️ Browser Notifications
**Type**: In-browser alerts
**Setup**: Click "Enable Notifications" on dashboard
**Requirements**: HTTPS in production, modern browser
**Features**:
- Instant alerts when tasks are due
- Customizable notification content
- Action buttons (Mark Complete, View Task)

### 2. 📧 Email Notifications
**Type**: HTML emails with task details
**Setup**: Configure mail settings in `.env`
**Requirements**: Mail server configuration
**Features**:
- Rich HTML templates
- Task priority indicators
- Direct links to task management
- Daily digest summaries

### 3. 💬 WhatsApp Notifications
**Type**: WhatsApp Business API
**Setup**: Facebook Business account + WhatsApp Business API
**Requirements**: Phone number required
**Features**:
- Formatted messages with emojis
- Task details and due dates
- Priority indicators

### 4. 📱 SMS Notifications
**Type**: Text messages via SMS providers
**Setup**: Twilio/Nexmo/AWS SNS account
**Requirements**: Phone number required
**Features**:
- Concise text format
- Delivery confirmations
- Cost-effective for critical alerts

### 5. 💭 GeKyChat Notifications
**Type**: Custom chat platform integration
**Setup**: GeKyChat API credentials
**Requirements**: Phone number required
**Features**:
- Rich message format
- JSON payload support
- Customizable message templates

### 6. 📲 Push Notifications
**Type**: Web push notifications
**Setup**: VAPID key generation
**Requirements**: HTTPS, modern browser with service worker support
**Features**:
- Background delivery
- Works when app is closed
- Mobile device support
- Action buttons

### 7. ✈️ Telegram Notifications
**Type**: Telegram Bot API
**Setup**: Create Telegram bot + webhook setup
**Requirements**: Telegram account
**Features**:
- Bot-based messaging
- Rich text formatting
- Interactive setup process
- Group chat support

## Quick Setup Guide

### Step 1: Database Migration
```bash
php artisan migrate
```

### Step 2: Environment Configuration
Add to your `.env` file:

```env
# Push Notifications
PUSH_ENABLED=true
VAPID_PUBLIC_KEY=your_generated_public_key
VAPID_PRIVATE_KEY=your_generated_private_key
VAPID_SUBJECT=mailto:admin@blacktask.com

# Telegram
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=your_bot_token_here

# WhatsApp
WHATSAPP_ENABLED=true
WHATSAPP_API_URL=https://graph.facebook.com/v17.0
WHATSAPP_TOKEN=your_access_token
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id

# SMS (Twilio example)
SMS_ENABLED=true
SMS_PROVIDER=twilio
SMS_API_URL=https://api.twilio.com/2010-04-01/Accounts/YOUR_SID/Messages.json
SMS_TOKEN=your_auth_token
SMS_FROM_NUMBER=+1234567890

# GeKyChat
GEKYCHAT_ENABLED=true
GEKYCHAT_API_URL=https://api.gekychat.com/messages
GEKYCHAT_TOKEN=your_token

# Email (already configured in Laravel)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

### Step 3: Generate VAPID Keys
```bash
php artisan notifications:generate-vapid-keys
```

### Step 4: Start Queue Worker
```bash
php artisan queue:work
```

## Detailed Setup Instructions

### Push Notifications Setup

1. **Generate VAPID Keys**:
   ```bash
   php artisan notifications:generate-vapid-keys
   ```

2. **HTTPS Required**: Push notifications require HTTPS in production.

3. **Service Worker**: Automatically registered at `/sw.js`.

### Telegram Setup

1. **Create Bot**:
   - Message @BotFather on Telegram
   - Use `/newbot` command
   - Follow setup instructions
   - Copy the bot token

2. **Configure Webhook** (optional):
   ```bash
   curl -X POST "https://api.telegram.org/bot{YOUR_BOT_TOKEN}/setWebhook" \
        -d "url=https://yourdomain.com/telegram/webhook"
   ```

3. **User Setup**: Users click "Setup Telegram" in settings to connect their account.

### WhatsApp Business API Setup

1. **Create Facebook Business Account**
2. **Set up WhatsApp Business API**
3. **Get Phone Number ID and Token**
4. **Configure in .env**

### SMS Setup (Twilio)

1. **Create Twilio Account**
2. **Get Account SID and Auth Token**
3. **Purchase Phone Number**
4. **Configure in .env**

## User Guide

### Managing Notification Preferences

1. **Go to Settings**: Click Settings in the navigation
2. **Toggle Channels**: Enable/disable each notification type
3. **Phone Number**: Required for phone-based notifications
4. **Daily Time**: Set preferred reminder time
5. **Save Settings**: Changes apply immediately

### Setting Up Individual Channels

#### Browser Notifications
- Click "Enable Notifications" on dashboard
- Grant permission when prompted
- Visual indicator shows when enabled

#### Telegram Setup
- Go to Settings
- Click "Setup Telegram" link
- Follow Telegram bot instructions
- Return to BlackTask settings

#### Push Notifications
- Automatically enabled when browser notifications are granted
- Works in background when app is closed
- Service worker handles delivery

## Testing Notifications

### Test All Channels
```bash
php artisan notifications:test user@example.com
```

### Test Specific Channel
```bash
php artisan notifications:test user@example.com --channel=email
```

### Available Channels
- `browser`, `email`, `whatsapp`, `sms`, `gekychat`, `push`, `telegram`

## API Integration

### Send Notifications Programmatically

```php
use App\Services\NotificationService;

$service = app(NotificationService::class);

// Send task reminder
$sent = $service->sendTaskReminder($user, $task);
// Returns: ['email', 'whatsapp', 'telegram'] (enabled channels)

// Send daily digest
$tasks = $user->tasks()->whereDate('task_date', today())->get();
$sent = $service->sendDailyDigest($user, $tasks);
```

### Telegram Bot Info
```php
$botInfo = $service->getTelegramBotInfo();
// Returns bot details or null if not configured
```

## Troubleshooting

### Common Issues

**Push notifications not working:**
- Ensure HTTPS in production
- Check VAPID keys are generated
- Verify service worker is registered

**Telegram setup fails:**
- Verify bot token is correct
- Check webhook URL is accessible
- Ensure user has Telegram account

**WhatsApp messages not sending:**
- Verify Business API credentials
- Check phone number format
- Confirm WhatsApp Business account is approved

**SMS delivery fails:**
- Check provider credentials
- Verify phone number format
- Check account balance/credits

### Debug Commands

```bash
# Check migration status
php artisan migrate:status

# View application logs
php artisan pail

# Test specific notification
php artisan notifications:test --channel=email

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## Security Considerations

- **API Keys**: Store securely, never in version control
- **Phone Numbers**: Encrypted in database
- **Rate Limiting**: Built-in protection against abuse
- **Permission Checks**: Users can only manage their own notifications
- **HTTPS Required**: For push notifications and security

## Performance Optimization

- **Queued Notifications**: All external API calls are queued
- **Background Processing**: Notifications don't block user requests
- **Error Handling**: Failed notifications are logged, not retried
- **Batch Processing**: Daily digests combine multiple notifications

## Monitoring & Analytics

### Queue Monitoring
```bash
# View queue status
php artisan queue:status

# Monitor failed jobs
php artisan queue:failed
```

### Log Monitoring
```bash
# View notification logs
tail -f storage/logs/laravel.log | grep notification
```

### Database Monitoring
```sql
-- Check notification preferences
SELECT email_notifications, whatsapp_notifications, sms_notifications,
       gekychat_notifications, push_notifications, telegram_notifications
FROM users WHERE id = 1;

-- Check recent notification logs
SELECT * FROM activity_logs
WHERE action LIKE '%notification%'
ORDER BY created_at DESC LIMIT 10;
```

## Cost Considerations

### Free Channels
- Browser notifications
- Email (if using own SMTP)
- Telegram

### Paid Channels
- WhatsApp Business API
- SMS (per message)
- GeKyChat (depending on plan)

### Cost Optimization
- Use email for bulk notifications
- Reserve SMS for critical alerts
- Implement user preferences to reduce unnecessary sends

## Future Enhancements

- **Mobile App Push**: Native iOS/Android push notifications
- **Notification Templates**: Customizable message formats
- **Advanced Scheduling**: Time-based delivery preferences
- **Notification Analytics**: Delivery success tracking
- **Bulk Operations**: Admin notification management
- **Integration APIs**: Third-party service integrations

## Support

For setup assistance or troubleshooting:

1. **Check Logs**: Review `storage/logs/laravel.log`
2. **Test Commands**: Use `php artisan notifications:test`
3. **Configuration**: Verify `.env` settings
4. **Documentation**: Refer to this guide
5. **Community**: Check GitHub issues

---

**Version**: 1.0
**Last Updated**: December 23, 2025
**Compatibility**: BLACKTASK v2.0+
