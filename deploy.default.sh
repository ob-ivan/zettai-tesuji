#!/usr/bin/env /bin/bash

### Скопируйте этот файл в deploy.sh и настройте значения переменных.

HOME=/work/chome/`whoami`
PATH=$HOME/bin:$PATH
TMPDIR=$HOME/tmp
PHP=/usr/local/php54/bin/php
COMPOSER="$PHP $HOME/bin/composer.phar --working-dir=$PWD"

LOCKDIR=$TMPDIR
LOCKFILE=deploy.lock
LOCKPATH=$LOCKDIR/$LOCKFILE

# Exit script if something goes wrong.
set -e

if [ -f $LOCKPATH ]; then
    echo 'Deployment is locked. Remove file '$LOCKPATH' to release the lock.'
else
    echo 'Deployment started at ['`date`']'
        
    (
        flock -x -n 200
        
        # Commands to run
        git pull
        $COMPOSER update
            
    ) 200>$LOCKPATH
    rm -f $LOCKPATH
    
    echo 'Deployment finished.'
fi