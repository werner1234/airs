<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/20 12:11:48 $
 		File Versie					: $Revision: 1.71 $

 		$Log: fondsEdit.php,v $
 		Revision 1.71  2020/06/20 12:11:48  rvv
 		*** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$ms = new AE_cls_Morningstar();

if($_GET['action']=='kopieerFondsparametes')
{
  include('fondsparameterHistorieEdit.php');
  exit;
}

$AETemplate = new AE_template();
$data = $_GET;
$action = $data['action'];

$__funcvar['listurl']  = "fondsList.php";
$__funcvar['location'] = "fondsEdit.php";

$object = new Fonds();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;



$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['javascript'] .='
var ajax = new Array();

function renteFrame()
{

//orderuitvoeringList.php?orderid={orderid_value}

  if (document.getElementById(\'Fonds\').value != "")
  {
    var url = "rentepercentageList.php?frame=1&Fonds=" + encodeURIComponent($("#Fonds").val());
    //document.getElementById(\'rente\').src = "rentepercentageList.php?frame=1&Fonds="+document.getElementById(\'Fonds\').value;
    loadToDiv("renteList", url, {ajaxRequestType : "list"});
    $("#renteList").show();
  }
  else
  {
    //document.getElementById(\'rente\').src = "blank.html";
    $("#renteList").html();
    $("#renteList").hide();
  }
}

function open(id,categorie)
{
  var script="";
  if (document.getElementById(\'Vermogensbeheerder\').value != "")
  {
    if(categorie == "Beleggingssector" || categorie == "AttributieCategorie" || categorie == "Regio" || categorie == "DuurzaamCategorie")
    {
      script = "beleggingssectorperfonds";
    }
    else if(categorie == "Beleggingscategorie" || categorie == "RisicoPercentageFonds" || categorie == "afmCategorie" || categorie == "duurzaamheid")
    {
      script = "beleggingscategorieperfonds";
    }
    else if(categorie == "Zorgplicht" || categorie == "Zorgplicht omschrijving" || categorie == "Percentage")
    {
      script = "zorgplichtperfonds";
    }


    if(id != "0")
    {
      //document.getElementById(\'koppeling\').src = script + "Edit.php?action=edit&frame=1&id="+id;
      var modalUrl = script + "Edit.php?action=edit&frame=1&id="+id;
        loadToDiv("modelContent", modalUrl);
    }
    else
    {
      //document.getElementById(\'koppeling\').src = script + "Edit.php?action=new&frame=1&Vermogensbeheerder=" + encodeURIComponent(document.getElementById(\'Vermogensbeheerder\').value)+"&Fonds="+encodeURIComponent(document.getElementById(\'Fonds\').value)+"&encoded=1";
      
      var modalUrl = script + "Edit.php?action=new&frame=1&newFonds=1&Vermogensbeheerder=" + encodeURIComponent(document.getElementById(\'Vermogensbeheerder\').value)+"&Fonds="+encodeURIComponent(document.getElementById(\'Fonds\').value)+"&encoded=1";
        loadToDiv("modelContent", modalUrl);

    }
  }
  else
  {
    document.getElementById(\'koppeling\').src = "blank.html";
  }
}



'.
"
function getKoppelingen (sel)
{
	var velden = encodeURIComponent(sel);
	if(velden.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Beleggingscategorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=KoppelingenPerFonds&query='+velden;	// Specifying which file to get
		ajax[index].onCompletion = function(){ toonKoppelingen(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function toonKoppelingen(index)
{
 	var	waarden = ajax[index].response;
  if(waarden.search('mislukt') > 1)
  {
    alert('".vt("Ophalen van records mislukt!")."');
  }
  
  let translations = {
    'BeleggingssectorPerFonds'        : '" . vt('BeleggingssectorPerFonds') . "',
 	  'BeleggingscategoriePerFonds'     : '" . vt('BeleggingscategoriePerFonds') . "',
 	  'ZorgplichtPerFonds'              : '" . vt('ZorgplichtPerFonds') . "',
 	  
 	  'Beleggingssector'                : '" . vt('Beleggingssector') . "',
    'Regio'                           : '" . vt('Regio') . "',
    'AttributieCategorie'             : '" . vt('AttributieCategorie') . "',
    'DuurzaamCategorie'               : '" . vt('DuurzaamCategorie') . "',
    
    'Beleggingscategorie'             : '" . vt('Beleggingscategorie') . "',
    'RisicoPercentageFonds'           : '" . vt('RisicoPercentageFonds') . "',
    'afmCategorie'                    : '" . vt('afmCategorie') . "',
    'duurzaamheid'                    : '" . vt('duurzaamheid') . "',
      
    'Zorgplicht'                      : '" . vt('Zorgplicht') . "',
    'Zorgplicht omschrijving'         : '" . vt('Zorgplicht omschrijving') . "',
    'Percentage'                      : '" . vt('Percentage') . "',
    
  };

 	Gegevens = new Array();

 	VertalingenTabel = new Array();
 	VertalingenTabel[0] = 'BeleggingssectorPerFonds';
 	VertalingenTabel[1] = 'BeleggingscategoriePerFonds';
 	VertalingenTabel[2] = 'ZorgplichtPerFonds';

  VertalingenWaarden = new Array();
  VertalingenWaarden[0] = new Array();
  VertalingenWaarden[0][0] = 'Beleggingssector';
  VertalingenWaarden[0][1] = 'Regio';
  VertalingenWaarden[0][2] = 'AttributieCategorie';
  VertalingenWaarden[0][3] = 'DuurzaamCategorie';

  VertalingenWaarden[1] = new Array();
  VertalingenWaarden[1][0] = 'Beleggingscategorie';
  VertalingenWaarden[1][1] = 'RisicoPercentageFonds';
  VertalingenWaarden[1][2] = 'afmCategorie';
  VertalingenWaarden[1][3] = 'duurzaamheid';

  VertalingenWaarden[2] = new Array();
  VertalingenWaarden[2][0] = 'Zorgplicht';
  VertalingenWaarden[2][1] = 'Zorgplicht omschrijving';
  VertalingenWaarden[2][2] = 'Percentage';

  VertalingenChecks = new Array();
  GegevensCheck = new Array();
 	var tabellen = waarden.split('\\t');
 	for(var i=0;i<tabellen.length;i++)
 	{
 	  Gegevens[i]	= new Array();
 	  GegevensCheck[i]	= new Array();
 	  var elements = tabellen[i].split('|');

    for(var j=0;j<elements.length;j++)
    {
      Gegevens[i][j] = new Array();
      GegevensCheck[i][j]	=0;
      var item = elements[j].split('~');
      if(item.length > 1)
      {
        Gegevens[i][j]['0']=item[0];
        Gegevens[i][j]['1']=item[1];
      }
      else
      {
        Gegevens[i][j]['0']	= elements[j];
      }
    }
 	}

  if(typeof(Gegevens[3]) == 'object'){Checks=Gegevens[3];}
  else{Checks= new Array(0,0,0,0,0,0,0,0);}

  VertalingenChecks[0]='Beleggingscategorie';
  VertalingenChecks[1]='Beleggingssector';
  VertalingenChecks[2]='Zorgplicht';
  VertalingenChecks[3]='Regio';
  VertalingenChecks[4]='AttributieCategorie';
  VertalingenChecks[5]='afmCategorie';
  VertalingenChecks[6]='duurzaamheid';
  VertalingenChecks[6]='DuurzaamCategorie';

 	var html='<table>';
 	var ref='';

for(var tabelId in VertalingenTabel)
{
  for(var recordId in VertalingenWaarden[tabelId])
  {
    for(var checkId in VertalingenChecks)
    {
      if(VertalingenWaarden[tabelId][recordId] == VertalingenChecks[checkId])
      {
        GegevensCheck[tabelId][recordId]=Checks[checkId];
      }
    }
  }
}

GegevensCheck[1][0]=1; //Beleggingscategorie altijd verplicht.

for(var tabelId in VertalingenTabel)
{
  html+='<tr class=\"list_kopregel\"><td colspan=10><b>'+ (VertalingenTabel[tabelId]  in translations ? translations[VertalingenTabel[tabelId]] : VertalingenTabel[tabelId])+' ".vt('koppelingen')."</b></td></tr>';
  html+='<tr class=\"list_kopregel\">';
	for(var recordId in VertalingenWaarden[tabelId])
  {
    if(typeof(Gegevens[tabelId][recordId]) == 'object' && Gegevens[tabelId][recordId][1])
    {
      html+='<td>' + (VertalingenWaarden[tabelId][recordId] in translations ? translations[VertalingenWaarden[tabelId][recordId]] : VertalingenWaarden[tabelId][recordId]) + ' &nbsp;</td>';
    }
    else
    {
      html+='<td><a href=\'javascript:open(0,\"'+VertalingenWaarden[tabelId][recordId]+'\");\'><img align=\"absmiddle\" border=\"0\" src=\"images/16/record_new.gif\">'+ (VertalingenWaarden[tabelId][recordId]  in translations ? translations[VertalingenWaarden[tabelId][recordId]] : VertalingenWaarden[tabelId][recordId]) +'&nbsp;</a></td>';
    }
  }
  html+='</tr><tr>';
  for(var recordId in VertalingenWaarden[tabelId])
  {
    var style='';
    if(GegevensCheck[tabelId][recordId] == 1){style='style=\'background-color: #FF6666;\'';}

    if(typeof(Gegevens[tabelId][recordId]) == 'object')
    {
      if(Gegevens[tabelId][recordId][1])
      {
        html+='<td class=\"listTableData\"><a href=\'javascript:open(\"'+Gegevens[tabelId][recordId][1]+'\",\"'+VertalingenWaarden[tabelId][recordId]+'\");\'><img align=\"absmiddle\" border=\"0\" src=\"images/16/muteer.gif\">&nbsp;'+Gegevens[tabelId][recordId][0]+'</a></td>';
      }
      else
      {
        html+='<td '+style+' class=\"listTableData\" >'+Gegevens[tabelId][recordId][0]+' &nbsp;</td>';
      }
    }
    else
    {
      html+='<td  '+style+' class=\"listTableData\" >&nbsp;</td>';
    }
  }
  html+='</tr><tr><td>&nbsp; </td> </tr>';
}
 	html+='</table>';
 	document.getElementById('huidigeKoppeling').innerHTML=html;
}

function VermogensbeheerderChanged()
{
  getKoppelingen($('#fondsEditForm #Vermogensbeheerder').val() + '|' + $('#fondsEditForm #Fonds').val());
}



function checkPassiefFonds()
{
  if($('#VKM').attr('checked') != 'checked' )
  {

    $('#passiefFonds').prop('checked',false);
    $('#passiefFondsMelding').html('<b>".vt("Indirect instrument dient aan te staan om passief fonds te kunnen aanzetten").".</b>');
    
    
  }
  else
  {
    $('#passiefFondsMelding').html('');
  }


}
";

if($__appvar["bedrijf"] == "HOME")
{
  $editcontent['javascript'] .= "
  function toonKoersVBH()
  {
    if($('#koersmethodiek').val()=='5')
    {
      $('#koersVBHspan').show();
    }
    else
    {
      $('#koersVBH').val('');
      $('#koersVBHspan').hide();
    }
  }
  ";
}
else
{
  $editcontent['javascript'] .= "
  function toonKoersVBH()
  {
    $('#koersVBHspan').hide();
  }
  ";
}

if ($ms->allowed(3,4))
{
  $editcontent['javascript'] .= "
  
  ";
}

$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();


while($vb = $DB->NextRecord())
	$vermogensbeheerders[]=$vb['Vermogensbeheerder'];
$vermogensbeheerders[]="---";
$editcontent['body'] = " onLoad=\"javascript:tabOpen('1');VermogensbeheerderChanged();toonKoersVBH();\" ";

$editObject->template = $editcontent;

$editObject->formTemplate = "fondsEditTemplate.html";
$editObject->usetemplate = true;

$data = $_GET;
$trimItems=array('Fonds','ISINCode','FondsImportCode');
foreach($trimItems as $item)
  $data[$item]=trim($data[$item]);

$action = $data['action'];

if ($action == 'update')
{
  $data['OptieExpDatum']= $data['expiratieJaar'].$data['expiratieMaand'];// $expDatum;
}

$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $gb['Valuta'];
}

if($data['optieCode']=='' && $data['fondsOptieSymbolen']<>'')
  $data['optieCode']=$data['fondsOptieSymbolen'];

$editObject->controller($action,$data);

$OptieExpDatum = $object->data['fields']['OptieExpDatum']['value'] ;
$expJaarDb=substr($OptieExpDatum,0,4);
$expMaandDb=substr($OptieExpDatum,4,2);

$optieJaar=substr($object->get('OptieExpDatum'),0,4);
$OptieExpJaar='';
$huidigeJaar = date('Y') - 1; //get current year minus one for history
if($optieJaar<>'' && $optieJaar<$huidigeJaar)
  $OptieExpJaar .= "<option value=\"".$optieJaar."\" SELECTED>".$optieJaar."</option>";

for ($i=0;$i<10;$i++)
{
  $expJaar = $huidigeJaar + $i;
  if ($expJaar == $expJaarDb)
    $OptieExpJaar .= "<option value=\"".$expJaar."\" SELECTED>".$expJaar."</option>";
  else
    $OptieExpJaar .= "<option value=\"".$expJaar."\" >".$expJaar."</option>";
}
$editObject->formVars["OptieExpJaar"]=$OptieExpJaar;


$huidigeMaand= date('n');
for($i=1; $i<13; $i++)
{
  if ($i<10)
    $maandString='0'.$i;
  else
    $maandString=$i;

  if ($i == $expMaandDb)
    $OptieExpMaand .= "<option value=\"$maandString\" SELECTED>".$__appvar["Maanden"][$i]." </option>";
  else
    $OptieExpMaand .= "<option value=\"$maandString\" >".$__appvar["Maanden"][$i]." </option>";
}
$editObject->formVars["OptieExpMaand"]=$OptieExpMaand;
$editObject->formVars["selectVermogensbeheerder"]	= SelectArray('',$vermogensbeheerders);

//debug($editObject->object->data["fields"]["KIDformulier"]);

if ($ms->allowed(3,4))  // call 7630
{
  $editObject->object->data["fields"]["KIDformulier"]["form_size"] = "100";
  $kidUrl =  str_replace("&amp;", "&", $object->get("KIDformulier"));
  $editObject->formVars["KIDformulier_testKnop"]	= "<a class='btn btn-gray' href='".$kidUrl."' id='btnKidUrl' target='_blank'>" . vt('Test link') . "</a> ";
}
else
{
  unset($editObject->object->data["fields"]["KIDformulier"]);
  $editObject->formVars["KIDformulier_testKnop"] = "";
}



$editObject->formVars['fondsparameterHistorie'] =
'<div class="btn btn-gray" onclick="document.editForm.action.value=\'kopieerFondsparametes\';submitForm();" >'.vt("Kopieer naar fondsparameter historie").'.</div>';
  $query="SELECT max(GebruikTot) as laatsteDatum, count(id) as aantal FROM FondsParameterHistorie WHERE Fonds='".mysql_real_escape_string($object->get('Fonds'))."'";
  $DB->SQL($query);
  $stats=$DB->lookupRecord();
  $editObject->formVars['fondsparameterHistorie'] .= " ".vt("Laatste record").": ".$stats['laatsteDatum'].", ".vt("Aantal records").":(".$stats['aantal'].")";


$query="SELECT count(id) as aantal FROM FondsOmschrijvingVanaf WHERE Fonds='".mysql_real_escape_string($object->get('Fonds'))."'";
$DB->SQL($query);
$stats=$DB->lookupRecord();
if($stats['aantal']>0)
$editObject->formVars['FondsOmschrijvingVanafTxt'] .= "<br>Er zitten (".$stats['aantal'].") omschrijvingen in de fondsomschrijving vanaf tabel.";


if(1)//$__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "ANO")
{
  //$query = "SELECT id FROM FondsExtraVelden limit 1";
  //if ($DB->QRecords($query))
  //{
    $editObject->formVars['tab7_extraVelden'] = '<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'7\');" id="tabbutton7" value="Extra info.">';
    $query = "SELECT id FROM FondsExtraInformatie WHERE fonds='" . mysql_real_escape_string($object->get('Fonds')) . "'";
    $DB->SQL($query);
    $FondsExtraInformatie = $DB->lookupRecord();
    $editObject->formVars['tab7_recordId'] = $FondsExtraInformatie['id'];
  //}

  $editObject->formVars['tab8_extraVelden'] = '<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'8\');" id="tabbutton8" value="'.vt("Overige").'">';

  $query = "SELECT Aantal,portefeuilleAantal as `Aantal portefeuilles` FROM ActieveFondsen WHERE ActieveFondsen.Fonds='" . mysql_real_escape_string($object->get('Fonds')) . "'";
  $DB->SQL($query);
  $activeFondsen = $DB->lookupRecord();
  $editObject->formVars['positieDetails'] = '<div class="form" ><b>'.vt("Positie").'</b>';
  foreach ($activeFondsen as $key => $value)
  {
    $editObject->formVars['positieDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\">".vt($key)." </div> <div class=\"formrechts\"><span style='text-align: right;display: inline-block;width:100px'> $value </span> </div></div>\n";
  }
  $editObject->formVars['positieDetails'] .= '</div>';
  

  $query = "SELECT percentage as `Kostenpercentage` , date(datum) as kostenDatum,transCostFund,perfFeeFund FROM fondskosten WHERE fondskosten.fonds='" . mysql_real_escape_string($object->get('Fonds')) . "' ORDER BY datum desc limit 1";
  $DB->SQL($query);
  $fondskosten = $DB->lookupRecord();
  if($fondskosten['kostenDatum'] <> '0000-00-00')
    $kostenDatum='op '.date('d-m-Y',db2jul($fondskosten['kostenDatum'])) ;
  $editObject->formVars['positieDetails'] .= "
  <div class='form' >
    <b>".vt("Kosten")."</b>
    <div class='formblock'>
      <div class='formlinks'>".vt("Kostenpercentage")." </div> 
      <div class='formrechts'> <span style='text-align: right;display: inline-block;width:100px'>".$fondskosten['Kostenpercentage']."%</span> ".$kostenDatum."</div>
    </div>
    <div class='formblock'>
      <div class='formlinks'>".vt("Transactiekostencentage")." </div>
      <div class='formrechts'> <span style='text-align: right;display: inline-block;width:100px'>".$fondskosten['transCostFund']."%</span> ".$kostenDatum." </div>
    </div>
    <div class='formblock'>
      <div class='formlinks'>".vt("Performancefeepercentage")." </div> 
      <div class='formrechts'> <span style='text-align: right;display: inline-block;width:100px'>".$fondskosten['perfFeeFund']."%</span> ".$kostenDatum." </div>
    </div>
  </div>";


  if ($ms->allowed(2,4))  // call 7630
  {
    $query="SELECT id FROM FondsenFundInformatie WHERE fonds = '".mysql_real_escape_string($object->get("Fonds"))."' ORDER BY datumVanaf desc limit 1";
    $DB->SQL($query);
    $laatsteFundInfo = $DB->lookupRecord();
  $fondsenInformatie=new FondsenFundInformatie();
  $fondsenInformatie->getById($laatsteFundInfo['id']);
  $velden=array();
  foreach($fondsenInformatie->data['fields'] as $veld=>$veldDetails)
    if($veldDetails['form_visible']==true && $veld<>'fonds')
      $velden[]=$veld;
  
    $table='';
    $n=0;
    $aantal=count($velden);
    $cols=array();
    $editObject->formVars['positieDetails'] .= "<div class=\"form\" ><b>".vt("Overige")."</b>";
    foreach($velden as $veld)
    {
      if ($n < $aantal / 2)
      {
        $col = 1;
      }
      else
      {
        $col = 2;
      }
      $cols[$col] .= "<div class=\"formblock\"><div class=\"formlinks\" style='width:200px'>".$fondsenInformatie->data['fields'][$veld]['description']."</div> <div class=\"formrechts\"><span style='text-align: right;display: inline-block;width:150px'> ".$fondsenInformatie->get($veld)." </span> </div></div>\n";
      $n++;
    }
    $editObject->formVars['positieDetails'] .= "<div class=\"form\"><table border='0'><tr><td width='500'>" . $cols[1] . "</td><td width='500'>" . $cols[2] . "</td></tr> </table></div>";


  $fondsenInformatie=new FondsenEMTdata();
  $fondsenInformatie->getByField('Fonds', $object->get('Fonds'));
  if($fondsenInformatie->data['fields']['id']['value']>0)
  {
    $velden=array('Clientclassificatie'=>array('01010 - Retail cliënt'=>'ClientTypeRetail',
                                               '01020 - Professionele cliënt'=>'ClientTypeProfessional',
			                                         '01030 - In aanmerking komende tegenpartij'=>'ClientTypeEligibleCounterparty'),
      'Kennisniveau'=>array('02010 - Cliënt met basiskennis'=>'ExpertiseBasic',
                            '02020 - Goed geïnformeerde cliënt'=>'ExpertiseInformed',
			                      '02030 - Cliënt met uitgebreide kennis'=>'ExpertiseAdvanced'),
	  'Risicohouding'=>array('03010 - Geen kapitaal verlies'=>'CapitalLossNone',
                           '03020 - Beperkt kapitaal verlies'=>'CapitalLossLimited',
                           '03040 - Volledig kapitaal verlies'=>'CapitalLossTotal',
			                     '03050 - Kapitaal verlies groter dan investering'=>'CapitalLossBeyondInvestment'),
	  'Risicotolerantie'=>array('04010 - PRIIPS SRI cijfer'=>'RiskPRIIPSRI',
                              '04020 - SRRI cijfer'=>'RiskSRRI',
                              '04030 - Risicotolerantie cijfer'=>'ClientRiskTolerance'),
	  'Clientprofiel'=>array('05010 - Portefeuille profiel Behoud'=>'ProfilePreservation',
                           '05020 - Portefeuille profiel Groei'=>'ProfileGrowth',
                           '05030 - Portefeuille profiel Inkomen'=>'ProfileIncome',
                           '05040 - Portefeuille profiel Hedging'=>'ProfileHedging',
                           '05050 - Portefeuille profiel Leverage'=>'ProfileOptionsLeverage',
                           '05060 - Portefeuille profiel Overige'=>'ProfileOther'),
    'Horizon'=>array('05080 - Cliënt horizon'=>'ClientHorizon'),
	  'Soort dienstverlening'=>array('06010 - Execution only'=>'ServiceExecOnly',
                                   '06020 - Execution only met geschiktheidstoets'=>'ServiceExecOnlyAppTest',
                                   '06030 - Vermogensadvies'=>'ServiceAdvice',
                                   '06040 - Vermogensbeheer'=>'ServiceManagement'));


      $editObject->formVars['fondsInfo'] ='<fieldset><legend>' . vt('Target Markt informatie') . '</legend>';
      foreach($velden as $categorie=>$categorieData)
      {
        $editObject->formVars['fondsInfo'] .='<div class="formblock"><div class="formlinks" style=\'width:300px\'><strong>'.$categorie.'</strong></div></div>';
        $table='';
        $n=0;
        $aantal=count($categorieData);
        $cols=array();
        foreach($categorieData as $omschrijving=>$veld)
        {
          if ($n < $aantal / 2)
          {
            $col = 1;
          }
          else
          {
            $col = 2;
          }
          $cols[$col] .= "<div class=\"formblock\"><div class=\"formlinks\" style='width:300px'>$omschrijving </div> <div class=\"formrechts\"><span style='text-align: right;display: inline-block;width:50px'> ".$fondsenInformatie->get($veld)." </span> </div></div>\n";
          $n++;
        }
        $editObject->formVars['fondsInfo'] .= "<div class=\"form\"><table border='0'><tr><td width='500'>" . $cols[1] . "</td><td width='500'>" . $cols[2] . "</td></tr> </table></div>";
      }
      /*
      $overslaan = array('id', 'Fonds', 'change_user', 'change_date', 'add_user', 'add_date');
      foreach ($fondsenInformatie->data['fields'] as $field => $fieldData)
      {
        if (!in_array($field, $overslaan))
        {
          $fondsInfoData[$fieldData['description']] = $fieldData['value'];
        }
      }
      $aantal = count($fondsInfoData);
      $cols = array();
      if ($aantal > 0)
      {
        $n = 0;
        foreach ($fondsInfoData as $veld => $waarde)
        {
          if ($n < $aantal / 2)
          {
            $col = 1;
          }
          else
          {
            $col = 2;
          }
          $cols[$col] .= "<div class=\"formblock\"><div class=\"formlinks\" style='width:300px'>$veld </div> <div class=\"formrechts\"><span style='text-align: right;display: inline-block;width:50px'> $waarde </span> </div></div>\n";
          $n++;
        }
        $editObject->formVars['fondsInfo'] = "<fieldset id=\"fondsInfo\"><div class=\"form\" ><b>fondsInfo</b><table border='0'><tr><td width='500'>" . $cols[1] . "</td><td width='500'>" . $cols[2] . "</td></tr> </table></div></fieldset>";
      }
      */
      $editObject->formVars['fondsInfo'] .='</fieldset>';
    }


  }


  $msFilter = $ms->doorkijkFilter(2,4);  // call 7630

  $query = "
  SELECT 
    msCategoriesoort, 
    date(max(datumVanaf)) as vanaf 
  FROM 
    doorkijk_categorieWegingenPerFonds 
  WHERE 
    fonds='" . mysql_real_escape_string($object->get('Fonds')) . "' 
    $msFilter
  GROUP BY 
    msCategoriesoort";
  $DB->SQL($query);
//debug($query);
  $DB->Query();
  $categorieSoortVanaf = array();
  while ($doorkijk = $DB->nextRecord())
  {
    $categorieSoortVanaf[$doorkijk['vanaf']][] = $doorkijk['msCategoriesoort'];
  }
  //echo $query;
  //listarray($categorieSoortVanaf);
  foreach ($categorieSoortVanaf as $vanaf => $categorieSoorten)
  {
    $query = "
    SELECT 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort,
      doorkijk_categorieWegingenPerFonds.msCategorie,
      doorkijk_categorieWegingenPerFonds.weging,
      doorkijk_categorieWegingenPerFonds.datumVanaf,
      doorkijk_categorieWegingenPerFonds.datumProvider,
      doorkijk_msCategoriesoort.omschrijving 
    FROM 
      doorkijk_categorieWegingenPerFonds 
    LEFT JOIN doorkijk_msCategoriesoort ON 
      doorkijk_categorieWegingenPerFonds.msCategorie=doorkijk_msCategoriesoort.msCategorie AND 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort=doorkijk_msCategoriesoort.msCategoriesoort
    WHERE 
      doorkijk_categorieWegingenPerFonds.fonds='" . mysql_real_escape_string($object->get('Fonds')) . "' AND 
      doorkijk_categorieWegingenPerFonds.datumVanaf='$vanaf' AND 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort IN('" . implode("','", $categorieSoorten) . "')
    ORDER BY 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort,
      doorkijk_categorieWegingenPerFonds.msCategorie,
      doorkijk_categorieWegingenPerFonds.datumVanaf";
//listarray($query);

    $DB->SQL($query);
    $DB->Query();
    $wegingTotaal = 0;

    while ($doorkijk = $DB->nextRecord())
    {
      if ($doorkijk['omschrijving'] <> '')
      {
        $msOmschrijving = $doorkijk['omschrijving'];
      }
      else
      {
        $msOmschrijving = $doorkijk['msCategorie'];
      }

      if ($doorkijk['msCategoriesoort'] <> $lastMsCategoriesoort)
      {
        if ($wegingTotaal <> 0)
        {
          $editObject->formVars['doorkijkDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\"> ".vt("Totaal")." </div> <div class=\"formrechts\"> <span style='text-align: right;display: inline-block;width:100px'>" . formatGetalGlobal($wegingTotaal, 2) . "%</span></div></div>\n";
        }
        $editObject->formVars['doorkijkDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\"><br>  <b>" .vt( $doorkijk['msCategoriesoort']) . "</b> </div> <div class=\"formrechts\"><br>".vt("Geldig vanaf")." " . date('d-m-Y', db2jul($vanaf)) . "  (".vt("provider datum")." " . date('d-m-Y', db2jul($doorkijk['datumProvider'])) . " ) </div></div>\n";
        $wegingTotaal = 0;
      }
      $editObject->formVars['doorkijkDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\">" . vt($msOmschrijving) . " </div> <div class=\"formrechts\"> <span style='text-align: right;display: inline-block;width:100px'>" . formatGetalGlobal($doorkijk['weging'], 2) . "%</span> </div></div>\n";
      $wegingTotaal += $doorkijk['weging'];
      $lastMsCategoriesoort = $doorkijk['msCategoriesoort'];
    }
    if ($wegingTotaal <> 0)
    {
      $editObject->formVars['doorkijkDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\"> ".vt("Totaal")." </div> <div class=\"formrechts\"> <span style='text-align: right;display: inline-block;width:100px'>" . formatGetalGlobal($wegingTotaal, 2) . "%</span></div></div>\n";
    }

  }
}
/** Bij toevoegen nieuwe toon hier de turbo en optie symbolen **/
$jsData = array();
if ( empty ($data['id']) && in_array($__appvar['bedrijf'],array('TEST','HOME', 'TRA')))
{

  include_once('fondsEditOptie.php');
  include_once('fondsEditTurbo.php');
  $editObject->formVars['createOption'] = $AETemplate->parseFile('fondsEdit/typeSelect.html'); //fonds type selectie

  $editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
  $editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');
}
$editObject->template['javascript'] .= $AETemplate->parseFile('fondsEdit/js/fondsEdit.js', $jsData);
$editObject->formVars['fondsEditStyle'] = $AETemplate->parseFile('fondsEdit/css/fondsEdit.css');


$codeError=false;
foreach($editObject->object->codesEnkel as $code)
  if(isset($editObject->object->data['fields'][$code]['error']))
    $codeError=true;
foreach($editObject->object->codesDubbel as $codeNaam=>$codes)
  foreach($codes as $code)
    if(isset($editObject->object->data['fields'][$code]['error']))
      $codeError=true;

/*
$names=array();
foreach($editObject->object->codesDubbel as $codeNaam=>$codes)
  foreach($codes as $code)
    $names[$editObject->object->data['fields'][$code]['description']]=$code;
foreach($editObject->object->codesEnkel as $code)
  $names[$editObject->object->data['fields'][$code]['description']]=$code;
ksort($names);
$html="<table width='1000'><tr>\n";
$n=0;
$kols=array();
$codes=array_values($names);
$aantal=count($codes);
foreach($names as $name=>$code)
{
  if($n<ceil($aantal/2))
    $kol=0;
  else
    $kol=1;
  $kols[$kol].='<div class="formblock"> <div class="formlinks">{'.$code.'_description}</div> <div class="formrechts">{'.$code.'_inputfield}{'.$code.'_error}</div></div>'."\n";

  $n++;
}
foreach($kols as $kol)
  $html.="<td valign='top'>$kol</td>";
$html.="</tr></table>\n";
echo $html;
*/

if($codeError)
{
  $editObject->formVars['codeErrorStyle'] = 'style="background:#FF8888"';
  $editcontent['body'] = " onLoad=\"javascript:tabOpen('4');VermogensbeheerderChanged();toonKoersVBH();\" ";
  $editObject->template = $editcontent;
}

if($action=='edit' && $data['id']==0)
{
  $velden = array('ISINCode','Valuta','Omschrijving','FondsImportCode');//,'Fonds','OptieSymbool','OptieType','OptieExpDatum','OptieUitoefenPrijs','Fondseenheid','Beurs','standaardSector','OptieBovenliggendFonds','identifierVWD');
  foreach($velden as $veld)
  {
    if(isset($data[$veld]) && $data[$veld] <> '')
    {
      $object->set($veld,$data[$veld]);
    }
  }
}
//echo $action;exit;
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($data['id']=="" && $action == 'update')
  {
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $fondsEmail=$cfg->getData('fondsEmail');
    //$fondsEmail='rvv@aeict.nl';
    if($fondsEmail !="" && $mailserver !='')
    {
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = $fondsEmail;
      $mail->FromName = "Airs";

      $body="<table style='font-family: sans-serif; font-size: 8px;'>\n<tr><td><b>Veld</b></td><td><b>" . vt('Waarde') . "</b></td></tr>\n";
      $mailWaarden=$data;
      unset($mailWaarden['action']);
      unset($mailWaarden['updateScript']);
      unset($mailWaarden['returnUrl']);
      unset($mailWaarden['Vermogensbeheerder']);
      
      foreach ($mailWaarden as $key=>$value)
        $body.="<tr><td>$key </td><td>$value</td></tr>\n";

      $db=new DB();
      $query="SELECT Fonds,Datum,Rentepercentage FROM Rentepercentages WHERE Fonds = '".$data['Fonds']."'";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>" . vt('Rentepercentages') . "</b></td><td>".$data['Fonds']."</td></tr>\n";
      $body.="<tr><td>" . vt('Datum') . "</td><td>" . vt('Rentepercentage') . "</td></tr>\n";
      while($dbData=$db->nextRecord())
      {
        $body.="<tr><td>".$dbData['Datum']."</td><td>".$dbData['Rentepercentage']."</td></tr>\n";
      }
      
      $query="SELECT Vermogensbeheerder,Beleggingscategorie,afmCategorie FROM BeleggingscategoriePerFonds WHERE Fonds = '".$data['Fonds']."' ORDER BY Vermogensbeheerder";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>" . vt('BeleggingscategoriePerFonds') . "</b></td><td>".$data['Fonds']."</td></tr>\n";
      while($dbData=$db->nextRecord())
      {
        $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>" . vt('Beleggingscategorie') . ":".$dbData['Beleggingscategorie']."</td></tr>\n";
        if($dbData['afmCategorie'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>" . vt('afmCategorie') . ":".$dbData['afmCategorie']."</td></tr>\n";
      }
      
      $query="SELECT Vermogensbeheerder,Beleggingssector,Regio,AttributieCategorie FROM BeleggingssectorPerFonds WHERE Fonds = '".$data['Fonds']."' ORDER BY Vermogensbeheerder";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>BeleggingssectorPerFonds</b></td><td>".$data['Fonds']."</td></tr>\n";
      while($dbData=$db->nextRecord())
      {
        $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>" . vt('Beleggingssector') . ":".$dbData['Beleggingssector']."</td></tr>\n";
        if($dbData['Regio'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>" . vt('Regio') . ":".$dbData['Regio']."</td></tr>\n";
        if($dbData['AttributieCategorie'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>" . vt('AttributieCategorie') . ":".$dbData['AttributieCategorie']."</td></tr>\n";
      }
      
      $body.="<tr><td colspan=2>" . vt('Verzonden om') . ": ".date("d-m-Y H:i")." (".$USR.")</td></tr>\n</table>";
      storeControleMail('nieuwFonds',"Nieuw Fonds aangemaakt: ".$data['Fonds'],$body);
      $mail->Body    = $body;
      $mail->AltBody = html_entity_decode(strip_tags($body));
      $mail->AddAddress($fondsEmail,$fondsEmail);
      $mail->Subject = "Nieuw Fonds aangemaakt: ".$data['Fonds'];
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo vt("Verzenden van e-mail mislukt.");
      }
    }
  }
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>