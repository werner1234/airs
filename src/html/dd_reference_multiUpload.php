<?php
/*
    AE-ICT sourcemodule created 18 sep. 2020
    Author              : Chris van Santen
    Filename            : dd_reference_multiUpload.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$msg = "";
$fmt = new AE_cls_formatter();

$data = array_merge($_GET,$_POST);

$db = new DB();
if ( ! $_SESSION['usersession']['cacheKey'] )
{
  $_SESSION['usersession']['cacheKey'] = mt_rand(10000, 99999);
}
$random = $_SESSION['usersession']['cacheKey'];

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
  <h3>Stap 1. selecteer bestanden (max 15)</h3>
  <style>
   #addDialog{
     display: none;
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
    #fileC{
      font-size: 1.5em;
      font-weight: 600;
   }

  </style>
  <div>

    <article id="addDialog">
      <p>
        Selecteer bestanden:
      </p>
      <form action="dd_reference_FileUpload.php" class="dropzone" id="inpDropzone"></form>
    </article>
  </div>
<br/>
  <button id="btnReload"><i class="fa fa-angle-double-right"></i> herladen / leegmaken </button>&nbsp;&nbsp;
  <button id="btnAddForm" class="btnGreen">Uploaden (<span id="prgrss">0</span>)</button>  <span id="fileC" ></span> bestanden<br/><br/>

  <form id="formCombine" action="dd_referenceEditMulti.php" target="_parent">
     <input type="hidden" name="action" value="fase2">
     <input type="hidden" name="rel_id" value="<?=$_GET["rel_id"]?>">
     <input type="hidden" name="batch" value="<?=$_GET["batch"]?>">
  </form>

  <article id="msgDialog" data-len="<?=strlen($msg)?>"><?=$msg?></article>



  <DIV id="preview-template" style="display: none;">

    <DIV class="dz-preview dz-file-preview">

      <DIV class="dz-details">
        <li><SPAN data-dz-name="" style="display: inline-block"></SPAN> (<SPAN data-dz-size="" style="display: inline-block"></SPAN>)
        <SPAN class="dz-upload" data-dz-uploadprogress=""></SPAN></li>
      <DIV class="dz-error-message"><SPAN data-dz-errormessage=""></SPAN></DIV>
      <div class="dz-success-mark">
  ok
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


        Dropzone.prototype.defaultOptions.dictDefaultMessage = "Klik hier of sleep de bestanden hier naartoe";
        var fCount= 0;
        var formFileCount=0;
        $("#fileC").html("0");
        $("#btnAddForm").prop('disabled', true);
        $("#btnAddForm").css('opacity', "0.2");
      console.log("ready file");
        Dropzone.options.inpDropzone = {
          paramName: "file",
            previewTemplate: document.querySelector('#preview-template').innerHTML,
            init: function() {
              console.log("init");
                this.on("totaluploadprogress", function(totaluploadprogress) {
                    document.querySelector("#prgrss").textContent = totaluploadprogress.toFixed(2)+" %";
                    if (totaluploadprogress.toFixed(2) == "100.00")
                    {
                        $("#btnAddForm").css('opacity', "1");
                        $("#btnAddForm").prop('disabled', false);
                    }
                    console.log(totaluploadprogress);
                });

                this.on("addedfile", function(file) {

                  console.log(file);
                    //if (file.status == "succes")
                    {
                      formFileCount++;
                      console.log('<input type="text" name="file_'+formFileCount+'" value="'+file.name+'">');
                      $("#formCombine").append('<input type="hidden" name="file_'+formFileCount+'" value="'+file.name+'">');
                      // $('#formCombine').append('<input type="text" name="file_'+formFileCount+'" value="'+file.name+'">');
                    }
                    // Create the remove button
                    var removeButton = Dropzone.createElement("<button>Remove file</button>");


                    // Capture the Dropzone instance as closure.
                    var _this = this;

                    // Listen to the click event
                    removeButton.addEventListener("click", function(e) {
                        // Make sure the button click doesn't submit the form:
                        e.preventDefault();
                        e.stopPropagation();

                        // Remove the file preview.
                        _this.removeFile(file);
                        // If you want to the delete the file on the server as well,
                        // you can do the AJAX request here.
                    });


                    // Add the button to the file preview element.
                    // file.previewElement.appendChild(removeButton);
                  fCount = this.files.length;
                  $("#fileC").html(fCount);
                });
            }
        };


        var infoDialog = $('#infoDialog').dialog(
      {
        autoOpen: false,
        height: 500,
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
      $("#addDialog").show(300);

      if ($("#msgDialog").data("len") > 0)
      {
        $("#msgDialog").show(300);
        setTimeout(function(){ $("#msgDialog").hide(300); },2000)
      }


      $("#btnAddForm").click(function(e){
        e.preventDefault();
        $("#btnAddForm").css('opacity', "0.4");
        $("#btnAddForm").prop('disabled', true);
        $("#btnAddForm").html("Bezig moment aub");
        $("#formCombine").submit();
      });

      $("#btnReload").click(function(e){
        e.preventDefault();
        window.open("?", "mergeframe");
      });





    });
  </script>
<?
echo "<br/> ";
echo template($__appvar["templateRefreshFooter"],$content);
