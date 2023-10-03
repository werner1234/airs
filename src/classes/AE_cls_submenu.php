<?php
/* 	
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/05/29 07:51:07 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: AE_cls_submenu.php,v $
 		Revision 1.2  2017/05/29 07:51:07  cvs
 		no message
 		
 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast
 		
 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2005/11/17 08:05:52  cvs
 		*** empty log message ***
 		
 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message
 		
 		Revision 1.1.1.1  2005/10/31 08:20:34  jwellner
 		no message
 		
 		Revision 1.2  2005/08/18 10:08:01  jwellner
 		no message
 		
 		Revision 1.4  2005/07/28 15:12:15  jwellner
 		no message
 		
 		Revision 1.3  2005/04/27 08:55:04  jwellner
 		no message
 		
 		Revision 1.2  2005/03/24 15:12:45  jwellner
 		no message
 		
*/



class Submenu 
{
  var $menuItems;
	
  function Submenu() 
  {
	 $this->menuItems = array();
  }
	
  function addItem($text, $url, $options = array())
  {
	 $tmp[text]    = $text;
	 $tmp[url]     = $url;
	 $tmp[options] = $options;
	 array_push($this->menuItems,$tmp);
  }
	
  function getHtml()
  {
    for($t=0;$t < count($this->menuItems); $t++)
    { 
      $extra = "";
      $targetFound = false;
      if ( is_array($this->menuItems[$t]['options']) )
		  {
 			  while (list($key, $value) = each($this->menuItems[$t]['options']))
 			  {	
 			    if (strtolower($key) == "target") $targetFound = true;
				  $extra .= " $key=\"$value\" ";
				}
			}
			if (!$targetFound)
			  $extra .= " target=\"content\" ";
      
       
      if($this->menuItems[$t][url])
        $html .= '<a  href="'.$this->menuItems[$t][url].'" '.$extra.' ><span class="submenItem">'.$this->menuItems[$t][text].'</span></a>';
     	else 
		  	$html .= $this->menuItems[$t][text];
    	
    }
    return $html;
  }
}
?>