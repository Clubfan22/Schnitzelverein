CREATE TABLE IF NOT EXISTS events(
id INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
event_date TIMESTAMP,
location VARCHAR(127),
street VARCHAR(127),
city VARCHAR(127),
text VARCHAR(2047)
);