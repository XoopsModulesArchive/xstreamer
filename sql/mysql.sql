CREATE TABLE `xstreamer_video` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `width` smallint(6) default NULL,
  `height` smallint(6) default NULL,
  `filepath` varchar(255) NOT NULL default '',
  `description` text,
  `date_added` int(11) default NULL,
  `rating` float(9,2) default NULL,
  `views` int(11) default NULL,
  `votes` int(11) default NULL,
  `comments` int(11) default NULL,
  `visible` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`),
  KEY `name` (`name`)
) TYPE=MyISAM;

CREATE TABLE `xstreamer_category` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `xstreamer_vote` (
  `id` int(11) NOT NULL auto_increment,
  `video_id` int(11) NOT NULL default '0',
  `uid` int(11) default NULL,
  `ip` varchar(15) default NULL,
  `rating` tinyint(4) default NULL,
  `date_voted` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `video_id` (`video_id`),
  KEY `uid` (`uid`),
  KEY `ip` (`ip`)
) TYPE=MyISAM;