<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/03/21 13:35:43 $
 		File Versie					: $Revision: 1.3 $

 		$Log: agendaDrawDay.php,v $
 		Revision 1.3  2010/03/21 13:35:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/02/10 17:49:17  rvv
 		*** empty log message ***

 		Revision 1.1  2010/01/24 17:05:54  rvv
 		*** empty log message ***

 		Revision 1.1  2008/09/23 08:04:42  cvs
 		eerste commit vanuit simbis 23092008

 		Revision 1.5  2007/06/04 09:24:11  rvv
 		*** empty log message ***

 		Revision 1.4  2007/01/29 17:27:14  cvs
 		*** empty log message ***

 		Revision 1.3  2006/01/09 14:05:27  cvs
 		*** empty log message ***

 		Revision 1.2  2005/12/01 13:23:47  cvs
 		*** empty log message ***

 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***


*/

include_once("wwwvars.php");
include_once("../config/agenda_functies.php");

if ($cookie_single == 1) $S_query = " AND gebruikers.init = '".strtoupper($USR)."' ";


$WIDTH     = 700;
$STARTTIME = 7;
$ENDTIME   = 22;
$HOURS     = $ENDTIME-$STARTTIME;

$PIXQUART  = $WIDTH/($HOURS*4);
$PIXHOUR   = $WIDTH/$HOURS;
$TMPTIME   = $STARTTIME;

$map = array();

if(empty($dag))  $dag = $dagpointer;

$querydag = substr(jul2db($dag),0,10);
$query = "SELECT  agenda.id AS agenda_id,
                  agenda.*,
                  Gebruikers.Gebruiker,
                  Gebruikers.bgkleur
          FROM agenda
          LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id
          LEFT JOIN Gebruikers ON agenda_gebruiker.user_id = Gebruikers.Gebruiker
          WHERE agenda.plandate = '".$querydag."' ".$S_query."
          ORDER BY Gebruikers.Gebruiker,agenda.plantime ASC";
//echo $query;

//echo $querydag;
/*
$query = "
SELECT
agenda.id AS agenda_id, agenda.*, Gebruikers.Gebruiker, 'FF0000' as bgkleur, agendaHerhaling.interval
FROM agenda
LEFT JOIN agenda_gebruiker ON agenda.id=agenda_gebruiker.agenda_id
LEFT JOIN Gebruikers ON agenda_gebruiker.user_id = Gebruikers.id
LEFT JOIN agendaHerhaling ON agenda.id = agendaHerhaling.agendaId
WHERE agenda.plandate = '".$querydag."' ".$S_query."
OR (
    (agendaHerhaling.eindDate >= '".$querydag."' AND agendaHerhaling.startDate <=  '".$querydag."') AND
    (
     (agendaHerhaling.interval = 'D') OR
     (agendaHerhaling.interval = 'W'  AND dayofweek(  '".$querydag."' ) = dayofweek(agenda.plandate)) OR
     (agendaHerhaling.interval = 'M'  AND dayofmonth( '".$querydag."' ) = dayofmonth(agenda.plandate)) OR
     (agendaHerhaling.interval = 'Y'  AND dayofyear(  '".$querydag."' ) = dayofyear(agenda.plandate))
    )
 )
ORDER BY Gebruikers.Gebruiker,agenda.plantime ASC
"; //agendaHerhaling.startDate <=  '".$querydag."'  AND agendaHerhaling.eindDate >=  '".$querydag."'
*/
//echo $query;

$HPOS    = 5;
$SPACING = 0;
$maxHPOS = 0;
$lastInit ='';
$db = new DB();
$db->SQL($query);
if ($db->Query())
{

  //
  $rowData = array();
  $count = 0;
  $HPOSInit =40;

  while ($row = $db->nextRecord())
  {
  // start while
	  $tijd  = explode(":",$row[plantime]);
	  $start = ((($tijd[0] - $STARTTIME) *4) + ($tijd[1] / 15) );
	  $tijd  = explode(":",$row[duur]);
	  $end	 = ($start + ($tijd[0] * 4) + ($tijd[1] / 15));

	  /*
    if($row['Gebruiker'] == $lastInit)
	  {
	   $HPOS = $HPOSInit;
	   $done=false;
	   while ($done != true)
	   {
	    $done = true;
	    for($i=0;$i<count($blockStart[$HPOS]);$i++)
	    {
	      if ($blockStart[$HPOS][$i] < $start && $blockEnd[$HPOS][$i] > $end )
	      {
	       $HPOS += 25;
         $done = false;
	      }
	      elseif ($blockStart[$HPOS][$i] < $start && $blockEnd[$HPOS][$i] > $start )
	      {
	       $HPOS += 25;
	       $done = false;
	      }
	      elseif ($blockStart[$HPOS][$i] > $end && $blockEnd[$HPOS][$i] < $end )
	      {
	       $HPOS += 25;
         $done = false;
	      }
	    }
	   }
	  }
	  else
	  {
	    $HPOS += 25;
	    $HPOSInit = $HPOS;
	  }
	  */

	  	    $HPOS += 25;
	    $HPOSInit = $HPOS;

 	  $blockStart[$HPOS][]=$start;
    $blockEnd[$HPOS][]=$end;
    $lastInit = $row['Gebruiker'] ;
    $row['HPOS']=$HPOS  ;
    $rowData[] = $row;
    $maxHPOS = max($maxHPOS,$HPOS);
    $count++;
   }
  //

  $HEIGTH =  $maxHPOS +35;
  $klok = imagecreatefrompng("images/alarmklok.png");
  $tick = imagecreatefrompng("images/agendadone.png");

  $im = @imagecreate ($WIDTH, $HEIGTH) or die ("Cannot Initialize new GD image stream");
  $wkday="";

  if (date("w-d",mktime()) == date("w-d",$dag))
  {
    $back = imagecolorallocate ($im, 214, 217, 232);
    $wkday=">";
  }
  else
  {
    if (date("w",$dag)==0 || date("w",$dag)==6)
      $back = imagecolorallocate ($im, 255, 255, 187);
    else
      $back = imagecolorallocate ($im, 234, 234, 234);
  }

  //#f9ffae
  $black    = imagecolorallocate ($im, 0, 0, 0);
  $gray     = imagecolorallocate ($im, 211, 211, 211);
  $darkgray = imagecolorallocate ($im, 78, 78, 78);
  $white    = imagecolorallocate ($im, 255, 255, 255);

  $linkToEdit = "agendaEdit.php?action=new&ref=".$_SESSION['agendascript'];
  // maak rastertje
  for($a=$PIXQUART; $a<=$WIDTH;$a=$a+$PIXQUART)
  {
    imageline($im, $a,0,$a,$HEIGTH,$gray);
    $prevX=$a-$PIXQUART;
    $kwartier = ($prevX/$PIXQUART);
    $uur = UitNullen($STARTTIME + floor($kwartier / 4),2);
    $min = ($kwartier % 4) * 15;
    $map[] = "<area title=\"Tijd: ".$uur." uur ".$min." min\" coords=\"".round($prevX).",0,".round($a).",".round($HEIGTH)."\" href=\"".$linkToEdit."&day=$dag&hour=$uur&minute=$min\" >\n";


  }

  for($a=0; $a <=$WIDTH;$a=$a+$PIXHOUR)
  {
    $TIME= $TMPTIME;
    imagestring ($im, 3, $a+2, 15, $TIME, $black);
    imageline($im, $a,0,$a,$HEIGTH,$black);
    $TMPTIME++;
  }

  //schrijf dag info
  imagefilledrectangle($im,0,0,$WIDTH,15,$black);
  imagestring($im, 3, 3,0, $wkday." ".dag($dag)." ".ldatum($dag),$white);

  $tmp=0;
 // print_r($rowData);
  foreach($rowData as $row)
  {  // start while
    $HPOS = $row['HPOS'];
   // echo $HPOS.'<br>';
    $tel=0;
    if ($row['Gebruiker'] =="")    $row['Gebruiker']    = $row['gebruiker'];
    if ($row['bgkleur']=="")  $row['bgkleur'] = "#c0c0c0";

    $highlight = hex2rgb($row[bgkleur]);

	  $tijd  = explode(":",$row['plantime']);
	  $start = ((($tijd[0] - $STARTTIME) *4) + ($tijd[1] / 15) );
	  $tijd  = explode(":",$row['duur']);
	  $end	 = ($start + ($tijd[0] * 4) + ($tijd[1] / 15));

    $x1 = $start*$PIXQUART;
    $y1 = $HPOS;
    $x2 = $end*$PIXQUART;
    $y2 = $HPOS+20;

    if($x1 == $x2)
      $x2 = $x1+10;

    $COL = imagecolorallocate ($im, $highlight['R'], $highlight['G'], $highlight['B']);


//int imagecopymerge ( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h, int pct)
//echo $linkToEdit.'<br>';
    $linkToEdit = "agendaEdit.php?action=edit&id=".$row[id]."&ref=".$_SESSION['agendascript'];
    imagefilledrectangle($im,$x1+2,$y1+2,$x2+2,$y2+2,$gray);
    imagefilledrectangle($im,$x1,$y1,$x2,$y2,$COL);
    imageline ($im, $x1, $y1,$x2, $y1, $darkgray);
    imageline ($im, $x1, $y1,$x1, $y2, $darkgray);
    imagestring ($im, 3, $x1+10, $y1+5, $al.$row['Gebruiker']." (".$row['klant'].")", $black);
    $alttxt = "         wie: ".$row['Gebruiker']."\nafspraak: ".$row['soort']."     \n      waar: ".$row['klant']."\n   betreft: ".$row['kop']."     ";
    $map[] = "<area title=\"".$alttxt."\" coords=\"".round($x1).",".round($y1).",".round($x2).",".round($y2)."\" href=\"".$linkToEdit."&day=$dag&hour=$uur&minute=$min\">\n";

    if ($row[done] == 1)
      imagecopymerge($im, $tick, $x1-8, $y1+2, 0, 0, 22, 16,100);
    else
    {
      if ($row[event_alert] == 1)
        imagecopymerge($im, $klok, $x1-8, $y1+2, 0, 0, 22, 16,100);
    }



  }

}

if ($do=="IMG")
{
  header ("Content-type: image/png");
  imagepng ($im);
}
else
{
  $map = array_reverse($map);
  for($a=0; $a < count($map); $a++)
    echo $map[$a];
}
?>