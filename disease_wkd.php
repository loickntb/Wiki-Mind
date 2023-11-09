<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiki-Mind</title>
    <link rel="stylesheet" href="page.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

    <div class="container">

        
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
  $diseaseDescription = "Pas de description en français disponible";
}
$graph = \EasyRdf\Graph::newAndLoad("wd:$diseaseID", 'turtle');

$maladie = $graph->resource("wd:$diseaseID");





// Display information about the disease
echo '<h1>' . $disease. '</h1>';

if ($maladie->get($WIKIDATA_IMAGE)) {
  print image_tag(
      $maladie->get($WIKIDATA_IMAGE),
      array('style'=>'max-width:400px;max-height:250px;margin:10px;float:right')
  );
}
else{
  echo '<p> Pas d\'image disponible</p>';
}


// Vérifier si la description en français n'est pas disponible
if ($diseaseDescription == "Pas de description en français disponible") {
  // Ajouter un texte cliquable pour charger la description en anglais
  echo '<p id="originalDescription">' . $diseaseDescription . '</p>';
  echo '<p id="loadEnglishDescription" style="color: blue; cursor: pointer; text-decoration: underline;">Charger la description en anglais</p>';
  echo '<p id="englishDescription" style="display: none;"></p>';
} else {
  // Afficher la description en français
  echo '<p>' . $diseaseDescription . '</p>';
}



?>


    </div>

    <script>
        // Gérer le clic sur le texte pour charger la description en anglais
        $("#loadEnglishDescription").on("click", function() {
            // Effectuer une requête asynchrone pour récupérer la description en anglais
            $.ajax({
                url: "load_english_wkd.php", // Remplacez cela par le chemin réel de votre fichier PHP pour charger la description en anglais
                method: "POST",
                data: { diseaseID: <?php echo json_encode($diseaseID); ?> }, // Passer l'identifiant de la maladie au fichier PHP
                success: function(englishDescription) {
                    // Mettre à jour le contenu de la description en anglais
                    $("#englishDescription").html(englishDescription);
                    // Afficher la description en anglais et masquer le texte cliquable
                    $("#englishDescription").show();
                    $("#loadEnglishDescription").hide();
                    $("#originalDescription").hide();
                }
            });
        });
    </script>

</body>
</html>
