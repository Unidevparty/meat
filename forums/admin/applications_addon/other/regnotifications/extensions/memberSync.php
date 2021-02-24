<?php

/**
* Member Synchronization extensions
*/
class regnotificationsMemberSync
{
	/**
	 * Registry reference
	 *
	 * @var		object
	 */
	public $registry;

	/**
	 * CONSTRUCTOR
	 *
	 * @return	@e void
	 */
	public function __construct()
	{
		$this->registry = ipsRegistry::instance();
	}

	/**
	 * This method is run when a new account is created
	 *
	 * @param	array	 $member	Array of member data
	 * @return	@e void
	 */
	public function onCreateAccount( $member )
	{
		require_once( IPSLib::getAppDir( 'regnotifications' ) . '/sources/classes/regnotifications.php' );
		$this->registry->setClass( 'regnotifications', new regnotifications( $this->registry ) );

		/**
		 * Загрузка информации о пользователе
		 */
		$member = IPSMember::load( $member['member_id'] );
		/**
		 * Вызов функции отправки письма
		 */
		$this->registry->regnotifications->sendMail( $member );
	}
}