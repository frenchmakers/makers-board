(function($){
    $.module = function(name){
      return $(".module[data-module='"+name+"']");
    };
})(jQuery);
