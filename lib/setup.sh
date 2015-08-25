#! /bin/bash

# Download HTMLPurifier
purifierVersion="htmlpurifier-4.7.0"
echo "Deleting old files, downloading new files, and installing purifier"
rm -rf htmlpurifier
wget "http://htmlpurifier.org/releases/$purifierVersion.tar.gz"
tar -xf "$purifierVersion.tar.gz"
mv $purifierVersion htmlpurifier
rm "$purifierVersion.tar.gz"
chmod -R 777 htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer

# Download PHPMailer
echo "Deleting old files, downloading PHP Mailer"
rm -rf PHPMailer
wget https://github.com/PHPMailer/PHPMailer/archive/master.zip
unzip master.zip
mv PHPMailer-master PHPMailer
rm master.zip
