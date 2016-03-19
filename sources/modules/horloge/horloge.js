(function($){
    var refreshHorloge=function(){
        $.module('horloge').html(new Date());        
    };
    var horloge=function(){
        setTimeout(function () {
            refreshHorloge();
            horloge();
        }, 1000);
    };
    refreshHorloge();
    horloge();
})(jQuery);
