#!/bin/bash
install_path=$1
user=$2

if [ ! $install_path ];
then
        install_path=$(pwd)
fi

if [ ! $user ];
then
        user=$(whoami)
fi

wget http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz -O /tmp/GeoLiteCity.dat.gz
if [ -f /tmp/GeoLiteCity.dat.gz ];
then
        gunzip -c /tmp/GeoLiteCity.dat.gz > $install_path/GeoLiteCity.dat && chown $user: $install_path/GeoLiteCity.dat
fi
