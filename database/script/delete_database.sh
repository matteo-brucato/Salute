#cd ~/Desktop/CS166_DBMS/Project/phase3/project/java

#cd $(dirname $(readlink $(which start_database.sh)))

port=$(< dbport)
folder=$(< dbfolder)
dbname=$(< dbname)

export PGDATA="$folder"

# Drop relational schema
psql -p $port -f ../tables/drop.sql $dbname

dropdb -p $port $dbname

pg_ctl -p $port stop

rm -fr "$folder"
