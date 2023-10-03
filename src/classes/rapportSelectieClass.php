<?
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/06/30 06:55:29 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: rapportSelectieClass.php,v $
 		Revision 1.2  2008/06/30 06:55:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:27:35  rvv
 		*** empty log message ***
 		
 	
*/

class rapportSelectie
{
  var $type;
  
  function rapportSelectie($type)
  {
    global $USR;
    session_start();
    $this->type = $_GET['type'];
    $this->superUser = checkAccess();
    if(!$this->superUser)
      $this->queryPortefeuilleJoin = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' ";
    $this->DB = new DB();
    $this->laatsteDatum = getLaatsteValutadatum();

    if($_GET['actief'] == "inactief" )
      $this->actievePortefeuilles = false;
    else 
    {
      $this->actievePortefeuilles = true;  
      $this->queryActief = " AND Portefeuilles.Einddatum  >=  NOW() ";
    }
    $this->script = 'rapportSelectie.php'; 

	}
  
  function genereerHTML()
  {
    switch($this->type)
	  {
	    case "dagRapportage" :
	      //dagRapportage
	      $this->dagRapportage();	      
	    break;  
	    case "maandRapportage" :
	      //maandRapportage
	      $this->maandRapportage();
	    break;  
	    case "kwartaalRapportage" :
	      //maandRapportage
	      $this->kwartaalRapportage();
	    break; 
	    case "frontConsolidatie" :
	      //frontConsolidatie
	      $this->frontConsolidatie();
	    break;  
	    case "factuur" :
	      //factuurRapportage
	      $this->factuurRapportage();
	    break;  	     
	    default:
        //frontoffice
        $this->frontoffice();
      break;
	  }  
  }
    
  
  function frontoffice()
  {
  //  $this->getVoorkeurSelectie(); //frontoffice rapport vinkjes uit database halen
    
    global $__appvar;
    global $USR;
    $list = new MysqlList();
    $list->idField = "id";
    $list->editScript = $editScript;
    $list->perPage = $__appvar['rowsPerPage'];

    $list->addField("Portefeuilles","id",array("width"=>100,"search"=>false));
    $list->addField("Portefeuilles","Portefeuille",array("list_width"=>150,"search"=>true));
    $list->addField("Portefeuilles","Client",array("list_width"=>200,"search"=>true));
    $list->addField("Client","Naam",array("search"=>true));
    
    if(!$this->superUser) // normale users mogen alleen hun eigen vermogensbeheerders zien
    {
	   $list->setJoin($this->queryPortefeuilleJoin);
    }  
    
    if($_GET['letter'])
	   $extraWhere = " AND Portefeuilles.Client LIKE '".mysql_escape_string($_GET['letter'])."%' ";

    $list->setWhere("Portefeuilles.Client = Clienten.Client ".$extraWhere.$this->queryActief);

    $_GET['sort'][] = "Portefeuilles.Client";
    $_GET['direction'][] = "ASC";

    $list->setOrder($_GET['sort'],$_GET['direction']);
    $list->setSearch($_GET['selectie']);
    $list->selectPage($_GET['page']);

    $_SESSION[NAV] = new NavBar('rapportSelectie.php', getenv("QUERY_STRING"));
    $_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],false));
    $_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
 
    $this->genereerMenuLinks();
    
    $content['jsincludes'] = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
    $content['javascript'] = "";
    $content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
    $kal = new DHTML_Calendar();
    $content['calendar'] = $kal->get_load_files_code();

    echo template($__appvar["templateContentHeader"],$content);
    // selecteer laatst bekende valutadatum
    $this->addJavascript();
	 
    $this->frontOfficeTabs();
    
    $this->frontOfficeFormKop();
    $this->frontOfficeDatumSelectie();
    ?>
    <table cellspacing="0">
    <?=$list->printHeader();?>
    <?php
    if (GetModuleAccess("CRM"))
    {
      $crmLink = "<td><a href=\"CRM_nawEdit.php?do=viaFrontOffice&port={Portefeuille_value}\">[ CRM ]</a></td>";
    }
    $template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" onClick="javascript:document.getElementById(\'{Portefeuille_value}\').checked=true;">
    <td class="list_button">
	   <div class="icon"><input type="radio" value="{Portefeuille_value}" name="Portefeuille" id="{Portefeuille_value}"></div>
    </td>
    <td class="listTableData"  width="150" align="left" >{Portefeuille_value} &nbsp;</td>
    <td class="listTableData"  width="150" align="left" >{Client_value} &nbsp;</td>
    <td class="listTableData"  align="left" >{Naam_value} &nbsp;</td>
    '.$crmLink.'
    </tr>';

    while($data = $list->printRow($template))
    {
	   echo $data;
    }
    ?>
    </table>
    </form>
    <?
    if($__debug) {
	   echo getdebuginfo();
    }
    echo template($__appvar["templateRefreshFooter"],$content);
  }
  
  function dagRapportage()
  {
   global $__appvar,$USR;
   $_SESSION['NAV'] ='';
   $content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
   $kal = new DHTML_Calendar();
   $content['calendar'] = $kal->get_load_files_code();	
   $this->getVoorkeurSelectie();
   $this->genereerMenuLinks();
   echo template($__appvar["templateContentHeader"],$content);
   $this->addJavascript();
   $this->backOfficeTabs();
   $this->backofficeFormKop();
   $this->backofficePortefeuilleSelectie();
   $this->backofficeFromVoet();
   echo template($__appvar["templateRefreshFooter"],$content);
  }
  
  function maandRapportage()
  {
   global $__appvar,$USR;
   $_SESSION['NAV'] ='';
   $content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
   $kal = new DHTML_Calendar();
   $content['calendar'] = $kal->get_load_files_code();	
   $this->getVoorkeurSelectie();
   $this->genereerMenuLinks();
   echo template($__appvar["templateContentHeader"],$content);
   $this->addJavascript();
   $this->backOfficeTabs();
   $this->backofficeFormKop();
   $this->backofficePortefeuilleSelectie();
   $this->backofficeFromVoet();
   echo template($__appvar["templateRefreshFooter"],$content);   
  }
  
  function  kwartaalRapportage()
  {
    global $__appvar,$USR;
   $_SESSION['NAV'] ='';
   $content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
   $kal = new DHTML_Calendar();
   $content['calendar'] = $kal->get_load_files_code();	
   $this->getVoorkeurSelectie();
   $this->genereerMenuLinks();
   echo template($__appvar["templateContentHeader"],$content);
   $this->addJavascript();
   $this->backOfficeTabs();
   $this->backofficeFormKop();
   $this->backofficePortefeuilleSelectie();
   $this->backofficeFromVoet();
   echo template($__appvar["templateRefreshFooter"],$content);   
  }
  
  function factuurrapportage()
  {
   global $__appvar,$USR;
   $_SESSION['NAV'] ='';
   $content[calendarinclude] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
   $kal = new DHTML_Calendar();
   $content[calendar] = $kal->get_load_files_code();	

   echo template($__appvar["templateContentHeader"],$content);
   $this->addJavascript();
   echo "<br>";
   $this->backofficeFormKop();
   $this->backofficePortefeuilleSelectie();
   $this->backofficeFromVoet();
   echo template($__appvar["templateRefreshFooter"],$content);   
  }
  
  function  frontConsolidatie()
  {
   global $__appvar,$USR;
   $_SESSION['NAV']='';
   $this->portefeuilles = array();
   
   if(!$this->superUser) // normale users mogen alleen hun eigen vermogensbeheerders zien
   {
	  $join = $this->queryPortefeuilleJoin;
   }
   
   $query = "SELECT Portefeuille, Client FROM (Portefeuilles) $join WHERE 1 ". $this->queryActief. " ORDER BY Client ";

   $this->DB->SQL($query);
   $this->DB->Query();
   $aantal = $this->DB->records();
   $t=0;
   while($gb = $this->DB->NextRecord())
   {
   $eersteLetter = substr($gb['Client'],0,1);
   $this->portefeuilles[$eersteLetter][$gb['Portefeuille']] = $gb['Portefeuille']. " - ".$gb['Client'];
   } 
  
   $this->genereerMenuLinks();
   
   $content['jsincludes'] = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
   $content['javascript'] = "";
   $content['jsincludes'] .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
   $content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
   $kal = new DHTML_Calendar();
   $content['calendar'] = $kal->get_load_files_code();
   $content['body'] 		= " onLoad=\"javascript:loadPortefeuilles('alles')\" ";
    
   echo template($__appvar["templateContentHeader"],$content);
   $this->addJavascript();
   $this->frontOfficeTabs();
   $this->frontOfficeFormKop();
   $this->frontOfficeDatumSelectie();
   $this->linksRechtsPortefeuilleSelectie();
   ?>
  </form>
   <?
   echo template($__appvar["templateRefreshFooter"],$content);
  }
  
  function getVoorkeurSelectie()
  {
    global $USR;
    // selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.
    if($this->superUser)
    {
      $query = "SELECT OIH, OIS, HSE, OIB, OIV, PERF, VOLK, VHO, TRANS, MUT, OIR, GRAFIEK, Vermogensbeheerders.Export_data_frontOffice as data FROM Vermogensbeheerders LIMIT 1";
      $this->DB->SQL($query);
      $this->DB->Query();
      $preData = $this->DB->nextRecord();

      if(strlen($preData['data']) > 0)
        $unserialise = true; 

      if($unserialise)
      {
        $preData = unserialize($preData['data']);
        foreach ($preData as $key=>$value)
        {
          $this->rdata[$key]=$value['checked'];
        }
      }
      else 
        $this->rdata = $preData;
    }
    else 
    {
      if ($this->type ==  'dagRapportage')
      {
        $selectie = " Vermogensbeheerders.Export_data_dag as data ";
        $unserialise = true;
      }
      else if ($this->type == 'maandRapportage')
      {
        $selectie = " Vermogensbeheerders.Export_data_maand as data ";
        $unserialise = true;
      }
      else if ($this->type == 'kwartaalRapportage')
      {
        $selectie = " Vermogensbeheerders.Export_data_kwartaal as data ";  
        $unserialise = true; 
      }    
      else 
        $selectie = " OIH, OIS, HSE, OIB, OIV, PERF, VOLK, VHO, TRANS, MUT, OIR, Vermogensbeheerders.Export_data_frontOffice as data GRAFIEK ";
      
       $query = "SELECT $selectie  
                 FROM Vermogensbeheerders, VermogensbeheerdersPerGebruiker 
                 WHERE Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
                 AND VermogensbeheerdersPerGebruiker.Gebruiker = \"$USR\" LIMIT 1";
      $this->DB->SQL($query); 
      $this->DB->Query();
      $preData = $this->DB->nextRecord();

      if(strlen($preData['data']) > 0)
        $unserialise = true; 
      
      if($unserialise == true)
      {
        $preData = unserialize($preData['data']);
        foreach ($preData as $key=>$value)
        {
          $this->rdata[$key]=$value['checked'];
        }
      }
      else 
        $this->rdata = $preData;
     }

  }
  
  function genereerMenuLinks()
  {
    global $__appvar;
    $html = "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
    $html .= "
    <script>
    function doStuff()
    {
	   document.selectForm.selected.value = \"\";
	   var tel =0;
	   for(var i=0; i < document.selectForm.rapport_type.length; i++)
	   {
		  if(document.selectForm.rapport_type[i].checked == true)
		  {
			document.selectForm.selected.value = document.selectForm.selected.value + '|' + document.selectForm.rapport_type[i].value;
			tel++;
		  }
	   }
	   executeRequest('ae_ajax_server.php','selectForm', 'storeRapportSelection', responseHandler);
    }

    function responseHandler(requester,formName)
    {
	   var theForm = document.forms[formName];
	   return true;
    }
    </script> ";

    $html .= "<b>selecteer rapport</b><br><br><form name=\"selectForm\">";
        
    while (list($key, $value) = each($__appvar["Rapporten"]))
    {
		 if (is_array($this->rdata))
		 { 
		   if($this->rdata[$key] > 0)
		     $selected = "checked";
	     else 
		     $selected = "";  
		 }
		 else 
		 {
		   if(in_array($key,$_SESSION['rapportSelection']))
		     $selected = "checked";
	     else
		     $selected = "";
		 }
		 
		 if ($this->type == 'dagRapportage')
		   $extra = "parent.frames['content'].selectTab();";
		 else 
		   $extra = '';
		   
	   $html .= "<input type=\"checkbox\" value=\"".$key."\" name=\"rapport_type\" id=\"".$key."\" onClick=\"JavaScript:doStuff();$extra\" ".$selected." >  ".
			 		 "<label for=\"".$key."\" title=\"".$value."\">".$key." </label><br>";
    }
    $html .= "<input name=\"selected\" value=\"\" type=\"hidden\">\n";
    $html .= "</form>";
    
    if($this->type != 'kwartaalRapportage')
      $html .="<br> <iframe src=\"laatsteValuta.php\" width=\"100%\" height=\"80\" marginwidth=\"0\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" align=\"middle\" frameborder=\"0\"></iframe> ";

    $_SESSION['submenu'] = New Submenu();
    $_SESSION['submenu']->addItem($html,"");
    
    if($this->type == 'kwartaalRapportage')
    {
      $_SESSION['submenu']->addItem('Brief opmaak','kwartaalBriefEdit.php');
      $_SESSION['submenu']->addItem('<br>','');
      $_SESSION['submenu']->addItem('ATT opmaak','kwartaalBriefEdit.php?brief=ATTopmaak&titel=ATTtitel');  
    }
  }
  

  
  function addJavascript()
  {
    ?>
    <script type="text/javascript">
    function doStore()
    {
	    executeRequest('ae_ajax_server.php','selectForm', 'storeDate', responseHandler);
    }

    function responseHandler(requester,formName)
    {
	   var theForm = document.forms[formName];
	   return true;
    }

    function setRapportTypes()
    {
      <?
      if($this->type == 'factuur')
      {
       echo 'if(document.selectForm.factuurnummer.value == "")
              {
              alert("Ongeldig factuurnummer!");
              return false;
              }
              else
              return true;';
      }
      else
      {
      ?>
	   document.selectForm.rapport_types.value = "";
	   var tel =0;
 	   for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	   {
 		   if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		   {
 			  document.selectForm.rapport_types.value = document.selectForm.rapport_types.value + '|' + parent.frames['submenu'].document.selectForm.rapport_type[i].value;
 			  tel++;
 		   }
 	   }
 	   return true;
 	   <?}?>
    }

    function print()
    {
      <?
      if($this->type == '' || $this->type == 'frontConsolidatie')
      {
       echo 'document.selectForm.target = "_blank";';
       ?>
    	 if(document.selectForm['inFields[]'])
	     {
		     var inFields  			= document.selectForm['inFields[]'];
		     var selectedFields 	= document.selectForm['selectedFields[]'];

		     for(j=0; j < selectedFields.options.length; j++)
		     {
 			     selectedFields.options[j].selected = true;
		     }
	     }
       <?
      }
      else 
        echo 'document.selectForm.target = "generateFrame";';
      ?>
 	    if(setRapportTypes())
 	    {
	    document.selectForm.save.value="0";
	    document.selectForm.submit();
 	    }
    }

    function saveasfile()
    {
      <?
      if($this->type == 'frontoffice')
        echo 'document.selectForm.target = "_blank";';
      else 
        echo 'document.selectForm.target = "generateFrame";';
      ?>
 	    if(setRapportTypes())
 	    {
	    document.selectForm.save.value="1";
	    document.selectForm.submit();
 	    }
    }
    
    function selectTab()
    {
	    document.getElementById('MUT').style.visibility="hidden";
    	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++) 
 	    {
 		    if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		    {
 			    if(parent.frames['submenu'].document.selectForm.rapport_type[i].value == "MUT")
 			    { 
 				  document.getElementById('MUT').style.visibility="visible";
 			    }
 		    }
 	    }
    }
    
    function exportData()
    {
      <?
      if($this->type == 'frontoffice')
        echo 'document.selectForm.target = "_blank";';
      else 
        echo 'document.selectForm.target = "generateFrame";';
      ?>
 	    if(setRapportTypes())
 	    {
	    document.selectForm.exportToFiles.value="1";
	    document.selectForm.submit();
 	    }
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

function doStore()
{
	executeRequest('ae_ajax_server.php','selectForm', 'storeDate', responseHandler);
}

function responseHandler(requester,formName)
{
	var theForm = document.forms[formName];
	return true;
}

function loadPortefeuilles(letter)
{
  inputBox = document.selectForm['inFields[]'];
  var Portefeuilles = new Array(); 

<?
if(count($this->portefeuilles) > 0 )
{
  while(list($letter,$data)= each($this->portefeuilles))
  {
  echo "Portefeuilles['$letter']	= new Array(); \n";
    while(list($portefeuille,$omschrijving)= each($data))
    {
    echo "Portefeuilles['$letter']['$portefeuille']	= '$omschrijving'; \n"; 
    }
  }
  reset($this->portefeuilles);
}
?>  

  for(var count = inputBox.options.length - 1; count >= 0; count--)
  {
    inputBox.options[count] = null;
  }

  if (letter == 'alles')
  {
    for (keyVar in Portefeuilles ) 
    {
      LoadLetter(Portefeuilles[keyVar]);
    }
  }
  LoadLetter(Portefeuilles[letter]);
}

function LoadLetter(letterPortefeuilles)
{
  inputBox = document.selectForm['inFields[]'];
  for (keyVar in letterPortefeuilles ) 
  {
 		inputBox.options.length++;
		inputBox.options[inputBox.options.length-1].text = letterPortefeuilles[keyVar];
		inputBox.options[inputBox.options.length-1].value = keyVar; 
  }  
}


function csv()
{
	if(document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
		document.selectForm.target = "generateFrame";	
		document.selectForm.filetype.value="cvs";	
		document.selectForm.save.value="1";	
		document.selectForm.submit();
		document.selectForm.filetype.value="pdf";	
	}
}


function xls()
{
	if(document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
		document.selectForm.target = "generateFrame";	
		document.selectForm.filetype.value="xls";	
		document.selectForm.save.value="1";	
		document.selectForm.submit();
		document.selectForm.filetype.value="pdf";	
	}
}
    
  </script>
  <?
  }
  
  function frontOfficeTabs()
  {
    ?>
    <br><br>
    <div class="tabbuttonRow">
	   <input type="button" class="<?if($this->type == '')echo 'tabbuttonActive'; else echo 'tabbuttonInActive';?>"   onclick="javascript:document.location = 'rapportSelectie.php';" id="tabbutton0" value="Clienten">
	   <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeFondsSelectie.php';" id="tabbutton1" value="Fondsen">
	   <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeManagementSelectie.php';" id="tabbutton2" value="Management info">
	   <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeOptieTools.php';" id="tabbutton3" value="Optie tools"> 
	   <input type="button" class="<?if($this->type == 'frontConsolidatie')echo 'tabbuttonActive'; else echo 'tabbuttonInActive';?>"  onclick="javascript:document.location = 'rapportSelectie.php?type=frontConsolidatie';" id="tabbutton4" value="Consolidatie tool"> 
    </div>
    <br>
    <? 
  }
  
  function backOfficeTabs()
  {
   
    ?>
    <br><br>
    <div class="tabbuttonRow">
	   <input type="button" class="<?if($this->type == 'dagRapportage')echo 'tabbuttonActive'; else echo 'tabbuttonInActive';?>" onclick="javascript:document.location = 'rapportSelectie.php?type=dagRapportage';" id="tabbutton0" value="Clienten">
	   <input type="button" class="<?if($this->type == 'maandRapportage')echo 'tabbuttonActive'; else echo 'tabbuttonInActive';?>"  onclick="javascript:document.location = 'rapportSelectie.php?type=maandRapportage';"  id="tabbutton1" value="Maandrapportage">
	   <input type="button" class="<?if($this->type == 'kwartaalRapportage')echo 'tabbuttonActive'; else echo 'tabbuttonInActive';?>"  onclick="javascript:document.location = 'rapportSelectie.php?type=kwartaalRapportage';" id="tabbutton2" value="Kwartaalrapportage">
    </div>
    <br>
    <? 
  }  
  
  function frontOfficeDatumSelectie()
  {
    if($this->actievePortefeuilles)
      $actiefChecked = 'checked';
    else 
      $inactiefChecked = 'checked';
    ?>
    <table border="0">
    <tr>
    <td width="540">
    <div class="form">
    <fieldset id="Selectie" >
    <legend accesskey="S"><u>S</u>electie</legend>
    <div class="formblock">
    <div class="formlinks"> Van datum: </div>
    <div class="formrechts">
    <?php
    $jr = substr($this->laatsteDatum,0,4);
    $kal = new DHTML_Calendar();
    $inp = array ('name' =>"datumVan",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
    echo $kal->make_input_field("",$inp,"onChange=\"javascript:doStore()\"");
    ?>
    <input type="radio" name="actief" id="actief" value="actief" <?=$actiefChecked?> onClick="document.location = '<?=$this->script?>?type=<?=$this->type?>&actief=actief'">
    <label for="actief" title="actief"> Actieve portefeuilles  </label>
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks"> T/m datum: </div>
    <div class="formrechts">
    <?php
    $kal = new DHTML_Calendar();
    $inp = array ('name' =>"datumTm",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($this->laatsteDatum)),'size'  => "11");
    echo $kal->make_input_field("",$inp,"onChange=\"javascript:doStore()\"");
    ?>
    <input type="radio" name="actief" id="actief" value="inactief" <?=$inactiefChecked?> onClick="document.location = '<?=$this->script?>?type=<?=$this->type?>&actief=inactief'">
    <label for="inactief" title="actief"> Alle portefeuilles </label>
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks"> <input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> Logo onderdrukken </div>
    <div class="formrechts"> <input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven"> Voorblad weergeven </div>
    </div>
    </fieldset>
    </div>
    </td>
    <td>
	   <input type="button" onclick="javascript:print();" 			value=" Afdrukken " style="width:100px"><br><br>
	   <input type="button" onclick="javascript:saveasfile();" value=" Opslaan " 	style="width:100px"><br>
    </td>
    </tr>
    <tr>
	   <td colspan="2">
	   
	  <?
	  if($this->type == 'frontConsolidatie')
	  {
	    while(list($letter,$data)= each($this->portefeuilles))
      {
	      echo "<a href=\"javascript:loadPortefeuilles('$letter');\" class=\"letterButton\">".$letter."</a>\n";
      }
      echo "<a href=\"javascript:loadPortefeuilles('alles');\" class=\"letterButton\" style=\"width:26px\">".vt("alles")."</a>\n";
	  }
	  else 
	  {
	  ?><a href="<?=$this->script?>?letter=0-9" class="letterButton" > 0-9 </a><?
    for($a=65; $a <= 90; $a++)
    {
	   echo "<a href=\"".$this->script."?letter=".chr($a)."&actief=".$actief."\" class=\"letterButton\">".chr($a)."</a>\n";
    }
    ?><a href="<?=$this->script?>" class="letterButton" style="width:26px">alles</a><?
	  }
	  ?>
	  </td>
    </tr>
    </table>
     <?
  }
  
  function linksRechtsPortefeuilleSelectie()
  {
  ?>
  <fieldset id="PSelectie" style="width:540">
  <legend accesskey="Sa"><u>S</u>electie</legend>
  <br><br>
  <input type="hidden" name="setValue" value="fields">
  <table cellspacing="0" width="500">
  <tr>
    <td>
	    <select name="inFields[]" multiple size="16" style="width : 200px; margin-left: 13px;">
	    </select>
    </td>
    <td width="70" >
	    <a href="javascript:moveItem(document.selectForm['inFields[]'],document.selectForm['selectedFields[]']);">
		    <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
	    </a>
	    <br><br>
	    <a href="javascript:moveItem(document.selectForm['selectedFields[]'],document.selectForm['inFields[]']);">
		    <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
	    </a>
    </td>
    <td>
	    <select name="selectedFields[]" multiple size="16" style="width : 200px">
	    </select>
   </td>
    <td width="70" >
	    <a href="javascript:moveOptionUp(document.selectForm['selectedFields[]'])">
		    <img src="images/16/pijl_omhoog.png" width="16" height="16" border="0" alt="omhoog" align="absmiddle">
	    </a>
	    <br><br>
	    <a href="javascript:moveOptionDown(document.selectForm['selectedFields[]'])">
		    <img src="images/16/pijl_omlaag.png" width="16" height="16" border="0" alt="omlaag" align="absmiddle">
	    </a>
    </td>
   </tr>
   </table>
  <br><br>
  </fieldset>
  <?
  }
  
  function backofficePortefeuilleSelectie()
  {
    global $USR;
    ?>
<table border="0">
<tr>
<td width="540">

<fieldset id="Selectie" >
<legend accesskey="S"><u>S</u>electie</legend>
	
<?
if(checkAccess($type))
	$join = "";		
else 
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$query="SELECT Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders ".$join." ORDER BY Vermogensbeheerders.Vermogensbeheerder";
$vermogensbeheerderOptions =$this->SelectWaarden($query,'Vermogensbeheerder');
?>
<div class="formblock">
<div class="formlinks"> <?=vt("Van vermogensbeheerder")?> </div>
<div class="formrechts">
<select name="vermogensbeheerderVan" style="width:200px">
<?=$vermogensbeheerderOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m vermogensbeheerder </div>
<div class="formrechts">
<select name="vermogensbeheerderTm" style="width:200px">
<?=$vermogensbeheerderOptions['B']?>
</select>
</div>
</div>
<?
if(checkAccess($type))
	$join = "";		
else 
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$query = "SELECT Accountmanagers.Accountmanager FROM Accountmanagers ".$join." ORDER BY Accountmanager";
$accountmanagerOptions =$this->SelectWaarden($query,'Accountmanager');
?>

<div class="formblock">
<div class="formlinks"> Van accountmanager </div>
<div class="formrechts">
<select name="accountmanagerVan" style="width:200px">
<?=$accountmanagerOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m accountmanager </div>
<div class="formrechts">
<select name="accountmanagerTm" style="width:200px">
<?=$accountmanagerOptions['B']?>
</select>
</div>
</div>
<?


if(checkAccess($type))
	$join = "";		
else 
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";		

$query = "SELECT Clienten.Client, Clienten.Naam FROM Clienten, Portefeuilles ".$join." WHERE Clienten.Client = Portefeuilles.Client AND Portefeuilles.Einddatum  >=  NOW() ORDER BY Client";
$clientOptions = $this->SelectWaarden($query,'Client');

?>
<div class="formblock">
<div class="formlinks"> Van client </div>
<div class="formrechts">
<select name="clientVan" style="width:200px">
<?=$clientOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m client </div>
<div class="formrechts">
<select name="clientTm" style="width:200px">
<?=$clientOptions['B']?>
</select>
</div>
</div>
<?

if(checkAccess($type))
	$join = "";		
else 
	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'";

$query = "SELECT Portefeuille FROM Portefeuilles ".$join." WHERE Portefeuilles.Einddatum  >=  NOW() ORDER BY Portefeuille";
$portfeuilleOptions = $this->SelectWaarden($query,'Portefeuille');
?>
<div class="formblock">
<div class="formlinks"> Van portefeuille </div>
<div class="formrechts">
<select name="portefeuilleVan" style="width:200px">
<?=$portfeuilleOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m portefeuille </div>
<div class="formrechts">
<select name="portefeuilleTm" style="width:200px">
<?=$portfeuilleOptions['B']?>
</select>
</div>
</div>

<?

$query = "SELECT Depotbank FROM Depotbanken ORDER BY Depotbank";
$depotbankOptions = $this->SelectWaarden($query,'Depotbank');
//$depotbankOptions = $depotbankOptions['C'];
?>

<div class="formblock">
<div class="formlinks"> Van depotbank </div>
<div class="formrechts">
<select name="depotbankVan" style="width:200px">
<?=$depotbankOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m depotbank </div>
<div class="formrechts">
<select name="depotbankTm" style="width:200px">
<?=$depotbankOptions['B']?>
</select>
</div>
</div>



<div class="formblock">
<div class="formlinks"> Van datum </div>
<div class="formrechts">
<?php
$jr = substr($this->laatsteDatum,0,4);
$kal = new DHTML_Calendar();
$inp = array ('name' =>"datumVan",'value' =>date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m datum </div>
<div class="formrechts">
<?php
$kal = new DHTML_Calendar();
$inp = array ('name' =>"datumTm",'value' =>date("d-m-Y",db2jul($this->laatsteDatum)),'size'  => "11");
echo $kal->make_input_field("",$inp,"");
?>
</div>
</div>

<!-- Variabele selecties -->
<?php

$query = "SELECT DISTINCT(Risicoklasse) AS Risicoklasse FROM Portefeuilles ".$join." ORDER BY Risicoklasse";
$risicoOptions = $this->SelectWaarden($query,'Risicoklasse');

if($risicoOptions['aantal'] >1)
{
?>
<div class="formblock">
<div class="formlinks"> Van risicoklasse </div>
<div class="formrechts">
<select name="RisicoklasseVan" style="width:200px">
<?=$risicoOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m risicoklasse </div>
<div class="formrechts">
<select name="RisicoklasseTm" style="width:200px">
<?=$risicoOptions['B']?>
</select>
</div>
</div>
<?
}


$query = "SELECT DISTINCT(AFMprofiel) AS AFMprofiel FROM Portefeuilles ".$join." ORDER BY AFMprofiel";
$AFMprofielOptions = $this->SelectWaarden($query,'AFMprofiel');
if($AFMprofielOptions['aantal'] >1)
{
?>
<div class="formblock">
<div class="formlinks"> Van AFM profiel </div>
<div class="formrechts">
<select name="AFMprofielVan" style="width:200px">
<?=$AFMprofielOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m AFM profiel </div>
<div class="formrechts">
<select name="AFMprofielTm" style="width:200px">
<?=$AFMprofielOptions['B']?>
</select>
</div>
</div>
<?
}

$query = "SELECT DISTINCT(SoortOvereenkomst) AS SoortOvereenkomst  FROM Portefeuilles ".$join." ORDER BY SoortOvereenkomst";
$SoortOvereenkomstOptions = $this->SelectWaarden($query,'SoortOvereenkomst');
if($SoortOvereenkomstOptions['aantal'] >1)
{
?>
<div class="formblock">
<div class="formlinks"> Van soort overeenkomst </div>
<div class="formrechts">
<select name="SoortOvereenkomstVan" style="width:200px">
<?=$SoortOvereenkomstOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m soort overeenkomst </div>
<div class="formrechts">
<select name="SoortOvereenkomstTm" style="width:200px">
<?=$SoortOvereenkomstOptions['B']?>
</select>
</div>
</div>
<?
}


$query = "SELECT DISTINCT(Remisier) AS Remisier   FROM Portefeuilles ".$join." ORDER BY Remisier ";
$RemisierOptions = $this->SelectWaarden($query,'Remisier');
if($RemisierOptions['aantal'] >1)
{
?>
<div class="formblock">
<div class="formlinks"> Van remisier </div>
<div class="formrechts">
<select name="RemisierVan" style="width:200px">
<?=$RemisierOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m remisier </div>
<div class="formrechts">
<select name="RemisierTm" style="width:200px">
<?=$RemisierOptions['B']?>
</select>
</div>
</div>
<?
}


if($this->type == 'factuur' || $this->type == 'kwartaalRapportage' )
{
?>
</fieldset>
<fieldset id="Selectie" >
<legend accesskey="O"><u>O</u>pties</legend>
<div class="formblock">
<div class="formlinks"> Factuur nummer </div>
<div class="formrechts">
<?if($this->type == 'kwartaalRapportage'){?><input type="checkbox" name="inclFactuur" value="1"><?}?>
<input type="text" name="factuurnummer" size="4">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Sortering op naam </div>
<div class="formrechts">
<input type="checkbox" name="orderNaam" value="1">
</div>
</div>

<?if($this->type == 'factuur')
{?>
<div class="formblock">
<div class="formlinks"> Lege kolommen verwijderen </div>
<div class="formrechts">
<input type="checkbox" name="nullenOnderdrukken" value="1">
</div>
</div>
<?}?>

<div class="formblock">
<div class="formlinks"> Algemeen drempel percentage </div>
<div class="formrechts">
<input type="text" name="drempelPercentage" size="4">
</div>
</div>  

<div class="formblock">
<div class="formlinks"> Brief toevoegen </div>
<div class="formrechts">
<input type="checkbox" name="inclBrief" value="1">
</div>
</div>
<?  
}
?>
</fieldset>

</td>
<td valign="top">
<?
if($this->type == 'factuur')
{
  ?>
  <input type="button" onclick="javascript:print();" 				value=" Afdrukken " style="width:100px"><br><br>
	<input type="button" onclick="javascript:saveasfile();" 	value=" Opslaan " style="width:100px"><br><br>
	<input type="button" onclick="javascript:csv();" 					value=" CSV-export " style="width:100px"><br><br>
	<input type="button" onclick="javascript:xls();" 					value=" XLS-export " style="width:100px"> 
	<?
}
else 
{
  if($this->type == 'dagRapportage')
    $exportTekst = 'Dag Export';
  elseif($this->type == 'maandRapportage')
    $exportTekst = 'Maand Export';
  elseif ($this->type == 'kwartaalRapportage')
    $exportTekst = 'Kwartaal Export';
  else 
    $exportTekst = 'Export';  
?>
	<input type="button" onclick="javascript:print();" value=" Adrukken " style="width:120px"><br><br>
	<input type="button" onclick="javascript:saveasfile();" value=" Opslaan " style="width:120px"><br><br>
	<input type="button" onclick="javascript:exportData();" value=" <?=$exportTekst?> " style="width:120px"><br><br>
	<input type="checkbox" value="1" id="logoOnderdrukken" name="logoOnderdrukken"> Logo onderdrukken <br><br>
	<input type="checkbox" value="1" id="voorbladWeergeven" name="voorbladWeergeven" checked> Voorblad weergeven <br><br>
	
	
<?
if($this->type =='dagRapportage')
{
$query = "SELECT DISTINCT(Grootboekrekening) AS Grootboekrekening  FROM Grootboekrekeningen  ORDER BY Grootboekrekening ";
$GrootboekOptions = $this->SelectWaarden($query,'Grootboekrekening'); 

?>  
<div id="Modelcontrole" style="visibility: hidden; position:absolute;">

<fieldset id="MUT" >
<legend accesskey="m">M<u>u</u>tatie-voorstel</legend>

<div class="formblock">
<div class="formlinks"> Van grootboek </div>
<div class="formrechts">
<select name="GrootboekVan" style="width:200px">
<?=$GrootboekOptions['A']?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> T/m grootboek </div>
<div class="formrechts">
<select name="GrootboekTm" style="width:200px">
<?=$GrootboekOptions['B']?>
</select>
</div>
</div>

</fieldset>

</div>
<?
}
?>

</td>
</tr>
</table>
 <?  
}
}
  
  function backofficeFormKop()
  {
    ?>
   <form action="<?=$this->script?>" method="POST" name="selectForm">
   <input type="hidden" name="posted" value="true" />
   <input type="hidden" name="save" value="" />
   <input type="hidden" name="exportToFiles" value="" />
   <input type="hidden" name="rapport_types" value="" />   
   <input type="hidden" name="type" value="<?=$this->type?>" />   
   <input type="hidden" name="filetype" value="" /> 
   
   <iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
    <?
  }
  
  function frontOfficeFormKop()
  {
    ?>
    <form action="<?=$this->script?>" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="type" value="<?=$this->type?>" />
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <?
  }
  
  function backofficeFromVoet()
  {
    ?>
   </form>
    <?
  }
  
  
  function SelectWaarden($query,$variabele)
  {
   $this->DB->SQL($query); 
   $this->DB->Query();
   $aantal = $this->DB->records();
   if($aantal >0)
   {
	  $t=0;
	  while($dbData = $this->DB->NextRecord())
	  {
		  $t++;
		 if($t == 1)
			 $selectA = "SELECTED";
		 else 
			 $selectA = "";
			
		 if($t == ($aantal))
			 $selectB = "SELECTED";
		  else 
			 $selectB = "";
			
		  $data['A'] .= "<option value=\"".$dbData[$variabele]."\" ".$selectA.">".$dbData[$variabele]."</option>\n";
		  $data['B'] .= "<option value=\"".$dbData[$variabele]."\" ".$selectB.">".$dbData[$variabele]."</option>\n";
	//	  $data['C'] .= "<option value=\"".$dbData[$variabele]."\" >".$dbData[$variabele]."</option>\n";
	  }
   } 
   $data['aantal']=$aantal;
   return $data; 
  }
}


?>