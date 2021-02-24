//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class hook223 extends _HOOK_CLASS_
{

	/**
	 * Convert a value into an "SEO Title" for friendly URLs
	 *
	 * @param	string	$value	Value
	 * @return	string
	 * @note	Many places require an SEO title, so we always need to return something, so when no valid title is available we return a dash
	 */
	static public function seoTitle( $value )
	{
		try
		{
			$value = parent::seoTitle( $value );
	
			$extra = array();
			if ( \IPS\Settings::i()->furltranslit_symbols )
			{
				foreach( json_decode( \IPS\Settings::i()->furltranslit_symbols ) as $k => $v )
				{
					$extra[ $k ] = $v;
				}
	
				$value = strtr( $value, $extra );
			}
	
			$extra = array();
			if ( \IPS\Settings::i()->furltranslit_languages != 'none' )
			{
				$lang = array();
				switch ( \IPS\Settings::i()->furltranslit_languages ) {
					case 'russian':
						$lang = array( 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye', 'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y', 'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k', 'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya' );
						break;
					case 'french':
						$lang = array( 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ç' => 'c', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ÿ' => 'y', 'Ñ' => 'n', 'ñ' => 'n' );
						break;
					case 'romanian':
						$lang = array( 'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ş' => 's', 'ș' => 's', 'ţ' => 't', 'ț' => 't' );
						break;
					case 'turkish':
						$lang = array( 'ü' => 'u', 'ğ' => 'g', 'ş' => 's', 'ı' => 'i', 'ö' => 'o', 'ç' => 'c' );
						break;
					case 'greek':
						$lang = array( 'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'i', 'θ' => 'th', 'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'x', 'ο' => 'o', 'π' => 'p', 'ρ' => 'r', 'ς' => 's', 'σ' => 's', 'τ' => 't', 'υ' => 'i', 'φ' => 'ph', 'χ' => 'kh', 'ψ' => 'ps', 'ω' => 'o', 'Ά' => 'a', 'Έ' => 'e', 'Ή' => 'i', 'Ί' => 'i', 'Ό' => 'o', 'Ώ' => 'o', 'ά' => 'a', 'έ' => 'e', 'ή' => 'i', 'ό' => 'o', 'ί' => 'i', 'ύ' => 'u', 'ώ' => 'o', 'Ϊ' => 'i', 'Ϋ' => 'i', 'ϊ' => 'i', 'ϋ' => 'i', 'ΰ' => 'i', 'ΐ' => 'i', 'ϐ' => 'b', 'ϑ' => 'th', 'ϒ' => 'i', 'ϓ' => 'i', 'ϔ' => 'i', 'ϕ' => 'ph', 'ϖ' => 'p', 'ϗ' => 'k' );
						break;
				}
	
				foreach( $lang as $k => $v )
				{
					$extra[ $k ] = $v;
				}
	
				$value = strtr( $value, $extra );
			}
			
			return $value;
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
