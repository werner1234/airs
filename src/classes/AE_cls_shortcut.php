<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/06/07 09:30:12 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: AE_cls_shortcut.php,v $
 		Revision 1.4  2017/06/07 09:30:12  cvs
 		no message
 		
 		Revision 1.3  2017/06/02 08:45:43  cvs
 		no message
 		
 		Revision 1.2  2017/05/29 07:50:52  cvs
 		no message
 		

 	
*/
class Shortcut
{
  var $shortcutItems;
	

	function Shortcut() 
  {
	  $this->shortcutItems = array();
  }
	
  function addItem($icon, $url, $options = array())
  {
	  $tmp["icon"]    = $icon;
	  $tmp["url"]     = $url;
	  $tmp["options"] = $options;
	  array_push($this->shortcutItems,$tmp);
  }

  function makeButton($name="default", $options=array())
  {
    global $ICONS16;
    global $__appvar, $__icon;

    $iconPath = "icon/shortcut/";

    $size     = ($options["size"]=="")?"16":$options["size"];  // default size = 16
    $disabled = $options["disabled"];                          // grayed version of icon;
    $text     = $options["text"];
    $tooltip  = $options["tooltip"];
    $class    = $options["class"];
    $BTR      = $options["BTR"];

    //listarray($options);
    // bestaat het icon in de standaard tabel, anders is het een bestandsnaam
    if (array_key_exists($name, $__icon))
    {
      $icon  = $__icon[$name][0];
      if ($options["hideText"]    <> true AND $text == "")     $text    = $__icon[$name][1];
      if ($options["hideTooltip"] <> true AND $tooltip == "")  $tooltip = $__icon[$name][2];
    }
    else
    {
      $icon  = $name;
    }


    $button = "<span ".(($tooltip <> "")?"title='{$tooltip}'":"")."><img src='{$iconPath}{$name}.png' class='{$class}' />";
    if ($text <> "")
      $button .= " ".$text;
    return $button."</span>";
  }

  function getHtml()
  {
//    <script src='widget/js/bootstrapTooltip.js'></script>

//    <script src='widget/js/jquery.min.js'></script>
    if (GetModuleAccess('alleenNAW') == 1)
    {
      $crmScript = 'CRM_nawOnlyList.php';
    }
    else
    {
      $crmScript = 'CRM_nawList.php';
    }
    $html = "

    <link href='style/airsShortcut.css' rel='stylesheet' type='text/css' media='screen'>
    
    <div class='airsShortcutContainer'>
    <form action='$crmScript' target='content'  method='get' >
    <input type='hidden' name='page' value='1'/>";
    for($t=0;$t < count($this->shortcutItems); $t++)
    { 
      $extra = "";
      $targetFound = false;
      $classFound  = false;
      $BTR = false;
      if ( is_array($this->shortcutItems[$t]['options']) )
		  {
 			  while (list($key, $value) = each($this->shortcutItems[$t]['options']))
 			  {	
 			    if (strtolower($key) == "target") $targetFound = true;
 			    if (strtolower($key) == "class")  $classFound = true;
          if (strtolower($key) == "btr") {
            $BTR = true;
            $targetFound = true;
            continue;
         }
				  $extra .= " $key=\"$value\" ";
				}
      }
      if ($BTR)              $extra .= " target=\"_PARENT\" ";
			if (!$targetFound)     $extra .= " target=\"content\" ";
      if (!$classFound)      $extra .= " class=\"airsShortcut\" ";
       
      if($this->shortcutItems[$t]["url"])
        $html .= "\n\t".'<a href="'.$this->shortcutItems[$t]["url"].'" '.$extra.' data-toggle="tooltip" >'.$this->makeButton($this->shortcutItems[$t]["icon"]).'</a>&nbsp;&nbsp;';
     	else 
		  	$html .= $this->shortcutItems[$t]["text"];
    	
    }
    $html .= "<div class='airsShortcutInputContainer'>".$this->makeButton("search", array("class"=>"airsShortcutIcon"))."
              <input name='selectie' id='shortcutSelectie' class='airsShortcutInput' data-toggle='tooltip' 
                   title='zoek op portefeuille of relatie'><button>&crarr;</button></form></div></div><br/>";

/*
   $html .='
    <script>
      $(document).ready(function(){
        $("body").tooltip(
        {
          selector: \'[data-toggle="tooltip"]\',
          placement: \'bottom\',
        });
      });
    </script>

    ';
*/
    return $html;
  }
}

?>