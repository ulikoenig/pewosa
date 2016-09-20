<?php

// include FPDF
require_once('fpdf.php');

// create document
$pdf = new FPDF();

// add font
$pdf->AddFont('Opensans-regular', '', 'Opensans-regular.php');

// add page
$pdf->AddPage();

// set new font
$pdf->SetFont('Opensans-regular', '', 12);

// write text
$pdf->Write(10,'Viel Erfolg mit Ihrer neuen Schriftart Opensans-regular in FPDF!');

// output
$pdf->Output();

?>
