<?php
$sql[] = "CREATE TABLE IF NOT EXISTS inv_awards_awarded (
  `row_id` int(255) NOT NULL auto_increment,
  `award_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `notes` text NOT NULL,
  `date` varchar(45) NOT NULL,
  PRIMARY KEY  (`row_id`)
)";
