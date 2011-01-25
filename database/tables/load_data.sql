--
--LOAD DATA
--

--Delete all tables in reverse order to clear references
DELETE FROM Permission;
DELETE FROM D_D_Connection;
DELETE FROM P_D_Connection;
DELETE FROM Payment;
DELETE FROM Medical_Record;
DELETE FROM Appointments;
DELETE FROM HCP_Account;
DELETE FROM Patient_Account;
DELETE FROM Messages;
DELETE FROM Accounts;


--Copy Account Information
COPY Accounts(
	account_id,
	email,
	password
)
FROM '/home/matteo/Desktop/salute/database/data/accounts.txt'
WITH DELIMITER ';';


--Copy Messages Information
COPY Messages(
	message_id,
	sender_id,
	receiver_id,
	subject,
	content
)
FROM '/home/matteo/Desktop/salute/database/data/messages.txt'
WITH DELIMITER ';';


--Copy Patient_Account Information
COPY Patient_Account(
	account_id,
	--patient_id,
	first_name,
	last_name,
	middle_name,
	ssn,
	dob,
	sex,
	tel_number,
	fax_number,
	address
)
FROM '/home/matteo/Desktop/salute/database/data/patient_account.txt'
WITH DELIMITER ';';


--Copy HCP_Account Information
COPY HCP_Account(
	account_id,
	--hcp_id,
	first_name,
	last_name,
	middle_name,
	ssn,
	dob,
	sex,
	tel_number,
	fax_number,
	specialization,
	org_name,
	address
)
FROM '/home/matteo/Desktop/salute/database/data/hcp_account.txt'
WITH DELIMITER ';';


--Copy Appointment Information
COPY Appointments(
	appointment_id,
	patient_id,
	hcp_id,
	descryption,
	date_time,
	approved
)
FROM '/home/matteo/Desktop/salute/database/data/appointments.txt'
WITH DELIMITER ';';

--Copy Medical_Record Information
COPY Medical_Record(
	medical_rec_id,
	patient_id,
	account_id, 
	issue,
	suplementary_info,
	file_path
)
FROM '/home/matteo/Desktop/salute/database/data/medical_records.txt'
WITH DELIMITER ';';


--Copy Payment Information
Copy Payment(
	bill_id,
	patient_id,
	hcp_id,
	amount,
	descryption,
	due_date,
	cleared
)
FROM '/home/matteo/Desktop/salute/database/data/payment.txt'
WITH DELIMITER ';';


--Copy P_D_Connection Information
COPY P_D_Connection(
	patient_id,
	hcp_id,
	accepted,
	date_connected
)
FROM '/home/matteo/Desktop/salute/database/data/p_d_connection.txt'
WITH DELIMITER ';';


--Copy D_D_Connection Information
COPY D_D_Connection(
	requester_id,
	accepter_id,
	aceepted,
	date_connected
)
FROM '/home/matteo/Desktop/salute/database/data/d_d_connection.txt'
WITH DELIMITER ';';


--Copy Permission Information
Copy Permission(
	medical_record_id,
	account_id,
	date_created
)
FROM '/home/matteo/Desktop/salute/database/data/permissions.txt'
WITH DELIMITER ';';
