#!/bin/bash
#aws s3 cp s3://alesharide.net/deploy-code.zip    /opt/
#unzip -o  -d  /usr/share/nginx/html/web-backend   /opt/deploy-code.zip
cd
chown ec2-user:apache  /usr/share/nginx/html/web-backend/* -R
cd   /usr/share/nginx/html/web-backend/ &&  chmod 775 bootstrap/ images/   && chmod 777 storage/ -R && chmod 775 -R  /var/www/html/web-backend/public/images 
HOME=/usr/share/nginx/html/web-backend/
LOG=$HOME/deploy.log
/bin/echo  "$(date '+%y-%m-%d'): ** After Install Hook Started ** " >> $LOG
pwd
echo '######  ####### #     # #######'
echo '#     # #     # ##    # #'
echo '#     # #     # # #   # #'
echo '#     # #     # #  #  # #####'
echo '#     # #     # #   # # #'
echo '#     # #     # #    ## #'
echo '######  ####### #     # #######'

