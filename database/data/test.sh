#!/bin/bash
#generate information for hcp account

counter=2;
for ((i = 1; i <= 50; i++))
do
	for(( j = 1 ; j < 5 && i <= 50 && counter <=50; j++))
	do
		if [ `expr $counter % 2` -eq 0 ]; then
			echo $i";"$counter";true;2011-03-01" >>  connections.txt
		else
			echo $i";"$counter";false;2011-03-01" >>  connections.txt
		fi
		((counter++))
	done
done

