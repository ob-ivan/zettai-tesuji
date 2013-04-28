#!/bin/bash
PHP=/usr/local/php54/bin/php
DIR="$( cd "$( dirname "$0" )"; pwd )"
DOCROOT="$( dirname $DIR )"
$PHP -C $DOCROOT/vendor/bin/phpunit -c $DOCROOT/test/
