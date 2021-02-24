<?php
/**
 * @brief		whowasonline Widget
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	whowasonline
 * @since		12 Jan 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\plugins\whowasonline\widgets;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * whowasonline Widget
 */
class _whowasonline extends \IPS\Widget
{
	/**
	 * @brief	Widget Key
	 */
	public $key = 'whowasonline';
	
	/**
	 * @brief	App
	 */
	public $app = '';
		
	/**
	 * @brief	Plugin
	 */
	public $plugin = '7';
	
	/**
	 * Initialise this widget
	 *
	 * @return void
	 */ 
	public function init()
	{
		$this->template( array( \IPS\Theme::i()->getTemplate( 'plugins', 'core', 'global' ), $this->key ) );
		parent::init();
	}
	
	/**
	 * Specify widget configuration
	 *
	 * @param	null|\IPS\Helpers\Form	$form	Form object
	 * @return	null|\IPS\Helpers\Form
	 */
	public function configuration( &$form=null )
	{
 		if ( $form === null )
		{
	 		$form = new \IPS\Helpers\Form;
 		}

		$form->add( new \IPS\Helpers\Form\Number( 'who_was_online_hours', isset( $this->configuration['who_was_online_hours'] ) ? $this->configuration['who_was_online_hours'] : 24, TRUE ) );
		$form->add( new \IPS\Helpers\Form\Select( 'who_was_online_who_can_see', isset( $this->configuration['who_was_online_who_can_see'] ) ? $this->configuration['who_was_online_who_can_see'] : '*', TRUE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true, 'unlimited' => '*', 'unlimitedLang' => 'everyone' ) ) );
		$form->add( new \IPS\Helpers\Form\YesNo( 'who_was_online_show_stat', isset( $this->configuration['who_was_online_show_stat'] ) ? $this->configuration['who_was_online_show_stat'] : 0, TRUE) );
		$form->add( new \IPS\Helpers\Form\Number( 'who_was_online_max_user', isset( $this->configuration['who_was_online_max_user'] ) ? $this->configuration['who_was_online_max_user'] : '', FALSE ) );
		$form->add( new \IPS\Helpers\Form\Select( 'who_was_online_ex_groups', isset( $this->configuration['who_was_online_ex_groups'] ) ? $this->configuration['who_was_online_ex_groups'] : '', FALSE, array( 'options' => \IPS\Member\Group::groups(), 'parse' => 'normal', 'multiple' => true ) ) );
		$form->add( new \IPS\Helpers\Form\Select( 'who_was_online_order_by', isset( $this->configuration['who_was_online_order_by'] ) ? $this->configuration['who_was_online_order_by'] : 'visit', TRUE, array( 'options' => array('name' => 'who_was_online_name', 'group' => 'who_was_online_group', 'visit' => 'who_was_online_last_visit'), 'multiple' => FALSE ) ) );
		$form->add( new \IPS\Helpers\Form\Radio( 'who_was_online_order_sort', isset( $this->configuration['who_was_online_order_sort'] ) ? $this->configuration['who_was_online_order_sort'] : 'desc', TRUE, array( 'options' => array('desc' => 'who_was_online_desc', 'asc' => 'who_was_online_asc') ) ) ); 		

 		return $form;
 	} 
 	
 	 /**
 	 * Ran before saving widget configuration
 	 *
 	 * @param	array	$values	Values from form
 	 * @return	array
 	 */
 	public function preConfig( $values )
 	{
 		return $values;
 	}

	/**
	 * Render a widget
	 *
	 * @return	string
	 */
	public function render()
	{
		$this->configuration['who_was_online_hours'] 				= isset($this->configuration['who_was_online_hours']) ?$this->configuration['who_was_online_hours']: 24;
		$this->configuration['who_was_online_who_can_see'] 			= isset($this->configuration['who_was_online_who_can_see']) ?$this->configuration['who_was_online_who_can_see']: '*';
		$this->configuration['who_was_online_max_user'] 			= isset($this->configuration['who_was_online_max_user']) ?$this->configuration['who_was_online_max_user']: '';
		$this->configuration['who_was_online_ex_groups'] 			= isset($this->configuration['who_was_online_ex_groups']) ?$this->configuration['who_was_online_ex_groups']: array();
		$this->configuration['who_was_online_order_by'] 			= isset($this->configuration['who_was_online_order_by']) ?$this->configuration['who_was_online_order_by']: 'visit';
		$this->configuration['who_was_online_order_sort'] 			= isset($this->configuration['who_was_online_order_sort']) ?$this->configuration['who_was_online_order_sort']: 'desc';
		$this->configuration['who_was_online_show_stat'] 			= isset($this->configuration['who_was_online_show_stat']) ?$this->configuration['who_was_online_show_stat']: 0;

		if ( $this->configuration['who_was_online_who_can_see'] != '*' && !\IPS\Member::loggedIn()->inGroup( $this->configuration['who_was_online_who_can_see'], TRUE ) ) {
			return "";
		}

		switch($this->configuration['who_was_online_order_by']) {
			case 'name':
				$order_by = 'name';
			break;
			case 'group':
				$order_by = 'member_group_id';
			break;
				$order_by = 'last_activity';
			break;
			case 'visit':
			default:
				$order_by = 'last_activity';
			break;
		}

		$order_type = $this->configuration['who_was_online_order_sort'] == 'desc' ? 'DESC' : 'ASC';

		$where = array(
			array( 'last_activity>' . \IPS\DateTime::create()->createFromFormat('Y-m-d|', \IPS\DateTime::create()->format( 'Y-m-d' ) )->getTimeStamp())
		);

		if(count($this->configuration['who_was_online_ex_groups'])) {
			$where[] = array( 'member_group_id NOT IN(' . implode(',', $this->configuration['who_was_online_ex_groups']) . ')' );
		}

		$members = iterator_to_array( \IPS\Db::i()->select( array( 'member_id', 'name', 'members_seo_name', 'member_group_id' ), 'core_members', $where, "{$order_by} {$order_type}" )->setKeyField( 'member_id' ) );

		$memberCount = count($members);

		if(!isset(\IPS\Data\Store::i()->who_was_online_stat_count) || $memberCount > \IPS\Data\Store::i()->who_was_online_stat_count) {
			\IPS\Data\Store::i()->who_was_online_stat_count = $memberCount;
			\IPS\Data\Store::i()->who_was_online_stat_time = \IPS\DateTime::create()->getTimeStamp();
		}

		$stat = false;
		if($this->configuration['who_was_online_show_stat']) {
			$stat = \IPS\Member::loggedIn()->language()->addToStack('who_was_online_stat', false, array( 'sprintf' => array($this->configuration['who_was_online_hours'], \IPS\Data\Store::i()->who_was_online_stat_count), 'htmlsprintf' => array(\IPS\DateTime::ts(\IPS\Data\Store::i()->who_was_online_stat_time)->html())));
		}

		if($this->configuration['who_was_online_max_user'] > 0) {
			$members = array_slice($members, 0, $this->configuration['who_was_online_max_user']);
		}

		return $this->output($members, $memberCount, $this->configuration['who_was_online_hours'], $stat);
	}
}