<?php

require_once realpath(__DIR__.'/')."/vendor/autoload.php";
require_once __DIR__."/html_tag_helpers.php";

    // Setup some additional prefixes for Wikidata
    \EasyRdf\RdfNamespace::set('wd', 'http://www.wikidata.org/entity/');
    \EasyRdf\RdfNamespace::set('wds', 'http://www.wikidata.org/entity/statement/');
    \EasyRdf\RdfNamespace::set('wdt', 'http://www.wikidata.org/prop/direct/');
    \EasyRdf\RdfNamespace::set('p', 'http://www.wikidata.org/prop/');
    \EasyRdf\RdfNamespace::set('wikibase', 'http://wikiba.se/ontology#');

// Connexion à WikiData
$SPARQL_ENDPOINT = 'https://query.wikidata.org/sparql';
$sparql = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);


$diseaseID = $_POST['diseaseID'];

// Query Wikidata for information about the disease in English
$SPARQL_QUERY = '
SELECT (COALESCE(?diseaseDescription, "") AS ?diseaseDescription)
WHERE {
  wd:'.$diseaseID.' schema:description ?diseaseDescription.
  FILTER (lang(?diseaseDescription) = "en").
}
';

$results = $sparql->query($SPARQL_QUERY);
$result = $results->current();

if ($result->diseaseDescription != "") {
    echo $result->diseaseDescription->getValue();
} else {
    echo "Pas de description en anglais disponible";
}
