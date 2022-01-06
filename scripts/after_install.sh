#!/bin/bash
HOME=/usr/share/nginx/html/web-backend/
LOG=$HOME/log/deploy.log

/bin/echo  "$(date '+%y-%m-%d'): ** After Install Hook Started ** " >> $LOG
/bin/echo  "$(date '+%y-%m-%d'): *** Changing Owner and Group of Application... ***" >> $LOG

#Verify the application directory has correct owner and group 
/usr/bin/sudo  /bin/chown -R ubuntu:www-data $HOME

echo -e "Done" >> $LOG

/bin/echo  "$(date '+%y-%m-%d'): ** After Install Hook Completed ** " >> $LOG