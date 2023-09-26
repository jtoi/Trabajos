/* 
 * Cambia los datos de los usuarios conectados
 */


	$(document).ready(function(){
		$('#idvf-contr2').hide();
		$('#contr').val('');
		camfec();
		camnum();
	});
	
	$('#contr').click(function(){
		$('#idvf-contr2').show();
		$('#contr').removeClass('has-error');
	});
	
	$('#contr2').click(function(){
		$('#contr2').removeClass('has-error');
	});
	
	$('#fechaf').change(function(){camfec();});
	$('#hrsf').change(function(){camfec();});
	$('#decs').change(function(){camnum();});
	$('#mils').change(function(){camnum();});

	/**
	 * Cambia el formato de los números en el ejemplo
	 * @returns string Error o el número formateado
	 */
	function camnum(){
		$('#decs').removeClass('has-error');
		$('#mils').removeClass('has-error');
		if ($('#decs option:selected').val() != $('#mils option:selected').val()) {
			$("body").esperaDiv('muestra');
			$.post('index.php',{
				mdr: 'dpr',
				pas: 'xtg',
				fun: 'genNumEj',
				datos: $('#decs option:selected').val() + '|' + $('#mils option:selected').val()
			},function(data){
				var datos = eval('(' + data + ')');
				$("body").esperaDiv('cierra');
				if (datos.error.length > 0) muestraErr(datos.error);
				if (datos.data.length > 0) {
					$("#numba span").html(_EXAM+': '+datos.data).addClass('alert-info');
				}
			});
		} else {
			$('#decs').addClass('has-error');
			$('#mils').addClass('has-error');
			alert(_SEPR_DEC_MILES);
		}
	}

	/**
	 * Cambia el formato de la fecha en el ejemplo
	 * @returns string Error o la fecha formateada
	 */
	function camfec(){
		$('#fechaf').removeClass('has-error');
		$('#hrsf').removeClass('has-error');
		$("body").esperaDiv('muestra');
		$.post('index.php',{
			mdr: 'dpr',
			pas: 'xtg',
			fun: 'genFecEj',
			datos: $('#fechaf option:selected').val() + ' ' + $('#hrsf option:selected').val()
		},function(data){
			var datos = eval('(' + data + ')');
			$("body").esperaDiv('cierra');
			if (datos.error.length > 0) muestraErr(datos.error);
			if (datos.data.length > 0) {
				$("#fecba span").html(_EXAM+': '+datos.data).addClass('alert-info');
			}
		});
	}
	
	/**
	 * Verifica los datos y los envía al server para salvarlos
	 * @returns string
	 */
	function verifica(){
		if ($('#contr').val() != $('#contr2').val()) {
			$('#contr').addClass('has-error');
			$('#contr2').addClass('has-error');
			muestraErr(_CONTR_NCOINCIDE);
			return false;
		}
		
		if ($('#decs option:selected').val() == $('#mils option:selected').val()) {
			$('#decs').addClass('has-error');
			$('#mils').addClass('has-error');
			muestraErr(_SEPR_DEC_MILES);
			return false;
		}
		
		$("body").esperaDiv('muestra');
		$.post('index.php',{
			mdr: 'dpr',
			pas: 'xtg',
			fun: 'salvDatPer',
			datos: $("#nombre").val() + '|' + $("#email").val() + '|' + $("#contr").val() + '|' + $('#fechaf option:selected').val() + '|' + $('#hrsf option:selected').val() + '|' + $('#id').val() + '|' + $('#decs option:selected').val() + '|' + $('#mils option:selected').val() + '|' + $('#idio option:selected').val()
		},function(data){
			var datos = eval('(' + data + ')');
			$("body").esperaDiv('cierra');
			if (datos.error.length > 0) muestraErr(datos.error);
			if (datos.data.length > 0) {
				SaveLocalStorageData("PROFILE_IMG_SRC", datos.idioma );
				muestraAcept(datos.data);
			}
		});
		
		return false;
	}


