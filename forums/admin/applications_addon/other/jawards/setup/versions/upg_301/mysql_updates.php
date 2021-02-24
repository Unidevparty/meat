<?php
$SQL[] = "ALTER TABLE groups CHANGE g_can_give_awards 	 g_jlogica_awards_can_give    TINYINT(1) NOT NULL DEFAULT '0'";
$SQL[] = "ALTER TABLE groups CHANGE g_can_remove_awards  g_jlogica_awards_can_remove  TINYINT(1) NOT NULL DEFAULT '0'";
$SQL[] = "ALTER TABLE groups CHANGE g_can_receive_awards g_jlogica_awards_can_receive TINYINT(1) NOT NULL DEFAULT '0'";
$SQL[] = "CREATE TABLE jlogica_awards (
`id` int(255) NOT NULL auto_increment,
`name` varchar(255) NOT NULL,
`desc` text NOT NULL,
`icon` varchar(50) NOT NULL,
`placement` INT(2) NOT NULL,
`parent` INT(255) NOT NULL,
`visible` TINYINT(1) NOT NULL DEFAULT '1',
`public_perms` TEXT NOT NULL,
`location` INT( 8 ) NOT NULL DEFAULT '0',
PRIMARY KEY(id)
);";

$SQL[] = "CREATE TABLE jlogica_awards_awarded (
  `row_id` int(255) NOT NULL auto_increment,
  `award_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `auto_award_id` mediumint(8) default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `notes` text NOT NULL,
  `date` varchar(45) NOT NULL,
  `awarded_by` INT(255) NOT NULL,
  `approved` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY  (`row_id`)
)";

$SQL[] = "CREATE TABLE jlogica_awards_cats (
  `cat_id` int(255) NOT NULL auto_increment,
  `title` varchar(25) NOT NULL,
  `placement` int(2) NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`cat_id`)
)";

$SQL[] = "CREATE TABLE jlogica_awards_auto_awards (
  `inst_id` mediumint(8) NOT NULL auto_increment,
  `award_id` mediumint(8) NOT NULL,
  `title` varchar(35) NOT NULL,
  `type` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `notes` varchar(150) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`inst_id`)
);";
