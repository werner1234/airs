<?php
/*
    AE-ICT CODEX source module versie 1.6, 2 juni 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/02/17 19:29:53 $
    File Versie         : $Revision: 1.42 $

*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("./orderControlleRekenClass.php");
include_once("./rapport/rapportRekenClass.php");


$DB = new DB();
$__funcvar['listurl']  = "orderregelsList.php";
$__funcvar['location'] = "orderregelsEdit.php";

$db = new DB();
$query="SELECT MAX(Vermogensbeheerders.check_module_ORDERNOTAS) AS ordernota,
max(Vermogensbeheerders.OrderuitvoerBewaarder) as OrderuitvoerBewaarder FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
$db->SQL($query);
$rechten=$db->lookupRecord();

$data = $_GET;
$action = $data['action'];

$object = new OrderRegels();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$knoppenOnderUit=false;





//listarray($_GET);
if (!$_GET['orderid'])
{
  $object->getById($_GET["id"]);
  $ordid = $object->get("orderid");
}
else
  $ordid = $_GET['orderid'];

/////////////////////////

  $db = new DB();
  $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$ordid."' ";
  $db->SQL($query);
  $tmp = $db->lookupRecord();
  $aandeelTotaal=$tmp["totaal"];
  $query = "SELECT * FROM Orders WHERE orderid='".$ordid."' ";
  $db->SQL($query);
  $orderRec = $db->lookupRecord();



$koppelObject = array();
$koppelObject[0] = new Koppel("Portefeuilles","editForm");
$koppelObject[0]->addFields("Portefeuille","portefeuille",true,true);
if($rechten['OrderuitvoerBewaarder']==0)
  $koppelObject[0]->addFields("Depotbank","Depotbank",false,false);
$koppelObject[0]->addFields("Risicoklasse","Risicoklasse",false,false);
$koppelObject[0]->addFields("Client","client",true,true);
$koppelObject[0]->name = "port";
$koppelObject[0]->extraQuery = " AND Portefeuilles.einddatum > NOW()";
//if($action == "new")
//  $koppelObject[0]->action = "preSearchNew(portefeuille,'rekeningnr')";
//else
if($rechten['OrderuitvoerBewaarder']==1)
  $koppelObject[0]->action = "preSearch(portefeuille,'rekeningnr');parent.preSearchNew(portefeuille,'Depotbank','".$orderRec["fonds"]."')";
else
  $koppelObject[0]->action = "preSearch(portefeuille,'rekeningnr')"; 
$koppelObject[0]->focus = "aantal";
//$koppelObject[0]->focus = "rekeningnr";

  if($orderRec['Depotbank'] == '' && $_GET['Depotbank'] != '')
  {
    $orderRec['Depotbank'] = $_GET['Depotbank'];
    $query = "UPDATE Orders SET Depotbank = '".$_GET['Depotbank']."' WHERE id = '".$orderRec['id']."'";
    $db->SQL($query);
    $db->Query();
  }

  $orderTxt  = '<table border="0" cellspacing="5" cellpadding="0">';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('order kenmerk') . '</b></td><td>'.$orderRec["orderid"].'-'.$data["id"].'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('fonds / ISIN') . '</b></td><td>'.$orderRec["fondsOmschrijving"].' / '.$orderRec["fondsCode"].'</td></tr>';
  if ($orderRec["transactieType"] == "L" or
      $orderRec["transactieType"] == "SL")
  {
    $trType = '&nbsp;&nbsp;&nbsp;(koers '.$orderRec["koersLimiet"].')';
  }
  if ($tmp["totaal"] <> $orderRec["aantal"])
  {
    $verschilTxt = "<font color=maroon><b> (verschil = ".($tmp["totaal"] - $orderRec["aantal"]).") </b></font> ";

  }
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('transactieType') . '</b></td><td>'.$__ORDERvar["transactieType"][$orderRec["transactieType"]].$trType.'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('transactieSoort') . '</b></td><td>'.$__ORDERvar["transactieSoort"][$orderRec["transactieSoort"]].'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('aantal') . '</b></td><td>'.$orderRec["aantal"].$verschilTxt.'</td></tr>';
  if ($orderRec["tijdsSoort"] == "DAT")
  {
    $looptijd = "&nbsp;&nbsp;(".$orderRec["tijdsLimiet"].")";
  }
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('looptijd') . '</b></td><td>'.$__ORDERvar["tijdsSoort"][$orderRec["tijdsSoort"]].$looptijd.'</td></tr>';
  $orderTxt .= '<tr><td bgcolor="#DDDDDD"><b> ' . vt('huidige status') . '</b></td><td>'.$__ORDERvar["status"][$orderRec["laatsteStatus"]].'</td></tr>';
  $orderTxt .= '</table>';
///////////////////////////


$subHeader = $orderTxt;
$mainHeader    = vt('muteren stukkenlijst') . " <br><br>";

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/orderRegelEdit.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>\n";

$editObject->formVars['Depotbank'] = '<input class="" type="hidden" value="'.$orderRec['Depotbank'].'" name="Depotbank" id="Depotbank">';
$editObject->formVars['DepotbankOld'] = '<input class="" type="hidden" value="'.$orderRec['Depotbank'].'" name="DepotbankOld" id="DepotbankOld">';
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();

$editcontent['javascript'] .= "
function updateScript()
{

 try
  {
    if (ns4)
    {
      var nKey=e.which;
  	  ctrlKey = e.ctrlKey;
    }
    if (ie4)
    {
      var nKey=event.keyCode;
    	ctrlKey = event.ctrlKey;
    }

    if(keySet || ie4)
    {
	    command(nKey);
	    keySet = false;
    }
    else
    {
       keySet = true;
    }
    if(nKey==13)
    {
      if( document.activeElement.name == 'portefeuille')
      {
        lookupPort();
      }
    }
  }
  catch(e){}


}
";
$editcontent['body'] = " onLoad=\"javascript:initScript()\" ";
$editObject->template = $editcontent;

$adding = ($data['id'] == 0 AND $data['adding']<>0)?true:false;


$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "orderregelsEditTemplate.html";

// valuta's array sammenstellen voor huidige rekening
$query = "SELECT * FROM Valutas WHERE TermijnValuta = 0";
$DB->SQL($query);
$DB->Query();
while ($rec = $DB->nextRecord())
{
  $vals[] = $rec["Valuta"];
}

$query = "
  SELECT
    id,
    Rekening,
    Portefeuille,
    Valuta,
    Memoriaal,
    Tenaamstelling,
    Termijnrekening
  FROM
    Rekeningen
  WHERE
    Memoriaal = 0 AND Termijnrekening = 0 AND Inactief = 0 AND Rekening LIKE '".$object->get("rekeningnr")."%'";

$DB->SQL($query);
$DB->Query();
while ($rec = $DB->nextRecord())
{
  $recNu = ereg_replace("[^0-9]","",$rec['Rekening']);
  $valuta = ereg_replace("[^A-Z]","",$rec['Rekening']);
  $reVal = '/^(.*)([A-Z]{3})$/m';
  preg_match_all($reVal, $rec['Rekening'], $matches);
  if(isset($matches[1][0]))
    $recNu=$matches[1][0];
  if(isset($matches[2][0]))
    $valuta=$matches[2][0];
  
  $valRek[] = $valuta;
}

for ($x=0;$x <count($vals);$x++)
{
  if (in_array($vals[$x],$valRek))
    $valList[$vals[$x]] = $vals[$x];
  else
    $valList[$vals[$x]] = $vals[$x]."*";
}
$query = "SELECT * FROM OrderRegels WHERE orderid = '".$ordid."' ORDER BY positie DESC LIMIT 1";
$DB->SQL($query);
$DB->Query();
if($action == "new")
{ 
  $editObject->formTemplate = "orderregelsEditNewTemplate.html";
  //orderregelsList.php?zonderKop=1&orderid={orderid_value}
  $DB->Query();
	if($DB->Records() > 0)
	{
		$volgnr = $DB->NextRecord();
		$newPos = $volgnr["positie"]+1;
	}
	else
	{
		$newPos = 1;
	}
  $object->data['fields']['positie']['value']     = $newPos;
  $object->data['fields']['orderid']['value'] 		= $_GET["orderid"];
  $object->data['fields']['valuta']['value']      = "EUR";
  $object->data['fields']['status']['value']      = 0;


  $object->setOption("portefeuille","form_extra","id=\"valuta\" onChange=\"lookupPort();\"  ");
}
else
{
   $editObject->formTemplate = "orderregelsEditTemplate.html";
   $object->getById($data["id"]);
   $object->setOption("valuta","form_extra","id=\"valuta\" ");
   $object->setOption("valuta","form_options",$valList);
   $object->setOption("portefeuille","form_extra","id=\"valuta\" onChange=\"lookupPort();\"");
   $object->setOption("rekeningnr","form_extra","id=\"rekeningnr\" onBlur=\"preSearch(this.value,'valuta')\" t");
}
//////

if ($action == "update" || $action == "edit")
{
  if($object->get('id') > 0 && $data['valuta'] <> '')
  {
    $object->set('valuta',$data['valuta']);
    $object->save();
  }
  if($object->get('status') < 1)
  {
    $order = new orderControlleBerekening();
    if($data['orderid'] != "" && $data['portefeuille'] !="" && $data['valuta'] != "")
    {
      $order->setdata($data['orderid'],$data['portefeuille'],$data['valuta'],$data['aantal'],	false);
      $query = "	SELECT Vermogensbeheerder FROM Portefeuilles 	WHERE portefeuille = '".$data['portefeuille']."'";
    }
    else
    {
      $order->setdata($object->data['fields']['orderid']['value'],$object->data['fields']['portefeuille']['value'],$object->data['fields']['valuta']['value'],$object->data['fields']['aantal']['value'],	false);
      $query = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille = '".$object->data['fields']['portefeuille']['value']."'";
    }
    $checks = $order->getchecks();
    $db->SQL($query);
  	$vermogenbeheerder = $db->lookupRecord();
  	$vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
    $checks = unserialize($checks[$vermogenbeheerder]);
    $order->setchecks($checks);
    $resultaat = $order->check();
    foreach ($checks as $key=>$value)//Set welke chk box getoond moet worden voor deze vermogensbeheerder
    {
    if ($value['checked']==1)
      $controles[$key]=$__ORDERvar["orderControles"][$key];
    }
  }
}


if ($action == "update" &&  $object->get('status') < 1)
{
	foreach ($__ORDERvar["orderControles"] as $key=>$value)
	{
		$export['controle_regels'][$key]['checked'] = $data["order_controle_checkbox_".$key];
	}
	$oldControle_regels=unserialize($object->get("controle_regels"));

	$object->set("controle_regels",serialize($export["controle_regels"]));
  $order->setdata(	$data['orderid'],$data['portefeuille'],$data['valuta'],$data['aantal'],true);
	$order->setregels($export["controle_regels"]);
	$object->set("controle",$order->checkmax());


	$newChecks='';
  $orderObject = new Orders();
  $orderObject->getById($orderRec['id']);
  //$oldControle_regels=unserialize($orderObject->get("controle_regels"));

	//foreach ($__ORDERvar["orderControles"] as $key=>$value)
	if($object->data['fields']['portefeuille']['value'] <>'')
	  $logPort=$object->data['fields']['portefeuille']['value'];
	else
	  $logPort=$data['portefeuille'];

	foreach ($export["controle_regels"] as $key=>$value)
	{
    if($oldControle_regels[$key]['checked'] != $value['checked'])
      $newChecks .= "\n".date("Ymd_Hi")."/$USR Check ".$logPort." $key ".$oldControle_regels[$key]['checked']." -> ".$value['checked']."";
	}
	//echo $newChecks;
	if($newChecks <> '')
	{
    $orderObject->set("status",$orderObject->get('status').$newChecks);
    $orderObject->save();
  }

}
else
{
	$export['controle_regels'] = unserialize($object->get("controle_regels"));
}

if($object->get("portefeuille") <> '')
  $portefeuille=$object->get("portefeuille");
elseif($_GET['portefeuille'] <> '')
  $portefeuille=$_GET['portefeuille'] ;
elseif($_POST['portefeuille'] <> '')
  $portefeuille=$_POST['portefeuille'] ;

$q = "SELECT * FROM Rekeningen WHERE  Memoriaal = 0 AND Termijnrekening = 0 AND Inactief = 0 AND Deposito = 0 AND Portefeuille = '".$portefeuille."'";
$DB->SQL($q);
$DB->Query();
$rekRows = array();
$tmpRec = "";
while ($row = $DB->nextRecord())
{
  $rekeningen[$row['Rekening']]=$row;
  $key = ereg_replace("[^0-9]","",$row[Rekening]);
  if ($tmpRec <> $key)
  {
    $rekRows[$key] = $key;
    $tmpRec = $key;
  }
}
$object->setOption("rekeningnr","form_options",$rekRows);

if (($action == "update" || $action = "edit") && $object->get('status') < 1 )
{
    $editObject->formVars["controlle_chk"] .= "
    <table border=1>";
    foreach ($controles as $key=>$value) //maak chk boxes voor deze vermogensbeheerder.
    {
      if($order->checksKort[$key] > 0)
      {
        if($export['controle_regels'][$key]['checked']==1)
          $error='';
        else
          $error='class="input_error"';

        $checkbox="<input type=\"checkbox\" $error value=\"1\" id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".(($export['controle_regels'][$key]['checked']==1)?"checked":"").">";

      }
      else
        $checkbox='&nbsp;';

      $editObject->formVars["controlle_chk"] .= "<tr> <td> $checkbox</td><td width=200> <div>  <label for=\"order_controle_checkbox_".$key."\" title=\"".$value."\">".$value." </label></div></td>\n";
	    $editObject->formVars['controlle_chk'] .= "<td>".$resultaat[$key] ." </td></tr>\n ";
    }
    $editObject->formVars["controlle_chk"] .= "</table>";
}
else
 $editObject->formVars["controlle_chk"]=$object->get('CheckResult');

$data['controle_regels']=$object->get("controle_regels");
$data['controle']=$object->get('controle');


if($tmp["totaal"] <> $orderRec["aantal"] && $object->get('status')==2)
{
    $object->setOption("portefeuille","form_extra",'READONLY');
    $object->setOption("rekeningnr","form_extra",'DISABLED');
    $object->setOption("valuta","form_extra",'DISABLED');
    $knoppenOnderUit=true;
}


// Nota informatie
if($rechten['ordernota'] && $object->get('definitief') > 0 )
{
  $object->setOption("portefeuille","form_extra",'READONLY onchange="lookupPort()"');
  $object->setOption("rekeningnr","form_extra",'DISABLED');
  $object->setOption("valuta","form_extra",'DISABLED');
  $object->setOption("aantal","form_extra",'READONLY');
  $object->setOption("memo","form_extra",'READONLY');
  $object->setOption("handelsDag","form_extra","READONLY");
  $object->setOption("handelsDag","form_type","text");
  $object->setOption("handelsTijd","form_extra",'READONLY');
  $object->setOption("beurs","form_extra",'DISABLED');
  $object->setOption("kosten","form_extra",'READONLY');

  $object->setOption("memoHandel","form_extra",'READONLY');
  $object->setOption("valutakoers","form_extra",'READONLY');
  $object->setOption("fondsKoers","form_extra",'READONLY');
  $object->setOption("brutoBedrag","form_extra",'READONLY');
  $object->setOption("kosten","form_extra",'READONLY');
  $object->setOption("opgelopenRente","form_extra",'READONLY');
  $object->setOption("nettoBedrag","form_extra",'READONLY');
  $object->setOption("definitief","form_extra",'DISABLED');

  $editObject->controller($_GET['action'],$data);
}
elseif($rechten['ordernota'] && $orderRec['laatsteStatus'] > 1)
{

  $query="SELECT SUM(uitvoeringsAantal) as aantal,SUM(uitvoeringsAantal * uitvoeringsPrijs) as waarde, MAX(uitvoeringsDatum) as datum
          FROM OrderUitvoering WHERE orderid = '".$orderRec['orderid'] ."'";
  $db->SQL($query);
  $uitvoering = $db->lookupRecord();

  $totaalWaarde = $uitvoering['waarde'];
  $totaalAantal = $uitvoering['aantal'];
  $uitvoeringsDatum = $orderRec['datum'];
  $gemiddeldePrijs = round($totaalWaarde/$totaalAantal,4);

  $query="SELECT Fonds as fonds, Rentedatum as rentedatum, EersteRentedatum as eersteRentedatum, Renteperiode as renteperiode, Valuta, Fondseenheid FROM Fondsen WHERE fonds='".$orderRec['fonds']."'";
  $db->SQL($query);
  $fonds = $db->lookupRecord();
  $fonds['totaalAantal']=$object->get('aantal');

  $query = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $fondsKoers = $db->lookupRecord();

  $query = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $valutaKoers = $db->lookupRecord();

  $editObject->formVars["fondsEenheid"]='<input class="" type="hidden" value="'.$fonds['Fondseenheid'].'" name="fondsEenheid" id="fondsEenheid" >';
  $editObject->formVars["fonds"]='<input class="" type="hidden" value="'.$fonds['fonds'].'" name="fonds" id="fonds" >';
  $editObject->formVars["gemiddeldePrijs"]='<input class="" type="hidden" value="'.$gemiddeldePrijs.'" name="gemiddeldePrijs" id="gemiddeldePrijs" >';
  $editObject->formVars["transactieSoort"]='<input class="" type="hidden" value="'.$orderRec["transactieSoort"].'" name="transactieSoort" id="transactieSoort" >';

  $object->setOption("aantal","form_extra",'onchange="getKoersen()"');
  $object->setOption("handelsDag","form_extra","onchange=\"getKoersen();\"");
  $object->setOption("kosten","form_extra",'onchange="berekenBedrag()"');
  $object->setOption("brokerkosten","form_extra",'onchange="berekenBedrag()"');
  $object->setOption("fondsKoers","form_extra",'onchange="berekenBedrag()"');
  $object->setOption("brutoBedrag","form_extra",'onchange="berekenBedrag()"');
  $object->setOption("valutakoers","form_extra",'onchange="berekenBedrag()"');
  $object->setOption("opgelopenRente","form_extra",'onchange="berekenBedrag()"');
  
  $query= " SELECT InternDepot FROM Portefeuilles WHERE Portefeuille='$portefeuille'"; 
  $db->SQL($query);
  $intern = $db->lookupRecord();

  if($intern['InternDepot']==1)
  {
    $object->setOption("kosten","form_extra",'READONLY');
    $object->setOption("brokerkosten","form_extra",'READONLY');
  }

$editObject->controller($_GET['action'],$data);
if($object->get('valuta')=='')
  $object->set('valuta','EUR');

if($object->get('handelsDag') == '0000-00-00')
  $object->set('handelsDag',formdate2db($uitvoeringsDatum));

if($object->get('valutakoers') == 0)
  $object->set('valutakoers',$valutaKoers['koers']);

if($object->get('fondsKoers') == 0)
  $object->set('fondsKoers',$gemiddeldePrijs);

if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "VEC")
{
  $rekeningValuta=$rekeningen[$object->get('rekeningnr').$object->get('valuta')]['Valuta'];
  if($fonds['Valuta']<>'USD')
  {
    $object->set('USDsettlement','0');
    $object->setOption('USDsettlement','form_extra','DISABLED');
  }
  elseif($rekeningValuta=='USD')
  {
    $object->set('USDsettlement','1');
    $object->setOption('USDsettlement','form_extra','DISABLED');
  }

  if($orderRec['Depotbank']=='KAS' && $object->get('PSET')=='')
    $object->set('PSET','KASBANK');

  $query="SELECT code,type FROM PSAFperFonds WHERE fonds='".mysql_real_escape_string($fonds['fonds'])."'";
  $DB->SQL($query);
  $DB->Query();
  while($dbData=$DB->nextRecord())
  {
    if($object->get($dbData['type']) == '')
      $object->set($dbData['type'],$dbData['code']);
  }  
}


if($object->get('opgelopenRente') == 0)
{
  $rente=round(renteOverPeriode($fonds,date('Y-m-d')),2);
  $acties = array('A'=>-1,'AO'=>-1,'AS'=>-1,'V'=>1,'VO'=>1,'VS'=>1,'I'=>-1);
  $rente = $acties[$orderRec["transactieSoort"]] * $rente;
  $object->set('opgelopenRente',$rente);
}

if($object->get('brutoBedrag') == 0)
  $object->set('brutoBedrag',$aandeelTotaal*$fonds['Fondseenheid']*$gemiddeldePrijs*$object->get('valutakoers'));
}
else
{
  $editObject->formVars["notaOptiesStyle"] = 'style="visibility:hidden;"';
  $editObject->controller($_GET['action'],$data);
}
if($object->checkAccess('edit'))
{
  if($editObject->object->locked)
    $editObject->formVars["knoppen"] ='locked';
  else
   $editObject->formVars["knoppen"] = '<input type="button" onclick="testAndSubmit();" value="opslaan en nieuw"> <input type="button" onclick="ready();"  value="Klaar met invoeren">';
}

if($knoppenOnderUit==true)
  $editObject->formVars["knoppen"] ='';

if($object->get('valuta')=='')
  $object->set('valuta','EUR');


echo $editObject->getOutput();


if($object->error || $_GET['adding'])
  $adding=true;
if ($result = $editObject->result)
{
  if($object->get('id') > 0)
    updateBrutoWaarde($object->get('id'));
  if ($adding)
  {
    header("Location: ".$__funcvar['location']."?action=new&orderid=".$data["orderid"]);
  }
  else
  {
	  header("Location: ".$returnUrl);
  }
}
else
{
	echo $_error = $editObject->_error;
}
/*
function updateBrutoWaarde($id)
{
  $query="SELECT Orders.fonds,OrderRegels.aantal, OrderRegels.brutoBedrag,Orders.laatsteStatus,OrderRegels.portefeuille FROM OrderRegels Inner Join Orders ON OrderRegels.orderid = Orders.orderid where OrderRegels.id='$id'";
  $db=new DB();
  $db->SQL($query);
  $orderRegel=$db->lookupRecord();

  if($orderRegel['laatsteStatus'] > 0)
    return;

  $query="SELECT Fonds as fonds, Valuta, Fondseenheid FROM Fondsen WHERE fonds='".$orderRegel['fonds']."'";
  $db->SQL($query);
  $fonds = $db->lookupRecord();

  $query = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $fondsKoers = $db->lookupRecord();

  $query = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
  $db->SQL($query);
  $valutaKoers = $db->lookupRecord();

  $data["brutoBedragValuta"]=$orderRegel['aantal']*$fonds['Fondseenheid']*$fondsKoers['koers'];
  $data["brutoBedrag"]=$data["brutoBedragValuta"]*$valutaKoers['koers'];

  $Orderkosten=Orderkosten($orderRegel['portefeuille'],$orderRegel['fonds'],$data["brutoBedrag"]);
  $query="UPDATE OrderRegels SET 
  brutoBedrag='".$data["brutoBedrag"]."', 
  brutoBedragValuta='".$data["brutoBedragValuta"]."',
  kosten='".$Orderkosten['kosten']."',
  brokerkosten='".$Orderkosten['brokerKosten']."' 
  WHERE id='$id'";

  $db->SQL($query);
  $db->Query();
}
*/
?>
