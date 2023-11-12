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


    // Modifier la requête SPARQL pour effectuer une recherche
    $SPARQL_QUERY = "
    SELECT ?disease  ?image
    WHERE {
      ?disease dbp:field dbr:Psychiatry.
    ?disease a dbo:Disease.
    ?disease dbo:thumbnail ?image.
    }
    ";


    // Exécuter la requête SPARQL avec la nouvelle requête
    $results = $sparql->query($SPARQL_QUERY);
    $result = $results->current();







//require 'vendor/autoload.php';

// Afficher l'en-tête HTML avec la liaison vers le fichier CSS
echo '<!DOCTYPE html>';
echo '<html lang="fr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="search.css">'; // Lien vers votre fichier CSS
echo '</head>';
echo '<body>';

// Afficher l'image en haut à gauche
echo '<img src="/images/logo.png" alt="Logo" style="position: absolute; top: 0; left: 0; margin: 10px; width: 125px; height: auto;">';

// Afficher le formulaire de recherche
echo '
<form method="post" action="">
    <label for="search">Rechercher une maladie : </label>
    <input type="text" id="search" name="search" required>
</form>
';

// Afficher la liste mise à jour avec JavaScript
echo '<div id="results-container"></div>';


    // Afficher les résultats dans une liste
    foreach ($results as $row) {
        $disease = $row->disease;
        $parts = explode("/", $disease);
        $diseaseQ = $parts[4];

        $graph = \EasyRdf\Graph::newAndLoad("dbr:$diseaseQ", 'turtle');
        $maladie = $graph->resource("dbr:$diseaseQ");

        if ($maladie->get($WIKIDATA_IMAGE)) {
            /*
            print image_tag(
                $maladie->get($WIKIDATA_IMAGE),
                array('style'=>'max-width:400px;max-height:250px;margin:10px;float:right')
            );*/
            echo '<img src='. $maladie->get($WIKIDATA_IMAGE) .' alt="img" >';
        }
    }


echo '</body>';
echo '</html>';

?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
var typingTimer;
var doneTypingInterval = 500; // Attendez 500 ms après que l'utilisateur a cessé de taper

$("#search").on("input", function() {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(performSearch, doneTypingInterval);
});

function performSearch() {
    var searchTerm = $("#search").val();
    $.ajax({
        url: "ajax_search_dbp.php",
        method: "POST",
        data: { search: searchTerm },
        success: function(data) {
            $("#results-container").html(data);
        }
    });
}
</script>
