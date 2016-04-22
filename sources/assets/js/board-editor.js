/**
 * Gestion de l'éditeur de tableau
 */
(function ($) {
    
    // Définition de l'éditeur de board
    $.fn.boardEditor = function (command, options) {
        // Validation des options
        if (!command || $.isPlainObject(command)) {
            options = command;
            command = "enable";
        }
        
        // Application du module
        return this.each(function () {
            var $this = $(this);
            switch (command) {
                case "enable":
                    $.fn.boardEditor.commands.enable.call($this, options);
                    break;
                case "load":
                    $.fn.boardEditor.commands.load.call($this, options);
                    break;
                case "save":
                    $.fn.boardEditor.commands.save.call($this, options);
                    break;
                case "resize":
                    $.fn.boardEditor.commands.resize.call($this, options);
                    break;
                default:
                    // Par défaut on redirige sur le module board()
                    $this.board(command, options);
                    break;
            }
        });
    };
    
    // Valeurs par défaut
    $.fn.boardEditor.defaults = {
        'enable': {}
    };
    
    // Le contenu de l'éditeur n'est pas encore chargé
    var editorLoaded = false;
    
    // Commandes
    $.fn.boardEditor.commands = {
        // Activation de l'éditeur
        'enable' : function(settings) {
            var $this = $(this);
            // Si le board est déjà défini on passe
            if ($this.data("board-editor-settings"))
                return false;
            // Enregistrement des paramètres
            settings = $.extend({}, $.fn.boardEditor.defaults.enable, settings);
            $this.data("board-editor-settings", settings);
            // Activation du board de base
            $this.board("enable", {
                refresh: false,
                editable: false,
                board: function () { return $("#current-board").val(); },
                moduleTemplate: '<div class="module" data-module="{{module}}" title="{{title}}"><div class="title">{{title}}</div></div>',
                moduleAdded: function($module){
                    setModuleEditor($(this), $module);
                    $this.boardEditor('save');
                },
                moduleDeleted: function($module){
                    $this.boardEditor('save');
                }
            });
            // Activation du mode edition
            setEditor($this);
        },
        // Chargement de l'éditeur
        'load' : function(options) {
            var $this = $(this);
            var settings = $this.data("board-editor-settings");
            if(!settings) return;
            // Provoque un appel au board
            $this.board('load',{
                done:function(data) {
                    $this.boardEditor('resize',{
                        'width': data.size.width,
                        'height': data.size.height                        
                    });
                    setEditor($this);
                    editorLoaded = true;
                }
            });
        },
        // Enregistrement du layout
        'save' : function(options) {
            // Tant que l'éditeur n'est pas chargé on ne sauvegarde rien pour ne pas écraser
            // le board avec des valeurs incorrectes
            if(!editorLoaded) return;
            
            var $this = $(this);
            var settings = $this.data("board-editor-settings");
            if(!settings) return;
            
            // Calcul les valeurs
            var currentSize = {
                'width': $this.attr("data-screen-width") ? $this.attr("data-screen-width") : $this.width(),
                'height': $this.attr("data-screen-height") ? $this.attr("data-screen-height") : $this.height()
            };
            var ratioX = currentSize.width / $this.width();
            var ratioY = currentSize.height / $this.height();
            
            // Extraction des informations de layout
            var layout = {
                size: currentSize,
                modules: []
            };
            $(".module", $this).each(function(){
                var $module = $(this);
                var p = $module.position();
                layout.modules.push({
                    x: p.left * ratioX,
                    y: p.top * ratioY,
                    w: $module.outerWidth() * ratioX,
                    h: $module.outerHeight() * ratioY,
                    id: $module.data("id"),
                    module: $module.data("module"),
                    title: $module.data("title")
                });
            });
            
            // Transmission des données
            $this.board("api",{
                callType: "board",
                method: "POST",
                data: layout
            });
        },
        // Provoque le redimensionnement de l'éditeur
        'resize': function(options) {
            var $this = $(this);
            var settings = $this.data("board-editor-settings");
            if(!settings) return;
            
            // Extraction des valeurs en cours
            var currentSize = {
                'width': $this.attr("data-screen-width") ? $this.attr("data-screen-width") : $this.width(),
                'height': $this.attr("data-screen-height") ? $this.attr("data-screen-height") : $this.height()
            };
            
            // Valide les options
            options = $.extend({}, {
                'width': currentSize.width,
                'height': currentSize.height
            }, options);
            
            // Calcul des ratios
            var tw = $this.width();
            var th = $this.height();
            var ratioX = options.width / currentSize.width;
            var ratioY = options.height / currentSize.height;
            
            // Redimensionne le board
            $this.attr({
                "data-screen-width": options.width,
                "data-screen-height": options.height
            });
            $this.height(options.height * (tw / options.width));
            
            var size = $("#screen-size option[data-w=" + options.width + "][data-h=" + options.height + "]").val();
            if(!size || size=="") size = "current";
            $("#screen-size").val(size);
                
            //$("#current-screen-size").attr({
            //    "data-width": options.width,
            //    "data-height": options.height,
            //}).text(options.width + "x" + options.height);
            
            // Repositionnement des modules
            $(".module", $this).each(function(){
                var $module = $(this);
                var p = $module.position();
                $module.css({
                    left: (p.left * ratioX) + "px",
                    top: (p.top * ratioY) + "px"
                })
                .width(Math.max(12, $module.width() * ratioX))
                .height(Math.max(12, $module.height() * ratioY));    
            });
            
            // Evénément 'board.resized'
            $this.trigger('board.resized');            
        }
    };
    
    // Définition du mode éditeur
    var setEditor = function($board){
        $(".module", $board).each(function(){
            setModuleEditor($board, $(this));
        });
    };
    var setModuleEditor = function($board, $module){
        // Si le module est déjà défini on passe
        var msettings = $module.data("module-settings"); 
        if(!msettings) return false;
        if(msettings.editor) return false;
        // Calcul des informations d'un module
        msettings.editor = true;
        $module.data("module-settings", msettings);
        // Activation du déplacement
        $module.draggable({
            containment: "parent",
            snap: true,
            snapMode: 'outer',
            stop: function () {
                $board.boardEditor("save");
            }
        });
        // Activation du dimensionnement
        $module.resizable({
            containment: "parent",
            minWidth: 32,
            minHeight: 32,
            stop: function () {
                $board.boardEditor("save");
            }
        });
        // Activation de la fermeture
        $module.append($("<div class='handle-close'>&times;</div>"));
        $(".handle-close", $module).click(function(e){
            if(confirm("Etes-vous sûrs de vouloir supprimer ce module ?")){
                $board.boardEditor("module.delete", {
                    "module": $(this).parents(".module").first().data("id")
                });
            }
        });
        // Activation de la configuration
        $module.append('<a href="#" class="config-module-command">Configuration</a>');
        $(".config-module-command", $module).click(function(e){
            e.preventDefault();
            configModule($board, $module);
        });
    };
    var configModule = function($board, $module) {
        $("#configModal .modal-title").text("Configuration de "+$module.data("title"));
        $("#configModal .modal-body").html('<p>Chargement de la configuration</p>');
        $("#configModal .btn-primary").attr("disabled", "disabled");
        $("#configModal").modal({
            'backdrop': 'static'
        });
        $module.board('api',{
            path:"/config",
            done: function(data){
                if(data.status == "error"){
                    $("#configModal .modal-body").html( $('<p></p>').text(data.message) );                    
                } else {
                    $("#configModal .modal-body").html(data.html);
                    $("#configModal .btn-primary").removeAttr("disabled");                
                }
            }
        });
    };

    // Initialisation du board
    $(".board-editor").boardEditor();

    // Initialisation de la taille d'écran
    $(".board-editor").boardEditor('resize', {
        'width': 1280,
        'height': 800
    });
    
    // Activation du board en mode edition
    $(".board-editor").boardEditor('load');
    
    // Gestion du sélecteur de la taille d'écran
    $("#screen-size").change(function () {
        var $opt = $("option:selected", this);
        if ($opt.val() != "current") {
            sw = $opt.data("w");
            sh = $opt.data("h");
        } else {
            sw = $("#current-screen-size").data("width");
            sh = $("#current-screen-size").data("height");
        }
        $(".board-editor")
            .boardEditor("resize",{
            'width': sw,
            'height': sh
        });
    });
    
    // Activation des liens d'ajout des modules
    $(".add-module-command").click(function(e){
        e.preventDefault();
        $(".board-editor").boardEditor("module.add", {
            "module": $(this).data("module"),
            "title": $(this).data("title")
        });
    });

})(jQuery);
