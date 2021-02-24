<?php
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_system_root extends ipsCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'jlogica_awards_awarded',
							   'where'  => 'approved != 1',
		));

		$getAwarded = $this->DB->execute();

		if($this->DB->getTotalRows())
		{
			while($a = $this->DB->fetch($getAwarded))
			{
				$this->DB->build(array(
									   'select' => '*',
									   'from'   => 'jlogica_awards',
									   'where'  => 'id = ' . $a['award_id'],
				));

				$getAward = $this->DB->execute();

				$aa = $this->DB->fetch($getAward);

				$to = IPSMember::load($a['user_id']);
				$by = IPSMember::load($a['awarded_by']);

				if($a['approved'] < 1 && $a['approved'] > -1)
				{
					$options = "<a href='{$this->settings['base_url']}module=awards&section=award&do=delete&id={$a['row_id']}&from=root' class='ipsBadge badge_red'>DELETE</a> <a href='{$this->settings['base_url']}module=awards&section=award&do=approve&id={$a['row_id']}&from=root' class='ipsBadge badge_green'>APPROVE</a>";
				}
				elseif($a['approved'] < 0)
				{
					$options = "<a href='{$this->settings['base_url']}module=awards&section=award&do=delete&id={$a['row_id']}&from=root' class='ipsBadge badge_red'>DELETE</a> <a href='{$this->settings['base_url']}module=awards&section=award&do=remove&id={$a['row_id']}&active=1&from=root' class='ipsBadge badge_green'>READD</a>";
				}

				$row = "
				<tr>
				  <td>{$aa['name']}</td>
				  <td>{$to['members_display_name']}</td>
				  <td>{$by['members_display_name']}</td>
				  <td>{$a['notes']}</td>
				  <td align='right'>{$options}</td>
				</tr>";

				if($a['approved'] < 0)
				{
					$removalRows .= $row;
				}
				else
				{
					$givingRows .= $row;
				}
			}
		}

		if(!$removalRows)
		{
			$removalRows = "
			<tr>
			  <td colspan='5' align='center'><em>There aren't any awarded awards pending deletion</em></td>
			</tr>";
		}

		if(!$givingRows)
		{
			$givingRows = "
			<tr>
			  <td colspan='5' align='center'><em>There aren't any awarded awards pending approval</em></td>
			</tr>";
		}

		$result = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'core_applications', 'where' => "app_directory='jawards'" ) );

		$classToLoad = IPSLib::loadLibrary( IPS_KERNEL_PATH . '/classFileManagement.php', 'classFileManagement' );
		$checker = new $classToLoad();

		/* Timeout to prevent page from taking too long */
		$checker->timeout = 5;

		/* Setup url and check */

		$url = "http://ipb.bbcode.it/resource_updates.php?resource=jawards&version={$result['app_long_version']}&boardVersion=" . IPB_LONG_VERSION;
		$return = $checker->getFileContents( $url );
		$x = explode( '|', $return );

		if( $x[0] == '0' )
		{
			$dwnld = "<span class='desctext'>Up to date</span>";
		}
		else
		{
			$dwnld = "";
			$dwnld = "<span class='ipsBadge badge_purple'>Update Available</span>";
			if( ! empty( $x[1] ) )
			{
				$dwnld = "<a href='{$x[1]}' target='_blank'>{$dwnld}</a>";
			}
			elseif( $result['app_website'] )
			{
				$dwnld = "<a href='{$result['app_website']}' target='_blank'>{$dwnld}</a>";
			}
		}

		$this->registry->getClass('output')->html_main = "
		<div class='section_title'>
		  <h2>[HQ] Awards &hellip; Overview</h2>
		</div>
		<div style='float:right; width:39%;'>
		  <div class='acp-box'>
 		    <h3>System Information</h3>
		  </div>
		  <div class='acp-box' style='margin-top:10px;'>
			<table class='ipsTable'>
			  <tr>
			    <td width='50%'><strong>Application Name</strong></td>
				<td align='center'>[HQ] Awards</td>
			  </tr>
			</table>
		  </div>
		  <div class='acp-box' style='margin-top:10px;'>
			<table class='ipsTable'>
			  <tr>
			    <td width='50%'><strong>Current Version</strong></td>
				<td align='center'>{$result['app_version']} {$dwnld}</td>
			  </tr>
			</table>
		  </div>
	 	  <br /><br />
		  <div class='acp-box'>
 		    <h3>Developer Information</h3>
			<table class='ipsTable'>
			  <tr><td width='5%'></td><td>
	 		    <center>
	 		    If you find this application has been helpful a testimonial and/or donation to invisionHQ <br />(paypal: reficul@lamoneta.it) would be appreciated.
	 		    <br /><br />
	 		    Special thanks to Alec Carpenter from Invision Envy who originally developed the (inv) Awards application and JLogica that resume this app.
	 		    <br /><br />
	 		    This application is copyright &copy; Gabriele Venturini, invisionHQ All Rights Reserved
	 		    </center>
 		    </td><td width='5%'></td></tr>
 		    </table>
		  </div>
		</div>
		<div style='float:left; width:60%;'>
		  <div class='acp-box'>
 		    <h3>Awards Pending Approval</h3>
			<table class='ipsTable'>
			  <tr>
			    <th width='20%'>Award</th>
				<th width='20%'>Awarded To</th>
				<th width='20%'>Awarded By</th>
				<th width='20%'>Notes</th>
				<th width='20%'></th>
			  </tr>
			  {$givingRows}
			</table>
		  </div>
		  <br />
		  <div class='acp-box'>
 		    <h3>Awards Pending Deletion</h3>
			<table class='ipsTable'>
			  <tr>
			    <th width='20%'>Award</th>
				<th width='20%'>Awarded To</th>
				<th width='20%'>Awarded By</th>
				<th width='20%'>Notes</th>
				<th width='20%'></th>
			  </tr>
			  {$removalRows}
			</table>
		  </div>
		</div>
		<div style='clear:both;'></div>";

		$this->registry->getClass('output')->sendOutput();
	}
}
