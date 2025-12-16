# 🗓️ BLACKTASK - Modern Task Management System

**BLACKTASK** is a powerful, elegant Laravel-based task management application designed to help you organize your daily tasks efficiently. With dark mode support, natural language date parsing, categories, priorities, and more.

![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

---

## ✨ Features

### Core Features
- ✅ **Task Management** - Create, update, complete, and delete tasks
- 📅 **Date-based Organization** - View and manage tasks by date
- 🎨 **Categories** - Organize tasks with color-coded categories
- ⚡ **Priority Levels** - Low, Medium, and High priority indicators
- 🔄 **Task Recurrence** - Daily, weekly, monthly, and yearly recurring tasks
- 🌓 **Dark Mode** - Beautiful dark theme with automatic theme detection
- 📱 **Progressive Web App (PWA)** - Install as an app on any device
- 🔐 **Secure Authentication** - Built with Laravel Breeze

### Advanced Features
- 🗣️ **Natural Language Input** - "Buy groceries tomorrow" automatically schedules for tomorrow
- 📊 **Analytics** - Track productivity and completion rates
- 📆 **Calendar View** - Visual calendar representation of tasks
- 🔔 **Task Reminders** - Optional reminder timestamps
- 📈 **Statistics Dashboard** - Insights into your task completion patterns

---

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL or SQLite

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/blacktask.git
cd blacktask
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blacktask
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Or use SQLite for development:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Build assets**
```bash
npm run build
# Or for development with hot reload:
npm run dev
```

7. **Start the server**
```bash
php artisan serve
```

8. **Visit the application**
```
http://localhost:8000
```

---

## 📖 Usage

### Creating Tasks

1. **Simple task**: Just type the task title and click "Add"
2. **With natural language**: Type "Buy milk tomorrow" - automatically schedules for tomorrow
3. **With category**: Select a category from the dropdown
4. **With priority**: Choose Low, Medium, or High priority

### Managing Tasks

- **Complete**: Click the checkbox to mark as done
- **Reschedule**: Click the calendar icon to move to tomorrow
- **Delete**: Click the trash icon to remove

### Categories

Create custom categories with colors to organize your tasks:
```php
// Example: Create a category programmatically
$user->categories()->create([
    'name' => 'Work',
    'color' => '#ef4444'
]);
```

### Recurring Tasks

Set up tasks that automatically recreate themselves:
- Daily (every day)
- Weekly (every 7 days)
- Monthly (same day each month)
- Yearly (same day each year)

---

## 🏗️ Architecture

### Project Structure

```
blacktask/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TaskController.php      # Task CRUD operations
│   │   │   ├── CalendarController.php  # Calendar view
│   │   │   └── AnalyticsController.php # Statistics
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php    # Task creation validation
│   │       └── UpdateTaskRequest.php   # Task update validation
│   ├── Models/
│   │   ├── Task.php                    # Task model
│   │   ├── Category.php                # Category model
│   │   └── User.php                    # User model
│   ├── Policies/
│   │   ├── TaskPolicy.php              # Task authorization
│   │   └── CategoryPolicy.php          # Category authorization
│   └── Services/
│       └── TaskService.php             # Business logic layer
├── database/
│   └── migrations/                     # Database schema
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── taskManager.js         # Task operations
│   │   │   └── themeToggle.js         # Dark mode
│   │   └── tasks.js                    # Main entry point
│   └── views/
│       ├── components/                 # Reusable Blade components
│       └── tasks.blade.php             # Main task view
└── routes/
    ├── web.php                         # Web routes
    └── api.php                         # API routes
```

### Design Patterns

- **Service Layer**: Business logic separated from controllers
- **Repository Pattern**: Data access abstraction (ready for implementation)
- **Policy-based Authorization**: Laravel policies for resource access control
- **Form Request Validation**: Dedicated request classes for validation
- **Component-based Views**: Reusable Blade components

---

## 🔒 Security Features

- ✅ **Authorization Policies** - Users can only access their own tasks
- ✅ **CSRF Protection** - All forms protected against CSRF attacks
- ✅ **SQL Injection Prevention** - Eloquent ORM with parameterized queries
- ✅ **XSS Protection** - All user input escaped in views
- ✅ **Password Hashing** - Bcrypt for secure password storage
- ✅ **API Authentication** - Sanctum for API token management

---

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TaskManagementTest.php

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/
│   ├── TaskManagementTest.php      # Task CRUD tests
│   ├── TaskAuthorizationTest.php   # Authorization tests
│   └── TaskRecurrenceTest.php      # Recurrence logic tests
└── Unit/
    ├── TaskServiceTest.php         # Service layer tests
    └── DateParserTest.php          # Date parsing tests
```

---

## 🛠️ Development

### Running in Development Mode

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server (hot reload)
npm run dev

# Optional Terminal 3: Watch for queue jobs
php artisan queue:work

# Optional Terminal 4: View logs
php artisan pail
```

Or use the convenient dev command:
```bash
composer dev
```

### Code Quality

```bash
# Format code
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

### Database Operations

```bash
# Fresh migration (WARNING: Destroys data)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback
```

---

## 📡 API Documentation

### Authentication

All API routes require Sanctum authentication token:

```bash
Authorization: Bearer {your-token}
```

### Endpoints

#### Get Tasks
```http
GET /api/tasks?start=2025-01-01&end=2025-12-31
```

Response:
```json
[
  {
    "id": 1,
    "title": "Buy groceries",
    "start": "2025-12-16",
    "color": "#ef4444",
    "allDay": true,
    "extendedProps": {
      "priority": 2,
      "is_done": false,
      "category": "Personal"
    }
  }
]
```

#### Get Statistics
```http
GET /api/tasks/statistics
```

Response:
```json
{
  "total": 150,
  "completed": 120,
  "pending": 30,
  "overdue": 5
}
```

#### Get Categories
```http
GET /api/categories
```

---

## 🎨 Customization

### Tailwind Configuration

Edit `tailwind.config.js` to customize colors, spacing, etc:

```js
module.exports = {
    theme: {
        extend: {
            colors: {
                primary: {
                    light: '#4f46e5',
                    dark: '#6366f1'
                }
            }
        }
    }
}
```

### Adding Custom Categories

```php
// In a seeder or tinker
Category::create([
    'name' => 'Fitness',
    'color' => '#10b981',
    'user_id' => 1
]);
```

---

## 🐛 Troubleshooting

### Common Issues

**Issue**: "Class 'App\Services\TaskService' not found"
```bash
Solution: composer dump-autoload
```

**Issue**: Migration errors
```bash
Solution: php artisan migrate:fresh (WARNING: Destroys data)
```

**Issue**: Assets not loading
```bash
Solution: npm run build && php artisan cache:clear
```

**Issue**: Permission denied on database file
```bash
Solution: chmod 664 database/database.sqlite
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Font Awesome](https://fontawesome.com) - Icon library
- [Luxon](https://moment.github.io/luxon/) - Date manipulation library

---

## 📧 Support

For support, email support@blacktask.com or open an issue on GitHub.

---

## 🗺️ Roadmap

- [ ] Mobile apps (iOS/Android)
- [ ] Task sharing and collaboration
- [ ] File attachments
- [ ] Task templates
- [ ] Integrations (Google Calendar, Outlook)
- [ ] AI-powered task suggestions
- [ ] Team workspaces
- [ ] Subtasks and task dependencies

---

**Made with ❤️ by the BLACKTASK Team**
