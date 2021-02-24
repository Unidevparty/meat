<?php
/**
 * <pre>
 * Odnoklassniki plug in for share links library.
 * </pre>
 *
 *
 */

/* Class name must be in the format of:
   sl_{key}
   Where {key}, place with the value of: core_share_links.share_key
 */
class sl_ok
{
	/**#@+
	* Registry Object Shortcuts
	*
	* @access	protected
	* @var		object
	*/
	protected $DB;
	protected $settings;
	protected $lang;
	protected $member;
	protected $memberData;
	protected $cache;
	protected $caches;
	/**#@-*/
	
	/**
	 * Construct.
	 * @access	public
	 * @param	object		Registry
	 * @return	void
	 */
	public function __construct( $registry )
	{
		/* Make object */
		$this->registry   =  $registry;
		$this->DB         =  $this->registry->DB();
		$this->settings   =& $this->registry->fetchSettings();
		$this->request    =& $this->registry->fetchRequest();
		$this->lang       =  $this->registry->getClass('class_localization');
		$this->member     =  $this->registry->member();
		$this->memberData =& $this->registry->member()->fetchMemberData();
		$this->cache      =  $this->registry->cache();
		$this->caches     =& $this->registry->cache()->fetchCaches();
	}
	
	/**
	 * Requires a permission check
	 *
	 * @access	public
	 * @param	array		Data array
	 * @return	boolean
	 */
	public function requiresPermissionCheck( $array )
	{
		return false;
	}

	/**
	 * Redirect to OK
	 * Exciting, isn't it.
	 *
	 * @access	private
	 * @param	string		Plug in
	 */
	public function share( $title, $url )
	{
		$title = IPSText::convertCharsets( $title, IPS_DOC_CHAR_SET, 'utf-8' );
		$url   = 'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' . urlencode( $url ) . '&title='. urlencode( $title );
		$this->registry->output->silentRedirect( $url );
	}

		
}