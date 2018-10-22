#!/bin/bash
# 1. first, run these bash commands
sudo /etc/init.d/mysql stop # stop mysql service
sudo mysqld_safe --skip-grant-tables & # start mysql without password
# enter -> go
mysql -uroot # connect to mysql
