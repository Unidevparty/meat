<?php
$SQL[] = "
CREATE TABLE inv_awards_cats (
  `cat_id` int(255) NOT NULL auto_increment,
  `title` varchar(25) NOT NULL,
  `placement` int(2) NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`cat_id`)
);";

$SQL[] = "INSERT INTO inv_awards_cats (`title`, `placement`) VALUES ('Main Category', '1')";
$SQL[] = "ALTER TABLE inv_awards ADD `placement` INT(2) NOT NULL, ADD `parent` INT(255) NOT NULL, ADD `visible` TINYINT(1) NOT NULL DEFAULT '1'";
$SQL[] = "UPDATE inv_awards SET `parent` = '1'";
