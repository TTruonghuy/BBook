CREATE TABLE IF NOT EXISTS  `#__content_postlike` (
	`content_id` INT(11) NOT NULL,
	`lastip` VARCHAR(50) NOT NULL,
	`rating_dislike` INT(11) NOT NULL,
	`rating_like` INT(11) NOT NULL,
	KEY `postlike_idx` (`content_id`)
 	);