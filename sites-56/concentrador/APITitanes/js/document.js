$(document).ready(function () {
    const currentUrl = window.location.href;
    var prof = 0;
    if (currentUrl.indexOf('documentb') > 0) prof = 1
    $("#defecto").change(function(){
        $.post('datos.php',{
            dato:	'19',
            def: $("#defecto").val()
        },function(data){
            var datos = eval('(' + data + ')');
            var options = $("#tipo");
            options.empty();
            $.each(datos, function(index,vale) {
                options.append($("<option />").val(vale.Id).text(vale.Nombre));
            });
        });
    });

   
	$.post('datos.php',{
		dato:	'6',
        beneficiario: prof
	},function(data){
		var datos = eval('(' + data + ')');
		var options = $("#persona");
		options.empty();
		options.append($("<option />").val('').text('Seleccione Persona..'));
		$.each(datos, function(index,vale) {
			options.append($("<option />").val(vale.IdTitanes).text(vale.persona));
		});
	});
	$.post('datos.php',{
			dato:	'19',
            def: $("#defecto").val()
		},function(data){
			var datos = eval('(' + data + ')');
			var options = $("#tipo");
			options.empty();
			$.each(datos, function(index,vale) {
				options.append($("<option />").val(vale.Id).text(vale.Nombre));
			});
		});
	
	$.post('datos.php',{
			dato:	'2'
		},function(data){
			var datos = eval('(' + data + ')');
			var options = $("#paise");
			options.empty();
			$.each(datos, function(index,vale) {
				options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
		});
    });
   
	$("#persona").change(function(){
		$.post('datos.php',{
			dato:	25,
			id: 	$("#persona").val()
		},function(data){
			var datos = eval('(' + data + ')');
			datos = datos[0];
			idtipo = datos.IdTipoDoc;
			idpais = datos.IdPaisDoc;
			$("#documento").val(datos.Documento);
			$("[name=defecto][value=true]").prop('checked', true);

			$.post('datos.php', {
				dato:	19,
				tipo:	idtipo
			}, function(data){
				var dato = eval('(' + data + ')');
				var options = $("#tipo");
				options.empty(); 
				$.each(dato, function(index,vale) {
					if (idtipo == vale.Id)
						options.append($("<option selected='selected' />").val(vale.Id).text(vale.Nombre));
					else
						options.append($("<option />").val(vale.Id).text(vale.Nombre));
				});
			});

			// $.post('datos.php', {
			// 	dato:	2,
			// 	pais:	idpais
			// }, function(data){
			// 	var dato = eval('(' + data + ')');
			// 	var options = $("#paise");
			// 	options.empty(); 
			// 	$.each(dato, function(index,vale) {
			// 		if (idtipo == vale.Id)
			// 			options.append($("<option selected='selected' />").val(vale.Iso2).text(vale.Nombre));
			// 		else
			// 			options.append($("<option />").val(vale.Iso2).text(vale.Nombre));
			// 	});
			// });
		});
	});
   

    $("#envia").on('click', function () {
        var pase = 1;
        
		esperafn();
        if ($("#documento").val().length < 3 || $("#documento").val().length > 60) {
            alert('Error en la documento');
            $("#documento").focus();
            pase = 0;
        }
        if ($("#alias").val().length < 3 || $("#alias").val().length > 60) {
            alert('Error en el alias del documento');
            $("#alias").focus();
            pase = 0;
        }
        //chequeo de fechas
        var patFecha = new RegExp("(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-(?:19|20)[0-9]{2}");
        if (!patFecha.test($("#fecha").val())) {
            pase = 0;
            $("#fecha").focus();
            alert("La fecha no cumple con el patr\u00F3n recomendado 'dd-mm-yyyy'");
        }
        var date = new Date();
        fechaArr = $("#fecha").val().split('-');

        if (fechaArr[2] < date.getFullYear() ) {
			alert('Error en la fecha: es una fecha vencida');
			$("#fecha").focus();
			pase = 0;
        }
        
        if (fechaArr[2] == date.getFullYear() && fechaArr[1]*1 <= date.getMonth()+1)  {
            alert('Error en la fecha: es una fecha vencida o est\u00e1 pr\u00f3xima a vencerse');
            $("#fecha").focus();
            pase = 0;
        }

        if ($('#archivo')[0].files[0].size > 10000000) {//hasta 10 megas
            alert('Sobrepasado el tama\u00f1o del fichero');
            pase=0;
        }
        var mime = $('#archivo')[0].files[0].type;
        
        if (mime.length > 3) {
            $.post('datos.php',{
                mime:   mime,
                dato:	'20'
            },function(data){
                var datos = eval('(' + data + ')');
                if (datos.Id > 0) pase = 1;
                else {
                    alert('Error en el fichero1');
                    $("#archivo").focus();
                    pase = 0;
                }
            });
        } else {
            alert('Error en el fichero2');
            $("#archivo").focus();
            pase = 0;
        }

        if (pase == 1) {
            var form_data = new FormData();
            
            form_data.append('file', $('#archivo')[0].files[0]);
            form_data.append("persona", $("#persona").val());
            form_data.append("tipoDoc", $("#tipo").val());
            form_data.append("mime", mime);
            form_data.append("documento", $("#documento").val());
            form_data.append("fecha", $("#fecha").val());
            form_data.append("alias", $("#alias").val());
            form_data.append("pais", $("#paise").val());
            form_data.append("defecto", $('select[name=defecto] :selected').val());
            form_data.append("dato", '21');

            $.ajax({
                cache: false,
                contentType: false,
                data: form_data,
                dataType: 'JSON',
                enctype: 'multipart/form-data',
                processData: false,
                method: "POST",
                type: "POST",
                url: "datos.php",
                success: function (data) {
                    if (data.error) alert(utf8Decode(data.error));
                    else alert("El documento ha sido correctamente insertado con IdTitanes: "+data.pase);
					esperafn();
                }

            });
        } else esperafn();

    });


});