-- If gooberton_marketing database already exists, delete
-- Create gooberton_marketing database
-- Select from gooberton_marketing database
DROP DATABASE IF EXISTS gooberton_marketing;
CREATE DATABASE gooberton_marketing;
USE gooberton_marketing;

-- Create the tables for the database
-- Create the clients table
CREATE TABLE clients (
	clientID		INT 			NOT NULL AUTO_INCREMENT,
	firstName 		VARCHAR(50) 	NOT NULL,
	lastName 		VARCHAR(50) 	NOT NULL,
	username      	VARCHAR(20)   	NOT NULL,
	password      	VARCHAR(255)  	NOT NULL,
	PRIMARY KEY 	(clientID)
);

-- Insert data into clients table
-- UserBrandon password = Tarantino1
-- fvanwest password = India1966
-- ls8646 password = Sacha_Kitty2020
INSERT INTO clients VALUES
(1001, 'Brandon', 'Darmouth', 'UserBrandon', '$2y$10$fICn02syZ4vC7El28mCQiukENhek94i13IoqmznFQmjFhOsUBeVOa'),
(1002, 'Bill', 'West', 'billwest', '$2y$10$LfTPT1k/UfoZokdF5CQftOPhL0pg2KXTajoF0fRxFe4SjhYiyNC9m'),
(1003, 'Lizzie', 'McGuire', 'lm8114', '$2y$10$YUiite1iJEdC0WvpN6yu0eg0PzL1Fy4qReetgmCLFFWnfmPDQnj.O');

-- Create the products table
CREATE TABLE products (
	productCode      VARCHAR(10)    NOT NULL   UNIQUE,
	productName      VARCHAR(255)   NOT NULL,
	listPrice        DECIMAL(10,2)  NOT NULL,
	PRIMARY KEY 	(productCode)
);

-- Insert data into products table
INSERT INTO products VALUES
('5YR', '5-year service contract', '2999.99'),
('ANNUAL', 'annual service acquisition', '699.99'),
('CRYPTODEV', 'cryptocurrency dev & launch program', '899.99'),
('DEMO2021', 'cryptocurrency revenue demo package', '499.99');

-- Create the orders table
CREATE TABLE orders (
	orderNumber			INT				NOT NULL	AUTO_INCREMENT,
	username 			VARCHAR(20) 	NOT NULL REFERENCES clients (username),
    productCode 		VARCHAR(10) 	NOT NULL REFERENCES products (productCode),
    registrationDate 	DATETIME 		NOT NULL,
    PRIMARY KEY (username, productCode)
);

-- Insert data into orders table
INSERT INTO orders VALUES
(1, 'billwest', 'CRYPTODEV', '2021-04-24'),
(2, 'billwest', '5YR', '2021-04-24'),
(3, 'UserBrandon', 'DEMO2021', '2021-04-26');

-- Create a user named mgr_brandino
GRANT SELECT, INSERT, UPDATE, DELETE
ON *
TO mgr_brandino@localhost
IDENTIFIED BY 'aw3s0me';

-- Create a user named patron with select access
GRANT SELECT
ON products
TO patron
IDENTIFIED BY 'pa55word';