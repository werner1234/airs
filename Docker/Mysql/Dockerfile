# build environment
FROM mysql:5.7
RUN mkdir -p /var/lib/mysql/log /var/lib/mysql/data
COPY /Docker/Mysql/my.cnf /etc/mysql
RUN chmod 444 /etc/mysql/my.cnf