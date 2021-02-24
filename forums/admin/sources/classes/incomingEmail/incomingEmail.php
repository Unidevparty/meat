<?php
/**
 * <pre>
 * Invision Power Services
 * IP.Board v3.4.8
 * Parse Incoming Emails
 * Last Updated: $Date: 2015-04-02 13:29:54 -0400 (Thu, 02 Apr 2015) $
 * </pre>
 *
 * @author 		$Author: rashbrook $
 * @copyright	© 2010 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/company/standards.php#license
 * @package		IP.Board
 * @subpackage	Kernel
 * @link		http://www.invisionpower.com
 * @since		25th June 2010
 * @version		$Revision: 12616 $
 */
 
class incomingEmail
{
	/**
	 * Params
	 */
	public $to = '';
	public $from = '';
	public $subject = '';
	public $cc = '';
	public $message = '';
	public $original_message = '';
	public $raw = '';
	public $alternative = '';
	public $attachments = array();
	
	/** 
	 * Debug Mode
	 */
	protected $debug_mode = false;
	
	/**
	 * Preferred order for alterative parts
	 */
	protected $alternativePrefs = array(
		'multipart'	=> array(),	// Multipart has to come first as it's the only one which will include attachments sometimes
		'text'		=> array(),
		);
		
	/**
	 * Constructor
	 */
	protected function __construct()
	{
		/* Init objects */
		$this->registry	  =  ipsRegistry::instance();
		$this->DB         =  $this->registry->DB();
		$this->settings	  =& $this->registry->fetchSettings();
		
		/* Fix up the base URL */
		$this->settings['base_url'] = preg_replace( '/^(.+\/).+?\?(.*?)$/', '$1index.php?$2', $this->settings['base_url'] );
		
		/* Get allowed attachment types */
		$this->types = array();
		$this->DB->build( array( 'select' => '*', 'from' => 'attachments_type' ) );
		$this->DB->execute();
		while ( $r = $this->DB->fetch() )
		{
			$this->types[ $r['atype_extension'] ] = $r;
		}
		
		/* Set our preferred format */
		if ( $this->settings['incoming_emails_textpref'] == 'html' )
		{
			$this->alternativePrefs['text'][] = 'html';
			$this->alternativePrefs['text'][] = 'plain';
		}
		else
		{
			$this->alternativePrefs['text'][] = 'plain';
			$this->alternativePrefs['text'][] = 'html';
		}
	}

	/**
	 * Main Parse Method
	 *
	 * @param	string	Raw email contents
	 * @param	array	Any headers to override
	 * @param	bool	Can pass true to not actually route
	 */
	public static function parse( $email, $override=array(), $doNotRoute=FALSE )
	{
		$obj = new self();
		$obj->raw = $email;
				
		//-----------------------------------------
		// Deconstruct it
		//-----------------------------------------
		
		// It raises strict warnings
		//@error_reporting( E_NONE );
 		//@ini_set( 'display_errors', 'off' );
 		
		require_once ( IPS_KERNEL_PATH . 'PEAR/Mail/mimeDecode.php' );/*noLibHook*/
		$decoder = new Mail_mimeDecode( $email );
		$mail = $decoder->decode( array(
			'include_bodies'	=> TRUE,
			'decode_bodies'		=> TRUE,
			'decode_headers'	=> TRUE,
			) );

		//--------------------------------------
		// Parse Headers
		//--------------------------------------
		
		/* To */
		if ( isset( $override['to'] ) )
		{
			$obj->to = $override['to'];
		}
		else
		{
			if ( $mail->headers['delivered-to'] )
			{
				$mail->headers['to'] = $mail->headers['delivered-to'];
			}
			$to = array();
			if ( strpos( $mail->headers['to'], ',' ) === FALSE )
			{
				$mail->headers['to'] = array( $mail->headers['to'] );
			}
			else
			{
				$mail->headers['to'] = explode( ',', $mail->headers['to'] );
			}
			foreach ( $mail->headers['to'] as $_to )
			{
				if ( preg_match( "/.+? <(.+?)>/", $_to, $matches ) )
				{
					$to[] = htmlentities( $matches[1] , ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, IPS_DOC_CHAR_SET );//-IBR-для php<5.4 и utf-8
				}
				else
				{
					$to[] = htmlentities( trim( $_to, '<>' ), ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, IPS_DOC_CHAR_SET );//-IBR-для php<5.4 и utf-8
				}
			}
			$obj->to = implode( ',', $to );
		}
				
		/* From */
		if ( isset( $override['from'] ) )
		{
			$obj->from = $override['from'];
		}
		else
		{
			if ( preg_match( "/.+? <(.+?)>/", $mail->headers['from'], $matches ) )
			{
				$obj->from = htmlentities( $matches[1],ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, IPS_DOC_CHAR_SET );//-IBR-для php<5.4 и utf-8
			}
			else
			{
				$obj->from = htmlentities( trim( $mail->headers['from'], '<>' ),ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, IPS_DOC_CHAR_SET );
			}//-IBR-для php<5.4 и utf-8
		}
		
		/* Subject */
		if ( isset( $override['subject'] ) )
		{
			$obj->subject = $override['subject'];
		}
		else
		{
			$obj->subject = ( (bool) trim( $mail->headers['subject'] ) ) ? $mail->headers['subject'] : '(No Subject)';
			$obj->subject = htmlentities( $obj->subject, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE | ENT_DISALLOWED, IPS_DOC_CHAR_SET );//-IBR-для php<5.4 и utf-8
		}
		
		/* CC */
		$mail->headers['cc'] = preg_replace( '/".+?" <(.+?)>/', '$1', $mail->headers['cc'] );
		if ( strpos( $mail->headers['cc'], ',' ) === FALSE )
		{
			$mail->headers['cc'] = array( $mail->headers['cc'] );
		}
		else
		{
			$mail->headers['cc'] = explode( ',', $mail->headers['cc'] );
		}
		foreach ( $mail->headers['cc'] as $_cc )
		{
			if ( preg_match( "/.+? <(.+?)>/", $_cc, $matches ) )
			{
				$cc[] = $matches[1];
			}
			else
			{
				$cc[] = trim( $_cc, '<> ' );
			}
		}
		$obj->cc = str_replace( array( '&gt;', '&lt;' ), '', implode( ',', $cc ) );
		
		//-----------------------------------------
		// Ignore?
		//-----------------------------------------
		
		if ( !$obj->debug_mode and !$doNotRoute )
		{
			$escapedFrom = $obj->DB->addSlashes( $obj->from );
			$log = $obj->DB->buildAndFetch( array( 'select' => '*', 'from' => 'core_incoming_email_log', 'where' => "log_email='{$escapedFrom}'" ) );
			if ( $log['log_id'] )
			{
				$oneMinuteAgo = time() - 60;
				if ( $log['log_time'] > $oneMinuteAgo && $obj->settings['sixty_second_rule'] )
				{
					$ignore = TRUE;
				}
				
				$obj->DB->update( 'core_incoming_email_log', array( 'log_time' => time() ), "log_id={$log['log_id']}" );
				
				if ( $ignore )
				{
					return;
				}
			}
			else
			{
				$obj->DB->insert( 'core_incoming_email_log', array( 'log_email' => $obj->from, 'log_time' => time() ) );
			}
		}
		
		//-----------------------------------------
		// Now destruct the message
		//-----------------------------------------
						
		$obj->message = '';
		$obj->_parsePart( $mail );
						
		//-----------------------------------------
		// Purify It
		//-----------------------------------------
		
		// &nbsp; sometimes breaks
		$obj->message = str_replace( '&nbsp;', ' ', $obj->message );

		/* Load */
		require_once( IPS_KERNEL_PATH . 'HTMLPurifier/HTMLPurifier.auto.php' );
		$config = HTMLPurifier_Config::createDefault();

		/* Set Configuration */
		$config->set( 'AutoFormat.Linkify', TRUE );
		$config->set( 'Core.Encoding', IPS_DOC_CHAR_SET );
		$config->set( 'HTML.TargetBlank', TRUE );
		$config->set( 'URI.Munge', ipsRegistry::getClass('output')->buildUrl('app=nexus&module=support&section=redirect&url=%s&key=%t&resource=%r', 'publicNoSession' ) );
		$config->set( 'URI.MungeResources', TRUE );
		$config->set( 'URI.MungeSecretKey', md5( ipsRegistry::$settings['sql_pass'] . ipsRegistry::$settings['board_url'] . ipsRegistry::$settings['sql_database'] ) );

		/* Retain collapse data attribute */
		$def = $config->getHTMLDefinition(true);
		$def->addAttribute( 'blockquote', 'data-author', 'CDATA' );
		$def->addAttribute( 'blockquote', 'data-collapsed', 'Number' );

		/* Purify */
		$purifier = new HTMLPurifier( $config );
		$obj->message = $purifier->purify( $obj->message );
		
		//-----------------------------------------
		// Route
		//-----------------------------------------
		
		if ( $doNotRoute )
		{
			return $obj;
		}
		
		$routed = FALSE;
		
		/* Try our routing criteria */
		$obj->DB->build( array( 'select' => '*', 'from' => 'core_incoming_emails' ) );
		$obj->DB->execute();
		while ( $row = $obj->DB->fetch() )
		{
			// What are we looking for?
			switch ( $row['rule_criteria_field'] )
			{
				case 'to':
					$analyse = $obj->to;
					break;
					
				case 'from':
					$analyse = $obj->from;
					break;
					
				case 'sbjt':
					$analyse = $obj->subject;
					break;
					
				case 'body':
					$analyse = $obj->message;
					break;
			}
			
			// Does it match?			
			$match = false;
			switch ( $row['rule_criteria_type'] )
			{
				case 'ctns':
					$match = (bool) ( strpos( $analyse, $row['rule_criteria_value'] ) !== FALSE );
					break;
					
				case 'eqls':
					if ( strpos( $analyse, ',' ) !== FALSE )
					{
						$match = (bool) in_array( $analyse, explode( ',', $analyse ) );
					}
					else
					{
						$match = (bool) ( $analyse == $row['rule_criteria_value'] );
					}
					break;
					
				case 'regx':
					$match = (bool) ( preg_match( "/{$row['rule_criteria_value']}/", $analyse ) == 1 );
					break;
			}
			
			// If it matches, give to the app				
			if ( $match )
			{
				$routed = true;
				if ( $row['rule_app'] != '--' )
				{
					$appdir = IPSLib::getAppDir( $row['rule_app'] );
					if ( is_file( $appdir . '/extensions/incomingEmails.php' ) )
					{
						$class = 'incomingEmails_' . $row['rule_app'];
						require_once( $appdir . '/extensions/incomingEmails.php' );/*noLibHook*/
						$class = new $class;
						$class->process( $obj );
					}
				}
				break;
			}
		}
				
		/* Still here? Try all our apps */		
		if ( !$routed )
		{
			foreach ( ipsRegistry::$applications as $app )
			{
				$file = IPSLib::getAppDir( $app['app_directory'] ) . '/extensions/incomingEmails.php';
				if ( file_exists( $file ) )
				{
					require_once( $file );
					$class = 'incomingEmails_' . $app['app_directory'];
					$i = new $class;
					if ( $routed = $i->handleUnrouted( $obj ) )
					{
						break;
					}
				}
			}
		}
								
		/* STILL Here? Throw the unrouted message */
		if ( !$routed  )
		{
			$unroutedMessage = @file_get_contents( DOC_IPS_ROOT_PATH . 'interface/email/unrouted.txt' );
			if ( $unroutedMessage )
			{
				IPSText::getTextClass('email')->to		= $obj->from;
				IPSText::getTextClass('email')->from	= $obj->to;
				IPSText::getTextClass('email')->subject	= "Re: {$obj->subject}";
				IPSText::getTextClass('email')->message	= nl2br( $unroutedMessage );
				IPSText::getTextClass('email')->setHtmlEmail( TRUE );
				IPSText::getTextClass( 'email' )->sendMail();
			}
		}
	}
		
	
	/**
	 * Parse a "part"
	 *
	 * @param	stdClass	As returned by mailMime_decode
	 */
	protected function _parsePart( $part )
	{	
		/* Some clients use uppercase, but we check by lowercase */
		$part->ctype_primary = strtolower( $part->ctype_primary );
		$part->ctype_secondary = strtolower( $part->ctype_secondary );
				
		/* Make sure .txt files get attached as .txt files */
		if ( $part->ctype_primary == 'text' and $part->disposition == 'attachment' )
		{
			$part->ctype_primary = 'unknown';
			$part->ctype_secondary = 'unknown';
		}
			
		/* What is this? */
		switch ( $part->ctype_primary )
		{
			/* Multipart - means there's more than one part to this part */
			case 'multipart':
				
				// "Alternative" means there's more than one way to interpret this part, usually means we have plain text and HTML varients
				if ( $part->ctype_secondary == 'alternative' )
				{
					// First, check if we have html and plaintext
					if ( !$this->alternative )
					{
						$haveHtml = false;
						$havePlain = false;
						foreach ( $part->parts as $p )
						{
							if ( $p->ctype_primary == 'text' )
							{
								if ( $p->ctype_secondary == 'html' )
								{
									$haveHtml = true;
								}
								elseif ( $p->ctype_secondary == 'plain' )
								{
									$havePlain = true;
								}
							}
						}
					}
					
					// This means we need to decide which of the parts we prefer
					$preferredPart = array_shift( $part->parts );
					foreach ( $part->parts as $p )
					{
						if ( $this->_isBetter( $preferredPart, $p ) )
						{
							$preferredPart = $p;
						}
					}
					
					// What did we choose?
					if ( !$this->alternative and $haveHtml and $havePlain )
					{
						if ( $preferredPart->ctype_secondary == 'html' )
						{
							$this->alternative = 'h';
						}
						elseif ( $preferredPart->ctype_secondary == 'html' )
						{
							$this->alternative = 'p';
						}
					}
					
					return $this->_parsePart( $preferredPart );
				}
				
				// Otherwise, parse all parts
				foreach ( $part->parts as $p )
				{
					$this->_parsePart( $p );
				}
				
				return;
				
			/* Text */
			case 'text':
						
				$body = $part->body;
				$this->original_message .= $body;
								
				//-----------------------------------------
				// Add <br /> tags
				//-----------------------------------------
				
				if ( $part->ctype_secondary != 'html' )
				{
					$body = nl2br( $body );
				}				
			
				//-----------------------------------------
				// Convert the charset if necessary
				//-----------------------------------------

				if ( isset( $part->ctype_parameters['charset'] ) and $part->ctype_parameters['charset'] != IPS_DOC_CHAR_SET )
				{
					// Sometimes the charset will be "xx-ascii" which isn't what we want
					if ( strpos( $part->ctype_parameters['charset'], 'ascii' ) !== FALSE )
					{
						//$part->ctype_parameters['charset'] = 'iso-8859-1';
					}
					
					// Convert
					$body = IPSText::convertCharsets( $body, $part->ctype_parameters['charset'], IPS_DOC_CHAR_SET );
					$part->ctype_parameters['charset']	= IPS_DOC_CHAR_SET;
				}

				//-----------------------------------------
				// Parse > style quotes
				//-----------------------------------------
				
				$quoteLevel = 0;
				$quotableLine = '';
				foreach ( explode( "<br />", $body ) as $k => $line )
				{
					$line = trim( $line );
					
					// We only need to check for opening/closing quotes if this line actually has content
					if ( $line )
					{
						// If this line starts with less >s than we're expecting, add a close quote tag
						// Note we strip whitespace when doing this check as sometimes you'll get ">>>" and other times "> > >"
						if ( substr( str_replace( ' ', '', $line ), 0, $quoteLevel ) != str_repeat( '>', $quoteLevel ) )
						{
							$quoteLevel--;
							$line = substr( $line, $quoteLevel );
							$line .= '[/quote]<br />';
						}
						else
						{
							
							// Strip out the >s that we're expecting
							// Note this has to use the regex as sometimes you'll get ">>>" and other times "> > >"
							$line = trim( preg_replace( '/^(>\s?){' . $quoteLevel . '}/', '', $line ) );
																			
							// If, after stripping the expected number of >s, the line still starts with a >, then open a quote tag
							if ( substr( $line, 0, 1 ) == '>' )
							{
								$quoteLevel++;
								$line = substr( $line, 1 );
								
								// If we have content to add into the tag, do that, otherwise, a blank quote is fine
								if ( $quotableLine )
								{
									$this->message = substr( $this->message, 0, strrpos( $this->message, $quotableLine ) );
									$this->message .= "[quote collapse='1' name='" . str_replace( array( "\r", "\n" ), ' ', $quotableLine ) . "']";
								}
								else
								{
									$this->message .= "[quote collapse='1']";
								}
							}
						}
						
						// Save the content of this line so that if a quote starts on the next line, we can put it in the header of that quote
						// Only do this if the line ends with a colon (i.e. "On x, y wrote:") and it doesn't contain any quote tags we've added
						$quotableLine = '';
						if ( substr( $line, -1 ) == ':' and strpos( $line, '[' ) === FALSE )
						{
							$quotableLine = $line;
						}
					}
										
					$this->message .=  $line . '<br />';
				}
				if ( $quoteLevel )
				{
					for ( $i = 0; $i < $quoteLevel; $i++ )
					{
						$this->message .= '[/quote]';
					}
				}
				
				//-----------------------------------------
				// Parse HTML block quotes
				//-----------------------------------------

				/* Loop */
				preg_match_all( '/<blockquote.+?>/s', $this->message, $matches );
				while ( !empty( $matches ) and !empty( $matches[0] ) )
				{
					$m = $matches[0][0];

					// Get all the content before the quote
					$stripped = substr( $this->message, 0, strpos( $this->message, $m ) );

					// Knock off any breaks or closed divs after it
					$stripped = trim( str_replace( '</div>', '', $stripped ) );
					$stripped = preg_replace( '/((<br(\s*\/)?>|&nbsp)*$)/s', '', $stripped );

					// Now scan back until we hit some other html
					$pos = 	strrpos( $stripped, '>' ) + 1;
					$stripped = trim( substr( $stripped, $pos ) );

					// If it's an acceptable header, use it
					if ( substr( $stripped, -1, 1 ) == ':' )
					{
						// Chop it out
						$this->message = substr_replace( $this->message, '', strpos( $this->message, $stripped ), strlen( $stripped ) );

						// We have to encode the header so future tags don't replace it
						$stripped = '{{QUOTE-ENCODE:' . base64_encode( $stripped ) . '}}';
												
						// Replace the blockquote					
						$this->message = substr_replace( $this->message, "[quote collapse='1' name='{$stripped}']", strpos( $this->message, $m ), strlen( $m ) );
					}
					else
					{
						// Replace the blockquote
						$this->message = substr_replace( $this->message, "[quote collapse='1']", strpos( $this->message, $m ), strlen( $m ) );
					}
					
					// Do the scan again
					preg_match_all( '/<blockquote.+?>/s', $this->message, $matches );
				}

				/* Decode the quote headers we made */
				$this->message = preg_replace_callback( '/{{QUOTE-ENCODE:(.+?)}}/', create_function( '$m', 'return str_replace( array( "\r", "\n" ), " ", base64_decode( $m[1] ) );' ), $this->message );
				
				/* Add in closing [/quote] tags */
				$this->message = preg_replace( '/<\/blockquote>/', '[/quote]', $this->message );				
																
				/* Check we haven't ended up with any blank quotes */
				preg_match_all( '/\[quote.+?\](.+?)\[\/quote\]/s', $this->message, $matches );
				foreach ( $matches[0] as $k => $m )
				{
					if ( !trim( strip_tags( $matches[1][$k] ) ) )
					{
						$this->message = str_replace( $m, '', $this->message );
					}
				}

				//-----------------------------------------
				// Quote out "Forwarded Message"
				//-----------------------------------------
				
				if ( preg_match( '/-{10,}\s+(.+?)\s-{10,}/', $this->message, $matches ) )
				{
					$this->message = str_replace( $matches[0], "[quote collapse='1']", $this->message );
					$this->message .= '[/quote]';
				}

				//-----------------------------------------
				// Look for common quote encapsulation methods
				//-----------------------------------------
				
				if( strpos( $this->message, 'OLK_SRC_BODY_SECTION' ) !== false )
				{
					if ( preg_match( '/<span id=["\']OLK_SRC_BODY_SECTION[\'"]>/ims', $this->message, $matches ) )
					{
						$this->message = str_replace( $matches[0], "[quote collapse='1']{$matches[0]}", $this->message );

						if( strpos( $this->message, '</body>' ) !== false )
						{
							$this->message = str_replace( '</body>', "[/quote]</body>", $this->message );
						}
						else
						{
							$this->message .= '[/quote]';
						}
					}
				}

				if( strpos( $this->message, 'MsoNormal' ) !== false ) // This must be from Outlook
				{
					if ( preg_match( '/<div style=["\'].+?border-top:.+?[\'"]>.*?<p class=["\']MsoNormal["\']>(.+)<\/div>/ims', $this->message, $matches ) )
					{
						$this->message = str_replace( $matches[0], "[quote collapse='1']{$matches[0]}[/quote]", $this->message );
					}
				}

				// This code does work, however:
				// --Differing email clients have differing formats, so it's not reliable
				// --There can be cases where you have [quote collapse='1' name='On date, at time, so and so wrote:'] already, which results in [quote name='[quote ...]'], breaking the parsing
				/*if( preg_match( '/\bon\s+?.+?,\s*?at.+?,\s*?' . $this->settings['board_name'] . '.+?' . $this->settings['email_out'] . '.+?wrote:/ims', $this->message, $matches ) )
				{
					//-----------------------------------------
					// If there's an opening <div> right after this, go there instead
					// but only if we're not going to strip quotes (otherwise we get a stray "blah blah said:" line)
					//-----------------------------------------

					if( strpos( $this->message, "<div>", strpos( $this->message, $matches[0] ) ) !== false )
					{
						$matches[0]	= substr( $this->message, strpos( $this->message, $matches[0] ), strpos( $this->message, "<div>", strpos( $this->message, $matches[0] ) ) - strpos( $this->message, $matches[0] ) + 5 );

						$this->message	= str_replace( $matches[0], "{$matches[0]}[quote collapse='1']", $this->message );
						$this->message	= preg_replace( "/<\/div>\s*?<\/body>/", "[/quote]</div></body>", $this->message );
					}
					else
					{
						$this->message = str_replace( $matches[0], "{$matches[0]}[quote collapse='1']", $this->message );

						if( strpos( $this->message, '</body>' ) !== false )
						{
							$this->message = str_replace( '</body>', "[/quote]</body>", $this->message );
						}
						else
						{
							$this->message .= '[/quote]';
						}
					}
				}*/

				/* Are we missing any closing tags? */
				$_opening	= substr_count( $this->message, '[quote ' );
				$_closing	= substr_count( $this->message, '[/quote]' );

				if( $_opening > $_closing )
				{
					for( $i = $_opening; $i < $_closing; $i++ )
					{
						$this->message .= '[/quote]';
					}
				}

				//-----------------------------------------
				// Parse the quote tag
				//-----------------------------------------

				//$this->message	= preg_replace( "/\[quote collapse='(.*?)'\]/ims", "<blockquote class='ipsBlockquote' data-collapsed='\\1'>", $this->message );
				//$this->message	= str_replace( "[/quote]", "</blockquote>", $this->message );

				/* Parse BBCode */
				IPSText::getTextClass('bbcode')->parse_html		 = 0;
				IPSText::getTextClass('bbcode')->parse_nl2br	 = 1;
				IPSText::getTextClass('bbcode')->parse_smilies	 = 1;
				IPSText::getTextClass('bbcode')->parse_bbcode	 = 1;
				IPSText::getTextClass('bbcode')->parsing_section = 'support';

				$this->message = IPSText::getTextClass('bbcode')->parseSingleBbcodes( $this->message, 'display', 'quote' );
																
				return;
				
			/* Attachments */
			default:

				if ( $part->ctype_primary == '_text' )
				{
					$part->ctype_primary = 'text';
				}
				
				$mime = "{$part->ctype_primary}/{$part->ctype_secondary}";
				
				$name = $part->ctype_parameters['name'] ? $part->ctype_parameters['name'] : $part->d_parameters['filename'];
				$name_parts = explode( '.', $name );
				$ext = array_pop( $name_parts );
				
				$content = "{ATTACHMENT WITH EXTENSION: {$ext}}";

				if ( isset( $this->types[ $ext ] ) and $this->types[ $ext ]['atype_post'] )
				{
					/* Create the file */
					$masked_name = md5( uniqid( 'email' ) ) . "-{$name}";
					while ( is_file( $this->settings['upload_dir'] . "/{$masked_name}" ) )
					{
						$masked_name = md5( uniqid( 'email' ) . microtime() ) . "-{$name}";
					}
					file_put_contents( $this->settings['upload_dir'] . "/{$masked_name}", $part->body );
					@chmod( $this->settings['upload_dir'] . "/{$masked_name}", IPS_FILE_PERMISSION );
																	
					/* Store attachment data */
					$this->akey++;
					$this->attachments[ $this->akey ] = array(
						'attach_ext'		=> $ext,
						'attach_file'		=> $name,
						'attach_location'	=> $masked_name,
						'attach_is_image'	=> ( $part->ctype_primary == 'image' ) ? 1 : 0,
						'attach_date'		=> time(),
						'attach_filesize'	=> $part->d_parameters['size'],
						);
					$content = "{ATTACHMENT:{$this->akey}}";
					break;
				}
								
				$addedContent = FALSE;
				if ( isset( $part->disposition ) and $part->disposition == 'inline' )
				{
					$contentId = trim( $part->headers['content-id'], '<>' );
										
					if ( strpos( $this->message, $contentId ) !== FALSE )
					{
						switch ( $part->ctype_primary )
						{
							case 'image':							
								$this->message = preg_replace( "/<img.+?src=['\"]cid:{$contentId}['\"].*?>/", $content, $this->message );
								$addedContent = TRUE;
								break;
						}
					}
				}
				if ( !$addedContent )
				{
					$this->message .= $content;
				}
				
				return;
		}
	}
	
	/**
	 * Decide if one part is better than another for parsing multipart/alternative
	 *
	 * @param	stdClass	Part 1
	 * @param	stdClass	Part 2
	 * @return	bool		TRUE if Part 2 is better than Part 1
	 */
	protected function _isBetter( $part1, $part2 )
	{
		/* Define our types */
		$p1Primary		= $part1->ctype_primary;
		$p1Secondary	= $part1->ctype_secondary;
		$p2Primary		= $part2->ctype_primary;
		$p2Secondary	= $part2->ctype_secondary;
		
		/* If they're the same, return false */
		if ( $p1Primary == $p2Primary and $p1Secondary == $p2Secondary )
		{
			return false;
		}
				
		/* Loop through preferences */
		foreach ( $this->alternativePrefs as $primary => $secondary )
		{
			// Part 1 has this
			if ( $p1Primary == $primary )
			{
				// Does part 2 have it to?
				if ( $p2Primary == $primary )
				{
					// Yes - check secondaries
					foreach ( $secondary as $s )
					{
						// Part 1 got it first? return false
						if ( $p1Secondary == $s )
						{
							return false;
						}
						// Part 2 got it first - return true
						elseif ( $p2Secondary == $s )
						{
							return true;
						}
					}
				}
				else
				{
					// No - then part 1 is better
					return false;
				}
			}
			// Part 2 has this but part 1 doesn't - part 2 is better
			elseif ( $p2Primary == $primary )
			{
				return true;
			}
		}
		
		/* Still here? Then we don't have details on this ctype - assume part 1 is better */
		return false;
	}
}