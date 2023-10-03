<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:23:28 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20090512_PREinstall.php,v $
 		Revision 1.1  2012/03/09 09:23:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2009/05/10 09:06:04  rvv
 		*** empty log message ***

 		Revision 1.1  2009/04/25 15:17:34  rvv
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

$tst->changeField("TijdelijkeRekeningmutaties","Fondseenheid",array("Type"=>"double","Null"=>false));



?>