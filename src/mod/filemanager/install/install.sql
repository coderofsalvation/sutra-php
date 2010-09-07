
CREATE TABLE `sutra_page` (
  `id` int(6) NOT NULL auto_increment,
  `parent_id` int(6) NOT NULL,
  `weigth` int(6) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `title_url` varchar(255) NOT NULL default '',
  `title_menu` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `type` text NOT NULL default '',
  `tpl` varchar(255) NOT NULL,
  `yaml` longtext NOT NULL,
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
