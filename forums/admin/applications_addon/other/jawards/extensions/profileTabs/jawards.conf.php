<?php
if (!defined('IN_IPB'))
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}
$this->registry->class_localization->loadLanguageFile( array( 'public_awards' ), 'jawards' );

$CONFIG['plugin_name']	   = 'Awards';
$CONFIG['plugin_lang_bit'] = 'awards_profile_tab_title';
$CONFIG['plugin_key']	   = 'jawards';
$CONFIG['plugin_enabled']  = $this->settings['jacore_show_profile'] && $this->settings['jacore_enable'] ? 1 : 0;
$CONFIG['plugin_order']    = 5;
