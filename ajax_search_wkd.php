<?php
// ajax_search.php

// ... (votre code PHP pour la connexion à Wikidata)

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


if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];

    // Modifier la requête SPARQL pour effectuer une recherche
    $SPARQL_QUERY = "
    SELECT ?disease ?diseaseLabel ?diseaseQ
    WHERE {
      ?disease wdt:P31 wd:Q112965645.
      ?disease rdfs:label ?diseaseLabel.
      FILTER(LANG(?diseaseLabel) = 'fr' && CONTAINS(LCASE(?diseaseLabel), LCASE('$searchTerm'))).
      SERVICE wikibase:label { bd:serviceParam wikibase:language '[AUTO_LANGUAGE],fr'. }
    }
    ORDER BY ?diseaseLabel
    LIMIT 10
    ";

    // Exécuter la requête SPARQL avec la nouvelle requête
    $results = $sparql->query($SPARQL_QUERY);

    // Afficher les résultats dans une liste
    echo '<ul>';
    foreach ($results as $row) {
        $disease = $row->disease;
        $diseaseLabel = $row->diseaseLabel;
        $parts = explode("/", $disease);
        $diseaseQ = $parts[4];

        $diseaseURL = 'disease_wkd.php?diseaseID=' . $diseaseQ;

        echo '<li><a href="' . $diseaseURL . '">' . $diseaseLabel->getValue() . '</a></li>';
    }
    echo '</ul>';
}
?>
