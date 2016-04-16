<?php
// Initialisation
include 'makerBoard.class.php';
$makerBoard = new makerBoard();

// Traitement des commandes
$message = "";
$command = isset($_REQUEST["command"]) ? $_REQUEST["command"] : "";
if ($command === "refresh-dashboard") {
    $makerBoard->setLastRefresh();
    $message = "L'ordre de rafraichissement est lancé.";
}

// Chargement des modules
$makerBoard->init();
$board = $makerBoard->readBoardConfig();
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
            <div class="messages">
            <?php
            if ($message !== "") {
                ?>
                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
                    <?php echo($message); ?>
                </div>
                <?php
            }
            ?>
            </div>
            
            <h2>Informations générales</h2>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Tableau</label>
                        <select class="form-control">
                            <option value="default">default: Makers board</option>
                        </select>
                        <label>Titre du tableau</label>
                        <input type="text" name="boardTitle" class="form-control" value="Makers board" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">                        
                            Commandes
                        </div>
                        <div class="panel-body">
                            <ul>
                                <li><a href="config.php?command=refresh-dashboard">Actualiser le tableau de bord</a></li>
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
                        <span id="current-screen-size" class="form-control-static">1234x789</span>
                    </div>
                    <div class="board-editor">
                        <div class="module" data-id="1" data-module="module1">
                            <div class="module-title">Titre du module</div>
                        </div>
                        <div class="module" data-id="2" data-module="module2">
                            <div class="module-title">Titre du module</div>
                        </div>
                        <div class="module" data-id="3" data-module="module1">
                            <div class="module-title">Titre du module</div>
                        </div>
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
                                foreach ($makerBoard->configs as $module => $config) {
                                    echo("<li class='module' data-module='{$config['code']}' data-w='{$config['w']}' data-h='{$config['h']}'><a href='#'>{$config['name']}</a></li>");
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
        <script type="text/javascript">
            (function($){
                // Gestion de la taille d'écran
                var sw = $("body").width();
                var sh = $("body").height();
                $("#current-screen-size").attr({
                    "data-width": sw,
                    "data-height": sh,
                }).text(sw+"x"+sh);
                $("#screen-size").change(function(){
                    var $opt = $("option:selected", this);
                    if($opt.val()!="current"){
                        //console.log($(".board-editor").width(), $(".board-editor").height());
                        //console.log($opt.data("w"), $opt.data("h"));
                        var bw = $(".board-editor").width() / $opt.data("w");
                        var bh = $opt.data("h") * bw;
                        //console.log(bw, bh);
                         $(".board-editor").height(bh);
                         $(".board-editor").board("save");
                    }else{
                        
                    } 
                });
                
                // Activation du board en mode edition
                $(".board-editor").board({
                    refresh: false,
                    editable: true
                }).board("refresh", {updateBoardSize: true});
                
            })(jQuery);
        </script>
    </body>
</html>
