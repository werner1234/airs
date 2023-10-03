<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/25 09:30:52 $
 		File Versie					: $Revision: 1.4 $

 		$Log: facmod_factuurMaakFactuur.php,v $
 		Revision 1.4  2019/11/25 09:30:52  cvs
 		call 7675
 		
 		Revision 1.3  2019/11/18 09:11:36  cvs
 		call 7675
 		
 		Revision 1.2  2019/11/13 15:13:48  cvs
 		call 7675
 		



*/

include_once("wwwvars.php");
session_start();
//include_once("mfinancieel_auth.php");  // check level van gebruiker geeft $myLevel terug



$_SESSION["NAV"]     = "";
$_SESSION["submenu"] = "";

$cfg = new AE_config();

$data = $_POST;
if ($data["action"] == "process")
{

}
$editcontent["jsincludes"] .= "


  ";
//$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");
//debug($_SESSION["facmod"]);

echo template($__appvar["templateContentHeader"],$editcontent);
$_SESSION["submenu"] = New Submenu();
$_SESSION["submenu"]->addItem($_SESSION["facmod"]["text"],$_SESSION["facmod"]["returnUrl"] );


$DB = new DB();

$DB->SQL("SELECT * FROM CRM_naw WHERE id =".$_GET["deb_id"] );
$nawRec = $DB->lookupRecord();
$kal = new DHTML_Calendar();
$inp = array ('name' =>'invoiceDate','value' =>date("d-m-Y"),'size'  => "11");
echo $kal->load_files();
?>


<style>

  <style>
  .formlinks{
    width: 150px;
  }
  .adres{
    width: 400px;
  }
  .formrechts{

  }
  fieldset{
    width:90%;
    margin-bottom: 15px;
  }

  legend{
    background: #EFEFEF;
    padding: 5px 10px 5px 10px;
    border-radius:4px;
  }
  #submit{
    padding: 10px;
    border-radius:4px;
    width: 300px;

  }
  #klaarTekst{
    font-size: 1.3em;
    color: maroon;

  }
</style>

<form action="facmod_factuurAfdrukkenPDF.php" method="POST" name="editForm" id="editForm" >
<input type="hidden" name="deb_id" value="<?=$_GET["deb_id"]?>">
<br>
&nbsp;&nbsp;Factuur maken bij debiteur :<br>
<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nawRec["naam"]?><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nawRec["adres"]?><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$nawRec["pc"]?>&nbsp;&nbsp;<?=$nawRec["plaats"]?><br></h3>
<br>
<br>

<div class="formblock">
	<div class="formlinks">proeffactuur
	</div>
	<div class="formrechts">
		<select name="conceptInvoice">
		  <option value="true" <?=($cfg->getData("proeffactuur") == 1)?"SELECTED":""?>>Ja</option>
		  <option value="false" <?=($cfg->getData("proeffactuur") <> 1)?"SELECTED":""?>>Nee</option>
		</select>
	</div>
</div>
  <fieldset>
    <legend>factuur via E-mail versturen</legend>

    <div class="formblock">
      <div class="formlinks">E-mailadres
      </div>
      <div class="formrechts">
        <input name="email_factuur" id="email_factuur"  class="adres" placeholder="wordt gemaild als hier een E-mailadres staat" value="<?=$nawRec["email"]?>"/> <button id="wisMail"> wis </button>
      </div>
    </div>
    <div class="formblock">

      </div>
      <div class="formrechts">
        <textarea name="email_template" id="email_template" style="width:800px; height: 300px;"><?=$cfg->getData("factuurtxt")?></textarea>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend>factuuradres</legend>
    <div class="formblock">
      <div class="formlinks">Naam
      </div>
      <div class="formrechts">
        <input name="facNaam" class="adres" value="<?=$nawRec["naam"]?>"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">T.a.v.
      </div>
      <div class="formrechts">
        <input name="facTav" class="adres" value="<?=$nawRec["tav"]?>"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">Straat
      </div>
      <div class="formrechts">
        <input name="facStraat" class="adres" value="<?=$nawRec["verzendAdres"]?>"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">PC & Plaats
      </div>
      <div class="formrechts">
        <input name="facPlaats" class="adres" value="<?=$nawRec["verzendPc"]?> <?=$nawRec["verzendPlaats"]?>" />
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">Land
      </div>
      <div class="formrechts">
        <input name="facLand"  class="adres" value="<?=$nawRec["verzendLand"]?>"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">BTW nr	</div>
      <div class="formrechts">
        <input name="facBTWnr"  class="adres" value="<?=$nawRec["btwnr"]?>"/>
      </div>
    </div>
  </fieldset>

<div class="formblock">
	<div class="formlinks">factuurdatum
	</div>
	<div class="formrechts">
		<?=$kal->make_input_field("",$inp)?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks">&nbsp;
	</div>
	<div class="formrechts">
    <button id="submitFactuur">maak factuur</button>
	</div>
</div>

</form>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<script>
  $(document).ready(function () {
    $("#submitFactuur").click(function (e) {
      console.log("gen factuur");
    });
    $("#wisMail").click(function (e) {
      e.preventDefault();
      $("#email_factuur").val("");
    })
  });
</script>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$editcontent);

?>