<?php

/**
 * Product Title:		(SOS34) Track Members
 * Product Version:		1.1.2
 * Author:				Adriano Faria
 * Website:				SOS Invision
 * Website URL:			http://forum.sosinvision.com.br/
 * Email:				administracao@sosinvision.com.br
 */
 
if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class task_item
{
	protected $class;
	protected $task			= array();
	protected $restrict_log	= false;

	protected $registry;
	protected $DB;
	protected $settings;
	protected $lang;

	protected $deleteTime = 5184000;

	public function __construct( ipsRegistry $registry, $class, $task )
	{
		/* Make registry objects */
		$this->registry	= $registry;
		$this->DB		= $this->registry->DB();
		$this->settings =& $this->registry->fetchSettings();
		$this->lang		= $this->registry->getClass('class_localization');

		$this->class	= $class;
		$this->task		= $task;
	}

	public function runTask()
	{
		$this->lang->loadLanguageFile( array( 'admin_trackmembers' ), 'trackmembers' );

		$limit = $this->settings['trackmembers_log_days'] == 60 ? 5184000 : IPS_UNIX_TIME_NOW + ( 86400 * $this->settings['trackmembers_log_days'] );

		$cutoff = intval( $limit ) ? $limit : 5184000;

		$this->DB->delete( "members_tracker", "date < " . (time() - $cutoff) );

		$this->class->appendTaskLog( $this->task, $this->lang->words['task_logprune'] );

		$this->class->unlockTask( $this->task );
	}
}
?>