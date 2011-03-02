#!/bin/bash
#generate information for all tables

#==============================================================================
#accounts.txt

#generates 50 patient accounts, w/ account ids 1-50
#generates 50 hcp     accounts, w/ account ids 51-100

for ((i = 1; i <= 50; i++))
do
	echo $i";patient"$i"@email.com;pat"$i  >> accounts.txt

done

for ((i = 51; i <= 100; i++))
do
	echo $i";doctor"$i"@email.com;doc"$i  >> accounts.txt
done

echo "\." >> accounts.txt

#==============================================================================
#hcp_account.txt

#fill in hcp information

for ((i = 51; i <= 100; i++))
do
	if [ `expr $i % 2` -eq 0 ]; then	
		echo $i";doc_"$i"_FN;doc_"$i"_LN;doc_"$i"_MN;"$i"00000000;1998-12-1;m;"$i"0000000000;0000000000"$i";Speciality_"$i";clinic_"$i";address_"$i";image_"$i  >> hcp_account.txt
	else
		echo $i";doc_"$i"_FN;doc_"$i"_LN;doc_"$i"_MN;"$i"00000000;1998-12-1;f;"$i"0000000000;0000000000"$i";Speciality_"$i";clinic_"$i";address_"$i";image_"$i  >> hcp_account.txt
	fi
		
done

echo "\." >> hcp_account.txt

#==============================================================================
#patient_account.txt

#fill in patient information

for ((i = 1; i <= 50; i++))
do
	if [ `expr $i % 2` -eq 0 ]; then	
		echo $i";patient_"$i"_FN;patient_"$i"_LN;patient_"$i"_MN;"$i"00000000;1998-12-1;m;"$i"0000000000;0000000000"$i";address_"$i";image_"$i  >> patient_account.txt
	else
		echo $i";patient_"$i"_FN;patient_"$i"_LN;patient_"$i"_MN;"$i"00000000;1998-12-1;f;"$i"0000000000;0000000000"$i";address_"$i";image_"$i  >> patient_account.txt
	fi
		
done

echo "\." >> patient_account.txt

#==============================================================================
#connections.txt

#patient->patient is already in file
#each patient connects to 3 other patients
#no overlap
#example
#	pat_id	pat_id		
#	1		2			
#	1		3			
#	1		4			
#	2		5			
#	2		6			
#	2		7
#ends at 17:50

#patient->hcp
#creates a connection between 1 patient and 5 hcp
#shift of 1, which means overlap of 4
#example
#	pat_id	hcp_id		pat_id	hcp_id		
#	1		51			2		52
#	1		52			2		53
#	1		53			2		54
#	1		54			2		55
#	1		55			2		56

#ends at 50;100

for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 55` && j <= 100; j++))
	do

		echo $i";"$j";true;2011-03-01" >>  connections.txt
	done

done

#hcp->hcp
#creates 2 connections for every hcp
#creates connection between current hcp and hcp 7 ids away
#example
#	hcp_id_1	hcp_id_2		
#		51			58			
#		51			59			
#		52			59			
#		52			60

#ends at 93;100			
for ((i = 51; i <= 100; i++))
do
	for(( j = `expr $i + 7`; j < `expr $i + 9` && j <= 100; j++))
	do

		echo $i";"$j";true;2011-03-01" >>  connections.txt
	done
done

echo "\." >> connections.txt

echo "\." >> connections.txt

#==============================================================================
#appointments.txt

#past appointments
#creates 4 appointments for each patient
#appointments are made with the first 4 doctors patient is connected with
appointment_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-02-22 12:00:00;true" >>  appointments.txt
		((appointment_id++))
	done

done


#upcomming appointments
#creates 4 appointments for each patient
#appointments are made with the first 4 doctors patient is connected with
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		if [ `expr $appointment_id % 2` -eq 0 ]; then	
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-04-02 09:30:00;true" >>  appointments.txt
		else
			echo $appointment_id";"$i";"$j";Checkup_"$appointment_id";2011-04-02 09:30:00;false" >>  appointments.txt
		fi
		((appointment_id++))
	done

done

echo "\." >> appointments.txt

#==============================================================================
#medical_records.txt

#creates 4 medical records for each patient
#they correspond to that patients past 4 appointments
#they are created by that corresponding hcp

medical_rec_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do	
		echo $i";"$j";Checkup_"$medical_rec_id";;file_name_"$medical_rec_id".pdf;gave prescription "$medical_rec_id >>  medical_records.txt
		((medical_rec_id++))
	done
done

echo "\." >> medical_records.txt

#==============================================================================
#payment.txt

#creates 4 bills for each patient
#they correspond to that patients past 4 appointments
#they correspond to the first 4 hcps a patients is connected with
bill_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		if [ `expr $bill_id % 2` -eq 0 ]; then	
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;true;false;false;2011-02-22 12:00:00" >>  payment.txt
		else
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;false;true;true;2011-02-22 12:00:00" >>  payment.txt
		fi
		((bill_id++))
	done
done

echo "\." >> payment.txt

#==============================================================================
#refers.txt

#each patients first hcp, referes them to an hcp that is 7 slots away
#example
#	refering_hcp_id		is_refered_hcp_id	pat_id		
#		51						58			1
#		51						59			1
#		52						59			2
#		52						60			2

#ends at 93:100:43

patient_id=1
for ((i = 51; i <= 100; i++))
do
	for(( j = `expr $i + 7`; j < `expr $i + 9` && j <= 100; j++))
	do
		echo  $i";"$j";"$patient_id>>  refers.txt
	done
	((patient_id++))
done

echo "\." >> refers.txt
