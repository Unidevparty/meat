<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.8
 * Some required extensions to check for
 * Last Updated: $Date: 2013-03-11 10:53:28 -0400 (Mon, 11 Mar 2013) $
 * </pre>
 *
 * @author 		Matt Mecham
 * @copyright	(c) 2001 - 2009 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @link		http://www.invisionpower.com
 * @since		1st December 2008
 * @version		$Revision: 12068 $
 *
 */
 
$INSTALLDATA = array(
	
array( 'prettyname'		=> "Поддержка DOM XML",
	   'extensionname'	=> "libxml2",
	   'helpurl'		=> "http://www.php.net/manual/ru/dom.setup.php",
	   'testfor'		=> 'dom',
	   'nohault'		=> false ),

array( 'prettyname'		=> "Библиотека GD",
	   'extensionname'	=> "gd",
	   'helpurl'		=> "http://www.php.net/manual/ru/image.setup.php",
	   'testfor'		=> 'gd',
	   'nohault'		=> true ),

array( 'prettyname'		=> "Reflection Class",
	   'extensionname'	=> "Reflection",
	   'helpurl'		=> "http://www.php.net/manual/ru/language.oop5.reflection.php",
	   'testfor'		=> 'Reflection',
	   'nohault'		=> false ),

array( 'prettyname'		=> "SPL",
	   'extensionname'	=> "SPL",
	   'helpurl'		=> "http://www.php.net/manual/ru/book.spl.php",
	   'testfor'		=> 'SPL',
	   'nohault'		=> true ),
	   
array( 'prettyname'		=> "OpenSSL",
	   'extensionname'	=> "openssl",
	   'helpurl'		=> "http://www.php.net/manual/ru/book.openssl.php",
	   'testfor'		=> 'openssl',
	   'nohault'		=> true ),
	   
array( 'prettyname'		=> "JSON",
	   'extensionname'	=> "json",
	   'helpurl'		=> "http://www.php.net/manual/ru/book.json.php",
	   'testfor'		=> 'json',
	   'nohault'		=> true ),
	   
array(  'prettyname'	=> "Ctype",
		'extensionname'	=> "ctype",
   		'helpurl'		=> "http://www.php.net/manual/ru/book.ctype.php",
   		'testfor'		=> 'ctype',
   		'nohault'		=> true ),
array(  'prettyname'	=> "cURL",
		'extensionname'	=> "curl",
   		'helpurl'		=> "http://www.php.net/manual/ru/book.curl.php",
   		'testfor'		=> 'ctype',
   		'nohault'		=> true ),
array(  'prettyname'	=> "MB String",
		'extensionname'	=> "mbstirng",
   		'helpurl'		=> "http://www.php.net/manual/ru/book.mbstring.php",
   		'testfor'		=> 'ctype',
   		'nohault'		=> true ),	   
);
