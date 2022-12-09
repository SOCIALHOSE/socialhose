FROM webdevops/php-apache:7.4

WORKDIR "/var/www/html"
RUN apt update -y  &  apt -y dist-upgrade
RUN apt -y -f install apt-transport-https bash vim
RUN curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | /bin/bash
RUN apt -y install default-mysql-client mc acl sudo git nodejs npm cron python-pip

RUN docker-php-ext-install bcmath

# Create user
ARG UID
ENV USER application
#RUN adduser -q --disabled-password --gecos ""  $USER  --uid $UID
RUN usermod -aG sudo $USER
RUN sed -i 's/%sudo\tALL=(ALL:ALL) ALL/%sudo ALL=(ALL:ALL) NOPASSWD:ALL/g' /etc/sudoers



# Add cron
COPY crontab /etc/cron.d/crontab
RUN sed -i 's/{{CWD}}/\/usr\/local\/bin\/php \/var\/www\/html\/bin\/console --env=stage/'  /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab
RUN touch /var/log/cron.log

# Add supervisor
RUN pip install supervisor
RUN rmdir /app && ln -s /var/www/html/web /app
USER application