#!/bin/bash
/usr/sbin/apache2ctl -D FOREGROUND && /usr/bin/php /var/www/html/src/App/Util/JobsConverter.php
