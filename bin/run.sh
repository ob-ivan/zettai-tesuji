#!/bin/bash

# Настроечные переменные.
PHP=/usr/local/php54/bin/php

DIR="$( cd "$( dirname "$0" )" && pwd )"

$PHP $DIR/index.php $@
