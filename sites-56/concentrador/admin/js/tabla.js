function cambia(renglon, acc) {
    if (acc == 'over') document.getElementById(renglon).bgColor = '#CCCCCC';
    else document.getElementById(renglon).bgColor = '';
}

function alerta(valor = null, estado = null, id = null, accion, solDe = null) {

    if (accion == 'T') {
        if (estado == "Transferencia") {
            /*document.pag.factura.value=id;
            envia(this.form);*/
            window.open('index.php?componente=comercio&pag=factura&tf=' + id, "_self");
            //			return true;

        } else alert('<?php echo _GRUPOS_ALERTA_FACT; ?>');

        return false;
    }

    if (accion == 'M') {
        document.forms[0].inserta.value = id;
        document.forms[0].submit();
        return true;
    } else if (accion == 'F') {
        document.pag.pagar.value = id;
        document.pag.submit();
        return true;
    } else if (accion == 'P' && (estado != 'P')) {
        if (confirm('<?php echo $alerta4 ?>')) {
            document.pag.pagar.value = id;
            document.pag.submit();
            return true;
        }
    } else {
        if (valor > 0 && (estado == 'V' || estado == 'A') && solDe == 1) {
            alert('<?php echo $alerta5 ?>');
        } else if (valor > 0 && (estado == 'V' || estado == 'A')) {
            if (accion == 'A') {
                if (confirm('<?php echo $alerta2 ?>')) {
                    document.pag.borrar.value = id;
                    document.pag.submit();
                    return true;
                }
            } else if (accion == 'S') {
                window.open('index.php?componente=comercio&pag=solde&tf=' + id, "_self");
            } else {
                document.pag.cambiar.value = id;
                document.pag.submit();
                return true;
            }
        } else {
            alert('<?php echo $alerta3 ?>');
        }
    }
    return false;
}

//$(document).ready(function(){
function transf(cierre) {
    $(".alerti").esperaDiv('muestra');
    $.post('componente/comercio/ejec.php', {
        fun: 'inscierre',
        cie: cierre
    }, function(data) {
        var datos = eval('(' + data + ')');
        $('.alerti').esperaDiv('cierra');
        $("#enviaForm").show();
        if (datos.error.length > 0) alert(datos.error);
        if (datos.pase.length > 0) {
            $("#salCierre").html(datos.pase[1]);
        }
    });
}
//});
