//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook394 extends _HOOK_CLASS_
{


	/**
	 * Returns the content
	 *
	 * @return	string
	 * @throws	\BadMethodCallException
	 */
	public function content()
	{
		try
		{
	  		try
			{
				$content = parent::content();

				//if ( $this->new_topic == 1 )
				//{
                //\IPS\Output::i()->metaTags['og:image'] = "here2";
				
					$img = $this->ipsv_getIMG( $content );
					if ( $img )
					{      
						$img = str_replace("%3C___base_url___%3E/uploads/", "", $img);
						$img = str_replace("uploads//", "uploads/", $img);
						\IPS\Output::i()->metaTags['og:image'] = $img;
					}
				
				//}
				return $content;
			}
			catch ( \RuntimeException $e )
			{
				if ( method_exists( get_parent_class(), __FUNCTION__ ) )
				{
					return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
				}
				else
				{
					throw $e;
				}
	        }
		}
		catch ( \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
  
protected function ipsv_getIMG($txt)
	{
		try
		{
			try
			{
				preg_match_all( '~<img[^>]*(?<!_mce_)src\s?=\s?([\'"])((?:(?!\1).)*)[^>]*>~i', $txt, $match );
				for ( $e = 0 ; $e < count($match[0]) ; $e++ )
				{
					if ( mb_strpos( $match[2][$e], 'fileStore.core_Emoticons' ) === false )
					{
						$img = $this->ipsv_imgMeta_getRealIMG($match[2][$e]);
						break;
					}
				}	
				
				if (preg_match( '/<div.*ipsEmbeddedVideo.*src=\"(.*)\".*><\/div>/isU', $txt, $match ))
				{
					$img = $this->ipsv_getvideoThumb($match[1]);	
				}		
				
				return $img;
			}
			catch ( \RuntimeException $e )
			{
				if ( method_exists( get_parent_class(), __FUNCTION__ ) )
				{
					return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
				}
				else
				{
					throw $e;
				}
			}
		}
		catch ( \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
	
	private function ipsv_imgMeta_getRealIMG($txt)
	{
		try
		{
			try
			{
				$txt = str_replace("fileStore.core_Emoticons", "", $txt);
				$txt = str_replace("fileStore.core_Attachment", "", $txt);
				$txt = str_replace("<>", "", $txt);			
				$txt = str_replace("&lt;&gt;", "", $txt);
				$txt = str_replace("%3C%3E", "", $txt);
				
				if ( \IPS\File::get( 'core_Attachment', $txt )->originalFilename ) 
				{
					return (string) \IPS\File::get( 'core_Attachment', $txt )->url;
				}
				elseif ( \IPS\File::get( 'forums_TopicThumbnail', $txt )->originalFilename )
				{
					return (string) \IPS\File::get( 'forums_TopicThumbnail', $txt )->url;
				}
				else
				{
					return $txt;
				}
			}
			catch ( \RuntimeException $e )
			{
				if ( method_exists( get_parent_class(), __FUNCTION__ ) )
				{
					return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
				}
				else
				{
					throw $e;
				}
			}
		}
		catch ( \RuntimeException $e )
		{
			if ( method_exists( get_parent_class(), __FUNCTION__ ) )
			{
				return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
			}
			else
			{
				throw $e;
			}
		}
	}
	
	protected function ipsv_getvideoThumb( $txt )
	{
		try
		{
			try
			{
				if ( preg_match( '#^(?:https?://)?(?:www\.)?(?:m\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x', $txt, $match ) )
				{
					$thumb = "http://i2.ytimg.com/vi/{$match[1]}/0.jpg";
				}
				else
				{
					$vmID = explode( "/", $txt );
					$url = "http://vimeo.com/api/v2/video/{$vmID[4]}.json";
					$request = \IPS\Http\Url::external( $url )->request()->get();
					$output = json_decode($request);
					$output = $output[0];
					$thumb = $output->thumbnail_large;	
				}		
				return $thumb;
					}
					catch ( \RuntimeException $e )
					{
						if ( method_exists( get_parent_class(), __FUNCTION__ ) )
						{
							return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
						}
						else
						{
							throw $e;
						}
					}
				}
				catch ( \RuntimeException $e )
				{
					if ( method_exists( get_parent_class(), __FUNCTION__ ) )
					{
						return call_user_func_array( 'parent::' . __FUNCTION__, func_get_args() );
					}
					else
					{
						throw $e;
					}
				}
	}		  

}
