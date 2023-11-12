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
    \EasyRdf\RdfNamespace::set('a', 'http://www.w3.org/2005/Atom');
    \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
    \EasyRdf\RdfNamespace::set('dbr', 'http://dbpedia.org/resource/');
    \EasyRdf\RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    \EasyRdf\RdfNamespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');

// Connexion à WikiData
$SPARQL_ENDPOINT = 'https://dbpedia.org/sparql';
$sparql = new \EasyRdf\Sparql\Client($SPARQL_ENDPOINT);

$WIKIDATA_IMAGE = 'dbo:thumbnail';


$diseaseID = $_GET['diseaseID'];

//echo $diseaseID;

// Query Wikidata for information about the disease
$SPARQL_QUERY = '
SELECT  ?diseaseLabel ?diseaseDescription
WHERE {
  dbr:'.$diseaseID.' rdfs:label ?diseaseLabel.
  FILTER (lang(?diseaseLabel) = "fr").
  OPTIONAL {
    dbr:'.$diseaseID.' dbo:abstract ?diseaseDescription.
    FILTER (lang(?diseaseDescription) = "fr").
}
}
';


$SYMPTOMS_QUERY = '
SELECT  ?symptom ?symptomLabel
WHERE {
  dbr:'.$diseaseID.' dbo:symptom ?symptom.
  ?symptom rdfs:label ?symptomLabel.
  FILTER(LANG(?symptomLabel) = "fr")
}
';

$results = $sparql->query($SPARQL_QUERY);

$result = $results->current();

$disease = $result->diseaseLabel->getValue();

$symptoms = $sparql->query($SYMPTOMS_QUERY);


if($result->diseaseDescription != "")  {
  $diseaseDescription = $result->diseaseDescription->getValue();
}
else{
  $diseaseDescription = "Pas de description en français disponible";
}
$graph = \EasyRdf\Graph::newAndLoad("dbr:$diseaseID", 'turtle');

$maladie = $graph->resource("dbr:$diseaseID");





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

echo '<p> Symptômes : </p>';

echo '<ul>';
if (count($symptoms) > 0) {
  foreach($symptoms as $row){

    $symptom = $row->symptom;
    $symptomLabel = $row->symptomLabel;
    $parts = explode("/", $symptom);
    $symptomQ = $parts[4];

    $symptomURL = 'disease_dbp.php?diseaseID=' . $symptomQ;

    echo '<li><a href="' . $symptomURL . '">' . $symptomLabel->getValue() . '</a></li>';  

  }
}
else{
  echo '<p id = "originalSymptom" > Pas de symptômes décrits en français </p>';
  echo '<p id="loadEnglishSymptom" style="color: blue; cursor: pointer; text-decoration: underline;">Charger la description en anglais</p>';
  echo '<p id="englishSymptom" style="display: none;"></p>';
}
echo '</ul>';



?>


    </div>

    <script>
        // Gérer le clic sur le texte pour charger la description en anglais
        $("#loadEnglishDescription").on("click", function() {
            // Effectuer une requête asynchrone pour récupérer la description en anglais
            $.ajax({
                url: "load_english_dbp.php", // Remplacez cela par le chemin réel de votre fichier PHP pour charger la description en anglais
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

<script>
        // Gérer le clic sur le texte pour charger la description en anglais
        $("#loadEnglishSymptom").on("click", function() {
            // Effectuer une requête asynchrone pour récupérer la description en anglais
            $.ajax({
                url: "load_english_symptom_dbp.php", // Remplacez cela par le chemin réel de votre fichier PHP pour charger la description en anglais
                method: "POST",
                data: { diseaseID: <?php echo json_encode($diseaseID); ?> }, // Passer l'identifiant de la maladie au fichier PHP
                success: function(englishDescription) {
                    // Mettre à jour le contenu de la description en anglais
                    $("#englishSymptom").html(englishDescription);
                    // Afficher la description en anglais et masquer le texte cliquable
                    $("#englishSymptom").show();
                    $("#loadEnglishSymptom").hide();
                    $("#originalSymptom").hide();
                }
            });
        });
    </script>

</body>
</html>
