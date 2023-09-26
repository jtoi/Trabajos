#!/bin/sh

cd /var/www/concentrador/
nom="concentrador-"$(date +"%d-%m-%Y")".tar.bz2";
wget -ct0 --ftp-user=concentradoramf --ftp-password='$1$QbX6VXav$QzVomUREI' ftp://administracomercios.com/julito/$nom;
tar -xjf $nom;
mysql concentramf_db -uroot -pg!3dAim@11*2 < concentramf_db.sql;
