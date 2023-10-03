<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/02/03 17:05:21 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: agendaDaglijst.php,v $
 		Revision 1.2  2010/02/03 17:05:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/01/24 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/09/23 08:04:42  cvs
 		eerste commit vanuit simbis 23092008
 		
 		Revision 1.2  2005/12/01 13:23:47  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***
 		
 	
*/
 
include_once("wwwvars.php");
include_once("../config/agenda_functies.php");
include_once("../classes/AE_cls_calendarPicker.php");
$datPick = new calendarPicker();
setlocale(LC_TIME,"nl_NL");

session_start();
$dagpointer = $_SESSION['agendaDagpointer'];

if ($cookie_single == 1) $S_query = " AND Gebruikers.Gebruiker = '".strtoupper($USR)."' ";

$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem("Daglijst afdrukken","javascript:parent.content.focus();parent.content.print();",array("target"=>"content"));
echo template($__appvar["templateContentHeader"],$content);
?>

<table border=0 bordercolor=#dddddd width=600>
<tr>
  <td colspan=10 align=center>
    <b>Agenda voor <?=dag($dagpointer)." ".ldatum($dagpointer)?></b><hr>
  </td>
</tr>

<?   

 
   $TDK = "<td bgcolor=$TDcolorKop valign=top nowrap ";
   $TDI = "<td valign=top nowrap ";
   $lnktag = "<font size=1>";
  		 
	
  $query = "SELECT * FROM agenda WHERE plandate = '".substr(jul2db($dagpointer),0,10)."' ".$S_query." ORDER by plantime";
  $db = new DB();
  $db2 = new DB();
  $db->SQL($query); 
  $db->Query();
  $recs = $db->Records();
  
  if ($recs > 0)
  {  
    while ($row = $db->nextRecord())
    {  
	    $q2 = "SELECT * FROM CRM_naw WHERE id = '".$row['rel_id']."'";
      $db2->SQL($q2);
      if (isset($row['rel_id']))
        if ($NAM = $db2->lookupRecord())
          $RELDATA = $row['klant'].", ".$NAM['adres'].", ".$NAM['plaats']."<br>".
                     "tel1 :".$NAM['tel1']."&nbsp;&nbsp;&nbsp;tel2 :".$NAM['tel2'];
        else
          $RELDATA = $row[klant];
          
     echo "<tr>
              <td nowrap width=50>".substr($row['plantime'],0,5)."</td>
              <td> -- ".$row['gebruiker']." -- ".$row['soort']." -- <br><b>".
                   $RELDATA."</b> <br> ".$row['kop']."<br>"."
              </td>
            </tr>
            <tr>
              <td></td>
              <td width=600>".ftxt($row['txt'])."<hr>
              </td>
            </tr>";
	  } 
  }	
  else
  {
  	  ?>
      <tr>
        <td>
	        Geen items voor gesecteerde dag
        </td>
      </tr>
      <?

	}    

    

?>
</table>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>