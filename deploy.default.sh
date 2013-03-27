#!/usr/bin/env /bin/bash

### Скопируйте этот файл в deploy.sh и настройте значения переменных.

HOME=/work/chome/`whoami`
PATH=$HOME/bin:$PATH
PHP=/usr/local/php54/bin/php
COMPOSER="$PHP $HOME/bin/composer.phar --working-dir=$PWD"

DIR="$( cd "$( dirname "$0" )" && pwd )"
LOCKPATH=$DIR/deploy.lock

# Exit script if something goes wrong.
set -e

if [ -f $LOCKPATH ]; then
    echo 'Deployment is locked. Remove file '$LOCKPATH' to release the lock.'
else
    echo 'Deployment started at ['`date`']'
        
    (
        flock -x -n 200
        
        # Commands to run
        echo 'Deployment runs git-pull at ['`date`']'
        git pull
        echo 'Deployment runs composer-install at ['`date`']'
        $COMPOSER install
            
    ) 200>$LOCKPATH
    rm -f $LOCKPATH
    
    echo 'Deployment finished at ['`date`'].'
fi
