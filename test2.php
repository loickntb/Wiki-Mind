<?php

//require 'vendor/autoload.php';

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
        


// Créer la requête SparQL
$SPARQL_QUERY = '
SELECT ?disease ?diseaseLabel 
WHERE {
  ?disease a dbo:Disease.
  ?disease rdfs:label ?diseaseLabel.
  FILTER (lang(?diseaseLabel) = "fr").
}
';

$WIKIDATA_IMAGE = 'wdt:P18';

// Exécuter la requête SparQL
$results = $sparql->query($SPARQL_QUERY);

// Afficher les noms des maladies
echo '<ul>';

foreach ($results as $row) {
    $disease =$row->disease;
    $diseaseLabel = $row->diseaseLabel;
    $parts = explode("/",$disease);
    $diseaseQ = $parts[4];

    $diseaseURL = 'disease_dbp.php?diseaseID=' . $diseaseQ;

    echo '<li><a href="' . $diseaseURL . '">' . $diseaseLabel->getValue() . '</a></li>';
}

echo '</ul>';

?>
