<?php
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/12/16 14:43:09 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: AE_cls_mainmenu.php,v $
 		Revision 1.3  2005/12/16 14:43:09  jwellner
 		classes aangepast
 		
 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message
 		
 		Revision 1.2  2005/11/09 15:09:56  cvs
 		*** empty log message ***
 		
 	
*/

class mainMenu
{
  var $menuarray;
  var $submenus;
  var $menuwidth;
  var $target;
  var $std_output_template;
  
  function mainMenu() 
  {
    $this->menuarray                  = Array();
    $this->menuwidth                  = 200;
    $this->target                     = "target=content";
    $this->std_output_template['kop'] = ' 
_menuCloseDelay=80;
_menuOpenDelay=150;
_scrollAmount=3;
_scrollDelay=20;
_followSpeed=5;
_followRate=40;
_subOffsetTop=4;
_subOffsetLeft=-5;

with(style1=new mm_style())
{
  offcolor="black";
  offbgcolor="#dddddd";
  oncolor="black";
  onbgcolor="#bbbbbb";
  bordercolor="#000000";
  borderstyle="solid";
  bordercolor="Gray";
  fontsize=11;
  fontstyle="normal";
  fontweight="normal";
  fontfamily="Verdana, Arial";
  padding=5;
  subimagepadding=2;
  high3dcolor="#eeeeee";
  low3dcolor="#222222";
  pagecolor="black";
  pagebgcolor="#7cabab";
  headercolor="#ffffff";
  headerbgcolor="#000099";
  separatorcolor="Silver";
}
';
    $this->std_output_template['mainmenu'] = '
with(milonic=new menuname("mainmenu"))
{
  top=0;
  itemheight=12;
  style=style1;
  style.subimage="";
  alwaysvisible=1;
  alignment="left";
  orientation="horizontal";
';

    $this->std_output_template['endtag'] = '
}';

    $this->std_output_template['submenu'] = '
with(milonic=new menuname("{menu}"))
{
  itemwidth='.$this->menuwidth.';
  borderwidth=1;
  style=style1;
  style.subimage="images/arrow.gif";
  alignment="left";';

    $this->std_output_template['einde'] = '
drawMenus(); ';
    
    
  }

  function addItem($menu="", $txt, $action,$sep="", $secu="")
  {
    if ($menu == "") $menu = "main";
    $tmp[menu]   = $menu;
    $tmp[txt]    = $txt;
    $tmp[action] = $action;
    $tmp[sep]    = $sep;
    $tmp[secu]   = $secu;
    array_push($this->menuarray,$tmp);
  }
  
  function createMenu($destination="")
  {
   
    Switch ($destination)
    {
      case "pda":
        break;
			case "dynarch":
				return $this->makeDynarch();
			  break;
      default:
        return $this->makeStdouput();  
    }
  }
  
  function makeStdouput()
  {
   
    $subarray = Array();
    $subarray[main] = $this->std_output_template['mainmenu'];
    for($t=0;$t < count($this->menuarray); $t++)
    {
      $index = $this->menuarray[$t][menu];
      if (!Array_key_Exists($index,$subarray) )
        $subarray[$index]  = str_replace("{menu}",$index,$this->std_output_template['submenu'])."\n";

      if (stristr($this->menuarray[$t][action],"submenu="))
      {
        $_bla = explode("=",$this->menuarray[$t][action]);
        $_act = "showmenu=".$_bla[1];
      }
      else 
        $_act = "url=".$this->menuarray[$t][action];
      $_sep = ";".$this->target.";";    
      if ($this->menuarray[$t][sep] <> "")
        $_sep .= "separatorsize=1";
      
      $subarray[$index] .= "  aI(\"text=".$this->menuarray[$t][txt].";".$_act.";".$_sep."\")\n";
    }
    
    $result = $this->std_output_template['kop']."\n";
    reset($subarray);
    while (list($key, $val) = each($subarray)) 
    {
      $result .= $subarray[$key].$this->std_output_template['endtag']."\n";
    }
    $result .=$this->std_output_template['einde'];
    return $result;
  }
  
  function makeDynarch()
  {
		for($t=0;$t < count($this->menuarray); $t++) 
		{
			$menu[$this->menuarray[$t][menu]][] = $this->menuarray[$t];
    }
    
    $main = $menu["main"];
		$result = "<ul>\n";
		
    for($a = 0; $a < count($main); $a++)
    {
			if (stristr($main[$a][action],"submenu="))
      {
      	// print menu
				$result .= "<li>\n"; 
				$result .= $main[$a][txt]."\n";
        $result .= $this->makeDynarchSubmenu($menu[substr($main[$a][action],8)]);
        $result .= "</li>\n"; 
      }
      else
      {
				$result .= "<li>\n";
				$result .= "\t<a href=\"".$submenu[$a][action]."\" target=\"content\">".$submenu[$a][txt]."</a>";
				$result .= "</li>\n";
      }
    }
    
		$result .= "</ul>\n";
    
    return $result;
  }
  
  function makeDynarchSubmenu($submenu) 
  {
		$result = "<ul>\n";
		for($a = 0; $a < count($submenu); $a++)
		{
			if (stristr($submenu[$a][action],"submenu="))
      {
      	// print menu
				$result .= "<li>\n"; 
				$result .= $submenu[$a][txt]."\n";
        $result .= $this->makeDynarchSubmenu($submenu[substr($submenu[$a][action],8)]);
        $result .= "</li>\n"; 
      }
      else 
      {
				$result .= "<li>";
				$result .= "\t<a href=\"".$submenu[$a][action]."\" target=\"content\">".$submenu[$a][txt]."</a>";
				$result .= "</li>\n";
      }
		}
		
		$result .= "</ul>\n";
		
  	return $result;
  }
}  
?>