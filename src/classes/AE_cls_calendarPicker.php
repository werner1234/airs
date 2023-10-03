<?php
/* 	
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/12/16 14:43:09 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: AE_cls_calendarPicker.php,v $
 		Revision 1.1  2005/12/16 14:43:09  jwellner
 		classes aangepast
 		
 		Revision 1.1  2005/11/21 10:08:25  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/17 08:09:45  cvs
 		*** empty log message ***
 		
 	
*/
class calendarPicker
{
  var $shortcutItems;
	var $output;

	function calendarPicker() 
  {
	  $this->output = "";
	  
  }
	
  
  function smallmonth ( $prevmonth=0, $prevyear=0) 
  {
 
    global $maandnaam;
    
    $DAG  = 24 * 3600;
    $WEEK = 24 * 3600 * 7;
    if ($prevmonth == 0) {$prevmonth = date('n',time());}
    if ($prevyear  == 0) {$prevyear  = date('Y',time());}
    $sun = get_sunday_before ( $prevyear, $prevmonth, 1 );
    $monthstart = mktime ( 0, 0, 0, $prevmonth, 1, $prevyear );
    $monthend = mktime ( 0, 0, 0, $prevmonth + 1, 0, $prevyear );
    $this->output = "
  <table class=\"calendarBody\" cellspacing=\"3\" cellpadding=\"0\" >
  <tr>
    <td class=\"calendarTop\" colspan=\"20\">
      <b>$maandnaam[$prevmonth]  $prevyear </b>
    </td>
  </tr>
	<tr>
	  <td class=\"calendarWeek\">W</td>
		<td class=\"calendarHead\">zo</td>
		<td class=\"calendarHead\">ma</td>
		<td class=\"calendarHead\">di</td>
		<td class=\"calendarHead\">wo</td>
		<td class=\"calendarHead\">do</td>
		<td class=\"calendarHead\">vr</td>
		<td class=\"calendarHead\">za</td>
		
	</tr>
	";
 
  for ( $i = $sun; date ( "Ymd", $i ) <= date ( "Ymd", $monthend ); $i += $WEEK ) 
  {
    $this->output .=  "<tr>\n";
    $this->output .= "<td  class=\"calendarWeek\"  >".date("W",( ( $i + 7200 + (6 * $DAG))))."</td>\n";
    for ( $j = 0; $j < 7; $j++ ) 
    {
      $date = $i + 7200 + ($j * $DAG);
      if ( date ( "Ymd", $date ) >= date ( "Ymd", $monthstart ) && date ( "Ymd", $date ) <= date ( "Ymd", $monthend ) ) 
      {
        $this->output .= "
        <td class=\"calendarCell\" >
          <a class=\"calendarLinks\" href=\"agendaDagpointer.php?spec=".$date."\" target=\"content\">". date ( "d", $date ) . "</a></td>\n";
      }
      else
      {
        $this->output .= "<td></td>\n";
      }
    }
    
    $this->output .= "</tr>\n";
  }
  $this->output .= "</table>\n";
}
  
  
  function getCalendar($prevmonth, $prevyear)
  {
    $this->smallmonth($prevmonth, $prevyear);
    return $this->output; 
  }
}

?>