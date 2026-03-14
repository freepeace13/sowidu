#!/usr/bin/env bash
set -e

DB_USERNAME=user
DB_PASSWORD=secret
DB_DATABASE=laravel

# --- Update system and install prerequisites ---
echo "🔄 Updating system..."
apt update -y
apt install -y lsb-release ca-certificates apt-transport-https software-properties-common curl gnupg build-essential unzip

# --- Add Sury PHP repository ---
echo "📦 Adding Sury PHP repository for Debian..."
curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/sury.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list

echo "🔄 Updating after adding repo..."
apt update -y

# --- Install MariaDB ---
echo "💾 Installing MariaDB server and client (MySQL compatible)..."
DEBIAN_FRONTEND=noninteractive apt install -y mariadb-server mariadb-client

echo "✅ Starting MariaDB service..."
if pidof systemd >/dev/null 2>&1; then
  systemctl enable mariadb
  systemctl start mariadb
else
  echo "⚙️ Non-systemd detected. Starting MariaDB manually..."
  mysqld_safe --datadir='/var/lib/mysql' &
  sleep 5
fi

# --- Create Laravel database and user ---
echo "💡 Creating Laravel database and user..."
MYSQL_ROOT_CMD="sudo mysql"
$MYSQL_ROOT_CMD <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS $DB_DATABASE;
CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_DATABASE.* TO '$DB_USERNAME'@'localhost';
FLUSH PRIVILEGES;
EXIT
MYSQL_SCRIPT
echo "✅ Laravel database '$DB_DATABASE' and user '$DB_USERNAME' created successfully."

# --- Installing Redis server ---
echo "💾 Installing Redis server and PHP Redis extension..."
apt install -y redis-server

echo "✅ Enabling and starting Redis service..."
if pidof systemd >/dev/null 2>&1; then
  systemctl enable redis-server
  systemctl start redis-server
else
  redis-server --daemonize yes
fi

echo "🔍 Verifying Redis service..."
if redis-cli ping | grep -q "PONG"; then
  echo "✅ Redis is running successfully."
else
  echo "⚠️ Redis service seems not running correctly. Please check logs."
fi

# --- Install PHP 8.1 and Laravel dependencies ---
echo "💾 Installing PHP 8.1 and Laravel dependencies..."
apt install -y \
  imagemagick imagemagick-doc ffmpeg optipng jpegoptim \
    php8.1-exif php8.1-cli php8.1-dev \
    php8.1-pgsql php8.1-sqlite3 \
    php8.1-curl php8.1-memcached \
    php8.1-imap php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
    php8.1-intl php8.1-readline php8.1-pcov \
    php8.1-msgpack php8.1-igbinary php8.1-ldap \
    php8.1-redis php8.1-swoole php8.1-xdebug \
    php8.1-imagick php8.1-gd

# --- Install Composer ---
echo "🧰 Installing Composer..."
apt install -y composer

# 🧰 Install NVM for the current non-root user
echo "🌐 Installing NVM (Node Version Manager) and latest Node.js LTS..."

# Determine the original (non-root) user who invoked sudo
if [ "$SUDO_USER" ]; then
  NVM_USER="$SUDO_USER"
  NVM_HOME=$(eval echo "~$NVM_USER")
else
  NVM_USER=$(whoami)
  NVM_HOME="$HOME"
fi

# Export NVM_DIR for that user
export NVM_DIR="$NVM_HOME/.nvm"

# Download and install NVM under that user
sudo -u "$NVM_USER" bash <<'EOF'
  export NVM_DIR="$HOME/.nvm"
  mkdir -p "$NVM_DIR"
  export PROFILE="$HOME/.bashrc"

  # Install NVM
  curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/master/install.sh | bash

  # Load NVM immediately for this session
  export NVM_DIR="$HOME/.nvm"
  [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

  # Install latest LTS Node.js version
  nvm install --lts
  nvm alias default 'lts/*'

  echo "✅ NVM and Node.js LTS installed successfully for user: $(whoami)"
EOF

# Ensure NVM loads on new shells
if [ -f "$NVM_HOME/.bashrc" ]; then
  grep -q 'NVM_DIR' "$NVM_HOME/.bashrc" || echo 'export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> "$NVM_HOME/.bashrc"
fi

# Install latest LTS Node.js
nvm install --lts
nvm use --lts
nvm alias default 'lts/*'

# --- Final status check ---
echo "✅ Installation complete!"
php -v
composer -V
mysql --version
node -v
npm -v

echo "🎉 Laravel-ready PHP 8.1 + MariaDB + Node.js environment installed successfully."
