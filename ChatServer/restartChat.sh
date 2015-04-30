#! /bin/bash

ps -ef | grep 'node .' | awk '{print $2}' | xargs kill
nohup node . &
