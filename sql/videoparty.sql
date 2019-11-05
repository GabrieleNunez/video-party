-- Dumping structure for table thecocon_levelcrush_movietime.chat_messages
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL DEFAULT '0',
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ticket` (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- Dumping structure for table thecocon_levelcrush_movietime.player_syncronization
CREATE TABLE IF NOT EXISTS `player_syncronization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `current` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `stream_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping structure for table thecocon_levelcrush_movietime.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `master` tinyint(1) DEFAULT '0',
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `deleted_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `viewerlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` int(11) NOT NULL,
  `ping` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket` (`ticket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=CHARSET=utf8_unicode_ci;;