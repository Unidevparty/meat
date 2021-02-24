<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class public_jawards_ajax_awards extends ipsAjaxCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		$return = '';
		# Verify we have a valid request
		if($this->request['do'] == "getAwarded")
		{
			# Get the language
			$this->lang->loadLanguageFile(array("public_awards"), 'jawards');

			# Run the query
			$this->DB->build(array(
									'select' => 'a.user_id',
									'from'   => array('jlogica_awards_awarded' => 'a'),
									'add_join' => array( array(	'select' => 'm.member_id,m.members_seo_name,m.members_display_name',
																'from'   => array('members' => 'm'),
																'where'  => 'm.member_id = a.user_id',
															),
														array(	'select' => 'g.prefix,g.suffix',
																'from'   => array( 'groups' => 'g' ),
																'where'  => 'm.member_group_id = g.g_id',
															),
														),
									'where'  => "`award_id` = '{$this->request['id']}' && `is_active` = '1' && `approved` = '1'",
			));

			$res = $this->DB->execute();

			# Get the awarded users
			$AWARDED = array();
			$num = 0;
			while($a = $this->DB->fetch($res))
			{
				if( ! isset( $AWARDED[$a['member_id']] ) )
				{
					$url     = ipsRegistry::getClass('output')->buildSEOUrl( 'showuser=' . $a['member_id'] . '&tab=jawards', 'public', $a['members_seo_name'], 'showuser');
					$AWARDED[$a['member_id']] =  "<a href='{$url}'>{$a['prefix']}{$a['members_display_name']}{$a['suffix']}</a>";
				}
			}
		}
		$this->returnHTML( $this->registry->output->getTemplate('jawards')->hoverCard( $AWARDED ) );
	}
}
