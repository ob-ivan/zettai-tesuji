CREATE TABLE `pai` (
  `pai_id` tinyint(2) unsigned NOT NULL auto_increment,
  `display` varchar(2) NOT NULL,
  PRIMARY KEY  (`pai_id`),
  UNIQUE KEY `display` (`display`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
