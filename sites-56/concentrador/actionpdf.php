<?php
include_once 'admin/classes/tcpdf/config/tcpdf_config.php';
include_once 'admin/classes/tcpdf/tcpdf.php';

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = K_PATH_IMAGES.'aisLogo.jpg';
		$this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Title
		$this->Cell(90, 15, 'AISRemesas Voucher', 10, false, 'C', 0, '', 0, false, 'M', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('');
$pdf->SetAuthor('AISRemesas');
$pdf->SetTitle('AISRemesas Voucher');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setFooterData();

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Ln(15);
$pdf->Write(0, 'Detalles de la transacción', '', 0, '', true, 0, false, false, 0);
$pdf->SetFont('helvetica', '', 11);

$pdf->SetLineWidth(0);

$pdf->Ln(5);
$border = array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
$pdf->Cell(90, 0, 'Id:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, '263', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Date:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, '13/04/2016', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Sender:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'ana gabriela monzon', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Beneficiary:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'maria jany jimenez del toro', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Total amount to pay:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, '$ 12.26 EUR', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Beneficiary receives:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, '$ 10.00 CUC', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, 'Fee amount:', $border, 0, 'R', 1, '', 0, false, 'T', 'C');
$pdf->Cell(90, 0, '$ 5.97 EUR', $border, 1, 'L', 1, '', 0, false, 'T', 'C');
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Ln(10);
$pdf->Write(0, 'Si tiene alguna pregunta acerca de la entrega o el seguimiento de su transacción, por favor póngase en contacto con nosotros en:', '', 0, '', true, 0, false, false, 0);
$pdf->Ln(5);
$pdf->writeHTML('Email: <a href="mailto:info@aisremesascuba.com">info@aisremesascuba.com</a>', true, false, true, false, '');

// ---------------------------------------------------------


$pdf->Output($_SERVER['DOCUMENT_ROOT'].'desc/example_001.pdf', 'F');




?>