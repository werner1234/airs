<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/04/13 13:05:18 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_positie_convertVerwerkUBS.php,v $
 		Revision 1.1  2017/04/13 13:05:18  cvs
 		no message
 		
 		Revision 1.6  2016/10/21 10:11:04  cvs
 		call 5240
 		
 		Revision 1.5  2016/03/16 12:54:56  cvs
 		call 4747
 		
 		Revision 1.4  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.3  2014/04/04 08:56:00  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/03/07 13:44:02  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2013/12/16 08:21:00  cvs
 		*** empty log message ***

 		Revision 1.1  2011/07/16 09:52:45  cvs
 		*** empty log message ***


*/

include_once('../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");

include_once("../classes/AE_cls_adventExport.php");
include_once("../classes/AE_cls_lookup.php");
include_once("../config/advent_functies.php");

function makeNr($value)
{
  return str_replace(",",".",$value);
}


//listarray($_GET);
$bank = $_GET["bank"];

$skipFoutregels = array();

$exportCash  = new adventExport();
$exportTrans = new adventExport();
$lkp = new AE_lookup();
$DB = new DB();


$exportCash->fieldsPerLine = 6;
$exportTrans->fieldsPerLine = 8;

//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//


$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
//echo template("../".$__appvar["templateContentHeader"],$content);


//
// setup van de progressbar
//
$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar



// start MT535 verwerking

$error = array();
$csvRegels = 1;
$volgnr = 1;

$progressStep = 0;



$file = $_GET["MT535"];
$regels = count(file($file));
$prb->max = $regels;
$handle = fopen($file, "r");
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$row=0;
$prb->moveMin();
$prb->setLabelValue('txt1','Converteren records ('.$regels.' MT535 records)');

while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;
  $prb->moveNext();

  if ($row < 9) continue;              // skip headers
  if ($data[11] != "TRAD")  continue;  // alleen TRAD regels gebruiken


  //listarray($data);

  $transRec = $lkp->getAdventInfoByEffectenPositie($data[19]."|".$data[54]."|".$data[22],"UBS");
  //  listarray($transRec);
  $datum = dbdate2advent(substr($data[7],0,4)."-".substr($data[7],4,2)."-".substr($data[7],6,2));
  $exportTrans->addField(1,$datum);
  $exportTrans->addField(2,$data[14]);
  $exportTrans->addField(3,$data[22]);
  $exportTrans->addField(4,$data[19]);
  $exportTrans->addField(5,$transRec["Fonds"]);
  $exportTrans->addField(6,$transRec["adventCode"]);
  $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
  $exportTrans->addField(8,$data[24]);
  $exportTrans->pushBuffer();
}
fclose($handle);
unlink($file);
echo "<hr/>";
if (count($error)>0)
{
  echo implode("<br/>", $error);
}
else
{
  echo "Geen fouten gevonden";
}
echo "<hr/>";

$exportTrans->makeCsv("effectenPosities_UBS_");



// start MT941 verwerking

$error = array();
$csvRegels = 1;
$volgnr = 1;

$progressStep = 0;



$file = $_GET["MT941"];
$regels = count(file($file));
$prb->max = $regels;
$handle = fopen($file, "r");
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$row = 0;
$prb->moveMin();
$prb->setLabelValue('txt1','Converteren records ('.$regels.' MT941 records)');

while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;
  $prb->moveNext();

  if ($row < 9) continue;              // skip headers

  $reknr = substr($data[2],0,14)."s1".$data[6];
  if (!$rekRec = $lkp->getRekening(array("rekening"=>$reknr,"depotbank"=>"UBS")))
  {
    $error[] = "UBS rekening: ".$reknr." niet gevonden" ;
  }

  $bedrag = $data[7];
  if ($data[4] == "D")
  {
    $bedrag = $bedrag * -1;
  }

  $datum = dbdate2advent("20".substr($data[5],0,2)."-".substr($data[5],2,2)."-".substr($data[5],4,2));
  $exportCash->addField(1,$datum);
  $exportCash->addField(2,$rekRec["Portefeuille"]);
  $exportCash->addField(3,$reknr);
  $exportCash->addField(4,$data[6]);
  $exportCash->addField(5,$bedrag);
  $exportCash->addField(6,$rekRec["typeRekening"]);
  $exportCash->pushBuffer();


}
fclose($handle);
unlink($file);
echo "<hr/>";
if (count($error)>0)
{
  echo implode("<br/>", $error);
}
else
{
  echo "Geen fouten gevonden";
}



$exportCash->makeCsv("cashPosities_UBS_");



$prb->hide();
?>


<b>Klaar met inlezen <br></b>

<?=$skipped?>
<hr>
<a target="content" href="advent_filemanager.php">Ga naar Advent uitvoermap</a>
<hr>
<?
//echo template("../".$__appvar["templateRefreshFooter"],$content);
?>