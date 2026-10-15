#!/bin/sh
# WP-CLI for the screenshot demo site (http://localhost/srdemo, database srdemo_wp). See tools/demo-content.php.
DIR=$(cygpath -m "$(cd "$(dirname "$0")" && pwd)")
export MSYS_NO_PATHCONV=1
/c/xampp/php/php.exe -d display_startup_errors=0 -d log_errors=0 "$DIR/wp-cli.phar" --path=C:/xampp/htdocs/srdemo "$@" 2>&1 | grep -v 'Module "ftp" is already loaded'
