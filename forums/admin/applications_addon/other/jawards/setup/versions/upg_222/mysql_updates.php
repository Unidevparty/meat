<?php
$SQL[] = "ALTER TABLE groups ADD `g_can_give_awards` TINYINT(1) NOT NULL DEFAULT '0'";
$SQL[] = "ALTER TABLE groups ADD `g_can_remove_awards` TINYINT(1) NOT NULL DEFAULT '0'";
$SQL[] = "ALTER TABLE groups ADD `g_can_receive_awards` TINYINT(1) NOT NULL DEFAULT '1'";
$SQL[] = "ALTER TABLE inv_awards ADD `public_perms` TEXT NOT NULL";
$SQL[] = "ALTER TABLE inv_awards_awarded ADD `awarded_by` INT( 255 ) NOT NULL AFTER `user_id`";
$SQL[] = "ALTER TABLE inv_awards_awarded ADD `approved` TINYINT(1) NOT NULL DEFAULT 1";
