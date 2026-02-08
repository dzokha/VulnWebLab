# INSTALL
## INSTALL XAMPP

sudo nano /Applications/XAMPP/xamppfiles/etc/httpd.conf
```

#Include etc/extra/httpd-vhosts.conf
```

sudo nano /Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf

```

<VirtualHost *:80>
    ServerName bwapp.local
    DocumentRoot "/Applications/XAMPP/htdocs/bwapp"

    <Directory "/Applications/XAMPP/htdocs/bwapp">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/bwapp_error.log"
    CustomLog "logs/bwapp_access.log" combined
</VirtualHost>
```
sudo nano /etc/hosts
```

127.0.0.1   localhost VulnWebLab.local
```
sudo /Applications/XAMPP/xamppfiles/xampp restartapache
