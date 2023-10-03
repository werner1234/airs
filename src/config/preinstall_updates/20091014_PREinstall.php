<?php
/*
    AE-ICT source module
    Author                                              : $Author: rvv $
                Laatste aanpassing      : $Date: 2009/10/17 13:27:49 $
                File Versie                                     : $Revision: 1.1 $

                $Log: 20091014_PREinstall.php,v $
                Revision 1.1  2009/10/17 13:27:49  rvv
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
$tst->changeField("Orders","printDate",array("Type"=>"datetime","Null"=>false));
$tst->changeField("OrderRegels","handelsDag",array("Type"=>"datetime","Null"=>false));
$tst->changeField("OrderRegels","handelsTijd",array("Type"=>"time","Null"=>false));
$tst->changeField("OrderRegels","beurs",array("Type"=>"varchar(4)","Null"=>false));
$tst->changeField("OrderRegels","memoHandel",array("Type"=>"varchar(200)","Null"=>false));
$tst->changeField("OrderRegels","valutakoers",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","fondsKoers",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","kosten",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","opgelopenRente",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","brutoBedrag",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","nettoBedrag",array("Type"=>"double","Null"=>false));
$tst->changeField("OrderRegels","definitief",array("Type"=>"tinyint(4)","Null"=>false));


?>
