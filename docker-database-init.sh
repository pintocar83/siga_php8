#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
	CREATE ROLE siga SUPERUSER LOGIN PASSWORD 'siga';
	CREATE DATABASE siga_fundacite_sucre WITH OWNER='siga' ENCODING='UTF8';
EOSQL
