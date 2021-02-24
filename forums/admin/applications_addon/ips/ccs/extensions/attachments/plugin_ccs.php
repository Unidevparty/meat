<?php

/**
 * <pre>
 * Invision Power Services
 * Attachments field type abstraction
 * Last Updated: $Date: 2011-10-31 14:05:03 -0400 (Mon, 31 Oct 2011) $
 * </pre>
 *
 * @author 		$Author: bfarber $
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Content
 * @link		http://www.invisionpower.com
 * @since		2nd Sept 2009
 * @version		$Revision: 9717 $
 */

if ( ! defined( 'IN_IPB' ) )
{
	print "<h1>Incorrect access</h1>You cannot access this file directly. If you have recently upgraded, make sure you upgraded all the relevant files.";
	exit();
}

class plugin_ccs extends class_attach
{
	/**
	 * Module type
	 *
	 * @access	public
	 * @var		string
	 */
	public $module = 'ccs';
	
	/**
	 * Checks the attachment and checks for download / show perms
	 *
	 * @access	public
	 * @param	integer		Attachment id
	 * @return	array 		Attachment data
	 */
	public function getAttachmentData( $attach_id )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$_ok = 0;
		
		//-----------------------------------------
		// Grab 'em
		//-----------------------------------------
		
		$this->DB->build( array( 'select'   => 'a.*',
								 'from'     => array( 'attachments' => 'a' ),
								 'where'    => "a.attach_rel_module='".$this->module."' AND a.attach_id=".$attach_id,
								 'add_join' => array( 0 => array( 'select' => 'am.*',
																  'from'   => array( 'ccs_attachments_map' => 'am' ),
																  'where'  => "am.map_id=a.attach_rel_id",
																  'type'   => 'left' ),
											          1 => array( 'select' => 'd.*',
																  'from'   => array( 'ccs_databases' => 'd' ),
																  'where'  => "d.database_id=am.map_database_id",
																  'type'   => 'left' ),
													  2 => array( 'select' => 'i.*',
																  'from'   => array( 'permission_index' => 'i' ),
																  'where'  => "i.perm_type='databases' AND i.perm_type_id=d.database_id",
																  'type'   => 'left' ) ) ) );

		$attach_sql = $this->DB->execute();
		
		$attach     = $this->DB->fetch( $attach_sql );
		
		$this->caches['ccs_attachments_data'][ $attach['map_record_id'] ][ $attach['map_id'] ]	= $attach;
		
		//-----------------------------------------
		// Check..
		//-----------------------------------------
		
		if ( empty( $attach['database_id'] ) )
		{
			return FALSE;
		}
		
		//-----------------------------------------
    	// For previews
    	//-----------------------------------------
    	
		if( $attach['perm_view'] != '*' )
		{ 
			if ( $this->registry->permissions->check( 'view', $attach ) != TRUE )
			{
				return false;
			}
		}
		
		if ( $this->registry->permissions->check( 'show', $attach ) != TRUE )
		{
			return false;
		}

		//-----------------------------------------
		// Ok?
		//-----------------------------------------

		return $attach;
	}
	
	/**
	 * Check the attachment and make sure its OK to display
	 *
	 * @access	public
	 * @param	array		Array of ids
	 * @param	array 		Array of relationship ids
	 * @return	array 		Attachment data
	 */
	public function renderAttachment( $attach_ids, $rel_ids=array(), $attach_post_key=0 )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$rows  		= array();
		$query_bits	= array();
		$query 		= '';
		$match 		= 0;
		
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( is_array( $attach_ids ) AND count( $attach_ids ) )
		{
			$query_bits[] = "attach_id IN (" . implode( ",", $attach_ids ) .")";
		}
		
		if ( is_array( $rel_ids ) and count( $rel_ids ) )
		{
			if( count($rel_ids) > 1 OR $rel_ids[0] != 0 )
			{
				$rel_ids = array_map( 'intval', $rel_ids );
				
				$query_bits		= array();
				$query_bits[]	= "attach_rel_id IN (" . implode( ",", $rel_ids ) . ")";
				
				$match = 1;
			}
		}
		
		if ( $attach_post_key )
		{
			$query_bits[] = "attach_post_key='".$this->DB->addSlashes( $attach_post_key )."'";

			$match  = 2;
		}
		
		if( !count($query_bits) )
		{
			return array();
		}
		else
		{
			$query = implode( " OR ", $query_bits );
		}

		//-----------------------------------------
		// Grab 'em
		//-----------------------------------------
		
		$this->DB->build( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_rel_module='{$this->module}' AND ( {$query} )" ) );
		$this->DB->execute();
		
		//-----------------------------------------
		// Loop through and filter off naughty ids
		//-----------------------------------------
		
		while( $row = $this->DB->fetch() )
		{
			$_ok = 1;
			
			if ( $match == 1 )
			{
				if ( ! in_array( $row['attach_rel_id'], $rel_ids ) )
				{
					$_ok = 0;
				}
			}
			else if ( $match == 2 )
			{
				if ( $row['attach_post_key'] != $attach_post_key )
				{
					$_ok = 0;
				}
			}
			
			//-----------------------------------------
			// This is purely for lightbox so the "rel id"
			// matches for each attachment in a record..
			// @link http://community.invisionpower.com/tracker/issue-20044-attachments-section-repeated
			//-----------------------------------------
			
			$row['attach_rel_id']	= $row['attach_post_key'];
			
			//-----------------------------------------
			// Ok?
			//-----------------------------------------
			
			if ( $_ok )
			{
				$rows[ $row['attach_id'] ] = $row;
			}
		}

		//-----------------------------------------
		// Return
		//-----------------------------------------
	
		return $rows;
	}
	
	/**
	 * Recounts number of attachments for the record
	 *
	 * @access	public
	 * @param	string		Post key
	 * @param	integer		Related ID
	 * @param	array 		Arguments for query
	 * @return	array 		Returns count of items found
	 */
	public function postUploadProcess( $post_key, $rel_id, $args=array() )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$cnt = array( 'cnt' => 0 );
		
		//-----------------------------------------
		// Check..
		//-----------------------------------------
		
		if ( ! $post_key )
		{
			return 0;
		}
		
		//-----------------------------------------
		// Get mapped attachments
		//-----------------------------------------
		
		$mapped	= array();
		
		$this->DB->build( array( 'select' => '*', 'from' => 'ccs_attachments_map', 'where' => "map_database_id={$args['database_id']} AND map_field_id={$args['field_id']} AND map_record_id={$args['record_id']}" ) );
		$this->DB->execute();
		
		while( $r = $this->DB->fetch() )
		{
			$mapped[ $r['map_attach_id'] ]	= $r;
		}
		
		$this->DB->build( array( 'select' => '*', 'from' => 'attachments', 'where' => "attach_post_key='{$post_key}'" ) );
		$outer	= $this->DB->execute();

		while( $r = $this->DB->fetch($outer) )
		{
			if( isset( $mapped[ $r['attach_id'] ] ) )
			{
				$this->DB->update( 'attachments', array( 'attach_rel_id' => $mapped[ $r['attach_id'] ]['map_id'] ), 'attach_id=' . $r['attach_id'] );
			}
			else
			{
				$insert	= array(
								'map_database_id'	=> $args['database_id'],
								'map_field_id'		=> $args['field_id'],
								'map_record_id'		=> $args['record_id'],
								'map_attach_id'		=> $r['attach_id'],
								);
								
				$this->DB->insert( 'ccs_attachments_map', $insert );
				$this->DB->update( 'attachments', array( 'attach_rel_id' => $this->DB->getInsertId() ), 'attach_id=' . $r['attach_id'] );
			}
			
			$cnt['cnt']++;
		}
		
		return array( 'count' => $cnt['cnt'] );
	}
	
	/**
	 * Cleanup from attachment ermoval
	 *
	 * @access	public
	 * @param	array 		Attachment data
	 * @return	boolean
	 */
	public function attachmentRemovalCleanup( $attachment )
	{
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! $attachment['attach_id'] )
		{
			return FALSE;
		}
		
		$map	= $this->DB->buildAndFetch( array( 'select' => '*',
								 				    'from'   => 'ccs_attachments_map',
								 				    'where'  => 'map_id=' . intval( $attachment['attach_rel_id'] ) ) );
	
		if ( $map['map_id'] )
		{
			//-----------------------------------------
			// Get num attachments for this record
			//-----------------------------------------
		
			$count	= $this->DB->buildAndFetch( array( 'select' => 'count(*) as total', 'from' => 'ccs_attachments_map', 'where' => "map_database_id={$map['map_database_id']} AND map_field_id={$map['map_field_id']} AND map_record_id={$map['map_record_id']}" ) );

			$this->DB->update( 'ccs_custom_database_' . $map['map_database_id'], array( 'field_' . $map['map_field_id'] => intval($count['total']) ), "primary_id_field=" . $map['map_record_id'] );
			
			$this->DB->delete( 'ccs_attachments_map', "map_id='{$map['map_id']}'" );
		}
		
		return TRUE;
	}
	
	/**
	 * Determines if you have permission for bulk attachment removal
	 * Returns TRUE or FALSE
	 * IT really does
	 *
	 * @access	public
	 * @param	array 		Ids to check against
	 * @return	boolean
	 */
	public function canBulkRemove( $attach_rel_ids=array() )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$ok_to_remove = FALSE;
		
		//-----------------------------------------
		// Allowed to remove?
		//-----------------------------------------
		
		if ( $this->memberData['g_is_supmod'] )
		{
			$ok_to_remove = TRUE;
		}
		
		return $ok_to_remove;
	}
	
	/**
	 * Determines if you can remove this attachment
	 * Returns TRUE or FALSE
	 * IT really does
	 *
	 * @access	public
	 * @param	array 		Attachment data
	 * @return	boolean
	 */
	public function canRemove( $attachment )
	{
		//-----------------------------------------
		// INIT
		//-----------------------------------------
		
		$ok_to_remove = FALSE;
		
		//-----------------------------------------
		// Check
		//-----------------------------------------
		
		if ( ! $attachment['attach_id'] )
		{
			return FALSE;
		}
		
		//-----------------------------------------
		// Allowed to remove?
		//-----------------------------------------
		
		if ( $this->memberData['member_id'] == $attachment['attach_member_id'] )
		{
			return TRUE;
		}
		else if ( $this->memberData['g_is_supmod'] )
		{
			return TRUE;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns an array of the allowed upload sizes in bytes.
	 * Return 'space_allowed' as -1 to not allow uploads.
	 * Return 'space_allowed' as 0 to allow unlimited uploads
	 * Return 'max_single_upload' as 0 to not set a limit
	 *
	 * @access	public
	 * @param	string		MD5 post key
	 * @param	id			Member ID
	 * @return	array 		[ 'space_used', 'space_left', 'space_allowed', 'max_single_upload' ]
	 */
	public function getSpaceAllowance( $post_key='', $member_id='' )
	{
		$max_php_size      = IPSLib::getMaxPostSize();
		$member_id         = intval( $member_id ? $member_id : $this->memberData['member_id'] );
		$space_left        = 0;
		$space_used        = 0;
		$space_allowed     = 0;
		$max_single_upload = 0;

		//-----------------------------------------
		// Allowed to attach?
		//-----------------------------------------
		
		if ( !$member_id )
		{
			$space_allowed = -1;
		}
		else
		{
			//-----------------------------------------
			// Generate total space allowed
			//-----------------------------------------

			$total_space_allowed = ( $this->memberData['g_attach_per_post'] ? $this->memberData['g_attach_per_post'] : $this->memberData['g_attach_max'] ) * 1024;
			
			//-----------------------------------------
			// Generate space used figure
			//-----------------------------------------
			
			if ( $this->memberData['g_attach_per_post'] )
			{
				//-----------------------------------------
				// Per post limit...
				//-----------------------------------------
				
				$_space_used = $this->DB->buildAndFetch( array( 'select' => 'SUM(attach_filesize) as figure',
																'from'   => 'attachments',
																'where'  => "attach_post_key='".$post_key."'" ) );

				$space_used    = $_space_used['figure'] ? $_space_used['figure'] : 0;
			}
			else
			{
				//-----------------------------------------
				// Global limit...
				//-----------------------------------------
				
				$_space_used = $this->DB->buildAndFetch( array( 'select' => 'SUM(attach_filesize) as figure',
																'from'   => 'attachments',
																'where'  => 'attach_member_id='.$member_id . " AND attach_rel_module IN( 'ccs' )" ) );

				$space_used    = $_space_used['figure'] ? $_space_used['figure'] : 0;
			}	
			//-----------------------------------------
			// Generate space allowed figure
			//-----------------------------------------
		
			if ( $this->memberData['g_attach_max'] > 0 )
			{
				if ( $this->memberData['g_attach_per_post'] )
				{
					$_g_space_used	= $this->DB->buildAndFetch( array( 'select' => 'SUM(attach_filesize) as figure',
																	   'from'   => 'attachments',
																	   'where'  => 'attach_member_id='.$member_id . " AND attach_rel_module IN( 'ccs' )" ) );

					$g_space_used    = $_g_space_used['figure'] ? $_g_space_used['figure'] : 0;
					
					if( ( $this->memberData['g_attach_max'] * 1024 ) - $g_space_used < 0 )
					{
						$space_used    			= $g_space_used;
						$total_space_allowed	= $this->memberData['g_attach_max'] * 1024;
						
						$space_allowed = ( $this->memberData['g_attach_max'] * 1024 ) - $space_used;
						$space_allowed = $space_allowed < 0 ? -1 : $space_allowed;
					}
					else
					{
						$space_allowed = ( $this->memberData['g_attach_per_post'] * 1024 ) - $space_used;
						$space_allowed = $space_allowed < 0 ? -1 : $space_allowed;
					}
				}
				else
				{
					$space_allowed = ( $this->memberData['g_attach_max'] * 1024 ) - $space_used;
					$space_allowed = $space_allowed < 0 ? -1 : $space_allowed;
				}
			}
			else
			{
				if ( $this->memberData['g_attach_per_post'] )
				{
					$space_allowed = ( $this->memberData['g_attach_per_post'] * 1024 ) - $space_used;
					$space_allowed = $space_allowed < 0 ? -1 : $space_allowed;
				}
				else
				{ 
					# Unlimited
					$space_allowed = 0;
				}
			}
			
			//-----------------------------------------
			// Generate space left figure
			//-----------------------------------------
			
			$space_left = $space_allowed ? $space_allowed : 0;
			$space_left = ($space_left < 0) ? -1 : $space_left;
			
			//-----------------------------------------
			// Generate max upload size
			//-----------------------------------------
			
			if ( ! $max_single_upload )
			{
				if ( $space_left > 0 AND $space_left < $max_php_size )
				{
					$max_single_upload = $space_left;
				}
				else if ( $max_php_size )
				{
					$max_single_upload = $max_php_size;
				}
			}
		}

		IPSDebug::fireBug( 'info', array( 'Space left: ' . $space_left ) );
		IPSDebug::fireBug( 'info', array( 'Max PHP size: ' . $max_php_size ) );
		IPSDebug::fireBug( 'info', array( 'Max single file size: ' . $max_single_upload ) );
		
		$return = array( 'space_used' => $space_used, 'space_left' => $space_left, 'space_allowed' => $space_allowed, 'max_single_upload' => $max_single_upload, 'total_space_allowed' => $total_space_allowed );

		return $return;
	}
	
	/**
	 * Returns an array of settings:
	 * 'siu_thumb' = Allow thumbnail creation?
	 * 'siu_height' = Height of the generated thumbnail in pixels
	 * 'siu_width' = Width of the generated thumbnail in pixels
	 * 'upload_dir' = Base upload directory (must be a full path)
	 *
	 * You can omit any of these settings and IPB will use the default
	 * settings (which are the ones entered into the ACP for post thumbnails)
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function getSettings()
	{
		$this->mysettings = array();
		
		return true;
	}

}