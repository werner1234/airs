<?php
$DB=new DB();

$query="SELECT indices.id As indexId, Fondsen.Fonds as hoofdFonds, indices.Fonds as indexFonds, indices.OptieUitoefenPrijs as opslag
    FROM Fondsen JOIN Fondsen AS indices ON indices.OptieBovenliggendFonds=Fondsen.Fonds AND indices.fondssoort='INDEX'
    WHERE Fondsen.HeeftOptie=1 AND Fondsen.fondssoort='INDEX'";
$DB->SQL($query);
$DB->Query();
$indices=array();
while($indexData=$DB->nextRecord())
  $indices[]=$indexData;

$query="SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Opbrengst = '1' OR  Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
$DB->SQL($query);
$DB->Query();

if(!isset($prefix))
  $prefix='';
else
  $mutSettings='';

$anchorLenght=strlen($prefix.'MUT_');
$mutSettings.="
<script>

function ".$prefix."checkAll(optie)
{
  if(document.selectForm){var theForm = document.selectForm.elements, z = 0;}
  else{var theForm = document.editForm.elements, z = 0;}
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,".$anchorLenght.") == '".$prefix."MUT_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}

</script>
<div id=\"wrapper\" style=\"overflow:hidden;\">
<div class=\"buttonDiv\" style=\"width:70px;float:left;\" onclick=\"".$prefix."checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> Alles</div>
<div class=\"buttonDiv\" style=\"width:70px;float:left;\" onclick=\"".$prefix."checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> Niets</div>
</div>";
$mutSettings.='<table>';

while($gb=$DB->nextRecord())
{
  if($_SESSION['lastPost']['MUT_'.$gb['Grootboekrekening']] == 1 || $_SESSION['backofficeSelectie']['MUT_'.$gb['Grootboekrekening']]==1 || $checks[$prefix.'MUT_'.$gb['Grootboekrekening']]==1)
    $check='checked';
  else
    $check = '';
  
  $mutSettings.='<tr><td><label for="'.$prefix.'MUT_'.$gb['Grootboekrekening'].'" title="'.$gb['Omschrijving'].'">'.$gb['Omschrijving'].'</td><td><input type="hidden" name="'.$prefix.'MUT_'.$gb['Grootboekrekening'].'" value="0"><input type="checkbox" name="'.$prefix.'MUT_'.$gb['Grootboekrekening'].'" value="1" '.$check.' ></td></tr>';
}
$mutSettings.='</table>';

$mmIndexSettings='<table>';
foreach($indices as $index)
{
  if($_SESSION['lastPost']['mmIndex_'.$index['indexId']] == $index['indexFonds'] || $checks[$prefix.'mmIndex_'.$index['indexId']]==1)
    $check='checked';
  else
    $check = '';
  $mmIndexSettings.='<tr><td>'.$index['indexFonds'].'</td><td><input type="checkbox" name="'.$prefix.'mmIndex_'.$index['indexId'].'" value="'.$index['indexFonds'].'" '.$check.' ></td></tr>';
}
$mmIndexSettings.='</table>';

$rapportSettings['default']='';
if($rdata['layout'] == 13 || $rdata['Layout'] == 13)
{
  if($rdata['Layout'] == 13)
    $var='backofficeSelectie';
  else
    $var='lastPost';
  
  if($_SESSION[$var]['vvgl'] == 1)
    $checks['vvglCheck'] = 'checked';
  if($_SESSION[$var]['perc'] == 1)
    $checks['percCheck'] = 'checked';
  if($_SESSION[$var]['opbr'] == 1)
    $checks['opbrCheck'] = 'checked';
  if($_SESSION[$var]['kost'] == 1)
    $checks['kostCheck'] = 'checked';
  if($_SESSION[$var]['kostPerc'] == 1)
    $checks['kostCheck'] = 'checked';
  if($_SESSION[$var]['GB_STORT_ONTTR'] == 1)
    $checks['STORT_ONTTRCheck'] = 'checked';
  if($_SESSION[$var]['GB_overige'] == 1)
    $checks['overigeCheck'] = 'checked';
  if($_SESSION[$var]['TRANS_RESULT'] == 1)
    $checks['TRANS_RESULT'] = 'checked';
  
  if($_SESSION[$var]['PERFG_totaal'] == 1)
    $checks['PERFG_totaalCheck'] = 'checked';
  if($_SESSION[$var]['PERFG_perc'] == 1)
    $checks['PERFG_percCheck'] = 'checked';
  
  
}
elseif ($rdata['layout'] == 5)
{
  if($_SESSION['lastPost']['perfBm'] == 1)
    $perfBm = 'checked';
}
elseif ($rdata['layout'] == 8 || $rdata['layout'] == 22 || $rdata['layout'] == 35 || $rdata['layout'] == 40 || $rdata['layout'] == 50 || $rdata['layout'] == 61 || $rdata['layout'] == 91)
{
  if($_SESSION['lastPost']['perfPstart'] == 1)
    $perfPstart = 'checked';
}
elseif ($rdata['layout'] == 32)
{
  if($_SESSION['lastPost']['nummeringUit'] == 1)
    $nummeringUit = 'checked';
}
elseif ($rdata['layout'] == 70)
{
  if($_SESSION['lastPost']['OIR_laatstevijf'] == 1)
    $OIR_laatstevijf = 'checked';
}
elseif ($rdata['layout'] == 75)
{
  if($_SESSION['lastPost']['nummeringUit'] == 1)
    $nummeringUit = 'checked';
  if($_SESSION['lastPost']['frontWit'] == 1)
    $frontWit = 'checked';
}
else
{
  $rapportSettings['default'] ='<b>'.$periode.'</b>';
}

$vkmaJavaBlok='
  parent.frames[\'content\'].$(\'#VKMA_Settings\').hide();
  for(var i=0; i < parent.frames[\'submenu\'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames[\'submenu\'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames[\'submenu\'].document.selectForm.rapport_type[i].value == "VKMA")
 			{
 				parent.frames[\'content\'].$(\'#VKMA_Settings\').show();
 			}
 		}
  }

';

$rapportSettings['default'] .= '

  <!-- '.$prefix.'MUT_Settings -->
  <div class="formHolder"  id="'.$prefix.'MUT_Settings" style="display: none; ">
    <div class="formTitle textB">Mutatie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      '.$mutSettings.'
    </div>
  </div>
  
  <!-- '.$prefix.'SCENARIO_Settings -->
  <div class="formHolder"  id="'.$prefix.'SCENARIO_Settings" style="display: none; ">
    <div class="formTitle textB">Senario</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <input type="checkbox" value="1" name="'.$prefix.'scenario_portefeuilleWaardeGebruik"> Gebruik waarde op rapportage datum.</br>
      <input type="checkbox" value="1" name="'.$prefix.'scenario_werkelijkVerloop"> Werkelijk verloop.
    </div>
  </div>
  
  <!-- Model_Settings -->
  <div class="formHolder"  id="Model_Settings" style="display: none; ">
    <div class="formTitle textB">Niveau</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <input type="radio" name="'.$prefix.'modelcontrole_level" value="fonds" checked> Fonds<br>
      <input type="radio" name="'.$prefix.'modelcontrole_level" value="beleggingscategorie" >Categorie<br>
      <input type="radio" name="'.$prefix.'modelcontrole_level" value="beleggingssector" >Sector<br>
      <input type="radio" name="'.$prefix.'modelcontrole_level" value="Regio" >Regio<br>
    </div>
  </div>
   <script>
   function checkWaardeprognoseSettings()
        {
          if($("#vkma_clientselectie").prop(\'checked\')==true)
          {
            $("#vkma_naam").prop("disabled", true);
            $("#vkma_naam").css(\'background\',\'#eee\');
            $("#vkma_naam").val(\'\');
            $("#vkma_bedrag").prop("disabled", true);
            $("#vkma_bedrag").css(\'background\',\'#eee\');
            $("#vkma_bedrag").val(\'\');
          }
          else
          {
            $("#vkma_naam").prop("disabled", false);
            $("#vkma_naam").css(\'background\',\'\');
            $("#vkma_bedrag").prop("disabled", false);
            $("#vkma_bedrag").css(\'background\',\'\');
          }
        }
      </script>
  
      <div class="formHolder"  id="VKMA_Settings" style="display: none;">
        <div class="formTitle textB">Kostenmaatstaf ex-ante</div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="VKMA_form">
      
          <div class="formblock">
            <div class="formlinks"> Via clientselectie </div>
            <div class="formrechts"> <input type="checkbox" id="vkma_clientselectie" name="vkma_clientselectie" onclick="javascript:checkWaardeprognoseSettings();" value="1" checked size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> Naam </div>
            <div class="formrechts"> <input type="text" id="vkma_naam" name="vkma_naam" style="background:#ccc" value="" disabled size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> Bedrag </div>
            <div class="formrechts"> <input type="text" id="vkma_bedrag" name="vkma_bedrag" style="background:#ccc" value="" disabled size="15">  </div>
          </div>

          <div class="formblock">
            <div class="formlinks"> Eindjaar </div>
            <div class="formrechts"> <input type="text" name="vkma_eindjaar" value="" size="4"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> Kostencomponenten </div>
            <div class="formrechts"> <input type="text" name="vkma_kosten_beheer" value="" size="2" > Beheerkosten <br>
              <input type="text" name="vkma_kosten_service" value="" size="2"> Servicekosten <br>
              <input type="text" name="vkma_kosten_transactie" value="" size="2"> Transactiekosten <br>
              <input type="text" name="vkma_kosten_bank" value="" size="2" > Overige bankkosten <br>
            </div>
          </div>
        </div>
  
      </div>

';

$rapportSelectie[5] =
  "
<script>
function settings()
{
  parent.frames['content'].$('#SCENARIO_Settings').hide();
  parent.frames['content'].$('#PERF_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
  parent.frames['content'].$('#VKMA_Settings').hide();
  
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
      if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERF\")
 			{
 				parent.frames['content'].$('#PERF_Settings').show();
 			}
      if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\" || parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFD\")
 			{
 				parent.frames['content'].$('#PERFG_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
  }
}


</script>
";

$rapportSelectie[8] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
  parent.frames['content'].$('#SCENARIO_Settings').hide();
  parent.frames['content'].$('#VKMA_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
  		if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
  }
}

</script>
";

$rapportSelectie[70] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#OIR_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"OIR\")
 			{
 				parent.frames['content'].$( \"#OIR_Settings\" ).show();
 				parent.frames['content'].document.getElementById('OIR_laatstevijf').checked=\"true\";
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}


}

</script>
";

$rapportSelectie[12] = "
<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
 	parent.frames['content'].$('#mmIndex_Settings').hide();
 	parent.frames['content'].$('#SCENARIO_Settings').hide();
 	parent.frames['content'].$('#VKMA_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\" || parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"HSE\")
 			{
 				parent.frames['content'].$('#PERFG_Settings').show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 				parent.frames['content'].$('#mmIndex_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
  }

}

</script>
";

$rapportSelectie[35] = "
<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
  		if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[40] = "
<script>
function settings()
{
  parent.frames['content'].$( \"#KERNV_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
  		if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"KERNV\")
 			{
 				parent.frames['content'].$( \"#KERNV_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[51] = "
<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
 	parent.frames['content'].$('#mmIndex_Settings').hide();
 	parent.frames['content'].$('#SCENARIO_Settings').hide();
 	parent.frames['content'].$('#VKMA_Settings').hide();
  parent.frames['content'].$('#JOURNAAL_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\" || parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"KERNZ\")
 			{
 				//parent.frames['content'].$('#PERFG_Settings').show();
 				//parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 				parent.frames['content'].$('#mmIndex_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"JOURNAAL\")
 			{
 				parent.frames['content'].$('#JOURNAAL_Settings').show();
 			}
 		}
  }

}

</script>
";

$rapportSelectie[50] = $rapportSelectie[35];
$rapportSelectie[68] = str_replace('PERFG','GRAFIEK',$rapportSelectie[35]);
$rapportSelectie[86] = $rapportSelectie[35];
$rapportSelectie[97] = str_replace('PERFG','PERFD',$rapportSelectie[35]);


$rapportSelectie[61] ="<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=false;
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[7] =
  "
<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
   parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
   for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}

</script>
";

$rapportSelectie[13] =
  "
<script>
function settings()
{
  parent.frames['content'].$('#PERF_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$('#SMV_Settings').hide();
  parent.frames['content'].$('#TRANS_Settings').hide();
  parent.frames['content'].$('#Model_Settings').hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERF\" || parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFD\")
 			{
 				parent.frames['content'].$('#PERF_Settings').show();
 			}
 	 		if( parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$('#PERFG_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\" || parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"OIR\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SMV\")
 			{
 			  parent.frames['content'].$('#SMV_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"TRANS\")
 			{
  				parent.frames['content'].$('#TRANS_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MODEL\")
 			{
 				parent.frames['content'].$('#Model_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}

</script>
";

$rapportSelectie[22] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=true;
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";
$rapportSelectie[91]=$rapportSelectie[22];


$rapportSelectie[29] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#OIH_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"OIH\")
 			{
 				parent.frames['content'].$( \"#OIH_Settings\" ).show();
 			}
 		  if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[32] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#FRONT_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"FRONT\")
 			{
 				parent.frames['content'].$( \"#FRONT_Settings\" ).show();
 				//parent.frames['content'].document.getElementById('attPstart').checked=true;
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[75] =
  "
<script>
function settings()
{
  parent.frames['content'].$( \"#FRONT_Settings\" ).hide();
  parent.frames['content'].$( \"#VKMA_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"FRONT\")
 			{
 				parent.frames['content'].$( \"#FRONT_Settings\" ).show();
 				//parent.frames['content'].document.getElementById('attPstart').checked=true;
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie[80] = "
<script>
function settings()
{
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$('#SCENARIO_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
 	parent.frames['content'].$('#mmIndex_Settings').hide();
 	parent.frames['content'].$('#VKMA_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
  		if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$('#PERFG_Settings').show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 				parent.frames['content'].$('#mmIndex_Settings').show();
 			}
 			else if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"KERNV\")
 			{
 				parent.frames['content'].$('#mmIndex_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"VKMA\")
 			{
 				parent.frames['content'].$('#VKMA_Settings').show();
 			}
 		}
  }

}

</script>
";

$rapportSelectie[84] =
  "
<script>
function settings()
{

  parent.frames['content'].$( \"#MUT_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$( \"#MUT_Settings\" ).show();
 			}
 		}
 	}
  
  parent.frames['content'].$('#SCENARIO_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 		}
  }


  parent.frames['content'].$( \"#RISK_Settings\" ).hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"RISK\")
 			{
 				parent.frames['content'].$( \"#RISK_Settings\" ).show();
 			}
 		}
 	}
}
</script>
";

$rapportSelectie['default_b'] = "
function selectTab()
{
	document.getElementById('MUT').style.visibility=\"hidden\";
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				document.getElementById('MUT').style.visibility=\"visible\";
 			}
 		}
 	}
}
";

$rapportSelectie['5_b'] =
  "
function selectTab()
{
  parent.frames['content'].$('#PERF_Settings').hide();
  parent.frames['content'].$('#SCENARIO_Settings').hide();
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERF\")
 			{
        parent.frames['content'].$('#PERF_Settings').show();
 			}
      
      if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SCENARIO\")
 			{
 				parent.frames['content'].$('#SCENARIO_Settings').show();
 			}
 		}
 	}
}
";

$rapportSelectie['7_b'] =
  "
function selectTab()
{

  parent.frames['content'].$( \"#MUT_Settings\" ).hide();

 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$( \"#MUT_Settings\" ).show();
 			}
 		}
 	}
}
";

$rapportSelectie['8_b'] =
  "
function selectTab()
{
	parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
 		}
 	}
}
";

$rapportSelectie['13_b'] =
  "
function selectTab()
{
  parent.frames['content'].$('#PERF_Settings').hide();
  parent.frames['content'].$('#PERFG_Settings').hide();
  parent.frames['content'].$('#MUT_Settings').hide();
  parent.frames['content'].$('#SMV_Settings').hide();
  parent.frames['content'].$('#TRANS_Settings').hide();
  parent.frames['content'].$('#Model_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERF\")
 			{
 				parent.frames['content'].$('#PERF_Settings').show();
 			}
 	 		if( parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$('#PERFG_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"SMV\")
 			{
 			  parent.frames['content'].$('#SMV_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"TRANS\")
 			{
  				parent.frames['content'].$('#TRANS_Settings').show();
 			}
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MODEL\")
 			{
 				parent.frames['content'].$('#Model_Settings').show();
 			}
 		}
 	}
  
}
";

$rapportSelectie['22_b'] =
  "
function selectTab()
{
	parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
 		}
 	}
}
";


$rapportSelectie['35_b'] =
  "
function selectTab()
{
	parent.frames['content'].$( \"#PERFG_Settings\" ).hide();
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"PERFG\")
 			{
 				parent.frames['content'].$( \"#PERFG_Settings\" ).show();
 				parent.frames['content'].document.getElementById('perfPstart').checked=\"true\";
 			}
 		}
 	}
}
";

$rapportSelectie['50_b'] = $rapportSelectie['35_b'];

$rapportSelectie['70_b'] =
  "
function selectTab()
{

  parent.frames['content'].$('#MUT_Settings').hide();
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"MUT\")
 			{
 				parent.frames['content'].$('#MUT_Settings').show();
 			}
 		}
 	}
 	
  parent.frames['content'].$('#OIR_Settings').hide();
  for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == \"OIR\")
 			{
 				parent.frames['content'].$('#OIR_Settings').show();
 				//parent.frames['content'].document.getElementById('OIR_laatstevijf').checked=\"true\";
 			}
 		}
 	}
}
";

$rapportSelectie['91_b'] =$rapportSelectie['22_b'] ;

$autoHideL5 = '
  <script>
    $(\'#PERF_Settings\').hide();
    $(\'#SCENARIO_Settings\').hide();
    $(\'#PERFG_Settings\').hide();
  </script>
';


$rapportSettings[5] = '

  <div id="'.$prefix.'settingsContainer">
  
    <!-- PERF_Settings -->
    <div class="formHolder"  id="PERF_Settings" style="display: none; ">
      <div class="formTitle textB">Performance</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <table>
          <tr><td>BM</td><td><input type="checkbox" name="'.$prefix.'perfBm" value="1" '.$perfBm.' ></td></tr>
        </table>
      </div>
    </div>
    
    <!-- SCENARIO_Settings -->
    <div class="formHolder"  id="SCENARIO_Settings" style="display: none; ">
      <div class="formTitle textB">Senario</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <input type="checkbox" value="1" name="'.$prefix.'scenario_portefeuilleWaardeGebruik" checked> Gebruik waarde op rapportage datum.</br>
        <input type="checkbox" value="1" name="'.$prefix.'scenario_werkelijkVerloop"> Werkelijk verloop.
      </div>
    </div>
    
    <!-- PERFG_Settings -->
    <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
      <div class="formTitle textB">Perf G/D</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <input type="checkbox" value="1" name="'.$prefix.'perfg_rapportagePeriode"> Gebruik rapportageperiode.</br>
      </div>
    </div>
    
    '.$rapportSettings['default'].'
  
  </div>
  
  '.$autoHideL5.'

';


$rapportSettings[7] = '

  <!-- MUT_Settings -->
  <div class="formHolder"  id="MUT_Settings" style="display: none; ">
    <div class="formTitle textB">Mutatie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      '.$mutSettings.'
    </div>
  </div>
  
  <script>
    $(\'#MUT_Settings\').hide();
  </script>

';


$rapportSettings[8] = '

  <!-- Performance -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" id="perfPstart" name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[12] = '
<td valign="top">
    <div id="'.$prefix.'settingsContainer">
    <b>'.$periode.'</b>
    
  <!-- MUT_Settings -->
  <div class="formHolder '.$prefix.'MUT_Settings"  id="MUT_Settings" style="display: none; ">
    <div class="formTitle textB">Mutatie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      '.$mutSettings.'
    </div>
  </div>
  
  <!-- PERFG_Settings -->
  <div class="formHolder '.$prefix.'PERFG_Settings"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB"></div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" id="'.$prefix.'perfPstart" name="'.$prefix.'perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>
  
      <!-- -->
      <div class="formHolder '.$prefix.'mmIndex_Settings"   id="mmIndex_Settings" style="display: none; ">
        <div class="formTitle textB">MM-index</div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          '.$mmIndexSettings.'
        </div>
      </div>
  
  
      <!-- SCENARIO_Settings -->
      <div class="formHolder '.$prefix.'SCENARIO_Settings"   id="SCENARIO_Settings" style="display: none; ">
        <div class="formTitle textB">Senario</div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
          <input type="checkbox" value="1" name="scenario_portefeuilleWaardeGebruik"> Gebruik waarde op rapportage datum.</br>
          <input type="checkbox" value="1" name="scenario_werkelijkVerloop"> Werkelijk verloop.</br>
          '.$inflatieVinkje.'
        </div>
      </div>
  
  
      <script>
        function checkWaardeprognoseSettings()
        {
          if($("#vkma_clientselectie").prop(\'checked\')==true)
          {
            $("#vkma_naam").prop("disabled", true);
            $("#vkma_naam").css(\'background\',\'#eee\');
            $("#vkma_naam").val(\'\');
            $("#vkma_bedrag").prop("disabled", true);
            $("#vkma_bedrag").css(\'background\',\'#eee\');
            $("#vkma_bedrag").val(\'\');
          }
          else
          {
            $("#vkma_naam").prop("disabled", false);
            $("#vkma_naam").css(\'background\',\'\');
            $("#vkma_bedrag").prop("disabled", false);
            $("#vkma_bedrag").css(\'background\',\'\');
          }
        }
      </script>
  
      <div class="formHolder"  id="VKMA_Settings" style="display: none;">
        <div class="formTitle textB">Kostenmaatstaf ex-ante</div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="VKMA_form">
      
          <div class="formblock">
            <div class="formlinks">Via clientselectie</div>
            <div class="formrechts"> <input type="checkbox" id="vkma_clientselectie" name="vkma_clientselectie" onclick="javascript:checkWaardeprognoseSettings();" value="1" checked size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks">Naam</div>
            <div class="formrechts"> <input type="text" id="vkma_naam" name="vkma_naam" style="background:#ccc" value="" disabled size="25"> </div>
          </div>
      
          <div class="formblock">
            <div class="formlinks"> Bedrag</div>
            <div class="formrechts"> <input type="text" id="vkma_bedrag" name="vkma_bedrag" style="background:#ccc" value="" disabled size="15">  </div>
          </div>

          <div class="formblock">
            <div class="formlinks"> "Eindjaar" </div>
            <div class="formrechts"> <input type="text" name="vkma_eindjaar" value="" size="4"> </div>
          </div>


            <table style="margin-left: 13px; width: 330px;">
              <tr><td><strong>Kostencomponenten</strong></td></tr>
              <tr><td><b>Kostensoort</b></td><td><b>Bedrag</b></td><td><b>%</b></td><td><strong>BTW-percentag</strong></td></tr>
              <tr><td>Beheerkosten</td>      <td><input type="text" name="vkma_bedrag_beheer" value="" size="2" ></td>    <td><input type="text" name="vkma_kosten_beheer" value="" size="2" ></td>           <td><input type="text" name="vkma_btw_beheer" value="" size="2" ></td></tr>
              <tr><td>Servicekosten</td>     <td><input type="text" name="vkma_bedrag_service" value="" size="2" ></td>   <td><input type="text" name="vkma_kosten_service" value="" size="2" ></td></tr>
              <tr><td>Transactiekosten</td>  <td><input type="text" name="vkma_bedrag_transactie" value="" size="2" ></td><td><input type="text" name="vkma_kosten_transactie" value="" size="2" ></td></tr>
              <tr><td>Overige bankkosten</td><td><input type="text" name="vkma_bedrag_bank" value="" size="2" ></td>      <td><input type="text" name="vkma_kosten_bank" value="" size="2" ></td></tr>
            </table>
        </div>
  
      </div>
  
      <div class="formHolder"  id="JOURNAAL_Settings" style="display: none;">
        <div class="formTitle textB">Journaal opties</div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="JOURNAAL_form">
      
          <div class="formblock">
            <div class="formlinks"> Rekeningmutaties per rekening</div>
            <div class="formrechts"> <input type="hidden" name="journaal_perRekening" value="0"> <input type="checkbox" id="journaal_perRekening" name="journaal_perRekening" value="1" > </div>
          </div>
      </div>







';

$rapportSettings[80] = $rapportSettings[12];

$rapportSettings[35] = '

  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" id="perfPstart" name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>

';
$rapportSettings[40] = '

  <!-- KERNV_Settings -->
  <div class="formHolder"  id="KERNV_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" id="perfPstart" name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[50] = $rapportSettings[35];
$rapportSettings[61] = $rapportSettings[35];
$rapportSettings[68] = str_replace(array('PERFG','Performance'),array('GRAFIEK','Grafiek'),$rapportSettings[35]);
$rapportSettings[86] = $rapportSettings[35];
$rapportSettings[97] = str_replace(array('PERFG','Performance'),array('PERFD','Grafiek'),$rapportSettings[35]);

$rapportSettings[70] = '

  <!-- OIR_Settings -->
  <div class="formHolder"  id="OIR_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Alleen laatste 5 jaar</td><td><input type="checkbox" id="OIR_laatstevijf" name="OIR_laatstevijf" value="1" '.$OIR_laatstevijf.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[22] = '

  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" id="perfPstart"  name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>

';
$rapportSettings[91]=$rapportSettings[22];
/*
<fieldset id="MUT_Settings" >
<legend accesskey="m">Mutatie-overzicht</legend>
'.$mutSettings.'
</fieldset>
*/
$autoHideL13 = '
  <script>
    $(\'#'.$prefix.'PERF_Settings\').hide();
    $(\'#'.$prefix.'MUT_Settings\').hide();
    $(\'#'.$prefix.'SMV_Settings\').hide();
    $(\'#'.$prefix.'TRANS_Settings\').hide();
    $(\'#'.$prefix.'Model_Settings\').hide();
  </script>
';

$rapportSettings[13] = '
  <td valign="top">
    <div id="'.$prefix.'settingsContainer">
    <b>'.$periode.'</b>

    <!-- PERF_Settings -->
    <div class="formHolder '.$prefix.'PERF_Settings"  id="PERF_Settings" style="display: none; ">
      <div class="formTitle textB">Performance</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <table>
          <tr><td>v vgl</td><td><input type="checkbox" name="'.$prefix.'vvgl" value="1" '.$checks['vvglCheck'].' ></td></tr>
          <tr><td>perc</td><td><input type="checkbox" name="'.$prefix.'perc" value="1" '.$checks['percCheck'].' ></td></tr>
          <tr><td>opbr</td><td><input type="checkbox" name="'.$prefix.'opbr" value="1" '.$checks['opbrCheck'].' ></td></tr>
          <tr><td>kost</td><td><input type="checkbox" name="'.$prefix.'kost" value="1" '.$checks['kostCheck'].' ></td></tr>
          <tr><td>o/k.perc.</td><td><input type="checkbox" name="'.$prefix.'kostPerc" value="1" '.$checks['kostPerc'].' ></td></tr>
        </table>
      </div>
    </div>

    <!-- PERFG_Settings -->
    <div class="formHolder '.$prefix.'PERFG_Settings"  id="PERFG_Settings" style="display: none; ">
      <div class="formTitle textB">Performance G</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <table>
          <tr><td>Totaal</td><td><input type="checkbox" name="'.$prefix.'PERFG_totaal" value="1" '.$checks['PERFG_totaalCheck'].' ></td></tr>
          <tr><td>perc</td><td><input type="checkbox" name="'.$prefix.'PERFG_perc" value="1" '.$checks['PERFG_percCheck'].' ></td></tr>
        </table>
      </div>
    </div>

    <!-- SMV_Settings -->
    <div class="formHolder '.$prefix.'SMV_Settings"  id="SMV_Settings" style="display: none; ">
      <div class="formTitle textB">Saldomutatieverloop</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <table>
          <tr><td>Stortingen/Onttrekkingen</td><td><input type="checkbox" name="'.$prefix.'GB_STORT_ONTTR" value="1" '.$checks['STORT_ONTTRCheck'].'></td></tr>
          <tr><td>Overige</td><td><input type="checkbox" name="'.$prefix.'GB_overige" value="1" '.$checks['overigeCheck'].'></td></tr>
        </table>
      </div>
    </div>

    <!-- TRANS_Settings -->
    <div class="formHolder '.$prefix.'TRANS_Settings"  id="TRANS_Settings" style="display: none; ">
      <div class="formTitle textB">Transactie-overzicht</div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
        <table>
          <tr><td>Resultaat</td><td><input type="checkbox" name="'.$prefix.'TRANS_RESULT" value="1" '.$checks['TRANS_RESULT'].'></td></tr>
        </table>
      </div>
    </div>

    '.$rapportSettings['default'].'

    '.$autoHideL13.'
  <div>
</td>

';

$rapportSettings[29] = '
  <script>
    function appendOptionLast()
    {
      var elOptNew = document.createElement(\'option\');
      var datum=document.getElementById(\'datum\').value;
      elOptNew.text = datum;
      elOptNew.value = datum;
      var elSel = document.getElementById(\'selectedFields\');
      try {
        elSel.add(elOptNew, null); // standards compliant;
      }
      catch(ex) {
        elSel.add(elOptNew); // IE only
      }
    }
    
    function removeOptionLast()
    {
      var elSel = document.getElementById(\'selectedFields\');
      if (elSel.length > 0)
      {
        elSel.remove(elSel.length - 1);
      }
    }
  </script>


  <!-- OIH_Settings -->
  <div class="formHolder"  id="OIH_Settings" style="display: none; ">
    <div class="formTitle textB">Datum selectie</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr>
          <td>Datum<input type="text" id="datum" name="datum" value="'.date("d-m-Y").'"></td>
          <td rowspan=3><select id="selectedFields" name="selectedFields[]" multiple size="8" style="width : 100px"></td>
        </tr>
        <tr><td><input type="button" value="Datum toevoegen." onclick="javascript:appendOptionLast();"></td></tr>
        <tr><td><input type="button" value="Datum verwijderen." onclick="javascript:removeOptionLast();"></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[32] = '

  <!-- FRONT_Settings -->
  <div class="formHolder"  id="FRONT_Settings" style="display: none; ">
    <div class="formTitle textB">FRONT</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Paginanummers onderdrukken</td><td><input type="checkbox" id="nummeringUit"  name="nummeringUit" value="1" '.$nummeringUit.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[75] = '

  <!-- FRONT_Settings -->
  <div class="formHolder"  id="FRONT_Settings" style="display: none; ">
    <div class="formTitle textB">FRONT</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Paginanummers onderdrukken</td><td><input type="checkbox" id="nummeringUit"  name="nummeringUit" value="1" '.$nummeringUit.' ></td></tr>
        <tr><td>Geen achtergrondkleur</td><td><input type="checkbox" id="frontWit"  name="frontWit" value="1" '.$frontWit.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings[84] = '

  <!-- RISK_Settings -->
  <div class="formHolder"  id="RISK_Settings" style="display: none; ">
    <div class="formTitle textB">Risk</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Jaren historie</td><td><select  id="RISK_jaren" name="RISK_jaren"><option>1</option><option>2</option><option>3</option><option>4</option><option selected>5</option></select></td></tr>
      </table>
    </div>
  </div>

';


/*
$rapportSettings[33] =
'
<td valign="top">

<fieldset id="vastrentend_settings">
<legend accesskey="e">Instellingen</legend>
<table>
<tr><td>Vastrentend</td><td><input type="checkbox" name="vastrentend" value="1" '.$vastrentend.' ></td></tr>
<tr><td>Zakelijk</td><td><input type="checkbox" name="zakelijk" value="1" '.$zakelijk.' ></td></tr>

</table>
</fieldset>
';
*/

$rapportSettings['5_b'] = '

  <!-- PERF_Settings -->
  <div class="formHolder"  id="PERF_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>BM</td><td><input type="checkbox" name="perfBm" value="1" '.$perfBm.' ></td></tr>
      </table>
    </div>
  </div>
  
  <!-- SCENARIO_Settings -->
  <div class="formHolder"  id="SCENARIO_Settings" style="display: none; ">
    <div class="formTitle textB">Senario</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <input type="checkbox" value="1" name="scenario_portefeuilleWaardeGebruik" checked> Gebruik waarde op rapportage datum.</br>
      <input type="checkbox" value="1" name="scenario_werkelijkVerloop"> Werkelijk verloop.</fieldset>
    </div>
  </div>

  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Perf G/D</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <input type="checkbox" value="1" name="perfg_rapportagePeriode"> Gebruik rapportageperiode.
    </div>
  </div>

';

$rapportSettings['70_b'] ='

  <!-- OIR_Settings -->
  <div class="formHolder"  id="OIR_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Alleen laatste 5 jaar</td><td><input type="checkbox" id="OIR_laatstevijf" name="OIR_laatstevijf" value="1" '.$OIR_laatstevijf.' ></td></tr>
      </table>
    </div>
  </div>

  <!-- MUT_Settings -->
  <div class="formHolder"  id="MUT_Settings" style="display: none; ">
    <div class="formTitle textB">Mutatie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      '.$mutSettings.'
    </div>
  </div>
  
';

$rapportSettings['8_b'] = '
  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>

';

$rapportSettings['35_b'] = '
  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>vanaf begin</td><td><input type="checkbox" name="perfPstart" value="1" '.$perfPstart.' ></td></tr>
      </table>
    </div>
  </div>
';
$rapportSettings['50_b'] = $rapportSettings['35_b'];
$rapportSettings['61_b'] = $rapportSettings['35_b'];

$rapportSettings['13_b'] = '
  <!-- PERF_Settings -->
  <div class="formHolder"  id="PERF_Settings" style="display: none; ">
    <div class="formTitle textB">Performance</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>v vgl</td><td><input type="checkbox" name="vvgl" value="1" '.$vvglCheck.' ></td></tr>
        <tr><td>perc</td><td><input type="checkbox" name="perc" value="1" '.$percCheck.' ></td></tr>
        <tr><td>opbr</td><td><input type="checkbox" name="opbr" value="1" '.$opbrCheck.' ></td></tr>
        <tr><td>kost</td><td><input type="checkbox" name="kost" value="1" '.$kostCheck.' ></td></tr>
      </table>
    </div>
  </div>

  <!-- PERFG_Settings -->
  <div class="formHolder"  id="PERFG_Settings" style="display: none; ">
    <div class="formTitle textB">Performance G</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Totaal</td><td><input type="checkbox" name="PERFG_totaal" value="1" '.$PERFG_totaalCheck.' ></td></tr>
        <tr><td>perc</td><td><input type="checkbox" name="PERFG_perc" value="1" '.$PERFG_percCheck.' ></td></tr>
      </table>
    </div>
  </div>

  <!-- MUT_Settings -->
  <div class="formHolder"  id="MUT_Settings" style="display: none; ">
    <div class="formTitle textB">Mutatie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      '.$mutSettings.'
    </div>
  </div>

  <!-- SMV_Settings -->
  <div class="formHolder"  id="SMV_Settings" style="display: none; ">
    <div class="formTitle textB">Saldomutatieverloop</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Stortingen/Onttrekkingen</td><td><input type="checkbox" name="GB_STORT_ONTTR" value="1" '.$STORT_ONTTRCheck.'></td></tr>
        <tr><td>Overige</td><td><input type="checkbox" name="GB_overige" value="1" '.$overigeCheck.'></td></tr>
      </table>
    </div>
  </div>

  <!-- TRANS_Settings -->
  <div class="formHolder"  id="TRANS_Settings" style="display: none; ">
    <div class="formTitle textB">Transactie-overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      <table>
        <tr><td>Resultaat</td><td><input type="checkbox" name="TRANS_RESULT" value="0" '.$TRANS_RESULT.'></td></tr>
      </table>
    </div>
  </div>

';



$rapportSettings['default_b'] = '
  <!-- Modelcontrole -->
  <div class="formHolder"  id="Modelcontrole" style="display: none; ">
    <div class="formTitle textB">Mutatie overzicht</div>
    <div class="formContent formContentForm pl-4 pt-2 PB-2" id="MUT">
        '.$mutSettings.'
    </div>
  </div>
';


?>