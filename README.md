# Install room_agenda_display on Debian

This README.md is an operating mode to correctly install room_agenda_display on Debian. I advise you to log in as root with sudo -i.

## Installation and configuration of Apache2 and PHP

First, you can install Apache2, PHP and MariaDB to host your website.

### Download Apache2

```bash
apt update

apt install apache2

systemctl status apache2
```

### Download and installation PHP 8.3
```bash
apt-get update

apt-get -y install lsb-release ca-certificates curl

curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb

dpkg -i /tmp/debsuryorg-archive-keyring.deb

sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'

apt-get update

apt-get install php8.3
```
### Configuration Apache2

To configurate apache2, you need to create a configuration file (.conf) with your url of your website (example : agenda.com)

```bash
nano /etc/apache2/sites-available/agenda.com.conf
```
```conf
<VirtualHost *:80>
    ServerName agenda.com # Replace by your url of your website

    DocumentRoot /var/www/html/agenda/src # You must indicate the path of index.php

    # If you want to place your website in a subfolder of your site (e.g. your virtual host is serving multiple applications),
    # you can use an Alias directive. If you do this, the DocumentRoot directive MUST NOT target your website directory itself.
    # Alias "/agenda" "/var/www/html/agenda/src"

    <Directory /var/www/html/agenda/src> # You must indicate the path of index.php
        Require all granted

        RewriteEngine On

        # Redirect all requests to GLPI router, unless file exists.
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
</VirtualHost>
```

Now, you can activate your website

```bash
a2ensite support.gpsea.fr.conf

a2dissite 000-default.conf

a2enmod rewrite

systemctl restart apache2
```

### Installation PHP 8.3-FPM with Apache2

```bash
apt-get install php8.3-fpm

a2enmod proxy_fcgi setenvif

a2enconf php8.3-fpm

systemctl reload apache2
```

You must activate "*session.cookie_httponly*" indicating "on" in this file : 

```bash
nano /etc/php/8.3/fpm/php.ini
```

And restart php8.3-fpm :

```bash
systemctl restart php8.3-fpm.service
```

Add these lines in your configuration file and restart Apache2 :

```bash
nano /etc/apache2/sites-available/support.gpsea.fr.conf
```
```conf
<FilesMatch \.php$>
    SetHandler "proxy:unix:/run/php/php8.3-fpm.sock|fcgi://localhost/"
</FilesMatch>
```
```bash
systemctl restart apache2
```

## Installation of website

Now that your server is ready, you can install the website. Download website and move the["agenda"](https://github.com/cbureau-gpsea/room_agenda_display/tree/main/agenda) folder in *'/etc/'*
```bash
wget [link]

mv agenda/agenda /etc/
```



## Error table
| Errors | Description |
| --- | --- |
| error 84 | List all new or modified files |
| error 101 | Show file differences that haven't been staged |
| error 102 | Show file differences that haven't been staged |
| error 103 | Show file differences that haven't been staged |
