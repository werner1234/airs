<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2009/03/31 09:04:07 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20090331_PREinstall.php,v $
 		Revision 1.1  2009/03/31 09:04:07  cvs
 		RaboCode toegevoegd
 		
 		Revision 1.1  2009/01/24 15:53:39  rvv
 		*** empty log message ***

 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***

 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM

 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***

 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***

 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***




*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");



$tst = new SQLman();

$tst->changeField("Fondsen","raboCode",array("Type"=>"varchar(35)","Null"=>false));





?>