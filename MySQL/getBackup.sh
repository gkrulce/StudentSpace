#!/bin/bash

currDate=`date +%Y-%m-%d`
bakDir="/home/gkrulce/Dropbox/"
fileName="StudentSpace_bak_"$currDate.sql
savePath=$bakDir$fileName

mysqldump -u webapp ucsdspace > $savePath
if [ $? -eq 0 ]; then
  echo "Backup saved to $savePath"
else
  echo "Backup not saved successfully"
fi
