<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class version_upgrade
{
	public function removeOldHooks( $hooks = array() )
	{
		$_hookIds	= array();
		$_total		= 0;

		/* Get hook records */
		$this->DB->build( array( 'select' => 'hook_id', 'from' => 'core_hooks', 'where' => "hook_key IN('" . implode( "','", $hooks ) . "')" ) );
		$this->DB->execute();

		while( $r = $this->DB->fetch() )
		{
			$_hookIds[]	= $r['hook_id'];
		}

		/* Remove associated files */
		if( count($_hookIds) )
		{
			$this->DB->build( array( 'select' => 'hook_file_stored', 'from' => 'core_hooks_files', 'where' => 'hook_hook_id IN(' . implode( ',', $_hookIds ) . ')' ) );
			$this->DB->execute();

			while( $r = $this->DB->fetch() )
			{
				@unlink( IPS_HOOKS_PATH . $r['hook_file_stored'] );
			}

			/* Remove hook records */
			$this->DB->delete( 'core_hooks_files', 'hook_hook_id IN(' . implode( ',', $_hookIds ) . ')' );
			$this->DB->delete( 'core_hooks', 'hook_id IN(' . implode( ',', $_hookIds ) . ')' );

			$_total++;
		}

		/* Message */
		$this->registry->output->addMessage("{$_total} outdated hook(s) uninstalled....");

		/* Next Page */
		$this->request['workact'] = '';
	}

	public function doExecute(ipsRegistry $registry)
	{
		$this->registry    =  $registry;
		$this->DB          =  $this->registry->DB();
		$this->settings    =& $this->registry->fetchSettings();
        $this->request     =& $this->registry->fetchRequest();
        $this->cache       =  $this->registry->cache();
        $this->caches      =& $this->registry->cache()->fetchCaches();

        //-----------------------------------------
        // What are we doing?
        //-----------------------------------------

        switch( $this->request['workact'] )
        {
                default:
                case 'step1':
                        $this->step1();
                        $this->request['workact']       = 'step2';	// Next Step id
                break;

                case 'step2':
                        $this->step2();
                        $this->request['workact']       = '';		// Finished empty next step
                break;
        }

        //-----------------------------------------
        // Return false if there's more to do
        //-----------------------------------------

        if ( $this->request['workact'] )
        {
                return false;
        }
        else
        {
                return true;
        }
	}

	private function step1()
	{
		// Remove old artifacts
		$this->DB->delete( 'core_hooks_files', "hook_file_real='awardsSigView.php'" );
		$this->DB->delete( 'core_hooks_files', "hook_file_real='awardsPostView.php'" );
		$award_path = IPS_ROOT_PATH . 'applications_addon/other/jawards/';
		@unlink( $award_path . 'xml/hooks/hook_autoAward_byPosts.xml' );
		@unlink( $award_path . 'xml/hooks/hook_publicAwarding.xml' );
		@unlink( $award_path . 'xml/hooks/hook_topicView.xml' );
		$this->registry->output->addMessage( "Removed old files" );
	}
	private function step2()
	{
		// Remove old hooks
		$hooks = array( 'jlogica_awards_public_awarding', 'jlogica_awards_aap', 'jlogica_awards_post_view' );
		$this->removeOldHooks( $hooks );
		$this->registry->output->addMessage( "Removed old Hooks" );
	}

	public function fetchOutput()
	{
		return "";
	}
}
