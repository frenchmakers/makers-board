/**
 * Gestion des tableaux de bord
 */
(function ($) {

    // Définition du module board
    $.fn.board = function (command, options) {
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
                    enableCommand.call($this, options);
                    break;
                case "refresh":
                    refreshCommand.call($this, options);
                    break;
                case "load":
                    loadCommand.call($this, options);
                    break;
                case "save":
                    saveCommand.call($this, options);
                    break;
                case "module.add":
                    moduleAddCommand.call($this, options);
                    break;
                case "module.delete":
                    moduleDeleteCommand.call($this, options);
                    break;
            }
        });
    };

    // Valeurs par défaut
    $.fn.board.defauls = {
        api: "api.php",
        editable: false,
        refresh: 5 * 1000,
        board: function(){ return $(this).data("board"); },
        error: function(message){ if(console) console.log(message); },
        moduleTemplate : '<div class="module" data-module="{{module}}" title="{{title}}"></div>'
    };

    // Activation d'un board
    var enableCommand = function (settings) {
        var $this = $(this);
        // Si le board est déjà défini on passe
        if ($this.data("board-settings"))
            return false;
        // Enregistrement des paramètres
        settings = $.extend({}, $.fn.board.defauls, settings);
        $this.data("board-settings", settings);
        // Activation du rafraichissement automatique
        if (settings.refresh !== false && settings.refresh > 0) {
            refreshCommand.call($this);
            setInterval(function () {
                refreshCommand.call($this);
            }, settings.refresh);
        }
        // Activation du mode edition
        if(settings.editable)
            setEditor($this);
    };

    // Actualisation d'un board
    var refreshCommand = function (options) {
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return;
        // Provoque un ping
        var boardName = getBoardName($this, settings);
        $.get(settings.api + "/board/"+boardName+"/ping", null, function (data) {
            // Si la valeur de dernière mise à jour à changée alors on charge le tableau
            if(data!=$this.data("lastUpdate")) {
                $this.data("lastUpdate", data);
                loadCommand.call($this, options);
            }
        });
    };
    
    // Chargement d'un board
    var loadCommand = function (options) {
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return;
        options = $.extend({
            "sendBoardSize": false
        }, options);
        // 
        var sdata = {
            _t: new Date().getTime()
        };
        if(options.sendBoardSize) {
            sdata.dw = $this.width();
            sdata.dh = $this.height();
        }
        var boardName = getBoardName($this, settings);
        $.get(settings.api + "/board/" + boardName, sdata, function (data) {
            // Erreur ?
            if(data.status && data.status=="error") {
                if($.isFunction(settings.error)) {
                    settings.error.call($this, data.message);
                }
                return;
            }
            // Enregistre la dernière mise à jour
            if(data.lastUpdate){
                $this.data("lastUpdate", data.lastUpdate);
            }
            // Enregistre la taille de référence
            $this.attr({
                "data-screen-width": options.width,
                "data-screen-height": options.height
            });
            // Calcul des ratios
            var ratioX = $this.width() / data.size.width;
            var ratioY = $this.height() / data.size.height;
            // Récupération des modules actuellement affichés
            var cModules = {};
            $(".module", $this).each(function(){
                var $module = $(this);
                var id = $module.data("id");
                // Enregistre le module ou le supprime
                if(id && id!==""){
                    cModules[id] = false;
                }else{
                    $module.remove();
                }
            });
            // Traitement de chaque module recu
            $.each(data.modules, function(idx, mod){
                var id = mod.id;
                if(id && id!=="") {
                    // Recherche un module existant
                    var smodule = $(".module[data-id='"+id+"']", $this);
                    if(smodule.length==0){
                        // Le module n'existe pas on le créé
                        smodule = moduleAddCommand.call($this, {
                            "id": id,
                            "module": mod.module,
                            "title": mod.title 
                        });
                    }
                    // Modification des modules
                    smodule.css({
                        left: (mod.x * ratioX) + "px",
                        top: (mod.y * ratioY) + "px"
                    }).attr({
                        "data-module": mod.module
                    }).width(Math.max(12, mod.w * ratioX))
                    .height(Math.max(12, mod.h * ratioY));
                    // Marque le module comme défini
                    cModules[id] = true;
                }
            });
            // Supprime tous les modules qui n'ont pas été traités
            for (var key in cModules) {
                if (cModules.hasOwnProperty(key)) {
                    if(cModules[key]===false){
                        $(".module[data-id='"+key+"']", $this).remove();
                    }
                }
            }
            // Evénément 'done' ?
            if($.isFunction(options.done)) {
                options.done.call($this, data);
            }
            
            // Evénément 'board.loaded'
            $this.trigger('board.loaded');
        }, "json");
    };
    
    // Enregistrement d'un board
    var saveCommand = function(){
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return;
        // Extraction des informations de layout
        var layout = {
            size: {
                width: $this.data("screen-width"),
                height: $this.data("screen-height")
            },
            modules: []
        };
        $(".module", $this).each(function(){
            var $module = $(this);
            var p = $module.position();
            layout.modules.push({
                x: p.left,
                y: p.top,
                w: $module.width(),
                h: $module.height(),
                id: $module.data("id"),
                module: $module.data("module"),
                title: $module.data("title")
            });
        });
        // Transmission des données
        var boardName = getBoardName($this, settings);
        $.post(settings.api + "/board/" + boardName, JSON.stringify(layout));
    };
    
    // Insertion d'un module 
    var moduleAddCommand = function(options) {
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return false;
        
        options = $.extend({
            "id": 0,
            "module": "",
            "title": "",
            "done": function($board, $module){},
            "save": true
        }, options);
        
        // Calcul le prochain id si nécessaire
        if(options.id === 0) {
            var lastId = 0;
            $(".module", $this).each(function(){
                var mid = parseInt($(this).data("id"));
                if(!isNaN(mid)) lastId = Math.max(mid, lastId);
            });
            options.id = lastId + 1;            
        }
        
        // Création du module
        $module = $( parseTemplate(settings.moduleTemplate, options) );
        $module.attr("data-id", options.id);
        $module.attr("data-module", options.module);
        $module.attr("data-title", options.title != "" ? options.title : options.module);
        
        // Insertion 
        $this.append($module);
        
        // Activation du mode édition
        if(settings.editable) {
            setModuleEditor($this, $module);
        }
        
        // Evénément 'done'
        if($.isFunction(options.done)) {
            options.done($this, $module);
        }
        
        // Evénément 'module.added'
        $this.trigger('module.added', $module);
        
        // Enregistrement du layout
        if(options.save === true)
            saveCommand.call($this);
        return $module;
    };
    
    // Suppression d'un module 
    var moduleDeleteCommand = function(options) {
        var $this = $(this);
        var settings = $this.data("board-settings");
        if(!settings) return false;
        
        // Construction des options
        if(!($.isArray(options) || $.isPlainObject(options))){
            options = {
                "module": options
            };
        }
        options = $.extend({
            "done": function($board, $module){},
            "save": true
        }, options);
        
        // Extraction du module
        var $module = options.module;
        if(!($module instanceof jQuery)){
            $module = $(".module[data-id='"+module+"']", $this);  
        }
        if($module.length==0) return;
        
        // Supprime le module
        $module.remove();
        
        // Evénément 'done'
        if($.isFunction(options.done)) {
            options.done($this, $module);
        }
        
        // Evénément 'module.deleted'
        $this.trigger('module.deleted', $module);
        
        // Enregistrement du layout
        if(options.save === true)
            saveCommand.call($this);
    };
    
    // Définition du mode éditeur
    var setEditor = function($this){
        $(".module", $this).each(function(){
            setModuleEditor($this, $(this));
        });
    };
    var setModuleEditor = function($board, $module){
        // Si le module est déjà défini on passe
        if($module.data("module-settings")) return false;
        // Calcul des informations d'un module
        var msettings = {
        };
        $module.data("module-settings", msettings);
        // Activation du déplacement
        $module.draggable({
            containment: "parent",
            snap: true,
            snapMode: 'outer',
            stop: function () {
                saveCommand.call($board);
            }
        });
        // Activation du dimensionnement
        $module.resizable({
            containment: "parent",
            minWidth: 32,
            minHeight: 32,
            stop: function () {
                saveCommand.call($board);
            }
        });
        // Activation de la fermeture
        $module.append($("<div class='handle-close'>&times;</div>"));
        $(".handle-close", $module).click(function(e){
            if(confirm("Etes-vous sûrs de vouloir supprimer ce module ?")){
                moduleDeleteCommand.call($board, $module);
            }
        });
    };
    
    // Extraction du nom du tableau de bord
    var getBoardName = function($board, settings){
        if(settings && settings.board){
            if($.isFunction(settings.board)){
                return settings.board.call($board);
            }
            return settings.board;
        }
        return $($board).data("board");
    };
    
    // Traitement des templates
    var parseTemplate = function(template, data){
        return template.replace(/\{\{[\w._-]+\}\}/gi, function(tag){
            var n = tag.substring(2, tag.length-2);
            return (n in data) ? data[n] : "";
        });
    };
    
})(jQuery);
