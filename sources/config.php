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
                    <h2>Informations générales</h2>
                    <form action="config.php" action="post">
                        <div class="form-group">
                            <label>Titre général</label>
                            <input type="text" name="title" class="form-control" value="<?php echo($makerBoard->title) ?>" />
                            <label>Titre du tableau</label>
                            <input type="text" name="boardTitle" class="form-control" value="<?php echo($board["title"]) ?>" />
                        </div>
                    </form>            
                    
                    <h2>Organisation des modules</h2>
                    <div>
                        <a href="#" class="cmd-add-separator">Ajouter un séparateur</a>
                    </div>
                    <div class="gridster editor">
                        <ul>
                            <?php
                            foreach ($board["layout"] as $elm) {
                                if($elm["type"]=="module"){
                                    $module = $makerBoard->findModule($elm['module']);
                                }
                            ?>
                                <li 
                                    data-sizey="<?php echo($elm["size_y"]) ?>" 
                                    data-sizex="<?php echo($elm["size_x"]) ?>" 
                                    data-col="<?php echo($elm["col"]) ?>" 
                                    data-row="<?php echo($elm["row"]) ?>" 
                                    data-type="<?php echo($elm["type"]) ?>" 
                                    <?php echo(isset($elm["module"]) ? "data-module='".$elm["module"]."'" : "") ?>
                                    >
                                    <?php if($elm["type"]=="module"){ ?>
                                    <div class="gridster-box" data-module="<?php echo($elm["module"]) ?>">
                                        <div class="module-title"><?php echo($module['name']) ?></div>
                                        <div class="module-content"></div>
                                        <a href="#" class="handle-close">&times;</a>
                                    </div>
                                    <?php } else { ?>
                                    <div class="gridster-box separator">
                                        <a href="#" class="handle-close">&times;</a>
                                    </div>            
                                    <?php } ?>
                                </li>                                
                            <?php 
                            }
                            ?>
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
                                    echo("<li class='module' data-module='{$config['code']}' data-w='{$config['w']}' data-h='{$config['h']}'><a href='#'>{$config['name']}</a></li>");
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="panel panel-default log">
                        <div class="panel-heading">                        
                            Log
                        </div>
                        <div class="panel-body">
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
        <div class="template-separator hidden">
            <div class="gridster-box separator">
                <a href="#" class="handle-close">&times;</a>
            </div>            
        </div>
        <script type="text/javascript">
            (function($){
                var gridster = null;
                //$(document).ready(function () {
                gridster = $(".gridster ul").gridster({
                    widget_base_dimensions: ['auto', 24],
                    autogenerate_stylesheet: true,
                    min_cols: 1,
                    max_cols: 12,
                    min_rows: 4,
                    max_rows: 24,
                    widget_margins: [4, 4],
                    serialize_params: function($w, wgd) { 
                        return { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y, type: $w.data("type"), module: $w.data("module") } 
                    },
                    resize: {
                        enabled: true,
                        stop: function(e, ui){
                            saveLayout();
                        }
                    },
                    /*collision:{
                        on_overlap_start: function(collider_data) { console.log(collider_data); },
                        on_overlap: function(collider_data) { console.log(collider_data); },
                        on_overlap_stop: function(collider_data) { console.log(collider_data); }
                    },*/
                    draggable:{
                        stop: function(e, ui){
                            saveLayout();
                        }
                    }
                }).data('gridster');
                // Gestion de la sérialisation
                var saving = false;
                var saveLayout = function(){
                    if(saving !== false) {
                        if(saving === true) saving = "required";
                        return;
                    }
                    saving = true;
                    $.post("board.php", {
                        "cmd": "save-board-layout",
                        "layout": JSON.stringify(gridster.serialize())
                    }, function(){
                        if(saving === "required"){
                            saving = false;
                            saveLayout();
                        }else{
                            saving = false;
                        }
                    });                    
                };
                // Gestion de la suppression d'un module
                var bindModuleClose = function(elm){
                    $(".handle-close", elm).click(function(e){
                        e.preventDefault();
                        gridster.remove_widget( $(this).parents("li").first());
                        saveLayout();
                    });
                };
                bindModuleClose($(".gridster"));
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
                    var mod = gridster.add_widget( 
                        $("<li></li>")
                        .attr("data-type", "module")
                        .attr("data-module", module)
                        .append(template),
                        parent.data('w'),
                        parent.data('h')
                    );
                    bindModuleClose(mod);
                    saveLayout();
                });
                // Gestion de l'ajout d'un séparateur
                $("a.cmd-add-separator").click(function(e){
                   e.preventDefault();
                    var template = $(".template-separator").html();
                    var mod = gridster.add_widget( 
                        $("<li></li>")
                        .attr("data-type", "separator")
                        .append(template)
                    );
                    bindModuleClose(mod);
                    saveLayout();                   
                });
            })(jQuery);
        </script>
    </body>
</html>
