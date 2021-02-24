<?php
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_awards_bulkaward extends ipsCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		$this->registry->getClass('output')->html_main = "
		<div class='section_title'>
		  <h2>Bulk Award</h2>
	    </div>";

		switch($this->request['do'])
		{
			case "award":
				if($this->request['for'] == "award")
				{
					$this->award_award();
				}
				elseif($this->request['for'] == "member")
				{
					$this->award_member();
				}
				else
				{
					$this->main();
				}
				break;
			default:
				$this->main();
				break;
		}

		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->sendOutput();
	}

	private function main()
	{
		$this->registry->getClass('output')->html_main .= "
		<div class='acp-box'>
 		  <h3>Choose a Method...</h3>
		  <div class='tablerow1' style='padding:10px;' align='center'>
		  	<strong><a href='{$this->settings['base_url']}module=awards&section=bulkaward&do=award&for=award'>Give 'X' Members a Certain Award</a></strong><br />
			<br />
			or<br />
			<br />
			<strong><a href='{$this->settings['base_url']}module=awards&section=bulkaward&do=award&for=member'>Give 'X' Awards to a Member</a></strong>
		  </div>
		</div>";
	}

	private function award_award()
	{
		if($this->request['continue'])
		{
			if(!$this->request['awardees'])
			{
				$this->registry->getClass('output')->html_main .= "
				<div class='warning'>
				  <h4>Error!</h4>
				  You must enter atleast one member name or ID to continue.
				</div>
				<br />";
			}
			else
			{
				if(!$this->request['award'])
				{
					$this->registry->getClass('output')->html_main .= "
					<div class='warning'>
					  <h4>Error!</h4>
					  You must choose an award to give.
					</div>
					<br />";
				}
				else
				{
					$awardees      = array();
					$invalidAwdees = array();

					$expAwdes = explode(",", $this->request['awardees']);

					foreach($expAwdes as $val)
					{
						$this->DB->build(array(
												'select' => 'member_id',
												'from'   => 'members',
												'where'  => "`members_display_name` = '{$val}' || `member_id` = '{$val}'",
						));

						$this->DB->execute();
						$am = $this->DB->fetch();

						if($am['member_id'])
						{
							if($awardees[$am['member_id']] && $this->request['multiples'])
							{
								$awardees[$am['member_id']]++;
							}
							elseif(!$awardees[$am['member_id']])
							{
								$awardees[$am['member_id']] = 1;
							}
						}
						else
						{
							$invalidAwdees[$val] = 1;
						}
					}

					if(count($invalidAwdees) >= 1)
					{
						foreach($invalidAwdees as $v => $n)
						{
							if($invalid)
							{
								$comma = ", ";
							}

							$invalid .= "{$comma}{$v}";
						}

						$this->registry->getClass('output')->html_main .= "
						<div class='warning'>
						  <h4>Error!</h4>
						  The following member names/ID's were invalid:<br />
						  <br />
						  {$invalid}
						</div>
						<br />";
					}
					else
					{
						foreach($awardees as $mid => $times)
						{
							$range = range(1, $times);

							foreach($range as $num)
							{
								$this->registry->getClass('class_jawards')->giveAward($this->request['award'], $mid, $this->request['reason']);
							}
						}

						$this->registry->output->global_message = "Bulk Award Complete";
						$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . 'module=awards&section=bulkaward');
					}
				}
			}
		}

		$this->registry->getClass('output')->html_main .= "
		<form name='bulk_by_Award' method='post' action='{$this->settings['base_url']}module=awards&section=bulkaward&do=award&for=award&continue=1'>
		<div class='acp-box'>
 		  <h3>Bulk Award</h3>
		  <table width='100%' class='alternate_rows double_pad'>
			<tr>
			  <td>
			    <strong>Award to Give</strong>
				<div class='desctext'>Select an award all members above will recieve.</div>
			  </td>
			  <td>
			    " . $this->registry->getClass('class_jawards')->awardsMenu("award", $this->request['award']) . "
			  </td>
			</tr>
			<tr>
			  <td>
			    <strong>Notes</strong>
				<div class='desctext'>Enter a reason for why you are giving this award.
			  </td>
			  <td><textarea name='reason' rows='4' cols='30'>{$this->request['reason']}</textarea></td>
			</tr>
			<tr>
			  <td>
			    <strong>Award to Duplicates</strong>
				<div class='desctext'>Do you want the system to give awards to members which may be listed multiple times?</div>
			  </td>
			  <td>
			    <input type='checkbox' name='multiples' value='1' /> Check, if yes.
			  </td>
			</tr>
		    <tr>
			  <td width='35%'>
			    <strong>Members to Award</strong>
				<div class='desctext'>Enter a combinaton of display names and/or user ID's.<br />
				<br />
				<strong style='color:#FF0000;'>SEPARATE EACH WITH A COMMA</strong></div>
			  </td>
			  <td><textarea name='awardees' style='width:95%;' rows='10'>{$this->request['awardees']}</textarea></td>
			</tr>
		  </table>
		  <div class='acp-actionbar'>
		    <input type='submit' value=' Awards Members ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}&module=awards&section=bulkaward'>Cancel</a></strong>
	  	  </div>
		</div>
		</form>";
	}

	private function award_member()
	{
		$query = $this->DB->build(array(
								'select' => '*',
								'from'   => 'jlogica_awards',
		));

		$getAwards  = $this->DB->execute($query);

		if($this->request['continue'])
		{
			$m = IPSMember::load($this->request['membername'], NULL, 'displayname');

			if(!$m['member_id'])
			{
				$this->registry->getClass('output')->html_main .= "
				<div class='warning'>
				  <h4>Error!</h4>
				  The member name you entered was invalid
				</div>
				<br />";
			}
			else
			{
				$AIDS = array();

				while($aa = $this->DB->fetch($getAwards))
				{
					if($this->request['giveAward_' . $aa['id']])
					{
						$AIDS[$aa['id']] = 1;
					}
				}

				if(!count($AIDS))
				{
					$this->registry->getClass('output')->html_main .= "
					<div class='warning'>
					  <h4>Error!</h4>
					  You must select some awards to give
					</div>
					<br />";
				}
				else
				{
					foreach($AIDS as $awdID => $num)
					{
						$this->registry->getClass('class_jawards')->giveAward($awdID, $m['member_id'], $this->request["notes_" . $awdID]);
					}

					$this->registry->output->global_message = "Bulk Award Complete";
					$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . 'module=awards&section=bulkaward');
				}
			}
		}

		$this->DB->build(array(
								'select' => '*',
								'from'   => 'jlogica_awards',
		));

		$this->DB->execute();

		while($a = $this->DB->fetch())
		{
			if($this->request['giveAward_' . $a['id']])
			{
				$checked = " checked";
			}

			$rows .= "
			<tr>
			  <td style='text-align:center; width:5%;'><input type='checkbox' name='giveAward_{$a['id']}' value='1'{$checked} /></td>
			  <td style='text-align:center; width:20%;'><img src='{$this->settings['upload_url']}/jawards/{$a['icon']}' /></td>
			  <td style='width:25%;'><strong>{$a['name']}</strong></td>
			  <td style='width:75%;'><textarea name='notes_{$a['id']}' rows='4' cols='30'>" . $this->request["notes_" . $a['id']] . "</textarea></td>
			</tr>";

			unset($checked);
		}

		$this->registry->getClass('output')->html_main .= "
		<form name='bulk_by_Award' method='post' action='{$this->settings['base_url']}module=awards&section=bulkaward&do=award&for=member&continue=1'>
		<div class='acp-box'>
 		  <h3>Bulk Award</h3>
		  <table width='100%' class='alternate_rows double_pad'>
		    <tr>
			  <td width='35%'>
			    <strong>Member to Award</strong>
				<div class='desctext'>Enter the display name of the member who will be awarded.
			  </td>
			  <td><input type='text' name='membername' value='{$this->request['membername']}' size='35' /></td>
			</tr>
		  </table>
		</div>
		<br />
		<div class='acp-box'>
 		  <h3>Choose Awards</h3>
		  <table width='100%' class='alternate_rows double_pad'>
		    {$rows}
		  </table>
		</div>
		<br />
		<div class='acp-actionbar'>
		  <input type='submit' value=' Give Awards ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}&module=awards&section=bulkaward'>Cancel</a></strong>
	  	</div>
		</form>";
	}
}
