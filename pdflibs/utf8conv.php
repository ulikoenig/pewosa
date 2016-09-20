<?
require_once("./pdflibs/fpdf.php");

class utfFPDF extends FPDF
{
function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
   {
      if ($txt != ''){
         $txt = utf8_decode($txt);
      }
      parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
   }
}
?>
