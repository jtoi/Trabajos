<?php define('_VALID_ENTRADA', 1);
require_once('configuration.php');
$d = $_REQUEST;
$contenido = file_get_contents("plantillas/main.html");
$contenido = str_replace("{{estaUrl}}", _ESTA_URL, $contenido);
$script = 'persona';
$add = '';
if (isset($d['var'])) $script = $d['var'];

switch ($script) {
    case 'persona':
        $title = "Entrar Personas";
        break;

    case 'persummary':
        $title = "Data Summary";
        break;

    case 'relacion':
        $title = "Create Relation";
        break;

    case 'pupdate':
        $title = "Person Update";
        break;

    case 'direccion':
        $title = 'Create Address';
        break;

    case 'direcupdate':
        $title = 'Update Address';
        break;

    case 'document':
        $title = 'Document Upload';
        $add = '?var=2';
        break;

    case 'documentb':
        $title = 'Document Upload';
        $add = '?var=1';
        $script = 'document';
        break;

    case 'docupdate':
        $title = 'Document Update';
        break;

    case 'docfalta':
        $title = 'Missing Document';
        break;

    case 'contact':
        $title = 'Create Contact';
        break;

    case 'conupdate':
        $title = 'Update Contact';
        break;

    case 'beneficiario':
        $title = 'Create Beneficiary';
        break;

    case 'cuenta':
        $title = 'Create Account';
        break;

    case 'ctaupdate':
        $title = 'Update Account';
        break;

    case 'ctabalance':
        $title = 'Account Balance';
        break;

    case 'transaccionTPV':
        $title = 'PayIn con TPV';
        break;

    case 'transaccion':
        $title = 'PayIn sin TPV';
        break;

    case 'transferencia':
        $title = 'Transferencia';
        break;

    case 'payout':
        $title = 'PayOut';
        break;

    case 'perver':
        $title = 'Ver Personas';
        break;

    case 'uprel':
        $title = 'Actualizar Relación';
        break;

    default:
        $title = "Entrar Personas";
        break;
}

echo str_replace(
    '{{titulo}}',
    $title,
    str_replace(
        '{{contenido}}',
        file_get_contents('plantillas/' . $script . '.html'),
        str_replace('{{script}}', $script, $contenido)
    )
);
