#!/bin/sh

BASEDIR=$(dirname $(dirname $(readlink -f $0)))
THEDATE=`date '+%Y%m%d-%H%M%S'`
THEFILE=$BASEDIR'/var/backup/database/'$THEDATE'-sma.sql'

mysqldump --databases pdhg_admin pdhg_common > $THEFILE
gzip $THEFILE
