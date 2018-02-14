CREATE TABLE `subscribers` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`fname` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	`lname` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	`email` VARCHAR(255) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	`phone` VARCHAR(255) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `email` (`email`),
	INDEX `phone` (`phone`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB