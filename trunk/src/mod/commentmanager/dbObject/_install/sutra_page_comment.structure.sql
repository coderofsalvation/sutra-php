CREATE TABLE sutra_page_comment (
  id int(11)      NOT NULL AUTO_INCREMENT,
  author varchar(255) NOT NULL,
  date date         NOT NULL,
  email varchar(255) NOT NULL,
  website varchar(255) NOT NULL,
  html text         NOT NULL,
  page_id int(11)      NOT NULL ,
  page_id int(11) NOT NULL,
  PRIMARY KEY( id )
) TYPE=MyISAM;
