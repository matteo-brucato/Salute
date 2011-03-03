#!/bin/bash
#generate information for hcp account

bill_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		if [ `expr $bill_id % 2` -eq 0 ]; then	
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;true;true;true;2011-02-22 12:00:00" >>  a.txt
		else
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;false;true;true;2011-02-22 12:00:00" >>  a.txt
		fi
		((bill_id++))
	done
done

echo "\." >> a.txt

