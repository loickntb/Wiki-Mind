<?php
// ajax_search.php

// ... (votre code PHP pour la connexion à Wikidata)

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


if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];

    // Modifier la requête SPARQL pour effectuer une recherche
    $SPARQL_QUERY = "
    SELECT ?disease ?diseaseLabel 
    WHERE {
      ?disease dbp:field dbr:Psychiatry.
      ?disease a dbo:Disease.
      ?disease rdfs:label ?diseaseLabel.
      FILTER(LANG(?diseaseLabel) = 'fr' && CONTAINS(LCASE(?diseaseLabel), LCASE('$searchTerm'))).
    }
    ORDER BY ?diseaseLabel
    LIMIT 5
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

        $diseaseURL = 'disease_dbp.php?diseaseID=' . $diseaseQ;

        echo '<li><a href="' . $diseaseURL . '">' . $diseaseLabel->getValue() . '</a></li>';
    }
    echo '</ul>';
}
?>
