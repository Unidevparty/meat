<?php
if(!defined('IN_ACP'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded 'admin.php'.";
	exit();
}

class admin_jawards_system_cabinet extends ipsCommand
{
	public function doExecute(ipsRegistry $registry)
	{
		if( $this->request['do'] == 'save' )
		{
			// do the save and redirect saved message back to display!
			$classToLoad   = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/class_public_permissions.php', 'classPublicPermissions' );
            $permissions    = new $classToLoad( ipsRegistry::instance() );
            $permissions->savePermMatrix( $this->request['perms'], 1, 'Cabinet' );
			$url      = $this->settings['base_url'] . 'module=system&section=cabinet';
			$this->registry->output->global_message = "Cabinet Permissions Updated";
			$this->registry->output->silentRedirectWithMessage($url);
		}
		$this->registry->class_localization->loadLanguageFile( array( 'admin_manage' ), 'jawards' );
		$this->registry->getClass('output')->html_main .= $this->registry->getClass('output')->global_template->global_frame_wrapper();
        $classToLoad   = IPSLib::loadLibrary( IPS_ROOT_PATH . 'sources/classes/class_public_permissions.php', 'classPublicPermissions' );
        $permissions    = new $classToLoad( ipsRegistry::instance() );
		$r = ipsRegistry::DB()->buildAndFetch( array( 'select'   => 'p.*',
				 							  	 	  'from'     => array( 'permission_index' => 'p' ),
													  'where'    => "app='jawards' AND perm_type='cabinet'",
											)      );
        $matrix = $permissions->adminPermMatrix( 'Cabinet', $r );
		$this->registry->getClass('output')->html_main .= "<div class='section_title'><h2>{$this->lang->words['ja_manage_cabinet']}</h2></div>";
		$this->registry->getClass('output')->html_main .= "<form id='adminform' name='adminform' method='post' action='{$this->settings['base_url']}module=system&section=cabinet&do=save' enctype='multipart/form-data'>\n";
		$this->registry->getClass('output')->html_main .= $matrix;
		$this->registry->getClass('output')->html_main .= "
		<div class='acp-actionbar'>
			<input type='submit' value='{$this->lang->words['ja_submit']}' class='realbutton' />
		</div>
</form>
";
		$this->registry->getClass('output')->sendOutput();
	}
}
