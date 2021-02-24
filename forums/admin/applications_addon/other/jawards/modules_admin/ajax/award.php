<?php
if (!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_jawards_ajax_award extends ipsAjaxCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		if($_POST['notes'] && $_POST['row_id'])
		{
			$this->DB->update('jlogica_awards_awarded', array('notes' => $_POST['notes']), '`row_id` = "' . $_POST['row_id'] . '"');

			$this->returnHTML(1);
		}
		else
		{
			$this->returnHTML(0);
		}
	}
}
