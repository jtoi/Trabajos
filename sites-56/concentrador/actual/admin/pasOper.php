<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
$temp = new ps_DB();
$ent = new entrada;
global $temp;
$corCreo = new correo();

$d = $_POST;
if (_MOS_CONFIG_DEBUG) var_dump($d);
$tit = 'Insertar';
$comer = $pasar = $moneda = '';
$cantidad = $orden = 1;
$mod = 0;

if (isset($d['modifica']) && $d['modifica'] > 0) { // modifica la tupla
    $pos = mueveT(array($d['comercio'],$d['moneda'],$d['orden'],$d['modifica'])) ;

    $temp->query("insert into tbl_rotPasarOperac (idcomercio, idpasarela, idmoneda, cantOperac, orden, id, activo, fecha) values ('".$d['comercio']."', '".$d['pasarela']."', '".$d['moneda']."', '".$d['cantidad']."', '".($pos+1)."', null, 1, null)");
    
}

if (isset($d['borrar']) && $d['borrar'] > 0) {
    $temp->query("select idcomercio, idmoneda, orden, id from tbl_rotPasarOperac where id = ".$d['borrar']);
    mueveT(array($temp->f('idcomercio'),$temp->f('idmoneda'),10000,$temp->f('id')));
}

function mueveT($arrEnt) {
    global $temp;

    $total = 1;
    $where = " idcomercio = '".$arrEnt[0]."' and idmoneda = '".$arrEnt[1]."' and activo = 1";

    $temp->query("select id from tbl_rotPasarOperac where $where");
    $toGen = $temp->num_rows();
    if ($arrEnt[2] > $toGen) $arrEnt[2] = $toGen;

    while ($total != 0) {
        $temp->query("select id from tbl_rotPasarOperac where $where and orden = ".$arrEnt[2]);
        $total = $temp->num_rows();

        if ($total > 0) {
            $temp->query("select * from tbl_rotPasarOperac where $where and id != ".$arrEnt[3]." order by orden");
            $arrVals = $temp->loadAssocList();

            $temp->query("delete from tbl_rotPasarOperac where $where ");

            $ord = 1;
            for ($i = 0; $i < count($arrVals); $i++) {
                
                if (($ord * 1) == ($arrEnt[2] * 1))  $ord++;

                $temp->query("insert into tbl_rotPasarOperac (idcomercio, idpasarela, idmoneda, cantOperac, orden, id, activo, fecha) values ('".$arrVals[$i]['idcomercio']."', '".$arrVals[$i]['idpasarela']."', '".$arrVals[$i]['idmoneda']."', '".$arrVals[$i]['cantOperac']."', '".$ord++."', null, 1, null )");
            }
        }
    }
    return $arrEnt[2];
}

if ($d['cambiar']) {
    $tit = 'Modificar';
    $q = "select idcomercio, idpasarela, idmoneda, cantOperac, orden from tbl_rotPasarOperac where id = ".$d['cambiar'];
    $temp->query($q);

    $comer = $temp->f('idcomercio');
    $pasar = $temp->f('idpasarela');
    $moneda = $temp->f('idmoneda');
    $cantidad = $temp->f('cantOperac');
    $orden = $temp->f('orden');
    $mod = $d['cambiar'];
}

//formulario insertar / modificar
$html = new tablaHTML;
$html->tituloPag = "Pasarela por Cantidad de Operaciones - Moneda";
$html->tituloTarea = $tit;
$html->anchoTabla = 650;
$html->tabed = true;
$html->anchoCeldaI = 300;
$html->anchoCeldaD = 340;

$html->inHide($mod, 'modifica');
$q = "select id, nombre from tbl_comercio where  activo = 'S' order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $q,  $comer);
$q = "select idPasarela id, nombre from tbl_pasarela where  activo = '1' order by nombre";
$html->inSelect('Pasarela', 'pasarela', 2, $q,  $pasar);
$q = "select idmoneda id, moneda nombre from tbl_moneda order by moneda";
$html->inSelect('Moneda', 'moneda', 2, $q,  $moneda);
$html->inTextb('Cantidad de operaciones', $cantidad, 'cantidad');
$html->inTextb('Orden', $orden, 'orden');

echo $html->salida();

//formulario buscar
$html = new tablaHTML;
$html->idio = $_SESSION['idioma'];
$html->tituloPag = "";
$html->tituloTarea = "Buscar";
$html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = 170; $html->anchoCeldaD = 320;

$html->inHide(true, 'buscr');
$q = "select id, nombre from tbl_comercio where  activo = 'S' order by nombre";
$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $q,  $comer);
$q = "select idPasarela id, nombre from tbl_pasarela where  activo = '1' order by nombre";
$html->inSelect('Pasarela', 'pasarela', 5, $q,  $pasar);
$q = "select idmoneda id, moneda nombre from tbl_moneda order by moneda";
$html->inSelect('Moneda', 'moneda', 5, $q,  $moneda);

echo $html->salida(null,null,true);

$vista = "select r.id, c.nombre comercio, p.nombre pasarela, m.moneda, r.cantOperac, r.orden, r.fecha from tbl_rotPasarOperac r, tbl_pasarela p, tbl_comercio c, tbl_moneda m ";

$where = " where m.idmoneda = r.idmoneda and r.idcomercio and p.idpasarela = r.idpasarela and c.id = r.idcomercio";
$orden = " c.nombre, m.moneda, r.orden";

$colEsp = array(array("b", "Borrar Registro", "css_borra", "Borrar")
                , array("e", "Cargar esta Operación", "css_edit", "Ver")
            );
$columnas = array(
                array('Comercio', "comercio", "", "center", "left" ),
                array('Pasarela', "pasarela", "", "center", "center" ),
                array('Moneda', "moneda", "", "center", "left" ),
                array('Cantidad', "cantOperac", "", "center", "left" ),
                array('Orden', "orden", "", "center", "left" ),
                array('Fecha sub.', "fecha", "", "center", "left" )
            );

if (isset($d['buscr'])) {
    $where .= " and r.idcomercio in ('".$d['comercio']."') and r.idmoneda in ('".$d['moneda']."') and r.idpasarela in ('".$d['pasarela']."')";
}
            
// echo $vista.$where." order by ".$orden;
$ancho = 900;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>