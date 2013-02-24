CREATE TABLE `mondai` (
  `mondai_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_hidden` tinyint(1) unsigned NOT NULL default '1',
  `content` text NOT NULL COMMENT 'json',
  PRIMARY KEY  (`mondai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
