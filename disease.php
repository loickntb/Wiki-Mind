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

$WIKIDATA_IMAGE = 'wdt:P18';


$diseaseID = $_GET['diseaseID'];

//echo $diseaseID;

// Query Wikidata for information about the disease
$SPARQL_QUERY = '
SELECT ?diseaseLabel (COALESCE(?diseaseDescription, "") AS ?diseaseDescription)
WHERE {
  wd:'.$diseaseID.' rdfs:label ?diseaseLabel.
  FILTER (lang(?diseaseLabel) = "fr").
  OPTIONAL {
    wd:'.$diseaseID.' schema:description ?diseaseDescription.
    FILTER (lang(?diseaseDescription) = "fr").
  }
}

';

$results = $sparql->query($SPARQL_QUERY);

$result = $results->current();

$disease = $result->diseaseLabel->getValue();

if($result->diseaseDescription != "")  {
  $diseaseDescription = $result->diseaseDescription->getValue();
}
else{
  $diseaseDescription = "Pas de description disponible";
}
$graph = \EasyRdf\Graph::newAndLoad("wd:$diseaseID", 'turtle');

$maladie = $graph->resource("wd:$diseaseID");

if ($maladie->get($WIKIDATA_IMAGE)) {
    print image_tag(
        $maladie->get($WIKIDATA_IMAGE),
        array('style'=>'max-width:400px;max-height:250px;margin:10px;float:right')
    );
}



// Display information about the disease
echo '<h1>' . $disease. '</h1>';

echo '<p>' . $diseaseDescription. '</p>';



?>
