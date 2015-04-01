#!/bin/bash

cat `ls *.sql` | mysql -u root -p

