<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/24 11:37:54 $
    File Versie         : $Revision: 1.8 $

    $Log: CRM_naw_ImportFase1.php,v $
    Revision 1.8  2018/10/24 11:37:54  cvs
    call 6713

    Revision 1.7  2018/03/12 10:28:26  cvs
    call 6713

    Revision 1.6  2018/03/07 15:13:16  cvs
    call 6713

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

$referer = end(explode("/",$_SERVER["HTTP_REFERER"]));

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

if ($referer != "index.php") // aanroep vanuit fase2/3
{
  $data['delimiter']   =  $import->getSetting('delimiter');
  $data['dateFormat']  =  $import->getSetting('dateFormat');
  $data['decimalChar'] =  $import->getSetting('decimalChar');
  $data['koppelMethode'] =  $import->getSetting('koppelMethode');
  $data['dateDelimiter'] =  $import->getSetting('dateDelimiter');
}

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
  <div id="setupDialog" title="<?= vt('Profielen beheren'); ?>">
    <div id="tabs-1" style="padding:0px;">
        <br/>
      &nbsp;&nbsp;&nbsp;<b><?= vt('selecteer de profielen die u wilt verwijderen'); ?>:</b>
        <div>
<?
  foreach($import->profileNames as $item)
  {
    if ($item != "default")
    {
      echo "<ol><input type='checkbox' class='profilesSelected' name='$item'/> ".$item." </ol>";
    }

  }
?>
        </div>
      </div>
    </div>
  <div>
    <h1 style="float:left"><?= vt('CRM_naw import'); ?>, <?= vt('stap 1'); ?> </h1><button id="btnSetup" style="float: right" class="fa fa-cog" aria-hidden="true"></button>
  </div>
  <div style="clear: both"/>
  <div id="msg"></div>
  <form enctype="multipart/form-data" method="POST"  action="CRM_naw_ImportFase2.php" name="editForm" id="editForm">
<?
    if ($referer == "index.php")
    {
      echo '<input type="hidden" name="clear" value="1" />';
    }
?>
<!--    <input type="hidden" name="profile" value="--><?//=$profile?><!--" />-->

    <fieldset>
      <legend><?= vt('Data bestand'); ?></legend>
      <div class="formblock">
        <div class="formlinks"><label for="bestand" title="bestand"><?= vt('Importbestand'); ?></label></div>
        <div class="formrechts">
          <input type="file" name="bestand" id="bestand" value="" />
        </div>
      </div>
    </fieldset>

    <br/>
    <br/>
    <fieldset>
      <legend><?= vt('Profiel'); ?></legend>
      <div class="formblock">
        <div class="formlinks"><input type="radio" name="selectProfile" id="selectProfileEx" value="existing" checked><label for="selectProfileEx" > <?= vt('Bestaand profiel'); ?></label></div>
        <div class="formrechts">
          <label for="selectProfileEx" ><?=$import->getInput('profile', "")?></label>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><input type="radio" name="selectProfile" id="selectProfileNw" value="new"><label for="selectProfileNw"> <?= vt('Nieuw profiel'); ?></label></div>
        <div class="formrechts">
          <label for="selectProfileNw"><input name="profileSaveName" id="profileSaveName" placeholder="Profielnaam" /></label>
        </div>
    </fieldset>
    <br/>
    <br/>
    <fieldset>
      <legend><?= vt('CSV delimiter en formatering'); ?></legend>
      <div class="formblock">
        <div class="formlinks"><?= vt('CSV delimiter'); ?></div>
        <div class="formrechts">
          <?=$import->getInput('delimiter', $data['delimiter'])?>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><?= vt('Decimaalteken getallen'); ?></div>
        <div class="formrechts">
          <?=$import->getInput('decimalChar', $data['decimalChar'])?>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><?= vt('Datumformaat'); ?></div>
        <div class="formrechts">
          <?=$import->getInput('dateFormat', $data['dateFormat'])?> <?= vt('delimter'); ?>:
          <?=$import->getInput('dateDelimiter', $data['dateDelimiter'])?><br/>
          <?= vt('Geef het datumformaat als volgt op: D voor dagen, M voor maanden, Y voor jaren,'); ?> <br/><br/>
          <?= vt('dus'); ?>: <b><?= vt('2017-10-23 wordt YYYY-MM-DD'); ?></b>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><?= vt('Koppelmethode'); ?></div>
        <div class="formrechts">
          <?=$import->getInput('koppelMethode', $data['koppelMethode'])?>
        </div>
      </div>
    </fieldset>
    <br/><br/>

    <div>
      <button style="float: right;" id="btnSubmit"><?= vt('volgende'); ?></button>
    </div>

  </form>

</div>
<script>
  $(document).ready(function(){
    var itemsToDelete = "";
    var setupDialog = $('#setupDialog').dialog(
      {
        autoOpen: false,
        height: 500,
        width: '40%',
        modal: true,
        buttons:
          {
            "Sluiten": function()
            {
              $( this ).dialog( "close" );
            },
            "Verwijderen": function()
            {
              itemsToDelete = "";
              $(".profilesSelected").each(function()
              {
                if ($(this).prop( "checked" ))
                {
                  itemsToDelete += $(this).attr("name")+"||";

                }

              });
              console.log(itemsToDelete);
              window.open("CRM_naw_ImportFase1.php?delete="+itemsToDelete,"content");
              $( this ).dialog( "close" );
            }
          },
        close: function ()
        {
        }
      });

<?
    if ($_GET["delete"])
    {
?>
    $("#msg").html("De geselecteerde profielen zijn verwijderd.").show(300);
    setTimeout(function(){ $("#msg").hide(300); $("#msg").html("")}, 3000);
<?
    }
?>

    $("#profileSaveName").focus(function()
    {
      $("#selectProfileNw").prop('checked', true);
    });

    $("#profile").focus(function(){
      $("#selectProfileEx").prop('checked', true);
    });

    $("#profile").change(function(){
      var  val = $(this).val();
      console.log(val);
      $.ajax({
        type: "POST",
        url: "ajax/updateCrmInport.php",
        data: {
          action: val
        },
        dataType:'json',
        success:function(data)
        {
          console.log(data);
          $("#delimiter").val(data.delimiter);
          $("#dateFormat").val(data.dateFormat);
          $("#decimalChar").val(data.decimalChar);
          $("#koppelMethode").val(data.koppelMethode);
          $("#msg").html("profiel <b>"+val+"</b> geladen").show(300);
          setTimeout(function(){ $("#msg").hide(300); $("#msg").html("")}, 3000);
        },
        error:function(data){
          console.log("error");
          console.table(data);
        },

      });

    });
    $("#btnSetup").click(function(e) {
      e.preventDefault();

        setupDialog.dialog('open');

    });

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if ($("#bestand").val() == "")
      {
        $("#msg").html("<?=vt('selecteer het te importeren bestand')?>");
        $("#msg").show(300);
      }
      else if ($("#profileSaveName").val() == "" && $("#selectProfileNw").is(':checked'))
      {
        $("#msg").html("<?=vt('geef een naam voor het nieuwe profiel')?>");
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
