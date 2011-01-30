#cd ~/Desktop/CS166_DBMS/Project/phase3/project/java

#cd $(dirname $(readlink $(which start_database.sh)))

dbport=$(< dbport)
dbfolder=$(< dbfolder)
dbname=$(< dbname)

# export PGDATA="$dbfolder"

if ! (cd "$dbfolder") ; then
	mkdir "$dbfolder"
	echo "Created folder $dbfolder"
fi

initdb -D $dbfolder

#pg_ctl -o "-p $dbport" -l logfile start
(postgres -p $dbport -D $dbfolder &)

sleep 4

createdb -p $dbport $dbname

cd ../

# Create relational schema
psql -p $dbport -f tables/create_tables.sql $dbname

# Create triggers and functions
psql -p $dbport -f tables/triggers_functions.sql $dbname

# Load data into tables
psql -p $dbport -f tables/load_data.sql $dbname

# Create indexes
psql -p $dbport -f tables/indexes.sql $dbname
