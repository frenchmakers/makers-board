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
                sendBoardSize: false,
                done:function(data) {
                    $("#board-title").val(data.title);
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
                title: $("#board-title").val(),
                size: currentSize,
                modules: []
            };
            $(".module", $this).each(function(){
                var $module = $(this);
                var p = $module.position();
                var mod = {
                    x: p.left * ratioX,
                    y: p.top * ratioY,
                    w: $module.outerWidth() * ratioX,
                    h: $module.outerHeight() * ratioY,
                    id: $module.data("id"),
                    module: $module.data("module"),
                    title: $module.data("title"),
                    params: {}
                };
                $.each(this.attributes, function() {
                    var n = this.nodeName || this.name;
                    if(n.substr(0, 11)=="data-param-") {
                        var v = this.nodeValue || this.value;
                        mod.params[n.substr(11)] = v;
                    }
                });
                layout.modules.push(mod);
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
            
            // Redimensionne le board
            var tw = $this.width();
            var th = $this.height();
            $this.attr({
                "data-screen-width": options.width,
                "data-screen-height": options.height
            });
            $this.height(options.height * (tw / options.width));
            
            // Calcul des ratios
            //var ratioX = currentSize.width / options.width;
            //var ratioY = currentSize.height / options.height;
            var ratioX = 1;
            var ratioY = $this.height() / th;

            var size = $("#screen-size option[data-w=" + options.width + "][data-h=" + options.height + "]").val();
            if(!size || size=="") {
                size = "custom";
                $("#screen-size option[value='custom']").attr({
                    'data-w': options.width,
                    'data-h': options.height,
                }).text("Taille personnalisée: " + options.width + "x" + options.height);
            }
            $("#screen-size").val(size);
                
            // Repositionnement des modules
            $(".module", $this).each(function(){
                var $module = $(this);
                var p = $module.position();
                console.log($module.width(),$module.height());
                $module.css({
                    left: (p.left * ratioX) + "px",
                    top: (p.top * ratioY) + "px"
                })
                .width(Math.max(12, $module.width() * ratioX))
                .height(Math.max(12, $module.height() * ratioY));    
                console.log($module.width(),$module.height());
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
        $("#configModal").data("module", $module.data("id")).modal({
            'backdrop': 'static'
        });
        $module.board('api',{
            path:"/config",
            done: function(data){
                if(data.status == "error"){
                    $("#configModal .modal-body").html( $('<p></p>').text(data.message) );                    
                } else {
                    $("#configModal .modal-body").html( $("<form></form>").append(data.html) );
                    $("#configModal .btn-primary").removeAttr("disabled");                
                }
            }
        });
    };
    $("#configModal .btn-primary").click(function(e){
        var params = $("#configModal .modal-body form").serializeArray();
        var obj = {};
        $.each(params, function(){
            if(obj[this.name]) {
                if(!obj[this.name].push){
                    obj[this.name] = [o[this.name]];
                }
                obj[this.name].push(this.value);
            } else {
                obj[this.name] = this.value || '';
            }
        });
        $(".board-editor").board('api',{
            'callType': 'module',
            'module': $("#configModal").data("module"),
            'path': '/config',
            'method': 'POST',
            'data': obj,
            'done': function(data){
                if(data.params) {
                    $module = $(".module[data-id='"+$("#configModal").data("module")+"']");
                    for (var name in data.params) {
                        if (data.params.hasOwnProperty(name)) {
                            $module.attr('data-param-'+name, data.params[name]);
                        }
                    }
                }
                $("#configModal").modal('hide');
            }
        });
    });
    
    // Gestion des messages
    var displayMessage = function(message) {
        $template = $($(".messages-template").html());
        $template.append(message);
        $(".messages").append($template);
        setTimeout(function(){
            $template.remove();
        }, 10*1000);
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
        sw = $opt.data("w");
        sh = $opt.data("h");
        $(".board-editor")
            .boardEditor("resize",{
            'width': sw,
            'height': sh
        });
    });
    
    // Gestion du titre
    $("#board-title").blur(function() {
        $(".board-editor").boardEditor('save');
    });
    
    // Activation des liens d'ajout des modules
    $(".add-module-command").click(function(e){
        e.preventDefault();
        $(".board-editor").boardEditor("module.add", {
            "module": $(this).data("module"),
            "title": $(this).data("title")
        });
    });
    
    // Commandes de boards
    $(".refresh-all-command").click(function(e){
        e.preventDefault();
        $(".board-editor").board('api',{
            'callType': '',
            'method': 'POST',
            'path': '/update-system'
        });
        displayMessage("L'ordre d'actualisation a été émis.");
    });

    // Récupération de la taille de l'écran du lecteur
    $("#set-as-current-size").click(function(e){
        e.preventDefault();
        var sw=$("#current-view-size").data("w");
        var sh=$("#current-view-size").data("h");
        $(".board-editor")
            .boardEditor("resize",{
            'width': sw,
            'height': sh
        });
    });
    $("#set-as-current-size").hide();
    $("#current-view-size").text("Non définie");
    var refreshCurrentViewSize = function(){
        $(".board-editor").board('api',{
            'callType': 'board',
            'path': '/view-size',
            'method': 'GET',
            'done': function(data){
                $("#current-view-size").text(data.dw+"x"+data.dh).attr({
                    'data-w': data.dw,
                    'data-h': data.dh,
                });
                $("#set-as-current-size").show();
            }
        });
    };
    setInterval(refreshCurrentViewSize, 10*1000);
    refreshCurrentViewSize();

})(jQuery);
