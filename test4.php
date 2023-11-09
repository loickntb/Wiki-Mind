<?php

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

// Afficher le formulaire de recherche
echo '
<form method="post" action="">
    <label for="search">Rechercher une maladie : </label>
    <input type="text" id="search" name="search" required>
</form>
';

// Afficher la liste mise à jour avec JavaScript
echo '<div id="results-container"></div>';

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
        url: "ajax_search.php",
        method: "POST",
        data: { search: searchTerm },
        success: function(data) {
            $("#results-container").html(data);
        }
    });
}
</script>
