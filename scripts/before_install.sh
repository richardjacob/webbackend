#!/bin/bash
HOME=/usr/share/nginx/html/web-backend/
LOGFOLDER=$HOME/log 
LOG=$LOGFOLDER/deploy.log
mkdir $LOGFOLDER
touch $LOG
/bin/echo  "$(date '+%y-%m-%d-%x'): ** Before Install Hook Started ** " >> $LOG

# Do something actions before the instrallation

/bin/echo  "$(date '+%y-%m-%d-%x'): ** Before Install Hook Completed ** " >> $LOG