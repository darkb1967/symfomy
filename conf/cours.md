<VirtualHost *:80>
    ServerName localhost
	ServerAdmin contact@example.com
	DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options +FollowSymlinks
        AllowOverride all
        Require all granted
        FallbackResource /index.php
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !favicon.ico$
        RewriteRule ^([a-z,A-Z,0-9,-,\/]+)([/]*)$ /index.php?p=$1 [QSA,L]

        # exemple
        # http://localhost:9000/articles/8/comments
        # internally rewite to
        # http://articles/8/comments
        
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>