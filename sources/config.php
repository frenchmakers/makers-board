<?php

// Traitement des commandes
$message = "";
$command = $_GET["command"];
if ($command === "refresh-dashboard") {
    $file = __DIR__ . "/datas/refresh-dashboard";
    file_put_contents($file, time());
    $message = "L'ordre de rafraichissement est lancé.";
}

// Chargement des modules
include 'makerBoard.class.php';
$makerBoard = new makerBoard();
$makerBoard->init();
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MakerBoard</title>
        <!-- Bootstrap -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/jquery.gridster.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/config.css" rel="stylesheet" type="text/css"/>
        <script src="assets/js/jquery-1.12.2.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery.gridster.min.js" type="text/javascript"></script>
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
            <div class="row">
                <div class="col-md-8">
                    <div class="gridster">
                        <ul>
                            <li data-sizey="2" data-sizex="2" data-col="4" data-row="1">
                                <div class="gridster-box">
                                    <div class="display">a</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="1" data-row="3">
                                <div class="gridster-box">
                                    <div class="display">b</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="2" data-row="3">
                                <div class="gridster-box">
                                    <div class="display">c</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="2" data-sizex="2" data-col="1" data-row="1">
                                <div class="gridster-box">
                                    <div class="display">d</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="3" data-row="1">
                                <div class="gridster-box">
                                    <div class="display">e</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="3" data-row="3">
                                <div class="gridster-box">
                                    <div class="display">f</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="4" data-row="3">
                                <div class="gridster-box">
                                    <div class="display">g</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="6" data-row="1">
                                <div class="gridster-box">
                                    <div class="display">h</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="5" data-row="3">
                                <div class="gridster-box">
                                    <div class="display">i</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="3" data-row="2">
                                <div class="gridster-box">
                                    <div class="display">j</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                            <li data-sizey="1" data-sizex="1" data-col="6" data-row="2">
                                <div class="gridster-box">
                                    <div class="display">k</div>
                                    <a href="#" class="handle-close">&times;</a>
                                </div>
                            </li>
                        </ul>
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
                    <div class="panel panel-default modules">
                        <div class="panel-heading">                        
                            Modules
                        </div>
                        <div class="panel-body">
                            <ul>
                                <?php
                                foreach ($makerBoard->configs as $module => $config) {
                                    echo("<li class='module' data-module='{$config['code']}'><a href='#'>{$config['name']}</a></li>");
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="template-module hidden">
            <div class="gridster-box" data-module="{{module}}">
                <div class="module-title">{{title}}</div>
                <div class="module-content"></div>
                <a href="#" class="handle-close">&times;</a>
            </div>            
        </div>
        <script type="text/javascript">
            (function($){
                var gridster = null;
                //$(document).ready(function () {
                gridster = $(".gridster ul").gridster({
                    widget_base_dimensions: ['auto', 100],
                    autogenerate_stylesheet: true,
                    min_cols: 1,
                    max_cols: 8,
                    min_rows: 4,
                    max_rows: 4,
                    widget_margins: [5, 5],
                    serialize_params: function($w, wgd) { 
                        return { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y, module: $w.data("module") } 
                    },
                    resize: {
                        enabled: false
                    },
                    draggable:{
                        stop: function(e, ui){
                            alert( JSON.stringify(gridster.serialize()) );
                        }
                    }
                }).data('gridster');
                // Gestion de la suppression d'un module
                $(".gridster .handle-close").click(function(e){
                    e.preventDefault();
                    gridster.remove_widget( $(this).parents("li").first() );
                });
                // Gestion de l'ajout d'un module
                $(".modules li a").click(function(e){
                    e.preventDefault();
                    var parent = $(this).parents("li").first();
                    var module = parent.data("module");
                    if(!module || module==='') { return ;}
                    var template = $(".template-module").html();
                    template = template
                           .replace("{{module}}", module)
                           .replace("{{title}}", $(this).text())
                    ;
                    var mod = gridster.add_widget( $("<li></li>").attr("data-module", module).append(template) );
                    $(".handle-close", mod).click(function(e){
                       e.preventDefault();
                       gridster.remove_widget( $(this).parents("li").first() );
                    });
                });
            })(jQuery);
        </script>
    </body>
</html>
