

Please wait while Enlightn scans your code base...

|------------------------------------------
| Running Performance Checks
|------------------------------------------

Check 1/67: Your application has the Composer autoloader optimization configured in producti
on. Not Applicable

Check 2/67: A proper cache driver is configured. Passed

Check 3/67: Your application caches compiled assets for improved performance. Not Applicable

Check 4/67: Aggregation is done at the database query level rather than at the Laravel Colle
ction level. Passed

Check 5/67: Application config caching is configured properly. Passed

Check 6/67: Your application does not use the debug log level in production. Passed

Check 7/67: Dev dependencies are not installed in production. Passed

Check 8/67: Your application does not contain env function calls outside of your config file
s. Passed

Check 9/67: Your application uses Horizon when using the Redis queue driver. Not Applicable 

Check 10/67: Your application minifies assets in production. Passed

Check 11/67: MySQL is configured properly on single server setups. Failed
When MySQL is running on the same server as your app, it is recommended to use unix sockets 
instead of TCP ports to improve performance by upto 50% (Percona benchmark).
At \config\database.php: line(s): 53.
Documentation URL: https://www.laravel-enlightn.com/docs/performance/mysql-single-server-ana
lyzer.html

Check 12/67: OPcache is enabled. Failed
OPcache is currently disabled. OPcache can give your application a significant performance b
oost and it is recommended to enable it in production.
Documentation URL: https://www.laravel-enlightn.com/docs/performance/opcache-analyzer.html  

Check 13/67: A proper queue driver is configured. Passed

Check 14/67: Application route caching is configured properly. Passed

Check 15/67: A proper session driver is configured. Passed

Check 16/67: Your application does not use locks on your default cache store. Not Applicable

Check 17/67: Your application does not contain unused global HTTP middleware. Failed        
Your application contains global middleware that is not currently being used. It is recommen
ded to remove these middleware from your Kernel class to enhance performance slightly. Your 
unused middleware include: [TrustProxies]
Documentation URL: https://www.laravel-enlightn.com/docs/performance/unused-global-middlewar
e-analyzer.html

Check 18/67: View caching is configured properly. Passed

|------------------------------------------
| Running Reliability Checks
|------------------------------------------

Check 19/67: Cache prefix is set to avoid collisions with other apps. Failed
Your cache prefix is too generic and may result in collisions with other apps that share the
 same cache servers. In general, this should be fixed if you set a non-generic app name iny 
our .env file.
At \config\cache.php: line(s): 106.
Documentation URL: https://www.laravel-enlightn.com/docs/reliability/cache-prefix-analyzer.h
tml

Check 20/67: Your application cache is working. Passed

Check 21/67: Your application's composer.json file is valid. Passed

Check 22/67: Your application defines custom error page views. Failed
Your application does not customize its error pages. This may hamper user experience and als
o exposes your application to fingerprinting, which means potential attackers can identify L
aravel as your framework.
Documentation URL: https://www.laravel-enlightn.com/docs/reliability/custom-error-page-analy
zer.html

Check 23/67: Database is accessible. Passed

Check 24/67: Your application does not contain any dead or unreachable code. Passed

Check 25/67: Your application does not use any deprecated code. Passed

Check 26/67: Your storage and cache directories are writable. Passed

Check 27/67: All env variables used in your .env file are defined in your .env.example file.
 Failed
Your application has some missing environment variables that are defined in your .env file b
ut missing in your .env.example file: APP_MAINTENANCE_STORE, DB_HOST, DB_PORT, DB_DATABASE, 
DB_USERNAME and DB_PASSWORD
Documentation URL: https://www.laravel-enlightn.com/docs/reliability/env-example-analyzer.ht
ml

Check 28/67: A .env file exists for your application. Passed

Check 29/67: All env variables defined in your example file are set in your .env file. Passe
d

Check 30/67: Your application only uses iterable types in foreach loops. Passed

Check 31/67: Your application does not contain invalid function calls. Passed

Check 32/67: Your application does not contain invalid imports. Passed

Check 33/67: Your application does not contain invalid method calls. Passed

Check 34/67: Your application does not contain invalid method overrides. Passed

Check 35/67: Your application does not use invalid offsets. Passed

Check 36/67: Your application does not access class properties in an invalid manner. Passed 

Check 37/67: Your application does not use invalid return types. Passed

Check 38/67: Your application is not currently in maintenance mode. Passed

Check 39/67: Your application does not refer to model relations that do not exist. Passed   

Check 40/67: Your application does not contain missing return statements. Passed

Check 41/67: An appropriate timeout and retry after is set for queues. Passed

Check 42/67: There are no syntax errors in your application code. Passed

Check 43/67: Your application does not rely on undefined constants. Passed

Check 44/67: Your application does not reference undefined variables. Passed

Check 45/67: Your application does not try to unset undefined variables. Passed

Check 46/67: Migrations are up-to date. Passed

|------------------------------------------
| Running Security Checks
|------------------------------------------

Check 47/67: Your application hides technical errors in production. Passed

Check 48/67: Sensitive environment variables are hidden in non-local environments. Passed   

Check 49/67: Application key is set. Passed

Check 50/67: Your application includes middleware to protect against CSRF attacks. Passed   

Check 51/67: Your application encrypts its cookies. Passed

Check 52/67: Your .env is not publicly accessible. Passed

Check 53/67: Your project files and directories use safe permissions. Failed
Your application's project directory permissions are not setup in a secure manner. This may 
expose your application to be compromised if another account on the same server is vulnerabl
e. This can be even more dangerous if you used shared hosting. All project directories in La
ravel should be setup with a max of 775 permissions and most app files should be provided 66
4 (except executables such as Artisan or your deployment scripts which should be provided 77
5 permissions). These are the max level of permissions in order to be secure. Your unsafe fi
les or directories include: [], [\app], [\resources], [\storage], [\public], [\config], [\da
tabase], [\routes], [\bootstrap], [\bootstrap\cache], [\bootstrap\app.php] and [\public\inde
x.php].
Documentation URL: https://www.laravel-enlightn.com/docs/security/file-permissions-analyzer.
html

Check 54/67: Your application does not expose foreign keys for mass assignment. Passed      

Check 55/67: Your application does not rely on frontend dependencies with known security iss
ues. Not Applicable

Check 56/67: Your application includes the HSTS header if it is a HTTPS only app. Not Applic
able

Check 57/67: A secure hashing strength is configured. Passed

Check 58/67: Cookies are secured as HttpOnly. Passed

Check 59/67: Your application does not rely on dependencies you are not legally allowed to u
se. Passed

Check 60/67: Your application includes login throttling for protection against brute force a
ttacks. Failed
Your application is not adequately protected from brute force attacks. This can be very dang
erous and you may resolve this by adding appropriate login throttling middleware to your log
in routes. We make an educated guess that the following routes could be unprotected login ro
utes: login. You may ignore this in case the throttling is already setup at the web server (
Nginx, Apache) level instead of the Laravel application level or in case you have devised yo
ur own custom throttling mechanism and are not using the throttling middleware or RateLimite
r class that ships with the Laravel Framework.
Documentation URL: https://www.laravel-enlightn.com/docs/security/login-throttling-analyzer.
html

Check 61/67: Your application is not exposed to mass assignment vulnerabilities. Passed     

Check 62/67: Your PHP configuration is secure. Failed
Your application does not set secure php.ini configuration values. This may expose your appl
ication to security vulnerabilities. Unless there is a very specific use case for your appli
cation, it is recommended to set your php.ini configuration as follows: [allow_url_fopen: Of
f or 0], [expose_php: Off or 0] and [display_startup_errors: Off or 0].
Documentation URL: https://www.laravel-enlightn.com/docs/security/php-ini-analyzer.html     

Check 63/67: Your application uses stable versions of dependencies. Failed
Your application's dependencies are unstable versions. These may include bug fixes and/or se
curity patches. It is recommended to update to the most stable versions.
Documentation URL: https://www.laravel-enlightn.com/docs/security/stable-dependency-analyzer
.html

Check 64/67: Your application does not un-guard models. Passed

Check 65/67: Dependencies are up-to-date. Passed

Check 66/67: Your application does not rely on backend dependencies with known security issu
es. Passed

Check 67/67: Your application sets appropriate HTTP headers to protect against XSS attacks. 
Failed
Your application is not adequately protected from XSS attacks. The Content-Security-Policy i
s either not set or not set adequately for XSS. It is recommended to set a "script-src" or "
default-src" policy directive without "unsafe-eval" or "unsafe-inline".
Documentation URL: https://www.laravel-enlightn.com/docs/security/xss-analyzer.html

Report Card
===========

+----------------+-------------+-------------+-----------+-----------+
| Status         | Performance | Reliability |  Security |     Total |
+----------------+-------------+-------------+-----------+-----------+
| Passed         |   11  (61%) |   25  (89%) | 14  (67%) | 50  (75%) |
| Failed         |    3  (17%) |    3  (11%) |  5  (24%) | 11  (16%) |
| Not Applicable |    4  (22%) |    0   (0%) |  2  (10%) |  6   (9%) |
| Error          |    0   (0%) |    0   (0%) |  0   (0%) |  0   (0%) |
+----------------+-------------+-------------+-----------+-----------+
