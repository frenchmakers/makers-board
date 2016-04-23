<?php
// Variables définies par l'api
// $makerBoard : objet makerBoard
// $module : Informations générales sur le module
// $moduleConfig : Configuration du module en cours d'édition
// $moduleParams : Paramètres du module en cours d'édition

$images = explode("\n", $moduleParams['images']);
foreach ($images as $image) {
    if(trim($image)=="") continue;
?>
<img style="width:100%;height:auto" src="<?php echo($image) ?>">
</img> 
<?php
}
?>
