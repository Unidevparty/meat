<?php
$SQL[] = "ALTER TABLE jlogica_awards_cats ADD `data` TEXT NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards_cats ADD `location` INT( 8 ) NOT NULL DEFAULT '0'";
$SQL[] = "ALTER TABLE jlogica_awards ADD `data` TEXT NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards DROP `location`";
$SQL[] = "ALTER TABLE jlogica_awards_auto_awards ADD `placement` INT( 8 ) NOT NULL";
