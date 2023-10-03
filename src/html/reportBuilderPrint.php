<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

include_once("../classes/AE_cls_progressbar.php");
include_once("rapport/FondsenBuilder.php");
include_once("rapport/GeaggregeerdBuilder.php");
include_once("rapport/ManagementBuilder.php");
include_once("rapport/ZorgplichtBuilder.php");

session_start();
$reportBuilder = $_SESSION['reportBuilder'];
session_write_close();

echo template($__appvar["templateContentHeader"],$content);
flush();

//$date = getLaatsteValutadatum();
$date = $reportBuilder['datum'];
$naam = $reportBuilder['naam'];
switch ($reportBuilder['rapport'])
{

	case "Fondsoverzicht" :
		$rapport = new FondsenBuilder($reportBuilder);
		$filename = "rb_fonds-".$naam."-".$date.".xls";
	break;
	case "Geaggregeerd-portefeuille-overzicht" :
		$rapport = new GeaggregeerdBuilder($reportBuilder);
		$filename = "rb_geaggr-".$naam."-".$date.".xls";
	break;
	case "Managementoverzicht" :
		$rapport = new ManagementBuilder($reportBuilder);
		$filename = "rb_manage-".$naam."-".$date.".xls";
	break;
	case "Zorgplichtcontrole" :
		$rapport = new ZorgplichtBuilder($reportBuilder);
		$filename = "rb_zorg-".$naam."-".$date.".xls";
	break;
}

$prb 						= new ProgressBar(536,8);
$prb->color 		= 'maroon';
$prb->bgr_color = '#ffffff';
$prb->brd_color = 'Silver';
$prb->left 			= 0;
$prb->top 			=	0;
$prb->show();

//$filename = "test.csv";

$rapport->progressbar = &$prb;
$rapport->datum = form2jul($reportBuilder['datum']);
$rapport->writeRapport();
//$rapport->OutputCSV($__appvar[tempdir].$filename,"F");
$rapport->OutputXls($__appvar['tempdir'].$filename,"F");
?>
<script type="text/javascript">
function pushpdf(file,save)
{
	var width='800';
	var height='600';
	var target = '_blank';
	var location = 'pushFile.php?filetype=csv&file=' + file;
	if(save == 1)
	{
		// opslaan als bestand
		document.location = location + '&action=attachment';
	}
	else
	{
		// pushen naar PDF reader
		var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
		doc.document.location = location;
	}
}
pushpdf('<?=$filename?>',1);
</script>

<?php
echo template($__appvar["templateRefreshFooter"],$content);
?>