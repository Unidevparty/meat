//<?php

/* General Tab */
$form->addTab( "furltranslit_symbols_tab_general" );
$form->addHtml( "<div class=\"ipsPad ipsType_normal\">" . \IPS\Member::loggedIn()->language()->addToStack( 'furltranslit_symbols_tab_general_desc' ) . "</div>" );

$form->add( new \IPS\Helpers\Form\Radio( 'furltranslit_languages', \IPS\Settings::i()->furltranslit_languages, FALSE, array(
	'options' 			=> array(
		'russian' 			=> 'furltranslit_languages_russian',
		'french' 			=> 'furltranslit_languages_french',
		'romanian' 			=> 'furltranslit_languages_romanian',
		'turkish' 			=> 'furltranslit_languages_turkish',
		'greek'				=> 'furltranslit_languages_greek',
		'none'				=> 'furltranslit_languages_none',
	),
), NULL, NULL, NULL, 'furltranslit_languages' ) );


/* Customs Tab */
$form->addTab( "furltranslit_symbols_tab_custom" );
$form->addHtml( "<div class=\"ipsPad ipsType_normal\">" . \IPS\Member::loggedIn()->language()->addToStack( 'furltranslit_symbols_tab_custom_desc' ) . "</div>" );

/* Format keyValue pairs */
$extra = array();
if ( \IPS\Settings::i()->furltranslit_symbols )
{
	foreach( json_decode( \IPS\Settings::i()->furltranslit_symbols ) as $k => $v )
	{
		$extra[] = array( 'key' => $k, 'value' => $v );
	}
}

$form->add( new \IPS\Helpers\Form\Stack( 'furltranslit_symbols', ( $extra and $extra[0]['key'] != '_empty_' ) ? $extra : array(), FALSE, array(
	'key' 				=> array(
		'minLength'			=> 1,
		'maxLength'			=> 3,
		'placeholder'		=> \IPS\Member::loggedIn()->language()->addToStack('furltranslit_symbols_before')
	),
	'value' 			=> array(
		'minLength'			=> 1,
		'maxLength'			=> 3,
		'placeholder'		=> \IPS\Member::loggedIn()->language()->addToStack('furltranslit_symbols_after')
	),
	'stackFieldType'	=> 'KeyValue',
), NULL, NULL, NULL, 'furltranslit_symbols' ) );

if ( $values = $form->values() )
{
	/* Reformat keyValue pairs */
	if ( isset( $values['furltranslit_symbols'] ) )
	{
		$extra = array();
		foreach( $values['furltranslit_symbols'] as $row )
		{
			if ( isset( $row['key'] ) )
			{
				$extra[ $row['key'] ] = $row['value'];
			}
		}

		if ( count( $extra ) )
		{
			$values['furltranslit_symbols'] = json_encode( $extra );
		}
	}

	/* Save */
	$form->saveAsSettings( $values );
	return TRUE;
}

return $form;