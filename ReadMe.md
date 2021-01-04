1. Change .env file for prod
2. ```composer install```
3. ```npm install --no-dev```
4. ```composer dump-env prod```
5. ```npm run-script build```
6. Change **RabbitMQ** credentials at _credentials.yaml_ file

----
**Apache config:**

```
<VirtualHost *:80>


    <IfModule mod_deflate.c>
      # compress text, html, javascript, css, xml:
      AddOutputFilterByType DEFLATE text/plain
      AddOutputFilterByType DEFLATE text/html
      AddOutputFilterByType DEFLATE text/xml
      AddOutputFilterByType DEFLATE text/css
      AddOutputFilterByType DEFLATE application/xml
      AddOutputFilterByType DEFLATE application/xhtml+xml
      AddOutputFilterByType DEFLATE application/rss+xml
      AddOutputFilterByType DEFLATE application/javascript
      AddOutputFilterByType DEFLATE application/x-javascript
      AddOutputFilterByType DEFLATE image/x-icon
    </IfModule>
    
    <IfModule mod_expires.c>
        <FilesMatch "\.(ogg|ogv|svg|svgz|eot|otf|woff|mp4|ttf|rss|atom|jpg|jpeg|gif|png|ico|zip|tgz|gz|rar|bz2|doc|xls|exe|ppt|tar|mid|midi|wav|bmp|rtf)$">
            Header set Cache-Control "max-age=2592000, must-revalidate"
        </FilesMatch>
    </IfModule>

   
       DocumentRoot /var/www/twitch/public
       <Directory /var/www/twitch/public>
           AllowOverride None
           Order Allow,Deny
           Allow from All
   
   	SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
   
           FallbackResource /index.php
       </Directory>
   
       # uncomment the following lines if you install assets as symlinks
       # or run into problems when compiling LESS/Sass/CoffeeScript assets
       # <Directory /var/www/project>
       #     Options FollowSymlinks
       # </Directory>
   
       # optionally disable the fallback resource for the asset directories
       # which will allow Apache to return a 404 error when files are
       # not found instead of passing the request to Symfony
       <Directory /var/www/twitch/public/bundles>
           FallbackResource disabled
       </Directory>
       ErrorLog /var/www/twitch/var/log/apache_error.log
       CustomLog /var/www/twitch/var/log/apache_access.log combined
   
       # optionally set the value of the environment variables used in the application
       #SetEnv APP_ENV prod
       #SetEnv APP_SECRET <app-secret-id>
       #SetEnv DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name"
</VirtualHost>

```

And enable mods:
1. ``expires``
2. ``headers``
3. ``deflate``
