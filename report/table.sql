CREATE TABLE `demo_twitter_timeline` (
  `id` int(10) NOT NULL auto_increment,
  `tweet` varchar(140) collate utf8_unicode_ci NOT NULL default '',
  `dt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;