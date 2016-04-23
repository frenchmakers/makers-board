<?php
// Initialisation
include 'makerBoard.class.php';
$makerBoard = new makerBoard();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Configuration MakerBoard</title>
        
        <!-- Bootstrap -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/editor.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="container">
            <h1>Configuration Maker Board</h1>
            <div class="messages-template hidden">
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>                
            </div>
            <div class="messages">
            </div>
            
            <h2>Informations générales</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Tableau</label>
                        <select id="current-board" class="form-control">
                            <option value="default">default: Makers board</option>
                        </select>
                        <label>Titre du tableau</label>
                        <input id="board-title" name="boardTitle" type="text" class="form-control" value="Makers board" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">                        
                            Commandes
                        </div>
                        <div class="panel-body">
                            <ul>
                                <li><a href="#" class="refresh-all-command">Actualiser tous les tableaux de bord</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <h2>Organisation des modules</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-inline">
                        <label>Ecran de référence</label>
                        <select id="screen-size" class="form-control">
                            <option value="800x600" data-w="800" data-h="600">800x600</option>
                            <option value="1024x768" data-w="1024" data-h="768">1024x768</option>
                            <option value="1280x768" data-w="1280" data-h="768">1280x768</option>
                            <option value="1280x800" data-w="1280" data-h="800">1280x800</option>
                            <option value="1360x768" data-w="1360" data-h="768">1360x768</option>
                            <option value="1440x900" data-w="1440" data-h="900">1440x900</option>
                            <option value="1600x900" data-w="1600" data-h="900">1600x900</option>
                            <option value="current">Taille actuelle</option>
                        </select>
                        <label>Taille actuelle de l'affichage</label>
                        <span id="current-screen-size" class="form-control-static"></span>
                    </div>
                    <div class="board-editor">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default modules">
                        <div class="panel-heading">                        
                            Modules
                        </div>
                        <div class="panel-body">
                            <ul>
                                <?php
                                foreach ($makerBoard->getModules() as $module) {
                                    $isConfig = $module['config'] == true ? "true" : "false";
                                    echo(<<<HTML
<li>
    <a href="#" class="add-module-command" data-module="{$module['code']}" data-title="{$module['name']}" data-config="$isConfig">
        {$module['name']}
    </a>
</li>
HTML
);
                                } 
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="configModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Configuration</h4>
                </div>
                <div class="modal-body">
                    <p>One fine body&hellip;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary">Valider</button>
                </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
                
        <script src="assets/js/jquery-1.12.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="assets/js/board.js"></script>
        <script src="assets/js/board-editor.js"></script>
    </body>
</html>
