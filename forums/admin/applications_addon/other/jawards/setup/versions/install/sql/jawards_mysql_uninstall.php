<?php
{
	function jlRemoveDir( $sDir )
	{
		if( is_dir( $sDir ) )
		{
			$sDir = rtrim( $sDir, '/' );
			$oDir = dir( $sDir );
			while( ( $sFile = $oDir->read() ) !== false )
			{
				if( $sFile != '.' && $sFile != '..' )
				{
					( ! is_link( "{$sDir}/{$sFile}" ) && is_dir( "{$sDir}/{$sFile}" ) ) ? jlRemoveDir( "{$sDir}/{$sFile}" ) : unlink( "{$sDir}/{$sFile}" );
				}
			}
			$oDir->close();
			rmdir( $sDir );
			return true;
		}
		return false;
	}
	jlRemoveDir( ipsRegistry::$settings['upload_dir'] . '/jawards' );
}

