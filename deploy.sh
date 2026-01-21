set -e  # Exit on any error

echo "=========================================="
echo "Laravel Product Management Deployment"
echo "=========================================="

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

# Check if running as root (not recommended)
if [ "$EUID" -eq 0 ]; then 
    print_warning "Running as root is not recommended for Laravel applications"
fi

# Check PHP version
print_status "Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;" | cut -d. -f1,2)
REQUIRED_VERSION="8.2"

if (( $(echo "$PHP_VERSION < $REQUIRED_VERSION" | bc -l) )); then
    print_error "PHP $REQUIRED_VERSION or higher is required. Current version: $PHP_VERSION"
    exit 1
fi
print_status "PHP version: $PHP_VERSION"

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed. Please install Composer first."
    exit 1
fi

# Set environment
ENVIRONMENT="${1:-production}"
print_status "Deploying for environment: $ENVIRONMENT"

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    print_warning ".env file not found. Creating from .env.example..."
    cp .env.example .env
    print_status ".env file created. Please configure it before continuing."
    
    # Generate application key
    php artisan key:generate
    print_status "Application key generated"
fi

# Install/Update Composer dependencies
print_status "Installing Composer dependencies..."
if [ "$ENVIRONMENT" = "production" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist
else
    composer install --optimize-autoloader --no-interaction
fi
print_status "Composer dependencies installed"

# Install NPM dependencies and build assets
if command -v npm &> /dev/null; then
    print_status "Installing NPM dependencies..."
    npm ci
    
    print_status "Building frontend assets..."
    if [ "$ENVIRONMENT" = "production" ]; then
        npm run build
    else
        npm run dev
    fi
    print_status "Frontend assets built"
else
    print_warning "NPM not found. Skipping frontend build."
fi

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 storage bootstrap/cache
print_status "Permissions set"

# Clear and cache configuration
print_status "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

if [ "$ENVIRONMENT" = "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    print_status "Application optimized for production"
else
    print_status "Application optimized for development"
fi

# Database operations
print_status "Running database migrations..."
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    print_status "Migrations completed"
    
    # Seed database (only for non-production or first setup)
    if [ "$ENVIRONMENT" != "production" ]; then
        read -p "Do you want to seed the database? (y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            php artisan db:seed --force
            print_status "Database seeded"
        fi
    fi
else
    print_warning "Skipping migrations"
fi

# Clear application cache
print_status "Clearing application cache..."
php artisan cache:clear
#php artisan queue:restart

# Run security checks
print_status "Running security checks..."

# Check for .env in version control
if git check-ignore .env > /dev/null 2>&1; then
    print_status ".env is properly ignored in git"
else
    print_error ".env should be in .gitignore!"
fi

# Check storage permissions
if [ -w storage ]; then
    print_status "Storage directory is writable"
else
    print_error "Storage directory is not writable!"
fi

# Final status
echo ""
echo "=========================================="
echo -e "${GREEN}Deployment completed successfully!${NC}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Configure your web server (Nginx/Apache)"
echo "2. Set up SSL certificate"
echo "3. Configure your firewall"
echo "4. Set up monitoring and logging"
echo "5. Test the application thoroughly"
echo ""

if [ "$ENVIRONMENT" != "production" ]; then
    echo "You can start the development server with:"
    echo "php artisan serve"
    echo ""
    echo "Default admin credentials:"
    echo "Email: admin@gmail.com"
    echo "Password: password123"
    echo ""
    echo -e "${YELLOW}Remember to change these in production!${NC}"
fi