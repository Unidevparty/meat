<?php
$SQL[] = "
CREATE TABLE inv_awards_auto_awards (
  `inst_id` mediumint(8) NOT NULL auto_increment,
  `award_id` mediumint(8) NOT NULL,
  `title` varchar(35) NOT NULL,
  `type` varchar(25) NOT NULL,
  `data` text NOT NULL,
  `notes` varchar(150) NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`inst_id`)
);";

$SQL[] = "ALTER TABLE inv_awards_awarded ADD `auto_award_id` mediumint(8) default NULL AFTER `user_id`";
