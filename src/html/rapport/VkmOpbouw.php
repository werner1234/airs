<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/09/29 16:18:57 $
File Versie					: $Revision: 1.1 $

$Log: VkmOpbouw.php,v $
Revision 1.1  2018/09/29 16:18:57  rvv
*** empty log message ***

Revision 1.42  2017/12/06 16:48:06  rvv
*** empty log message ***

Revision 1.41  2017/03/29 15:56:14  rvv
*** empty log message ***

Revision 1.40  2016/01/31 09:52:08  rvv
*** empty log message ***

Revision 1.39  2015/11/18 17:06:10  rvv
*** empty log message ***

Revision 1.38  2015/11/14 13:25:54  rvv
*** empty log message ***

Revision 1.37  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.36  2014/04/05 15:33:11  rvv
*** empty log message ***

Revision 1.35  2013/08/28 16:02:00  rvv
*** empty log message ***

Revision 1.34  2013/08/07 17:18:57  rvv
*** empty log message ***

Revision 1.33  2012/08/05 10:46:20  rvv
*** empty log message ***

Revision 1.32  2012/07/14 13:19:37  rvv
*** empty log message ***

Revision 1.31  2012/07/11 15:49:25  rvv
*** empty log message ***

Revision 1.30  2012/06/23 15:19:58  rvv
*** empty log message ***

Revision 1.29  2012/04/08 08:11:42  rvv
*** empty log message ***

Revision 1.28  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.27  2011/12/11 10:58:18  rvv
*** empty log message ***

Revision 1.26  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.25  2010/10/31 15:42:33  rvv
*** empty log message ***

Revision 1.24  2010/10/17 09:22:15  rvv
Gebruik van rapportagevaluta voor perf berekening.

Revision 1.23  2009/06/07 10:27:29  rvv
*** empty log message ***

Revision 1.22  2009/01/20 17:44:08  rvv
*** empty log message ***

Revision 1.21  2007/08/02 14:46:01  rvv
*** empty log message ***

Revision 1.20  2007/04/20 12:21:16  rvv
*** empty log message ***

Revision 1.19  2007/04/03 13:26:33  rvv
*** empty log message ***

Revision 1.18  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.17  2006/11/03 11:24:04  rvv
Na user update

Revision 1.16  2006/10/31 11:59:40  rvv
Voor user update

Revision 1.15  2006/09/20 14:24:21  rvv
vergelijking AEX uitgezet

Revision 1.14  2006/06/09 13:50:38  jwellner
*** empty log message ***

Revision 1.13  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.12  2006/01/13 15:46:51  jwellner
diverse aanpassingen

Revision 1.11  2005/12/09 13:28:51  jwellner
bugfix managementoverzicht

Revision 1.10  2005/12/08 13:55:21  jwellner
Modelcontrole rapport

Revision 1.9  2005/11/07 10:29:17  jwellner
no message

Revision 1.8  2005/10/14 16:17:56  jwellner
no message

Revision 1.7  2005/10/12 15:12:31  jwellner
fix in mysqlObject , change dat vullen bij nieuw record!

Revision 1.6  2005/09/12 09:10:42  jwellner
diverse aanpassingen / bugfixes gemeld in e-mails theo

Revision 1.5  2005/08/30 15:15:41  jwellner
no message

Revision 1.4  2005/08/26 07:16:27  jwellner
met snelle portefeuille waarde berekening

Revision 1.3  2005/08/24 10:42:55  jwellner
no message

Revision 1.2  2005/08/05 09:44:23  jwellner
no message

Revision 1.1  2005/08/03 14:15:44  jwellner
- FrontOffice aanpassingen
- BackOffice toegevoegd
- Facturatie Bugfix.
- Managementoverzicht


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class VkmOpbouw
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function VkmOpbouw($selectData)
	{
    global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "VkmOpbouw";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];


		if($this->selectData['manExtraVelden'] == 1)
		  $this->extraVeldenTonen=true;
		else
		  $this->extraVeldenTonen=false;

    $this->orderByVelden=array('Vermogensbeheerder','Accountmanager','Risicoklasse','SoortOvereenkomst');
    $this->lagen=array();
    foreach($this->orderByVelden as $veld)
    {
      if($this->selectData['orderby'.$veld] == 1 )
        $this->lagen[]=$veld;
    }
    
    if($this->selectData['orderbyVermogensbeheerder'] == 1 || $this->selectData['orderbyAccountmanager'] == 1 ||
       $this->selectData['orderbyRisicoklasse'] == 1 || $this->selectData['orderbySoortOvereenkomst'] == 1)
    {
      $this->orderby='';
      foreach($this->orderByVelden as $veld)
      {
        if($this->selectData['orderby'.$veld] == 1)
        {
          if($this->orderby != '')
            $this->orderby.=",";
          $this->orderby  .= " Portefeuilles.".$veld;
        }
      }
    }   
		else
		{
			$this->orderby  = " Clienten.Client ";
		}

		$query="SELECT Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen WHERE kosten=1";
		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		  $grootboeken[]=$data['Grootboekrekening'];

		$grootboekDb="`".implode("` DOUBLE NOT NULL,\n`",$grootboeken)."` DOUBLE NOT NULL,";

$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`inprocenttotaal` DOUBLE NOT NULL ,
`performance` DOUBLE NOT NULL ,
`resultaat` DOUBLE NOT NULL ,
`rendement` DOUBLE NOT NULL ,
`AFMstd` DOUBLE NOT NULL ,
$grootboekDb
`liquiditeiten` DOUBLE NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)
)";



	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	global $__appvar;
		$this->pdf->__appvar = $this->__appvar;

    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie(false);
    $this->afmTotalen=array();

		if($records <= 0)		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$rapportageDatum['a'] = jul2sql($this->selectData['datumVan']);
		$rapportageDatum['b'] = jul2sql($this->selectData['datumTm']);
		// vul eerst de tijdelijketabel
    
    
    
    $this->pdf->excelData[] = array("Naam","Portefeuille", "Profiel", "Soort overeenkomst", "Depotbank", "Gemiddeld vermogen","Rendement","Kosten","Doorl. Kosten","Trans. Kosten",'Perf. Fee','Totaal Kosten','VKM');
    
    $this->pdf->AddPage();
    
		foreach($portefeuilles as $pdata)
		{
      foreach($this->orderByVelden as $veld)
      {
        if(trim($pdata[$veld])=='')
          $pdata[$veld]='leeg';
      }

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']." ");
			}

			$portefeuille = $pdata['Portefeuille'];
			if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}
			$julrapport 		= db2jul($startdatum);
			$rapportMaand 	= date("m",$julrapport);
			$rapportDag 		= date("d",$julrapport);

			if($rapportMaand == 1 && $rapportDag == 1)
				$startjaar = true;
			else
				$startjaar = false;

			$einddatum = $rapportageDatum['b'];

			$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,  $startdatum, $startjaar,'EUR',$startdatum);
			$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille,  $einddatum , false     ,'EUR',$startdatum);

			vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startdatum);
			vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$einddatum);

			// tel totaal op!
			$DB2 = new DB();

  		$this->pdf->SetFont("Times","",10);


			$this->tel ++;
			$portefeuille = $pdata['Portefeuille'];

			if(db2jul($rapportageDatum[a]) < db2jul($pdata['Startdatum']))
			{
				$startdatum = $pdata['Startdatum'];
			}
			else
			{
				$startdatum = $rapportageDatum['a'];
			}

			$einddatum = $rapportageDatum['b'];


      //echo "performanceMeting($portefeuille, $startdatum, $einddatum, ".$pdata['PerformanceBerekening'].",".$pdata['RapportageValuta'].");";
      if($pdata['RapportageValuta'] =='')
        $pdata['RapportageValuta']='EUR';
			$performance 			  = performanceMeting($portefeuille, $startdatum, $einddatum, $pdata['PerformanceBerekening'], $pdata['RapportageValuta']);


      $vkm=new RapportVKM(null,$portefeuille,$einddatum,$einddatum);
      $vkm->writeRapport();
      $data['vkmDoorlKst'] = round($vkm->vkmWaarde['vkmPercentagePortefeuille'],2);
      $data['vkmDirK'] = round($vkm->vkmWaarde['kostenPercentage'],2);
      $data['vkm'] = round($vkm->vkmWaarde['vkmWaarde'],2);
      
      

      $this->pdf->Row(array($pdata['Naam'],$pdata['Portefeuille'],$pdata['Risicoklasse'],$pdata['SoortOvereenkomst'],$pdata['Depotbank'],
                   $this->formatGetal($vkm->vkmWaarde['gemiddeldeWaarde'],0),
                   $this->formatGetal($performance,2),
                   $this->formatGetal($vkm->vkmWaarde['grootBoekKostenTotaal'],0),
                   $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekosten'],0),
                   $this->formatGetal($vkm->vkmWaarde['FundTransCost'],0),
                   $this->formatGetal($vkm->vkmWaarde['FundPerfFee'],0),
                   $this->formatGetal($vkm->vkmWaarde['vkmWaarde']*$vkm->vkmWaarde['gemiddeldeWaarde']*.01,0),
                   $this->formatGetal($vkm->vkmWaarde['vkmWaarde'],2)));

		  	$this->pdf->excelData[] = array($pdata['Naam'],$pdata['Portefeuille'],$pdata['Risicoklasse'],$pdata['SoortOvereenkomst'],$pdata['Depotbank'],
          round($vkm->vkmWaarde['gemiddeldeWaarde'],0),
          round($performance,2),
          round($vkm->vkmWaarde['grootBoekKostenTotaal'],2),
          round($vkm->vkmWaarde['totaalDoorlopendekosten'],2),
          round($vkm->vkmWaarde['FundTransCost'],2),
          round($vkm->vkmWaarde['FundPerfFee'],2),
          round($vkm->vkmWaarde['vkmWaarde']*$vkm->vkmWaarde['gemiddeldeWaarde']*.01,2),
          round($vkm->vkmWaarde['vkmWaarde'],2)
					);

    if($this->selectData['filetype']=='database')
    {
	$this->dbWaarden[]=$tmparray;
     }

			verwijderTijdelijkeTabel($portefeuille,$startdatum);

		}

  
		$this->pdf->SetFont("Times","b",10);

		$this->pdf->SetFont("Times","",10);
		if($this->progressbar)
			$this->progressbar->hide();
	}
  
  

	function OutputDatabase()
	{
	  global $USR;
	  $db=new DB();
	  $table="reportbuilder_$USR";
	  $query="SHOW TABLES like '$table'";
	  if($db->QRecords($query) > 0)
	  {
	    $db->SQL("DROP table $table");
	    $db->Query();
	  }
    if($this->dbTable)
    {
      $db->SQL($this->dbTable);
	    $db->Query();
	    $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }
    if(is_array($this->dbWaarden))
    {
      foreach ($this->dbWaarden as $rege=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          $query.=",`$key`='".addslashes($value)."' ";
        }
        $db->SQL($query);
	      $db->Query();
      }
    }

	}
}
?>