CREATE DATABASE musshop DEFAULT CHARSET utf8;

use musshop;

CREATE TABLE category (
	id 	INT NOT NULL AUTO_INCREMENT,
	name 	VARCHAR(40) NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE products (
		id 			INT NOT NULL AUTO_INCREMENT,
		marking		VARCHAR(15) NOT NULL,
		name    		VARCHAR(40) NOT NULL,
		category_id	INT NOT NULL,
		amount		INT NOT NULL,
		PRIMARY KEY(id),
		UNIQUE KEY(marking),
		FOREIGN KEY (category_id) REFERENCES category(id)
);

INSERT INTO category(name) VALUES
	('Гитары'),
	('Клавишные'),
	('Ударные'),
	('Микрофоны'),
	('DJ'),
	('Свет');

INSERT INTO products(marking, name, category_id, amount) VALUES
	('ab08433', 'IBANEZ GRX40 BLACK NIGHT', 1, 4),
	('ab0c433', 'IBANEZ GART60 BLACK NIGHT', 1, 2),
	('cd0x888', 'YAMAHA Gigmaker GM2F53A', 3, 10);

GRANT SELECT, INSERT, UPDATE, DELETE
ON musshop.*
TO admin@localhost IDENTIFIED BY 'admin107';