/* 
 * Valores y funciones globales de Javascript
 */


var fadeout = 7000; //valor del fadeout de los objetos que muestran resultados

var profileIdioma		= readLocalStorageData("PROFILE_IDIOMA");

function cambPag(numpag){
	$('#numpag').val(numpag);
	constTabla();
}

function constTabla(){
	$("body").esperaDiv('muestra');
	$.post('index.php',{
			mdr: $("#pag").val(),
			pas: 'xtg',
			fun: 'constrTabl',
			datos: $('#columnas').val() + '|' + $('#tablas').val() + '|' + $('#buscar').val() + '|' + $('#orden').val() + '|' + $('#numpag').val()
		},function(data){
			var datos = eval('(' + data + ')');
			$("body").esperaDiv('cierra');
			if (datos.error.length > 0) muestraErr(datos.error);
			if (datos.data.length > 0) {
				$("#tablaGen").html(datos.data);
			}
		});
}

function cambOrden(column){
	$("#orden").val(column);
	constTabla();
}

function muestraErr(texto) {
	$("#respue_spam").addClass("label-danger").removeClass("hide").removeClass("label-success").html(texto).show();
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function muestraAcept(texto) {
	$("#respue_spam").removeClass("label-danger").removeClass("hide").addClass("label-success").html(texto).show().fadeOut(fadeout);
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

(function( $ ){
     var config, methods = {
		 /*
		 * Crea un layer semitransparente encima del objeto dado y pone un gif animado
		 * Ejemplo de Uso:
		 * $("#izq").esperaDiv('muestra');
		 */
        muestra: function() {
            var config = {
                animacion: "images/rotate.gif"
            }
            return this.each(function () {
				var $this = jQuery(this);
				var ancho = $this.width();
				var alto = $this.height();
				$this.prepend('<div id="EDTod" style="width:'+ancho+'px;height:'+alto+'px;position:absolute;background-color:#000000;z-index:10;opacity:0.2;"><img src="'+config.animacion
					+'" style="position:absolute;top:'+(($(window).innerHeight())/2)+'px;left:'+(($(window).innerWidth())/2)+'px;opacity:1;"></div>');
            });
        },
        /*
		 * Destruye el layer creado en la funciï¿½n anterior
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


/**
 * Para trabajar con los input file
 * @returns {undefined}
 */
$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });
  
});