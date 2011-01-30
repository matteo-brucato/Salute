port=$(< dbport)
folder=$(< dbfolder)
dbname=$(< dbname)

# Drop relational schema
psql -p $port -f ../tables/load_data.sql $dbname
