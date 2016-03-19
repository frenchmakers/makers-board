(function($){
    // Retourne l'objet jQuery contenant le module
    $.module = function(name){
      return $(".module[data-module='"+name+"']");
    };
    
    // Appel l'actualisation d'un module
    $.refreshModule = function(name, callback){
        $.get("module.php", { "module": name, "_t":new Date().getTime()}, function(data){
            if($.isFunction(callback)){
                callback(data);
            }
        });
    };
})(jQuery);
