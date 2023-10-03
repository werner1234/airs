<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/04/08 12:32:31 $
 		File Versie					: $Revision: 1.8 $

 		$Log: wwb_wachtwoordWijzigen.php,v $
 		Revision 1.8  2019/04/08 12:32:31  cvs
 		call 7670
 		
 		Revision 1.7  2017/09/27 11:31:42  cvs
 		call 5932
 		
 		Revision 1.6  2017/04/12 11:30:48  cvs
 		no message
 		
 		Revision 1.5  2016/05/27 12:41:32  cvs
 		no message
 		
 		Revision 1.4  2016/04/29 12:36:35  cvs
 		reload afhankelijk maken
 		
 		Revision 1.3  2016/03/18 14:28:15  cvs
 		call 3691
 		
 		Revision 1.2  2016/03/16 09:28:52  cvs
 		opgeschoond
 		
 		Revision 1.1  2016/03/16 09:22:22  cvs
 		Eerste commit
 		

*/

include_once("wwwvars.php");
include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';
$sec = new AE_cls_secruity($USR);
$_SESSION["NAV"]='';

if($_POST["action"] == "go")
{
  // Komt de request vanaf het wwb form?
  $refer = basename($_SERVER["HTTP_REFERER"]);
  if ($refer != "wwb_wachtwoordWijzigen.php")
  {
    $_SESSION["wwb_WWchange"] = false;
    echo "<script>window.top.location.reload();</script>";
    exit;
  }

  // Voldoet de lengte aan de instellingen
  $complexHeid = $sec->wwComplexiteit;
  $lengteWW = 4;
  if (substr($complexHeid,0,4) == "comp")
  {
    switch($complexHeid)
    {
      case "complex6":
      case "complexer":
        $lengteWW = 6;
        break;
      case "complexer10":
        $lengteWW = 10;
        break;
      default:
    }
  }
  if (strlen($_POST["nieuwWW"]) < $lengteWW)
  {
    $_SESSION["wwb_WWchange"] = false;
    echo "<script>window.top.location.reload();</script>";
    exit;
  }
  else
  {
    $db = new DB();
    $query = "
    UPDATE 
      `GebruikersLogin` 
    SET
        change_user = '".$_POST["gebruiker"]."'
      , change_date = NOW()
      , huidigWW = '".$sec->pwHash($_POST["nieuwWW"])."'
      , geldigTot = '".$sec->newGeldigDate()."'  
    WHERE
      `userNaam` = '".$_POST["gebruiker"]."'
          
    ";
    $db->executeQuery($query);
    $mesg = "uw wachtwoord is opgeslagen";
  }

  if ($_SESSION["wwb_WWchange"])
  {
    $_SESSION["wwb_WWchange"] = false;
    echo "<script>window.top.location.reload();</script>";
  }
  else
  {
    header("location: welcome.php?msg=wwSave");
  }

   exit();
}

$content["pageHeader"] = "<br><div class='edit_actionTxt'>

  <b>$mainHeader</b> $subHeader
</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);

?>
<style>
  legend{
    background: #333;
    color: white;
    width:80%;
    padding:3px;
  }
  .msg{
    width: 600px;
    padding: 10px;
    border-radius:5px;
    border: 1px solid #999;
    margin-bottom: 25px;
    box-shadow: 2px 2px 5px 2px #808080;
    background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #B0E0E6), color-stop(1, #F5F5F5));
    background-image: -o-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: -moz-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: -webkit-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: linear-gradient(to bottom, #B0E0E6, #F5F5F5);
    font-weight: bold;
  }
  #wwDialog{
    padding: 20px;
    width: 600px;

    margin-bottom: 25px;
  }
  .complex{
    width: 600px;
    padding: 10px;
    border-radius:5px;
    border: 1px solid #999;
    margin-bottom: 25px;
    box-shadow: 2px 2px 5px 2px #808080;
    background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #CCC), color-stop(1, #F5F5F5));
    background-image: -o-linear-gradient(top, #CCC, #F5F5F5);
    background-image: -moz-linear-gradient(top, #CCC, #F5F5F5);
    background-image: -webkit-linear-gradient(top, #CCC, #F5F5F5);
    background-image: linear-gradient(to bottom, #CCC, #F5F5F5);
  
  }
  #feedback{
  position: absolute;
  left: 250px;
  top:10px;
  display: none;
  font-size: 1.2em;
  color: white;
  margin:auto;
  padding:15px;
  border-radius:5px;
  text-align: left;
  box-shadow: 2px 2px 2px 0px #333

}
 .fbRood{
  background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #CC0000), color-stop(1, #FF9999));
  background-image: -ms-linear-gradient(top, #CC0000, #FF9999);
  background-image: -o-linear-gradient(top, #CC0000, #FF9999);
  background-image: -moz-linear-gradient(top, #CC0000, #FF9999);
  background-image: -webkit-linear-gradient(top, #CC0000, #FF9999);
  background-image: linear-gradient(to bottom, #CC0000, #FF9999)
}
.fbGroen{
  background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #339900), color-stop(1, #33FF66));
  background-image: -ms-linear-gradient(top, #339900, #33FF66);
  background-image: -o-linear-gradient(top, #339900, #33FF66);
  background-image: -moz-linear-gradient(top, #339900, #33FF66);
  background-image: -webkit-linear-gradient(top, #339900, #33FF66);
  background-image: linear-gradient(to bottom, #339900, #33FF66)
}
.grayed{
  color: #BBB !important;
}
</style>
<?

$wwTot = "Uw nieuwe wachtwoord is geldig tot ".dbdate2form($sec->newGeldigDate());
$mesg = "Wijzig uw wachtwoord en klik opslaan<br/>$wwTot";

/*
if ($sec->beleid == "")
{

?>

<div id="wwDialog">
  <b>Uw beheerder voert het wachtwoord beleid, <br/>neem met hem/haar contact op om uw wachtwoord te wijzigen.<br/>
</div>
<br/>
<?
  exit;
}
*/
if ($_SESSION["wwb_WWchange"])
{
?>

<div id="wwDialog">
  <b>Het systeem heeft geconstateerd dat uw wachtwoord opnieuw ingesteld dient te worden.<br/>
  Redenen hiervoor kunnen zijn:</b><br/><br/>
  <li>uw account is gereset door uw beheerder</li>
  <li>de geldigheidstermijn van uw wachtwoord is verlopen</li>
  <li>u logt de eerste maal in met het systeem wachtwoord </li>
</div>
<br/>
<?
}

$complexHeid = $sec->wwComplexiteit;
?>

<div class="msg">
  <?=$mesg?>
</div>
<form method="POST">
  <input type="hidden" name="action"    value="go" />
  <input type="hidden" name="gebruiker" value="<?=$USR?>" />
  

<fieldset  style="width: 600px;">       
  <legend> Wachtwoord wijzigen voor <?=$USR?></legend>
  <div class="formblock">
    <div class="formlinks"><label for="nieuwWW" >nieuw wachtwoord</label> </div>
    <div class="formrechts">
      <input type="text" name="nieuwWW" id="nieuwWW" autocomplete="off" placeholder="" />&nbsp;&nbsp;&nbsp;<div id="feedback" class=""></div>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks"><label for="nieuwWW" >herhaal wachtwoord</label> </div>
    <div class="formrechts">
      <input type="text" name="nieuwWW2" id="nieuwWW2" autocomplete="off" placeholder="" />&nbsp;&nbsp;&nbsp;<div id="feedback" class=""></div>
    </div>
  </div>
</fieldset>
<br/>

<input type="submit" id="btnSubmit" value="Opslaan">
</form>

<script>
  
  $(document).ready(function()
  {
    var herhaalGelijk = false;
    var wwLengte = false;
    var wwComplexheid = false;
    var wwTestpasswd = true;
<?
    if (substr($complexHeid,0,4) == "comp")
    {
      switch($complexHeid)
      {
        case "complex6":
        case "complexer":
          $lengteWW = 6;
          break;
        case "complexer10":
          $lengteWW = 10;
          break;
        default:
          $lengteWW = 4;

      }

    }
?>

    //$('input[type="submit"]').attr('disabled' , false).attr("class","");


    function feedback()
    {
      wwLengte = ($("#nieuwWW").val().length >= <?=$lengteWW?>);
<?
      if ($complexHeid == "complexer" OR $complexHeid == "complexer10")
      {
?>
      var bevatLetters = false;
      var bevatHoofdLetters = false;
      var bevatCijfers = false;
      var bevatLeestekens = false;
      var ww = $("#nieuwWW").val();



      for (var i = 0; i < ww.length; i++)
      {
        var ch = ww.charAt(i);
        if (ch.match(/[a-z]/))
        {
          bevatLetters = true
        } else if (ch.match(/[A-Z]/))
        {
          bevatHoofdLetters = true
        } else if (ch.match(/\d/))
        {
          bevatCijfers = true;
        } else
        {
          bevatLeestekens = true;
        }
      }

      var htmlBlock = "<li>lengte " + ((wwLengte) ? "&#10004;" : " ") + "</li>" +
        "<li>letters " + ((bevatLetters) ? "&#10004;" : " ") + "</li>" +
        "<li>hoofdletters " + ((bevatHoofdLetters) ? "&#10004;" : " ") + "</li>" +
        "<li>cijfers " + ((bevatCijfers) ? "&#10004;" : " ") + "</li>" +
        "<li>leestekens " + ((bevatLeestekens) ? "&#10004;" : " ") + "</li>" +
        "<li>herhaal gelijk " + ((herhaalGelijk) ? "&#10004;" : " ") + "</li>" +
        "<li><> vorig ww " + ((wwTestpasswd) ? "&#10004;" : " ") + "</li>";

      if (wwLengte && bevatLetters && bevatHoofdLetters && bevatCijfers && bevatLeestekens && wwTestpasswd && herhaalGelijk)
      {
        $("#feedback").removeClass("fbRood").addClass("fbGroen");
        $('input[type="submit"]').attr('disabled', false).attr("class", "");
        //$('input[type="submit"]').attr("class","");
        $("#btnSubmit").attr("title", "Klik om uw nieuwe wachtwoord op te slaan");

      } else
      {
        $("#feedback").removeClass("fbGroen").addClass("fbRood");
        $('input[type="submit"]').attr('disabled', true).attr("class", "grayed");
        $("#btnSubmit").attr("title", "U kunt pas opslaan als uw nieuwe wachtwoord voldoet");
      }
      $("#feedback").html(htmlBlock);

      <?
      }
      else
      {
      ?>

      var htmlBlock = "<li>lengte " + ((wwLengte) ? "&#10004;" : " ") + "</li>" +
        "<li>herhaal gelijk " + ((herhaalGelijk) ? "&#10004;" : " ") + "</li>" +
        "<li><> vorig ww " + ((wwTestpasswd) ? "&#10004;" : " ") + "</li>";
      if (wwLengte && wwTestpasswd && herhaalGelijk)
      {
        $("#feedback").removeClass("fbRood").addClass("fbGroen");
        $('input[type="submit"]').attr('disabled', false).attr("class", "");
        $("#btnSubmit").attr("title", "Klik om uw nieuwe wachtwoord op te slaan");

      } else
      {
        $("#feedback").removeClass("fbGroen").addClass("fbRood");
        $('input[type="submit"]').attr('disabled', true).attr("class", "grayed");
        $("#btnSubmit").attr("title", "U kunt pas opslaan als uw nieuwe wachtwoord voldoet");
      }
      $("#feedback").html(htmlBlock);
      <?
      }


?>
    }

    function testPasswd()
    {
      var ww = $("#nieuwWW").val();
      if (ww.length > 2)
      {

        $.ajax(
            {
              type: "POST",
              url: "lookups/wwb-testpasswd.php",
              async: false,
              data: { user: "<?=$USR?>", passwd: ww, location: "<?=$__appvar["bedrijf"]?>" }
            }).done(function( msg ) 
            {
              switch (msg)
              {
                case "true":
                  wwTestpasswd = true;
                  break;
                default:
                  wwTestpasswd = false;
                  break;  
              }
              return;
            });
          }    
    }
    var cnt = 1;

    var position = $("#nieuwWW").position();
    $("#feedback").css("top",position.top ).css("left",(position.left + 200));
    $('input[type="submit"]').attr('disabled' , true).attr("class","grayed");

    $("#nieuwWW2").keyup(function()
    {

      herhaalGelijk = ($("#nieuwWW").val() === $("#nieuwWW2").val());
      feedback();
      console.log("in keyup ww1 ww="+ $("#nieuwWW").val()+ "  ww2= " + $("#nieuwWW2").val() + " :: " + cnt++);
    });

    $("#nieuwWW").keyup(function()
    {
      testPasswd();
      var l = $(this).val().length;
      feedback();
      $("#feedback").show();

    });
  });
    

</script>  

<?


echo template($__appvar["templateRefreshFooter"],$content);


?>