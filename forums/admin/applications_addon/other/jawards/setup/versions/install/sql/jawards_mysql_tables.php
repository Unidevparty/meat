<?php
$TABLE[] = "CREATE TABLE jlogica_awards (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `descno` varchar(255) NOT NULL,
  `longdesc` TEXT NOT NULL,
  `icon` varchar(50) NOT NULL,
  `placement` INT(2) NOT NULL,
  `parent` INT(8) NOT NULL,
  `visible` TINYINT(1) NOT NULL DEFAULT '1',
  `public_perms` TEXT NOT NULL,
  `badge_perms` TEXT NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY(id)
)";

$TABLE[] = "CREATE TABLE jlogica_awards_awarded (
  `row_id` int(8) NOT NULL auto_increment,
  `award_id` int(8) NOT NULL,
  `user_id` int(8) NOT NULL,
  `auto_award_id` int(8) default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `notes` text NOT NULL,
  `date` varchar(45) NOT NULL,
  `awarded_by` INT(8) NOT NULL,
  `approved` TINYINT(1) NOT NULL DEFAULT 1,
  `data` TEXT NOT NULL,
  PRIMARY KEY  (`row_id`),
  INDEX user_id (`user_id`)
)";

$TABLE[] = "CREATE TABLE jlogica_awards_cats (
  `cat_id` int(8) NOT NULL auto_increment,
  `title` varchar(25) NOT NULL,
  `placement` int(2) NOT NULL,
  `visible` tinyint(1) NOT NULL default '1',
  `frontend` TINYINT( 1 ) NOT NULL DEFAULT '1',
  `location` INT( 8 ) NOT NULL DEFAULT '0',
  `data` TEXT NOT NULL,
  PRIMARY KEY  (`cat_id`)
)";

$TABLE[] = "CREATE TABLE jlogica_awards_auto_awards (
  `inst_id` int(8) NOT NULL auto_increment,
  `award_id` int(8) NOT NULL,
  `title` varchar(35) NOT NULL,
  `type` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `notes` varchar(150) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  `placement` INT( 8 ) NOT NULL,
  PRIMARY KEY  (`inst_id`)
)";

$TABLE[] = "UPDATE jlogica_awards SET `parent` = '1'";
$TABLE[] = "INSERT INTO jlogica_awards_cats (`title`, `placement`) VALUES ('Main Category', '1')";

$TABLE[] = "ALTER TABLE groups ADD `g_jlogica_awards_can_give` TINYINT(1) NOT NULL DEFAULT '0'";
$TABLE[] = "ALTER TABLE groups ADD `g_jlogica_awards_can_remove` TINYINT(1) NOT NULL DEFAULT '0'";
$TABLE[] = "ALTER TABLE groups ADD `g_jlogica_awards_can_receive` TINYINT(1) NOT NULL DEFAULT '1'";
