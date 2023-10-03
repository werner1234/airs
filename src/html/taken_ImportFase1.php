<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/19 11:45:16 $
    File Versie         : $Revision: 1.2 $

    $Log: taken_ImportFase1.php,v $
    Revision 1.2  2018/03/19 11:45:16  cvs
    call 6440

    Revision 1.1  2018/03/06 14:32:02  cvs
    call 6440

    Revision 1.5  2017/11/17 14:09:03  cvs
    call 6145 bevindingen

    Revision 1.4  2017/11/17 11:00:51  cvs
    call 6145

    Revision 1.3  2017/11/17 08:03:57  cvs
    call 6145

    Revision 1.2  2017/11/13 13:31:21  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:31:26  cvs
    call 6145



*/

include_once("wwwvars.php");
include_once "../classes/AIRS_cls_CRM_naw_importHelper.php";

$_SESSION["NAV"]='';
$tmpl = new AE_template();
$tmpl->appendSubdirToTemplatePath("crmImport");

$msg = "Bestandsindeling en formatering";

$profile = ($_GET["profile"] != "")?$_GET["profile"]:"default";
$import = new CRM_naw_importHelper($profile);

if ($_GET["delete"])
{

  $items = explode("||", $_GET["delete"]);
  array_pop ( $items );  // laatste lege item weggooien
  $profileNames = $import->profileNames;
  $import->profileNames = array();

  foreach($profileNames as $profile)
  {
    if (!in_array($profile, $items))
    {
      $import->profileNames[] = $profile;
    }
  }
  array_unshift($import->profileNames, "default");
  $import->saveProfileNames();

}


$data['delimiter']   =  $import->getSetting('delimiter');
$data['dateFormat']  =  $import->getSetting('dateFormat');
$data['decimalChar'] =  $import->getSetting('decimalChar');
$data['koppelMethode'] =  $import->getSetting('koppelMethode');

unset($_SESSION["crmImportPOST"]);
unset($_SESSION["crmImportProfile"]);

echo template($__appvar["templateContentHeader"],$content);
?>
<link rel="stylesheet" href="widget/css/font-awesome.min.css">
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">


<style>
  <?=$tmpl->parseBlockFromFile("crmImport.css");?>
ol{
  -webkit-margin-before: .3em;
  -webkit-margin-after: .1em;
}

</style>

<div class="container">

  <div>
    <h1 style="float:left">Taken import, stap 1 </h1>
  </div>
  <div style="clear: both"/>
  <div id="msg"></div>
  <form enctype="multipart/form-data" method="POST"  action="taken_ImportFase2.php" name="editForm" id="editForm">
<!--    <input type="hidden" name="profile" value="--><?//=$profile?><!--" />-->

    <fieldset>
      <legend>Excel bestand (.xlsx)</legend>
      <div class="formblock">
        <div class="formlinks"><label for="bestand" title="bestand">import bestand</label></div>
        <div class="formrechts">
          <input type="file" name="bestand" id="bestand" value="" style="width: 500px" /><br/><br/>
          (maximaal 500 regels per bestand)

        </div>
      </div>
    </fieldset>


    <div >

      <button style="float: right;" id="btnSubmit">volgende</button>
    </div>

  </form>

</div>
<script>
  $(document).ready(function(){

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if ($("#bestand").val() == "")
      {
        $("#msg").html("selecteer het te importeren bestand");
        $("#msg").show(300);
      }
      else
      {
        $("#editForm").submit();
      }
      setTimeout(function(){ $("#msg").hide(300); }, 3000);

    });
  });
</script>

<?

echo template($__appvar["templateRefreshFooter"],$content);
