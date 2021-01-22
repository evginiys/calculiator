PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE types (id integer not null primary key,
name text not null,
isfamily integer not null  default 0);
INSERT INTO types VALUES(1,'Новостройка',1);
INSERT INTO types VALUES(2,'Вторичный рынок',1);
CREATE TABLE IF NOT EXISTS "options"
(
	firstpaymentmin integer not null,
	firstpaymentmax integer not null,
	pricemin integer not null,
	pricemax integer not null,
	termmin integer not null,
	termmax integer not null,
	percent numeric not null,
	intertype integer default 0,
	type_id integer not null
		references types,
	primary key (type_id, intertype)
);
INSERT INTO options VALUES(12,50,1000000,100000000,100,300,9,0,1);
INSERT INTO options VALUES(9,50,1000000,100000000,100,350,5,1,1);
INSERT INTO options VALUES(9,50,1000000,100000000,100,350,5,1,2);
INSERT INTO options VALUES(9,50,1000000,100000000,100,350,7,0,2);
COMMIT;
