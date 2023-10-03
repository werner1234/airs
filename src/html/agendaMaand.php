<?
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/24 17:05:54 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: agendaMaand.php,v $
 		Revision 1.1  2010/01/24 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/09/23 08:04:42  cvs
 		eerste commit vanuit simbis 23092008
 		
 		Revision 1.4  2007/06/04 09:24:11  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2005/12/01 13:23:47  cvs
 		*** empty log message ***
 		
 	
*/

include_once("wwwvars.php");
include_once("../config/agenda_functies.php");
include_once("../classes/AE_cls_calendarPicker.php");
$datPick = new calendarPicker();
setlocale(LC_TIME,"nl_NL");

session_start();
$dagpointer = $_SESSION['agendaDagpointer'];
$_SESSION['submenuHtml'] = "selecteer datum<br>".
                           $datPick->getCalendar(date('n',$dagpointer-(31*86400)),date('Y',$dagpointer-(31*86400))).
                           "<br>".
                           $datPick->getCalendar(date('n',$dagpointer+(31*86400)),date('Y',$dagpointer+(31*86400)));

if ($_SESSION['agendaLayout'] <> "maand" )
{
	$_SESSION['agendaLayout'] = "maand";
}
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
   
 
$TDK = "<td bgcolor=$TDcolorKop valign=top nowrap ";
$TDI = "<td valign=top nowrap ";
$lnktag = "<font size=1>";
$maandtm  = date('t',$dagpointer);

if ($cookie_single == 1) $S_query = " AND Gebruikers.Gebruiker = '".strtoupper($USR)."' ";

//$query = "SELECT * FROM agenda WHERE plandate >= '".substr(jul2db($dagpointer),0,8)."01' AND plandate <= '".substr(jul2db($dagpointer),0,8).$maandtm."' ".$S_query." ORDER BY plandate,plantime ";

$query = "SELECT  agenda.id AS agenda_id,
                  agenda.*,
                  Gebruikers.Gebruiker,
                  Gebruikers.bgkleur
          FROM agenda
          LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id
          LEFT JOIN Gebruikers ON agenda_gebruiker.user_id LIKE Gebruikers.Gebruiker
          WHERE agenda.plandate >= '".substr(jul2db($dagpointer),0,8)."01' AND agenda.plandate <= '".substr(jul2db($dagpointer),0,8).$maandtm."' ".$S_query."
          ORDER BY agenda.plantime,Gebruikers.Gebruiker ASC";

$startdag = "'".substr(jul2db($dagpointer),0,8)."01' ";
$stopdag  = "'".substr(jul2db($dagpointer),0,8).$maandtm."' ";

/*
$query = "
SELECT 
agenda.id AS agenda_id, agenda.*, Gebruikers.Gebruiker, Gebruikers.bgkleur, agendaHerhaling.interval, agendaHerhaling.eindDate, agendaHerhaling.startDate
FROM agenda 
LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id 
LEFT JOIN Gebruikers ON agenda_gebruiker.user_id LIKE Gebruikers.Gebruiker 
LEFT JOIN agendaHerhaling ON agenda.id = agendaHerhaling.agendaId 
WHERE agenda.plandate >= $startdag AND agenda.plandate <= $stopdag ".$S_query."
OR (
     agendaHerhaling.startDate <=  $startdag 
    
 ) 
ORDER BY agenda.plantime, Gebruikers.Gebruiker ASC 
"; //agendaHerhaling.startDate >= $startdag AND agendaHerhaling.eindDate <= $stopdag  AND 
echo $query;
*/
$prevmonth = date('n',$dagpointer);
$prevyear  = date('Y',$dagpointer);
$sun = get_sunday_before ( $prevyear, $prevmonth, 1 );
$monthstart = mktime ( 0, 0, 0, $prevmonth, 1, $prevyear );
$monthend = mktime ( 0, 0, 0, $prevmonth + 1, 0, $prevyear );

$db = new DB();
$db->SQL($query);
if ($db->QRecords($query))
{
  $cmdline = "";
  $i=0;
  while ($row = $db->NextRecord())
  {
    $endTime = db2jul($row['eindDate']);
    $startTime = db2jul($row['startDate']);

    
    if ($row[init] =="")    $row[init]    = $row[gebruiker];
    if ($row[bgkleur]=="")  $row[bgkleur] = "#c0c0c0";

    unset($highlight);
    $tel = 0;

    $highlight = $row[bgkleur];

    $addstr  = "<table bgcolor=".$highlight." border=0 width=100% >\n";
    $addstr .= "<tr><td> \n";
    $addstr .= "<a href=\"agendaEdit.php?id=".$row[id]."&action=edit&ref=agendaMaand.php\"><font size=1>";
    $addstr .= substr($row[plantime],0,5)." (".substr($row[duur],0,5).") ".$row[init]."</font><font size=1></a>\n";
    $addstr .= $row[soort]." ".$row[klant]."\n";
    $addstr .= "<br>".$row[kop]."</font>\n";
    $addstr .= "</td></tr>\n";

	  $addstr .= "</table><hr align=\"center\" size=\"1\">";

	  
	  $plandate = db2jul($row[plandate]);      
	  if ($row['interval'] == '')
	  {
	    $teststr = date ( "d", $plandate);
      $ap[$teststr] .= $addstr;
	  } 
	  else 
	  {
	    if ($row['interval'] == 'D' )
	    {
	      for ($i=0; $i <= date('d',$monthend); $i++) 
	      {
	        if (($endTime > $monthstart + ($i * 86400)) && ($startTime < $monthstart + $i * 86400) )
	        {
	        if ($i <10)
	          $ap['0'.$i] .= $addstr;
	        else 
	          $ap[$i] .= $addstr;
	        }
	      }
	    }
	    else 
	    {
	      if ($row['interval'] == 'W')
	       $searchTime = 'w';
	      elseif ($row['interval'] == 'M' )
	       $searchTime = 'j';
	      elseif ($row['interval'] == 'J' )
	       $searchTime = 'z';
	         
	      $planDay = date($searchTime,$plandate) +1;
	      
	      for ($i=0; $i <= date('d',$monthend); $i++) 
	      {
	        $huidigeDag = $monthstart + ($i * 86400);
	        if ($planDay == date($searchTime,$huidigeDag) && ($endTime > $huidigeDag) && ($startTime < $huidigeDag))
	        {
	        if ($i <10)
	          $ap['0'.$i] .= $addstr;
	        else 
	          $ap[$i] .= $addstr;
	        }
	      }
	    }
	  }

	 

  }
}

//$prevmonth = date('n',$dagpointer);
//$prevyear  = date('Y',$dagpointer);

echo "<table border=\"0\" width=\"98%\" cellspacing=\"1\" align=\"center\">";
//$sun = get_sunday_before ( $prevyear, $prevmonth, 1 );
//$monthstart = mktime ( 0, 0, 0, $prevmonth, 1, $prevyear );
//$monthend = mktime ( 0, 0, 0, $prevmonth + 1, 0, $prevyear );
echo "<tr><td colspan=\"8\" bgcolor=\"#eeeeff\" align=\"center\"><font size=\"4\">".$maandnaam[date('n',$dagpointer)]." ".date('Y',$dagpointer) . "</font></td></td>
      <tr bgcolor=\"#eeeeff\"><td>zo</td><td>ma</td><td>di</td><td>wo</td><td>do</td><td>vr</td><td>za</td><td align=\"center\" valign=\"top\" bgcolor=\"#eeeeff\">w</td></tr>\n";

for ( $i = $sun; date ( "Ymd", $i ) <= date ( "Ymd", $monthend );  $i += ( 24 * 3600 * 7 ) )
{
  print "<tr>\n";
  $week = $i+24*3600;
  for ( $j = 0; $j < 7; $j++ )
  {
    $bgc = "#ffffff";
    $date = $i + 7200 + ( $j * 24 * 3600 );
    if ( date ( "Ymd", $date ) >= date ( "Ymd", $monthstart ) &&
         date ( "Ymd", $date ) <= date ( "Ymd", $monthend ) )
    {
	    $d = date ( "d", $date );
	    if ($d == date ( "d", $dagpointer ))
	    {
	      $bgc = "#eeffee";
	    }
	    else
	    {
	      $bgc = "#eeeeee";
	    }
      print "<td width=\"14%\" height=\"25\" valign=\"top\" bgcolor=\"".$bgc."\"><font size=\"-2\"><a href=\"agendaDagpointer.php?spec=$date\">" .$d . "</a></font><br>".$ap[$d]."</td>\n";
    }
    else
    {
      print "<td width=\"14%\" height=\"25\" bgcolor=\"".$bgc."\"></td>\n";
    }
  }
  print "<td  align=\"center\" valign=\"top\" bgcolor=\"#eeeeff\">".date("W",$date)."</td>\n";
  print "</tr>\n";
}

echo "</table>\n";
echo template($__appvar["templateRefreshFooter"],$content);
?>