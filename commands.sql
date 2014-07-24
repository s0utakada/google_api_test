CREATE DATABASE google_connect_php;
GRANT ALL ON google_connect_php.* TO dbuser@localhost
IDENTIFIED BY '';

USE google_connect_php;

CREATE TABLE users (
    id                  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    google_user_id      VARCHAR(30) UNIQUE,
    google_email        VARCHAR(255),
    google_name         VARCHAR(255),
    google_picture      VARCHAR(255),
    google_access_token VARCHAR(255),
    created             DATETIME,
    modified            DATETIME
);
