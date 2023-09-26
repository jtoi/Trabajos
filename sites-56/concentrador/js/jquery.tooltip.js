/**
 * 
 */

;(function($){ 
     
    return this.each(function(){ 
        var self = $(this), 
        title = self.attr("title"); 

        $(this).hover(function(event){ 
                 
            var effect = defaults.show.effect; 
     
            if(title){ 
                this.title = ""; 

                $("<div>") 
                    .appendTo("body") 
                    .text(title) 
                    .hide() 
                    .updatePosition(event) 
                    .addClass(opt.className) 
                    .fadeIn(); 
                         
                } 
            },function(){ 
                $(".tooltip").remove(); 
            }).mousemove(function(event){ 
                $(".tooltip").updatePosition(event); 
            }); 
        }); 



    $.fn.updatePosition = function(event) { 
      return this.each(function() { 
         
        $(this).css({ 
          left: event.pageX+10, 
          top:  event.pageY-80 
        }); 
      }); 
    }; 
})(jQuery)
