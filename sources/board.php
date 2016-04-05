<?php 

// Initialisation 
include 'makerBoard.class.php';
$makerBoard = new makerBoard();

// Extraction des informations
$cmd = $_REQUEST["cmd"];

// Commande pour le rafraîchissement
if ($cmd == "refresh") {
    echo($makerBoard->getLastRefresh());
} else if ($cmd === "save-board-layout") {
    $layout = isset($_REQUEST["layout"]) ? $_REQUEST["layout"] : "";
    if ($layout != "") {
        $makerBoard->writeBoardLayout($layout);
        $makerBoard->setLastRefresh();
    }
}
