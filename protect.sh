find . -name '*.js' -exec chmod a-w {} \;
find . -name '*.css' -exec chmod a-w {} \;
find . -name '*.pl' -exec chmod a-w {} \;
find . -name '*.cgi' -exec chmod a-w {} \;
find . -name '.htaccess' -exec chmod 0444 {} \;
find bitrix/admin -name '*.php*' ! -exec chmod a-w {} \;
find . -name 'error_log' -exec chmod a+w {} \;
find . -name '*.dat' -exec chmod a+w {} \;
find . -name '*.ini' -exec chmod a+w {} \;
find . -name '*.txt' -exec chmod a+w {} \;
find . -name '*.log' -exec chmod a+w {} \;
find . -name '*.sql' -exec chmod a+w {} \;
find . -name '*.xml' -exec chmod a+w {} \;
find . -name '*.json' -exec chmod a+w {} \;
chmod a-w `pwd`