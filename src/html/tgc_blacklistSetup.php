<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/12/04 14:13:34 $
 		File Versie					: $Revision: 1.2 $

 		$Log: tgc_blacklistSetup.php,v $
 		Revision 1.2  2017/12/04 14:13:34  cvs
 		call 6349
 		
 		Revision 1.1  2017/01/04 13:24:11  cvs
 		call 5542, uitrol WWB en TGC
 		
 		Revision 1.1  2016/03/18 14:28:15  cvs
 		call 3691
 		



*/
include_once("wwwvars.php");

$tgc = new AE_cls_toegangsControle();
$tgc->initModule();

$cfg=new AE_config();

if($_POST)
{
  $data=$_POST;

  if ($__appvar["SMSapiKey"] <> "")
  {
    $cfg->addItem('tgc_blacklist_ttl',$data['tgc_blacklist_ttl']);
    $cfg->addItem('tgc_loginMaxTry',$data['tgc_loginMaxTry']);
    header("Location: welcome.php?msg=tgcSettings");
    exit;
  }

}


$_SESSION['NAV']='';
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);


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


<fieldset  style="width: 600px;"> 
  <legend> <?= vt('Toegangscontrole / blacklist'); ?> </legend>

  <div class="formblock">
    <div class="formlinks"><label for="tgc_loginMaxTry" title="tgc_loginMaxTry"><?= vt('login pogingen'); ?></label> </div>
    <div class="formrechts">
      <select name="tgc_loginMaxTry" id="tgc_loginMaxTry">
        <?=vulOptions(array("2"  => vt("2 pogingen"),
                            "3"  => vt("3 pogingen"),
                            "4"  => vt("4 pogingen"),
                            "5"  => vt("5 pogingen"),
                            "6"  => vt("6 pogingen"),
                            "7"  => vt("7 pogingen"),
                            "8"  => vt("8 pogingen")),$cfg->getData('tgc_loginMaxTry'));?>
      </select>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="tgc_blacklist_ttl" title="tgc_blacklist_ttl"><?= vt('blokkeertijd'); ?></label> </div>
    <div class="formrechts">
      <select name="tgc_blacklist_ttl" id="tgc_blacklist_ttl">
        <?=vulOptions(array("1"  => vt("1 uur"),
                            "2"  => vt("2 uur"),
                            "3"  => vt("3 uur"),
                            "4"  => vt("4 uur"),
                            "5"  => vt("5 uur"),
                            "6"  => vt("6 uur"),
                            "7"  => vt("7 uur"),
                            "8"  => vt("8 uur"),
                            "12" => vt("12 uur"),
                            "24" => vt("24 uur")),$cfg->getData('tgc_blacklist_ttl'));?>
      </select>
    </div>
  </div>

</fieldset>
<br/>
<script>

  
  $(document).ready(function(){
    
  });
</script>  


<input type="submit" value="Opslaan">
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
?>