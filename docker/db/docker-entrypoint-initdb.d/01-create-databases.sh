#!/bin/bash

set -e

function createAndPopulateDatabase() {
  local user=$1
  local db=$2
  local dump=$3

  echo "CREATE DATABASE $db OWNER $user ENCODING 'UTF8' LC_COLLATE = 'hu_HU.UTF-8' LC_CTYPE = 'hu_HU.UTF-8' TEMPLATE = template0;" | psql -U "$POSTGRES_USER"

  echo "DROP SCHEMA public CASCADE;" | psql -U "$POSTGRES_USER" "$db"
  echo "CREATE SCHEMA public;" | psql -U "$user" "$db"

  if [[ "$dump" != "" && -f $dump ]]; then
      psql -U "$user" "$db" -c "DROP SCHEMA public CASCADE;"
      psql -U "$user" "$db" -c "CREATE SCHEMA public;"
      psql -U "$user" "$db" < "$dump"
  fi
}

function createUser() {
  local user=$1
  local pass=$2

  echo "CREATE USER $user WITH PASSWORD '$pass';" | psql -U "$POSTGRES_USER"
}

users=$(echo "$POSTGRES_USERS" | tr "," " ")
for user in $users
do
  parts=$(echo "$user" | tr ":" " ")
  createUser $parts
done

dbs=$(echo "$POSTGRES_DATABASES" | tr "," " ")
for db in $dbs
do
  parts=$(echo "$db" | tr ":" " ")
  createAndPopulateDatabase $parts
done
