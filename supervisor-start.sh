#!/bin/bash
set -e
rm -r -f /etc/supervisor/conf.d/
cp -r configuration/supervisord/ /etc/supervisor/conf.d
cd /etc/supervisor/conf.d
for f in *; do mv "$f" "$f.conf"; done
for f in *; do sed -i 's/socialhose/application/'  $f; done
for f in *; do sed -i 's/{{CWD}}/\/var\/www\/html/'  $f; done
service supervisor start
supervisorctl reread
supervisorctl update
cd /var/www/html