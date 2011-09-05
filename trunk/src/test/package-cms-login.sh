#!/bin/sh
test $# -lt 2 && printf "Usage: `basename $0` <baseurl> <logfile> <username> <password> <cookiefile> <newcookiesfiles>\n\n" && exit 65

BASEDIR=$(dirname $0)
BASEURL=$1
LOGFILE=$2
USERNAME=$3
PASSWORD=$4
COOKIE=$5
SESSION_ARGS="--cookie $COOKIE --cookie-jar $COOKIE -s --silent"

 # exit if --nologin is passed to 'sutra test'
test $# -le 3 && echo "[|skip|]" > $LOGFILE && exit 1

curl $SESSION_ARGS "$BASEURL/admin" 1> $LOGFILE 2> /dev/null
curl -F "username=$USERNAME" -F "password=$PASSWORD" --request POST  $SESSION_ARGS "$BASEURL/admin" 1>> $LOGFILE 2> /dev/null
curl $SESSION_ARGS "$BASEURL/home" 1>> $LOGFILE 2> /dev/null

# now lets check if we 'see' a logout icon (it means we are logged in)
grep "lib/cms/admin/tpl/gfx/icon.logout.gif" $LOGFILE 1> /dev/null 2> /dev/null
# write string of death in logfile (triggers error)
if [[ $? != 0 ]] ; then echo "[|error|] something is wrong" >> $LOGFILE ; fi 

