CREATE TABLE `theme` (
  `theme_id` int(11) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `is_hidden` tinyint(1) NOT NULL,
  `intro` text,
  `min_exercise_id` int(11) default NULL,
  `max_exercise_id` int(11) default NULL,
  `advanced_percent` int(11) default NULL,
  `intermediate_percent` int(11) default NULL,
  PRIMARY KEY  (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
