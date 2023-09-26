/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


 (function( $ ){
     var config, methods = {
		 /*
		 * Crea un layer semitransparente encima del objeto dado y pone un gif animado
		 * Ejemplo de Uso:
		 * $("#izq").esperaDiv('muestra');
		 */
        muestra: function() {
            var config = {
                animacion: "template/images/loading.gif"
            }
            return this.each(function () {
				var $this = jQuery(this);
				var ancho = $this.width();
				var alto = $this.height();
				$this.prepend('<div id="EDTod" style="width:'+ancho+'px;height:'+alto+'px;position:absolute;background-color:#000000;z-index:10;opacity:0.2;"><img src="'+config.animacion
					+'" style="position:absolute;top:'+(alto/2)+'px;left:'+(ancho/2)+'px;opacity:1;"></div>');
            });
        },
        /*
		 * Destruye el layer creado en la funci�n anterior
		 * Ejemplo de Uso:
		 * $("#izq").esperaDiv("cierra");
		 */
        cierra: function() {
           jQuery("#EDTod").remove();
			return this;
        },
        /*
		 *Esta function permite cambiar el texto de dentro de los textbox en los formularios
		 *Ejemplo de uso
			$(document).ready(function(){
				esperaDiv('trabText', 'explicacion nombre');
			});
		*/
        trabText: function(texto){
			var config = {
				activeColor: "#000",
				disabledColor: "#8D8D8D"
			}
			return this.each(function(){
				var $this = jQuery(this);
				$this.css("color",config.disabledColor).val(texto);
				$this.focus(function(){
					if($this.val() == texto){
						$this.val("").css("color",config.activeColor);
					}
				}); 
				$this.blur(function(){
					if(jQuery.trim($this.val()).length==0){
						$this.css("color",config.disabledColor).val(texto);
					}
				});
			});
		}
    }

    $.fn.esperaDiv = function (method) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' + method + ' does not exist on jQuery.myplugin' );
        }
    };

})( jQuery );


