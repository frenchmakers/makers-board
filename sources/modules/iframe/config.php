<?php
// Variables définies par l'api
// $makerBoard : objet makerBoard
// $module : Informations générales sur le module
// $moduleConfig : Configuration du module en cours d'édition
// $moduleParams : Paramètres du module en cours d'édition
$url = isset($moduleParams['url']) ? $moduleParams['url'] : "";
?>
<div class="form-group">
    <label>URL affiché</label>
    <input name="url" class="form-control" ><?php echo(htmlentities($url)) ?></input>
</div>
