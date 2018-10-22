use mysql; -- use mysql table
update user set authentication_string=PASSWORD("password") where User='root'; -- update password to nothing
update user set plugin="mysql_native_password" where User='root'; -- set password resolving to default mechanism for root user

flush privileges;
quit;
