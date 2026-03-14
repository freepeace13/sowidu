FROM mysql:5.7

ARG MYSQL_PORT=3306
ENV MYSQL_PORT=${MYSQL_PORT}

COPY my.cnf /etc/mysql/conf.d/my.cnf

ENTRYPOINT mysqld --port=$MYSQL_PORT --user=root