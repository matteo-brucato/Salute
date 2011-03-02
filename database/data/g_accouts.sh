#!/bin/bash
#generate information for accounts table
#inserted into accounts.txt

#==============================================================================
#accounts.txt

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

for ((i = 1; i <= 50; i++))
do
	for(( j = `expr $i + 50`; j < `expr $i + 55` && j <= 100; j++))
	do

		echo $i";"$j";true;2011-03-01" >>  connections.txt
	done

done

echo "\." >> connections.txt
