<?php
$SQL[] = "ALTER TABLE jlogica_awards CHANGE `id` `id` INT( 8 ) NOT NULL AUTO_INCREMENT";
$SQL[] = "ALTER TABLE jlogica_awards CHANGE `parent` `parent` INT( 8 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `row_id` `row_id` INT( 8 ) NOT NULL AUTO_INCREMENT";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `award_id` `award_id` INT( 8 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `user_id` `user_id` INT( 8 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `date` `date` INT( 10 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `awarded_by` `awarded_by` INT( 8 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_cats CHANGE `cat_id` `cat_id` INT( 8 ) NOT NULL AUTO_INCREMENT";
$SQL[] = "ALTER TABLE jlogica_awards_cats CHANGE `title` `title` VARCHAR( 64 ) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_awarded CHANGE `auto_award_id` `auto_award_id` INT( 8 ) NULL DEFAULT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_auto_awards CHANGE `inst_id` `inst_id` INT( 8 ) NOT NULL AUTO_INCREMENT";
$SQL[] = "ALTER TABLE jlogica_awards_auto_awards CHANGE `award_id` `award_id` INT( 8 ) NOT NULL";
