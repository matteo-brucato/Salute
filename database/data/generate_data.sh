#!/bin/bash
#generate information for all tables

#==============================================================================
#accounts.txt

#generates 50 patient accounts, w/ account ids 1-50
#generates 50 hcp     accounts, w/ account ids 51-100

echo -e "GENERATING accounts.txt\n"

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
#patient_account.txt

#fill in patient information

echo -e "GENERATING patient_account.txt\n"

echo "1;Teresa;Ramirez;;567834576;1970-7-12;f;19098842354;;1023 River Drive, Riverside, CA 92521;patient1.jpeg" >> patient_account.txt
echo "2;Ben;Yong;Le;846384792;1965-2-23;m;19517938734;19517638879; 9834 West Adam St., Colton, CA 92324;patient2.jpeg" >> patient_account.txt
echo "3;Alber;Man;;530717265;2000-4-15;m;17027681264;;8345 Linden St, Reno, NV 89501;patient3.jpeg" >> patient_account.txt
echo "4;James;Foster;;173872648;2005-9-9;m;14807892364;;1292 Main St, Sprigfield, AR 85640;patient4.jpeg" >> patient_account.txt
echo "5;Ariana;Mejilla;Jena;452048120;1999-12-19;f;19515671290;;342 Washington, Colton, CA 92324;patient5.jpeg" >> patient_account.txt
echo "6;Ben;Wheatherspon;;820011293;1990-3-23;m;19512392845;;9343 Blaine St, Colton, CA 92324;patient6.jpeg" >> patient_account.txt
echo "7;Rony;Doodle;;912582348;1985-11-8;m;17028901234;;2342 Lendis St, Reno, NV 89501;patient7.jpeg" >> patient_account.txt
echo "8;Mike;James;;923011209;1992-1-13;m;14808901202;;92834 Idontknow st, Springfield, AR 85640;patient8.jpeg" >> patient_account.txt
echo "9;Larry;Martinez;;203038287;1970-7-22;m;17022038454;;2389 Soto Ave, Reno, NV 89501;patient9.jpeg" >> patient_account.txt

for ((i = 10; i <= 50; i++))
do
	if [ `expr $i % 2` -eq 0 ]; then	
		echo $i";patient_"$i"_FN;patient_"$i"_LN;patient_"$i"_MN;"$i"000000;1998-12-1;m;"$i"00000000;00000000"$i";address_"$i";image_"$i".jpeg"  >> patient_account.txt
	else
		echo $i";patient_"$i"_FN;patient_"$i"_LN;patient_"$i"_MN;"$i"000000;1998-12-1;f;"$i"00000000;00000000"$i";address_"$i";image_"$i".jpeg"  >> patient_account.txt
	fi
		
done

echo "\." >> patient_account.txt


#==============================================================================
#hcp_account.txt

#fill in hcp information

echo -e "GENERATING hcp_account.txt\n"

echo "51;Musa;Rayyan;;635492165;1988-12-1;m;19096936527;19093428463;Cardioligist;Rayyan Medical Center;15632 Frederic Ave, Riverside, CA 92521;doctor1.jpeg" >> hcp_account.txt
echo "52;Matteo;Brucato;;234567891;1985-5-12;m;19517253847;19519776537;Brain Surgeon;Matteo's Family Clinic;1298 Orange St, Colton, CA 92324;doctor2.jpeg" >> hcp_account.txt
echo "53;Nada;Hashem;;567390804;1989-10-19;f;17028335678;17022349876;Cardio-Thoracic Surgeon;Hashem's Medical Center; 9345 Hashem St, Reno, NV 89501;doctor3.jpeg" >> hcp_account.txt
echo "54;Aswhin;Gopalakrishnan;;876976546;1965-9-24;m;14803491203;14808451298;General Practitioner;Ashwin Medical Center; 2342 Saint St, Springfield, AR 85640;doctor4.jpeg" >> hcp_account.txt
echo "55;Connie;Chang;;12313434;1965-9-24;f;34534546;14808451298;Pharmacy Practitioner;Kaiser; 2342 Saint St, Springfield, AR 85640;doctor4.jpeg" >> hcp_account.txt

for ((i = 56; i <= 100; i++))
do
	if [ `expr $i % 2` -eq 0 ]; then	
		echo $i";doc_"$i"_FN;doc_"$i"_LN;doc_"$i"_MN;"$i"000000;1998-12-1;m;"$i"00000000;00000000"$i";Speciality_"$i";clinic_"$i";address_"$i";image_"$i".jpeg"  >> hcp_account.txt
	else
		echo $i";doc_"$i"_FN;doc_"$i"_LN;doc_"$i"_MN;"$i"000000;1998-12-1;f;"$i"00000000;00000000"$i";Speciality_"$i";clinic_"$i";address_"$i";image_"$i".jpeg"  >> hcp_account.txt
	fi
		
done

echo "\." >> hcp_account.txt


#==============================================================================
#connections.txt

echo -e "GENERATING connections.txt\n"

#patient->patient
#each patient connects to 4 other patients
#2 are accepted, 2 are pending
#no overlap
#example
#	pat_id	pat_id		
#	1		2			
#	1		3			
#	1		4
#   1		5		
#	2		6		
#	2		5		
#	2		8
#	2		9
#ends at 13:50
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

#==============================================================================
#appointments.txt

echo -e "GENERATING appointments.txt\n"

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

echo -e "GENERATING medical_records.txt\n"

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

echo -e "GENERATING payment.txt\n"

#creates 4 bills for each patient
#they correspond to that patients past 4 appointments
#they correspond to the first 4 hcps a patients is connected with
bill_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do
		if [ `expr $bill_id % 2` -eq 0 ]; then	
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;true;true;true;2011-02-22 12:00:00" >>  payment.txt
		else
			echo $bill_id";"$i";"$j";1234.56;Checkup_"$bill_id";2011-03-22 12:00:00;false;true;true;2011-02-22 12:00:00" >>  payment.txt
		fi
		((bill_id++))
	done
done

echo "\." >> payment.txt

#==============================================================================
#permissions.txt

echo -e "GENERATING permissions.txt\n"

#gives permission to all hcps that uploaded a medical record
permission_id=1
for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 54` && j <= 100; j++))
	do	
		echo $permission_id";"$i";"$j";2011-02-22 12:00:00" >>  permissions.txt
		((permission_id++))
	done
done

echo "\." >> permissions.txt

#==============================================================================
#refers.txt

echo -e "GENERATING refers.txt\n"

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
