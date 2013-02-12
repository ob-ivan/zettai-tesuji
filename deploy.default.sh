#!/usr/bin/env /bin/bash

### Скопируйте этот файл deploy.sh и настройте значения переменных.

### Укажите команду для запуска composer.
# COMPOSER="/usr/local/php54/bin/php $HOME/bin/composer.phar --working-dir=$PWD"

git pull
$COMPOSER update
