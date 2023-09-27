function updateContact() {
    var pase = 1;
   
    if (
        $("#contacto").val().length == 0
    ) {
        pase = 0;
        $("#contacto").focus();
        alert("Este campo no puede estar vac\u00EDo")
    }
    if (pase == 1) {
		esperafn();
        $.post("datos.php", {
            IdPersona:              $("#persona").val(),
            ContactMethodTypeId:    $("#metodo").val(),
            ContactMethodValue:     $("#contacto").val(),
            ContactMethodAlias:     $("#alias").val(),
            IsContactMethodDefault: $('select[name=defecto] :selected').val(),
            idTitanes:              $("#idtitanes").val(),
            dato:                   26,
            func:                   'updateContact'
        }, function(data) {
			data = data.replace('[]','');
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error))
            else alert("El contacto ha sido correctamente actulizado con IdTitanes: "+datos.pase);
            esperafn();
        });
    }
}

function updateUser(){
    var pase = 1;
    if ($("#tipo").val() == '1') {
        if (
            $("#nombre").val().length == 0
            || $("#papellido").val().length == 0
        ) {
            pase = 0;
        }
        var patNombre = new RegExp('^[a-zA-Z][a-zA-Z-áéíóúüñ _\]{1,50}$');
        
        if (!patNombre.test($("#nombre").val())) {
            pase = 0;
            $("#nombre").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if (!patNombre.test($("#papellido").val())) {
            pase = 0;
            $("#papellido").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if ($("#sapellido").val().length > 0) {
            if (!patNombre.test($("#sapellido").val())) {
                pase = 0;
                $("#sapellido").focus();
                alert("Caracteres no v\u00E1lidos");
            }
        }

    } else if ($("#tipo").val() == '2') {
        if (
            $("#comercialName").val().length == 0
            || $("#dSocial").val().length == 0
        ) {
            pase = 0;
        }
        var patNombre = new RegExp('^[a-zA-Z0-9][a-zA-Z0-9-áéíóúüñ _\.]{1,50}$');
        if (!patNombre.test($("#comercialName").val())) {
            pase = 0;
            $("#comercialName").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if (!patNombre.test($("#dSocial").val())) {
            pase = 0;
            $("#dSocial").focus();
            alert("Caracteres no v\u00E1lidos");
        }
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
    if (fechaArr[2] > date.getFullYear() ) {
        alert('Error en la fecha');
        $("#fecha").focus();
        pase = 0;
    }
    if (fechaArr[2] == date.getFullYear() && fechaArr[1] >= date.getMonth())  {
        alert('Error en la fecha');
        $("#fecha").focus();
        pase = 0;
    }

    if (pase == 1) {
		var fecha = fechaArr[2]+'-'+fechaArr[1]+'-'+fechaArr[0];
        esperafn();
        $.post("datos.php", {
            IdPersona:              $("#idpersona").val(),
            Name:                   $("#nombre").val(),
            LastName1:              $("#papellido").val(),
            LastName2:              $("#sapellido").val(),
            PersonType:             $("#tipo").val(),
            CommercialName:         $("#comercialName").val(),
            BusinessName:           $("#dSocial").val(),
            DateOfBirth:            fecha,
            Activity:               $("#activ").val(),
            MainActivityDesc:       $("#activExtr").val(),
            OriginISOCountryCode:   $("#paiso").val(),
            IsPublicOffice:         $('select[name=persona] :selected').val(),
            dato:                   26,
            func:                   'updateUser'
        },function(data){
			data = data.replace('[]','');
            var datos = eval('(' + data + ')');
            if(datos.error.length > 3) alert(utf8Decode(datos.error));
            else alert("La Persona ha sido correctamente actualizada con IdTitanes: "+datos.pase);
            esperafn();
        });
    }
}

function insertUser(prof){
    var pase = 1;
	
    if ($("#tipo").val() == '1') {
        if (
            $("#nombre").val().length == 0
            || $("#papellido").val().length == 0
        ) {
            pase = 0;
        }
        var patNombre = new RegExp('^[a-zA-Z][a-zA-Z-áéíóúüñ _\]{1,50}$');
        
        if (!patNombre.test($("#nombre").val())) {
            pase = 0;
            $("#nombre").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if (!patNombre.test($("#papellido").val())) {
            pase = 0;
            $("#papellido").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if ($("#sapellido").val().length > 0) {
            if (!patNombre.test($("#sapellido").val())) {
                pase = 0;
                $("#sapellido").focus();
                alert("Caracteres no v\u00E1lidos");
            }
        }

    } else if ($("#tipo").val() == '2') {
        if (
            $("#comercialName").val().length == 0
            || $("#dSocial").val().length == 0
        ) {
            pase = 0;
        }
        var patNombre = new RegExp('^[a-zA-Z0-9][a-zA-Z0-9-áéíóúüñ _\]{1,50}$');
        if (!patNombre.test($("#comercialName").val())) {
            pase = 0;
            $("#comercialName").focus();
            alert("Caracteres no v\u00E1lidos");
        }
        if (!patNombre.test($("#dSocial").val())) {
            pase = 0;
            $("#dSocial").focus();
            alert("Caracteres no v\u00E1lidos");
        }
    }

    if (prof == 2) {//para los Clientes
        //chequeo de fechas
        var patFecha = new RegExp("(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-(?:19|20)[0-9]{2}");
        if (!patFecha.test($("#fecha").val())) {
            pase = 0;
            $("#fecha").focus();
            alert("La fecha no cumple con el patr\u00F3n recomendado 'dd-mm-yyyy'"); 
        }
        var date = new Date();
        fechaArr = $("#fecha").val().split('-');

        if (fechaArr[2] > date.getFullYear() ) {
            alert('Error en la fecha');
            $("#fecha").focus();
            pase = 0;
        }
        
        if (fechaArr[2] == date.getFullYear() && fechaArr[1] >= date.getMonth())  {
            alert('Error en la fecha');
            $("#fecha").focus();
            pase = 0;
        }

        var patDocum = new RegExp('^[a-zA-Z0-9][a-zA-Z0-9-áéíóúüñ_\.]{3,20}$');
        if (!patDocum.test($("#documento").val())) {
            pase = 0;
            $("#documento").focus();
            alert("Caracteres no v\u00E1lidos");
        }

        if (pase == 1) {
			esperafn();
            var arrfecha = $("#fecha").val().split('-');
            var fecha = arrfecha[2]+'-'+arrfecha[1]+'-'+arrfecha[0];
            $.post("datos.php", {
                Name:                   $("#nombre").val(),
                LastName1:              $("#papellido").val(),
                LastName2:              $("#sapellido").val(),
                PersonType:             $("#tipo").val(),
                CommercialName:         $("#comercialName").val(),
                BusinessName:           $("#dSocial").val(),
                DateOfBirth:            fecha,
                Activity:               $("#activ").val(),
                MainActivityDesc:       $("#activExtr").val(),
                OriginISOCountryCode:   $("#paiso").val(),
                Gender:                 $('select[name=sexo] :selected').val(),
                IsPublicOffice:         $('select[name=persona] :selected').val(),
                BusinessPerson:         $('select[name=negocio] :selected').val(),
                Document:               $("#documento").val(),
                ExpeditionCountryISO2:  $("#paise").val(),
                PersonProfile:          $("#PersonProfile").val(),
                DocumentType:           $("#tdoc").val(),
                func:                   'setUser',
                dato:                   26
            },function(data){
				data = data.replace('[]','');
                var datos = eval('(' + data + ')');
                if(datos.error.length > 3) alert(utf8Decode(datos.error));
                else alert("La Persona ha sido correctamente inscrita con Id: "+datos.pase);
                esperafn();
            });
        }
    } else { //para los beneficiarios

        if (pase == 1) {
            esperafn();
            $.post("datos.php", {
                Name:                   $("#nombre").val(),
                LastName1:              $("#papellido").val(),
                LastName2:              $("#sapellido").val(),
                CommercialName:         $("#comercialName").val(),
                BusinessName:           $("#dSocial").val(),
                PersonType:             $("#tipo").val(),
                RelationatedPersonId:   $("#persona").val(),
                RelatedTypeIds:         $("#trel").val()*1,
                func:                   'setBeneficiario'
            },function(data){
				data = data.replace('[]','');
                var datos = eval('(' + data + ')');
                if(datos.error.length > 3) alert(utf8Decode(datos.error))
                else alert("La Persona ha sido correctamente inscrita con Id: "+datos.pase);
                esperafn();
            });
        }

    }
} 

const cambiaTipo=(val)=>{
    if (val == 'Legal' || val == '2') {
        $(".plegal").show();
        $(".sexo").hide();
        $("#nombre").val('');
        $("#papellido").val('');
        $("#sapellido").val('');
        $(".pfisica").hide();
        $("#lfnac").html('Fecha de Creaci&oacute;n *: ')
    } else {
        $(".pfisica").show();
        $("#dSocial").val('');
        $("#comercialName").val('');
        $(".plegal").hide();
        $(".sexo").show();
        $("#lfnac").html('Fecha de Nacimiento *: ')
    }
}

function isValidCode(code, tipo='swift') {
    if (tipo == 'card') reg = /^([0-9]{13,16})$/;
    else if (tipo == 'iban') reg = /^([A-Z]{2}[ \-]?[0-9]{2})(?=(?:[ \-]?[A-Z0-9]){9,30}$)((?:[ \-]?[A-Z0-9]{3,5}){2,7})([ \-]?[A-Z0-9]{1,3})?$/;
    else reg = /^([A-Za-z]{5,8}[ \-]?[0-9]{0,2}?[A-Za-z]{0,4})$/;

    let regex = new RegExp(reg);
 
    if (code == null) {
        return "false";
    }
 
    if (regex.test(code) == true) {
        return "true";
    }
    else {
        return "false";
    }
}

function esperafn(){
    if ($("#espera").is(':visible')) $('#espera').hide(); else $('#espera').show();
}

function utf8Decode(utf8String) {
    if (typeof utf8String != 'string') throw new TypeError('parameter ?utf8String? is not a string');
    // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
    const unicodeString = utf8String.replace(
        /[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,  // 3-byte chars
        function(c) {  // (note parentheses for precedence)
            var cc = ((c.charCodeAt(0)&0x0f)<<12) | ((c.charCodeAt(1)&0x3f)<<6) | ( c.charCodeAt(2)&0x3f);
            return String.fromCharCode(cc); }
    ).replace(
        /[\u00c0-\u00df][\u0080-\u00bf]/g,                 // 2-byte chars
        function(c) {  // (note parentheses for precedence)
            var cc = (c.charCodeAt(0)&0x1f)<<6 | c.charCodeAt(1)&0x3f;
            return String.fromCharCode(cc); }
    );
    return unicodeString;
}