<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/20 06:25:12 $
    File Versie         : $Revision: 1.1 $

    $Log: millogic_updatedb.php,v $
    Revision 1.1  2017/09/20 06:25:12  cvs
    megaupdate 2722



*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->tableExist("millogic_fondsparameters",true);
$tst->changeField("millogic_fondsparameters","fonds",array("Type"=>" varchar(25)","Null"=>false));
$tst->changeField("millogic_fondsparameters","isShare",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("millogic_fondsparameters","nlFonds",array("Type"=>" tinyint","Null"=>false));

$tst->tableExist("millogic_rekeningen",true);
$tst->changeField("millogic_rekeningen","rekening",array("Type"=>" varchar(25)","Null"=>false));
$tst->changeField("millogic_rekeningen","nietParticulier",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("millogic_rekeningen","rekeningZonderKosten",array("Type"=>" tinyint","Null"=>false));

$tst->tableExist("millogic_transactieMapping",true);
$tst->changeField("millogic_transactieMapping","depotbank",array("Type"=>" varchar(15)","Null"=>false));
$tst->changeField("millogic_transactieMapping","bankcode",array("Type"=>" varchar(15)","Null"=>false));
$tst->changeField("millogic_transactieMapping","Millogic",array("Type"=>" varchar(6)","Null"=>false));
$tst->changeField("millogic_transactieMapping","omschrijving",array("Type"=>" varchar(60)","Null"=>false));

$tst->changeField("TijdelijkeRekeningmutaties","bankTransactieCode",array("Type"=>" varchar(15)","Null"=>false));




