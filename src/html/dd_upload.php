<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/06/08 08:41:33 $
    File Versie         : $Revision: 1.7 $

    $Log: batch_importBestandenSamenvoegen.php,v $
    Revision 1.7  2020/06/08 08:41:33  cvs
    call 8679

    Revision 1.6  2020/02/05 07:53:15  cvs
    call 7995

    Revision 1.5  2020/01/29 10:25:56  cvs
    call 7995

    Revision 1.4  2019/09/03 06:37:05  cvs
    call 7995

    Revision 1.2  2018/09/28 06:59:53  cvs
    call 6734

    Revision 1.1  2018/05/07 14:55:37  cvs
    call 6734

    Revision 1.4  2018/03/28 13:11:49  cvs
    call 3503

    Revision 1.3  2018/03/28 12:36:06  cvs
    call 3503

    Revision 1.2  2018/03/28 12:35:07  cvs
    call 3503

    Revision 1.1  2018/03/09 12:45:08  cvs
    call 3503



*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

include_once "../classes/AE_cls_dd_helper.php";

$dd = new AE_cls_dd_helper(getVermogensbeheerderField("Vermogensbeheerder"));

if ($dd->error())
{
  print_r($dd->errorArray);
  exit;
}

$directory = $dd->path;

$msg = "";
$fmt = new AE_cls_formatter();


$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/dropzone.css?cache='.$random.'"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="javascript/algemeen.js"></script>
<script type="text/javascript" src="javascript/dropzone.js"></script>
  
';

echo template($__appvar["templateContentHeader"],$content);
?>

<style>
  #addDialog{
   background:#eee;
   width: 100%;
  }
  #msgDialog{
   display: none;
   background:#60676a;
   color: white;
   font-size: 16px;
   border-radius: 10px;
   text-align: center;
   padding: 10px;
   width: 95%;
   margin-bottom: 20px;
  }
  .dropzone{
    margin-left: 10px;
    margin-right: 10px;
  }
</style>

  <br/>
  <article id="msgDialog" data-len="<?=strlen($msg)?>"><?=$msg?></article>
  <div>
    <article id="addDialog">
      <p>
        Selecteer bestanden:
      </p>
      <form action="dd_fileUpload.php" class="dropzone" id="inpDropzone"></form>
    </article>
  </div>

  <DIV id="preview-template" style="display: none;">
    <DIV class="dz-preview dz-file-preview">
      <DIV class="dz-details">
        <li><SPAN data-dz-name="" style="display: inline-block"></SPAN> (<SPAN data-dz-size="" style="display: inline-block"></SPAN>)
        <SPAN class="dz-upload" data-dz-uploadprogress=""></SPAN></li>
      <DIV class="dz-error-message"><SPAN data-dz-errormessage=""></SPAN></DIV>
      <div class="dz-success-mark">
      </div>
      <div class="dz-error-mark">
        error
      </div>
      </DIV>
    </DIV>
  </DIV>

  <script>

    $(document).ready(function ()
    {
      var fCount= 0;

      Dropzone.options.inpDropzone = {
      previewTemplate: document.querySelector('#preview-template').innerHTML,
      init: function() {
        this.on("totaluploadprogress", function(totaluploadprogress)
        {
          if (totaluploadprogress.toFixed(2) == "100.00")
          {
            parent.frames['listframe'].location.href = "dd_fileList.php?random=" + Math.floor((Math.random() * 10000) + 1);
          }
        });

        this.on("addedfile", function(file)
        {

        });
      }
    };


    var infoDialog = $('#infoDialog').dialog(
    {
      autoOpen: false,
      height: 200,
      width: '70%',
      modal: true,
      buttons:
        {
          "Sluiten": function()
          {
            $( this ).dialog( "close" );
          }
        },
      close: function ()
        {
        }
    });

    $("#inpDropzone").show();

    if ($("#msgDialog").data("len") > 0)
    {
      $("#msgDialog").show(300);
      setTimeout(function(){ $("#msgDialog").hide(300); },2000)
    }

    });
  </script>
<?
echo "<br/>";
echo template($__appvar["templateRefreshFooter"],$content);
