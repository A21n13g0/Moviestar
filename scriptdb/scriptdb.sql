CREATE DATABASE MOVIESTAR;

CREATE TABLE USERS(
	ID_USER INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(100) NOT NULL,
    LAST_NAME VARCHAR(100) NOT NULL,
    EMAIL VARCHAR(200) NOT NULL UNIQUE,
    PASSWORD VARCHAR (200) NOT NULL,
    IMAGE_USER VARCHAR(200),
    TOKEN VARCHAR(200),
    BIOGRAPHY TEXT
);

CREATE TABLE MOVIES(
	ID_MOVIE INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    TITLE VARCHAR(100) NOT NULL,
    DESCRIPTION TEXT NOT NULL,
    IMAGE_MOVIE VARCHAR(200) NOT NULL,
    TRAILER VARCHAR(150),
    CATEGORY VARCHAR(50) NOT NULL,
    LENGTH VARCHAR(30) NOT NULL,
    FK_USER_ID INT(11) UNSIGNED,
    FOREIGN KEY(FK_USER_ID) REFERENCES USERS(ID_USER)
);

CREATE TABLE REVIEWS(
	ID_REVIEW INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    RATING INT,
    REVIEW TEXT NOT NULL,
    FK_USER_ID INT(11) UNSIGNED,
    FK_MOVIES_ID INT(11) UNSIGNED,
    FOREIGN KEY(FK_USER_ID) REFERENCES USERS(ID_USER),
    FOREIGN KEY(FK_MOVIES_ID) REFERENCES MOVIES(ID_MOVIE)
);