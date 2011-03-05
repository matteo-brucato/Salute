#if ! (cd $(< dbfolder)) ; then
#	mkdir $(< dbfolder)
#	echo "Created folder $(< dbfolder)"
#fi

#initdb -D $(< dbfolder)

#(postgres -p $(< dbport) -D $(< dbfolder) &)

## Run Postgres server as deamon
(postgres -p $(< dbport) -D $(< dbfolder) >logfile 2>&1 </dev/null &)

while true; do
	sleep 1
	l=`cat logfile | wc -l`
	if [ $l -gt 2 ]; then
		cat logfile
		exit
	fi
done
