<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/16 08:20:59 $
 		File Versie					: $Revision: 1.2 $

 		$Log: tijdelijkePositieVerwerk.php,v $
 		Revision 1.2  2013/12/16 08:20:59  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2011/06/22 11:47:03  cvs
 		*** empty log message ***
 		



*/
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
echo "start";
session_start();
$_SESSION[submenu] = "";
//clear navigatie
$_SESSION[NAV] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);
	

	
		$jaar = date("Y",$dat);

		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();	                              // show the ProgressBar

		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Verwerken tijdelijke tabel');
		$pro_step = 0;

		$DB = new DB();
    
    
    if (is_array($_SESSION["portefeuillesQueries"]) > 0)
    {
      for ($x=0; $x < count($_SESSION["portefeuillesQueries"]); $x++)
      {
        $DB->executeQuery($_SESSION["portefeuillesQueries"][$x]);
        $_SESSION["portefeuillesQueries"][$x] = "";
      }    
    }
    
    if (is_array($_SESSION["rekeningNrsQueries"]) > 0)
    {
      for ($x=0; $x < count($_SESSION["rekeningNrsQueries"]); $x++)
      {
        $DB->executeQuery($_SESSION["rekeningNrsQueries"][$x]);
        $_SESSION["rekeningNrsQueries"][$x] = "";
      }    
    }
    
    if (is_array($_SESSION["beleggingscategorieQueries"]))
    {
      for ($x=0; $x < count($_SESSION["beleggingscategorieQueries"]); $x++)
      {
        $DB->executeQuery($_SESSION["beleggingscategorieQueries"][$x]);
        $_SESSION["beleggingscategorieQueries"][$x] = "";
      }    
    }
    
		$DB2 = new DB();

		$query = "SELECT * FROM TijdelijkePositieLijst WHERE TijdelijkePositieLijst.add_user = '$USR' ";
		$DB->executeQuery($query);

		//

		$pro_multiplier = (100 / $DB->Records());

		while($positieRec = $DB->NextRecord())
		{
   		$pro_step += $pro_multiplier;
   		$prb->moveStep($pro_step);

			$_query = "INSERT INTO PositieLijst SET \n";
	    $sep = " ";
	    while (list($key, $value) = each($positieRec))
	    {
        $_query .= "$sep PositieLijst.$key = '".mysql_escape_string($value)."'\n";
        $sep = ",";
     }
  
     //echo "<PRE>".$_query."<?PRE><BR />";
  
     $DB2->SQL($_query);
		 if(!$DB2->Query())
		 {
		   $prb->hide();
		   fout("Fout: ".$_query,mysql_error());
			 exit;
		 }
      
    }

			// done, remove tijdelijke mutatie!
		$query = "DELETE FROM TijdelijkePositieLijst WHERE add_user = '$USR' ";
		$DB2->SQL($query);
		$DB2->Query();
  
  $_txt = "De gegevens zijn verwerkt voor gebruiker ($USR)";
	$prb->hide();

?>
<br>
<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<?=$_txt?>
<br>
<br>

<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>