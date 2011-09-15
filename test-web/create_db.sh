#!/bin/sh

source ./config

mysql -u root -p <<EOF
create database ${TESTDB};
grant all on ${TESTDB}.* to '${USER}';
flush privileges;
EOF

# TODO test schemas
mysql -u ${USER} ${TESTDB} < ${BASE}/library.sql
