# Project Setup Guide

## Prerequisites Installation

This project requires several system dependencies to be installed before you can proceed with Laravel setup. Follow these steps to prepare your system:

### 1. Install PHP 8.2+

For Ubuntu/Debian systems:
```bash
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-json php8.2-redis
```

For CentOS/RHEL/Fedora systems:
```bash
# Enable EPEL and Remi repositories
sudo dnf install epel-release
sudo dnf install https://rpms.remirepo.net/enterprise/remi-release-8.rpm  # For RHEL/CentOS 8
# For newer versions use appropriate Remi repo URL

# Enable PHP 8.2 module
sudo dnf module reset php
sudo dnf module enable php:remi-8.2

# Install PHP and extensions
sudo dnf install php php-cli php-common php-mysql php-zip php-gd php-mbstring php-curl php-xml php-bcmath php-json php-redis
```

### 2. Install Composer

```bash
# Download Composer installer
curl -sS https://getcomposer.org/installer | php

# Move to global location
sudo mv composer.phar /usr/local/bin/composer

# Make executable
sudo chmod +x /usr/local/bin/composer
```

### 3. Install Database System (Choose one)

#### For MySQL:
```bash
# Ubuntu/Debian
sudo apt install mysql-server mysql-client

# CentOS/RHEL/Fedora
sudo dnf install mysql-server mysql
```

#### For PostgreSQL:
```bash
# Ubuntu/Debian
sudo apt install postgresql postgresql-contrib

# CentOS/RHEL/Fedora
sudo dnf install postgresql-server postgresql
```

### 4. Install Redis
```bash
# Ubuntu/Debian
sudo apt install redis-server

# CentOS/RHEL/Fedora
sudo dnf install redis
```

### 5. Verify Installation
```bash
# Check PHP version
php -v

# Check Composer
composer --version

# Check PHP extensions
php -m | grep -E "(curl|gd|mbstring|xml|bcmath|zip|redis)"
```

---

## Laravel Project Creation

Once all prerequisites are installed, you can create the Laravel project:

```bash
# Create new Laravel 11 project
composer create-project laravel/laravel:^11.0 project-management-app

# Navigate to project directory
cd project-management-app

# Install additional packages required for the project
composer require laravel/sanctum
composer require pusher/pusher-php-server
composer require laravel/horizon
composer require spatie/laravel-permission
```

---

## Environment Configuration

After creating the Laravel project, configure the environment:

### 1. Copy Environment File
```bash
cp .env.example .env
```

### 2. Generate App Key
```bash
php artisan key:generate
```

### 3. Configure Database
Edit the `.env` file to set up your database connection:

```
DB_CONNECTION=mysql  # or pgsql for PostgreSQL
DB_HOST=127.0.0.1
DB_PORT=3306         # or 5432 for PostgreSQL
DB_DATABASE=project_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Configure Redis (for caching and queues)
```
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 5. Configure Pusher (for real-time features)
```
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=your_cluster
```

---

## Project Structure Setup for DDD + Clean Architecture

After Laravel is installed, you'll need to create the directory structure to support Domain-Driven Design and Clean Architecture:

```bash
# Create the main architecture directories
mkdir -p app/{Domain,Application,Infrastructure,Interfaces}

# Domain layer structure
mkdir -p app/Domain/{Entities,ValueObjects,Repositories,Services,Events}

# Application layer structure  
mkdir -p app/Application/{Commands,Handlers,DTOs,Services,Exceptions}

# Infrastructure layer structure
mkdir -p app/Infrastructure/{Persistence,Eloquent,Http/Controllers,Http/Requests,Http/Resources,Events/Listeners,Services/Mail}

# Interfaces layer structure (if separating API from application)
mkdir -p app/Interfaces/Http/{Controllers,Requests,Resources}
```

Then, update your `composer.json` to include the new namespaces:

```json
{
    "autoload": {
        "psr-4": {
            "App\\Domain\\": "app/Domain/",
            "App\\Application\\": "app/Application/",
            "App\\Infrastructure\\": "app/Infrastructure/",
            "App\\Interfaces\\": "app/Interfaces/"
        }
    }
}
```

And run:
```bash
composer dump-autoload
```

---

## Next Steps

After completing the prerequisites installation and Laravel setup:

1. Run database migrations:
   ```bash
   php artisan migrate
   ```

2. Install and start Laravel Horizon for queue management:
   ```bash
   php artisan horizon:install
   php artisan queue:table
   php artisan migrate
   php artisan horizon
   ```

3. Start the development server:
   ```bash
   php artisan serve
   ```

4. Begin implementing the Domain layer components as outlined in the project architecture documentation.

This setup guide provides all necessary steps to initialize the project management application with the DDD and Clean Architecture approach.