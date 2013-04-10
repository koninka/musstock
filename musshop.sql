CREATE DATABASE musstock DEFAULT CHARSET utf8;

use musstock;

CREATE TABLE category (
	id 	INT NOT NULL AUTO_INCREMENT,
	name 	VARCHAR(40) NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE user (
		id 				INT NOT NULL AUTO_INCREMENT,
		name 				VARCHAR(70),
		email	 	 		VARCHAR(70) NOT NULL,
		login     		VARCHAR(45) NOT NULL,
		password	 		CHAR(40)    NOT NULL,
		verification	BOOLEAN		NOT NULL,
		salt				VARCHAR(8)	NOT NULL,
		register_date	TIMESTAMP	NOT NULL,
		PRIMARY KEY(id),
		UNIQUE KEY(email)
);

CREATE TABLE goods (
		id 			INT NOT NULL AUTO_INCREMENT,
		marking		VARCHAR(15) NOT NULL,
		name    		VARCHAR(40) NOT NULL,
		category_id	INT NOT NULL,
		amount		INT NOT NULL,
		PRIMARY KEY(id),
		UNIQUE KEY(marking),
		FOREIGN KEY(category_id) REFERENCES category(id)
);

CREATE TABLE orders (
		id 			INT NOT NULL AUTO_INCREMENT,
		user_id 		INT NOT NULL,
		order_date 	TIMESTAMP,
		PRIMARY KEY(id),
		FOREIGN KEY(user_id) REFERENCES user(id)
);

CREATE TABLE order_goods (
		id 		INT NOT NULL AUTO_INCREMENT,
		order_id INT NOT NULL,
		good_id 	INT NOT NULL,
		PRIMARY KEY(id),
		FOREIGN KEY(order_id) REFERENCES orders(id),
		FOREIGN KEY(good_id) REFERENCES goods(id)
);

INSERT INTO user(name, email, login, password, verification, salt) VALUES
	('Король жизни', 'example@trs.com', 'login', 'qwerty', 0, 'aqD'),
	('Ленка с третьего подьезда','some_name@gmail.com', 'gdg', 'pass', 1, 'aqDgg13');

INSERT INTO orders(user_id) VALUES (1),(2);

INSERT INTO category(name) VALUES
	('Гитары'),
	('Клавишные'),
	('Ударные'),
	('Микрофоны'),
	('DJ'),
	('Свет');

INSERT INTO goods(marking, name, category_id, amount) VALUES
	('ab08433', 'IBANEZ GRX40 BLACK NIGHT', 1, 4),
	('ab0c433', 'IBANEZ GART60 BLACK NIGHT', 1, 2),
	('cd0x888', 'YAMAHA Gigmaker GM2F53A', 3, 10);

INSERT INTO order_goods(order_id, good_id) VALUES
	(1, 3),
	(1, 2),
	(2, 2);

GRANT SELECT, INSERT, UPDATE, DELETE
ON musstock.*
TO admin@localhost IDENTIFIED BY 'admin107';