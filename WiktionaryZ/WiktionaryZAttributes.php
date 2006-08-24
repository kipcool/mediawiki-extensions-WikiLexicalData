<?php

require_once("Attribute.php");

global
	$languageAttribute, $spellingAttribute, $textAttribute;

$languageAttribute = new Attribute("language", "Language", "language");
$spellingAttribute = new Attribute("spelling", "Spelling", "spelling");
$textAttribute = new Attribute("text", "Text", "text");

global
	$expressionIdAttribute, $identicalMeaningAttribute;
	
$expressionIdAttribute = new Attribute("expression-id", "Expression Id", "expression-id");
$identicalMeaningAttribute = new Attribute("indentical-meaning", "Identical meaning?", "boolean");

global
	$expressionAttribute;
	
$expressionAttribute = new Attribute("expression", "Expression", new RecordType(new Structure($languageAttribute, $spellingAttribute)));

global
	$collectionAttribute, $sourceIdentifierAttribute;

$collectionAttribute = new Attribute("collection", "Collection", "collection");
$sourceIdentifierAttribute = new Attribute("source-identifier", "Source identifier", "short-text"); 

global
	$collectionMembershipAttribute;

$collectionMembershipAttribute = new Attribute("collection-membership", "Collection membership", new RecordSetType(new Structure($collectionAttribute, $sourceIdentifierAttribute)));

global
	 $classAttribute;
	 
$classAttribute = new Attribute("class", "Class", "class");
	
global
	$classMembershipAttribute;

$classMembershipAttribute = new Attribute("class-membership", "Class membership", new RecordSetType(new Structure($classAttribute)));

global
	$relationTypeAttribute, $otherDefinedMeaningAttribute;
	
$relationTypeAttribute = new Attribute("relation-type", "Relation type", "relation-type"); 
$otherDefinedMeaningAttribute = new Attribute("other-defined-meaning", "Other defined meaning", "defining-expression");

global
	$relationsAttribute, $relationStructure;
	
$relationStructure = new Structure($relationTypeAttribute, $otherDefinedMeaningAttribute);	
$relationsAttribute = new Attribute("relations", "Relations", new RecordSetType($relationStructure));

global
	$translatedTextIdAttribute, $translatedTextStructure;
	
$translatedTextIdAttribute = new Attribute("translated-text-id", "Translated text ID", "integer");	
$translatedTextStructure = new Structure($languageAttribute, $textAttribute);	

global
	$definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute;

$definitionIdAttribute = new Attribute("definition-id", "Definition identifier", "integer");
$alternativeDefinitionAttribute = new Attribute("alternative-definition", "Alternative definition", new RecordSetType($translatedTextStructure));
$sourceAttribute = new Attribute("source-id", "Source", "defined-meaning");

global
	$alternativeDefinitionsAttribute;
	
$alternativeDefinitionsAttribute = new Attribute("alternative-definitions", "Alternative definitions", new RecordSetType(new Structure($definitionIdAttribute, $alternativeDefinitionAttribute, $sourceAttribute)));

global
	$definitionAttribute, $synonymsAndTranslationsAttribute;
	
$definitionAttribute = new Attribute("definition", "Definition", new RecordSetType($translatedTextStructure));
$synonymsAndTranslationsAttribute = new Attribute("synonyms-translations", "Synonyms and translations", new RecordSetType(new Structure($expressionIdAttribute, $expressionAttribute, $identicalMeaningAttribute)));

global
	$definedMeaningIdAttribute;

$definedMeaningIdAttribute = new Attribute("defined-meaning-id", "Defined meaning identifier", "defined-meaning-id");

global
	$textValueIdAttribute, $textAttributeAttribute, $textValueAttribute, $textAttributeValuesAttribute, $textAttributeValuesStructure;
	
$textAttributeAttribute = new Attribute("text-attribute", "Text attribute", "text-attribute");
$textValueIdAttribute = new Attribute("text-value-id", "Text value identifier", "text-value-id");
$textValueAttribute = new Attribute("text-value", "Text value", new RecordSetType($translatedTextStructure));

$textAttributeValuesStructure = new Structure($textAttributeAttribute, $textValueIdAttribute, $textValueAttribute);
$textAttributeValuesAttribute = new Attribute("text-attribute-values", "Text attribute values", new RecordSetType($textAttributeValuesStructure));

global
	$definedMeaningAttribute;
		
$definedMeaningAttribute = new Attribute("defined-meaning", "Defined meaning", new RecordType(new Structure($definitionAttribute, $alternativeDefinitionsAttribute, $synonymsAndTranslationsAttribute, $relationsAttribute, $classMembershipAttribute, $collectionMembershipAttribute, $textAttributeValuesAttribute)));

global
	$expressionsAttribute, $expressionMeaningsAttribute;
	
$expressionMeaningsAttribute = new Attribute("expression-meanings", "Defined meanings", new RecordSetType(new Structure($definedMeaningIdAttribute, $textAttribute, $definedMeaningAttribute)));
$expressionsAttribute = new Attribute("expressions", "Expressions", new RecordSetType(new Structure($expressionIdAttribute, $expressionAttribute, $expressionMeaningsAttribute)));

?>
