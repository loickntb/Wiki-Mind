<?php

//require 'vendor/autoload.php';

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
        


// Créer la requête SparQL
$SPARQL_QUERY = '
SELECT ?disease ?diseaseLabel ?diseaseQ
WHERE {
  ?disease wdt:P31 wd:Q112965645.
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],fr". }
}
ORDER BY ?diseaseLabel
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

    $diseaseURL = 'disease.php?diseaseID=' . $diseaseQ;

    echo '<li><a href="' . $diseaseURL . '">' . $diseaseLabel->getValue() . '</a></li>';
}

echo '</ul>';

?>
