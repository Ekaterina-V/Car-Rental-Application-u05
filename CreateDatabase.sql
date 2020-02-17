DROP DATABASE IF EXISTS car_rental_db;
CREATE DATABASE car_rental_db;
USE car_rental_db;

CREATE TABLE Customers (customerPersonalNumber BIGINT PRIMARY KEY,
                        customerName VARCHAR(255),
                        customerAddress VARCHAR(255),
                        customerPostalAddress VARCHAR(255),
                        customerPhoneNumber INTEGER ZEROFILL);

CREATE TABLE Cars (carRegistrationNumber VARCHAR(255) PRIMARY KEY,
                   carModel VARCHAR(255) NOT NULL,
                   carColor VARCHAR(255),
                   carYear YEAR,
                   carPrice DECIMAL,
                   checkedOutBy BIGINT,
                   checkedOutTime DATETIME,
                   FOREIGN KEY (checkedOutBy) REFERENCES Customers(customerPersonalNumber));

CREATE TABLE History (carRegistrationNumber VARCHAR(255) NOT NULL,
                      customerPersonalNumber BIGINT NOT NULL ,
                      checkedOutTime DATETIME,
                      checkedInTime DATETIME,
                      days INTEGER NOT NULL,
                      cost REAL,
                      FOREIGN KEY (customerPersonalNumber) REFERENCES Customers(customerPersonalNumber),
                      FOREIGN KEY (carRegistrationNumber) REFERENCES Cars(carRegistrationNumber));

CREATE TABLE CarModels (carModel VARCHAR(100));
CREATE TABLE CarColors (carColor VARCHAR(100));

INSERT INTO CarColors(CarColor)
VALUES  ("Red"),
        ("Yellow"),
        ("Blue"),
        ("White"),
        ("Black"),
        ("Green"),
        ("Purple"),
        ("Pink"),
        ("Dark Blue"),
        ("Orange"),
        ("Grey"),
        ("Brown"),
        ("Magenta");

INSERT INTO CarModels (CarModel)
VALUES ("Volkswagen"),
       ("Mercedes-Benz"),
       ("Mitsubishi"),
       ("Honda"),
       ("Suzuki"),
       ("Renault"),
       ("Fiat"),
       ("Toyota"),
       ("Hyundai"),
       ("Peugeot"),
       ("Chrystler"),
       ("Skoda"),
       ("Volvo"),
       ("Subaru");

INSERT INTO Customers(customerPersonalNumber,
                      customerName,
                      customerAddress,
                      customerPostalAddress,
                      customerPhoneNumber)
VALUES (9309230465, 'Adam Bertilsson', 'Huvudsta gatan 5', '16900 Solna', 0723452134),
       (9209258087, 'Monika Andersson', 'Storgatan 1', '13200 Stockholm', 0723422180),
       (6302254344, 'Ceasar Davidsson', 'Fleminggatan 11', '13200 Stockholm', 0738123456),
       (6107280833, 'David Eriksson', 'Falugatan 7', '11332 Stockholm', 0732954840),
       (1805148796, 'Erik Filipsson', 'Fleminggatan 8', '13200 Stockholm', 0730219835),
       (5506052074, 'Filip Gustavsson', 'Falugatan 3', '11332 Stockholm', 0730218420),
       (8703089543, 'August Backman', 'Storgatan 22', '13200 Stockholm', 0730283835),
       (2001128087, 'Selma Lagersson', 'Ballonggatan 18', '16930 Solna', 0730283831),
       (8807280030, 'Johan Strindberg', 'Ballonggata 12', '16932 Solna', 0730928135),
       (7103130436, 'Maria Derberg', 'Storgatan 10', '13200 Stockholm', 0730283882),
       (3305235255, 'Ulf Nilson', 'Storgatan 15', '13200 Stockholm', 0730220881);

INSERT INTO Cars (carRegistrationNumber,
                  carModel,
                  carColor,
                  carYear,
                  carPrice,
                  checkedOutBy,
                  checkedOutTime)
VALUES ('ABC234', 'Chrysler', 'Orange', 2007, 100, 9309230465, '2019-12-31 12:00:00'),
       ('CDE987', 'Volkswagen', 'Grey', 2011, 200, 9209258087, '2019-12-30 09:00:00'),
       ('HJK678', 'Mercedes-Benz', 'Brown', 2013, 450, 5506052074, '2019-12-28 12:00:00'),
       ('BKR205', 'Honda', 'White', 2015, 200, 1805148796, '2019-12-29 09:00:00'),
       ('LKQ98A', 'Mitsubishi', 'Red', 2008, 150, 6107280833, '2020-01-03 08:53:00'),
       ('KOD381', 'Skoda', 'White', 2018, 300, 6302254344, '2020-01-03 09:14:00');