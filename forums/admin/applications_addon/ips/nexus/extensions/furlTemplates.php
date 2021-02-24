<?php
/**
 * Invision Power Services
 * IP.Nexus FURL Extension
 * Last Updated: $Date: 2011-10-10 05:53:45 -0400 (Mon, 10 Oct 2011) $
 *
 * @author 		$Author: mark $ (Orginal: Mark)
 * @copyright	(c) 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Nexus
 * @link		http://www.invisionpower.com
 * @since		29th July 2010
 * @version		$Revision: 9589 $
 */

$_SEOTEMPLATES = array(
	'store-featured' => array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))section=store(?:(?:&|&amp;))featured=1(?:(?:&|&amp;))view=(\w+)/i', 'store/featured-$1/' ),
		'in'			=> array( 
			'regex'		=> "#/store/featured-(\w+)/?$#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'featured', '1' ),
				array( 'view', '$1' )
				)
			) 
		),
	'store-popular'	=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))section=store(?:(?:&|&amp;))popular=1(?:(?:&|&amp;))view=(\w+)/i', 'store/popular-$1/' ),
		'in'			=> array( 
			'regex'		=> "#/store/popular-(\w+)/?$#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'popular', '1' ),
				array( 'view', '$1' )
				)
			) 
		),
	'store-latest'	=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))section=store(?:(?:&|&amp;))latest=1(?:(?:&|&amp;))view=(\w+)/i', 'store/latest-$1/' ),
		'in'			=> array( 
			'regex'		=> "#/store/latest-(\w+)/?$#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'latest', '1' ),
				array( 'view', '$1' )
				)
			) 
		),
	'storecat'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))cat=(\d+)/i', 'store/category/$1-#{__title__}/' ),
		'in'			=> array( 
			'regex'		=> "#/store/category/(\d+?)-#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'cat', '$1' ),
				)
			) 
		),
	'storeitem'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))section=store(?:(?:&|&amp;))do=item(?:(?:&|&amp;))id=(\d+)/i', 'store/product/$1-#{__title__}/' ),
		'in'			=> array( 
			'regex'		=> "#/store/product/(\d+?)-#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'section', 'store' ),
				array( 'do', 'item' ),
				array( 'id', '$1' ),
				)
			) 
		),
	'storecart'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))do=view/i', 'store/cart/' ),
		'in'			=> array( 
			'regex'		=> "#/store/cart/?#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'do', 'view' ),
				)
			) 
		),
	'gift_vouchers'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))do=vouchers/i', 'store/gift-vouchers/' ),
		'in'			=> array( 
			'regex'		=> "#/store/gift-vouchers/?#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'do', 'vouchers' ),
				)
			) 
		),
	'redeem'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 0,
		'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=payments(?:(?:&|&amp;))do=redeem/i', 'store/redeem/' ),
		'in'			=> array( 
			'regex'		=> "#/store/redeem/?#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'do', 'redeem' ),
				)
			) 
		),
	'network-status' => array(
			'app'			=> 'nexus',
			'allowRedirect' => 0,
			'out'			=> array( '/app=nexus(?:(?:&|&amp;))module=clients(?:(?:&|&amp;))section=status/i', 'network-status/' ),
			'in'			=> array(
					'regex'		=> "#/network-status/?#i",
					'matches'	=> array(
							array( 'app', 'nexus' ),
							array( 'module', 'clients' ),
							array( 'section', 'status' ),
					)
			)
		),
	'app=nexus'		=> array( 
		'app'			=> 'nexus',
		'allowRedirect' => 1,
		'out'			=> array( '/app=nexus((?:(?:&amp;|&))module=payments(?:(?:&amp;|&))section=store)?/i', 'store/' ),
		'in'			=> array( 
			'regex'		=> "#/store(/|$|\?)#i",
			'matches'	=> array(
				array( 'app', 'nexus' ),
				array( 'module', 'payments' ),
				array( 'section', 'store' )
				)
			) 
		),
	);
