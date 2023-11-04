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
$WIKIDATA_POINT = 'wdt:P625';

// Exécuter la requête SparQL
$results = $sparql->query($SPARQL_QUERY);

/*
// Récupérer les résultats de la requête
$diseases = array();
foreach ($results->getResults() as $result) {
  $diseases[] = $result["disease"]["value"];
}

// Afficher les résultats
echo "<ul>";
foreach ($diseases as $disease) {
  echo "<li>$disease</li>";
}
echo "</ul>";
*/
if (isset($_REQUEST['id'])) {
  $id = $_REQUEST['id'];
  $graph = \EasyRdf\Graph::newAndLoad("wd:$id", 'turtle');

  $disease = $graph->resource("wd:$id");

  if ($village->get($WIKIDATA_IMAGE)) {
      print image_tag(
          $village->get($WIKIDATA_IMAGE),
          array('style'=>'max-width:400px;max-height:250px;margin:10px;float:right')
      );
  }
  print content_tag('h2',$village->label('en'));
  print content_tag('p', $village->get('schema:description', null, 'en'));


  print content_tag('h3', "Pages about " . $village->label('en'));
  print "<ul>\n";
  foreach ($graph->all($village, "^schema:about") as $doc) {
      print '<li>'.link_to($doc)."</li>\n";
  }
  print "</ul>\n";

  echo "<br /><br />";
  echo $village->dump();
} else {
  print "<p>List of villages in Fife.</p>";
  $sparql = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);
  $results = $sparql->query($SPARQL_QUERY);

  print "<ul>\n";
  foreach ($results as $row) {
    if (preg_match("|/(Q\d+)|", $row->item, $matches)) {
      print '<li>'.link_to_self($row->itemLabel, "id=".$matches[1])."</li>\n";
    }
  }
  print "</ul>\n";
}

?>
