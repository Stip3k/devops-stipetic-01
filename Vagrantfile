Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/jammy64"
  config.vm.hostname = "laravel-app"
  config.vm.network "forwarded_port", guest: 80, host: 8000, auto_correct: true
  config.vm.network "forwarded_port", guest: 443, host: 8443, auto_correct: true
  config.vm.synced_folder ".", "/vagrant"
  
  config.vm.provider "virtualbox" do |vb|
    vb.name   = "laravel-app"
    vb.memory = 2048
    vb.cpus   = 2
  end
  
  config.vm.provision "shell", inline: <<-SHELL
    set -e
    
    echo "[*] Update paketov..."
    apt-get update -y
    
    echo "[*] Namestitev odvisnosti..."
    DEBIAN_FRONTEND=noninteractive apt-get install -y \
      nginx \
      mysql-server \
      php-fpm \
      php-mysql \
      php-mbstring \
      php-xml \
      php-bcmath \
      php-zip \
      php-curl \
      unzip \
      git
            
    echo "[*] Konfiguracija MySQL..."
    mysql -e "CREATE DATABASE IF NOT EXISTS ucilnice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS 'ucilnica'@'localhost' IDENTIFIED BY 'ucilnica';"
    mysql -e "GRANT ALL PRIVILEGES ON ucilnice.* TO 'ucilnica'@'localhost'; FLUSH PRIVILEGES;"
    
    # Uvoz baze (če obstaja)
    if [ -f /vagrant/db.sql ]; then
      echo "[*] Uvažam bazo podatkov..."
      mysql ucilnice < /vagrant/db.sql
    fi
    
    echo "[*] Namestitev Composerja..."
    if ! command -v composer >/dev/null 2>&1; then
      curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
      php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
      rm /tmp/composer-setup.php
    fi
    
    echo "[*] Priprava Laravel aplikacije..."
    # Premik aplikacije
    if [ -d /vagrant/ucilnice ]; then
      rm -rf /var/www/ucilnice
      cp -r /vagrant/ucilnice /var/www/
    else
      echo "[!] NAPAKA: /vagrant/ucilnice mapa ne obstaja!"
      exit 1
    fi
    
    cd /var/www/ucilnice
    
    # .env konfiguracija
    if [ ! -f .env ]; then
      cp .env.example .env
      sed -i 's/DB_DATABASE=.*/DB_DATABASE=ucilnice/' .env
      sed -i 's/DB_USERNAME=.*/DB_USERNAME=ucilnica/' .env
      sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=ucilnica/' .env
    fi
    
    # Composer dependencies
    echo "[*] Namestitev Laravel odvisnosti..."
    composer install --no-interaction --optimize-autoloader
    
    # Laravel setup in optimiyacija
    php artisan key:generate --force
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache

    # Permissions
    chown -R www-data:www-data /var/www/ucilnice
    chmod -R 777 /var/www/ucilnice/storage /var/www/ucilnice/bootstrap/cache

    mkdir -p /etc/nginx/ssl
    cp /vagrant/ssl/laravel.crt /etc/nginx/ssl/laravel.crt
    cp /vagrant/ssl/laravel.key /etc/nginx/ssl/laravel.key
    chmod -R 600 /etc/nginx/ssl/
    
    echo "[*] Konfiguracija Nginx..."
    cat > /etc/nginx/sites-available/laravel <<'NGINX_CONF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2 default_server;
    listen [::]:443 ssl http2 default_server;

    server_name st.stipek.si localhost _;
    root /var/www/ucilnice/public;
    index index.php index.html;

    ssl_certificate /etc/nginx/ssl/laravel.crt;
    ssl_certificate_key /etc/nginx/ssl/laravel.key;

    # SSL Security Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    # CSP dovoli inline scripts in styles za Laravel/Vite
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    # Handle Laravel Routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP Processing
    location ~ .php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_index index.php;
        fastcgi_hide_header X-Powered-By;
    }

    # Static Files Caching
    location ~* .(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security: Deny access to hidden files
    #location ~ /. {
    #    deny all;
    #}

    # Security: Deny access to sensitive files
    location ~ /(.env|.git|composer.(json|lock)|package.json) {
        deny all;
    }

    # Favicon and robots.txt
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
}
NGINX_CONF

    rm -f /etc/nginx/sites-enabled/default
    ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
    
    echo "[*] Restart storitev..."
    systemctl enable nginx php8.1-fpm
    systemctl restart nginx php8.1-fpm
    
    IP=$(hostname -I | awk '{print $1}')
    echo "Aplikacija dostopna na http://$IP:8000"
  SHELL
end