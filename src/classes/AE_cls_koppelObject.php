<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/01/23 16:44:11 $
 		File Versie					: $Revision: 1.4 $

 		$Log: AE_cls_koppelObject.php,v $
 		Revision 1.4  2013/01/23 16:44:11  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/12/24 16:31:41  rvv
 		*** empty log message ***

 		Revision 1.2  2006/03/09 14:59:07  jwellner
 		*** empty log message ***

 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***

 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message

 		Revision 1.2  2005/11/09 15:09:56  cvs
 		*** empty log message ***
*/

/**
 * Object voor het koppelen van 2 tabellen via een Form.
 *
 */
class Koppel
{
	var $table;            // tabelnaam
	var $name;             // objectnaam
	var $formName;         // formuliernaam
	var $description;      // omschrijving van de actie
	var $action;           // javascript actie na het selecteren van een record
	var $extraQuery;       // extra query zoals: AND werknemer = 'AE'

	var $width  = 400;
	var $height = 350;

	var $field 		= array();  // tabel rij naam
	var $form 		= array();  // formulier veldnaam
	var $display 	= array();  // veld tonen ja/nee
	var $search 	= array();  // zoeken op veld ja/nee

  function Koppel($t,$f="editForm",$join='')
  {
  	$this->table		= $t;
  	$this->join 		= $join;
  	$this->name 		= $t;
		$this->formName = $f;
		$this->description = "selectie maken uit de ".$t." tabel";
  }

  function addFields($ft,$ff,$d,$s)
  {
  	array_push($this->field,$ft);
  	array_push($this->form,$ff);
  	array_push($this->display,$d);
  	array_push($this->search,$s);
  }

  function getJavascript()
  {
		$output .= "function select_".$this->name."(search)\n";
		$output .= "{ \n";
  	$output .= "url = 'koppelSelect.php?koppelObject=".urlencode(serialize($this))."&search='+search;\n";
		$output .= "loadwindow(url,".$this->width.",".$this->height.");\n";
		$output .= "}\n";
		return $output;
  }
}
?>