#!/bin/sh
test $# -lt 2 && printf "Usage: `basename $0` <baseurl> <logfile> [username] [password] [cookiefile] [newcookiesfiles]\n\n" && exit 65

BASEDIR=$(dirname $0)
BASEURL=$1
LOGFILE=$2
USERNAME=$3
PASSWORD=$4
COOKIE=$5

curl "$BASEURL/home" --request GET -silent >> $LOGFILE
