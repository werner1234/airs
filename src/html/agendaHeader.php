<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/24 17:05:54 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: agendaHeader.php,v $
*/


?>
    <table border="0" cellspacing="0" cellpadding="5" width="100%">
    <tr>
      <td align="left" width="33%">
      &nbsp;<?= vt('Agenda van'); ?> <?=$USR?> <br>
      </td>
      <td align="center" width="33%">
       <?= vt('vandaag'); ?> <?=dag()?>, <?=ldatum()?>
      </td>
      <td align="right" width="33%">
       
       <?= vt('Vernieuwen over'); ?> <a id="clock">..</a> <?= vt('seconden'); ?>&nbsp;
      </td>
    </tr>
    </table>

  	<table border="0" cellspacing="1" cellpadding="0" width="98%" bgcolor="darkGray" align="center">
    <tr>
      <td align=center valign=middle bgcolor="#EEEEEE">
<?
      if (isset($USR))
      {
?>
        <table width="90%">
        <tr>
          <td><a href="agendaDagpointer.php?richting=B" class="button"><?=makebutton("record_back",16);?></a></td>
          <td><a href="agendaEdit.php?action=new"       class="button"><?=makebutton("record_add",16);?></a></td>
          <td><a href="agendaDagpointer.php?richting=F" class="button"><?=makebutton("record_next",16);?></a></td>
          <td><a href="agendaDagpointer.php?vandaag=1"  class="button"><?= vt('ga naar vandaag'); ?></a></td>
          <td><a href="agendaDag.php"      class="button"><?=makebutton("agenda_dag",16);?></a></td>
          <td><a href="agendaWeek.php"     class="button"><?=makebutton("agenda_week",16);?></a></td>
          <td><a href="agendaMaand.php"    class="button"><?=makebutton("agenda_maand",16);?></a></td>
          <td><a href="agendaDaglijst.php" class="button"><?=makebutton("agenda_daglijst",16);?></a></td>
          <td><a href="takenList.php"   class="button"><?=makebutton("agenda_taken",16);?> </a></td>
        </tr>
        </table>
<?
      }
      else
      {
        echo vt("Niet ingelogd in de Agenda");
      }
?>
      </td>
    </tr>
    </table>
