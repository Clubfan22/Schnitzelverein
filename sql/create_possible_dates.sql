CREATE TABLE IF NOT EXISTS possible_dates (
id INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
event_id INT UNSIGNED NOT NULL ,
possible_date TIMESTAMP NOT NULL,
CONSTRAINT `fk_e_id` FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE ON UPDATE CASCADE
);