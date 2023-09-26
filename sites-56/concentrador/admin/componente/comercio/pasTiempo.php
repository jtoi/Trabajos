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
$cantidad = $orden = $horas = 1;
$mod = 0;

if ($d['modifica'] > -1) { // modifica la tupla
    $pos = mueveT(array($d['comercio'],$d['orden'],$d['modifica'])) ;

    $temp->query("insert into tbl_rotComPas (idcom, idpasarela, orden, id, activo, fecha, horas) values ('".$d['comercio']."', '".$d['pasarela']."', '".($pos+1)."', null, 1, unix_timestamp(), '".$d['horas']."')");
    
}

if (isset($d['borrar']) && $d['borrar'] > 0) {
    $temp->query("select idcom, orden, id from tbl_rotComPas where id = ".$d['borrar']);
    mueveT(array($temp->f('idcom'),10000,$temp->f('id')));
}

function mueveT($arrEnt) {
    global $temp;

    $total = 1;
    $where = " idcom = '".$arrEnt[0]."' and activo = 1 and tipo = 0 and horas != 0";

    $temp->query("select id from tbl_rotComPas where $where");
    $toGen = $temp->num_rows();
    if ($arrEnt[1] > $toGen) $arrEnt[1] = $toGen;

    while ($total != 0) {
        $temp->query("select id from tbl_rotComPas where $where and orden = ".$arrEnt[1]);
        $total = $temp->num_rows();

        if ($total > 0) {
            $temp->query("select * from tbl_rotComPas where $where and id != ".$arrEnt[2]." order by orden");
            $arrVals = $temp->loadAssocList();

            $temp->query("delete from tbl_rotComPas where $where ");

            $ord = 1;
            for ($i = 0; $i < count($arrVals); $i++) {
                
                if (($ord * 1) == ($arrEnt[2] * 1))  $ord++;

                $temp->query("insert into tbl_rotComPas (idcom, idpasarela, orden, id, activo, fecha, horas) values ('".$arrVals[$i]['idcom']."', '".$arrVals[$i]['idpasarela']."', '".$ord++."', null, 1, unix_timestamp(), '".$arrVals[$i]['horas']."' )");
            }
        }
    }
    return $arrEnt[2];
}

if ($d['cambiar']) {
    $tit = 'Modificar';
    $q = "select idcom, idpasarela, orden, horas from tbl_rotComPas where id = ".$d['cambiar'];
    $temp->query($q);

    $comer = $temp->f('idcom');
    $pasar = $temp->f('idpasarela');
    $orden = $temp->f('orden');
    $horas = $temp->f('horas');
    $mod = $d['cambiar'];
}

//formulario insertar / modificar
$html = new tablaHTML;
$html->tituloPag = "Pasarela por Horas";
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
$html->inTextb('Horas', $horas, 'horas');
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

echo $html->salida(null,null,true);

$vista = "select r.id, c.nombre comercio, p.nombre pasarela, r.horas, r.orden, r.fecha from tbl_rotComPas r, tbl_pasarela p, tbl_comercio c ";

$where = " where r.tipo = 0 and r.activo = 1 and r.horas != 0 and p.idpasarela = r.idpasarela and c.id = r.idcom";
$orden = " c.nombre, r.orden";

$colEsp = array(array("b", "Borrar Registro", "css_borra", "Borrar")
                , array("e", "Cargar esta Operación", "css_edit", "Ver")
            );
$columnas = array(
                array('Comercio', "comercio", "", "center", "left" ),
                array('Pasarela', "pasarela", "", "center", "center" ),
                array('Horas', "horas", "", "center", "left" ),
                array('Orden', "orden", "", "center", "left" ),
                array('Fecha sub.', "fecha", "", "center", "left" )
            );

if (isset($d['buscr'])) {
    $where .= " and r.idcom in ('".$d['comercio']."') and r.idpasarela in ('".$d['pasarela']."')";
}
            
// echo $vista.$where." order by ".$orden;
$ancho = 900;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>