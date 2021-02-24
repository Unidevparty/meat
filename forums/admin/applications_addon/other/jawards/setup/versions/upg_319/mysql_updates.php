<?php
$SQL[] = "ALTER TABLE jlogica_awards ADD `descno` varchar(255) NOT NULL";
$SQL[] = "ALTER TABLE jlogica_awards CHANGE `desc` `desc` varchar(255) NOT NULL";
