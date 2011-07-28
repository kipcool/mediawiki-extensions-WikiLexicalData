<?php

/** 
 * @param $purge purge cache
 * @return array of language names for the user's language preference
 **/
function getOwLanguageNames( $purge = false ) {
	global $wgLang ;
	static $owLanguageNames = null;
	if ( is_null( $owLanguageNames ) && !$purge ) {
		$owLanguageNames = getLangNames( $wgLang->getCode() );
	}
	return $owLanguageNames;

}

/* Return an array containing all language names translated into the language
	indicated by $code, with fallbacks in English where the language names
	aren't present in that language. */
function getLangNames( $code ) {
	$dbr = wfGetDB( DB_SLAVE );
	$names = array();
	$sql = getSQLForLanguageNames( $code );
	$lang_res = $dbr->query( $sql );
	while ( $lang_row = $dbr->fetchObject( $lang_res ) )
		$names[$lang_row->row_id] = $lang_row->language_name;
	return $names;
}

function getLanguageIdForCode( $code ) {

	static $languages = null;
	if ( is_null( $languages ) ) {
		$dbr = wfGetDB( DB_SLAVE );
		$id_res = $dbr->query( "select language_id,wikimedia_key from language" );
		while ( $id_row = $dbr->fetchObject( $id_res ) ) {
			$languages[$id_row->wikimedia_key] = $id_row->language_id;
		}
	}
	if ( is_array( $languages ) && array_key_exists( $code, $languages ) ) {
		return $languages[$code];
	} else {
		return null;
	}
	
}

function getLanguageIdForIso639_3( $code ) {

	static $languages = null;
	
	if ( is_null( $languages ) ) {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->query( "SELECT language_id,iso639_3 FROM language" );
		while ( $row = $dbr->fetchObject( $result ) ) {
			$languages[$row->iso639_3] = $row->language_id;
		}
	}
	
	if ( is_array( $languages ) && array_key_exists( $code, $languages ) ) {
		return $languages[$code];
	} else {
		return null;
	}
	
}

function getLanguageIso639_3ForId( $id ) {

	static $languages = null;
	
	if ( is_null( $languages ) ) {
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->query( "SELECT language_id,iso639_3 FROM language" );
		while ( $row = $dbr->fetchObject( $result ) ) {
			$languages['id' . $row->language_id] = $row->iso639_3;
		}
	}
	
	if ( is_array( $languages ) && array_key_exists( 'id' . $id, $languages ) ) {
		return $languages['id' . $id];
	} else {
		return null;
	}
	
}

/* Return SQL query string for fetching language names. */
function getSQLForLanguageNames( $lang_code ) {
	/* Use a simpler query if the user's language is English. */
	/* getLanguageIdForCode( 'en' ) = 85 */

	if ( $lang_code == 'en' || !( $lang_id = getLanguageIdForCode( $lang_code ) ) )
		return 'SELECT language_id AS row_id, language_name' .
			' FROM language_names' .
			' WHERE name_language_id = 85' ;
	/* Fall back on English in cases where a language name is not present in the
		user's preferred language. */
	else
		return 'SELECT language_id AS row_id, language_name' .
			' FROM language_names' .
			' WHERE name_language_id = ' . $lang_id .
			' OR ( name_language_id = 85 ' .
			' AND language_id NOT IN ( SELECT language_id FROM language_names WHERE name_language_id =  ' . $lang_id .
			' ) ) ' ;
}

function getLanguageIdForName( $name ) {
	$dbr = wfGetDB( DB_SLAVE );
	$queryResult = $dbr->query( "SELECT language_id FROM language_names WHERE language_name=" . $dbr->addQuotes( $name ) );
	
	if ( $languageId = $dbr->fetchObject( $queryResult ) )
		return $languageId->language_id;
	else
		return 0;
}


