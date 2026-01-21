# Laravel Product Management System

A robust, enterprise-grade Laravel application implementing CRUD operations, role-based access control, and modern security practices.

## 🚀 Features

### Core Functionality
- ✅ **Authentication System** - Secure login with Laravel Breeze
- ✅ **Role-Based Access Control (RBAC)** - Admin vs Standard User roles
- ✅ **Product Management (CRUD)** - Full create, read, update, delete operations
- ✅ **Advanced Search & Filtering** - Optimized full-text search with filters
- ✅ **Rich Text Editor** - For product descriptions
- ✅ **Server-Side Validation** - Comprehensive input validation

### Architecture & Design Patterns
- ✅ **Repository Pattern** - Data access abstraction
- ✅ **Service Layer** - Business logic separation
- ✅ **Thin Controllers** - Clean, maintainable code
- ✅ **Dependency Injection** - Loose coupling
- ✅ **Interface Segregation** - Clean contracts

### Security Features
- ✅ **SQL Injection Prevention** - Eloquent ORM protection
- ✅ **XSS Protection** - HTML sanitization
- ✅ **CSRF Protection** - Token-based validation
- ✅ **Security Headers** - Comprehensive HTTP headers
- ✅ **Rate Limiting** - Request throttling
- ✅ **Input Sanitization** - Multi-layer validation

### Performance Optimization
- ✅ **Database Indexing** - Optimized queries
- ✅ **Full-Text Search** - MySQL full-text indexes
- ✅ **Query Caching** - Redis integration
- ✅ **Eager Loading** - N+1 query prevention
- ✅ **Asset Optimization** - Compiled CSS/JS

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0 or PostgreSQL >= 13
- Redis (optional, for caching)
- Node.js >= 18.x & NPM
- Git

## 🛠️ Installation

### Method 1: Quick Setup with Make

```bash
# Clone repository
git clone https://github.com/yourusername/laravel-product-management.git
cd laravel-product-management

# Quick setup
make install-full

# Start development server
make dev
```

### Method 2: Manual Installation

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Create storage link
php artisan storage:link

# Set permissions
chmod -R 755 storage bootstrap/cache

# Build frontend assets
npm run build

# Start server
php artisan serve
```

### Method 3: Docker Setup

```bash
# Start containers
docker-compose up -d

# Run migrations inside container
docker-compose exec app php artisan migrate --seed

# Access application
# http://localhost
```

## 🔧 Configuration

### Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=product_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Redis Configuration (Optional)

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 👥 Default Users

After seeding, you can login with:

**Admin Account:**
- Email: `admin@gmail.com`
- Password: `password123`

**Standard User Account:**
- Email: `user@gmail.com`
- Password: `password123`

⚠️ **Change these credentials immediately in production!**

## 📚 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php      # Thin controllers
│   ├── Requests/
│   │   ├── StoreProductRequest.php    # Validation
│   │   └── UpdateProductRequest.php
│   └── Middleware/
│       └── SecurityHeaders.php        # Security headers
├── Models/
│   ├── Product.php                    # Eloquent model
│   └── User.php
├── Repositories/
│   ├── Interfaces/
│   │   └── ProductRepositoryInterface.php
│   └── ProductRepository.php          # Data access layer
└── Services/
    └── ProductService.php             # Business logic

database/
├── migrations/
├── seeders/
│   └── RolePermissionSeeder.php       # RBAC setup
└── factories/
    └── ProductFactory.php

resources/
└── views/
    └── products/
        ├── index.blade.php            # Product listing
        ├── create.blade.php           # Create form
        ├── edit.blade.php             # Edit form
        └── show.blade.php             # Product details
```

## 🔒 Security Implementation

### SQL Injection Prevention
- Using Eloquent ORM with automatic parameterization
- Query Builder with parameter binding
- No raw SQL queries with user input

### XSS Protection
- Blade template engine auto-escaping with `{{ }}`
- HTML sanitization in service layer
- Allowed HTML tags whitelist
- Content Security Policy headers

### CSRF Protection
- Automatic token generation
- `@csrf` directive in forms
- Token validation on all mutations
- SameSite cookie attribute

### Additional Security
- Password hashing with bcrypt
- Rate limiting on routes
- Security headers middleware
- Input validation at multiple layers

## 🚀 Deployment

### Production Deployment

```bash
# Using deployment script
bash deploy.sh production

# Or using Make
make deploy
```

### Docker Deployment

```bash
# Build and start containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose exec app php artisan migrate --force
```

### Manual Production Setup

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Build assets
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# With coverage
php artisan test --coverage

# Using Make
make test
```

## 📊 Performance Optimization

### Database Optimization
- Full-text indexes on searchable columns
- Composite indexes for filtered queries
- Query result caching
- Eager loading relationships

### Application Optimization
- Config caching
- Route caching
- View caching
- Opcode caching (OPcache)
- Asset compression

### Redis Caching
- Product detail caching
- Query result caching
- Session storage in Redis

## 🔍 API Endpoints

| Method | URI | Action | Middleware |
|--------|-----|--------|------------|
| GET | `/products` | Index | auth |
| GET | `/products/create` | Create Form | auth, permission |
| POST | `/products` | Store | auth, permission |
| GET | `/products/{id}` | Show | auth |
| GET | `/products/{id}/edit` | Edit Form | auth, permission |
| PUT/PATCH | `/products/{id}` | Update | auth, permission |
| DELETE | `/products/{id}` | Destroy | auth, permission |

## 🛡️ Roles & Permissions

### Admin Role
- Full access to all products
- Can create, edit, delete any product
- User management capabilities
- System configuration access

### Standard User Role
- Can view all products
- Can create own products
- Can edit/delete only own products
- Limited system access

## 📈 Monitoring & Logging

All important actions are logged:
- Product creation
- Product updates
- Product deletion
- Authentication events
- Authorization failures
- Error occurrences

Logs location: `storage/logs/laravel.log`

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🙏 Acknowledgments

- Laravel Framework
- Spatie Laravel Permission
- Laravel Breeze
- TailwindCSS

## 📧 Support

For issues and questions, please open an issue on GitHub or contact support@yourcompany.com

---

**Built with ❤️ using Laravel**