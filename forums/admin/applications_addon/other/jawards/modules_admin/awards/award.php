<?php
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_awards_award extends ipsCommand
{
	function doExecute(ipsRegistry $registry)
	{
		switch($this->request['do'])
		{
			case "give":
				$this->give();
				break;
			case "remove":
				$this->remove();
				break;
			case "delete":
				$this->delete();
				break;
			case "approve":
				$this->approve();
				break;
			case "user":
				$this->user();
				break;
			case "search":
				$this->search();
				break;
			default:
				$this->main();
				break;
		}

		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	function main()
	{
		# Title
		$this->registry->getClass('output')->html_main .= "
		<div class='section_title'>
		  <h2>User's Awards Management</h2>
	    </div>";


		# The rest....
		$this->registry->getClass('output')->html_main .= "
		<form name='userSearch' method='post' action='{$this->settings['base_url']}module=awards&section=award&do=search'>
		<div class='acp-box'>
 		  <h3>Search for User</h3>
		  <div class='tablerow1' style='padding:10px;' align='center'>
		  	Enter the display name of an user below to manage their awards. Leave blank to retrieve all members.<br />
			<br />
			<input type='text' name='query' size='35' />
		  </div>
		  <div class='acp-actionbar' align='center'>
		    <input type='submit' value=' Search ' class='realbutton' />
		  </div>
		</div>
		</form>";
	}

	function search()
	{
		urldecode($this->request['query']);

		# Get members
		$this->DB->build(array(
								'select' => '*',
								'from'   => 'members',
								'where'  => "members_display_name LIKE '%{$this->request['query']}%'",
		));

		$this->DB->execute();

		$num = $this->DB->getTotalRows();

		# Did we get any?
		if(!$num)
		{
			$this->registry->getClass('output')->html_main .= "
			<div class='information-box' style='margin-bottom:15px;'>
			  <h4><img src={$this->settings['skin_acp_url']}/images/icons/bullet_error.png' /> No Results!</h4>
			  Your search has not returned any results.
			</div>";

			$this->main();

			return;
		}

		# Is there just one
		if($num === 1)
		{
			$m = $this->DB->fetch();

			$this->registry->output->silentRedirect($this->settings['base_url'] . 'module=awards&section=award&do=user&id=' . $m['member_id']);
		}

		# Create the results
		$n = 1;
		while($m = $this->DB->fetch())
		{
			if($n&1)
			{
				$row = "tablerow1";
			}
			else
			{
				$row = "tablerow2";
			}

			$rows .= "
			<div class='{$row}' style='padding:10px;'><a href='{$this->settings['base_url']}module=awards&section=award&do=user&id={$m['member_id']}&returnQuery={$this->request['query']}'>{$m['members_display_name']}</a></div>";

			$n++;
		}

		# Title
		$this->registry->getClass('output')->html_main .= "
		<div class='section_title'>
		  <h2>User's Awards Management</h2>
	    </div>";

		# Show results
		$this->registry->getClass('output')->html_main .= "
		<div class='acp-box'>
 		  <h3>Search Results for: {$this->request['query']}</h3>
		  {$rows}
		</div>";
	}

	function user()
	{
		# Get the member data
		$currentMember = ipsRegistry::instance()->member()->fetchMemberData();
		$editingMember = IPSMember::load($this->request['id']);

		# Make sure it's all good...
		if(!$editingMember['member_id'])
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		$returnQuery = urlencode($this->request['returnQuery']);

		if(!$returnQuery)
		{
			$returnURL = $this->settings['base_url'] . "module=awards&section=award";
			$text      = "Search Again";
		}
		else
		{
			$returnURL = $this->settings['base_url'] . "module=awards&section=award&do=search&query=" . $returnQuery;
			$text      = "Return to Results";
		}

		# Title
		$this->registry->getClass('output')->html_main .= "
		<div class='section_title'>
		  <h2>User's Awards Management</h2>
		  <ul class='context_menu'>
		    <li>
			  <a href='{$returnURL}'>{$text}</a>
		    </li>
		  </ul>
	    </div>";

		# See if we can award!
		$canAward = 1;

		if( ! $this->settings['jacore_self_awarding'] )
		{
			if( $currentMember['member_id'] == $editingMember['member_id'] && ! $this->currentMember['g_access_cp'] )
			{
				$canAward = 0;
			}
		}

		$field = $this->registry->getClass('class_jawards')->awardsMenu("awardToGive");

		# Show the "give award" box
		if($field)
		{
				$this->registry->getClass('output')->html_main .= "
				<form name='giveAward' method='post' action='{$this->settings['base_url']}module=awards&section=award&do=give&mid={$editingMember['member_id']}'>
				<div class='acp-box'>
				  <h3>Give {$editingMember['members_display_name']} an Award</h3>
				  <table with='100%' class='double_pad'>
					<tr>
					  <td class='tablerow1' width='35%'><strong>Choose an Award</strong></td>
					  <td class='tablerow2'>{$field}</td>
					</tr>
					<tr>
					  <td class='tablerow1'>
						<strong>Notes</strong><br />
						<div class='desctext'>A brief description for giving this award.</div>
					  </td>
					  <td class='tablerow2'>
						<textarea name='notes' rows='4' cols='25'></textarea>
					  </td>
					</tr>
				  </table>
				  <div class='acp-actionbar'>
					  <input type='submit' value=' Give Award ' class='realbutton' />
					</div>
				</div>
				</form>";
		}

		# Prepare the awards this user has for output
		$this->DB->build(array(
							   'select'   => 'aa.row_id,aa.notes,aa.is_active',
							   'from'     => array('jlogica_awards_awarded' => 'aa'),
							   'add_join' => array(
							   					   0 => array(
												   			  'select' => 'a.id,a.name,a.icon',
												 			  'from'   => array('jlogica_awards' => 'a'),
												  			  'where'  => 'a.id = aa.award_id',
														),
							   ),
							   'where'    => 'user_id = ' . $editingMember['member_id'],
							   'order'    => 'date ASC',
		));

		$this->DB->execute();

		if($this->DB->getTotalRows())
		{
			while($a = $this->DB->fetch())
			{
				# Includes the editing feature for the notes
				$notes = $a['notes'] . " <span style='display:none;' id='edit_{$a['row_id']}'><a href='javascript: editNotes({$a['row_id']}, 1, 0);'><img src='{$this->settings['skin_acp_url']}/images/icons/pencil.png' alt='Edit Notes' title='Edit Notes' border='0' /></a></span>";

				# Creates the array for the javascript
				$notesArray .= "notesArray[{$a['row_id']}] = \"" . addslashes($a['notes']) . "\";";

				if($a['is_active'])
				{
					$currentRows .= "
					<tr id='awardRow_{$a['row_id']}' class='ipsControlRow' onmouseover='javascript: showEditBtn({$a['row_id']}, 1);' onmouseout='javascript: showEditBtn({$a['row_id']}, 0);'>
					  <td align='center'><img src='" . $this->settings['upload_url'] . '/jawards/' . "{$a['icon']}' /></td>
					  <td>{$a['name']}</td>
					  <td id='notes_td_{$a['row_id']}'>{$notes}</td>
					  <td align='center' width='15%'>
						<ul class='ipsControlStrip'>
					      <li class='i_disable'>
						    <a href='{$this->settings['base_url']}module=awards&section=award&do=remove&id={$a['row_id']}&active=0'>Remove Award</a>
					      </li>
						  <li class='i_delete'>
						    <a href='javascript: deleteAward({$a['row_id']});'>Delete Award</a>
					      </li>
				        </ul>
					  </td>
					</tr>";
				}
				else
				{
					$removedRows .= "
					<tr id='awardRow_{$a['row_id']}' class='ipsControlRow' onmouseover='javascript: showEditBtn({$a['row_id']}, 1);' onmouseout='javascript: showEditBtn({$a['row_id']}, 0);'>
					  <td align='center'><img src='" . $this->settings['upload_url'] . '/jawards/' . "{$a['icon']}' /></td>
					  <td>{$a['name']}</td>
					  <td id='notes_td_{$a['row_id']}'>{$notes}</td>
					  <td align='center'>
					    <ul class='ipsControlStrip'>
					      <li class='i_add'>
						    <a href='{$this->settings['base_url']}module=awards&section=award&do=remove&id={$a['row_id']}&active=1'>Re-Add Award</a>
					      </li>
						  <li class='i_delete'>
						    <a href='javascript: deleteAward({$a['row_id']});'>Delete Award</a>
					      </li>
				        </ul>
					  </td>
					</tr>";
				}
			}
		}

		# Check to see if there are rows for both current and removed awards
		if(!$currentRows)
		{
			$currentRows = "
			<tr>
			  <td colspan='4' align='center'>{$editingMember['members_display_name']} does not have any \"current\" awards</td>
			</tr>";
		}

		if(!$removedRows)
		{
			$removedRows = "
			<tr>
			  <td colspan='4' align='center'>{$editingMember['members_display_name']} does not have any \"removed\" awards</td>
			</tr>";
		}

		$this->registry->getClass('output')->html_main .= "
		<br />
		<script type='text/javascript'>
		var conf;
		var InEditMode = 0;
		var notesArray = [];
		var CP_SKIN_URL = '{$this->settings['skin_acp_url']}';
		{$notesArray}
		</script>

		<script type='text/javascript' src='{$this->settings['js_app_url']}award.js'></script>

		<div class='acp-box'>
		  <h3>{$editingMember['members_display_name']}'s Awards</h3>
          <div id='tabstrip_usersAwards' class='ipsTabBar with_left with_right'>
			<ul id='tab_awards' class='tab_bar no_title'>
	          <li id='tab_current' class=''>Current Awards</li>
		      <li id='tab_removed' class=''>Removed Awards</li>
		    </ul>
		  </div>
		  <div id='tabstrip_usersAwards_content' class='ipsTabBar_content'>
		  <div id='tab_current_content'>
		    <table with='100%' class='ipsTable double_pad'>
		      <tr>
				<th width='15%'><div align='center'>Award Image</div></th>
				<th width='25%'>Award Title</th>
				<th width='30%'>Notes</th>
				<th width='15%'>&nbsp;</th>
			  </tr>
			  {$currentRows}
			</table>
		  </div>
		  <div id='tab_removed_content' style='display:none;'>
		    <table with='100%' class='ipsTable double_pad'>
		      <tr>
				<th width='15%'><div align='center'>Award Image</div></th>
				<th width='25%'>Award Title</th>
				<th width='30%'>Notes</th>
				<th width='15%'>&nbsp;</th>
			  </tr>
			  {$removedRows}
			</table>
		  </div>
		  </div>
		</div>

		<script type='text/javascript'>
		  jQ(\"#tabstrip_usersAwards\").ipsTabBar({ tabWrap: \"#tabstrip_usersAwards\" });
		</script>";
	}

	function remove()
	{
		# Get award
		$this->DB->build(array(
								'select' => '*',
								'from'   => 'jlogica_awards_awarded',
								'where'  => "row_id = {$this->request['id']}",
		));

		$this->DB->execute();

		# Make sure it's all good...
		if(!$this->DB->getTotalRows())
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		$a = $this->DB->fetch();

		if($this->request['from'] == "root")
		{
			$approved = 1;
			$url      = $this->settings['base_url'];
		}
		else
		{
			$approved = $a['approved'];
			$url      = $this->settings['base_url'] . 'module=awards&section=award&do=user&id=' . $a['user_id'];
		}

		$this->DB->update('jlogica_awards_awarded', array(
													  'is_active' => $this->request['active'],
													  'approved'  => $approved,
		), 'row_id=' . $a['row_id']);

		# Redirect
		$this->registry->output->global_message = "Award Status Updated";
		$this->registry->output->silentRedirectWithMessage($url);
	}

	function delete()
	{
		# Get award
		$this->DB->build(array(
								'select' => '*',
								'from'   => 'jlogica_awards_awarded',
								'where'  => "row_id = {$this->request['id']}",
		));

		$this->DB->execute();

		# Make sure it's all good...
		if(!$this->DB->getTotalRows())
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		$a = $this->DB->fetch();

		$this->DB->delete('jlogica_awards_awarded', 'row_id = ' . $a['row_id']);

		if($this->request['from'] == "root")
		{
			$url = $this->settings['base_url'];
		}
		else
		{
			$url = $this->settings['base_url'] . 'module=awards&section=award&do=user&id=' . $a['user_id'];
		}

		# Redirect
		$this->registry->output->global_message = "Award Deleted";
		$this->registry->output->silentRedirectWithMessage($url);
	}

	function approve()
	{
		# Get award
		$this->DB->build(array(
								'select' => '*',
								'from'   => 'jlogica_awards_awarded',
								'where'  => "row_id = {$this->request['id']}",
		));

		$this->DB->execute();

		# Make sure it's all good...
		if(!$this->DB->getTotalRows())
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		$a = $this->DB->fetch();

		$this->DB->update('jlogica_awards_awarded', array('approved' => 1), 'row_id = ' . $a['row_id']);

		if($this->request['from'] == "root")
		{
			$url = $this->settings['base_url'];
		}
		else
		{
			$url = $this->settings['base_url'] . 'module=awards&section=award&do=user&id=' . $a['user_id'];
		}

		# Redirect
		$this->registry->output->global_message = "Award Approved";
		$this->registry->output->silentRedirectWithMessage($url);
	}

	function give()
	{
		# Get member
		$m = IPSMember::load($this->request['mid']);

		# Make sure it's all good...
		if(!$m['member_id'])
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		if(!$this->request['awardToGive'])
		{
			$this->registry->output->showError('You must select an award');
		}

		$this->registry->getClass('class_jawards')->giveAward($this->request['awardToGive'], $m['member_id'], $this->request['notes']);

		$this->registry->output->global_message = "Award Given";
		$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . 'module=awards&section=award&do=user&id=' . $m['member_id']);
	}
}
