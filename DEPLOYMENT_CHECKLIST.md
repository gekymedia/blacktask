# 🚀 BlackTask Deployment Checklist

## Pre-Deployment Setup

### 1. Register Policies ⚠️ REQUIRED

Add to `app/Providers/AppServiceProvider.php` in the `boot()` method:

```php
use App\Models\Task;
use App\Models\Category;
use App\Policies\TaskPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Task::class, TaskPolicy::class);
    Gate::policy(Category::class, CategoryPolicy::class);
}
```

### 2. Environment Configuration

```bash
# Copy environment file
cp env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Optional: Seed with sample data
php artisan db:seed
```

### 4. Install Dependencies

```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# JavaScript dependencies
npm install
npm run build
```

### 5. Optimize Application

```bash
# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## File Structure Overview

### New Files Created

```
app/
├── Http/
│   ├── Requests/
│   │   ├── StoreTaskRequest.php      ✅ NEW
│   │   └── UpdateTaskRequest.php     ✅ NEW
├── Policies/
│   ├── TaskPolicy.php                ✅ NEW
│   └── CategoryPolicy.php            ✅ NEW
└── Services/
    └── TaskService.php                ✅ NEW

database/
├── factories/
│   ├── TaskFactory.php                ✅ NEW
│   └── CategoryFactory.php            ✅ NEW
└── migrations/
    ├── 2025_08_06_091626_add_recurrence_to_tasks_table.php ✅ RENAMED
    └── 2025_12_16_000000_add_missing_columns_and_indexes_to_tasks.php ✅ NEW

resources/
├── js/
│   ├── components/
│   │   ├── taskManager.js            ✅ NEW
│   │   └── themeToggle.js            ✅ NEW
│   └── tasks.js                       ✅ NEW
└── views/
    ├── components/
    │   ├── task-item.blade.php       ✅ NEW
    │   ├── task-form.blade.php       ✅ NEW
    │   ├── notification.blade.php    ✅ NEW
    │   └── empty-state.blade.php     ✅ NEW
    └── tasks-improved.blade.php       ✅ NEW

tests/
├── Feature/
│   ├── TaskManagementTest.php        ✅ NEW
│   └── TaskRecurrenceTest.php        ✅ NEW
└── Unit/
    └── TaskServiceTest.php            ✅ NEW

Documentation/
├── ANALYSIS_AND_IMPROVEMENTS.md       ✅ NEW
├── IMPLEMENTATION_SUMMARY.md          ✅ NEW
├── DEPLOYMENT_CHECKLIST.md            ✅ NEW (this file)
├── README.md                          ✅ UPDATED
└── env.example                        ✅ NEW
```

### Modified Files

```
app/Http/Controllers/TaskController.php    ✅ REFACTORED
routes/web.php                             ✅ IMPROVED
routes/api.php                             ✅ IMPROVED
database/migrations/*.php                  ✅ FIXED
```

---

## Testing Checklist

```bash
# Run all tests
php artisan test

# Expected output: All tests should pass
# - TaskManagementTest: ~12 tests
# - TaskRecurrenceTest: ~6 tests
# - TaskServiceTest: ~10 tests
```

### Manual Testing Steps

1. **Registration & Login**
   - [ ] Register a new account
   - [ ] Login successfully
   - [ ] Logout works

2. **Task Management**
   - [ ] Create a new task
   - [ ] Mark task as complete
   - [ ] Reschedule task to tomorrow
   - [ ] Delete task
   - [ ] Category selection works
   - [ ] Priority selection works

3. **Authorization**
   - [ ] Cannot access other users' tasks
   - [ ] 403 error when trying to modify other user's task

4. **UI/UX**
   - [ ] Dark mode toggle works
   - [ ] Responsive design on mobile
   - [ ] Notifications appear correctly
   - [ ] Empty state shows when no tasks

---

## Production Deployment Checklist

### Security

- [ ] Change `APP_ENV` to `production`
- [ ] Set `APP_DEBUG` to `false`
- [ ] Generate new `APP_KEY`
- [ ] Use strong database credentials
- [ ] Enable HTTPS
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Configure security headers
- [ ] Enable CSRF protection (should be default)
- [ ] Review and restrict file permissions

### Database

- [ ] Use MySQL or PostgreSQL (not SQLite)
- [ ] Set up database backups
- [ ] Configure connection pooling
- [ ] Test database migrations
- [ ] Set up monitoring

### Performance

- [ ] Enable OPcache
- [ ] Configure Redis for cache/sessions
- [ ] Set up queue workers
- [ ] Configure CDN for static assets
- [ ] Enable Gzip compression
- [ ] Optimize images

### Monitoring

- [ ] Set up error tracking (Sentry, Bugsnag)
- [ ] Configure application monitoring
- [ ] Set up log aggregation
- [ ] Configure uptime monitoring
- [ ] Set up performance monitoring

### Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    server_name blacktask.com;
    root /var/www/blacktask/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://blacktask.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=blacktask_prod
DB_USERNAME=blacktask_user
DB_PASSWORD=strong-password-here

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

---

## Post-Deployment Verification

### Health Checks

1. **Application Health**
   ```bash
   curl https://blacktask.com/
   # Should return 200 OK
   ```

2. **API Health**
   ```bash
   curl https://blacktask.com/api/user \
     -H "Authorization: Bearer YOUR_TOKEN"
   # Should return user data
   ```

3. **Database Connection**
   ```bash
   php artisan tinker
   >>> User::count()
   # Should return number without error
   ```

### Performance Tests

```bash
# Run load tests
ab -n 1000 -c 10 https://blacktask.com/

# Check response times
# Target: < 200ms for most requests
```

---

## Rollback Plan

### If Issues Occur

1. **Keep previous version backup**
   ```bash
   cp -r /var/www/blacktask /var/www/blacktask.backup
   ```

2. **Database backup before migration**
   ```bash
   php artisan backup:database
   ```

3. **Quick rollback steps**
   ```bash
   # Restore code
   mv /var/www/blacktask /var/www/blacktask.failed
   mv /var/www/blacktask.backup /var/www/blacktask
   
   # Rollback database
   php artisan migrate:rollback
   
   # Clear cache
   php artisan cache:clear
   php artisan config:clear
   ```

---

## Support & Maintenance

### Daily Tasks

- [ ] Check application logs
- [ ] Monitor error rates
- [ ] Review performance metrics
- [ ] Check database size/growth

### Weekly Tasks

- [ ] Review security advisories
- [ ] Update dependencies (if needed)
- [ ] Database optimization
- [ ] Clear old logs

### Monthly Tasks

- [ ] Full security audit
- [ ] Performance review
- [ ] Backup verification
- [ ] Update documentation

---

## Quick Reference Commands

```bash
# Development
php artisan serve
npm run dev
php artisan test

# Production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Maintenance
php artisan down
php artisan up

# Queue Workers
php artisan queue:work
php artisan queue:restart

# Cache Management
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Troubleshooting

### Common Issues

**Issue**: 500 Internal Server Error
```bash
Solution: 
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

**Issue**: Database connection failed
```bash
Solution: 
Check .env database credentials
Test: php artisan tinker -> DB::connection()->getPdo()
```

**Issue**: Assets not loading
```bash
Solution:
npm run build
php artisan storage:link
```

**Issue**: Policies not working
```bash
Solution:
Verify policies are registered in AppServiceProvider
php artisan config:clear
```

---

## Contact & Support

- **Documentation**: See README.md
- **Issues**: GitHub Issues
- **Email**: support@blacktask.com

---

**Last Updated**: December 16, 2025  
**Version**: 2.0  
**Status**: Production Ready ✅

