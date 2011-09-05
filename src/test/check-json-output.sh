#!/bin/sh
test $# -lt 2 && printf "Usage: `basename $0` <baseurl> <logfile> [username] [password] [cookiefile] [newcookiesfiles]\n\n" && exit 65

BASEDIR=$(dirname $0)
BASEURL=$1
LOGFILE=$2
USERNAME=$3
PASSWORD=$4
COOKIE=$5

#curl -G --data "param=bla&foo=bar" "$BASEURL/yourjsonurl" -silent >> $LOGFILE
## now lets check if we '{' and a ':' chars to verify we have json output
#grep "{" $LOGFILE 1> /dev/null 2> /dev/null
## write string of death in logfile (triggers error)
#if [[ $? != 0 ]] ; then echo "[|error|] something is wrong" >> $LOGFILE ; fi
#grep ":" $LOGFILE 1> /dev/null 2> /dev/null
## write string of death in logfile (triggers error)
#if [[ $? != 0 ]] ; then echo "[|error|] something is wrong" >> $LOGFILE ; fi
