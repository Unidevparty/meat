//<?php

$form->add( new \IPS\Helpers\Form\Text( 'custom_date_format', \IPS\Settings::i()->custom_date_format ?: NULL, FALSE, array( 'nullLang' => 'custom_date_locale_aware' ) ) );
$form->add( new \IPS\Helpers\Form\Radio( 'custom_time_format', \IPS\Settings::i()->custom_time_format, FALSE, array( 'options' => array(
	''	=> 'custom_date_locale_aware',
	12	=> 'custom_date_12_hour',
	24	=> 'custom_date_24_hour'
) ) ) );

if ( $values = $form->values() )
{
	$form->saveAsSettings();
	return TRUE;
}

return $form;