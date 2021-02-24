<?php
if (!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class admin_jawards_ajax_perms extends ipsAjaxCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		$field = 'g_jlogica_awards_can_' . $this->request['which'];
		$this->DB->update('groups', array($field => $_POST['newperm']), 'g_id = ' . $this->request['gid']);
		$this->cache->rebuildCache('group_cache', 'global');
		$this->returnHTML('OkGo');
	}
}
