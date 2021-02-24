<?php
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_awards_perms extends ipsCommand
{
	function doExecute(ipsRegistry $registry)
	{
		if($this->settings['jawards_disable_public_awding'])
		{
			$url = $this->settings['base_url'];
			$url = str_replace('app=jawards', 'app=core', $url);

			$this->registry->getClass('output')->html_main .= "
			<div class='warning'>
			  <h4>Alert!</h4>
			  You have disabled public awarding, that makes these permissions irrelevant! Please turn public awarding on before continuing.<br />
			  <br />
			  <a href='{$url}module=settings&section=settings&do=findsetting&key=publicawarding'>Click Here to Change Settings</a>
			</div>";

			$this->registry->getClass('output')->sendOutput();
		}

		# Set some stuff
		$imagePermOK = "<img src='{$this->settings['skin_acp_url']}/images/icons/tick.png' title='Disable Ability for Group' />";
		$imagePermNO = "<img src='{$this->settings['skin_acp_url']}/images/icons/cross.png' title='Enable Ability for Group' />";

		$this->registry->getClass('output')->html_main .= "
		<div class='section_title'>
		  <h2>Public Awarding Permissions</h2>
	    </div>";

		# Get the groups
		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'groups',
							   'order'  => 'g_id ASC',
		));

		$this->DB->execute();

		while($g = $this->DB->fetch())
		{
			if($g['g_jlogica_awards_can_give'])
			{
				$give = $imagePermOK;
			}
			else
			{
				$give = $imagePermNO;
			}

			if($g['g_jlogica_awards_can_remove'])
			{
				$remove = $imagePermOK;
			}
			else
			{
				$remove = $imagePermNO;
			}

			if($g['g_jlogica_awards_can_receive'])
			{
				$receive = $imagePermOK;
			}
			else
			{
				$receive = $imagePermNO;
			}

			$givePerms   .= "
			giveAwdPerms[{$g['g_id']}] = {$g['g_jlogica_awards_can_give']};";
			$removePerms .= "
			removeAwdPerms[{$g['g_id']}] = {$g['g_jlogica_awards_can_remove']};";
			$receivePerms .= "
			receiveAwdPerms[{$g['g_id']}] = {$g['g_jlogica_awards_can_receive']};";

			if($g['g_access_cp'])
			{
				$note = " ( <em>Allowed by Default</em> )";
			}

			$rows .= "
			<tr>
			  <td>{$g['prefix']}{$g['g_title']}{$g['suffix']}{$note}</td>
			  <td align='center'><a href='javascript: updatePerm(\"give\", {$g['g_id']});' id='give_{$g['g_id']}'>{$give}</a></td>
			  <td align='center'><a href='javascript: updatePerm(\"remove\", {$g['g_id']});' id='remove_{$g['g_id']}'>{$remove}</a></td>
			  <td align='center'><a href='javascript: updatePerm(\"receive\", {$g['g_id']});' id='receive_{$g['g_id']}'>{$receive}</a></td>
			</tr>";

			unset($note);
		}

		$this->registry->getClass('output')->html_main .= "
		<script type='text/javascript'>
		var stylesURL = '{$this->settings['skin_acp_url']}';
		var OKimg     = \"{$imagePermOK}\";
		var NOimg     = \"{$imagePermNO}\";

		var giveAwdPerms    = [];
		var removeAwdPerms  = [];
		var receiveAwdPerms = [];{$givePerms}{$removePerms}{$receivePerms}
		</script>
		<script type='text/javascript' src='applications_addon/other/jawards/js/perms.js'></script>
		<div class='information-box' style='margin-bottom:15px;'>
		  <h4>Help Using this Page</h4>
		  {$imagePermOK} = The group has permission to preform that action, click it to disable that permission<br />
		  {$imagePermNO} = The group does not have permission to preform that action, click it to enable that permission<br />
		  <br />
		  You can determine the awards each group is allowed to give by editing the settings for each individual award.
		</div>
		<div class='acp-box'>
		  <h3>User Groups</h3>
		  <table class='ipsTable double_pad'>
		    <tr>
			  <th width='25%'>Group Title</th>
			  <th width='25%'><div align='center'>Can Give Awards</div></th>
			  <th width='25%'><div align='center'>Can Remove Awards</div></th>
			  <th width='25%'><div align='center'>Can Receive Awards</div></th>
			</tr>
		    {$rows}
		  </table>
		</div>";

		$this->registry->getClass('output')->sendOutput();
	}
}
