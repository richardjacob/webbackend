#!/bin/bash
file="scripts/instance.txt"
pwd
lines=`cat $file`
for a in $lines; do
        # echo "$a"
		aws ssm send-command --instance-ids "$a" --document-name "AWS-RunShellScript" --comment "Deploy Rider App script" --parameters commands="sh /home/ec2-user/appsync.sh" --output text --region ap-south-1
done
