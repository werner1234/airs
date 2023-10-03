<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/04 16:38:51 $
 		File Versie					: $Revision: 1.2 $

 		$Log: rapportFrontofficeManagementSelectieold.php,v $
 		Revision 1.2  2020/03/04 16:38:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/11/13 15:43:38  rm
 		7929
 		
 		Revision 1.135  2018/11/16 16:39:19  rvv
 		*** empty log message ***
 		
 		Revision 1.134  2018/11/05 06:49:00  rvv
 		*** empty log message ***
 		
 		Revision 1.133  2018/11/03 18:47:36  rvv
 		*** empty log message ***
 		
 		Revision 1.132  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.131  2018/09/29 16:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.130  2018/09/09 10:03:30  rvv
 		*** empty log message ***
 		
 		Revision 1.129  2018/09/08 17:38:55  rvv
 		*** empty log message ***
 		
 		Revision 1.128  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.127  2018/07/25 15:33:43  rvv
 		*** empty log message ***
 		
 		Revision 1.126  2018/06/13 15:19:26  rvv
 		*** empty log message ***
 		
 		Revision 1.125  2018/06/10 14:41:03  rvv
 		*** empty log message ***
 		
 		Revision 1.124  2018/05/30 16:07:23  rvv
 		*** empty log message ***
 		
 		Revision 1.123  2018/04/08 07:29:15  rvv
 		*** empty log message ***
 		
 		Revision 1.122  2018/03/28 15:55:27  rvv
 		*** empty log message ***
 		
 		Revision 1.121  2018/03/25 10:15:31  rvv
 		*** empty log message ***
 		
 		Revision 1.120  2018/03/19 07:20:27  rvv
 		*** empty log message ***
 		
 		Revision 1.119  2018/02/19 07:17:00  rvv
 		*** empty log message ***
 		
 		Revision 1.118  2018/02/17 19:16:15  rvv
 		*** empty log message ***
 		
 		Revision 1.117  2018/02/11 13:24:12  rvv
 		*** empty log message ***
 		
 		Revision 1.116  2018/02/04 15:45:37  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2018/01/07 14:04:01  rvv
 		*** empty log message ***
 		
 		Revision 1.114  2018/01/06 19:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.113  2017/11/20 07:45:21  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2017/10/21 17:28:59  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2017/09/18 17:30:08  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2017/08/30 15:01:24  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2017/08/27 07:36:32  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2017/08/19 18:16:21  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2017/08/02 18:21:44  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2017/07/09 11:56:18  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2017/03/18 20:28:27  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2017/02/08 16:22:38  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2017/02/06 07:27:59  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2017/02/05 16:24:16  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2017/02/02 08:11:51  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2016/11/27 11:07:26  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2016/11/24 07:05:22  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2016/10/09 14:57:18  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2016/08/13 16:52:24  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2016/07/03 06:45:27  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2016/07/02 09:31:57  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2016/04/17 17:56:35  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2016/01/31 09:51:23  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2015/11/07 16:43:17  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2015/07/29 16:08:40  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2015/05/03 12:59:54  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2015/04/04 15:13:41  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2015/03/25 14:47:52  rvv
 		*** empty log message ***
 		
 		Revision 1.86  2015/02/11 16:44:19  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2015/02/01 11:08:02  rvv
 		*** empty log message ***
 		
 		Revision 1.84  2015/01/31 19:58:37  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2014/12/15 10:42:10  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2014/12/13 19:38:44  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2014/12/13 19:12:11  rvv
 		*** empty log message ***
 		
 		
*/

//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");

$type='portefeuille';
$maxVink=25;



$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

//$content['body']='onload="javascript:$(\'#zorgplichtSelectie\').hide();selectTab();"';

echo template($__appvar["templateContentHeader"],$editcontent);
flush();

if($_GET['actief'] == "inactief" )
{
  $inactiefChecked = "checked";
  $actief = "inactief";
  $alleenActief = " ";
}
elseif($_GET['actief'] == "positie" )
{
  $positieChecked='checked';
  $actief = "positie";
  
}
else
{
  $actiefChecked = "checked";
  $actief = "actief";
  $alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}

if($_POST['posted'])
{
  include_once("../classes/portefeuilleSelectieClass.php");
  include_once("rapport/rapportVertaal.php");
  include_once("rapport/rapportRekenClass.php");
  include_once("rapport/PDFOverzicht.php");
  include_once("rapport/PDFRapport.php");
  include_once("rapport/CashPositie.php");
  include_once("rapport/Managementoverzicht.php");
  include_once("rapport/Vermogensverloop.php");
  include_once("rapport/RendementPerCategorie.php");
//	include_once("rapport/ManagementoverzichtHAR.php");
  include_once("rapport/Portefeuilleverdeling.php");
  include_once("rapport/Valutarisicooverzicht.php");
  include_once("rapport/Omloopsnelheidsoverzicht.php");
  include_once("rapport/Risicometing.php");
  include_once("rapport/Risicoanalyse.php");
  include_once("rapport/Zorgplichtcontrole.php");
  include_once("rapport/ZorgplichtcontroleDetail.php");
  include_once("rapport/Mandaatcontrole.php");
  include_once("rapport/Restrictiecontrole.php");
  include_once("rapport/PortefeuilleIndex.php");
  include_once("rapport/PortefeuilleParameters.php");
  include_once("rapport/CashLijst.php");
  include_once("rapport/Remisiervergoeding.php");
  include_once("rapport/ClientAnalyse.php");
  include_once("rapport/RapportEigendomsverhouding.php");
  include_once("rapport/RapportAfmExport.php");
  include_once("rapport/Transactieoverzicht.php");
  include_once("rapport/Modelcontrole.php");
  include_once("rapport/RendementDetails.php");
  include_once("rapport/MutatievoorstelFondsen.php");
  include_once("rapport/VkmOpbouw.php");
  
  if(!empty($_POST['datumTm']))
  {
    $dd = explode($__appvar["date_seperator"],$_POST['datumTm']);
    if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
    {
      echo "<b>Fout: ongeldige datum opgegeven!</b>";
      exit;
    }
    else
    {
      $rapJul=form2jul($_POST['datumTm']);
      $valutaDatum = getLaatsteValutadatum();
      $valutaJul = db2jul($valutaDatum);
      if($rapJul > $valutaJul + 86400)
      {
        echo "<b>Fout: kan niet in de toekomst rapporteren.</b>";
        exit;
      }
    }
  }
  else
  {
    echo "<b>Fout: geen datum opgegeven!</b>";
    exit;
  }
  $selectData=$_POST;
  $selectData['datumVan'] 							= form2jul($_POST['datumVan']);
  $selectData['datumTm'] 								= form2jul($_POST['datumTm']);
  $selectData['selectedPortefeuilles'] = $_POST['selectedFields'];
  
  if ($selectData['VermogensbeheerderVan'] == $selectData['VermogensbeheerderTm'])
  {
    $db = new DB();
    $query = "SELECT Layout FROM Vermogensbeheerders WHERE Vermogensbeheerder='" . $selectData['VermogensbeheerderVan'] . "'";
    $db->SQL($query);
    $layoutData = $db->lookupRecord();
  }
  // maak progressbar
  $prb = new ProgressBar(536,8);	// create new ProgressBar
  $prb->color = 'maroon';	// bar color
  $prb->bgr_color = '#ffffff';	// bar background color
  $prb->brd_color = 'Silver';
  $prb->left = 0;	                  // Frame position from left
  $prb->top = 	0;
  $prb->show();	                  // show the ProgressBar
  
  function loadLayoutUser($user)
  {
    $query="SELECT Vermogensbeheerders.Layout FROM Vermogensbeheerders
	JOIN  VermogensbeheerdersPerGebruiker ON (VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder)
	WHERE  VermogensbeheerdersPerGebruiker.Gebruiker = '$user' ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->nextRecord();
    return $data['Layout'];
  }
  
  $userLayout = loadLayoutUser($USR);
  $xlsuitvoer = "xls";
  switch($selectData['soort'])
  {
    case "RendementPerCategorie" :
      $rapport = new RendementPerCategorie( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_REND";
      break;
    case "CashPosities" :
      $rapport = new CashPositie( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_CASH";
      break;
    case "Vermogensverloop" :
      $rapport = new Vermogensverloop( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VERM";
      break;
    case "Managementoverzicht" :
      $rapport = new Managementoverzicht( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_MAN";
      break;
    case "Omloopsnelheid" :
      $rapport = new Omloopsnelheidsoverzicht( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_OML";
      break;
    case "Portefeuilleverdeling" :
      $selectData['title'] = "portefeuille-verdeling";
      $selectData['userLayout'] = $userLayout ;
      $rapport = new Portefeuilleverdeling( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      
      $rapportnaam = $__appvar["bedrijf"]."_VERD";
      break;
    case "Valuta Risico" :
      $rapport = new Valutarisicooverzicht( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VR";
      break;
    case "Risicometing" :
      $rapport = new Risicometing( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_RIM";
      break;
    case "Risicoanalyse" :
      $rapport = new Risicoanalyse( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_RIA";
      break;
    case "Zorgplichtcontrole" :
      $rapport = new Zorgplichtcontrole( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_ZORG";
      break;
    case "ZorgplichtcontroleDetail" :
      $rapport = new ZorgplichtcontroleDetail( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_ZORGD";
      break;
    case "Mandaatcontrole" :
      if ($layoutData['Layout'] > 0 &&  file_exists("rapport/include/layout_".$layoutData['Layout']."/Mandaatcontrole_L" . $layoutData['Layout'] . ".php") )
      {
        include("rapport/include/layout_".$layoutData['Layout']."/Mandaatcontrole_L" . $layoutData['Layout'] . ".php");
        $rapClass="Mandaatcontrole_L".$layoutData['Layout'];
        $rapport = new $rapClass( $selectData );
      }
      elseif ($layoutData['Layout'] > 0 && file_exists("rapport/include/Mandaatcontrole_L" . $layoutData['Layout'] . ".php"))
      {
        include("rapport/include/Mandaatcontrole_L" . $layoutData['Layout'] . ".php");
        $rapClass="Mandaatcontrole_L".$layoutData['Layout'];
        $rapport = new $rapClass( $selectData );
      }
      else
      {
        $rapport = new Mandaatcontrole($selectData);
      }
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_MAND";
      break;
    case "Restrictiecontrole" :
      $rapport = new Restrictiecontrole( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_REST";
      break;
    case "PortefeuilleIndex" :
      $rapport = new PortefeuilleIndex( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_PortIndex";
      break;
    case "PortefeuilleParameters" :
      $rapport = new PortefeuilleParameters( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_PortPar";
      break;
    case "CashLijst" :
      $rapport = new CashLijst( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_CASH";
      break;
    case "Remisiervergoeding" :
      $rapport = new Remisiervergoeding( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_REMV";
      break;
    case "ClientAnalyse" :
      $rapport = new ClientAnalyse( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_CLIENT";
      break;
    case "RapportEigendomsverhouding" :
      $rapport = new RapportEigendomsverhouding( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_Owner";
      break;
    case "afmExport" :
      $rapport = new RapportAfmExport( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_AFM";
      break;
    case "Transactieoverzicht" :
      $rapport = new Transactieoverzicht( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_TRA";
      break;
    case "RendementDetails" :
      $rapport = new RendementDetails( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_REND";
      break;
    case "Modelcontrole" :
      if($selectData['modelcontrole_rapport']=='gecomprimeerd')
        $rapport = new ModelWaardecontrole( $selectData );
      else
        $rapport = new Modelcontrole( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_MOD";
      break;
    case "Mutatievoorstel Fondsen" :
      $rapport = new MutatievoorstelFondsen( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_MUT";
      break;
    case "VkmOpbouw" :
      $rapport = new VkmOpbouw( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VKM";
      break;
  }
  
  $rapportnaam=$rapportnaam.'_'.date('Ymdhis');
  switch($_POST['filetype'])
  {
    case "PDF" :
      $filename = $rapportnaam.".pdf";
      $filetype = "pdf";
      $rapport->pdf->Output($__appvar['tempdir'].$filename,"F");
      break;
    case "cvs" :
      $filename =  $rapportnaam.".csv";
      $filetype = "csv";
      $rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
      break;
    case "xls" :
      if(class_exists('XMLWriter')) //if($__appvar["bedrijf"]=='TEST')
        $xlsuitvoer = "xlsx";
      if($xlsuitvoer == "xlsx")
        $filename =  $rapportnaam.".xlsx";
      else
        $filename =  $rapportnaam.".xls";
      $rapport->pdf->OutputXLS($__appvar['tempdir'].$filename,"F",$xlsuitvoer);
      break;
    case "database" :
      $filetype = "database";
      $rapport->OutputDatabase();
      ?>
      <script type="text/javascript">
        parent.document.location = 'reportBuilder2.php';
      </script>
      <?
      exit;
      break;
    case "order" :
      $tmpOrdernr =  $rapport->OutputOrder();
      if(is_array($tmpOrdernr) && $tmpOrdernr['versie']=='V2')
      {
        ?>
        <script type="text/javascript">
          parent.AEConfirm('<?=$tmpOrdernr['message']?> Wilt u naar de orderregels gaan?', 'Orderregel verwerking',
            function ()
            {
              parent.document.location = 'tijdelijkebulkordersv2List.php?rapportageInvoer=1&resetFilter=1';
            }, function () {  });
        </script>
        <?
      }
      elseif($tmpOrdernr)
      {
        ?>
        <script type="text/javascript">
          parent.document.location = 'orderGenereer.php?tmpOrdernr=<?=$tmpOrdernr?>';
        </script>
        <?
      }
      else
      {
        ?>
        <script type="text/javascript">
          alert('Er zijn geen orderregels aangemaakt binnen deze selectie.');
        </script>
        <?
      }
      exit;
      // location is volgende script met ordernr ?
      break;
  }
  
  // push javascript de PDF te openen in een nieuw window en daarna het bestand verwijderen.
  ?>
  <script type="text/javascript">
    function pushpdf(file,save)
    {
      var width='800';
      var height='600';
      var target = '_blank';
      var location = 'pushFile.php?filetype=<?=$filetype?>&file=' + file;
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
    pushpdf('<?=$filename?>',<?=$save?>);
  </script>
  <?
}
else
{
  
  $DB = new DB();
  $query = "SELECT ModelPortefeuilles.Portefeuille,
				 ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles
		  LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille WHERE Portefeuilles.Einddatum>now() ORDER BY ModelPortefeuilles.Omschrijving";
  
  $DB->SQL($query);
  $DB->Query();
  $aantal = $DB->records();
  $t=0;
  
  while($gb = $DB->NextRecord())
  {
    $t++;
    $Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
  }

// selecteer laatst bekende valutadatum
  $totdatum = getLaatsteValutadatum();
  
  $jr = substr($totdatum,0,4);
  
  $DB= new DB();
  
  $invoerData = array();
  $invoerData['alles']=array('alles'=>'Alles');
  $invoerData['H-cat']=array('alles'=>'Alles');
  $invoerData['cat']=array('alles'=>'Alles');
  $invoerData['H-sec']=array('alles'=>'Alles');
  $invoerData['sec']=array('alles'=>'Alles');
  $invoerData['regio']=array('alles'=>'Alles');
  $invoerData['valuta']=array('alles'=>'Alles');
  
  
  $DB->SQL("SELECT KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
if(KeuzePerVermogensbeheerder.categorie='Beleggingscategorien',Beleggingscategorien.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='Beleggingssectoren',Beleggingssectoren.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='Regios',Regios.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='DuurzaamCategorien',DuurzaamCategorien.Omschrijving,''
)))) as Omschrijving
FROM
KeuzePerVermogensbeheerder
Inner Join VermogensbeheerdersPerGebruiker ON KeuzePerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
LEFT Join Regios ON KeuzePerVermogensbeheerder.waarde = Regios.Regio
LEFT Join Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
LEFT Join Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector
LEFT JOIN DuurzaamCategorien ON KeuzePerVermogensbeheerder.waarde = DuurzaamCategorien.DuurzaamCategorie
WHERE
KeuzePerVermogensbeheerder.categorie IN('Beleggingssectoren','Beleggingscategorien','Regios','DuurzaamCategorien') AND
VermogensbeheerdersPerGebruiker.Gebruiker='$USR' GROUP BY waarde ORDER BY Omschrijving");
  $DB->Query();
  while($cat = $DB->NextRecord())
  {
    if($cat['Omschrijving']=='')
      $cat['Omschrijving']=$cat['waarde'];
    if($cat['categorie'] == 'Regios')
      $invoerData['regio'][$cat['waarde']]=addslashes($cat['Omschrijving']);
    elseif($cat['categorie'] == 'Beleggingscategorien')
      $invoerData['cat'][$cat['waarde']]=addslashes($cat['Omschrijving']);
    elseif($cat['categorie'] == 'Beleggingssectoren')
      $invoerData['sec'][$cat['waarde']]=addslashes($cat['Omschrijving']);
    elseif($cat['categorie'] == 'DuurzaamCategorien')
      $invoerData['duu'][$cat['waarde']]=addslashes($cat['Omschrijving']);
  }
  
  $DB->SQL("SELECT CategorienPerHoofdcategorie.Hoofdcategorie,Beleggingscategorien.Omschrijving
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
Inner Join VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['H-cat'][$cat['Hoofdcategorie']]=addslashes($cat['Omschrijving']);
  
  $DB->SQL("SELECT SectorenPerHoofdsector.Hoofdsector,
Beleggingssectoren.Omschrijving,
VermogensbeheerdersPerGebruiker.Gebruiker
FROM SectorenPerHoofdsector
JOIN Beleggingssectoren ON SectorenPerHoofdsector.Hoofdsector = Beleggingssectoren.Beleggingssector
JOIN VermogensbeheerdersPerGebruiker  ON SectorenPerHoofdsector.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
GROUP BY SectorenPerHoofdsector.Hoofdsector");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['H-sec'][$cat['Hoofdsector']]=addslashes($cat['Omschrijving']);
  
  
  if(count($invoerData['cat']) == 1)
  {
    $DB->SQL("SELECT Beleggingscategorien.Beleggingscategorie, Beleggingscategorien.Omschrijving
  FROM Beleggingscategorien, BeleggingscategoriePerFonds
  WHERE Beleggingscategorien.Beleggingscategorie =  BeleggingscategoriePerFonds.Beleggingscategorie
  GROUP BY Beleggingscategorien.Beleggingscategorie");
    $DB->Query();
    while($cat = $DB->NextRecord())
      $invoerData['cat'][$cat['Beleggingscategorie']]=addslashes($cat['Omschrijving']);
  }
  
  if(count($invoerData['sec']) == 1)
  {
    $DB->SQL("SELECT Beleggingssectoren.Beleggingssector, Beleggingssectoren.Omschrijving
  FROM Beleggingssectoren, BeleggingssectorPerFonds
  WHERE Beleggingssectoren.Beleggingssector =  BeleggingssectorPerFonds.Beleggingssector
  GROUP BY Beleggingssectoren.Beleggingssector");
    $DB->Query();
    while($cat = $DB->NextRecord())
      $invoerData['sec'][$cat['Beleggingssector']]=addslashes($cat['Omschrijving']);
  }
  if(count($invoerData['sec']) == 1)
  {
    $DB->SQL("SELECT DuurzaamCategorien.DuurzaamCategorie, DuurzaamCategorien.Omschrijving
  FROM DuurzaamCategorien JOIN BeleggingssectorPerFonds ON DuurzaamCategorien.DuurzaamCategorie = BeleggingssectorPerFonds.DuurzaamCategorie
  GROUP BY DuurzaamCategorien.DuurzaamCategorie");
    $DB->Query();
    while($cat = $DB->NextRecord())
      $invoerData['duu'][$cat['DuurzaamCategorie']]=addslashes($cat['Omschrijving']);
  }
  
  if(count($invoerData['regio']) == 1)
  {
    $DB->SQL("SELECT Regios.Regio, Regios.Omschrijving
  FROM Regios, BeleggingssectorPerFonds
  WHERE BeleggingssectorPerFonds.Regio =  Regios.Regio
  GROUP BY Regios.Regio");
    $DB->Query();
    while($cat = $DB->NextRecord())
      $invoerData['regio'][$cat['Regio']]=addslashes($cat['Omschrijving']);
  }
  
  if(count($invoerData['valuta']) == 1)
  {
    $DB->SQL("SELECT
Fondsen.Valuta,
Valutas.Omschrijving
FROM
Fondsen
INNER JOIN Valutas ON Fondsen.Valuta = Valutas.Valuta
WHERE Fondsen.Valuta <> ''
GROUP BY Fondsen.Valuta
ORDER BY Omschrijving");
    $DB->Query();
    while($cat = $DB->NextRecord())
      $invoerData['valuta'][$cat['Valuta']]=addslashes($cat['Omschrijving']);
  }
  
  $DB->SQL("SELECT afmCategorien.afmCategorie as type ,
						                afmCategorien.Omschrijving
		 		                    FROM 	afmCategorien
                            JOIN BeleggingscategoriePerFonds ON afmCategorien.afmCategorie=BeleggingscategoriePerFonds.afmCategorie
						                GROUP BY afmCategorien.afmCategorie");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['afm'][$cat['type']]=addslashes($cat['Omschrijving']);
  
  $layouts=array();
  $vermogensbeheerders=array();
  $query="SELECT layout,Vermogensbeheerders.vermogensbeheerder FROM Vermogensbeheerders
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ";
  $DB->SQL($query);
  $DB->Query();
  while($layout = $DB->NextRecord())
  {
    $layouts[] = $layout['layout'];
    $vermogensbeheerders[] = $layout['vermogensbeheerder'];
  }
  
  $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder IN('".implode("','",$vermogensbeheerders)."')";
  $DB->SQL($query);
  $DB->Query();
  
  $zorgplichtSelect = "<option value=\"\" >Allemaal</option>\n";
  while($cat = $DB->NextRecord())
  {
    $zorgplichtSelect .= "<option value=\"".$cat['Zorgplicht']."\" >".$cat['Omschrijving']."</option>\n";
  }
  
  session_start();
  
  $selectie=new selectOptie();
  $html='<form name="selectForm">';
  $selectie->getInternExternActive();
  $html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
  $html.=$selectie->getInternExternHTML($PHP_SELF);
  $html .="<br>";
  if(method_exists($selectie,'getConsolidatieHTML'))
    $html.=$selectie->getConsolidatieHTML($PHP_SELF);
  $html.='</form>';
  
  
  $_SESSION[NAV] = "";
  $_SESSION[submenu] = New Submenu();
  $_SESSION[submenu]->addItem($html,"");
  session_write_close();
  if($actief == "positie")
  {
    $positieJoin = "JOIN ActieveFondsen ON Fondsen.Fonds = ActieveFondsen.Fonds";
    $alleenActief=" AND ActieveFondsen.InPositie = '1' ";
    $fondsPrefix="Fondsen.";
  }
  else
  {
    $fondsPrefix='';
  }
  $koppelObject = array();
  $koppelObject[0] = new Koppel("Fondsen","selectForm",$positieJoin);
  $koppelObject[0]->addFields($fondsPrefix."Fonds","fonds",false,true);
  $koppelObject[0]->addFields($fondsPrefix."ISINCode","",true,true);
  $koppelObject[0]->addFields($fondsPrefix."Omschrijving","",true,true);
  $koppelObject[0]->name = "fonds";
  $koppelObject[0]->extraQuery = $alleenActief;
  
  $koppelObject[1] = new Koppel("Fondsen","selectForm",$positieJoin);
  $koppelObject[1]->addFields($fondsPrefix."Fonds","kostprijsFonds",false,true);
  $koppelObject[1]->addFields($fondsPrefix."ISINCode","",true,true);
  $koppelObject[1]->addFields($fondsPrefix."Omschrijving","",true,true);
  $koppelObject[1]->name = "kostprijsFonds";
  $koppelObject[1]->extraQuery = $alleenActief;
  
  $koppelObject[2] = new Koppel("Fondsen","selectForm",$positieJoin);
  $koppelObject[2]->addFields($fondsPrefix."Fonds","fondsverloopFonds",false,true);
  $koppelObject[2]->addFields($fondsPrefix."ISINCode","",true,true);
  $koppelObject[2]->addFields($fondsPrefix."Omschrijving","",true,true);
  $koppelObject[2]->name = "fondsverloopFonds";
  $koppelObject[2]->extraQuery = $alleenActief;
  
  
  ?>
  <script language=JavaScript src="javascript/popup.js" type=text/javascript></script>
  
  <script type="text/javascript">
    
    <?=$koppelObject[0]->getJavascript()?>
    <?=$koppelObject[1]->getJavascript()?>
    <?=$koppelObject[2]->getJavascript()?>
    <?=$selectie->getSelectJava();?>
    
    
    
    function fondsChange()
    {
      var statusDisabled = false;
      var statusBackground = '#FBFBFB';
      
      for (var i=0; i < document.selectForm.transactieType.length; i++)
      {
        if (document.selectForm.transactieType[i].checked)
        {
          var rad_val = document.selectForm.transactieType[i].value;
        }
      }
      
      if((document.selectForm.fonds.value != '' && rad_val=='enkelvoudig') || (document.selectForm.aankoopFonds.value != '' && rad_val=='switch'))
      {
        statusDisabled = true;
        statusBackground = '#CCCCCC';
        document.selectForm.newFonds.value = '';
        document.selectForm.newFondsISIN.value = '';
        document.selectForm.newFondsValutaCode.value = '';
        document.selectForm.newFondsEenheid.value = '';
      }
      
      if(rad_val=='switch')
      {
        document.selectForm.berekeningswijze.disabled = true;
      }
      else
      {
        document.selectForm.berekeningswijze.disabled = false;
      }
      
      
      if(document.selectForm.fonds.value != '')
      {
        statusDisabled = true;
        statusBackground = '#CCCCCC';
        document.selectForm.newFonds.value = '';
        document.selectForm.newFondsISIN.value = '';
        document.selectForm.newFondsValutaCode.value = '';
        document.selectForm.newFondsEenheid.value = '';
      }
      
      document.selectForm.newFonds.disabled = statusDisabled;
      document.selectForm.newFonds.style.backgroundColor = statusBackground ;
      
      document.selectForm.newFondsISIN.disabled = statusDisabled;
      document.selectForm.newFondsISIN.style.backgroundColor = statusBackground ;
      
      document.selectForm.newFondsValutaCode.disabled = statusDisabled;
      document.selectForm.newFondsValutaCode.style.backgroundColor = statusBackground ;
      
      document.selectForm.newFondsEenheid.disabled = statusDisabled;
      document.selectForm.newFondsEenheid.style.backgroundColor = statusBackground ;
    }
    
    function print()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value = "PDF";
      document.selectForm.save.value = "0";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    
    function saveasfile()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value = "PDF";
      document.selectForm.save.value = "1";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    function csv()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value = "cvs";
      document.selectForm.save.value = "1";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    function xls()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value="xls";
      document.selectForm.save.value="1";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    function database()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value="database";
      document.selectForm.save.value="1";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    function order()
    {
      document.selectForm.target = "generateFrame";
      document.selectForm.filetype.value="order";
      selectSelected();
      if (checkfield())
      {
        document.selectForm.submit();
      }
    }
    
    function checkfield()
    {
      if($("#soort").val()=='Modelcontrole')
      {
        if($('input[name=modelcontrole_rapport]:checked').val()=='vastbedrag')
        {
          var bedrag=$('[name=modelcontrole_vastbedrag]').val();
          if(bedrag=='' || parseFloat(bedrag)==0.0)
          {
            alert('Het bedrag is nog niet opgegeven.');
            return false;
          }
        }
      }
      return true;
    }
    
    
    
    function moveItem(from,to){
      var tmp_text = new Array();
      var tmp_value = new Array();
      for(var i=0; i < from.options.length; i++) {
        if(from.options[i].selected)
        {
          var blnInList = false;
          for(j=0; j < to.options.length; j++)
          {
            if(to.options[j].value == from.options[i].value)
            {
              //alert("already in list");
              blnInList = true;
              break;
            }
          }
          if(!blnInList)
          {
            to.options.length++;
            to.options[to.options.length-1].text = from.options[i].text;
            to.options[to.options.length-1].value = from.options[i].value;
          }
        }
        else
        {
          tmp_text.length++;
          tmp_value.length++;
          tmp_text[tmp_text.length-1] = from.options[i].text;
          tmp_value[tmp_text.length-1] = from.options[i].value;
          
        }
      }
      from.options.length = 0;
      for(var i=0; i < tmp_text.length; i++) {
        from.options.length++;
        from.options[from.options.length-1].text = tmp_text[i];
        from.options[from.options.length-1].value = tmp_value[i];
      }
      from.selectedIndex = -1;
    }
    
    function selectSelected()
    {
      if(document.selectForm['inFields[]'])
      {
        var inFields  			= document.selectForm['inFields[]'];
        var selectedFields 	= document.selectForm['selectedFields[]'];
        
        for(j=0; j < selectedFields.options.length; j++)
        {
          selectedFields.options[j].selected = true;
        }
      }
      if(document.selectForm['modelportefeuille[]'])
      {
        var inFields  			= document.selectForm['inModelportefeuille[]'];
        var selectedFields 	= document.selectForm['modelportefeuille[]'];
        
        for(j=0; j < selectedFields.options.length; j++)
        {
          selectedFields.options[j].selected = true;
        }
      }
      <?=$selectie->getPortefeuilleInternJava()?>
      <?
      if(method_exists($selectie,'getConsolidatieJava'))
        echo $selectie->getConsolidatieJava()
      ?>
      return true;
    }
    
    function selectTab()
    {
      $('#zorgplichtSelectie').hide();
      $('#Risicometing').hide();
      $('#div_filterFonds').hide();
      $('#cashlijstSelectie').hide();
      $('#Modelcontrole').hide();
      $('#portPar').hide();
      $('#Managementinfo').hide();
      $('#Mutatievoorstel').hide();
      $('#Vermogensverloop').hide();
      $('#sm').hide();
      $('#RestrictiecontroleDiv').hide();
      $('#MandaatcontroleDiv').hide();
      $('#RendementDetailsSelectie').hide();
      $('#TransactieTypeDiv').hide();
      
      document.getElementById('knopPDF').style.visibility="visible";
      document.getElementById('knopSAVE').style.visibility="visible";
      document.getElementById('orderButton').style.visibility="hidden";
      document.getElementById('databaseButton').style.visibility="hidden";
      var soort = $("#soort").val();
      
      if(soort == "Risicometing" || soort== "Risicoanalyse")
      {
        $('#Risicometing').show();
      }
      else if(soort == "Managementoverzicht" || soort == "Portefeuilleverdeling" )
      {
        $('#Managementinfo').show();
        document.getElementById('databaseButton').style.visibility="visible";
      }
      else if (soort == "Omloopsnelheid") // portPar
      {
        //document.getElementById('databaseButton').style.visibility="visible";
      }
      else if (soort == "PortefeuilleParameters") // portPar
      {
        $('#portPar').show();
        document.getElementById('knopPDF').style.visibility="hidden";
        document.getElementById('knopSAVE').style.visibility="hidden";
      }
      else if (soort == "CashLijst" )
      {
        $('#cashlijstSelectie').show();
        document.getElementById('knopPDF').style.visibility="hidden";
        document.getElementById('knopSAVE').style.visibility="hidden";
      }
      else if (soort == "ClientAnalyse" || soort == "RapportEigendomsverhouding" ) // cashLijst
      {
        document.getElementById('knopPDF').style.visibility="hidden";
        document.getElementById('knopSAVE').style.visibility="hidden";
      }
      else if (soort == "CashPosities" || soort == "PortefeuilleIndex" )  // cash || index
      {
        
        document.getElementById('knopPDF').style.visibility="hidden";
        document.getElementById('knopSAVE').style.visibility="hidden";
      }
      else if (soort == "Zorgplichtcontrole") // Zorgplicht
      {
        document.getElementById('databaseButton').style.visibility="visible";
        $('#zorgplichtSelectie').show();
      }
      else if (soort == "Modelcontrole") // Modelcontrole
      {
        $('#Modelcontrole').show();
        document.getElementById('orderButton').style.visibility="visible";
      }
      else if (soort == "RendementDetails") // Modelcontrole
      {
        $('#RendementDetailsSelectie').show();
        document.getElementById('knopPDF').style.visibility="hidden";
        document.getElementById('knopSAVE').style.visibility="hidden";
      }
      else if (soort == "Mutatievoorstel Fondsen") // Modelcontrole
      {
        $('#TransactieTypeDiv').show();
        $('#Mutatievoorstel').show();
        $('#sm').show();
        
        for (var i=0; i < document.selectForm.transactieType.length; i++)
        {
          if (document.selectForm.transactieType[i].checked)
          {
            var rad_val = document.selectForm.transactieType[i].value;
          }
        }
        
        if(rad_val=='enkelvoudig')
        {
          mutatieEnkel(rad_val);
        }
        else
        {
          mutatieSwitch(rad_val);
        }
        
        document.getElementById('orderButton').style.visibility="visible";
      }
      else if (soort == "Vermogensverloop") // Vermogensverloop
      {
        $('#Vermogensverloop').show();
      }
      else if (soort == "Restrictiecontrole") // Mandaatcontrole
      {
        $('#RestrictiecontroleDiv').show();
      }
      else if (soort == "Mandaatcontrole") // Mandaatcontrole
      {
        $('#MandaatcontroleDiv').show();
      }
      else
      {
        document.getElementById('Risicometing').style.visibility="hidden";
      }
      
      
    }
    
    
    
    
    function loadField(field)
    {
      inputBox = document.selectForm['invoer'];
      var Waarden = new Array();
      
      <?
      while(list($categorie,$data)= each($invoerData))
      {
        echo "Waarden['$categorie']	= new Array(); \n";
        while(list($waarde,$omschrijving)= each($data))
        {
          echo "Waarden['$categorie']['$waarde']	= '".$omschrijving."'; \n";
        }
      }
      reset($invoerData);
      ?>
      
      for(var count = inputBox.options.length - 1; count >= 0; count--)
      {
        inputBox.options[count] = null;
      }
      
      if (field == 'alles1')
      {
        for (keyVar in Waarden )
        {
          LoadWaarde(Waarden[keyVar]);
        }
      }
      LoadWaarde(Waarden[field]);
    }
    
    function LoadWaarde(waarde)
    {
      inputBox = document.selectForm['invoer'];
      for (keyVar in waarde )
      {
        inputBox.options.length++;
        inputBox.options[inputBox.options.length-1].text = waarde[keyVar];
        inputBox.options[inputBox.options.length-1].value = keyVar;
      }
    }
    
    var ajax = new Array();
    function getAjaxWaarden (sel,formExtra,Veld)
    {
      if(document.getElementById(Veld).options.length < 10)
      {
        var oldValue = document.getElementById(Veld).value;
        if(sel.length>0){
          var index = ajax.length;
          ajax[index] = new sack();
          ajax[index].element = Veld;
          ajax[index].requestFile = 'lookups/ajaxLookup.php?module=queryLookups&query='+sel;	// Specifying which file to get
          ajax[index].onCompletion = function(){ setAjaxWaarden(index,Veld,oldValue,formExtra) };	// Specify function that will be executed after file has been found
          ajax[index].onError = function(){ alert('Ophalen velden mislukt.') };
          ajax[index].runAJAX();		// Execute AJAX function
        }
      }
    }
    function setAjaxWaarden(index,veld,oldValue,formExtra)
    {
      var	Waarden = ajax[index].response;
      var elements = Waarden.split('\n');
      var useDiv=0;
      if(document.getElementById("div_"+veld)){useDiv=1};
      if(elements.length > 1)
      {
        var item='';
        if(useDiv)
        {
          var div_a ='<select name="'+veld+'\" style="width:200px" '+formExtra+' >';
          div_a += '<option value="alles">Alles</option>';
          var selectedA='';
        }
        else
        {
          document.getElementById(veld).options.length=0;
          AddName(veld,'---','');
        }
        for(var i=0;i<elements.length;i++)
        {
          var fields = elements[i].split('\t');
          if(elements[i] != '')
          {
            if(useDiv)
            {
              if(fields[0]==oldValue){selectedA="selected";}else{selectedA=""};
              div_a += '<option value="' + fields[0] + '" ' + selectedA + '>' + fields[1] + '</option>';
            }
            else
            {
              AddName(veld,fields[0],fields[1]);
            }
          }
        }
      }
      if(useDiv)
      {
        div_a += "</select>";
        document.getElementById("div_"+veld).innerHTML=div_a;
      }
      else
      {
        document.getElementById(veld).value = oldValue;
      }
    }
    function AddName(p_SelectName,p_OptionText,p_OptionValue)
    {
      document.getElementById(p_SelectName).options[document.getElementById(p_SelectName).length] = new Option(p_OptionText,p_OptionValue);
    }
    
    
    
    function mutatieEnkel(rad_val)
    {
      $('#fondsVerkoopSpan').hide();
      $('#Smash').show();
      $('#fondsAankoopSpan').hide();
      $('#VoorstelSelectie').show();
      $('#fondsenSelectieKader').hide();
      editSmash(rad_val);
      
      fondsChange();
    }
    
    function mutatieSwitch(rad_val)
    {
      $('#fondsVerkoopSpan').show();
      $('#Smash').hide();
      $('#fondsAankoopSpan').show();
      $('#VoorstelSelectie').show();
      $('#fondsenSelectieKader').hide();
      editSmash(rad_val);
      fondsChange();
    }
    
    function mutatieMeer(rad_val)
    {
      $('#VoorstelSelectie').show();
      $('#fondsVerkoopSpan').hide();
      $('#Smash').hide();
      $('#fondsAankoopSpan').hide();
      $('#fondsenSelectieKader').show();
      editSmash(rad_val);
      fondsChange();
    }
    
    function editSmash(rad_val)
    {
      for (var i=0; i < document.selectForm.transactieType.length; i++)
      {
        if (document.selectForm.transactieType[i].checked)
        {
          var rad_val = document.selectForm.transactieType[i].value;
        }
      }
      if(rad_val=='enkelvoudig__')
      {
        var statusDisabled = true;
        var statusBackground = '#CCCCCC';
      }
      else
      {
        var statusDisabled = false;
        var statusBackground = '#FBFBFB';
        document.selectForm.newFondsKoers.value='';
        document.selectForm.newFondsValutaKoers.value='';
      }
      document.selectForm.newFondsKoers.disabled=statusDisabled;
      document.selectForm.newFondsValutaKoers.disabled=statusDisabled;
      document.selectForm.newFondsKoers.style.backgroundColor=statusBackground;
      document.selectForm.newFondsValutaKoers.style.backgroundColor=statusBackground;
    }
    
    function unsetVastBedrag()
    {
      $('input[name=modelcontrole_vastbedrag]').val('');
      $('input[name=modelcontrole_rebalance]').attr('checked',false);
    }
  
  </script>
  <br><br>
  <div class="tabbuttonRow">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeClientSelectieold.php';" id="tabbutton0" value="Clienten">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeFondsSelectieold.php';" id="tabbutton1" value="Fondsen">
    <input type="button" class="tabbuttonActive" onclick="" id="tabbutton2" value="Management info">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeOptieToolsold.php';" id="tabbutton3" value="Optie tools">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeConsolidatieSelectieold.php';" id="tabbutton4" value="Consolidatie tool">
  </div>
  
  
  <form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="filetype" value="PDF" />
    <input type="hidden" name="portefeuilleIntern" value="" />
    <input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
    <table border="0">
      <tr>
        <td width="540" valign="top">
          <div class="form">
            <fieldset id="Rapport" >
              <legend accesskey="R"><u>R</u>apport</legend>
              <div class="formblock">
                <div class="formlinks"> Rapport </div>
                <div class="formrechts">
                  <select name="soort" id="soort" style="width:200px" onChange="javascript:selectTab();">
                    <option value="Managementoverzicht">Managementoverzicht</option>
                    <option value="Omloopsnelheid">Omloopsnelheid</option>
                    <!--	<option value="Valuta Risico">Valuta Risico</option> -->
                    <option value="Risicometing">Risicometing</option>
                    <option value="Risicoanalyse">Risicoanalyse</option>
                    <option value="Zorgplichtcontrole">Zorgplichtcontrole</option>
                    <option value="CashPosities">Cash Posities</option>
                    <option value="PortefeuilleIndex">Portefeuille Index</option>
                    <option value="PortefeuilleParameters">Portefeuille Parameters</option>
                    <option value="CashLijst">Cash Lijst</option>
                    <option value="Remisiervergoeding">Remisiervergoeding</option>
                    <option value="ZorgplichtcontroleDetail">Zorgplichtcontrole Detail</option>
                    <option value="ClientAnalyse">Client Analyse</option>
                    <option value="RapportEigendomsverhouding">Eigendomsverhouding</option>
                    <option value="afmExport">AFM export</option>
                    <option value="Vermogensverloop">Vermogensverloop</option>
                    <option value="Transactieoverzicht">Transactieoverzicht</option>
                    <option value="Portefeuilleverdeling">portefeuille-verdeling</option>
                    <option value="Modelcontrole">Modelcontrole</option>
                    <option value="Mutatievoorstel Fondsen" <?=($_GET['selectRapport']=="MutatievoorstelFondsen"?"selected":"")?>>Mutatievoorstel Fondsen</option>
                    <option value="Restrictiecontrole">Restrictiecontrole</option>
                    <option value="Mandaatcontrole">Mandaatcontrole</option>
                    <?
                    //if(checkAccess()||$__appvar['bedrijf']=='JAN')//$__appvar['bedrijf']=='HOME' || $__appvar['bedrijf']=='TEST' ||
                    //	echo '<option value="Mandaatcontrole">Mandaatcontrole</option>';
                    
                    if(in_array(12,$layouts))
                      echo '<option value="RendementPerCategorie">Rendement per categorie</option>';
                    ?>
                    <option value="RendementDetails">Rendement Details</option>
                    <option value="VkmOpbouw">VKM-opbouw</option>
                  </select>
                </div>
              </div>
              
              <?
              // portefeuille selectie
              echo $selectie->createDatumSelectie();
              ?>
            
            </fieldset>
            
            <fieldset id="Selectie" >
              <legend accesskey="S"><u>S</u>electie</legend>
              
              <?
              // portefeuille selectie
              if($_SESSION['selectieMethode'] == 'portefeuille')
              {
              ?>
              <script language="Javascript">
              
              </script>
              <table cellspacing="0" border = 1>
                
                <?
                
                
                $DB = new DB();
                $DB->SQL($selectie->queries['ClientPortefeuille']);
                $DB->Query();
                ?>
                <br><br>
                <?
                while($gb = $DB->NextRecord())
                  $data[$gb['Portefeuille']]=$gb;
                echo "<br><br>";
                echo $selectie->createEnkelvoudigeSelctie($data);
                echo "<br><br>";
                ?>
                <br><br>
                <?
                }
                // end portefeuille selectie
                else
                {
                  $DB = new DB();
                  $maxVink=25;
                  $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
                  foreach ($opties as $optie=>$omschrijving)
                  {
                    $data=$selectie->getData($optie);
                    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
                      echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                    else
                      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                  }
                  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters');
                  foreach ($opties as $optie=>$omschrijving)
                  {
                    $data=$selectie->getData($optie);
                    
                    if(count($data) > 1)
                    {
                      if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
                        echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                      else
                        echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                    }
                  }
                  
                  
                }
                if(!method_exists($selectie,'getConsolidatieJava'))
                {
                ?>
                <div class="formblock">
                  <div class="formlinks"> Geconsolideerde portefeuilles opnemen</div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="geconsolideerd">
                  </div>
                </div>
            </fieldset>
          </div>
          <?
          }
          ?>
        </td>
        <td valign="top">
          
          <div class="buttonDiv" id="knopPDF" style="width:130px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> Afdrukken</div><br>
          <div class="buttonDiv" id="knopSAVE" style="width:130px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> Opslaan </div><br>
          <div class="buttonDiv" id="knopCSV" style="width:130px" onclick="javascript:csv();">&nbsp;&nbsp;<?=maakKnop('csv.png',array('size'=>16))?> CSV-export </div><br>
          <div class="buttonDiv" id="knopXLS" style="width:130px" onclick="javascript:xls();">&nbsp;&nbsp;<?=maakKnop('xls.png',array('size'=>16))?> XLS-export </div><br>
          <div class="buttonDiv" id="databaseButton" style="width:130px" onclick="javascript:database();">&nbsp;&nbsp;<?=maakKnop('table.png',array('size'=>16))?> Reportbuilder </div><br>
          <?
          if (checkOrderAcces('rapportages_aanmaken') === true || GetModuleAccess('ORDER') < 2)
            echo '<div class="buttonDiv" id="orderButton" style="width:130px; visibility: hidden;" onclick="javascript:order();">&nbsp; Genereer orders</div><br>';
          else
            echo '<div class="buttonDiv" id="orderButton" style="width:150px; visibility: hidden;" >&nbsp; Geen order rechten</div><br>';
          ?>
          
          <div id="TransactieTypeDiv"  style="display:none">
            <fieldset id="TransactieTypeFieldset">
              <div class="formblock">
                <div class="formlinks">
                  <u>Order type</u><br>
                  <input type="radio" name="transactieType" value="enkelvoudig" checked onClick="javascript:mutatieEnkel();"> Enkelvoudige order <br>
                  <input type="radio" name="transactieType" value="switch" onClick="javascript:mutatieSwitch();">  Switch order  <br>
                  <!--			<input type="radio" name="transactieType" value="meervoudig" disabled onClick="javascript:mutatieMeer();"> Meervoudige order -->
                </div>
              </div>
            </fieldset>
          </div>
          
          <fieldset id="Risicometing" style="display:none">
            <legend accesskey="R"><u>R</u>isico</legend>
            <div class="formblock">
              <div class="formlinks"> Risico methode</div>
              <div class="formrechts">
                <select name="risicoMethode">
                  <option value="perBeleggingscategorie">obv % per beleggingscategorie</option>
                  <option value="perFonds">obv % per fonds</option>
                </select>
              </div>
            </div>
          </fieldset>
          
          <fieldset id="zorgplichtSelectie" style="display:none">
            <legend accesskey="A">zorgplicht opties</legend>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> Alleen portefeuilles die niet voldoen </div>
              <div class="formrechts"> <input type="checkbox" name="zorgplichtVoldoetNiet" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> Alleen categorieën per portefeuille die niet voldoen</div>
              <div class="formrechts"> <input type="checkbox" name="zorgplichtVoldoetNietCategorie" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> Gehanteerde zorgplichtmethodiek</div>
              <div class="formrechts">
                <select name="ZpMethodeKeuze">
                  <option value="aandelen">Volgens categorien </option>
                  <option value="afm">AFM standaarddeviatie</option>
                  <option value="stdev">Werkelijke standaarddeviatie</option>
                  <option value="contractueel">Contractuele methode</option>
                </select>
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks" style="width:300px"> Extra Portefeuilleselectie</div>
              <div class="formrechts">
                <select name="ZorgMethodeFilter">
                  <option value="alles">Alle portefeuilles</option>
                  <option value="contractueel">Contractuele portefeuilles</option>
                  <option value="aandelen">Portefeuilles met categorie methode</option>
                  <option value="afm">Portefeuilles met AFM standaarddeviatie methode</option>
                  <option value="leeg">Portefeuilles zonder paramters</option>
                </select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> Doorkijk (huisfonds) gebruiken</div>
              <div class="formrechts"> <input type="checkbox" name="zorgDoorkijk" value="1"> </div>
            </div>
          </fieldset>
          
          <fieldset id="cashlijstSelectie" style="display:none">
            <legend accesskey="C">Cash Lijst opties</legend>
            <div class="formblock">
              <div class="formlinks"> Rekeningen met 0-saldo tonen </div>
              <div class="formrechts"> <input type="checkbox" name="nulTonen" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks"> Inactieve rekeningen tonen </div>
              <div class="formrechts"> <input type="checkbox" name="inactiefTonen" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks"> Deposito's tonen </div>
              <div class="formrechts"> <input type="checkbox" name="depositoTonen" value="1" > </div>
            </div>
          </fieldset>
          
          <fieldset id="RendementDetailsSelectie" style="display:none">
            <legend accesskey="R">Rendement details opties</legend>
            <div class="formblock">
              <div class="formlinks"> Periode </div>
              <div class="formrechts"> <select name="periode">
                  <option value="maanden">Maanden</option>
                  <option value="dagen">Dagen</option>
                  <option value="weken">Weken</option>
                  <option value="halveMaanden">Twee weken</option>
                </select></div>
            </div>
          </fieldset>
          
          <fieldset id="Managementinfo">
            <legend accesskey="A">M<u>a</u>nagementinfo</legend>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyVermogensbeheerder"> Subtotaal per Vermogensbeheerder
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyAccountmanager">	Subtotaal per Accountmanager
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyRisicoklasse">	Subtotaal per Risicoklasse
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbySoortOvereenkomst">	Subtotaal per SoortOvereenkomst
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="manExtraVelden">	Extra velden
              </div>
            </div>
          
          </fieldset>
          
          <fieldset id="Vermogensverloop" style="display:none">
            <legend accesskey="V">Vermogensverloop</legend>
            
            <div class="formblock">
              <div class="formlinks"> Subtotaal per</div>
              <div class="formrechts">
                <select name="verloopGroupBy">
                  <option value=""> Geen</option>
                  <option value="Vermogensbeheerder"> Vermogensbeheerder</option>
                  <option value="Accountmanager"> Accountmanager</option>
                  <option value="Risicoklasse"> Risicoklasse</option>
                  <option value="SoortOvereenkomst"> SoortOvereenkomst</option>
                  <option value="Depotbank"> Depotbank</option>
                </select>
              </div>
            </div>
            
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="verloopDetails" checked>	Inclusief details
              </div>
            </div>
          
          </fieldset>
          
          
          <?
          $DB = new DB();
          $DB->SQL("SELECT DISTINCT Fondseenheid FROM Fondsen ORDER by Fondseenheid");
          $DB->Query();
          $fondseenheid .= "<option value='' >---</option>\n";
          while($gb = $DB->NextRecord())
          {
            $fondseenheid .= "<option value=\"".$gb['Fondseenheid']."\" >".$gb['Fondseenheid']."</option>\n";
          }
          
          $DB->SQL("SELECT DISTINCT Valuta  FROM Fondsen");
          $DB->Query();
          $valutaCode .= "<option value='' >---</option>\n";
          while($gb = $DB->NextRecord())
          {
            $valutaCode.= "<option value=\"".$gb['Valuta']."\" >".$gb['Valuta']."</option>\n";
          }
          $db = new DB();
          $query="SELECT max(Vermogensbeheerders.OrderuitvoerBewaarder) as OrderuitvoerBewaarder FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
          $db->SQL($query);
          $bewaarder=$db->lookupRecord();
          if($bewaarder['OrderuitvoerBewaarder']==1)
          {
            $DB->SQL("SELECT Depotbank,Omschrijving FROM Depotbanken ORDER BY Depotbank");
            $DB->Query();
            $depotbankOptions .= "<option value=\"\" >---</option>\n";
            while($gb = $DB->NextRecord())
              $depotbankOptions.= "<option value=\"".$gb['Depotbank']."\" >".$gb['Depotbank']." - ".$gb['Omschrijving']."</option>\n";
          }
          
          if($DB->QRecords("SELECT CategorienPerHoofdcategorie.id FROM CategorienPerHoofdcategorie JOIN VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1"))
            $hoofdcategorie='<input type="radio" name="modelcontrole_level" value="hoofdcategorie" >Hoofdcategorie<br>';
          
          
          if($actief == "positie")
          {
            $positieJoin = "JOIN ActieveFondsen a ON f.Fonds = a.Fonds";
            $alleenActief= "a.InPositie = '1'";
            $query="SELECT f.Fonds, f.Omschrijving FROM Fondsen f $positieJoin WHERE ".$alleenActief." ORDER BY Omschrijving";
            $DB->SQL($query);
            $DB->query();
            $fondsen = "";
            while($gb = $DB->NextRecord())
              $fondsen .= "<option value=\"".$gb['Fonds']."\" >".$gb['Omschrijving']."</option>\n";
            
            $getFonds=urlencode(base64_encode(gzcompress($query)));
          }
          else
          {
            $getFonds = urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 " . $alleenActief . " ORDER BY Omschrijving")));
          }
          ?>
          
          <fieldset id="portPar" style="display:none">
            <legend accesskey="A"><u>P</u>ortefeuille parameters</legend>
            
            <table>
              <tr><td><b>Invoer </b></td><td><b>Uitvoer</b> </td><td><b>Filter</b> </td> </tr>
              <tr>
                <td width="120">
                  <input type="radio" name="typeInvoer" value="alles" checked onclick="javascript:loadField('alles')"> Alles <br>
                  <input type="radio" name="typeInvoer" value="H-cat" onclick="javascript:loadField('H-cat')"> Hoofd categorieën <br>
                  <input type="radio" name="typeInvoer" value="cat" onclick="javascript:loadField('cat')"> Categorien <br>
                  <input type="radio" name="typeInvoer" value="H-sec"  onclick="javascript:loadField('H-sec')"> Hoofd sectoren <br>
                  <input type="radio" name="typeInvoer" value="sec"  onclick="javascript:loadField('sec')"> Sectoren <br>
                  <input type="radio" name="typeInvoer" value="regio"  onclick="javascript:loadField('regio')"> Regios <br>
                  <input type="radio" name="typeInvoer" value="valuta"  onclick="javascript:loadField('valuta')"> Valutas <br>
                  <input type="radio" name="typeInvoer" value="afm"  onclick="javascript:loadField('afm')"> AFM categorieën <br>
                  <input type="radio" name="typeInvoer" value="duurzaam"  onclick="javascript:loadField('duu')"> Duurzaam categorieën <br><br>
                </td>
                <td width="200">
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="alles" checked>Alles <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="categorien">Categorieën <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="hoofdCategorien">Hoofd Categorieën <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="hoofdSectoren">Hoofd Sectoren <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="sectoren">Sectoren <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="regios">Regio's <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="valuta">Valuta's <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="afm">AFM categorieën <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="duurzaam">Duurzaam categorieën <br>
                  <input type="radio" name="uitvoer"  value="instrumenten"
                         onclick="$('#div_filterFonds').show(); $('#instrumentAantal').removeAttr('disabled');">Instrumenten
                  <div id="div_filterFonds"><select name="filterFonds" id="filterFonds" style="width:200px;" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);"><?=$fondsen?></select></div><br>
                </td>
                <td width="200" valign="top" >
                  <br>Type<br>
                  <select name="filterType" style="width:200px">
                    <option value="geen">geen filter</option>
                    <option value="groter">groter dan </option>
                    <option value="kleiner">kleiner dan </option>
                    <option value="groterGelijk">groter of gelijk dan </option>
                    <option value="kleinerGelijk">kleiner of gelijk dan </option>
                    <option value="gelijk">gelijk aan </option>
                    <option selected value="nietGelijk">niet gelijk aan </option>
                  </select>
                  <br><br>
                  Waarde<br>
                  <input type="text" value="0" name="filterWaarde" />
              
              </tr>
            
            </table>
            <table>
              <tr>
                <td width="210">
                  Invoer waarde <br>
                  <select name="invoer" style="width:200px">
                    <option value="alles">Alles</option>
                  </select>
                </td>
              
              </tr>
              <tr><td>&nbsp;</td><td>&nbsp;</td> </tr>
              <tr><td> <input type="radio" name="percentages"  value="true" checked> Relatieve waarden.</td><td></td> </tr>
              <tr><td> <input type="radio" name="percentages"  value="" >Absolute waarden</td><td></td> </tr>
              <tr><td> <input type="radio" name="percentages" id="instrumentAantal" disabled value="aantal" >Aantallen</td><td></td> </tr>
            </table>
          </fieldset>
          
          
          
          <div id="Modelcontrole" style="display:none">
            <fieldset id="Modelportefeuille" >
              <legend accesskey="m">M<u>o</u>delcontrole</legend>
              <div class="formblock">
                Modelportefeuille
                <select name="modelcontrole_portefeuille">
                  <option value="">-</option>
                  <?
                  echo "<option value=\"Allemaal\">Allemaal</option>";
                  ?>
                  <?=$Modelportefeuilles?>
                </select>
              </div>
              
              
              
              <div class="formblock">
                <u>Rapportsoort</u><br>
                <input type="radio" name="modelcontrole_rapport" value="gecomprimeerd" onclick="unsetVastBedrag()"> Gecomprimeerd op totaal<br>
                <input type="radio" name="modelcontrole_rapport" value="percentage" checked onclick="unsetVastBedrag()"> Modelcontrole in percentage<br>
                <input type="radio" name="modelcontrole_rapport" value="liquideren" onclick="unsetVastBedrag()"> Liquideren portefeuille<br>
                <input type="radio" name="modelcontrole_rapport" value="vastbedrag"> Mutatievoorstel Portefeuille<br>
                Vast bedrag: <input type="text" name="modelcontrole_vastbedrag" value="" size="4" onchange="$('input[name=modelcontrole_rapport][value=vastbedrag]').attr('checked',true);javascript:checkAndFixNumber(this);">  Incl rebalance: <input type="checkbox" name="modelcontrole_rebalance" value="1" size="4">
              </div>
              
              <div class="formblock">
                <u>Uitvoer soort</u><br>
                <input type="radio" name="modelcontrole_uitvoer" value="alles" checked> Alles<br>
                <input type="radio" name="modelcontrole_uitvoer" value="afwijkingen"> Alleen afwijkingen
                <input type="text" name="modelcontrole_percentage" onChange="javascript:checkAndFixNumber(this);" value="0.0" size="4"> Afwijkingspercentage<br>
              </div>
              
              <div class="formblock">
                <u>Filter</u><br>
                <input type="radio" name="modelcontrole_filter" value="alles"> Alles<br>
                <input type="radio" name="modelcontrole_filter" value="gekoppeld" checked> Alleen gekoppelde depots<br>
              </div>
              
              <div class="formblock">
                <u>Niveau</u><br>
                <input type="radio" name="modelcontrole_level" value="fonds" checked> Fonds<br>
                <input type="radio" name="modelcontrole_level" value="beleggingscategorie" >Categorie<br>
                <?echo $hoofdcategorie;?>
                <input type="radio" name="modelcontrole_level" value="beleggingssector" >Sector<br>
                <input type="radio" name="modelcontrole_level" value="Regio" >Regio<br>
              </div>
            </fieldset>
          </div>
          
          <!-- begin mutatievoorstel fondsen-->
          
          <div id="Mutatievoorstel" style="display : none; position:absolute;">
            <fieldset id="VoorstelSelectie">
              <legend accesskey="e">S<u>p</u>ecificatie</legend>
              
              
              <div id="Fondsoverzicht">
                <div class="formblock">
                  <div class="formlinks"> <span id="fondsVerkoopSpan"> Verkoop </span> Fonds
                    <a href="javascript:getAjaxWaarden('<?=$getFonds?>','',document.getElementById('fonds').name);select_fonds(document.selectForm.newFondsISIN.value);">
                      <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
                  </div>
                  <div class="formrechts" id="div_fonds">
                    <select name="fonds" id="fonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);" onchange="javascript:fondsChange();">
                      <option value="">---</option>
                      <?=$fondsen?>
                    </select>
                  </div>
                </div>
              </div>
              
              
              <span id="fondsAankoopSpan">
      <div class="formblock">
      <div class="formlinks"> Aankoop Fonds
        <a href="javascript:getAjaxWaarden('<?=$getFonds?>','',document.getElementById('aankoopFonds').name);select_aankoopFonds('');">
        <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
      </div>
      <div class="formrechts" id="div_aankoopFonds">
        <select name="aankoopFonds" id="aankoopFonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);" onchange="javascript:fondsChange();">
					<option value="">---</option>
          <?=$fondsen?>
        </select>
      </div>
      </div>
    </span>
              
              
              <div class="formblock">
                
                
                
                
                <br><br>
                <div id="wrapper" style="overflow:hidden;width:=400px;">
                  <div class="buttonDiv" id="fondsButtonNieuw" style="width:120px;float:left;text-align: center;" onclick="$('#newFondsDiv').toggle();"> Nieuw fonds </div>
                  <div class="buttonDiv" id="fondsButtonExtra" style="width:120px;float:left;text-align: center;" onclick="$('#mutatieVoorstelOptieDiv').toggle();"> Extra </div>
                </div>
              </div>
              <div id="newFondsDiv" style="display: none;">
                <div class="formblock">
                  <div class="formlinks"> Nieuwe fonds naam </div>
                  <div class="formrechts">
                    <input type="text" name="newFonds" id="newFonds">
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Fonds ISIN code </div>
                  <div class="formrechts">
                    <input type="text" name="newFondsISIN" id="newFondsISIN">
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Fonds koers </div>
                  <div class="formrechts">
                    <input type="text" name="newFondsKoers" id="newFondsKoers" size="5">
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Fonds valuta koers </div>
                  <div class="formrechts">
                    <input type="text" name="newFondsValutaKoers" id="newFondsValutaKoers" size="5" >
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Fonds valuta code </div>
                  <div class="formrechts">
                    
                    <select name="newFondsValutaCode" id="newFondsValutaCode" style="width:200px" >
                      <?=$valutaCode?>
                    </select>
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Fonds eenheid </div>
                  <div class="formrechts">
                    <select name="newFondsEenheid" id="newFondsEenheid" style="width:200px;" >
                      <?=$fondseenheid?>
                    </select>
                  </div>
                </div>
              </div>
              
              <div id="mutatieVoorstelOptieDiv" style="display: none;">
                <div class="formblock">
                  <div class="formlinks"> &nbsp; </div>
                  <div class="formrechts">
                    <input type="radio" name="actief" id="actief" value="actief" <?=$actiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=actief&selectRapport=Fondsoverzicht'">
                    <label for="actief" title="actief"> Actieve fondsen  </label>
                    <input type="radio" name="actief" id="positie" value="positie" <?=$positieChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=positie&selectRapport=Fondsoverzicht'">
                    <label for="positie" title="actief"> In positie  </label>
                    <input type="radio" name="actief" id="inactief" value="inactief" <?=$inactiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=inactief&selectRapport=Fondsoverzicht'">
                    <label for="inactief" title="actief"> Alle fondsen </label>
                  </div>
                </div>
                
                
                <?if($bewaarder['OrderuitvoerBewaarder']==1){?>
                  <div class="formblock">
                    <div class="formlinks">Order voorkeur depotbank</div>
                    <div class="formrechts">
                      <select name="orderDepotbank" style="width:200px">
                        <?=$depotbankOptions?>
                      </select>
                    </div>
                  </div>
                  <?
                }
                ?>
                
                
                <div class="formblock">
                  <div class="formlinks"> Afronding </div>
                  <div class="formrechts">
                    <input type="text" name="afronding" value="1" size="5">
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Berekeningswijze </div>
                  <div class="formrechts">
                    <select name="berekeningswijze">
                      <option value="Totaal vermogen">Totaal vermogen</option>
                      <option value="Totaal belegd vermogen">Totaal belegd vermogen</option>
                      <option value="Belegd vermogen per beleggingscategorie">Belegd vermogen per beleggingscategorie</option>
                    </select>
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Via norm</div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="berekeningswijzeViaNorm">
                  </div>
                </div>
                
                
                <div class="formblock">
                  <div class="formlinks"> Deposito's uitsluiten </div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="depositoUitsluiten">
                  </div>
                </div>
                
                
                
                <div class="formblock">
                  <div class="formlinks"> Opties weergeven </div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="optiesWeergeven">
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Portrait versie</div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="portraitVersie" <?if($layout == 13)echo "CHECKED";?> >
                  </div>
                </div>
                
                <div class="formblock">
                  <div class="formlinks"> Uitvoer op bewaarder </div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="fondsenOpBewaarder">
                  </div>
                </div>
              </div>
            
            </fieldset>
            
            <fieldset id="fondsenSelectieKader" style="display : none;">
              <!--
              <table>
                <tr>
                  <td>Percentage<input type="text" id="fondsPercentage" name="fondsPercentage" align="right" value="0.0"></td>
                  <td rowspan=4><select id="mutatieVoorstelselectedFondsen" name="selectedFondsen[]" multiple size="8" style="width : 200px"></td>
                </tr>
                <tr><td><input type="checkbox" name="norm" value="1" onclick="editSmash();">Berekening volgens norm</td></tr>
                <tr><td><input type="button" value="Fonds toevoegen." onclick="javascript:appendFonds('mutatieVoorstelselectedFondsen');"></td></tr>
                <tr><td><input type="button" value="Fonds verwijderen." onclick="javascript:removeFonds('mutatieVoorstelselectedFondsen');"></td></tr>
              </table>
              -->
            </fieldset>
            
            <div id="sm" style="display : none; position:absolute;">
              
              <fieldset id="Smash" >
                <legend accesskey="m">S<u>m</u>ash</legend>
                <?
                
                $DB = new DB();
                /*
                $query = "SELECT ModelPortefeuilles.Portefeuille,
				 ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles
		  LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille ".$join ." WHERE Portefeuilles.Einddatum>now() ORDER BY ModelPortefeuilles.Omschrijving";
                
                $DB->SQL($query);
                $DB->Query();
                $aantal = $DB->records();
                $t=0;
                
                while($gb = $DB->NextRecord())
                {
                  $t++;
                  $Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
                }
                */
                $query="SELECT Risicoklasse FROM Risicoklassen JOIN VermogensbeheerdersPerGebruiker ON Risicoklassen.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
                $risicoklassen='';
                $DB->SQL($query);
                $DB->Query();
                while($gb = $DB->NextRecord())
                {
                  $risicoklassen .= "<option value=\"".$gb['Risicoklasse']."\" >".$gb['Risicoklasse']."</option>\n";
                }
                
                
                if($DB->QRecords("SELECT CategorienPerHoofdcategorie.id FROM CategorienPerHoofdcategorie JOIN VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1"))
                  $hoofdcategorie='<input type="radio" name="modelcontrole_level" value="hoofdcategorie" >Hoofdcategorie<br>';
                ?>
                <script>
                  function moveCheckSmash()
                  {
                    if(document.getElementById('typeModel').checked==true)
                    {
                      return true;
                    }
                    else
                    {
                      alert('Alleen mogelijk bij "Via model".');
                      return false;
                    }
                    
                  }
                  function moveBack()
                  {
                    moveItem(document.selectForm['modelportefeuille[]'],document.selectForm['inModelportefeuille[]'],true);
                  }
                
                
                </script>
                
                <div class="formblock">
                  <input type="radio" name="type" value="Handmatig" checked> Handmatig &nbsp;	Percentage: <input type="text" onChange="javascript:checkAndFixNumber(this);" name="percentage" value="0.0" size="4"> <input type="checkbox" value="1" name="nulUitlsuiten"> Aantal 0 niet tonen  <br><br>
                  <input type="radio" name="type" value="Model"> Via model &nbsp;
                  Modelportefeuille:
                  
                  <table cellspacing="0" >
                    <tr>
                      <td>
                        <select name="inModelportefeuille[]" multiple size="8" style="width : auto; margin-left: 8px;"> <?=$Modelportefeuilles?> </select>
                      </td>
                      <td width="10" >
                        <a href="javascript:moveItem(document.selectForm['inModelportefeuille[]'],document.selectForm['modelportefeuille[]']);">
                          <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
                        </a>
                        <br><br>
                        <a href="javascript:moveItem(document.selectForm['modelportefeuille[]'],document.selectForm['inModelportefeuille[]']);">
                          <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
                        </a>
                      </td>
                      <td>
                        <select name="modelportefeuille[]" multiple size="8" style="width : 150px"></select>
                      </td>
                    </tr>
                  </table>
                
                
                </div>
              </fieldset>
            </div>
          
          </div>
          
          <!-- einde mutatievoorstel fondsen -->
          
          <div id="MandaatcontroleDiv" style="display:none">
            <fieldset id="MandaatUitvoer" >
              <legend accesskey="m">M<u>a</u>ndaatcontrole</legend>
              <div class="formblock">
                <div class="formlinks"> Zorgplichtcategorie </div>
                <div class="formrechts">
                  <select name="mandaat_zorgplichtCategorie">
                    <?=$zorgplichtSelect?>
                  </select>
                </div>
              </div>
              
              <div class="formblock">
                <div class="formlinks"> Alleen geconsolideerde portefeuilles weergeven </div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="restrictie_alleenConsolidaties">
                </div>
              </div>
            
            </fieldset>
          </div>
          
          <div id="RestrictiecontroleDiv" style="display:none">
            <fieldset id="RestrictieUitvoer" >
              <legend accesskey="m">R<u>e</u>strictie</legend>
              <div class="formblock">
                <u>Uitvoer soort</u><br>
                <input type="radio" name="restrictie_uitvoer" value="alles" checked> Alles<br>
                <input type="radio" name="restrictie_uitvoer" value="afwijkingen"> Alleen afwijkingen
              </div>
            </fieldset>
          </div>
        
        </td>
      </tr>
    </table>
  
  </form>
  <?echo progressFrame();?>
  <?php
  if($__debug) {
    echo getdebuginfo();
  }
  echo template($__appvar["templateRefreshFooter"],$content);
}
?>
