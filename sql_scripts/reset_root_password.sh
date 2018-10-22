#!/bin/bash
sudo mkdir /var/run/mysqld; sudo chown mysql /var/run/mysqld
sudo mysqld_safe --skip-grant-tables&
sudo mysql --user=root mysql

#update user set authentication_string=PASSWORD('new-password') where user='root'; flush privileges;
