<?php
/*
    AE-ICT sourcemodule created 24 jul 2019
    Author              : Chris van Santen
    Filename            : ot_testform.php

*/


include_once("wwwvars.php");
session_start();

echo template($__appvar["templateContentHeader"],$editcontent);

$data = $_REQUEST;

if ($data["posted"] == "true")
{
  if ($_FILES["pdfFile"]["error"] != 0)
  {
    echo "foute invoer;";
  }
  else
  {


    $ot = new AE_cls_OTtransport();

    $ot->addPostData("email_1", $data["email_1"]);
    $ot->addPostData("name_1", $data["name_1"]);
    $ot->addPostData("memo_1", $data["memo_1"]);
    $ot->addPostData("email_2", $data["email_2"]);
    $ot->addPostData("name_2", $data["name_2"]);
    $ot->addPostData("memo_2", $data["memo_2"]);
    $ot->addPostData("clientId", $data["clientId"]);
    if ($ot->sendRequestNew())
    {
      debug($_FILES["pdfFile"]["tmp_name"]);
      $ot->pushPdf($_FILES["pdfFile"]["tmp_name"]);
    debug($data);
    debug($_FILES);
    }

    debug($ot->lastResult, $ot->lastHttpCode);


    exit;
  }
}

?>

<form enctype="multipart/form-data" action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
  <input type="hidden" name="posted" value="true" />
  <br />
  <b>ondertekenen testform</b><br><br>
<?php
if ($data["error"])
{
  echo "<b style=\"color:red;\">".$data["error"]."</b>";
}

?>
  <div class="form">
    <div class="formblock">
      <div class="formlinks"><span id="posBestand">pdf bestand</span> </div>
      <div class="formrechts">
        <input type="file" name="pdfFile" size="50">
      </div>
    </div>
    <fieldset>
      <legend>eerste ondertekenaar</legend>
      <div class="formblock">
        <div class="formlinks">E-mail to </div>
        <div class="formrechts">
          <input type="text" name="email_1" size="50" value="cvsmob@gmail.com"/>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Naam klant </div>
        <div class="formrechts">
          <input type="text" name="name_1" size="50" value="Chris mobiel"/>
          <input type="text" name="clientId" size="5" value="12345"/>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks">Bericht </div>
        <div class="formrechts">
          <textarea name="memo_1" style="width: 40vw; height: 10vh">Graag dit document ondertekenen</textarea>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend>tweede ondertekenaar</legend>
    <div class="formblock">
      <div class="formlinks">E-mail to </div>
      <div class="formrechts">
        <input type="text" name="email_2" size="50" value="cvsmob@gmail.com"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">Naam klant </div>
      <div class="formrechts">
        <input type="text" name="name_2" size="50" value="Chris mobiel"/>

      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">Bericht </div>
      <div class="formrechts">
        <textarea name="memo_2" style="width: 40vw; height: 10vh">Graag dit document ondertekenen</textarea>
      </div>
    </div>
    </fieldset>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <input type="submit" value="versturen..">
      </div>
    </div>
  </div>

</form>

<?

echo template($__appvar["templateRefreshFooter"],$content);