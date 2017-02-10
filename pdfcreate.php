<?
require_once("./pdflibs/fpdf.php");
require_once("./pdflibs/fpdi.php");


function converttopdf($firstname,$lastname,$jobtitle,$phone,$email,$subject,$body){

//Wir legen ein paar Variablen an, weil wir es können
$pname=utf8_decode($firstname." ".$lastname);
$pjobtitle=utf8_decode($jobtitle);
$pphone=utf8_decode($phone);
$email=utf8_decode($email);
$psubject=utf8_decode($subject);
$pbody=utf8_decode($body);
$padress=utf8_decode(COMPANYSTREET);
$timestamp = time();
$datum = date("d.m.Y",$timestamp);

//Hier beginnt der ganze PDF Schlotterkram
$pdf= new fpdi('P','mm'); 

//Hier wird das Template eingebunden
$pagecount = $pdf->setSourceFile("./pdflibs/pdftemplate.pdf"); 

$tplidx = $pdf->ImportPage(1); 
$pdf->addPage(); 
$pdf->useTemplate($tplidx,0,0,0); 

//Hier werden die Fonts eingebunden, wichtig die Fonts müssen im Font Ordner liegen. Und sie müssen konvertiert sein.
$pdf->AddFont('Opensans', '', 'Opensans-regular.php');
$pdf->AddFont('Opensans', 'B', 'Opensans-bold.php');

//Hier wird der Zeilenumbruch definiert, das 2. Argument gibt den Abstand vom Fuß an.
$pdf->SetAutoPageBreak(true, 50);

//Pressemitteilung oben stehen lassen
$pdf->SetFont("Opensans", "", 18);
$pdf->Text(20, 55, "P R E S S E M I T T E I L U N G");
//Daten des Absenderfeldes oben rechts und das Datum
$pdf->SetFont("Opensans", "B", 12);
$pdf->Text(125, 55, "$pname");
$pdf->SetFont("OpenSans","",10);
$pdf->Text(125, 62, "$pjobtitle");
$pdf->Text(125, 67, "$padress");
$pdf->Text(125, 71, "COMPANYCITY");
$pdf->Text(125, 77, "Tel.: $pphone");
$pdf->Text(125, 82, "$pmail");
$texting=COMPANYCITYNAME. ", ".$datum;
$pdf->Text(125, 90, "$texting");

//Festlegen der Betreffzeile
$pdf->SetFont("Opensans","B",12);
$pdf->SetXY(25, 110);
$pdf->Multicell(160, 5, "$psubject"); 

//Hier kommt dann der eigentliche TextBody
$pdf->SetFont("Opensans","",12); 
$pdf->SetXY(25, 125);
$pdf->Multicell(160, 5, "$pbody");

// hier wird das PDF als String ausgegeben.
$attachment = $pdf->Output(''.'S');
//$pdf->Output(I);
return $attachment;
}

?>
