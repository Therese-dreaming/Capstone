# SSL Setup Guide for Laravel Project in XAMPP

## 1. Generate SSL Certificate

1. Open OpenSSL command prompt (included with XAMPP)
2. Navigate to XAMPP's Apache conf directory:
   ```bash
   cd C:\xampp\apache\conf
   ```

3. Generate the private key:
   ```bash
   openssl genrsa -out server.key 2048
   ```

4. Generate the Certificate Signing Request (CSR):
   ```bash
   openssl req -new -key server.key -out server.csr
   ```
   - Fill in the prompts:
     - Country Name: Your country code (e.g., PH)
     - State: Your state
     - Locality: Your city
     - Organization Name: Your organization
     - Organizational Unit: Your department
     - Common Name: localhost
     - Email Address: Your email
     - Challenge password: (leave empty)
     - Optional Company Name: (leave empty)

5. Generate the self-signed certificate:
   ```bash
   openssl x509 -req -days 365 -in server.csr -signkey server.key -out server.crt
   ```

## 2. Configure Apache (httpd-ssl.conf)

1. Open `C:\xampp\apache\conf\extra\httpd-ssl.conf`
2. Find and update these sections:

```apache
<VirtualHost _default_:443>
    DocumentRoot "C:/xampp/htdocs"
    ServerName localhost:443
    ServerAdmin admin@localhost
    ErrorLog "C:/xampp/apache/logs/error.log"
    TransferLog "C:/xampp/apache/logs/access.log"

    SSLEngine on
    SSLCertificateFile "conf/server.crt"
    SSLCertificateKeyFile "conf/server.key"

    <FilesMatch "\.(cgi|shtml|phtml|php)$">
        SSLOptions +StdEnvVars
    </FilesMatch>

    <Directory "C:/xampp/cgi-bin">
        SSLOptions +StdEnvVars
    </Directory>

    BrowserMatch "MSIE [2-5]" \
        nokeepalive ssl-unclean-shutdown \
        downgrade-1.0 force-response-1.0
</VirtualHost>
```

## 3. Configure Apache (httpd.conf)

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Make sure these lines are uncommented:
```apache
LoadModule ssl_module modules/mod_ssl.so
LoadModule socache_shmcb_module modules/mod_socache_shmcb.so
Include conf/extra/httpd-ssl.conf
```

## 4. Configure Laravel

1. Update `.env` file:
```
APP_URL=https://localhost/capstone/public
```

2. Update `config/app.php`:
```php
'url' => env('APP_URL', 'https://localhost/capstone/public'),
```

## 5. Storage Link Setup

After moving the project to a new device:

1. Remove existing storage link:
```bash
rm public/storage
```

2. Create new storage link:
```bash
php artisan storage:link
```

## 6. Accessing the Application

- Local access: `https://localhost/capstone/public`
- Mobile access: `https://[your-computer-ip]/capstone/public`

To find your computer's IP:
```bash
ipconfig | findstr IPv4
```

## 7. Troubleshooting

1. If you get certificate warnings:
   - This is normal for self-signed certificates
   - Click "Advanced" or "More Information"
   - Click "Proceed to site" or "Accept the risk"

2. If images don't appear:
   - Check if storage link is created correctly
   - Verify file permissions
   - Check image paths in database

3. If Apache won't start:
   - Check error logs in `C:\xampp\apache\logs\error.log`
   - Verify SSL configuration in httpd-ssl.conf
   - Make sure port 443 is not in use

## 8. Important Notes

- Keep your SSL certificates secure
- Back up your certificates before system changes
- Remember to update APP_URL when moving to a new device
- Always use HTTPS for camera access in mobile browsers
- Make sure Apache and MySQL services are running in XAMPP 