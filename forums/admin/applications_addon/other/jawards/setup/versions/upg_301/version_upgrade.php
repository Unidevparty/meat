<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}
function RemoveDir( $sDir )
{
	if( is_dir( $sDir ) )
	{
		$sDir = rtrim( $sDir, '/' );
		$oDir = dir( $sDir );
		while( ( $sFile = $oDir->read() ) !== false )
		{
			if( $sFile != '.' && $sFile != '..' )
			{
				( ! is_link( "{$sDir}/{$sFile}" ) && is_dir( "{$sDir}/{$sFile}" ) ) ? RemoveDir( "{$sDir}/{$sFile}" ) : unlink( "{$sDir}/{$sFile}" );
			}
		}
		$oDir->close();
		rmdir( $sDir );
		return true;
	}
	return false;
}

class version_upgrade
{
	public function renameTable( $old, $new )
	{
		$this->DB->build( array(	'select' => '*',
							   		'from'   => $old
						));
		$exec = $this->DB->execute();
		if( $this->DB->getTotalRows( $exec ) )
		{
			while( $m = $this->DB->fetch( $exec ) )
			{
				$this->DB->insert( $new, $m );
			}
		}
		$this->DB->dropTable( $old );
	}

	public function doExecute(ipsRegistry $registry)
	{
		$this->registry =  $registry;
		$this->DB       =  $this->registry->DB();

		// Purge old directories
		$award_upgrades = IPS_ROOT_PATH . 'applications_addon/other/awards/setup/versions/';
		RemoveDir( $award_upgrades . 'upg_20100' );
		RemoveDir( $award_upgrades . 'upg_2020010' );
		RemoveDir( $award_upgrades . 'upg_20202000' );
		RemoveDir( $award_upgrades . 'upg_2020400010' );
		@unlink( IPS_ROOT_PATH . 'applications_addon/other/awards/xml/hook.xml' );

		// Rename Tables
		$this->renameTable( 'inv_awards',				'jlogica_awards' );
		$this->renameTable( 'inv_awards_auto_awards',	'jlogica_awards_auto_awards' );
		$this->renameTable( 'inv_awards_awarded',		'jlogica_awards_awarded' );
		$this->renameTable( 'inv_awards_cats',			'jlogica_awards_cats' );

		// Remove old hooks
		$hooks = array( 'inv_awards_post_view', 'inv_awards_public_awarding', 'inv_awards_auto_award_posting' );
		foreach( $hooks as $hook )
		{
			$hook = $this->DB->buildAndFetch( array( 'select' => '*', 'from' => 'core_hooks', 'where' => "hook_key='" . $hook . "'" ) );

			if ( ! $hook['hook_id'] )
			{
				continue;
			}

			$this->DB->delete( 'core_hooks', "hook_id={$hook['hook_id']}" );

			/* Get associated files */
			$this->DB->build( array( 'select' => 'hook_file_stored', 'from' => 'core_hooks_files', 'where' => 'hook_hook_id=' . $hook['hook_id'] ) );
			$this->DB->execute();

			while ( $r = $this->DB->fetch() )
			{
				@unlink( IPS_HOOKS_PATH . $r['hook_file_stored'] );
			}

			/* Delete hook file entries */
			$this->DB->delete( 'core_hooks_files', "hook_hook_id={$hook['hook_id']}" );
		}
		return true;
	}

	public function fetchOutput()
	{
		return "";
	}
}
