<?php
/*
 * Product Title:		Awards Management System
 * Product Version:		3.0.23
 * Author:				InvisionHQ
 * Website:				bbcode.it
 * Website URL:			http://bbcode.it/
 * Email:				reficul@lamoneta.it
 * Copyright©:			InvisionHQ - Gabriele Venturini - bbcode.it - 2012/2013
 */
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class profile_jawards extends profile_plugin_parent
{
	public function return_html_block($member=array())
	{
		# language!
		$this->registry->class_localization->loadLanguageFile( array( "public_awards" ), "jawards" );

		# variables!
		$content = "";

		# Get the awards!
		$this->DB->build( array(	'select'   => 'aa.*',
							   		'from'     => array( 'jlogica_awards_awarded' => 'aa' ),
							   		'add_join' => array(	array(	'select' => 'a.*, a.desc AS award_desc',
														 			'from'   => array( 'jlogica_awards' => 'a' ),
														  			'where'  => 'a.id = aa.award_id',
																),
															array(	'select' => 'c.title',
																	'from'   => array( 'jlogica_awards_cats' => 'c' ),
																	'where'  => 'c.cat_id = a.parent',
																),
							   							),
							   		'where'    => "aa.user_id = {$member['member_id']} AND aa.is_active = 1 AND c.frontend = 1",
							   		'order'    => 'date DESC',
							));

		$getAwards = $this->DB->execute();

		$awardList = array();
		$awardList[] = 0;
		if( $this->DB->getTotalRows( $getAwards ) )
		{
			$ct = 0;
			while( $a = $this->DB->fetch( $getAwards ) )
			{
				$awardList[] = $a['award_id'];
				if( $ct & 1 )
				{
					$num = 1;
				}
				else
				{
					$num = 2;
				}

				#$a['date'] = gmdate($this->setting['time_use_relative_format'], $a['date'] + $this->registry->class_localization->getTimeOffset());
				$a['date'] = $this->registry->getClass('class_localization')->getDate($a['date'], 'RELATIVE');

				if($a['awarded_by'])
				{
					$md            = IPSMember::load($a['awarded_by']);
					$a['given_by'] = IPSMember::makeProfileLink($md['members_display_name'], $md['member_id'], $md['members_seo_title']);
				}

				if( ( $a['awarded_by'] == $this->memberData['member_id'] AND $this->memberData['g_jlogica_awards_can_remove'] )	OR
					( $this->memberData['member_id'] AND $a['awarded_by'] != $this->memberData['member_id'] AND ! $this->settings['japa_restricted_removing'] )	OR
					$this->memberData['g_access_cp'] )
				{
					$a['remove_option'] = "<a href='{$this->settings['base_url']}app=jawards&do=remove&id={$a['row_id']}'>{$this->lang->words['remove_award']}</a>";
				}

				$show = 0;

				if( $a['approved'] )
				{
					$show = 1;
				}
				elseif( ! $a['approved'] && $this->memberData['member_id'] == $a['awarded_by'] )
				{
					$show               = 1;
					$a['remove_option'] = "<em>{$this->lang->words['pending_approval']}</em>";
				}

				if( $show )
				{
					IPSText::getTextClass('bbcode')->parsing_section = 'global';
					IPSText::getTextClass('bbcode')->parse_smilies = TRUE;
					IPSText::getTextClass('bbcode')->parse_bbcode = TRUE;
					IPSText::getTextClass('bbcode')->parse_html = TRUE;
					IPSText::getTextClass('bbcode')->parse_nl2br = FALSE;
					IPSText::getTextClass('bbcode')->bypass_badwords = FALSE;
					$a['longdesc'] = IPSText::getTextClass('bbcode')->preDisplayParse( $a['longdesc'] );
					$rows .= $this->registry->getClass('output')->getTemplate('jawards')->profile_award_row($a, $this->settings['upload_url']);
				}
				$ct++;
			}
			$content = $this->registry->getClass('output')->getTemplate('jawards')->profile_awards_block( $rows );
		}
		else
		{
			$content = $this->registry->getClass('output')->getTemplate('jawards')->profile_noawards();
		}

		if( $this->settings['jacore_showmissing'] )
		{
			$awardList = implode( ',', $awardList );
			$this->DB->build( array(	'select'   => 'a.*',
					   		'from'     => array( 'jlogica_awards' => 'a' ),
					   		'add_join' => array(	array(	'select' => 'c.title',
															'from'   => array( 'jlogica_awards_cats' => 'c' ),
															'where'  => 'c.cat_id = a.parent',
														),
					   							),
					   		'where'    => "c.frontend = 1 AND id NOT IN({$awardList})",
					   		'order'    => 'c.placement, a.placement',
					));
			IPSDebug::fireBug( 'info', array( $this->DB->fetchSqlString(), "getAwards SQL" ) );
			$getAwards = $this->DB->execute();
			$awards = array();
			while( $a = $this->DB->fetch( $getAwards ) )
			{
				IPSText::getTextClass('bbcode')->parsing_section = 'global';
				IPSText::getTextClass('bbcode')->parse_smilies = TRUE;
				IPSText::getTextClass('bbcode')->parse_bbcode = TRUE;
				IPSText::getTextClass('bbcode')->parse_html = TRUE;
				IPSText::getTextClass('bbcode')->parse_nl2br = FALSE;
				IPSText::getTextClass('bbcode')->bypass_badwords = FALSE;
				$a['longdesc'] = IPSText::getTextClass('bbcode')->preDisplayParse( $a['longdesc'] );
				$awards[] = $a;
			}
			$content .= $this->registry->getClass('output')->getTemplate('jawards')->profile_unawarded( $awards, $this->settings['upload_url'] );
		}

		return( $content );
	}
}
