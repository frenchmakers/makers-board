<?php
// Variables définies par l'api
// $makerBoard : objet makerBoard
// $module : Informations générales sur le module
// $moduleConfig : Configuration du module en cours d'édition
// $moduleParams : Paramètres du module en cours d'édition
$images = isset($moduleParams['images']) ? $moduleParams['images'] : "";
?>
<div class="form-group">
    <label>Liste des images</label>
    <textarea name="images" class="form-control" rows="10"><?php echo(htmlentities($images)) ?></textarea>
</div>
