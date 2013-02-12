#!/usr/bin/env /bin/bash

### Скопируйте этот файл deploy.sh и настройте значения переменных.

### Укажите команду для запуска composer.
# COMPOSER=composer

git pull
$COMPOSER update
