<?php
if ( !defined( 'MEDIAWIKI' ) ) die();

require_once( "WikiDataAPI.php" ); // for bootstrapCollection
require_once( "Utilities.php" );

class SpecialAddCollection extends SpecialPage {
	function SpecialAddCollection() {
		parent::__construct( 'AddCollection' );
	}

	function execute( $par ) {

		global $wgOut, $wgUser, $wgRequest;

		$wgOut->setPageTitle( 'Add Collection' );

		if ( !$wgUser->isAllowed( 'addcollection' ) ) {
			$wgOut->addHTML( 'You do not have permission to add a collection.' );
			return false;
		}

		$dbr = wfGetDB( DB_MASTER );

		if ( $wgRequest->getText( 'collection' ) ) {
			require_once( 'WikiDataAPI.php' );
			require_once( 'Transaction.php' );

			$dc = $wgRequest->getText( 'dataset' );
			$collectionName = $wgRequest->getText( 'collection' );
			startNewTransaction( $wgUser->getID(), wfGetIP(), 'Add collection ' . $collectionName );
			bootstrapCollection( $collectionName, $wgRequest->getText( 'language' ), $wgRequest->getText( 'type' ), $dc );
			$wgOut->addHTML( wfMessage( 'ow_collection_added', $collectionName )->text() . "<br />" );
		}
		$datasets = wdGetDatasets();
		$datasetarray[''] = wfMessage( "none_selected" )->text();
		foreach ( $datasets as $datasetid => $dataset ) {
			$datasetarray[$datasetid] = $dataset->fetchName();
		}

		// CLAS: an object added to a collection of type CLAS becomes a class (e.g. animal)
		// Then, objects can be attached to that class to give them class-specific attributes (e.g. species)
		// LANG: an object added to a collection of type LANG is considered as a language
		// which allows one to add lang-specific attributes (such as defining "part of speech" for that language
		// so, it acts a bit like a class, but it is not possible to attach an object to that class manually
		// this is done automatically when a word is known to belong to a language.
		$collectionTypes = array(
			'' => 'None',
			'CLAS' => 'CLAS',
			'LANG' => 'LANG',
			'LEVL' => 'LEVL',
			'MAPP' => 'MAPP',
			'RELT' => 'RELT'
		);
		$wgOut->addHTML( getOptionPanel(
			array(
				'Collection name:' => getTextBox( 'collection' ),
				'Language of name:' => getSuggest( 'language', 'language' ),
				'Collection type:' => getSelect( 'type', $collectionTypes ),
				'Dataset:' => getSelect( 'dataset', $datasetarray )
			),
			'', array( 'create' => wfMessage( 'ow_create' )->text() )
		) );
	}
}

