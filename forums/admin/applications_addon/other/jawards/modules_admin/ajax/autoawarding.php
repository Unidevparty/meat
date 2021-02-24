<?php
if (!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_jawards_ajax_autoawarding extends ipsAjaxCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		$this->DB->update('jlogica_awards_auto_awards', array('enabled' => $this->request['status']), 'inst_id = ' . $this->request['id']);

		$this->returnHTML(1);
	}
}
