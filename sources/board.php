<?php
// Initialisation 
include 'makerBoard.class.php';
$makerBoard = new makerBoard();
    
// Extraction des informations
$cmd = $_GET["cmd"];

// Commande pour le rafraîchissement
if($cmd == "refresh"){
    echo($makerBoard->getLastRefresh());
}
