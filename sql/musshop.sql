CREATE DATABASE musstock DEFAULT CHARSET utf8;

use musstock;

CREATE TABLE category (
   id    INT NOT NULL AUTO_INCREMENT,
   name  VARCHAR(40) NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE user (
      id             INT NOT NULL AUTO_INCREMENT,
      name           VARCHAR(70),
      email          VARCHAR(70) NOT NULL,
      login          VARCHAR(45) NOT NULL,
      password       CHAR(40)    NOT NULL,
      verification   BOOLEAN     NOT NULL,
      salt           VARCHAR(8)  NOT NULL,
      register_date  TIMESTAMP   NOT NULL,
      PRIMARY KEY(id),
      UNIQUE KEY(email)
);

CREATE TABLE goods (
      id          INT NOT NULL AUTO_INCREMENT,
      marking     VARCHAR(15) NOT NULL,
      name        VARCHAR(40) NOT NULL,
      category_id INT NOT NULL,
      amount      INT NOT NULL,
      PRIMARY KEY(id),
      UNIQUE KEY(marking),
      FOREIGN KEY(category_id) REFERENCES category(id) ON DELETE CASCADE
);

CREATE TABLE orders (
      id          INT NOT NULL AUTO_INCREMENT,
      order_id    INT NOT NULL,
      user_id     INT NOT NULL,
      order_date  TIMESTAMP,
      PRIMARY KEY(id),
      UNIQUE KEY(order_id),
      FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE subcategory (
      id        INT NOT NULL,
      parent_id INT NOT NULL,
      FOREIGN KEY(id)        REFERENCES category(id) ON DELETE CASCADE,
      FOREIGN KEY(parent_id) REFERENCES category(id) ON DELETE CASCADE
);

CREATE TABLE order_goods (
      id       INT NOT NULL AUTO_INCREMENT,
      order_id INT NOT NULL,
      good_id  INT NOT NULL,
      PRIMARY KEY(id),
      FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
      FOREIGN KEY(good_id)  REFERENCES goods(id)  ON DELETE CASCADE
);

INSERT INTO user(name, email, login, password, verification, salt) VALUES
   ('Король жизни', 'example@trs.com', 'login', 'qwerty', 0, 'aqD'),
   ('Ленка с третьего подьезда','some_name@gmail.com', 'gdg', 'pass', 1, 'aqDgg13');

INSERT INTO orders(order_id, user_id) VALUES (590, 1),(63, 2);

INSERT INTO category(name) VALUES
   ('Гитары'),
   ('Клавишные'),
   ('Ударные'),
   ('Микрофоны'),
   ('DJ'),
   ('Свет'),
   ('Ударные установки'),
   ('Акустические ударные установки'),
   ('Электронные ударные установки'),
   ('Sonor'),
   ('YAMAHA'),
   ('TAMA');

INSERT INTO subcategory(id, parent_id) VALUES
   (1, 1),
   (2, 2),
   (3, 3),
   (4, 4),
   (5, 5),
   (6, 6),
   (7, 7),
   (8, 7),
   (9, 7),
   (10, 8),
   (11, 8),
   (12, 8);

INSERT INTO goods(marking, name, category_id, amount) VALUES
   ('ab08433', 'IBANEZ GRX40 BLACK NIGHT', 1, 4),
   ('ab0c433', 'IBANEZ GART60 BLACK NIGHT', 1, 2),
   ('cd0x888', 'YAMAHA Gigmaker GM2F53A', 3, 10);

INSERT INTO order_goods(order_id, good_id) VALUES
   (2, 3),
   (2, 2),
   (1, 2);

GRANT SELECT, INSERT, UPDATE, DELETE
ON musstock.*
TO admin@localhost IDENTIFIED BY 'admin107';