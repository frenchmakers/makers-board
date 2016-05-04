<?php
// Variables définies par l'api
// $makerBoard : objet makerBoard
// $module : Informations générales sur le module
// $moduleConfig : Configuration du module en cours d'édition
// $moduleParams : Paramètres du module en cours d'édition

$url = isset($moduleParams['url']) ? $moduleParams['url'] : "";
?>
<iframe style="width:100%;height:100%;border:none;" src="<?php echo($url) ?>"></iframe>
