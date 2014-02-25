#! /bin/bash
# shellscript to run in crontab
# chron entry to run every 4 minutes
# */4 * * * * sh path/to/script/wakeup.sh >> /path/for/log_chron.txt 2>&1
# change the mac address to your device
if test -f /var/www/wakeupflag
then
	date
	wakeonlan 00:00:00:00:00:00
fi
