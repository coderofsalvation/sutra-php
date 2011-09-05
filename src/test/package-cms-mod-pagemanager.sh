#!/bin/sh
test $# -lt 2 && printf "Usage: `basename $0` <baseurl> <logfile> <username> <password> <cookiefile> <newcookiesfiles>\n\n" && exit 65

BASEDIR=$(dirname $0)
BASEURL=$1
LOGFILE=$2
USERNAME=$3
PASSWORD=$4
COOKIE=$5
SESSION_ARGS="--cookie $COOKIE --cookie-jar $COOKIE -s --silent"

# include login to cms
sh $BASEDIR/package-cms-login.sh $1 $2 $3 $4 $5 
# get webpage backend
curl $SESSION_ARGS "$BASEURL/pagemanager/backend" 1>> $LOGFILE 2> /dev/null
## now lets check if we 'see' a form (it means we are logged in)
grep "name=\"path\"" $LOGFILE 1> /dev/null 2> /dev/null
# write string of death in logfile (triggers error)
if [[ $? != 0 ]] ; then echo "[|error|] something is wrong" >> $LOGFILE ; fi 

