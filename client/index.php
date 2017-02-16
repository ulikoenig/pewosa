<?php
if (file_exists("config.php") && is_readable("config.php")) {
	include_once("config.php");
} else {
	//header("Location: ./index.php?Error=CONFIGMISSING",TRUE,307);
	echo "Fehler: config.php nicht gefunden";
	die;  
}
 
function fetchstringvartmp ($varname,$defaultvalue)
{
	If (isset($_POST[$varname])) 
		{$returnvar = $_POST[$varname];
		} else 	If(isset($_GET[$varname])) 	
		{ $returnvar = $_GET[$varname];
		};
	if ( ! is_string ($returnvar ) ) 
		{ $returnvar = $defaultvalue;
		}
	return  $returnvar;
}


function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function throw404($request){
	http_response_code(404);
	$fehler = "<h1>Fehler 404</h1><p>Datei ".$request." nicht gefunden.</p>";
	//header("Location: ./index.php?Error=FILEMISSING&msg=".$fehler,TRUE,307);
	die ($fehler);
}

unset($request);
$request = fetchstringvartmp("requestphp","");

chdir(getcwd()."/".MASTERINSTALLDIR);
//echo "Request $request"; 

//Startseite aufrufen
if ($request == "index.php"){
	$request = fetchstringvartmp("requestfile","");

	//böse abfragen abfangen
	if (substr($request, 0, 1 ) === "/") {throw404($request);}
	if (strpos($request, '..') == true) {throw404($request);}
	if (endsWith($request,"php") == true) {throw404($request);}
	if (strpos($request, 'intern/') == true) {throw404($request);}
	if (strpos($request, 'lib/') == true) {throw404($request);}
	if (strpos($request, 'pdflibs/') == true) {throw404($request);}
 
	if ($request == ""){
		header("Location: ./messagelist.php",TRUE,301);
		exit;}
	else {
		if ((endsWith($request,"png") == true) or
		    (endsWith($request,"jpg") == true) or
		    (endsWith($request,"css") == true)){
			if (file_exists($request)) {
	           	 	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
				header("Cache-Control: public"); // needed for internet explorer
				header("Content-Type: ".mime_content_type($request));
				header("Content-Transfer-Encoding: Binary");
				//header("Content-Length:".filesize($request));
				readfile($request);
			exit;        
	        	} else throw404($request);
		} else throw404($request);
	}
} else {
	//böse abfragen abfangen
	if (substr($request, 0, 1 ) === "/") {throw404($request);}
	if (strpos($request, '..') == true) {throw404($request);}
	if (strpos($request, 'intern/') == true) {throw404($request);}
	if (strpos($request, 'lib/') == true) {throw404($request);}
	if (strpos($request, 'pdflibs/') == true) {throw404($request);}


	//Nicht die Startseite

	if (file_exists($request) && is_readable($request) && endsWith($request,"php")) {
		include_once($request);
	} else {
		throw404($request);
	}
}

?>
