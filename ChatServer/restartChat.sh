#! /bin/bash

rm -f nohup.out
ps -ef | grep 'node .' | awk '{print $2}' | xargs kill
nohup sudo node . &
