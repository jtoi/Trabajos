<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
include_once 'admin/classes/tcpdf/config/tcpdf_config.php';
include_once 'admin/classes/tcpdf/tcpdf.php';


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	var $arremp 			= array();	//Datos de las empresas (Bidaiondo, Publinet, etc)
	var $arrcom				= array();	//Datos del Comercio
	var $arrmon 			= array();	//Datos de las monedas
	var $estop 				= '';		//Tipo de operaciones Aceptadas, Devueltas y Reclamadas, etc
	var $arrtabA			= array(9, 25, 45, 26, 50, 20, 26, 22, 22, 22);	//Ancho de las celdas para la tabla de las operaciones
	var $arrtabD			= array(6, 19, 37, 22, 27, 12, 21, 13, 13, 12, 21, 13, 13, 12, 13, 13);	//Ancho de las celdas para la tabla de las operaciones Devueltas
	var $numCier			= ''; 	//Número del Cierre
	var $nombre				= '';	//Nombre del cierre
	var $totMon				= 0;	//Total en la moneda de la operacion
	var $totEur				= 0;	//Total en Euros
	var $totDev				= 0; 	//Total devuelto
	var $tgEur				= 0;	//Total General en Euros
	var $arrTG				= array('EUR'=>0);	//total General de todas las monedas.
	var $imgEmp				= '';	//Imagen de la empresa a usar en encabezado y pie


	//Page header
	public function Header() {
		// Logo
		$this->imgEmp = $this->arremp[3];
		$this->Image(K_PATH_IMAGES_CIERRE.'e'.$this->imgEmp, 115, 2, 60, '', 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
	}

	//Page footer
	public function Footer() {
		// Logo
		$this->Image(K_PATH_IMAGES_CIERRE.'p'.$this->imgEmp, 70, 190, 150, '', 'JPG', '', 'M', false, 300, '', false, false, 0, false, false, false);
	}

	/**
	 * Inicializa todas las variables
	 */
	public function inicializa() {
		define(K_PATH_IMAGES_CIERRE, $_SERVER['DOCUMENT_ROOT']."concentrador/images/cierre/");
		$this->SetCreator('');
		$this->SetAuthor(utf8_encode($this->arremp[1]));
		$this->nombre = $this->numCier.' Cierre '.$this->arrcom[2].' '.strtoupper(date('dMy'));
		$this->SetTitle(utf8_encode($this->nombre));

		// set default header data
		$this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$this->setFooterData();

		// set header and footer fonts
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
	}
	
	/**
	 * Realiza la primera línea de la tabla de las oepraciones Cliente, Cierre, etc
	 */
	function plineaOper() {

		$this->setFontSubsetting(true);
		$this->totEur = $this->totMon = $this->totDev = 0;
		
		$this->AddPage();
		$this->SetFont('helvetica', 'B', 10);
		$this->SetLineWidth(0);
		$this->Ln(10);
		$this->SetFillColor(225, 0, 0);
		
		$this->SetFont('helvetica', 'B', 10);
		$this->MultiCell(25, 0, 'Cliente:', 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(97, 0, utf8_encode($this->arrcom[2]), 0, 'L', 1, 0, '', '', true);
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('helvetica', 'B', 10);
		$this->MultiCell(25, 0, 'Cierre:', 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(70, 0, utf8_encode($this->numCier), 0, 'L', 1, 0, '', '', true);
		
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('helvetica', 'B', 10);
		$this->MultiCell(25, 0, utf8_encode('Año:'), 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(25, 0, utf8_encode(date('Y')), 0, 'L', 1, 0, '', '', true);
		$this->Ln();
	}
	
	/**
	 * Realiza la segunda linea en la tabla de las operaciones Mes, día, etc
	 */
	function slineaOper() {
		$this->SetFillColor(225, 0, 0);
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell(12, 0, 'Mes:', 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(17, 0, utf8_encode(date('M')), 0, 'L', 1, 0, '', '', true);
		
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell(12, 0, utf8_encode('Día:'), 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(10, 0, utf8_encode(date('d')), 0, 'L', 1, 0, '', '', true);
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell(12, 0, utf8_encode('a las'), 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(15, 0, utf8_encode('00:00h'), 0, 'L', 1, 0, '', '', true);
		
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell(25, 0, utf8_encode('Hasta el día:'), 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(10, 0, utf8_encode(date('d')), 0, 'L', 1, 0, '', '', true);
		$this->SetTextColor(0, 0, 0);
		$this->MultiCell(12, 0, utf8_encode('a las'), 0, 'C', 1, 0, '', '', true);
		$this->SetFont('helvetica', '', 10);
		$this->SetTextColor(255, 255, 255);
		$this->MultiCell(15, 0, utf8_encode('24:00h'), 0, 'L', 1, 0, '', '', true);
		$this->Ln();
	}

	/**
	 * Realiza el encabezado de las páginas que van a mostrar las operaciones del cierre
	 */
	public function EncTrans() {
		$this->plineaOper();

		$this->SetFillColor(133, 133, 133);
		$this->SetTextColor(255, 255, 255);
		$this->SetFont('helvetica', 'B', 10);
		$this->MultiCell(127, 0, utf8_encode('Operaciones '.$this->estop.' en '.$this->arrmon[2]), 0, 'C', 1, 0, '', '', true);

		$this->slineaOper();

		$this->SetFillColor(190, 190, 190);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('helvetica', '', 7);
		$this->MultiCell($this->arrtabA[0], 0, utf8_encode('No.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[1], 0, utf8_encode('Id.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[2], 0, utf8_encode('Comercio'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[3], 0, utf8_encode('Ref.Comer.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[4], 0, utf8_encode('Cliente'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[5], 0, utf8_encode('Ref.Bnco.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[6], 0, utf8_encode('Fecha'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[7], 0, utf8_encode('Val.Inicial '.$this->arrmon[1]), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[8], 0, utf8_encode('Val.Inicial EUR'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabA[9], 0, utf8_encode('Tasa'), 1, 'C', 1, 0, '', '', true);

	}

	/**
	 * Realiza el encabezado de las páginas que van a mostrar las operaciones del cierre
	 */
	public function EncTransD() {
		$this->plineaOper();

		$this->SetFillColor(133, 133, 133);
		$this->SetTextColor(255, 255, 255);
		$this->SetFont('helvetica', 'B', 10);
		$this->MultiCell(127, 0, utf8_encode('Operaciones '.$this->estop), 0, 'C', 1, 0, '', '', true);

		$this->slineaOper();

		$this->SetFillColor(190, 190, 190);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('helvetica', '', 6);
		$this->MultiCell($this->arrtabD[0], 0, utf8_encode('No.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[1], 0, utf8_encode('Id.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[2], 0, utf8_encode('Comercio'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[3], 0, utf8_encode('Ref.Comer.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[4], 0, utf8_encode('Cliente'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[5], 0, utf8_encode('Ref.Bnco.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[6], 0, utf8_encode('Fecha Ini'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[7], 0, utf8_encode('ValIni mon.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[8], 0, utf8_encode('ValIni EUR'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[9], 0, utf8_encode('Tasa'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[10], 0, utf8_encode('Fecha Dev'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[11], 0, utf8_encode('Dev. mon.'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[12], 0, utf8_encode('Dev EUR'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[13], 0, utf8_encode('Tasa'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[14], 0, utf8_encode('Valor EUR'), 1, 'C', 1, 0, '', '', true);
		$this->MultiCell($this->arrtabD[15], 0, utf8_encode('Estado'), 1, 'C', 1, 0, '', '', true);

	}

	/**
	 * Llena las tablas con las oepraciones
	 * @param array $arrOper
	 */
	public function llenaOper($arrOper, $tipo) {
		$this->Ln();
		// 		$this->SetFont('helvetica', '', 7);
		for ($i=0; $i<count($arrOper); $i++) {
			if ($tipo == 1) {// para operaciones Aceptadas
				$this->MultiCell($this->arrtabA[$i], 0, utf8_encode($arrOper[$i]), 1, 'C', 0, 0, '', '', true);
				if ($i == 7) $this->totMon += str_ireplace(",", "", $arrOper[$i]);
				if ($i == 8) $this->totEur += str_ireplace(",", "", $arrOper[$i]);
			} else {//para operac devueltas, anuladas o reclamadas
				$this->MultiCell($this->arrtabD[$i], 0, utf8_encode($arrOper[$i]), 1, 'C', 0, 0, '', '', true);
				if ($i == 8) $this->totEur += str_ireplace(",", "", $arrOper[$i]);
				if ($i == 12) $this->totMon += str_ireplace(",", "", $arrOper[$i]);
				if ($i == 14) $this->totDev += str_ireplace(",", "", $arrOper[$i]);
			}
		}
	}

	/**
	 * Rellena la línea de los totales debajo de cada página de moneda
	 */
	public function totales($tipo) {
		$this->Ln();
		if ($tipo == '1') {
			$arrEsp = array(175, 26, 22, 22, 22);
			$arrVal = array('','Total', $this->totMon, $this->totEur, '');
			$arrFdo = array(0,1,1,1,0);
		} else {
			$arrEsp = array(123, 34, 13, 46, 13, 12, 13, 13);
			$arrVal = array('', 'Total', $this->totEur, '', $this->totMon, '', $this->totDev, '');
			$arrFdo = array(0,1,1,0,1,0,1,0);
		}
		
		for ($i=0; $i<count($arrEsp); $i++){
			$this->MultiCell($arrEsp[$i], 0, $arrVal[$i], 1, 'C', $arrFdo[$i], 0, '', '', true);
		}

		//Llenar los totales
		$this->tgEur += $this->totEur;
		foreach ($this->arrTG as $key => $value) {
			if ($key == $this->arrmon[1])
				$value = $this->totMon;
				else $this->arrTG[$this->arrmon[1]] = $this->totMon;
		}
	}
}
?>