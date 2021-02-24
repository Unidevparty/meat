<?php
$SQL[] = "ALTER TABLE jlogica_awards ADD `longdesc` TEXT NOT NULL";
$SQL[] = "UPDATE jlogica_awards SET `longdesc` =  `desc`";
