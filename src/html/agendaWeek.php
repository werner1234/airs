<?
include_once("wwwvars.php");
include_once("../config/agenda_functies.php");
include_once("../classes/AE_cls_calendarPicker.php");
$datPick = new calendarPicker();
setlocale(LC_TIME,"nl_NL");

session_start();
$dagpointer = $_SESSION['agendaDagpointer'];
if ($_SESSION['agendaLayout'] <> "week" )
{
	$_SESSION['agendaLayout'] = "week";
}
$_SESSION['submenuHtml'] = "selecteer datum<br>".
                           $datPick->getCalendar(date('n',$dagpointer),date('Y',$dagpointer)).
                           "<br>".
                           $datPick->getCalendar(date('n',$dagpointer+(31*86400)),date('Y',$dagpointer+(31*86400)));
                           
                           
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($_SESSION['submenuHtml'],"");
                           
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));

session_write_close();

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

$TDK = "<td bgcolor=\"".$TDcolorKop."\" valign=\"top\" nowrap ";
$TDI = "<td valign=\"top\" nowrap ";
$lnktag = "<font size=\"1\">";

if (empty($page))
  $page=1;

$weekvan  = Startweek($dagpointer);
$weektm   = Stopweek($dagpointer);

if(empty($dag))
  $dag = $dagpointer;

if (!isset($page) || ($page == 1))
{
  $limit = " LIMIT $MaxRows ";
  $page = 1;
}
else
{
  $limit = " LIMIT ".(($page-1) * $MaxRows).",$MaxRows ";
}

if ($cookie_single == 1) $S_query = " AND gebruikers.init = '".strtoupper($USR)."' ";

//$query = Qweek("agenda","plandate",$dagpointer).$S_query."ORDER BY plandate,plantime ";

$vanjul = $dagpointer - ((date('w',$dagpointer)-1)*86400);
$startstr = date('Y',$vanjul)."-".date('m',$vanjul)."-".date('d',$vanjul);

$indat = (int)db2jul($startstr)+ 604800 - 86400;
$tm = (date('d',$indat));
$stopstr = date('Y',$indat)."-".date('m',$indat)."-".$tm;

$query = "SELECT  agenda.id AS agenda_id,
                  agenda.*,
                  Gebruikers.Gebruiker,
                  Gebruikers.bgkleur
          FROM agenda
          LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id
          LEFT JOIN Gebruikers ON agenda_gebruiker.user_id = Gebruikers.Gebruiker
          WHERE agenda.plandate >= '".$startstr."' AND agenda.plandate <= '".$stopstr."' ".$S_query."
          ORDER BY agenda.plantime,Gebruikers.Gebruiker ASC";

$db =new DB();
$db->SQL($query);
$db->Query();
$recs = $db->Records();
if ($recs)
{
  $cmdline = "";
  $i=0;
  while ($row = $db->nextRecord())
  {
    if ($row[init] =="")    $row[init]    = $row[gebruiker];
    if ($row[bgkleur]=="")  $row[bgkleur] = "#c0c0c0";

    unset($highlight);
    $tel = 0;

    $highlight = $row[bgkleur];

    $teststr = substr(dag(db2jul($row[plandate])),0,2);

    $addstr  = "<table bgcolor=".$highlight." border=0 width=98% align=\"center\">\n";
    $addstr .= "<tr><td> \n";
    $addstr .= "<a href=\"agendaEdit.php?action=edit&id=".$row[id]."&ref=agendaWeek.php\"><b>";
    $addstr .= substr($row[plantime],0,5)." (".substr($row[duur],0,5).") ".$row[init]."</b></a>\n";
    $addstr .= $row[soort]." ".$row[klant]."\n";
    $addstr .= "<br>".$row[kop]."\n";
    $addstr .= "</td></tr>\n";

    $addstr .= "</table>        <hr noshade size=\"1\">";

    switch ($teststr)
    {
      case "ma":
        $str[0] .= $addstr;
        break;
      case "di" :
        $str[1] .= $addstr;
        break;
      case "wo" :
        $str[2] .= $addstr;
        break;
      case "do" :
        $str[3] .= $addstr;
        break;
      case "vr" :
        $str[4] .= $addstr;
        break;
      case "za" :
        $str[5] .= $addstr;
        break;
      case "zo" :
        $str[6] .= $addstr;
        break;
    }
  }
}
else
{
    unset($str);
}
?>
<div align="center">
<table border="0" style="height:20px" width="100%" cellspacing="1" >
  <tr>
    <td bgcolor="#ffffff" colspan=7 height=30 align=center >
      <font face="Arial, Helvetica, sans-serif">Weekoverzicht - week <b><?=date("W",$dagpointer)?></b> van <b><?=ldatum($weekvan)?></b> t/m <b><?=ldatum($weektm)?></font></b>
    </td>
  </tr>

</table>
<table border=0>
    <tr>
      <td>
        <?
          $dag = $weekvan;
          $maxHPOS =0;
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">

      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*1);
          $maxHPOS =0;
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*2);
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*3);
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*4);
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*5);
        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    <tr>
      <td>
        <?
          $dag = $weekvan+(86400*6);

        ?>
        <map name="<?=$dag?>">
        <?include("agendaDrawDay.php");?>
        </map>
        <a name=<?=$dag?>>
        <img src="agendaDrawDay.php?dag=<?=$dag?>&do=IMG" alt="" border="0" usemap="#<?=$dag?>">
      </td>
    </tr>
    </table>
</div>
</body>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>