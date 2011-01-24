--
--DROP TABLES
--	
DROP TABLE Accounts CASCADE;
DROP TABLE Messages CASCADE;
DROP TABLE Patient_Account CASCADE;
DROP TABLE HCP_Account CASCADE;
DROP TABLE Appointments CASCADE;
DROP TABLE Medical_Record CASCADE;
DROP TABLE Payment CASCADE;
DROP TABLE P_D_Connection CASCADE;
DROP TABLE D_D_Connection CASCADE;
DROP TABLE Permission CASCADE;


--
--CREATE TABLES
--

--Accounts Table
CREATE TABLE Accounts(
	account_id SERIAL NOT NULL,
	email VARCHAR(20) NOT NULL,
	password VARCHAR(15) NOT NULL,
	UNIQUE(email),
	PRIMARY KEY(account_id)
);


--Messages Table
CREATE TABLE Messages(
	message_id SERIAL NOT NULL,
	sender_id SERIAL NOT NULL,
	receiver_id SERIAL NOT NULL,
	subject TEXT NOT NULL,
	content TEXT NOT NULL,
	PRIMARY KEY(message_id),
	FOREIGN KEY (sender_id) REFERENCES Accounts(account_id),
	FOREIGN KEY (receiver_id) REFERENCES Accounts(account_id)
);


--Patient Table
CREATE TABLE Patient_Account(
	account_id SERIAL NOT NULL,
	--patient_id SERIAL NOT NULL,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	middle_name VARCHAR(30),
	ssn NUMERIC(9,0) NOT NULL CHECK (ssn > 99999999),
	dob DATE NOT NULL,
	sex CHAR(6) NOT NULL,
	tel_number VARCHAR(11),
	fax_number VARCHAR(11),
	address TEXT,
	PRIMARY KEY(account_id),
	FOREIGN KEY (account_id) REFERENCES Accounts(account_id)
);



--HCP_Account Table
CREATE TABLE HCP_Account(
	account_id SERIAL NOT NULL,
	--hcp_id SERIAL NOT NULL,
	first_name VARCHAR(30) NOT NULL,
	last_name VARCHAR(30) NOT NULL,
	middle_name VARCHAR(30),
	ssn NUMERIC(9,0) NOT NULL CHECK (ssn > 99999999),
	dob DATE NOT NULL,
	sex CHAR(6) NOT NULL,
	tel_number VARCHAR(11),
	fax_number VARCHAR(11),
	org_name VARCHAR(30),
	address TEXT,
	PRIMARY KEY(account_id),
	FOREIGN KEY (account_id) REFERENCES Accounts(account_id)
);


--Apointments Table
CREATE TABLE Appointments(
	appointment_id SERIAL NOT NULL,
	patient_id SERIAL NOT NULL,
	hcp_id SERIAL NOT NULL,
	descryption TEXT NOT NULL,
	date_time TIMESTAMP(0) WITH TIME ZONE NOT NULL,
	approved BOOLEAN NOT NULL DEFAULT FALSE,
	PRIMARY KEY(appointment_id),
	FOREIGN KEY (patient_id) REFERENCES Patient_Account(account_id),
	FOREIGN KEY (hcp_id) REFERENCES HCP_Account(account_id)
);


--Medical_Records Table
CREATE TABLE Medical_Record(
	medical_rec_id SERIAL NOT NULL,
	patient_id SERIAL NOT NULL,
	account_id SERIAL NOT NULL, 
	issue TEXT NOT NULL,
	suplementary_info TEXT,
	file_path TEXT NOT NULL,
	PRIMARY KEY (medical_rec_id),
	FOREIGN KEY (patient_id) REFERENCES Patient_Account(account_id),
	FOREIGN KEY (account_id) REFERENCES Accounts(account_id)	
);


--Payment Table
CREATE TABLE Payment(
	bill_id SERIAL NOT NULL,
	patient_id SERIAL NOT NULL,
	hcp_id SERIAL NOT NULL,
	amount DECIMAL(9,2) NOT NULL,
	descryption TEXT NOT NULL,
	due_date TIMESTAMP(0) WITH TIME ZONE NOT NULL,
	cleared BOOLEAN NOT NULL DEFAULT FALSE,
	PRIMARY KEY(bill_id),
	FOREIGN KEY (patient_id) REFERENCES Patient_Account(account_id),
	FOREIGN KEY (hcp_id) REFERENCES HCP_Account(account_id)

);


--
--RELATIONSHIP TABLES
--

--Patient to Doctor Table
CREATE TABLE P_D_Connection(
	patient_id SERIAL NOT NULL,
	hcp_id SERIAL NOT NULL,
	accepted BOOLEAN NOT NULL DEFAULT FALSE,
	date_connected DATE,
	PRIMARY KEY(patient_id, hcp_id),
	FOREIGN KEY (patient_id) REFERENCES Patient_Account(account_id),
	FOREIGN KEY (hcp_id) REFERENCES HCP_Account(account_id)
);


--Doctor to Doctor Table
CREATE TABLE D_D_Connection(
	requester_id serial NOT NULL,
	accepter_id serial NOT NULL,
	aceepted BOOLEAN NOT NULL DEFAULT FALSE,
	date_connected DATE,
	PRIMARY KEY (requester_id, accepter_id),
	FOREIGN KEY (requester_id) REFERENCES HCP_Account(account_id),
	FOREIGN KEY (accepter_id) REFERENCES HCP_Account(account_id)
);


--Permissions Medical Recorda Table
CREATE TABLE Permission(
	medical_record_id SERIAL NOT NULL,
	account_id SERIAL NOT NULL,
	date_created DATE NOT NULL,
	PRIMARY KEY (medical_record_id, account_id),
	FOREIGN KEY (medical_record_id) REFERENCES Medical_Record(medical_rec_id),
	FOREIGN KEY (account_id) REFERENCES Accounts(account_id)

);
