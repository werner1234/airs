<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/02/18 17:28:26 $
 		File Versie					: $Revision: 1.9 $

 		$Log: AE_cls_navbar.php,v $
 		Revision 1.9  2017/02/18 17:28:26  rvv
 		*** empty log message ***

 		Revision 1.8  2015/01/17 18:29:45  rvv
 		*** empty log message ***

 		Revision 1.7  2011/12/31 18:15:22  rvv
 		*** empty log message ***

 		Revision 1.6  2010/07/25 14:39:21  rvv
 		*** empty log message ***

 		Revision 1.5  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message

 		Revision 1.2  2005/11/09 15:09:56  cvs
 		*** empty log message ***


*/

class NavBar
{
	var $items = array();

	var $currentScript;
	var $currentQueryString;
  var $extraSettings;
	var $target;

	var $returnUrl;

	function NavBar($currentScript,$currentQueryString="",$target='')
	{
		$this->currentScript = $currentScript;
		$this->currentQueryString= $currentQueryString;
		if($target<>'')
			$this->target=$target;
		else
		  $this->target='content';
	}

	function addItem($item)
	{
		// add verschillden item soorten
		$item->currentScript 			= $this->currentScript;
		$item->currentQueryString = $this->currentQueryString;
		$this->items[strtolower(get_class($item))] = $item;
		return true;
	}

	function getQuerystring($page)
	{
		$newString = ereg_replace("&page=([0-9]*)","",$this->currentQueryString);
		return $newString .= "&page=".$page;
	}

}

class NavList
{
	// standaard item gegevens
	var $currentScript;
	var $currentQueryString;

	var $currentPage;
	var $totalRecords;
	var $lastPage;
	var $buttonAdd;
	var $buttonFirst;
	var $buttonPrev;
	var $buttonNext;
	var $buttonLast;


	function NavList($currentPage, $records, $maxRows, $allowAdd=true, $overrideQueryLimit=false)
	{
		if(empty($currentPage))
			$currentPage = 1;
		$this->currentPage	= $currentPage;
		$this->totalRecords	= $records;
		$this->lastPage		  = ceil($records/$maxRows);
    $this->buttonAdd    = $allowAdd;
    $this->buttonFirst  = (($this->totalRecords) && (($this->totalRecords > $this->maxRows) && ($this->currentPage <> 1)));
    $this->buttonPrev   = (($this->totalRecords) && (($this->currentPage >= 2)));
    $this->buttonNext   = (($this->totalRecords) && (($this->totalRecords >= $this->maxRows) && ($this->currentPage <  $this->lastPage)));
    $this->buttonLast   = (($this->totalRecords) && (($this->totalRecords > $this->maxrows)  && ($this->currentPage <> $this->lastPage)));
    $this->overrideQueryLimit = $overrideQueryLimit;
	}

}

class NavEdit// extends NavItem
{
	var $currentScript;
	var $currentQueryString;

	var $buttonSave;
	var $buttonDelete;
	var $form;

	function NavEdit($form, $allowSave=true, $allowDelete=true, $saveStay=false,$message='')
	{
    $this->buttonSave   = $allowSave;
    $this->buttonDelete = $allowDelete;
    $this->buttonSaveStay = $saveStay;
    $this->message      = $message;
    $this->buttonBack	  = true;
    $this->form			    = $form;
	}
}

class NavSearch //extends NavItem
{
	var $currentScript;
	var $currentQueryString;

	//standaard button gegevens
	function NavSearch($selection)
	{
		$this->selection = $selection;
	}
}

class NavHeader// extends NavItem
{
	var $currentScript;
	var $currentQueryString;

	var $kopje;

	function NavHeader($kopje)
	{
		$this->kopje = $kopje;
	}
}

?>