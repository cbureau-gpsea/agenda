# Install room_agenda_display on Debian

This README.md is an operating mode to correctly install room_agenda_display on Debian. room_agenda_display is a local website to display room calendars on certain devices
Before you start, I advise you to log in as root with sudo -i.

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

To configurate apache2, you need to create a configuration file (.conf) with your url of your website (example : agenda.com).

```bash
nano /etc/apache2/sites-available/agenda.com.conf
```
```conf
<VirtualHost *:80>
    ServerName agenda.com # Replace by your url of your website

    DocumentRoot /var/www/html/room_agenda_display/src # You must indicate the path of index.php

    # If you want to place your website in a subfolder of your site (e.g. your virtual host is serving multiple applications),
    # you can use an Alias directive. If you do this, the DocumentRoot directive MUST NOT target your website directory itself.
    # Alias "/room_agenda_display" "/var/www/html/room_agenda_display/src"

    <Directory /var/www/html/room_agenda_display/src> # You must indicate the path of index.php
        Require all granted

        RewriteEngine On

        # Redirect all requests to GLPI router, unless file exists.
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php [QSA,L]
    </Directory>
</VirtualHost>
```

Now, you can activate your website.

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

Now that your server is ready, you can install the website. Download website, move the ["agenda"](https://github.com/cbureau-gpsea/room_agenda_display/tree/main/agenda) folder in *'/etc/'* and the website in *'/var/www/html'*.
```bash
wget "https://github.com/cbureau-gpsea/room_agenda_display/archive/refs/tags/Agenda.tar.gz"

mv room_agenda_display/agenda /etc/

chmod 755 /etc/agenda

chmod 666 /etc/agenda/config.json

mv room_agenda_display /var/www/html
```

## Display agendas

You can add different agendas on different devices. For that you must open *'/etc/agenda/config.json'*, you will have almost this :
```json
{
    "1": {
        "ip": "IP Device",
        "title_site": "Title of website",
        "intervalle": "10",
        "url_agenda": [
            "Link 1",
            "Link 2"
        ],
        "name_agenda": [
            "Title of link 1",
            "Title of link 2"
        ]
    }
}
```

To add a device you must copy table "1", add a comma at the end of table "1", paste below, rename "2" and so on, if you want to add more like this :

```json
{
    "1": {
        "ip": "IP Device",
        "title_site": "Title of website",
        "interval": "10",
        "url_agenda": [
            "Link 1",
            "Link 2"
        ],
        "name_agenda": [
            "Title of link 1",
            "Title of link 2"
        ]
    },
    "2": {
        "ip": "IP Device",
        "title_site": "Title of website",
        "interval": "10",
        "url_agenda": [
            "Link 1",
            "Link 2"
        ],
        "name_agenda": [
            "Title of link 1",
            "Title of link 2"
        ]
    },
    "3": {
        "ip": "IP Device",
        "title_site": "Title of website",
        "interval": "10",
        "url_agenda": [
            "Link 1",
            "Link 2"
        ],
        "name_agenda": [
            "Title of link 1",
            "Title of link 2"
        ]
    }
}
```

To configure a device you must fill fields of your table : 

| Fields | Explication |
| --- | --- |
| ip | You must add the IP address of the device on which you access the website. |
| title_site | This is the name of the site page. |
| interval | This is the interval to move from one calendar to another (in seconds). |
| url_agenda | You can add the URLs of your calendars here (note: the line must end with a comma except the last one). |
| name_agenda | You can add the names of your calendars here. The first name entered will be on the first agenda that you entered in 'url_agenda' and so on (note: the line must end with a comma except the last one). |

To have the calendar links, you must go to the calendar account settings.

![Account Outlook Parameter](https://github.com/cbureau-gpsea/room_agenda_display/blob/main/img/Capture%20d'%C3%A9cran%202024-11-06%20092319.png)

Go to "Calendar" > "Shared Calendars".

![Path Calendar](https://github.com/cbureau-gpsea/room_agenda_display/blob/main/img/Capture%20d'%C3%A9cran%202024-11-06%20092342.png)

Then in the "Publish a calendar" section, select "Calendar" then "Can display titles and locations" and click on Publish.

![Calendar Parameter](https://github.com/cbureau-gpsea/room_agenda_display/blob/main/img/Capture%20d'%C3%A9cran%202024-11-06%20092409.png)

You can copy the HTML link.

![Calendar Link](https://github.com/cbureau-gpsea/room_agenda_display/blob/main/img/Capture%20d'%C3%A9cran%202024-11-06%20092432.png)

Now you know how to add a device and configure it. I recommend that you make a reservation of the IP of the device added in your DHCP to avoid any problems if the IP changes.

## Error table

If you encounter errors, here are some explanations :

| Errors | Description |
| --- | --- |
| error 84 | Your configuration file is not in the right place, you should have this: *'/etc/agenda/config.json'* or the configuration file does not have reading rights, go to “Installation of the website”. |
| error 101 | You have not carried out any configuration for this device or the IP you gave is not the correct one. |
| error 102 | The configuration file does not have write rights, go to “Installation of the website” or make a reservation of the device IP so that it does not change. |
| error 104 | The configuration file is corrupt, check that you have not forgotten any commas in "url_agenda" or "name_agenda" and that you have not put any in the last lines, refer to the examples above. Please note, comments are prohibited ! |
