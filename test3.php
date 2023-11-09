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
        

// Récupérer les données du formulaire
$symptom = $_POST["symptom"];

// Créer la requête SparQL
$SPARQL_QUERY = '
SELECT ?disease ?diseaseLabel
WHERE {
  ?disease wdt:P31 wd:Q12136.
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],fr". }
}
ORDER BY ?diseaseLabel
';

$WIKIDATA_IMAGE = 'wdt:P18';

// Exécuter la requête SparQL
$results = $sparql->query($SPARQL_QUERY);

echo "<p>Liste des maladies</p>";
echo "<ul>";

foreach ($results as $row) {
    $diseaseURI = $row->disease->getUri();
    $diseaseLabel = $row->diseaseLabel;
    
    // Afficher le nom de la maladie comme un lien vers la page détaillée de la maladie
    echo "<li>";
    echo link_to_self($diseaseLabel, "disease=" . urlencode($diseaseURI));
    echo "</li>";
}

echo "</ul>";
/*

// Traitement de la sélection d'une maladie
if (isset($_GET['disease'])) {
    // Récupérer l'URI de la maladie à partir des paramètres de la requête
    $selectedDiseaseURI = urldecode($_GET['disease']);

    // Requête SPARQL pour récupérer des informations détaillées sur la maladie sélectionnée
    $diseaseDetailsQuery = '
    SELECT ?property ?value
    WHERE {
        <' . $selectedDiseaseURI . '> ?property ?value.
    }';

    // Exécuter la requête SPARQL pour les informations détaillées
    $diseaseDetails = $sparql->query($diseaseDetailsQuery);

    // Afficher les informations détaillées de la maladie
    echo "<h2>Informations détaillées sur la maladie</h2>";
    echo "<ul>";
    foreach ($diseaseDetails as $detail) {
        $propertyLabel = $detail->property->label('fr');
        $value = $detail->value;
        echo "<li>$propertyLabel : $value</li>";
    }
    echo "</ul>";
}*/


?>
