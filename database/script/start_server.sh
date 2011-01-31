#if ! (cd $(< dbfolder)) ; then
#	mkdir $(< dbfolder)
#	echo "Created folder $(< dbfolder)"
#fi

#initdb -D $(< dbfolder)

(postgres -p $(< dbport) -D $(< dbfolder) &)
