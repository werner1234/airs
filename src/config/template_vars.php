<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/04/07 14:16:32 $
 		File Versie					: $Revision: 1.50 $
*/
// hier worden de templates gezet ..
if(strstr($_SERVER['HTTP_USER_AGENT'],"Firefox/2"))
  $content['doctype']="<!doctype html>\n";

$page=basename($_SERVER['SCRIPT_FILENAME'], ".php");

$template_content = array(
'title'               => $PRG_NAME,
'versie'              => $PRG_VERSION." (".$PRG_RELEASE.")",
'database'			      => $_DB_resources[1]['db'],
'style'               => '<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">',
'meta'                => '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">',
'onload'              => 'placeFocus();',
'onunload'            => '',
'javascript'          => '',
'body'                => '',
'initial_content'     => 'welcome.php',
'initial_submenu'     => 'submenu.php',
'initial_navigatie'   => 'navigatie.php',
'initial_logo'        => 'logo.php',
'submenu'				      => '<a href="http://www.aeict.nl" target="main">test</a>',
'menu'                => 'mainmenu.php',
'navigatie'           => $_navigatie,
'navbar'              => $_nav,
'path'                => $__appvar["baseurl"]."/");
$editcontent = array();
$editcontent['title'] = "Muteren gegevens";

//todo: call 4830 dit kan na de ontwikkelcyclus weg
if ($__develop)
{
  $template_content['initial_content'] = 'welcome.php';
}




$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>";
$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";


$editcontentNieuw['style'] = '<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';
$editcontentNieuw['style'] .= '<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">';


$loadnewContentForPage = basename($_SERVER['PHP_SELF']);
if ( strpos($loadnewContentForPage, 'List') !== false ){

  $loadnewContentForPage = 'List.php';
}
switch ($loadnewContentForPage)
{
  case 'List.php':
  case 'ordersEditV2.php':
  case 'ordersEditBulkV2.php':
  case 'tijdelijkebulkordersv2Verwerken.php':
  case 'bulkordersv2verwerken.php':
  case 'ordersStatusEditV2.php':
  case 'rapportFrontofficeHtmlRapport.php':
  case 'risicoklassenEdit.php':
  case 'vermogensbeheerderEdit.php':
  case 'controlerapportEmt.php':
  case 'rekeningenEdit.php':
  case 'rapportFrontofficeClientSelectie.php':
  case 'doorkijk_categoriePerVermogensbeheerderEdit.php':
  case 'portefeuillesEdit.php':
  case 'beleggingscategorieperfondsEdit.php':
  case 'fondsEdit.php':
  case 'rapportFrontofficeFondsSelectie.php':
  case 'rapportFrontofficeConsolidatieSelectie.php':
  case 'rapportEmtSelectie.php':
  case 'rapportFrontofficeManagementSelectie.php':
  case 'rapportFrontofficeOptieTools.php':
  case 'rekeningmutaties_v2_Edit.php':
  case 'customTemplateEdit.php':
  case 'orderuitvoeringEditV2.php':
  if ( ! $_SESSION['usersession']['cacheKey'] )
  {
    $_SESSION['usersession']['cacheKey'] = mt_rand(100, 10000);
  }
  $random = $_SESSION['usersession']['cacheKey'];
//    $editcontent['jsincludes'] .= '<script type="text/javascript" src="javascript/jquery-1.11.1.min.js"></script>';
    $editcontent['jsincludes'] .= '<script type="text/javascript" src="javascript/jquery/jquery-3.4.1.min.js"></script>';
    $editcontent['jsincludes'] .= '<script type="text/javascript" src="javascript/jquery/jquery-ui.min.js"></script>';

    $template_content['style'] .= $editcontent['style'] = '<link href="style/aeStyle.css?cache=' . $random . '" rel="stylesheet" type="text/css" media="screen">';
    $editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-colorpicker.min.js\"></script>";
    $editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-4.3.1/util.js\"></script>";
    $editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-4.3.1/bootstrap.min.js\"></script>";
    $editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-4.3.1/popper.min.js\"></script>";
    $editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/bootstrap-4.3.1/tooltip.js\"></script>";

  break;
}

//uitstapje voor crm rapporten opmaak.
if ( basename($_SERVER['PHP_SELF']) === 'CRM_nawEdit.php') {
  $editcontent['style'] = '<link href="style/aeStyle.css?cache=' . $random . '" rel="stylesheet" type="text/css" media="screen">';
}

$editcontent['style'] .= '<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">';

$editcontent['style'] .= '<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">';

/** jquery ui style **/
$editcontent['style'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';


$editcontent['javascript'] = "

// Keys afvangen
var ie4 = (document.all)? true:false;
var ns4 = !ie4;

var keySet = false;
var ctrlKey = false;
var fromChanged = false;

 if (typeof window.event == 'undefined')
 {
   document.onkeypress = function(e)
   {
    fromChanged = true;
   	var test_var=e.target.nodeName.toUpperCase();
 	  if (e.target.type) var test_type=e.target.type.toUpperCase();
 	  if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA'){
 	    return e.keyCode;
 	 }else if (e.keyCode == 8){
 	   e.preventDefault();
  	}
   }
 }
 else
 {
   document.onkeydown = function()
   {
     fromChanged = true;
     if (window.updateScript)
     {
       updateScript();
     }
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

 	   var test_var=event.srcElement.tagName.toUpperCase();
     if (event.srcElement.type)
 	   {
 	     var test_type=event.srcElement.type.toUpperCase();
 	   }
 	   if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA')
 	   {
 	     return event.keyCode;
 	   }
 	   else if (event.keyCode == 8)
 	   {
 	     event.returnValue=false;
 	   }
   }
 }

function keyDown(e)
{
  fromChanged = true;
  if (window.updateScript)
  {
    updateScript();
  }
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
}

function command(nKey)
{
	// normale Keys
  if(nKey==121)
  {
    submitForm();
  }
}

function submitForm()
{
	//check values ?
	document.editForm.submit();
}


parent.document.title = '$page';

";


$content['javascript']="parent.document.title = '$page';\n";


/** updated jquery **/


$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery.kiketable.colsizable-1.1.js\"></script>";
$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery.event.drag-1.5.min.js\"></script>";
$editcontent['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/sack/tw-sack.js\"></script>";

$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";


$content['jsincludes'] .=$editcontent['jsincludes'];

$content['javascript'] .= "
  $(function () {
    $
  
  })
";



/*
$content['javascript'].='

$(document).ready(function(){
		jQuery.fn.fixClearType = function(){
		   return this.each(function(){
			   if(typeof this.style.filter  && this.style.removeAttribute)
				   this.style.removeAttribute("filter");
		   })
		}

	$(".list_tabel")
		.eq(0)
			.kiketable_colsizable({
				dragMove : false,
				saveChange : true,
				dragCells : "tr:first>*:not(:first)"


				})
			.end();
	});';
*/


?>