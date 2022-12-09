#!/bin/bash

# show help text
function showHelp()
{
    echo  "Usage:";
    echo " ${txtbld}./update.sh${txtrst}    - update without '${txtyel}composer install${txtrst}' and '${txtyel}composer update${txtrst}'";
    echo " ${txtbld}./update.sh -c${txtrst} - update with '${txtyel}composer install${txtrst}' and '${txtyel}composer update${txtrst}'";
    echo ;
}

# set terminal variable
export TERM=xterm;

txtred=$(tput setaf 1)    # Red
txtgrn=$(tput setaf 2)    # Green
txtyel=$(tput setaf 3)    # Yellow
txtbld=$(tput bold)       # Bold
txtrst=$(tput sgr0)       # Text reset


COMPOSER=false; # not using composer by default

optname='?';

while getopts "ch" optname
  do
    case "$optname" in
      "c")
      # run with composer
        COMPOSER=true
        ;;
      "?")
      # wrong option
        showHelp
        exit 127;
        ;;
      "h")
      # show help
        showHelp
        exit 0;
        ;;
      *)
      # Should not occur
        echo "Unknown error while processing options";
        showHelp
        exit 127;
        ;;
    esac
  done

if $COMPOSER ; then

    printf "%-120s %s\n" "Use composer: [${txtyel}Yes${txtrst}]"

    # install composer
    if [ ! -f ./composer.phar ] ; then
        curl -sS https://getcomposer.org/installer | php 2>&1
        if [ ! -f ./composer.phar ] ; then
        echo "${txtred}Install composer Error${txtrst}"
    exit 1;
        fi
        printf "%-120s %s\n" "Install composer [${txtgrn}Ok${txtrst}]"
    fi

    # run install
    ./composer.phar install
    if [ $? -ne 0 ] ; then
        echo "${txtred}Update: Error${txtrst}"
        exit 2;
    fi
    printf "%-120s %s\n" "Update: [${txtgrn}Ok${txtrst}]";
else
    printf "%-120s %s\n" "Use composer: [${txtyel}No${txtrst}]"
fi

# set permissions for cache and logs, and group permissions for all files
rm -rf var/cache/*
rm -rf var/logs/*

APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd' | grep -v root | head -1 | cut -d\  -f1`

if [ `command -v setfacl 2>&1` ] ; then
    setfacl -R -m u:$APACHEUSER:rwX -m u:`whoami`:rwX var
    setfacl -dR -m u:$APACHEUSER:rwX -m u:`whoami`:rwX var
    if [ $(getent group phpteam ) ]; then
      setfacl -R -m g:phpteam:rw ./;
      printf "%-120s %s\n" "Setted permissions by setfacl:"
      getfacl ./ | grep phpteam | grep -v "#"
    fi
    printf "%-120s %s\n" "Set permissions by setfacl [${txtgrn}Ok${txtrst}]"
else
    chmod +a "$APACHEUSER allow delete,write,append,file_inherit,directory_inherit" var
    chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" var
    printf "%-120s %s\n" "Set permissions by chmod [${txtgrn}Ok${txtrst}]"
fi
printf "%-120s %s\n" "Set permissions to cache and logs for $APACHEUSER: [${txtgrn}Ok${txtrst}]"

exit 0;
