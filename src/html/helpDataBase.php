<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/02 16:16:06 $
 		File Versie					: $Revision: 1.18 $

 		$Log: helpDataBase.php,v $
 		Revision 1.18  2019/01/02 16:16:06  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/12/08 18:25:00  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/12/05 16:35:09  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/11/16 16:39:19  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/11/10 18:21:35  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/09/05 15:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/08/19 08:06:30  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/01/17 11:58:09  cvs
 		call 6516
 		
 		Revision 1.10  2017/07/22 18:20:50  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/03/19 16:34:15  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/04/20 16:28:49  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/04/08 08:10:43  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2011/08/31 14:37:39  rvv
 		*** empty log message ***

 		Revision 1.5  2008/01/10 09:45:55  rvv
 		Truncate tijdelijkerapportage

 		Revision 1.4  2007/11/02 12:54:18  cvs
 		*** empty log message ***

 		Revision 1.3  2007/01/02 11:42:42  rvv
 		Met tabel reparatiefunctie


*/



if(isset($argv) && $argv[1]=='leesDB')
{
 $action='leesDB';
 $disable_auth = true;
 $console=true;
}
else
{
  $console=false;
}

include_once("wwwvars.php");
session_start();
$_SESSION['submenu'] = "";
$_SESSION['NAV'] = "";
session_write_close();


$content = array(
  "jsincludes" => '
    
    <link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
    

    <script src="widget/js/jquery.min.js"></script>
    <script src="widget/js/jquery-ui.js"></script>
    
    
   
    <script type="text/javascript" src="javascript/algemeen.js"></script>
   
   '
);
if($console==false)
{
  echo template($__appvar["templateContentHeader"],$content);

?>

  <style>
    legend{
      padding: 3px 10px 3px 10px;
      background: whitesmoke;
    }
    #tabels
    {
      height: 250px;
      overflow: auto;
      margin-bottom: 15px;
    }
    .hrefLink{
      border: 1px solid #BBB;
      border-radius: 3px;
      padding: 3px 10px 3px 10px;
      background: #DDD;
    }
        .hrefLink:hover{
      background: #BBB;
    }
    .formblock{
      padding-top:10px;
    }
  </style>
<b><?=$PRG_NAME?> <?= vt('Database informatie'); ?></b><br><br>


<div class="formblock">
	<div class="formlinks"><?= vt('Database server adres'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[1]['server']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Update server adres'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[2]['server']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Update server login naam'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[2]['user']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Lokale database'); ?>
	</div>
	<div class="formrechts">
		<?=$_DB_resources[1]['db']?>
	</div>
</div>




<?
  $db = new DB();
  if($action == "repair" && $table !="")
  {
  $query = "REPAIR TABLE $table";
  $db->SQL($query);
  $db->Query();
  $result=$db->lookupRecord();
?>
 <div class="formblock">
	<div class="formlinks"><?= vt('Reparatie'); ?> <?=$table?>
	</div>
	<div class="formrechts">
		<?=$result['Msg_text']?>
	</div>
</div>
<?
  }

  if($action == "collation")
  {
    $DB=new DB();
    $query="show variables like 'character_set_database'";
    $DB->SQL($query);
    $charset=$DB->LookupRecord();
    $charset=$charset['Value'];
    if($charset != '')
    {
      $query="SHOW table status";
      $DB = new DB();
      $DB2 = new DB();
      $DB->SQL($query);
      $DB->Query();
      ?>
 <div class="formblock">
	<div class="formlinks"><?= vt('Collation fix'); ?>
	</div>
	<div class="formrechts">
	<?
      while($data=$DB->nextRecord('num'))
      {
        if($_GET['quick']==1)
          $maxrecords=10000;
        else
          $maxrecords=100000000;

        if($data[4] < $maxrecords)
        {
          $table=$data[0];
          $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
          $DB2->SQL($query);
          $DB2->Query();
          echo "$table fixed. <br>\n";
          ob_flush();
	      }
      }
    }
		?>
	</div>
</div>
<?
  }

  if($action == "consolidatie")
  {
    $DB = new DB();
    $query = "DELETE FROM Portefeuilles where consolidatie=2";
    $DB->SQL($query);
    $DB->Query();
    $portefeuilleMutaties = $DB->mutaties();
    $query = "DELETE FROM Rekeningen where consolidatie=2";
    $DB->SQL($query);
    $DB->Query();
    $rekeningenMutaties = $DB->mutaties();
    ?>
    <div class="formblock">
      <div class="formlinks"><?= vt('Verwijderen consolidatie records'); ?>
      </div>
      <div class="formrechts">
        <?
        echo "$portefeuilleMutaties portefeuille record(s) verwijderd.<br>\n";
        echo "$rekeningenMutaties rekeningen record(s) verwijderd.<br>\n";
        ?>
      </div>
    </div>
    <?
  }

  if($action == "consolidatiePaar")
  {
    include_once("../classes/AIRS_consolidatie.php");
    $con=new AIRS_consolidatie();
    $con->debug=true;

    $con->showOnly=false;
    $con->updateClient=false;
    $con->insertClient=true;
    $con->insertPortefeuille=true;
  
    $con->bijwerkenConsolidaties();
    ?>
    <div class="formblock">
    <div class="formlinks">MutatieInfo
    </div>
    <div class="formrechts">
    <?
    echo "Aantal mutaties: (".$con->mutatieAantal.")";
    ?>
    </div>
    </div>
    <?
  }


  if ( $action == "rekeningmutaties" )
  {

    $db = new DB();
    $query = "UPDATE `Rekeningafschriften` SET `Verwerkt` = '1', `change_date` = NOW(), `change_user` = '$USR' WHERE `Verwerkt` ='0' AND  `Datum` < '".date("Y")."-01-01 00:00:00'";
    $db->executeQuery($query);
    $mutaties1 = $db->mutaties();
    $query = "UPDATE `Rekeningmutaties` SET `Verwerkt` = '1', `change_date` = NOW(), `change_user` = '$USR' WHERE Verwerkt ='0' AND  `Boekdatum` < '".date("Y")."-01-01 00:00:00'";
    $db->executeQuery($query);
    $mutaties2 = $db->mutaties();
    ?>
    <div class="formblock">
      <div class="formlinks"><?= vt('Verwerken rekeningmutaties ouder dan'); ?> 01-01-<?=date("Y")?>
      </div>
      <div class="formrechts">
        <?
        echo "$mutaties1 " . vt('Rekeningafschriften verwerkt') . "<br>\n";
        echo "$mutaties2 " . vt('Rekeningmutaties verwerkt') . "<br>\n";
        ?>
      </div>
    </div>
    <?
  }

  if($action == "truncate" && strtoupper($table)=='TIJDELIJKERAPPORTAGE')
  {
  $query = "TRUNCATE TABLE $table";
  $db->SQL($query);
  $db->Query();
  $result=$db->lookupRecord();
?>
 <div class="formblock">
	<div class="formlinks"><b><?=$table?> <?= vt('leeg gemaakt'); ?>. </b>
	</div>
	<div class="formrechts">
		<?=$result['Msg_text']?>
	</div>
</div>
<?
  }

  $db->SQL("SHOW tables");
  $db->Query();
  while ($data = $db->nextRecord("num"))
  {
    $dbArray[] = $data[0];
  }
  sort($dbArray);
?>

<div class="formblock" >
	<div class="formlinks"><?= vt('Aangemelde tabellen'); ?>
	</div>
	<div class="formrechts" id="tabels">
	<table border="0" cellpadding="4" >
	<tr bgcolor="#CCCCCC">
	<?
	  $k=1;

	  while ($data = Next($dbArray))
	  {
	    if ($k > 3)
	    {
	     if ($col == "#EEEEEE")
	       $col = "#CCCCCC";
	     else
	       $col = "#EEEEEE";
	     echo "</tr><tr bgcolor=\"$col\">" ;
	     $k=1;
	    }
	    if(strtoupper($data)=='TIJDELIJKERAPPORTAGE')
	    echo  "<td> <a class='hrefLink' href=\"helpDataBase.php?action=repair&table=$data\">R</a></td> <td align=\"left\"  title=\"klik alleen op de T wanneer er niemand rapporten genereert\">".$data."
	           <a class='hrefLink' href=\"helpDataBase.php?action=truncate&table=$data\">[T]</a></td>" ;
	    else
	    echo  "<td> <a class='hrefLink' href=\"helpDataBase.php?action=repair&table=$data\">R</a></td> <td align=\"left\">".$data."</td>" ;
	    $k++;
	  }
?>
	</tr>
	</table>

	</div>
</div>


<fieldset>
  <legend><?= vt('speciale acties'); ?></legend>

  <div class="formblock">
    <div class="formlinks"><?= vt('Collation fix'); ?>
    </div>
    <div class="formrechts">
  <a class='hrefLink' href="helpDataBase.php?action=collation&quick=1"><?= vt('Snel'); ?></a> <a class='hrefLink' href="helpDataBase.php?action=collation&quick=0"><?= vt('Volledig'); ?></a>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><?= vt('Verwijderen consolidatie records'); ?>
    </div>
    <div class="formrechts">
      <a class='hrefLink'  href="helpDataBase.php?action=consolidatie"><?= vt('verwijderen'); ?></a>
    </div>
  </div>
<!--
<!--
  <div class="formblock">
    <div class="formlinks">Consolidatieparen bijwerken
    </div>
    <div class="formrechts">
      <a class='hrefLink'  href="helpDataBase.php?action=consolidatiePaar">bijwerken</a>
    </div>
  </div>
-->
  <div class="formblock">
    <div class="formlinks"><?= vt('Lees database'); ?>
    </div>
    <div class="formrechts">
  <a class='hrefLink' href="helpDataBase.php?action=leesDB"><?= vt('Lees'); ?></a>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><?= vt('Maak standaard vulling'); ?>
    </div>
    <div class="formrechts">
     <a class='hrefLink'  href="helpDataBase.php?action=standaardVulling"><?= vt('Check'); ?></a>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><?= vt('Verwerk oude rekeningmutaties'); ?>
    </div>
    <div class="formrechts">
      <a class='hrefLink'  href="helpDataBase.php?action=rekeningmutaties"><?= vt('verwerk'); ?></a>
    </div>
  </div>

</fieldset>
<?
}
  if($action == "leesDB")
  {
    $db=new DB();

    $query="SELECT Gebruiker FROM Gebruikers";
    $db->SQL($query);
    $db->Query();
    $filters=array();
    while($data=$db->nextRecord())
      $filters[]="_".$data['Gebruiker'];

    $filters[]='tmp';
    $filters[]='temp';
    $filters[]='_copy';
    $filters[]='dd_';

    $query="SHOW variables like 'datadir'";
    $db->SQL($query);
    $datadir=$db->lookupRecord();
    $datadir=$datadir['Value']."/".$_DB_resources[1]['db'];
    if(is_dir($datadir))
    {
      echo "<br>\ndatadir gevonden <br>\n";
       if ($dh = opendir($datadir))
       {
        while (($file = readdir($dh)) !== false)
        {
           $skip=false;
          foreach ($filters as $filter)
          {
            if(stristr($file,$filter))
               $skip=true;
          }
          if($skip==false)
          {
            $fullFile=$datadir."/".$file;
            $handle = fopen($fullFile, "r");
            if($handle == false)
               echo "Kan $file niet openen. <br>\n";
            else
            {
			  if($console==false)
                echo "$file geopend.<br>\n";
			  else
               echo "$file geopend.\n";			  
              while (!feof($handle))
              {
                 fread($handle, 1048576);
                 echo ".";
              }
			  if($console==false)
                echo "<br>\nfile $file gelezen<br>\n";
            }
          }
        }
        closedir($dh);
      }
      else
      {
        echo  'Geen leesrechten op mysqldir dir';
      }
    }
    else
    {
      echo "Kan mysql datadir niet openen.";
    }

  }
  
  if($action == "standaardVulling")
  {  
    $velden=array('debiteur'=>'Clienten','crediteur'=>'Leveranciers','prospect'=>'Prospects','overige'=>'Overige');
    $db=new DB();
    foreach($velden as $veld=>$omschrijving)
    {
      $query="SELECT id FROM CRM_eigenVelden WHERE veldnaam='$veld'"; 
      if($db->QRecords($query) < 1)
      {
        $query="INSERT INTO CRM_eigenVelden SET veldnaam='$veld',omschrijving='$omschrijving',veldtype='Checkbox', relatieSoort=1, add_user='SYS',change_user='SYS',add_date=now(),change_date=now()";
        $db->SQL($query);
	      $db->query();
        echo "In tabel CRM_eigenVelden is $veld al toegevoegd. <br>\n";
      }
      else
        echo "In tabel CRM_eigenVelden is $veld al aanwezig. <br>\n";
    }
  }
  
  if($action == "importdata")
  {
    $Bedrijf = $__appvar['bedrijf'];
    $db=new DB(2);
    $query="SELECT * FROM updates WHERE complete=2 order by exportId limit 1";
    $db->SQL($query);
    $queueData=$db->lookupRecord();
    if($queueData['exportId'] > 0)
    {
      $db = new DB();
      $query="SHOW tables like 'importdata'";
      if($db->QRecords($query) > 0)
      {
        $query = "SELECT count(*) as aantal FROM importdata WHERE Bedrijf = '" . $Bedrijf . "' AND exportId = '" . $queueData['exportId'] . "'";
        $db->SQL($query);
        $db->Query();
        $eersteAantal = $db->nextRecord();
        sleep(5);
        $db->SQL($query);
        $db->Query();
        $tweedeAantal = $db->nextRecord();
        if ($eersteAantal['aantal'] == $tweedeAantal['aantal'])
        {
          if($eersteAantal['aantal']>0)
          {
            echo "Import lijkt niet meer actief, import opnieuw gestart.<br>\n";
            $status = updateDataFromQueue($Bedrijf, $queueData['exportId'], $queue);
          }
          else
          {
            echo "Geen Records meer om te importeren.<br>\n";
          }
        }
        else
        {
          echo "Import actief ".round(($tweedeAantal['aantal']-$eersteAantal['aantal'])/5,0)." records per seconde. Nog ".$tweedeAantal['aantal']." Records te importeren.<br>\n";
        }
      }
      else
      {
        echo "Geen importdata tabel aanwezig. <br>\n";
      }
    }
    else
    {
      echo "Geen import actief. <br>\n";
    }
  }

// print templateFooter (met default vars)
if($console==false)
  echo template($__appvar["templateRefreshFooter"],$content);
?>