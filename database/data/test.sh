#!/bin/bash
#generate information for hcp account

#past appointments
appointment_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 52` && j <= 100; j++))
	do
		if [ `expr $appointment_id % 2` -eq 0 ]; then	
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-02-02 09:30:00;true" >>  appointments.txt
		else
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-02-02 09:30:00;true" >>  appointments.txt
		fi
		((appointment_id++))
	done

done


#upcomming appointments
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		if [ `expr $appointment_id % 2` -eq 0 ]; then	
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2012-04-02 09:30:00;true" >>  appointments.txt
		else
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-04-02 09:30:00;false" >>  appointments.txt
		fi
		((appointment_id++))
	done

done

echo "\." >> appointments.txt
