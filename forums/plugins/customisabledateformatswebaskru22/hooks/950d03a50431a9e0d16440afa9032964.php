//<?php

class hook222 extends _HOOK_CLASS_
{
	/**
	 * Format the date and time according to the user's locale
	 *
	 * @return	string
	 */
	public function __toString()
	{
		try
		{
			return \IPS\Member::loggedIn()->language()->convertString( strftime( $this->localeDateFormat() . ' ' . $this->localeTimeFormat(), $this->getTimestamp() + $this->getTimezone()->getOffset( $this ) ) );
		}
		catch ( \RuntimeException $e )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
	}
	
	
	/**
	 * Format the date according to the user's locale (without the time)
	 *
	 * @return	string
	 */
	public function localeDate()
	{
		try
		{
			return \IPS\Member::loggedIn()->language()->convertString( strftime( $this->localeDateFormat(), $this->getTimestamp() + $this->getTimezone()->getOffset( $this ) ) );
		}
		catch ( \RuntimeException $e )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
	}
	
	/**
	 * Locale date format
	 *
	 * @return	string
	 */
	public function localeDateFormat()
	{
		try
		{
			return \IPS\Settings::i()->custom_date_format ?: '%x';
		}
		catch ( \RuntimeException $e )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
	}
	
	/**
	 * Locale time format
	 *
	 * PHP always wants to use 24-hour format but some
	 * countries prefer 12-hour format, so we override
	 * specifically for them
	 *
	 * @param	bool	$seconds	If TRUE, will include seconds
	  * @param	bool	$minutes	If TRUE, will include minutes
	 * @return	string
	 */
	public function localeTimeFormat( $seconds=FALSE, $minutes=TRUE )
	{
		try
		{
			if ( \IPS\Settings::i()->custom_time_format == 12 )
			{
				if( mb_strtoupper( mb_substr( PHP_OS, 0, 3 ) ) === 'WIN' )
				{
					return '%I' . ( $minutes ? ':%M' : '' ) . ( $seconds ? ':%S' : '' ) . ' %p ';
				}
				else
				{
					return '%l' . ( $minutes ? ':%M' : '' ) . ( $seconds ? ':%S' : '' ) . ' %p ';
				}
			}
			elseif ( \IPS\Settings::i()->custom_time_format == 24 )
			{
				return '%H' . ( $minutes ? ':%M' : '' ) . ( $seconds ? ':%S ' : ' ' );
			}
			else
			{
				return parent::localeTimeFormat( $seconds, $minutes );
			}
		}
		catch ( \RuntimeException $e )
		{
			return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
		}
	}
}