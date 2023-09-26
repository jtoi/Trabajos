/**
 * Hace un layer flotante
 */
(function($){
	$.fn.floatDiv = function(){
		var view = viewport();
		this.css({"top": view.y,"left": view.width/2 - this.width()/2});
	}

function viewport() {
        return {
            x: $(document).scrollLeft(),
            y: $(document).scrollTop(),
            width: $(window).width(),
            height: $(window).height()
        };
    }
})(jQuery);


/**
* enciende y apaga el layer actualOK
 */
 (function($){
	$.fn.alerta = function(options, textos, duracion){
		var vista = {
			'background-color'	: 'green',
			'width'				: 100,
			'display'			: 'inline',
			'text-align'		: 'center'
		};
	
		if ( options ) { 
			$.extend( vista, options );
		}
		
		this.css(vista)
		.html(textos)
		.animate({'opacity': 'toggle'},{duration: duracion});
	}
 })(jQuery);


