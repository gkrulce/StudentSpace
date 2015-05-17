#! /bin/bash

echo "Deleting old files, downloading new files, and installing purifier"
rm -rf htmlpurifier
wget http://htmlpurifier.org/releases/htmlpurifier-4.6.0.tar.gz
tar -xf htmlpurifier-4.6.0.tar.gz
mv htmlpurifier-4.6.0 htmlpurifier
rm htmlpurifier-4.6.0.tar.gz
chmod -R 777 htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer
