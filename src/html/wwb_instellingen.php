<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/06 15:11:46 $
 		File Versie					: $Revision: 1.2 $

 		$Log: wwb_instellingen.php,v $
 		Revision 1.2  2020/03/06 15:11:46  cvs
 		call 8437
 		
*/
include_once("wwwvars.php");

$cfg=new AE_config();
if($_POST)
{
  $data=$_POST;

  if ($__appvar["SMSapiKey"] <> "")
  {
    $cfg->addItem('wwBeleid_soort',$data['wwBeleid_soort']);
    $cfg->addItem('wwBeleid_livetime',$data['wwBeleid_livetime']);
    $cfg->addItem('wwBeleid_complexiteit',$data['wwBeleid_complexiteit']);
    $cfg->addItem('wwBeleid_2factor',$data['wwBeleid_2factor']);
    $cfg->addItem('wwBeleid_sessieDuur',$data['wwBeleid_sessieDuur']);
    header("Location: welcome.php?msg=wwSettings");
    exit;
  }

}


$_SESSION["NAV"]='';
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);
if (!isset($__appvar["SMSapiKey"]))
{
   echo vt("U kunt deze functie pas gebruiken nadat de koppeling met de SMS module is geconfigureerd");
   exit;
}


?>
<style>
  legend{
    background: #333;
    color: white;
    width:25%;
    padding:3px;
  }
</style>
<form method="POST">

<br/>
<br/>

<?
  if ($__appvar["SMSapiKey"] <> "")
  {  
?>
<fieldset  style="width: 600px;"> 
  <legend> <?=vt('Wachtwoord beleid');?> </legend>
  <div class="formblock">
    <div class="formlinks"><label for="wwBeleid_soort" title="wwBeleid_soort"><?=vt('soort beleid');?></label> </div>
    <div class="formrechts">
      <select name="wwBeleid_soort" id="wwBeleid_soort">
        <?=vulOptions(array(""=>vt("klassiek"),"complex" => vt("complex")),$cfg->getData('wwBeleid_soort'));?>
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks"><label for="wwBeleid_2factor" title="wwBeleid_2factor"><?=vt('2 factor');?></label> </div>
    <div class="formrechts">
      <select name="wwBeleid_2factor" id="wwBeleid_2factor">
        <?=vulOptions(array("uit" => vt("uitschakelen"),
                            "aan" => vt("SMS verificatie"),
                            "tfa" => vt("Google auth.")),$cfg->getData('wwBeleid_2factor'));?>
      </select>
    </div>
  </div>
  <div class="formblock" id="wwbExtras">
    <div class="formblock">
        <div class="formlinks"><label for="wwBeleid_livetime" title="wwBeleid_livetime"><?=vt('geldigheid wachtwoord');?></label> </div>
        <div class="formrechts">
          <select name="wwBeleid_livetime" id="wwBeleid_livetime">
            <?=vulOptions(array(""    => vt("onbeperkt"),
                                "1"   => vt("1 maand"),
                                "2"   => vt("2 maanden"),
                                "3"   => vt("3 maanden"),
                                "4"   => vt("4 maanden"),
                                "5"   => vt("5 maanden"),
                                "6"   => vt("6 maanden"),
                                "7"   => vt("7 maanden"),
                                "8"   => vt("8 maanden"),
                                "9"   => vt("9 maanden"),
                                "10"  => vt("10 maanden"),
                                "11"  => vt("11 maanden"),
                                "12"  => vt("12 maanden")
                               ),$cfg->getData('wwBeleid_livetime'));?>
          </select>
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="wwBeleid_complexiteit" title="wwBeleid_complexiteit"><?=vt('complexiteit');?></label> </div>
          <div class="formrechts">
            <select name="wwBeleid_complexiteit" id="wwBeleid_complexiteit">
              <?=vulOptions(array("complex4"    => vt("minimaal 4 tekens"),
                                  "complex6"    => vt("minimaal 6 tekens"),
                                  "complexer"   => vt("minimaal 6 tekens met hoofdletters en leestekens"),
                                  "complexer10" => vt("minimaal 10 tekens met hoofdletters en leestekens")
                                  ),$cfg->getData('wwBeleid_complexiteit'));?>
            </select>
          </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="wwBeleid_sessieDuur" title="wwBeleid_sessieDuur"><?=vt('sessie duur');?></label> </div>
          <div class="formrechts">
            <select name="wwBeleid_sessieDuur" id="wwBeleid_sessieDuur">
              <?=vulOptions(array("1"  => vt("1 uur"),
                                  "2"  => vt("2 uur"),
                                  "3"  => vt("3 uur"),
                                  "4"  => vt("4 uur"),
                                  "5"  => vt("5 uur"),
                                  "6"  => vt("6 uur"),
                                  "7"  => vt("7 uur"),
                                  "8"  => vt("8 uur"),
                                  "12" => vt("12 uur"),
                                  "24" => vt("24 uur")),$cfg->getData('wwBeleid_sessieDuur'));?>
            </select>
          </div>
      </div>
  </div>
</fieldset>
<br/>
<script>
  function showWwbBlock()
  {
    if ($("#wwBeleid_soort").val() == "")
    {
      $("#wwbExtras").hide(300);
    }
    else
    {
      $("#wwbExtras").show(300);
    }
  }
  
  $(document).ready(function(){
    
    showWwbBlock();
    $("#wwBeleid_soort").change(function(){
      showWwbBlock();
    });
  });
</script>  

<?
  }
?>
<input type="submit" value="<?=vt('Opslaan');?>">
</form>
<?


echo template($__appvar["templateRefreshFooter"],$content);

function vulOptions($srcArray, $value="")
{
  $out = "";
  foreach ($srcArray as $k=>$v)
  {
    $selected = ($k == $value)?"SELECTED":"";
    $out .= "\n\t<option value='$k' $selected>$v</option>";
  }
  return $out;
}
