<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 12 februari 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/12/01 19:48:44 $
    File Versie         : $Revision: 1.17 $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "autorunList.php";
$__funcvar['location'] = "autorunEdit.php";

$object = new AutoRun();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];


$editObject->formTemplate = "autorunEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;


$instellingsvelden=array(
'Zorgplichtcontrole'=>array('ZpMethodeKeuze','tijdelijkUitsluiten','ZorgMethodeFilter','zorgplichtVoldoetNiet','zorgplichtVoldoetNietCategorie','zorgDoorkijk'),
'Modelcontrole'=>array('modelcontrole_portefeuille','modelcontrole_percentage','modelcontrole_rapport','modelcontrole_vastbedrag',
	                     'modelcontrole_uitvoer','modelcontrole_filter','modelcontrole_level'),
'ouderdomsAnalyse'=>array('ouderdomDagen','ouderdomPercentage'),
'Mandaatcontrole'=>array('restrictie_alleenConsolidaties','mandaat_zorgplichtCategorie'),
'Mandaatcontrole_L79'=>array('restrictie_alleenConsolidaties','mandaat_zorgplichtCategorie'));


foreach($instellingsvelden[$data['Rapportage']] as $index=>$key)
	$instellingen[$key]=$data[$key];
$instellingen['filetype']=$data['filetype'];
$instellingen['geconsolideerd']=$data['geconsolideerd'];
$instellingen['portefeuilleIntern']=$data['portefeuilleIntern'];
$instellingen['metConsolidatie']=$data['metConsolidatie'];


$data['instellingen']=serialize($instellingen);

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

$instellingen=unserialize($object->get('instellingen'));

if($instellingen['portefeuilleIntern']=='')
	$instellingen['portefeuilleIntern']='10';
$htmlPortefeuilleIntern ='
<input type="radio" name="portefeuilleIntern" id="portefeuilleIntern" value="0"  '.(($instellingen['portefeuilleIntern']=='0')?'checked':'').' ">
<label for="actief" title="actief"> ' . vt('Externe portefeuilles') . '  </label><br/>
<input type="radio" name="portefeuilleIntern" id="portefeuilleIntern" value="1"  '.(($instellingen['portefeuilleIntern']=='1')?'checked':'').' ">
<label for="actief" title="actief"> ' . vt('Interne portefeuilles') . '  </label><br/>
<input type="radio" name="portefeuilleIntern" id="alles" value="10"  '.(($instellingen['portefeuilleIntern']=='10')?'checked':'').' ">
<label for="inactief" title="actief"> ' . vt('Alle portefeuilles') . ' </label>';

$htmlMetConsolidatie ='
<input type="radio" name="metConsolidatie" value="0" '.(($instellingen['metConsolidatie']=='0')?'checked':'').' ">
<label for="actief" title="actief"> ' . vt('Zonder consolidatie') . '  </label><br/>
<input type="radio" name="metConsolidatie" value="1" '.(($instellingen['metConsolidatie']=='1')?'checked':'').' ">
<label for="actief" title="actief"> ' . vt('Alleen consolidatie') . '  </label><br/>
<input type="radio" name="metConsolidatie" value="10" '.(($instellingen['metConsolidatie']=='10')?'checked':'').' ">
<label for="inactief" title="actief"> ' . vt('Met consolidatie') . ' </label>';

$DB = new DB();
$query = "SELECT ModelPortefeuilles.Portefeuille, ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille WHERE Portefeuilles.Einddatum>now() ORDER BY ModelPortefeuilles.Omschrijving";
$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
while($gb = $DB->NextRecord())
{
	if($instellingen['modelcontrole_portefeuille']==$gb['Portefeuille'])
		$Modelportefeuilles .= "<option selected value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
	else
  	$Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
}

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

if($object->get('Vermogensbeheerder')<>'')
  $vermogensbeheerders=array($object->get('Vermogensbeheerder'));

$query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder IN('".implode("','",$vermogensbeheerders)."')";
$DB->SQL($query);
$DB->Query();

if($instellingen['mandaat_zorgplichtCategorie']=='')
  $zorgplichtSelect = "<option selected value=\"\" >" . vt('Allemaal') . "</option>\n";
else
  $zorgplichtSelect = "<option value=\"\" >" . vt('Allemaal') . "</option>\n";
while($cat = $DB->NextRecord())
{
  if($instellingen['mandaat_zorgplichtCategorie']==$cat['Zorgplicht'])
    $zorgplichtSelect .= "<option selected value=\"".$cat['Zorgplicht']."\" >".$cat['Omschrijving']."</option>\n";
  else
    $zorgplichtSelect .= "<option value=\"".$cat['Zorgplicht']."\" >".$cat['Omschrijving']."</option>\n";
}



$editObject->formVars['instellingen']='
<div id="zorgplichtSelectie" >
<fieldset >
<legend accesskey="A">' . vt('zorgplicht opties') . '</legend>
<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Alleen portefeuilles die niet voldoen') . ' </div>
<div class="formrechts"> <input type="checkbox" '.(($instellingen['zorgplichtVoldoetNiet']=='1')?'checked':'').'  name="zorgplichtVoldoetNiet" value="1" > </div>
</div>
<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Alleen categorieën per portefeuille die niet voldoen') . '</div>
<div class="formrechts"> <input type="checkbox" '.(($instellingen['zorgplichtVoldoetNietCategorie']=='1')?'checked':'').' name="zorgplichtVoldoetNietCategorie" value="1" > </div>
</div>
<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Zorgplichtmethode') . '</div>
<div class="formrechts"> 
<select name="ZpMethodeKeuze">
	<option  '.(($instellingen['ZpMethodeKeuze']=='aandelen')?'selected':'').' value="aandelen">' . vt('Volgens categorien') . ' </option>
  <option  '.(($instellingen['ZpMethodeKeuze']=='afm')?'selected':'').' value="afm">' . vt('AFM standaarddeviatie') . '</option>
	<option  '.(($instellingen['ZpMethodeKeuze']=='stdev')?'selected':'').' value="stdev">' . vt('Werkelijke standaarddeviatie') . '</option>
  <option  '.(($instellingen['ZpMethodeKeuze']=='contractueel')?'selected':'').' value="contractueel">' . vt('Contractuele methode') . '</option>
</select>
</div>
</div>
<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Tijdelijk uitgesloten portefeuilles uitsluiten') . '</div>
<div class="formrechts"> <input type="checkbox" '.(($instellingen['tijdelijkUitsluiten']=='1')?'checked':'').'  name="tijdelijkUitsluiten" value="1" > </div>
</div>
<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Filter op methode') . '</div>
<div class="formrechts">
<select name="ZorgMethodeFilter">
	<option  '.(($instellingen['ZorgMethodeFilter']=='alles')?'selected':'').' value="alles">' . vt('Alle portefeuilles') . '</option>
  <option  '.(($instellingen['ZorgMethodeFilter']=='contractueel')?'selected':'').' value="contractueel">' . vt('Contractuele portefeuilles') . '</option>
	<option  '.(($instellingen['ZorgMethodeFilter']=='aandelen')?'selected':'').' value="aandelen">' . vt('Portefeuilles met categorie methode') . '</option>
  <option  '.(($instellingen['ZorgMethodeFilter']=='afm')?'selected':'').' value="afm">' . vt('Portefeuilles met AFM standaarddeviatie methode') . '</option>
</select>
</div>
</div>
<div class="formblock">
		<div class="formlinks" style="width:300px"> ' . vt('Doorkijk (huisfonds) gebruiken') . '</div>
		<div class="formrechts"> <input type="checkbox" '.(($instellingen['zorgDoorkijk']=='1')?'checked':'').' name="zorgDoorkijk" value="1"> </div>
</div>
</fieldset>
</div>

<div id="Modelcontrole" >
<fieldset id="Modelportefeuille" >
<legend accesskey="m">' . vt('Modelcontrole') . '</legend>
<div class="formblock">
	<div class="formlinks" style="width:300px"> ' . vt('Modelportefeuille') . '
	<select name="modelcontrole_portefeuille">
	<option '.(($instellingen['modelcontrole_portefeuille']=='')?'selected':'').' value="">-</option>
  <option '.(($instellingen['modelcontrole_portefeuille']=='Allemaal')?'selected':'').' value="Allemaal">' . vt('Allemaal') . '</option>
  '.$Modelportefeuilles.'
 	</select>
</div>

<div class="formblock">
	<u>' . vt('Rapportsoort') . '</u><br>
	<input type="radio" name="modelcontrole_rapport" '.(($instellingen['modelcontrole_rapport']=='gecomprimeerd')?'checked':'').' value="gecomprimeerd"> ' . vt('Gecomprimeerd op totaal') . '<br>
	<input type="radio" name="modelcontrole_rapport" '.(($instellingen['modelcontrole_rapport']=='percentage')?'checked':'').' value="percentage" '.(($instellingen['modelcontrole_rapport']=='')?'checked':'').' > ' . vt('Modelcontrole in percentage') . '<br>
	<input type="radio" name="modelcontrole_rapport" '.(($instellingen['modelcontrole_rapport']=='vastbedrag')?'checked':'').' value="vastbedrag"> ' . vt('Mutatievoorstel Portefeuille') . '<br>
	' . vt('Vast bedrag') . ': <input type="text" name="modelcontrole_vastbedrag" value="'.$instellingen['modelcontrole_vastbedrag'].'" size="4">
</div>

<div class="formblock">
	<u>Uitvoer soort</u><br>
	<input type="radio" name="modelcontrole_uitvoer"  '.(($instellingen['modelcontrole_uitvoer']=='alles')?'checked':'').' value="alles"  '.(($instellingen['modelcontrole_uitvoer']=='')?'checked':'').' > ' . vt('Alles') . '<br>
	<input type="radio" name="modelcontrole_uitvoer"  '.(($instellingen['modelcontrole_uitvoer']=='afwijkingen')?'checked':'').' value="afwijkingen"> vt(Alleen afwijkingen) &nbsp;&nbsp; <input type="text" name="modelcontrole_percentage" onChange="javascript:checkAndFixNumber(this);" value="'.$instellingen['modelcontrole_percentage'].'" size="4"> ' . vt('Afwijkingspercentage') . '<br>
</div>

<div class="formblock">
	<u>Filter</u><br>
	<input type="radio" name="modelcontrole_filter" '.(($instellingen['modelcontrole_filter']=='alles')?'checked':'').' value="alles"> ' . vt('Alles') . '<br>
	<input type="radio" name="modelcontrole_filter" '.(($instellingen['modelcontrole_filter']=='gekoppeld')?'checked':'').' value="gekoppeld"  '.(($instellingen['modelcontrole_filter']=='')?'checked':'').' > ' . vt('Alleen gekoppelde depots') . '<br>
</div>

<div class="formblock">
	<u>Niveau</u><br>
	<input type="radio" name="modelcontrole_level" '.(($instellingen['modelcontrole_level']=='fonds')?'checked':'').' value="fonds" '.(($instellingen['modelcontrole_level']=='')?'checked':'').'> ' . vt('Fonds') . '<br>
	<input type="radio" name="modelcontrole_level" '.(($instellingen['modelcontrole_level']=='beleggingscategorie')?'checked':'').' value="beleggingscategorie" >' . vt('Categorie') . '<br>
	<input type="radio" name="modelcontrole_level" '.(($instellingen['modelcontrole_level']=='hoofdcategorie')?'checked':'').' value="hoofdcategorie" >' . vt('Hoofdcategorie') . '<br>
	<input type="radio" name="modelcontrole_level" '.(($instellingen['modelcontrole_level']=='beleggingssector')?'checked':'').' value="beleggingssector" >' . vt('Sector') . '<br>
	<input type="radio" name="modelcontrole_level" '.(($instellingen['modelcontrole_level']=='Regio')?'checked':'').' value="Regio" >' . vt('Regio') . '<br>
</div>
</fieldset>
</div>

	<div id="ouderdomsAnalyseDiv" style="display:none">
		<fieldset id="Selectie1">
			<legend accesskey="e">' . vt('Ouderdomsanalye') . '</legend>
			<div>
				<div class="formblock">
					<div class="formlinks"> ' . vt('Datum ingevoerd max X dagen terug') . ' </div>
					<div class="formrechts" >	<input type="text" name="ouderdomDagen" value="'.$instellingen['ouderdomDagen'].'" size="2"> </div>
				</div>
				<div class="formblock">
					<div class="formlinks"> ' . vt('Minimaal afwijkingspercentage') . ' </div>
					<div class="formrechts" >	<input type="text" name="ouderdomPercentage" value="'.$instellingen['ouderdomPercentage'].'" size="2">  </div>
				</div>
			</div>
		</fieldset>
	</div>
	
		<div id="MandaatcontroleDiv" style="display:none">
		<fieldset id="MandaatUitvoer" >
			<legend accesskey="m">' . vt('Mandaatcontrole') . '</legend>
			<div class="formblock">
				<div class="formlinks"> ' . vt('Zorgplichtcategorie') . ' </div>
				<div class="formrechts">
					<select name="mandaat_zorgplichtCategorie">
						'.$zorgplichtSelect.'
					</select>
				</div>
			</div>

			<div class="formblock">
				<div class="formlinks"> ' . vt('Alleen geconsolideerde portefeuilles weergeven') . ' </div>
				<div class="formrechts">
					<input type="checkbox" value="1" '.(($instellingen['restrictie_alleenConsolidaties']=='1')?'checked':'').' name="restrictie_alleenConsolidaties" >
				</div>
			</div>

		</fieldset>
	</div>

<div class="formblock">
<div class="formlinks" style="width:300px"> ' . vt('Uitvoer') . '   <select name="filetype">
  <option '.(($instellingen['filetype']=='csv')?'selected':'').' value="csv">' . vt('csv') . '</option>;
  <option '.(($instellingen['filetype']=='pdf')?'selected':'').' value="pdf">' . vt('pdf') . '</option>;
  <option '.(($instellingen['filetype']=='xls')?'selected':'').' value="xls">' . vt('xls') . '</option>;
  </select>
</div>
</div>

<div class="formblock"  id="consolidatieDiv" style="display:none">
<div class="formlinks"> ' . vt('Geconsolideerde portefeuilles') . '</div>
<div class="formrechts">
	<input type="checkbox" value="1" name="geconsolideerd"  '.(($instellingen['geconsolideerd']=='1')?'checked':'').' >
</div>
</div>

'.$htmlPortefeuilleIntern.'<br/><br/>
'.$htmlMetConsolidatie.'

</fieldset>
</div>

<script>

  function checkRapportInstelling()
  {
    $(\'#zorgplichtSelectie\').hide();
    $(\'#Modelcontrole\').hide();
    $(\'#koerscontroleDiv\').hide();
    $(\'#ouderdomsAnalyseDiv\').hide();
    $(\'#MandaatcontroleDiv\').hide();
    $(\'#consolidatieDiv\').show();
    
    var soort = $("#Rapportage").val();
  	if (soort == "Zorgplichtcontrole") // Zorgplicht
	  {
	    $(\'#zorgplichtSelectie\').show();
	  }
	  else if (soort == "Modelcontrole") // Modelcontrole
	  {
      $(\'#Modelcontrole\').show();
    } 
    else if (soort == "koersControle") // koersControle
	  {
      $(\'#koerscontroleDiv\').show();
      $(\'#consolidatieDiv\').hide();
    } 
    else if (soort == "ouderdomsAnalyse") // ouderdomsAnalyse
	  {
      $(\'#ouderdomsAnalyseDiv\').show();
      $(\'#consolidatieDiv\').hide();
    }
    else if (soort == "Mandaatcontrole" || soort == "Mandaatcontrole_L79") // Mandaatcontrole
	  {
      $(\'#MandaatcontroleDiv\').show();
    }
    
    
	}
	
	checkRapportInstelling();
	
	</script>

';

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>