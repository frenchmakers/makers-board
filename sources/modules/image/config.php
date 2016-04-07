<?php
require_once __DIR__."/../../makerBoard.class.php";
$makerBoard = new makerBoard();
$makerBoard->init();
$config = $makerBoard->findModule("image");
?>
<div class="form-group">
    <label>Liste des images</label>
    <textarea name="images" class="form-control"></textarea>
</div>
