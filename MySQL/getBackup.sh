#!/bin/bash

currDate=`date +%Y-%m-%d`
bakDir="/home/ubuntu/MySQL_backups/"
fileName="StudentSpace_bak_"$currDate.sql
savePath=$bakDir$fileName

mysqldump -u ggr ucsdspace > $savePath
if [ $? -eq 0 ]; then
  echo "Backup saved to $savePath"
else
  echo "Backup not saved successfully"
fi
