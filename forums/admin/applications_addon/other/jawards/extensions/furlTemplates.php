<?php

if ( !defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

$_SEOTEMPLATES = array( 'app=jawards' => array( 'app'			=> 'jawards',
												'allowRedirect' => 1,
												'out'           => array( '/app=jawards/i', 'jawards/' ),
												'in'            => array( 'regex'   => "#^/jawards(/|$|\?)#i", 'matches' => array( array( 'app', 'jawards' ) ) ) )
					  );
