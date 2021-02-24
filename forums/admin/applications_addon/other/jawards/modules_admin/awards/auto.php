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

class admin_jawards_awards_auto extends ipsCommand
{
	private $output;

	function doExecute(ipsRegistry $registry)
	{
		/* Load Skin and Lang */
		$this->html               = $this->registry->output->loadTemplate( 'cp_skin_auto', 'jawards' );
		$this->html->form_code    = 'module=awards&amp;section=auto';
		$this->html->form_code_js = 'module=awards&section=auto';
		$this->registry->class_localization->loadLanguageFile( array( 'admin_manage' ), 'jawards' );

		switch($this->request['do'])
		{
			case "add":
				$this->add_step_one();
				break;
			case "edit":
				$this->form('edit');
				break;
			case "delete":
				$this->delete();
				break;
			default:
				$this->main();
				break;
		}

		$this->registry->getClass('output')->html_main = $this->registry->getClass('output')->global_template->global_frame_wrapper();
		$this->registry->getClass('output')->html_main .= $this->output;
		$this->registry->getClass('output')->sendOutput();
	}

	private function main()
	{
		$data = array();
		$this->DB->build( array(	'select'	=> '*',
									'from'		=> 'jlogica_awards_auto_awards',
									'order'		=> 'type, placement',
							));
		$loadFuncs = $this->DB->execute();
		while( $f = $this->DB->fetch( $loadFuncs ) )
		{
			$a = $this->DB-> buildAndFetch( array(	'select' => '*',
   													'from'   => 'jlogica_awards',
													'where'  => 'id = ' . $f['award_id'],
												));
			$f['award'] = $a;
			$data[] = $f;
		}
		$this->registry->output->html .= $this->html->main( $data );
	}

	private function add_step_one()
	{
		if(isset( $this->request['continue'] ) && $this->request['type'] )
		{
			$this->form();
			return;
		}

		$opendir = @opendir(IPSLib::getAppDir('jawards') . '/auto_awarding/');
		$data = array();
		if( $opendir )
		{
			while( $file = readdir( $opendir ) )
			{
				if( substr( $file, -4 ) == ".php" )
				{
					include_once(IPSLib::getAppDir('jawards') . '/auto_awarding/'  . $file );
					$class  = "auto_award_" . str_replace( ".php", "", $file );
					if( class_exists( $class ) )
					{
						$call   = new $class( ipsRegistry::instance() );
						$cfg = $call->config();
						if( $cfg['name_human'] AND $cfg['name_cpu'])
						{
							$data[] = $cfg;
						}
					}
				}
			}
		}
		else
		{
			$this->registry->output->showError( $this->lang->words['ja_dirwrite'] );
		}
		$this->registry->output->html .= $this->html->step1( $data );
	}

	private function form( $type = 'add' )
	{
		if( $type == "edit" )
		{
			$this->DB->build( array(	'select' => '*',
										'from'   => 'jlogica_awards_auto_awards',
										'where'  => 'inst_id = ' . $this->request['id'],
							));
			$this->DB->execute();
			if( ! $this->DB->getTotalRows() )
			{
				$this->registry->output->showError( 'The URL you have followed is invalid' );
			}
			$hook     = $this->DB->fetch();
			$hookData = json_decode( $hook['data'], TRUE);
			$load = $hook['type'];
		}
		else
		{
			$load = $this->request['type'];
		}

		$include = IPSLib::getAppDir('jawards') . '/auto_awarding/' . $load . ".php";
		@require_once( $include );
		$class  = "auto_award_" . $load;
		$call   = new $class( ipsRegistry::instance() );
		$cfg = $call->config();

		if( isset( $this->request['save'] ) )
		{
			if( ! $this->request['function_title'] )
			{
				$this->output .= "
				<div class='warning' style='margin-bottom:15px;'>
				  <h4><img src={$this->settings['skin_acp_url']}/images/icons/bullet_error.png' /> Error!</h4>
				  Please enter a title before continuing!
				</div>";
			}
			else
			{
				if( ! $this->request['award'] )
				{
					$this->output .= "
					<div class='warning' style='margin-bottom:15px;'>
					  <h4><img src={$this->settings['skin_acp_url']}/_newimages/icons/bullet_error.png' /> Error!</h4>
					  Please select an award before continuing!
					</div>";
				}
				else
				{
					$errors = array();
					$data   = array();
					$key	= '';

					foreach( $cfg['fields'] as $i )
					{
						$v = $this->request[$i['name']];
						if( ! $v and $i['required'] )
						{
							$errors[] = $i['label'];
							continue;
						}
						$data[$i['name']] = $v;
						if( $i['key'] and $cfg['sequence'] == 'key' and empty( $key ) )
						{
							$key = $v;
						}
					}
					if( $cfg['sequence'] == 'drag' )
					{
						$key = $this->request['key'];
					}
					if( count( $errors ) )
					{
						$this->output .= "<div class='warning' style='margin-bottom:15px;'>";
						foreach( $errors as $e )
						{
							$this->output .= "<h4><img src={$this->settings['skin_acp_url']}/images/icons/bullet_error.png' /> Error!</h4> You must enter something for field '{$errors}'";
						}
						$this->output .= "</div>";
					}
					else
					{
						$title        = $this->request['function_title'];
						$award        = $this->request['award'];
						$hookType     = $this->request['type'] ? $this->request['type'] : $load;
						$encoded_data = json_encode( $data );
						$notes        = $this->request['notes'];
/*
echo '<pre>';
echo '<hr>';
echo 'Config<br>';
echo '<hr>';
print_r( $cfg );
echo '<hr>';
echo 'Request<br>';
echo '<hr>';
print_r( $this->request );
echo '<hr>';
echo 'Data<br>';
echo '<hr>';
print_r( $data );
echo '<hr>';
echo 'Encoded Data<br>';
echo '<hr>';
print_r( $encoded_data );
echo '<hr>';
echo 'Settings<br>';
echo '<hr>';
print_r( $this->settings );
echo '</pre>';
exit;
*/
						$queryData = array(	'award_id'	=> $award,
											'title'		=> $title,
											'type'		=> $hookType,
											'data'		=> $encoded_data,
											'notes'		=> $notes,
											'placement'	=> $key,
											);

						if( $type == "add" )
						{
							$this->DB->insert( 'jlogica_awards_auto_awards', $queryData );
						}
						elseif( $type == "edit" )
						{
							$this->DB->update( 'jlogica_awards_auto_awards', $queryData, "inst_id = '{$this->request['id']}'" );
						}

						$this->registry->output->global_message = "Auto-Award Function Saved";
						$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=auto");
					}
				}
			}
		}

		if( ! $cfg['fields'] OR ! count( $cfg['fields'] ) )
		{
			$this->registry->output->showError('The configuration file for the auto-award function you have choosen is invalid. Make sure it includes a variable called $cfg[\'fields\'].');
		}

		foreach( $cfg['fields'] as $info )
		{
			$label = $info['label'];
			$type = $info['type'];

			if( $hookData[$info['name']] )
			{
				$value = $hookData[$info['name']];
			}

			if( $this->request[ $info['name']] )
			{
				$value = $this->request[$info['name']];
			}

			if( $type == "drop" && $info['options'] )
			{
				$field = $this->registry->output->formDropdown( $info['name'], $info['options'], $value );
			}
			elseif( $type == "multi" && $info['options'] )
			{
				if( ! is_array( $value ) )
				{
					$value = explode( ",", $value );
				}
				$field = $this->registry->output->formMultiDropdown( $info['name'] . '[]', $info['options'], $value );
			}
			elseif( $type == "yesno" or $type == "yn" )
			{
				$field = $this->registry->output->formYesNo( $info['name'], $value );
			}
			else
			{
				$field = $this->registry->output->formInput( $info['name'], IPSText::parseCleanValue( $value ) );
			}

			if( $info['description'])
			{
				$desc = "<br />{$info['description']}";
			}
			$options .= "
			<tr>
			  <td><strong>{$label}</strong>{$desc}</td>
			  <td>{$field}</td>
			</tr>";
		}

		$title_value = $hook[ 'title' ];
		if( $this->request['function_title'] )
		{
			$title_value = $this->request['function_title'];
		}

		$notes_value = $hook['notes'];
		if( $this->request['notes'] )
		{
			$notes_value = $this->request['notes'];
		}

		$award_value = $hook['award_id'];
		if( $this->request['award'] )
		{
			$award_value = $this->request['award'];
		}

		$award_placement = $hook['placement'];
		if( $this->request['placement'] )
		{
			$award_value = $this->request['placement'];
		}

		$awards_opt = $this->registry->getClass('class_jawards')->awardsMenu( "award", $award_value );

		if($this->request['do'] == "edit")
		{
			$submit = "{$this->settings['base_url']}module=awards&section=auto&do=edit&id={$this->request['id']}&save";
		}
		else
		{
			$submit = "{$this->settings['base_url']}module=awards&section=auto&do=add&continue&save";
		}

		$this->output .= "
		<form name='autoAwardFuncSettings' method='post' action='{$submit}'>
		<div class='acp-box'>
		  <h3>Managing Auto-Award Function</h3>
		  <table class='ipsTable'>
		    <tr>
			  <td width='35%'>
			    <strong>Function Title</strong><br />
				This is just to allow you to easily reffer to this specific function.
			  </td>
			  <td><input type='text' name='function_title' value='{$title_value}' style='width:200px;' /></td>
			</tr>
			<tr>
			  <td width='35%'>
			    <strong>Notes</strong><br />
				A short message for why the user would receive this award.
			  </td>
			  <td><input type='text' name='notes' value='{$notes_value}' style='width:400px;' /></td>
			</tr>
			<tr>
			  <td width='35%'>
			    <strong>Award to Give</strong><br />
				This is the award which will be given.
			  </td>
			  <td>{$awards_opt}</td>
			</tr>
			<tr>
			  <th colspan='2'>Other Options</th>
			</tr>
			{$options}
		  </table>
	      <div class='acp-actionbar' align='center'><input type='submit' value=' Save ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}module=awards&section=auto'>Cancel</a></strong></div>
		</div>
		<input type='hidden' name='type' value='{$this->request['type']}' />
		<input type='hidden' name='key' value='{$this->request['key']}' />
		</form>";

		if($cfg['author'])
		{
			if($cfg['web'])
			{
				$auth = "<a href='{$cfg['web']}' target='_blank'>{$cfg['author']}</a>";
			}
			else
			{
				$auth = $cfg['author'];
			}

			$this->output .= "
			<div style='margin-top:10px;'>
			  This auto-award function was created by {$auth}.
			</div>";
		}
	}

	private function delete()
	{
		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'jlogica_awards_auto_awards',
							   'where'  => 'inst_id = ' . $this->request['id'],
		));

		$this->DB->execute();

		if(!$this->DB->getTotalRows())
		{
			$this->registry->output->showError('The URL you have followed is invalid');
		}

		$f = $this->DB->fetch();

		if(isset($this->request['continue']) && $this->request['deleteFunc'] == $f['inst_id'])
		{
			$this->DB->delete('jlogica_awards_auto_awards', 'inst_id = ' . $f['inst_id']);

			if($this->request['deleteAwards'])
			{
				$this->DB->delete('jlogica_awards_awarded', 'auto_award_id = ' . $f['inst_id']);
			}

			$this->registry->output->global_message = "Auto-Award Function Deleted";
			$this->registry->output->silentRedirectWithMessage($this->settings['base_url'] . "module=awards&section=auto");
		}

		$this->DB->build(array(
							   'select' => '*',
							   'from'   => 'jlogica_awards_awarded',
							   'where'  => 'auto_award_id = ' . $f['inst_id'],
		));

		$this->DB->execute();

		if($this->DB->getTotalRows())
		{
			$deleteAwards = "
			<div class='tablerow1' style='padding:10px;'>
			  Awards have been given by this auto-award function, do you want to delete those too?<br />
			  <br />
			  Check if yes: <input type='checkbox' name='deleteAwards' value='1' />
		    </div>";
		}

		$this->output .= "
		<form name='deleteAward' method='post' action='{$this->settings['base_url']}module=awards&section=auto&do=delete&id={$this->request['id']}&continue'>
		<div class='acp-box'>
 		  <h3>Deleting Auto-Award Function: {$f['title']}</h3>
	      <div class='tablerow1' style='padding:10px;'>
			Are you sure that you would like to delete this auto-award function? This cannot be undone.
		  </div>
		  {$deleteAwards}
		  <div class='acp-actionbar'>
			<input type='hidden' name='deleteFunc' value='{$f['inst_id']}' />
			<input type='submit' value=' Yes, Delete ' class='realbutton' /> or <strong><a href='{$this->settings['base_url']}'>Cancel</a></strong>
	  	  </div>
        </div>
		</form>";
	}
}
