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
    
    // Actualisation du dashboard
    var lastRefresh=0;
    var refreshDashboard = function(){
        $.refreshModule('dashboard', function(data){
            var rVal = parseInt(data);
            if(lastRefresh<rVal){
                document.location.reload();
                lastRefresh = rVal;
            } 
        });
    };
    var timerRefreshDashboard = function(){
        setTimeout(function(){
            refreshDashboard();
            timerRefreshDashboard();
        }, 10*1000);
    };
    timerRefreshDashboard();
})(jQuery);
