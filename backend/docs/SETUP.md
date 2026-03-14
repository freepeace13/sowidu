# Sowidu - Setup Guide

This guide will help you set up the Sowidu application for local development using Laravel Sail.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **Git** - Version control
- **Docker Desktop** - For running Laravel Sail containers
- **Docker Compose** - Usually bundled with Docker Desktop
- **SSH Key configured for GitHub** - Required for cloning private packages

### System Requirements

- **OS**: Linux, macOS, or Windows with WSL2
- **Docker**: Version 20.10 or higher
- **Docker Compose**: Version 1.29 or higher
- **Disk Space**: At least 5GB free space

## Installation Steps

### 1. Clone the Repository

Clone the main Sowidu application repository:

```bash
git clone git@github.com:sowiduteam/app-web.git sw-web
cd sw-web
```

### 2. Check System Requirements

Before proceeding, run the requirements checker to ensure your system has everything needed:

```bash
./scripts/check_requirements.sh
```

This script will verify:
- Docker and Docker Compose installation
- Git and SSH configuration for GitHub
- Required PHP extensions in the Docker image
- Port availability (80, 3306, 6379, 8025)
- Disk space availability

If any critical checks fail, fix them before continuing. The script will provide helpful error messages and suggestions.

### 3. Clone Private Packages

The application depends on several private packages. Run the provided script to clone them:

```bash
# Make sure you're in the project root
./scripts/clone_packages.sh
```

This script will clone the following packages into the `packages/` directory:
- `translation`
- `contacts`
- `addressable`
- `avatarable`
- `active-status`

**Note**: Packages that already exist will be skipped automatically.

### 4. Setup Environment File

Copy the local environment template to create your `.env` file:

```bash
cp env.local .env
```

#### Configure Environment Variables

Open the `.env` file and update the following variables according to your setup:

**Database Configuration:**
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sowidu
DB_USERNAME=sail
DB_PASSWORD=password
```

**Redis Configuration:**
```env
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Application Configuration:**
```env
APP_NAME=Sowidu
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

**Mail Configuration:**
```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

> ⚠️ **Important**: Make sure to update any credentials, API keys, and service configurations specific to your development environment.

### 5. Install PHP Dependencies

Install Composer dependencies using Docker (no local PHP installation required):

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install
```

This command:
- Runs a temporary Docker container with PHP 8.0 and Composer
- Mounts your project directory
- Installs all PHP dependencies (verified by the requirements check in step 2)
- Removes the container after completion

**Required PHP Extensions** (included in the `laravelsail/php80-composer` image):
- bcmath, curl, exif, gd, imagick, intl, mbstring
- mysql, pdo_mysql, pcov, redis, soap, xml, zip

#### Troubleshooting Composer Install

**If composer install fails with extension errors:**

The Laravel Sail image should have all required extensions, but if you encounter errors like:
```
The requested PHP extension ext-xxx is missing from your system
```

**Solutions:**

1. **Verify the Docker image** has the extension:
   ```bash
   docker run --rm laravelsail/php80-composer:latest php -m | grep -i xxx
   ```

2. **Use a custom Dockerfile** (if extension is missing from Sail image):
   - The project includes custom Dockerfiles in `docker/8.0/` and `docker/8.1/`
   - These have additional extensions like imagick, ffmpeg, ghostscript
   - See "Alternative: Using Custom Docker Setup" section below

3. **Check composer output** for specific missing extensions and install them in your custom Dockerfile

**Note**: While your `composer.json` doesn't explicitly require extensions, many packages (like `spatie/image`, `barryvdh/laravel-dompdf`, `php-ffmpeg/php-ffmpeg`) need specific extensions at runtime. The Laravel Sail image includes these by default.

### 6. Start Laravel Sail

Start the development environment using Laravel Sail:

```bash
# Start all services in the background
./vendor/bin/sail up -d
```

Available Sail services:
- **MySQL** - Database server (port 3306)
- **Redis** - Cache and queue backend (port 6379)
- **Mailhog** - Mail testing (port 8025)
- **Application** - Laravel app (port 80)

### 7. Generate Application Key

Generate the application encryption key:

```bash
./vendor/bin/sail artisan key:generate
```

### 8. Run Database Migrations

Create the database schema:

```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Run migrations with seeders (optional)
./vendor/bin/sail artisan migrate --seed
```

### 9. Install Node Dependencies

Install JavaScript dependencies:

```bash
./vendor/bin/sail npm install
```

### 10. Build Frontend Assets

Compile the frontend assets:

```bash
# For development (with hot reload)
./vendor/bin/sail npm run dev

# Or run in watch mode
./vendor/bin/sail npm run watch
```

## Accessing the Application

Once everything is set up, you can access:

- **Application**: http://localhost
- **Mailhog** (Email testing): http://localhost:8025
- **MySQL**: `localhost:3306` (using any MySQL client)

## Common Sail Commands

Here are some frequently used Laravel Sail commands:

```bash
# Start services
./vendor/bin/sail up -d

# Stop services
./vendor/bin/sail down

# View logs
./vendor/bin/sail logs

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run composer commands
./vendor/bin/sail composer [command]

# Run npm commands
./vendor/bin/sail npm [command]

# Access the container shell
./vendor/bin/sail shell

# Run tests
./vendor/bin/sail test
```

### Creating a Bash Alias

To make your life easier, create a bash alias for Sail:

```bash
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

Now you can use `sail` instead of `./vendor/bin/sail`:

```bash
sail up -d
sail artisan migrate
sail npm run dev
```

## Troubleshooting

### Port Conflicts

If you get port conflict errors, check which services are using the required ports:

```bash
# Check what's using port 80
sudo lsof -i :80

# Check what's using port 3306
sudo lsof -i :3306
```

You can modify the ports in `docker-compose.yml` if needed.

### Permission Issues

If you encounter permission issues with files created by Docker:

```bash
# Fix ownership of files
sudo chown -R $USER:$USER .
```

### Container Issues

If containers aren't starting properly:

```bash
# Stop all containers
./vendor/bin/sail down

# Remove volumes (⚠️ This will delete database data)
./vendor/bin/sail down -v

# Rebuild containers
./vendor/bin/sail build --no-cache

# Start fresh
./vendor/bin/sail up -d
```

### Package Cloning Issues

If the `clone_packages.sh` script fails:

1. Ensure your SSH key is added to GitHub: `ssh -T git@github.com`
2. Verify you have access to the sowiduteam organization
3. Check if packages already exist: `ls -la packages/`

## Alternative: Using Custom Docker Setup

If you need additional PHP extensions or system packages not included in the standard Laravel Sail image, this project includes custom Dockerfiles with enhanced capabilities.

### Custom Docker Images Include:

- **All standard PHP 8.0/8.1 extensions**
- **ImageMagick** with full support (imagick extension)
- **FFmpeg** for video processing
- **Ghostscript** for PDF manipulation
- **Node.js 14.20.0** via NVM
- **Optimizers**: optipng, jpegoptim
- Additional extensions: exif, swoole, xdebug, etc.

### Using Custom Docker Setup:

The `docker-compose.yml` is already configured to use the custom image from `docker/8.1/`:

```bash
# Build the custom Docker image
docker-compose build

# Start services with custom image
docker-compose up -d

# Install dependencies (container already running)
docker-compose exec sowidu.test composer install

# Run artisan commands
docker-compose exec sowidu.test php artisan key:generate
docker-compose exec sowidu.test php artisan migrate --seed

# Install frontend dependencies
docker-compose exec sowidu.test npm install
docker-compose exec sowidu.test npm run dev
```

### When to Use Custom Docker Setup:

- ✅ You need **Ghostscript** for PDF processing
- ✅ You need **FFmpeg** for video/audio manipulation
- ✅ You need specific **PHP extensions** not in standard Sail
- ✅ You need **precise Node.js version control** (NVM)
- ✅ You're **deploying to production** with similar environment

### When to Use Standard Sail:

- ✅ Quick local development
- ✅ Standard Laravel features only
- ✅ Don't need special system packages
- ✅ Faster initial setup

## Next Steps

After setup is complete:

1. Review the development workflow in [`DEV.md`](./DEV.md)
2. Check the deployment process in [`DEPLOY.md`](./DEPLOY.md)
3. Familiarize yourself with the codebase structure in [`docs/directory-structure.md`](./docs/directory-structure.md)
4. Read about Action Class standards in [`.cursor/rules/action-classes.mdc`](./.cursor/rules/action-classes.mdc)

## Getting Help

If you encounter any issues during setup:

1. Check the Laravel Sail documentation: https://laravel.com/docs/sail
2. Review the project's existing documentation
3. Contact the development team

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Vue.js Documentation](https://vuejs.org)

