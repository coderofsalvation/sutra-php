
CREATE TABLE `sutra_category` (
  `id` int(6) NOT NULL auto_increment,
  `parent_id` int(6) NOT NULL,
  `weight` int(6) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `title_url` varchar(255) NOT NULL default '',
  `yaml` longtext NOT NULL,
  `locked` tinyint(1) NOT NULL default '0',
  `count_pages` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
