<?php

require_once realpath(__DIR__.'/')."/vendor/autoload.php";
require_once __DIR__."/html_tag_helpers.php";

    // Setup some additional prefixes for Wikidata
    \EasyRdf\RdfNamespace::set('a', 'http://www.w3.org/2005/Atom');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbr', 'http://dbpedia.org/resource/');
    \EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

// Connexion à WikiData
$SPARQL_ENDPOINT = 'https://dbpedia.org/sparql';
$sparql = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);


$diseaseID = $_POST['diseaseID'];

// Query Wikidata for information about the disease in English
$SPARQL_QUERY = '
SELECT  ?symptom_en
WHERE {
  dbr:'.$diseaseID.' dbp:symptoms ?symptom_en.
}
';

$results = $sparql->query($SPARQL_QUERY);
$result = $results->current();

if ($result->symptom_en != "") {
    echo $result->symptom_en->getValue();
} else {
    echo "Pas de symptômes en anglais disponible";
}
