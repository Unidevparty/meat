<?php
/**
 * Invision Power Services
 * IP.Nexus Comments Extension (Powers Product Reviews)
 * Last Updated: $Date: 2011-12-09 18:03:32 -0500 (Fri, 09 Dec 2011) $
 *
 * @author 		$Author: bfarber $ (Orginal: Mark)
 * @copyright	(c) 2011 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		4th October 2011
 * @version		$Revision: 9979 $
 */

class comments_nexus_products extends classes_comments_renderer
{
	private $_remap = array(
		'comment_id'			=> 'review_id',
		'comment_author_id'		=> 'review_author_id',
		'comment_author_name'	=> 'review_author_name',
		'comment_text'			=> 'review_text',
		'comment_ip_address'	=> 'review_ip_address',
		'comment_edit_date'		=> 'review_edit_date',
		'comment_date'			=> 'review_date',
		'comment_approved'		=> 'review_approved',
		'comment_parent_id'		=> 'review_product',
		'review_useful'			=> 'review_useful',
		'review_rating'			=> 'review_rating',
		);
	
	/** 
	 * Specify what the columns in nexus_packages are called and how to get their data
	 */
	private $_parentRemap = array(
		'parent_id'			=> 'p_id',
		'parent_owner_id'	=> 0,
		'parent_parent_id'	=> 'p_group',
		'parent_title'		=> 'p_name',
		'parent_seo_title'	=> 'p_seo_name',
		'parent_date'		=> 0
		);
	
	/**
	 * Specify app
	 */
	public function whoAmI()
	{
		return 'nexus-products';
	}
	
	public function settings()
	{
		return array( 'urls-showParent' => "app=nexus&amp;module=payments&amp;section=store&amp;do=item&amp;id=%s",
					  'urls-report'		=> "app=core&module=reports&rcom=nexus&id=%s" );
	}
	
	public function skin()
	{
		return 'nexus_payments';
	}

	/**
	 * Specify what the columns in nexus_reviews are called
	 */
	public function table()
	{
		return 'nexus_reviews';
	}
	
	public function remapKeys( $type='comment' )
	{
		return ( $type == 'comment' ) ? $this->_remap : $this->_parentRemap;
	}
	
	public function seoTemplate()
	{
		return 'storeitem';
	}
	
	public function fetchParent( $id )
	{
		require_once( IPSLib::getAppDir('nexus') . '/sources/packageCore.php' );/*noLibHook*/
		return package::load( $id )->data;
	}
	
	/**
	 * Perform a permission check
	 *
	 * @param	string	Type of check (add/edit/delete/editall/deleteall/approve all)
	 * @param	array 	Array of GENERIC data
	 * @return	true or string to be used in exception
	 */
	public function can( $type, array $array )
	{
		switch ( $type )
		{
			case 'hide':
				return IPSMember::canModerateContent( $this->memberData, IPSMember::CONTENT_HIDE, $array['comment_author_id'] ) ? TRUE : 'NO_PERMISSION';
				break;
			case 'unhide':
				return IPSMember::canModerateContent( $this->memberData, IPSMember::CONTENT_UNHIDE, $array['comment_author_id'] ) ? TRUE : 'NO_PERMISSION';
				break;
			default:
				return $this->memberData['g_is_supmod'] ? TRUE : 'NO_PERMISSION';
				break;
		}
	}
	 
}	