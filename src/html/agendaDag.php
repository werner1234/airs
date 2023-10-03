<?
include_once("wwwvars.php");
include_once("../config/agenda_functies.php");
include_once("../classes/AE_cls_calendarPicker.php");
$datPick = new calendarPicker();

setlocale(LC_TIME,"nl_NL");

session_start();
$dagpointer = $_SESSION['agendaDagpointer'];

if ($_SESSION['agendaLayout'] <> "dag" )
{
	$_SESSION['agendaLayout'] = "dag";
}
$_SESSION['submenuHtml'] = "selecteer datum<br>".
                           $datPick->getCalendar(date('n',$dagpointer),date('Y',$dagpointer)).
                           "<br>".
                           $datPick->getCalendar(date('n',$dagpointer+(31*86400)),date('Y',$dagpointer+(31*86400)));
                           
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
session_write_close();
$filename = explode("/",$PHP_SELF);
$_SESSION['agendascript'] = $filename[(count($filename)-1)];
$editcontent = array();
$content['body'] = "onload=\"startClock()\"";
$content['jsincludes'] = '
<meta http-equiv=Refresh content=120;>
<SCRIPT LANGUAGE="JavaScript">
    var x = 120;
    var y = 1;
    function startClock()
    {
      x = x-y
      document.getElementById("clock").innerHTML = x
      timerID = setTimeout("startClock()", 1000)
    }
</SCRIPT>
';

echo template($__appvar["templateContentHeader"],$content);



include("agendaHeader.php");

?>
<div align="center">
<table border="0" style="height:20px" width="100%" cellspacing="1" >
  <tr>
    <td bgcolor="#ffffff" colspan=7 height=20 align=center >
      <font face="Arial, Helvetica, sans-serif">Dagoverzicht - week <b><?=date("W",$dagpointer)?></b>, geselecteerde datum <b><?=ldatum($dagpointer)?></b> </font></b>
    </td>
  </tr>
</table>



  <map name="imgmap">
      <?include_once("agendaDrawDay.php");?>
      </map>
      <img src="agendaDrawDay.php?dag=<?=$dagpointer?>&do=IMG" alt="" border="0" usemap="#imgmap">
      
</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>