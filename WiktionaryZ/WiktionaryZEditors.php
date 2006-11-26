<?php

require_once('Editor.php');
require_once('WiktionaryZAttributes.php');
require_once('WikiDataBootstrappedMeanings.php');
require_once('Fetcher.php');

initializeObjectAttributeEditors(true);
function initializeObjectAttributeEditors($showRecordLifeSpan) {
	global
		$objectAttributesAttribute,
		$definedMeaningObjectAttributesEditor, $definedMeaningIdAttribute,
		$definitionObjectAttributesEditor, $definedMeaningIdAttribute,
		$synonymsAndTranslationsObjectAttributesEditor, $syntransIdAttribute,
		$relationsObjectAttributesEditor, $relationIdAttribute,
		$textValueObjectAttributesEditor, $textAttributeIdAttribute,
		$translatedTextValueObjectAttributesEditor, $translatedTextAttributeIdAttribute,
		$definedMeaningMeaningName, $definitionMeaningName,
		$relationMeaningName, $synTransMeaningName,
		$annotationMeaningName;
		
	$definedMeaningObjectAttributesEditor =	new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$definitionObjectAttributesEditor =	new RecordUnorderedListEditor($objectAttributesAttribute, 5); 
	$synonymsAndTranslationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$relationsObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$textValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	$translatedTextValueObjectAttributesEditor = new RecordUnorderedListEditor($objectAttributesAttribute, 5);
	
	setObjectAttributesEditor($definedMeaningObjectAttributesEditor, $showRecordLifeSpan, new ObjectIdFetcher(0, $definedMeaningIdAttribute), $definedMeaningMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($definitionObjectAttributesEditor, $showRecordLifeSpan, new DefinitionObjectIdFetcher(0, $definedMeaningIdAttribute), $definitionMeaningName, new ObjectIdFetcher(0, $definedMeaningIdAttribute));
	setObjectAttributesEditor($synonymsAndTranslationsObjectAttributesEditor, $showRecordLifeSpan, new ObjectIdFetcher(0, $syntransIdAttribute), $synTransMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($relationsObjectAttributesEditor, $showRecordLifeSpan, new ObjectIdFetcher(0, $relationIdAttribute), $relationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($textValueObjectAttributesEditor, $showRecordLifeSpan, new ObjectIdFetcher(0, $textAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));
	setObjectAttributesEditor($translatedTextValueObjectAttributesEditor, $showRecordLifeSpan, new ObjectIdFetcher(0, $translatedTextAttributeIdAttribute), $annotationMeaningName, new ObjectIdFetcher(1, $definedMeaningIdAttribute));	
}

function getTransactionEditor($attribute) {
	global
		$userAttribute, $timestampAttribute;

	$transactionEditor = new RecordTableCellEditor($attribute);
	$transactionEditor->addEditor(new UserEditor($userAttribute, new SimplePermissionController(false), true));
	$transactionEditor->addEditor(new TimestampEditor($timestampAttribute, new SimplePermissionController(false), true));

	return $transactionEditor;
}

function createTableLifeSpanEditor($attribute) {
	global
		$addTransactionAttribute, $removeTransactionAttribute;
	
	$result = new RecordTableCellEditor($attribute);
	$result->addEditor(getTransactionEditor($addTransactionAttribute));
	$result->addEditor(getTransactionEditor($removeTransactionAttribute));
	
	return $result;
}

function addTableLifeSpanEditor($editor, $showRecordLifeSpan) {
	global
		$recordLifeSpanAttribute, $addTransactionAttribute, $removeTransactionAttribute, $wgRequest;

	if ($wgRequest->getText('action') == 'history' && $showRecordLifeSpan) 
		$editor->addEditor(createTableLifeSpanEditor($recordLifeSpanAttribute));
}

function getDefinitionEditor($attribute, $controller, $showRecordLifeSpan) {
	global
		$translatedTextAttribute, $definitionObjectAttributesEditor;

	$editor = new RecordDivListEditor($attribute);
	$editor->addEditor(getTranslatedTextEditor($translatedTextAttribute, new DefinedMeaningDefinitionController(), $showRecordLifeSpan));
	$editor->addEditor(new PopUpEditor($definitionObjectAttributesEditor, 'Annotation'));

	return $editor;		
}	

function getTranslatedTextEditor($attribute, $controller, $showRecordLifeSpan) {
	global
		$languageAttribute, $textAttribute;

	$editor = new RecordSetTableEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, true, $controller);
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function setObjectAttributesEditor($objectAttributesEditor, $showRecordLifeSpan, $objectIdFetcher, $levelDefinedMeaningName, $dmObjectIdFetcher) {
	$objectAttributesEditor->addEditor(getTextAttributeValuesEditor($showRecordLifeSpan, new TextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
	$objectAttributesEditor->addEditor(getTranslatedTextAttributeValuesEditor($showRecordLifeSpan, new TranslatedTextAttributeValuesController($objectIdFetcher), $levelDefinedMeaningName, $dmObjectIdFetcher));
}

function getAlternativeDefinitionsEditor($showRecordLifeSpan) {
	global
		$alternativeDefinitionsAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

	$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningAlternativeDefinitionsController());
//		$editor = new RecordSetTableEditor($alternativeDefinitionsAttribute, new AlternativeDefinitionsPermissionController(), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningAlternativeDefinitionsController());
	$editor->addEditor(getTranslatedTextEditor($alternativeDefinitionAttribute, new DefinedMeaningAlternativeDefinitionController(), $showRecordLifeSpan));
	$editor->addEditor(new DefinedMeaningReferenceEditor($sourceAttribute, new SimplePermissionController(false), true));
	
	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getExpressionTableCellEditor($attribute) {
	global
		$languageAttribute, $spellingAttribute;

	$editor = new RecordTableCellEditor($attribute);
	$editor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new SpellingEditor($spellingAttribute, new SimplePermissionController(false), true));
	
	return $editor;
}

function getClassAttributesEditor($showRecordLifeSpan) {
	global
		$definedMeaningIdAttribute, $classAttributesAttribute, $classAttributeLevelAttribute ,$classAttributeAttributeAttribute;

	$tableEditor = new RecordSetTableEditor($classAttributesAttribute, new SimplePermissionController(true), new ShowEditFieldForClassesChecker(0, $definedMeaningIdAttribute), new AllowAddController(true), true, false, new ClassAttributesController());
	$tableEditor->addEditor(new ClassAttributesLevelDefinedMeaningEditor($classAttributeLevelAttribute, new SimplePermissionController(false), true));
	$tableEditor->addEditor(new DefinedMeaningReferenceEditor($classAttributeAttributeAttribute, new SimplePermissionController(false), true));
	addTableLifeSpanEditor($tableEditor, $showRecordLifeSpan);
	return $tableEditor;
}

function getSynonymsAndTranslationsEditor($showRecordLifeSpan) {
	global
		$synonymsAndTranslationsAttribute, $identicalMeaningAttribute, $expressionIdAttribute, 
		$expressionAttribute, $synonymsAndTranslationsObjectAttributesEditor;

	$tableEditor = new RecordSetTableEditor($synonymsAndTranslationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new SynonymTranslationController());
	$tableEditor->addEditor(getExpressionTableCellEditor($expressionAttribute));
	$tableEditor->addEditor(new BooleanEditor($identicalMeaningAttribute, new SimplePermissionController(true), true, true));
	$tableEditor->addEditor(new PopUpEditor($synonymsAndTranslationsObjectAttributesEditor, 'Annotation'));

	addTableLifeSpanEditor($tableEditor, $showRecordLifeSpan);

	return $tableEditor;
}

function getDefinedMeaningRelationsEditor($showRecordLifeSpan) {
	global
		$relationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor;

	$editor = new RecordSetTableEditor($relationsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningRelationController());
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, 'Annotation'));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan) {
	global
		$reciprocalRelationsAttribute, $relationTypeAttribute, $otherDefinedMeaningAttribute,
		$relationsObjectAttributesEditor;

	$editor = new RecordSetTableEditor($reciprocalRelationsAttribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
	$editor->addEditor(new DefinedMeaningReferenceEditor($otherDefinedMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new RelationTypeReferenceEditor($relationTypeAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new PopUpEditor($relationsObjectAttributesEditor, 'Annotation'));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningClassMembershipEditor($showRecordLifeSpan) {
	global
		$classMembershipAttribute, $classAttribute;

	$editor = new RecordSetTableEditor($classMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningClassMembershipController());
	$editor->addEditor(new ClassReferenceEditor($classAttribute, new SimplePermissionController(false), true));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan) {
	global
		$collectionMembershipAttribute, $collectionMeaningAttribute, $sourceIdentifierAttribute;

	$editor = new RecordSetTableEditor($collectionMembershipAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, new DefinedMeaningCollectionController());
	$editor->addEditor(new CollectionReferenceEditor($collectionMeaningAttribute, new SimplePermissionController(false), true));
	$editor->addEditor(new ShortTextEditor($sourceIdentifierAttribute, new SimplePermissionController(true), true));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getTextAttributeValuesEditor($showRecordLifeSpan, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$textAttributeAttribute, $textAttribute, $textAttributeValuesAttribute, $textValueObjectAttributesEditor;

	$editor = new RecordSetTableEditor($textAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TextAttributeEditor($textAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(new TextEditor($textAttribute, new SimplePermissionController(true), true));
	$editor->addEditor(new PopUpEditor($textValueObjectAttributesEditor, 'Annotation'));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getTranslatedTextAttributeValuesEditor($showRecordLifeSpan, $controller, $levelDefinedMeaningName, $objectIdFetcher) {
	global
		$translatedTextAttributeAttribute, $translatedTextValueAttribute, $translatedTextAttributeValuesAttribute, $translatedTextValueObjectAttributesEditor;

	$editor = new RecordSetTableEditor($translatedTextAttributeValuesAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), true, false, $controller);
	$editor->addEditor(new TranslatedTextAttributeEditor($translatedTextAttributeAttribute, new SimplePermissionController(false), true, $levelDefinedMeaningName, $objectIdFetcher));
	$editor->addEditor(getTranslatedTextEditor($translatedTextValueAttribute, new TranslatedTextAttributeValueController(), $showRecordLifeSpan));
	$editor->addEditor(new PopUpEditor($translatedTextValueObjectAttributesEditor, 'Annotation'));

	addTableLifeSpanEditor($editor, $showRecordLifeSpan);

	return $editor;
}

function getExpressionMeaningsEditor($attribute, $allowAdd, $showRecordLifeSpan) {
	global
		$definedMeaningIdAttribute;
	
	$definedMeaningEditor = getDefinedMeaningEditor($showRecordLifeSpan);

	$definedMeaningCaptionEditor = new DefinedMeaningHeaderEditor($definedMeaningIdAttribute, new SimplePermissionController(false), true, 75);
	$definedMeaningCaptionEditor->setAddText("New exact meaning");

	$expressionMeaningsEditor = new RecordSetListEditor($attribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController($allowAdd), false, $allowAdd, new ExpressionMeaningController(), 3, false);
	$expressionMeaningsEditor->setCaptionEditor($definedMeaningCaptionEditor);
	$expressionMeaningsEditor->setValueEditor($definedMeaningEditor);
	
	return $expressionMeaningsEditor;
}

function getExpressionsEditor($spelling, $showRecordLifeSpan) {
	global
		$expressionMeaningsAttribute, $expressionExactMeaningsAttribute, $expressionApproximateMeaningsAttribute, $expressionAttribute, $languageAttribute, $expressionsAttribute;

	$expressionMeaningsRecordEditor = new RecordUnorderedListEditor($expressionMeaningsAttribute, 3);
	
	$exactMeaningsEditor = getExpressionMeaningsEditor($expressionExactMeaningsAttribute, true, $showRecordLifeSpan);
	$expressionMeaningsRecordEditor->addEditor($exactMeaningsEditor);
	$expressionMeaningsRecordEditor->addEditor(getExpressionMeaningsEditor($expressionApproximateMeaningsAttribute, false, $showRecordLifeSpan));
	
	$expressionMeaningsRecordEditor->expandEditor($exactMeaningsEditor);
	
	$expressionEditor = new RecordSpanEditor($expressionAttribute, ': ', ' - ');
	$expressionEditor->addEditor(new LanguageEditor($languageAttribute, new SimplePermissionController(false), true));

	$expressionsEditor = new RecordSetListEditor($expressionsAttribute, new SimplePermissionController(true), new ShowEditFieldChecker(true), new AllowAddController(true), false, false, new ExpressionController($spelling), 2, true);
	$expressionsEditor->setCaptionEditor($expressionEditor);
	$expressionsEditor->setValueEditor($expressionMeaningsRecordEditor);

	return $expressionsEditor;
}

function getDefinedMeaningEditor($showRecordLifeSpan) {
	global
		$definitionAttribute, $definedMeaningAttribute, $definedMeaningObjectAttributesEditor;
	
	$definitionEditor = getDefinitionEditor($definitionAttribute, new DefinedMeaningDefinitionController(), $showRecordLifeSpan);
	$classAttributesEditor = getClassAttributesEditor($showRecordLifeSpan);		
	$synonymsAndTranslationsEditor = getSynonymsAndTranslationsEditor($showRecordLifeSpan);
	$relationsEditor = getDefinedMeaningRelationsEditor($showRecordLifeSpan);
	$reciprocalRelationsEditor = getDefinedMeaningReciprocalRelationsEditor($showRecordLifeSpan);
	$classMembershipEditor = getDefinedMeaningClassMembershipEditor($showRecordLifeSpan);
	$collectionMembershipEditor = getDefinedMeaningCollectionMembershipEditor($showRecordLifeSpan);
	
	$definedMeaningEditor = new RecordUnorderedListEditor($definedMeaningAttribute, 4);
	$definedMeaningEditor->addEditor($definitionEditor);
	$definedMeaningEditor->addEditor($classAttributesEditor);
	$definedMeaningEditor->addEditor(getAlternativeDefinitionsEditor($showRecordLifeSpan));
	$definedMeaningEditor->addEditor($synonymsAndTranslationsEditor);
	$definedMeaningEditor->addEditor($relationsEditor);
	$definedMeaningEditor->addEditor($reciprocalRelationsEditor);
	$definedMeaningEditor->addEditor($classMembershipEditor);
	$definedMeaningEditor->addEditor($collectionMembershipEditor);

	$objectAttributeValuesEditor = $definedMeaningObjectAttributesEditor;	
	$definedMeaningEditor->addEditor($objectAttributeValuesEditor);

	$definedMeaningEditor->expandEditor($definitionEditor);
	$definedMeaningEditor->expandEditor($synonymsAndTranslationsEditor);

//		$definedMeaningEditor->expandEditor($relationsEditor);
//		$definedMeaningEditor->expandEditor($classMembershipEditor);
//		$definedMeaningEditor->expandEditor($collectionMembershipEditor);
//		$definedMeaningEditor->expandEditor($objectAttributeValuesEditor);

	return $definedMeaningEditor;
}

function createTableViewer($attribute) {
	return new RecordSetTableEditor($attribute, new SimplePermissionController(false), new ShowEditFieldChecker(true), new AllowAddController(false), false, false, null);
}

function createLanguageViewer($attribute) {
	return new LanguageEditor($attribute, new SimplePermissionController(false), false);
}

function createLongTextViewer($attribute) {
	$result = new TextEditor($attribute, new SimplePermissionController(false), false);
	
	return $result;
}

function createShortTextViewer($attribute) {
	return new ShortTextEditor($attribute, new SimplePermissionController(false), false);
}

function createBooleanViewer($attribute) {
	return new BooleanEditor($attribute, new SimplePermissionController(false), false, false);
}

function createDefinedMeaningReferenceViewer($attribute) {
	return new DefinedMeaningReferenceEditor($attribute, new SimplePermissionController(false), false);
}

function createSuggestionsTableViewer($attribute) {
	$result = createTableViewer($attribute);
	$result->setRowHTMLAttributes(array(
		"class" => "suggestion-row",
		"onclick" => "suggestRowClicked(event, this)",
		"onmouseover" => "mouseOverRow(this)",
		"onmouseout" => "mouseOutRow(this)"
	));
	
	return $result;
}

?>
