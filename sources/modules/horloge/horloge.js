(function($){
    var refreshHorloge=function(){
        $.module('horloge').html(new Date());
//        $.refreshModule('horloge', function(data){
//            $.module('horloge').html(data);
//        });
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
