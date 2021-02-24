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
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_awards_manage extends ipsCommand
{

	public function doExecute(ipsRegistry $registry)
	{
		/* Load Skin and Lang */
		$this->html               = $this->registry->output->loadTemplate( 'cp_skin_manage', 'jawards' );
		$this->html->form_code    = 'module=awards&amp;section=manage';
		$this->html->form_code_js = 'module=awards&section=manage';
		$this->registry->class_localization->loadLanguageFile( array( 'admin_manage' ), 'jawards' );
		IPSDebug::fireBug( 'info', array( $this->request, "Request" ) ) ;


		switch( $this->request['do'] )
		{
			case "delete":
				if( $this->request['what'] == "award" )
				{
					$this->deleteAward();
				}
				elseif( $this->request['what'] == "category" )
				{
					$this->deleteCategory();
				}
				else
				{
					$this->main();
				}
				break;

			case "add":
				$this->addAward();
				break;

			case "edit":
				if( $this->request['what'] == "award" )
				{
					$this->editAward();
				}
				elseif( $this->request['what'] == "category" )
				{
					$this->editCategory();
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

	private function reCache()
	{
		$this->registry->jawards_core->rebuildAllCaches();
	}

	private function main()
	{
		$data = array();
		$this->DB->build( array(	'select' => '*',
									'from'   => 'jlogica_awards_cats',
									'order'  => 'placement ASC',

						));
		$getCats = $this->DB->execute();
		while( $cat = $this->DB->fetch( $getCats ) )
		{
			$cat['Awards'] = array();
			# Get awards to show
			$this->DB->build( array(	'select'   => '*',
										'from'     => 'jlogica_awards',
										'where'    => 'parent = ' . $cat['cat_id'],
										'order'    => 'placement ASC',
							));
			$getAwards = $this->DB->execute();
			while( $award = $this->DB->fetch( $getAwards ) )
			{
				$cat['Awards'][] = $award;
			}
			$data[] = $cat;
		}
		$this->registry->output->html .= $this->html->main( $data );
	}

	private function deleteAward()
	{
		IPSDebug::addMessage( "Loaded JAwards.Manage.DeleteAward()" );
		# Get the award to show
		$this->DB->build(array( 'select'   => '*',
								'from'     => 'jlogica_awards',
								'where' => "id = {$this->request['id']}",
						));

		$this->DB->execute();
		$num = $this->DB->getTotalRows();

		# Make sure this award exists
		if( ! $num || $num > 1 )
		{
			$this->registry->output->showError( 'The URL you have followed is invalid' );
		}

		$data = $this->DB->fetch();

		# Veryify the user didn't skip a step
		if( $this->request['continue'] )
		{
			if( $_POST['deleteAward'] == $data['id'] )
			{
				// Delete icon
				if( $a['icon'] )
				{
					@unlink( $this->settings['upload_url'] . '/jawards/' . $data['icon']);
				}
				$this->DB->delete('jlogica_awards_awarded', 'award_id = ' . $data['id']);
				// Delete the entire award
				$this->DB->delete( 'jlogica_awards', 'id=' . $data['id'] );
				$this->reCache();
				// Redirect....
				$this->registry->output->global_message = "Award Deleted";
				$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=manage");
			}
			else
			{
				$this->registry->output->showError( 'Please make sure you are not skipping a step' );
			}
		}

		# Check if this award exists in Auto-Award functions
		$this->DB->build( array(	'select' => '*',
									'from'   => 'jlogica_awards_auto_awards',
									'where'  => 'award_id = ' . $data['id'],
							));
		$this->DB->execute();
		if($this->DB->getTotalRows())
		{
			$this->registry->output->showError( 'This award is being used in some Auto-Award Functions, it must be removed from those before you can delete it.' );
		}
		$this->registry->output->html .= $this->html->deleteAward( $data );
	}

	private function deleteCategory()
	{
		# Get the category
		$cat = intval( $this->request['id'] );
		if( $cat < 1 )
		{
			$this->registry->output->showError( 'The URL you have followed is invalid' );
		}
		if( $cat == 1 )
		{
			$this->registry->output->showError( 'The deafult category cannot be deleted' );
		}
		$this->DB->build( array(	'select'	=> '*',
									'from'		=> 'jlogica_awards_cats',
									'where'		=> "cat_id = {$cat}",
						));
		$this->DB->execute();
		$num = $this->DB->getTotalRows();

		# Make sure this award exists
		if( ! $num or $num > 1 )
		{
			$this->registry->output->showError( 'The URL you have followed is invalid' );
		}
		$c = $this->DB->fetch();

		# Check for awards
		$this->DB->build( array(	'select'	=> '*',
									'from'		=> 'jlogica_awards',
									'where'		=> "parent = {$cat}",
						));
		$awds      = $this->DB->execute();
		$numAwards = $this->DB->getTotalRows( $awds );

		# Is is time to delete?
		if( $this->request['continue'] && $this->request['deleteCategory'] == $cat )
		{
			# Verify all is OK to delete
			if( ! $this->request['awards_option'] && $numAwards )
			{
				$error = "You must choose an action to preform with the existing awards in this category.";
			}
			else
			{
				# Delete the category
				$this->DB->delete( 'jlogica_awards_cats', "`cat_id` = '$cat'" );
				# Do we delete or move the awards
				if( is_numeric( $this->request['awards_option'] ) )
				{
					$this->DB->update( 'jlogica_awards', array( 'parent' => $this->request['awards_option'] ), "parent='{$cat}'" );
				}
				else
				{
					$this->DB->delete( 'jlogica_awards', "parent='{$cat}'" );
				}
				$this->reCache();
				# Redirect
				$this->registry->output->global_message = "Category Deleted";
				$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=manage");
			}
		}
		$data['cat']		= $cat;
		$data['title']		= $c['title'];
		$data['numAwards']	= $numAwards	? $numAwards	: 0;
		$data['error']		= $error		? $error		: '';
		$this->registry->output->html .= $this->html->deleteCategory( $data );
	}

	private function addAward()
	{
		$publicPerms = '';
		if( count( $_POST['public_perms'] ) )
		{
			$publicPerms = implode( ',', $_POST['public_perms'] );
		}
		# Ready to add it?
		if( $this->request['continue'] )
		{
			$name = $this->request['award_name'];
			$desc = $this->request['award_desc'];
			$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
			$editor = new $classToLoad();
			$longdesc = $editor->process( $_POST['award_longdesc'] );
			IPSText::getTextClass( 'bbcode' )->bypass_badwords		= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_smilies		= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_html			= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_nl2br			= FALSE;
			IPSText::getTextClass( 'bbcode' )->parse_bbcode			= TRUE;
			IPSText::getTextClass( 'bbcode' )->parsing_section		= 'global';
			$longdesc = IPSText::getTextClass( 'bbcode' )->preDbParse( $longdesc );
			$icon = $_FILES['award_icon'];

			# Did you enter a name
			if( ! $name )
			{
				$this->registry->output->showError('You must enter a name for this award');
			}

			$i = $this->processIcon( $icon );


			# Run the query
			$this->DB->insert( 'jlogica_awards', array(
													'name'      	=> $name,
													'desc'      	=> $desc,
													'longdesc'		=> $longdesc,
													'icon'       	=> $i,
													'parent'      	=> $_POST['category'],
													'public_perms'	=> $publicPerms,
											));

			$this->reCache();
			# Redirect
			$this->registry->output->global_message = "Award Added";
			$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=manage");
		}
		$data = array();
		$data['name']		= '';
		$data['desc']		= '';
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
		$editor = new $classToLoad();
		$data['editor'] = $editor->show( 'award_longdesc' );
		$data['parent']		= 1;
		$data['perminfo']	= $publicPerms;
		$this->registry->output->html .= $this->html->editAward( $data, true );
	}

	private function editAward()
	{
		# Check the award was added
		$a = $this->DB->buildAndFetch( array(	'select' => '*',
												'from'   => 'jlogica_awards',
												'where'  => 'id = ' . $this->request['id'],
										));

		# Ready to add it?
		if( $this->request['continue'] )
		{
			$cat = $this->DB->buildAndFetch( array(	'select' => '*',
													'from'   => 'jlogica_awards_cats',
													'where'  => 'cat_id = ' . intval( $_POST['category'] ),
											));
			$name = $this->request['award_name'];
			$desc = $this->request['award_desc'];
			$descno = $this->request['award_descno'];
			$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
			$editor = new $classToLoad();
			$longdesc = $editor->process( $_POST['award_longdesc'] );
			IPSText::getTextClass( 'bbcode' )->bypass_badwords		= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_smilies		= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_html			= TRUE;
			IPSText::getTextClass( 'bbcode' )->parse_nl2br			= FALSE;
			IPSText::getTextClass( 'bbcode' )->parse_bbcode			= TRUE;
			IPSText::getTextClass( 'bbcode' )->parsing_section		= 'global';
			$longdesc = IPSText::getTextClass( 'bbcode' )->preDbParse( $longdesc );
			IPSDebug::fireBug( 'info', array( $longdesc, "After IPSText::getTextClass( 'bbcode' )->preDbParse()" ) ) ;
			$icon = $_FILES['award_icon'];

			# Did you enter a name
			if( ! $name )
			{
				$this->registry->output->showError( 'You must enter a name for this award' );
			}
			$i = $this->processIcon( $icon, $a['icon'] );
			$publicPerms = '';
			$badgePerms = '';
			if( $cat['location'] == '2' )	// Badge display
			{
				if( count( $_POST['badge_perms'] ) )
				{
					$badgePerms = implode( ',', $_POST['badge_perms'] );
				}
			}
			else
			{
				if( count( $_POST['public_perms'] ) )
				{
					$publicPerms = implode( ',', $_POST['public_perms'] );
				}
			}
			# Run the query
			$this->DB->update( 'jlogica_awards', array(	'`name`'		=> $name,
														'`desc`'		=> $desc,
														'`descno`'		=> $descno,
														'longdesc'		=> $longdesc,
														'`icon`'		=> $i,
														'parent'		=> $_POST['category'],
														'badge_perms'	=> $badgePerms,
														'public_perms'	=> $publicPerms
														),
												"id = {$a['id']}");

			$this->reCache();
			# Redirect
			$this->registry->output->global_message = "Changes Saved";
			$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=manage");
		}
		IPSText::getTextClass('bbcode')->parsing_section = 'global';
		IPSText::getTextClass('bbcode')->parse_smilies = TRUE;
		IPSText::getTextClass('bbcode')->parse_bbcode = TRUE;
		IPSText::getTextClass('bbcode')->parse_html = TRUE;
		IPSText::getTextClass('bbcode')->parse_nl2br = FALSE;
		IPSText::getTextClass('bbcode')->bypass_badwords = FALSE;
//		IPSText::getTextClass( 'bbcode' )->parsing_mgroup = $this->memberData['member_group_id'];
//		IPSText::getTextClass( 'bbcode' )->parsing_mgroup_others = $this->memberData['mgroup_others'];
		$text = html_entity_decode( $a['longdesc'], ENT_QUOTES );
		$text = IPSText::getTextClass('bbcode')->preEditParse( $text  );
		$classToLoad = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/editor/composite.php', 'classes_editor_composite' );
		$editor = new $classToLoad();
		$editor->setContent( $text );
		$a['editor'] = $editor->show( 'award_longdesc' );
		$this->registry->output->html .= $this->html->editAward( $a );
	}

	private function processIcon( $icon, $old = "" )
	{
		$uDir = $this->settings['upload_dir'] . '/jawards';
		if( $icon['name'] )
		{
			# Is your icon good?
			if(strpos($icon['type'], "image") === FALSE)
			{
				$this->registry->output->showError('You can only upload images for the icon');
			}

			$icon['name'] = str_replace(' ', '_', $icon['name']);

			# Just incase! ;)
			if(file_exists($uDir . '/' . $icon['name']))
			{
				$icon['name'] = rand(1,9999) . "_" . $icon['name'];
			}

			# Check for the directory
			if( ! is_dir( $uDir ) )
			{
				mkdir( $uDir );
			}

			# Upload the image
			if( ! @move_uploaded_file( $icon['tmp_name'], $uDir . '/' . $icon['name'] ) )
			{
				$this->registry->output->showError('Adding this award has failed, please try again. (a)');
			}

			# Delete the old icon
			if( $old )
			{
				@unlink( $uDir . '/' . $old );
			}
		}
		else
		{
			$icon['name'] = $old;
		}
		return( $icon['name'] );
	}

	private function editCategory()
	{
		if( $this->request['continue'] )
		{
			$fe = $this->request['frontend'] ? 1 : 0;
			$this->DB->update( 'jlogica_awards_cats',
								array(	'frontend'	=> $fe,
										'title'		=> $this->request['title'],
										'location'	=> $this->request['location'],
									),
								"cat_id = {$this->request['id']}");

			$this->reCache();
			# Redirect
			$this->registry->output->global_message = "Changes Saved";
			$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=manage");
		}

		$data = $this->DB->buildAndFetch( array(	'select' => '*',
													'from'   => 'jlogica_awards_cats',
													'where'  => 'cat_id = ' . $this->request['id'],
											));
		$this->registry->output->html .= $this->html->editCategory( $data );
	}

}
