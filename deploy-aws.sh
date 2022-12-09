#!/bin/bash
result=`ps aux | grep -i "deploy-aws.sh" | grep -v "grep" | wc -l`
if [ $result -ge 1 ]
   then
        echo "ABORT: Script already running."
   else
        cd /var/www/html  && shopt -s extglob && echo eivuz6Ai | sudo -S rm -r !(var|vendor)
fi
