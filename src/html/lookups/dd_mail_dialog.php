<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:39:25 $
 		File Versie					: $Revision: 1.2 $

 		$Log: dd_mail_dialog.php,v $
 		Revision 1.2  2018/07/24 06:39:25  cvs
 		call 7041
 		
 		Revision 1.1  2016/04/22 10:10:07  cvs
 		call 4296 naar ANO
 		
 		Revision 1.1  2016/03/18 14:27:25  cvs
 		call 3691
 		
 		

*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");

include_once("../../classes/AE_cls_mysql.php");
require("../../config/checkLoggedIn.php");

if ((int)$_POST["id"] == 0)
{
  exit;
}

$db = new DB();
$cfg = new AE_config();
$options = "";
$query = "SELECT route FROM `dd_mailQueue` WHERE id = ".(int)$_POST["id"];

$rec = $db->lookupRecordByQuery($query);

$rows = explode("\n",$rec["route"]);
foreach ($rows as $row)
{
  $items = explode("|", $row);
  if ($items[0] > 0)
  {
    $options .= "\n<option value='".$items[2]."' >".$items[3]."</option>";
  }

}


?>
Koppel voorstel:<br/>
<select id="koppelId">
<?=$options?>
</select>
<br/>
of
<br/>
andere relatie selecteren: <input id="koppelAnders" class="koppelAnders" />
<script>
  var autoCompleteVars =
  {

    source : "lookups/dd_naw.php",
    create : function(event, ui)// onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
    {
      console.log("start lookup");
      oldID = $("#koppelId").val();
    },
    close : function(event, ui)// controle of ID gevuld is anders reset naar onCreate waarden
    {
      if ($("#rel_id").val() == "none")
      {
        alert("geen geldige selectie");
        $("#rel_id").val(oldID);
      }
    },
    search : function(event, ui)// als zoeken gestart het ID veld leegmaken
    {
      $("#rel_id").val("none") // reset koppel pointer
    },
    select : function(event, ui)// bij selectie clientside vars updaten
    {
      $("#retourId").val(ui.item.rel_id);
      
    },
    autoFocus: true,
    minLength : 2, // pas na de tweede letter starten met zoeken
    delay : 0

  };
  $(".koppelAnders").autocomplete(autoCompleteVars);

</script>

