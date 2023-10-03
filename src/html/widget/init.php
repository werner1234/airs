<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/12/11 10:52:53 $
    File Versie         : $Revision: 1.4 $

    $Log: init.php,v $
    Revision 1.4  2018/12/11 10:52:53  cvs
    update 2862

    Revision 1.3  2018/08/18 12:40:15  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/10/20 11:41:38  cvs
    no message

    Revision 1.1  2016/12/22 09:39:34  cvs
    call 4830 eerste commit



*/
if (version_compare(phpversion(), '5.3.0', '<'))
    include_once("AE_lib2.php3");
  include_once("../../config/local_vars.php");
  include_once("../../config/vars.php");
  include_once("../../config/auth.php");
  include_once("../../classes/AE_cls_WidgetsFilter.php");
  include_once("../../classes/AE_cls_WidgetsCaching.php");

  $tmpl = new AE_template();
  $tmpl->loadTemplateFromFile("kop.html","kop");
  $tmpl->loadTemplateFromFile("kopZonderSetup.html","kopZonder");
  $tmpl->loadTemplateFromFile("voet.html","voet");
?>
<style>
  <?=$tmpl->parseBlockFromFile("widget.css");?>
</style>
