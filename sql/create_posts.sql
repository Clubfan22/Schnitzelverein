CREATE TABLE IF NOT EXISTS posts(
id INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
release_date TIMESTAMP,
author_id INT UNSIGNED,
title VARCHAR(255),
text TEXT,
CONSTRAINT `fk_author` FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
);
