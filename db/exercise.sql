CREATE TABLE `exercise` (
  `exercise_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_hidden` tinyint(1) unsigned NOT NULL default '1',
  `content` text NOT NULL COMMENT 'json',
  PRIMARY KEY  (`exercise_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
