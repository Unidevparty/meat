<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class public_jawards_awards_awards extends ipsCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		# Get the language
		$this->lang->loadLanguageFile(array("public_awards"), 'jawards');

		# Title and navigation
		$this->registry->output->setTitle($this->lang->words['title']);
		$this->registry->output->addNavigation($this->lang->words['title'], "app=jawards&module=awards&section=awards");

		# Is this page enabled?
		if(!$this->settings['jacore_enable'])
		{
			# Is there a custom message to show?
			if($this->settings['jacore_disabled_msg'])
			{
				$msg = $this->settings['jacore_disabled_msg'];
			}
			else
			{
				$msg = $this->lang->words['error_listings_disabled'];
			}

			$this->registry->getClass('output')->showError( $msg, "11A1", false );
		}
		$row = ipsRegistry::DB()->buildAndFetch( array( 'select'   => 'p.*',
														'from'     => array( 'permission_index' => 'p' ),
														'where'    => "app='jawards' AND perm_type='cabinet'",
											)      );
		if( !$this->registry->permissions->check( 'view', $row ) )
		{
		        $this->registry->getClass('output')->showError( 'no_permission' );
		}

		switch($this->request['do'])
		{
			case "award":
				$this->publicAward();
				break;
			case "remove":
				$this->publicRemove();
				break;
			default:
				$this->showListings();
				break;
		}

		# Finish it off
        $this->registry->output->sendOutput();
	}

	private function publicAward()
	{
		$memberData = IPSMember::load($this->request['mid']);

		if(!$this->registry->getClass('class_jawards')->canPublic_Award($memberData) || $this->settings['japa_public_awarding'])
		{
			$this->registry->getClass('output')->showError($this->lang->words['error_no_permission'], "11A4", false);
		}

		$this->registry->output->addNavigation($this->lang->words['give_award_title'] . " " . $memberData['members_display_name'], "");

		if($this->request['submit'])
		{
			$approved = 1;

			if($this->settings['japa_approve_new'])
			{
				if(!$this->memberData['g_access_cp'])
				{
					if(strpos($this->settings['japa_approval_exempt'] . ",", $this->memberData['member_group_id'] . ",") === FALSE)
					{
						$approved = 0;
					}
				}
			}

			$this->DB->insert('jlogica_awards_awarded', array(
														  'award_id'   => $_POST['award'],
														  'user_id'    => $memberData['member_id'],
														  'awarded_by' => $this->memberData['member_id'],
														  'approved'   => $approved,
														  'is_active'  => 1,
														  'notes'      => $_POST['notes'],
														  'date'       => IPS_UNIX_TIME_NOW,
			));

			$url = ipsRegistry::getClass('output')->buildSEOUrl('showuser=' . $memberData['member_id'] . '&tab=awards', 'public', $memberData['members_seo_name'], 'showuser');

			$this->registry->getClass('output')->redirectScreen($this->lang->words['award_added'], $url);
		}

		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'jlogica_awards',
		));

		$this->DB->execute();

		while($a = $this->DB->fetch())
		{
			if(strpos($a['public_perms'], $this->memberData['member_group_id']) !== FALSE || !$a['public_perms'])
			{
				$options[$a['id']] = $a['name'];
			}
		}

		# Show Form
		$output .= $this->registry->output->getTemplate('jawards')->giveAward_form($memberData, $options);

		# Put it out there!
		$this->registry->getClass('output')->addContent($output);
	}

	private function publicRemove()
	{
		if(!$this->memberData['g_jlogica_awards_can_remove'] && !$this->memberData['g_access_cp'])
		{
			$this->registry->getClass('output')->showError($this->lang->words['error_no_permission'], "11A5", false);
		}

		if(!$this->memberData['g_access_cp'])
		{
			$furtherValidation = " && awarded_by = {$this->memberData['member_id']}";
		}

		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'jlogica_awards_awarded',
							   'where'  => 'row_id = ' . $this->request['id'] . $furtherValidation,
		));

		$this->DB->execute();

		if(!$this->DB->getTotalRows())
		{
			$this->registry->getClass('output')->showError($this->lang->words['error_invalid_request'], "11A6", false);
		}

		$a  = $this->DB->fetch();
		$am = IPSMember::load($a['user_id']);

		$approved = 1;
		if($this->settings['japa_approve_removals'])
		{
			if(!$this->memberData['g_access_cp'])
			{
				if(strpos($this->settings['japa_approval_exempt'] . ",", $this->memberData['member_group_id'] . ",") === FALSE)
				{
					$approved = 0;
				}
			}
		}

		if($approved)
		{
			$this->DB->delete('jlogica_awards_awarded', 'row_id = ' . $a['row_id']);
		}
		else
		{
			$this->DB->update('jlogica_awards_awarded', array(
														  'approved'  => '-1',
														  'is_active' => 0,
			), 'row_id = ' . $a['row_id']);
		}

		$url = ipsRegistry::getClass('output')->buildSEOUrl('showuser=' . $am['member_id'] . '&tab=awards', 'public', $am['members_seo_name'], 'showuser');

		$this->registry->getClass('output')->redirectScreen($this->lang->words['award_removed'], $url);
	}

	private function showListings()
	{
//		$this->registry->getClass('output')->showError( "Hello there kimosabe", "10JBAAM2" );

		# Get categories
		$this->DB->build( array(	'select'	=> '*',
									'from'		=> 'jlogica_awards_cats',
									'where'		=> "`visible` = '1' AND `frontend` = '1'",
									'order'		=> 'placement ASC',
						));
		$getCats = $this->DB->execute();
		$rows = '';
		while( $c = $this->DB->fetch( $getCats ) )
		{
			$rows .= $this->registry->output->getTemplate('jawards')->category_row_start( $c );
			$this->DB->build( array(	'select'	=> '*',
										'from'		=> array( 'jlogica_awards' => 'a' ),
										'where'		=> "visible = 1 AND parent = {$c['cat_id']}",
										'order'		=> 'placement',
								));
			$getAwards = $this->DB->execute();
			while( $a = $this->DB->fetch( $getAwards ) )
			{
				$aa = $this->DB->buildAndFetch( array(	'select'	=> 'COUNT(aa.award_id) AS award_count, MAX(aa.date) AS last_awarded',
														'from'		=> array( 'jlogica_awards_awarded' => 'aa' ),
														'where'		=> "aa.award_id = {$a['id']}",
											));
				$a['award_count']	= $aa['award_count'];
				$a['last_awarded']	= $aa['last_awarded'];
				IPSText::getTextClass('bbcode')->parsing_section = 'global';
				IPSText::getTextClass('bbcode')->parse_smilies = TRUE;
				IPSText::getTextClass('bbcode')->parse_bbcode = TRUE;
				IPSText::getTextClass('bbcode')->parse_html = TRUE;
				IPSText::getTextClass('bbcode')->parse_nl2br = FALSE;
				IPSText::getTextClass('bbcode')->bypass_badwords = FALSE;
				$a['longdesc'] = IPSText::getTextClass('bbcode')->preDisplayParse( $a['longdesc'] );
				$rows .= $this->registry->output->getTemplate('jawards')->listings_row( $c, $a, $this->settings['upload_url'] );
			}
			$rows .= $this->registry->output->getTemplate('jawards')->category_row_end( $c );
		}

		# Verify we have awards
		if( empty ( $rows ) )
		{
			$this->registry->getClass( 'output')->showError($this->lang->words['error_no_awards'], "10A2", false );
		}

		# AJAX URL
		$output .= $this->registry->output->getTemplate('jawards')->listings_container($rows, $this->member->form_hash );

		# Put it out there!
		$this->registry->getClass('output')->addContent($output);
	}

}
