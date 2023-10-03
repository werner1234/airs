<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/11 16:20:55 $
File Versie					: $Revision: 1.87 $

$Log: Zorgplichtcontrole.php,v $
Revision 1.87  2020/03/11 16:20:55  rvv
*** empty log message ***

Revision 1.86  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.85  2019/10/23 13:32:07  rvv
*** empty log message ***

Revision 1.84  2019/09/11 15:50:19  rvv
*** empty log message ***

Revision 1.83  2018/09/12 14:48:38  rvv
*** empty log message ***

Revision 1.82  2018/08/29 12:20:25  rvv
*** empty log message ***

Revision 1.81  2018/08/22 14:28:02  rvv
*** empty log message ***

Revision 1.80  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.79  2018/06/17 15:52:49  rvv
*** empty log message ***

Revision 1.78  2018/03/28 15:54:43  rvv
*** empty log message ***

Revision 1.76  2018/02/04 15:46:22  rvv
*** empty log message ***

Revision 1.75  2017/07/01 17:05:07  rvv
*** empty log message ***

Revision 1.74  2017/04/05 15:39:02  rvv
*** empty log message ***

Revision 1.73  2016/10/09 14:43:49  rvv
*** empty log message ***

Revision 1.72  2016/07/02 09:36:03  rvv
*** empty log message ***

Revision 1.71  2016/06/19 15:23:27  rvv
*** empty log message ***

Revision 1.70  2015/12/21 18:31:26  rvv
*** empty log message ***

Revision 1.69  2015/12/21 08:51:45  rvv
*** empty log message ***

Revision 1.68  2015/12/19 09:11:58  rvv
*** empty log message ***

Revision 1.67  2015/12/17 07:20:38  rvv
*** empty log message ***

Revision 1.66  2015/12/16 17:04:53  rvv
*** empty log message ***

Revision 1.65  2015/11/29 13:12:20  rvv
*** empty log message ***

Revision 1.64  2015/11/25 16:45:01  rvv
*** empty log message ***

Revision 1.63  2015/11/14 13:25:54  rvv
*** empty log message ***

Revision 1.62  2015/11/12 08:37:10  rvv
*** empty log message ***

Revision 1.61  2015/11/12 07:46:18  rvv
*** empty log message ***

Revision 1.60  2015/11/11 17:21:56  rvv
*** empty log message ***

Revision 1.59  2015/04/06 20:05:59  rvv
*** empty log message ***

Revision 1.58  2015/04/06 19:59:33  rvv
*** empty log message ***

Revision 1.57  2015/04/06 19:43:18  rvv
*** empty log message ***

Revision 1.56  2015/04/04 15:14:38  rvv
*** empty log message ***

Revision 1.55  2015/01/31 20:00:33  rvv
*** empty log message ***

Revision 1.54  2014/12/21 10:32:26  rvv
*** empty log message ***

Revision 1.53  2014/08/06 15:39:52  rvv
*** empty log message ***

Revision 1.52  2014/03/22 15:47:44  rvv
*** empty log message ***

Revision 1.51  2014/03/19 16:34:55  rvv
*** empty log message ***

Revision 1.50  2013/11/23 17:22:11  rvv
*** empty log message ***

Revision 1.49  2013/11/02 17:03:13  rvv
*** empty log message ***

Revision 1.48  2013/10/05 15:57:41  rvv
*** empty log message ***

Revision 1.47  2013/09/07 16:00:33  rvv
*** empty log message ***

Revision 1.46  2013/08/28 16:02:00  rvv
*** empty log message ***

Revision 1.45  2013/08/07 17:18:57  rvv
*** empty log message ***

Revision 1.44  2013/07/20 16:25:21  rvv
*** empty log message ***

Revision 1.43  2013/07/17 15:52:10  rvv
*** empty log message ***

Revision 1.42  2013/04/17 11:35:08  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
global $__appvar;
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");
include_once($__appvar["basedir"]."/config/rapportage.php");//rvv 29-08-06

class Zorgplichtcontrole
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Zorgplichtcontrole( $selectData )
	{
    global $USR;
		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "zorgplichtcontrole";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];

		$this->orderby  = " Portefeuilles.ClientVermogensbeheerder ";

		$this->pdf->excelData = array();
		$this->dbWaarden2=array();
		//rvv
		loadLayoutSettings($this->pdf, $this->selectData['portefeuilleVan']); //rvv 29-08-06
    $this->TijdelijkUitsluitenZpConversie=array(0=>'Niet uitsluiten',1=>'Geheel uitsluiten',2=>'Tijdelijk akkoord');

		$this->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`Risicoklasse` VARCHAR( 50 )  NOT NULL ,
`Conclusie` VARCHAR( 50 )  NOT NULL ,
`Reden` VARCHAR( 255 )  NOT NULL ,
`norm` VARCHAR( 255 )  NOT NULL ,
`TijdelijkUitsluitenZp` VARCHAR( 50 )  NOT NULL ,
`add_date` datetime ,
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)
)";

		$this->dbTable2="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`Vermogensbeheerder` VARCHAR( 10 ) NOT NULL ,
`Client` VARCHAR( 16 ) NOT NULL ,
`Naam` VARCHAR( 50 ) NOT NULL ,
`Naam1` VARCHAR( 50 ) NOT NULL ,
`totaalvermogen` DOUBLE NOT NULL ,
`Risicoklasse` VARCHAR( 50 )  NOT NULL ,
`Conclusie` VARCHAR( 50 )  NOT NULL ,
`Reden` VARCHAR( 255 )  NOT NULL ,
`norm` VARCHAR( 255 )  NOT NULL ,
`TijdelijkUitsluitenZp` VARCHAR( 50 )  NOT NULL ,
`add_date` datetime ,
";

	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function risicoPercentage($portefeuille, $einddatum)
	{
		global $__appvar;
		// haal totaalwaarde op om % te berekenen
		$DB = new DB();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$einddatum."' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT Beleggingscategorien.Omschrijving, ".
		" Beleggingscategorien.RisicoEUR, ".
		" Beleggingscategorien.RisicoVV, ".
		" Valutas.Omschrijving AS ValutaOmschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.actueleValuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta)  ".
		" LEFT JOIN Beleggingscategorien on (TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$einddatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY Beleggingscategorien.Afdrukvolgorde asc, Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $risicoTotaal=0;
		while($categorien = $DB->NextRecord())
		{
			if($categorien['valuta'] == "EUR")
				$risico = $categorien['RisicoEUR'];
			else
				$risico = $categorien['RisicoVV'];

			$risicoBedrag = ($categorien['subtotaalactueel'] / 100) * $risico;
			$risicoTotaal += $risicoBedrag;
		}

		// print risico score
		$risicoScore = $risicoTotaal / ($totaalWaarde/100);

		return $risicoScore;
	}

	function writeRapport()
	{
		global $__appvar;
		$einddatum = jul2sql($this->selectData['datumTm']);

		$this->pdf->__appvar = $this->__appvar;

		$fondswaardenClean = array();
		$fondswaardenRente = array();
		$rekeningwaarden 	 = array();

		$jaar = date("Y",$this->datumTm);

		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();


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

		$this->pdf->AddPage();
    
    $preExcel=array();
    $preExcelCategorien=array();

		// print CSV kop
		$this->pdf->excelData[] = array("Client",
												"Naam",
                        "Portefeuille",
                        "Accountmanager",
                        'Soort overeenkomst',
                        'Risicoprofiel',
												"Depotbank",
												"Totale waarde portefeuille",
												"Risicoklasse",
												"Conclusie",
												"Reden(en) niet voldoen","Wegingen II","Wegingen Norm",'TijdelijkUitsluitenZp');

		$db=new DB();
		foreach($portefeuilles as $pdata)
		{
		  if($pdata['TijdelijkUitsluitenZp']==1 && $this->selectData['tijdelijkUitsluiten']==1)
        continue; 
        
		  if($pdata['ZpMethode']==0 && $this->selectData['ZorgMethodeFilter']=='contractueel')
        continue;    

      if($this->selectData['ZorgMethodeFilter'] <> 'alles')
      {
        if($pdata['ZpMethode']==0 && $this->selectData['ZorgMethodeFilter']!='leeg')
          continue;
        
		    if($pdata['ZpMethode']==1 && $this->selectData['ZorgMethodeFilter']!='aandelen')
          continue;  
        
		    if($pdata['ZpMethode']==2 && $this->selectData['ZorgMethodeFilter']!='afm')
          continue;

				if($pdata['ZpMethode']==2 && $this->selectData['ZorgMethodeFilter']!='stdev')
					continue;
			}
     // echo "|".$pdata['ZpMethode']."|";
			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				logScherm("Portefeuille: ".$pdata['Portefeuille']."");
			}
			// set portefeuillenr
			// load settings.
			$portefeuille = $pdata['Portefeuille'];
			$this->pdf->portefeuille = $pdata['Portefeuille'];
			
			if($this->selectData['zorgDoorkijk']==1)
			{
				include_once('PDFRapport.php');
				$pdf = new PDFRapport('L','mm');
				$pdf->SetAutoPageBreak(true,15);
				$pdf->__appvar = $__appvar;
				$pdf->rapport_datumvanaf = db2jul(jul2sql($this->selectData['datumVan']));
				$pdf->rapport_datum = db2jul($einddatum);
				$pdf->rapportageValuta = "EUR";
				$pdf->ValutaKoersEind  = 1;
				$pdf->ValutaKoersStart = 1;
				$pdf->ValutaKoersBegin = 1;
				$fondswaarden = bepaaldFondsWaardenVerdiept($pdf, $portefeuille, $einddatum);
			}
			else
			{
				$fondswaarden = berekenPortefeuilleWaarde($portefeuille, $einddatum);
			}
			vulTijdelijkeTabel($fondswaarden ,$portefeuille, $einddatum);
			runPreProcessor($portefeuille);

      if($this->selectData['ZpMethodeKeuze']=='afm' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==2))
        $zorgPlichtResultaat=$this->standaarddeviatieMeting($pdata,$einddatum);
			elseif($this->selectData['ZpMethodeKeuze']=='aandelen' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==1))
				$zorgPlichtResultaat=$this->zorgplichtMeting($pdata,$einddatum);
			elseif($this->selectData['ZpMethodeKeuze']=='stdev' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==3))
				$zorgPlichtResultaat=$this->werkelijkeStandaarddeviatieMeting($pdata,$einddatum);
			elseif($pdata['ZpMethode']==0)
				$zorgPlichtResultaat=array('zorgMetingReden'=>'geen parameters');
      else
        $zorgPlichtResultaat=array('zorgMetingReden'=>'niet uitgevoerd');

			$zorgPlichtModelResultaat=array();
			if($pdata['ModelPortefeuille'] <> '')
			{
				$db=new DB();
				$query="SELECT Portefeuilles.Portefeuille,Portefeuilles.Vermogensbeheerder,ModelPortefeuilles.Fixed,ModelPortefeuilles.Beleggingscategorie 
FROM Portefeuilles JOIN  ModelPortefeuilles ON Portefeuilles.Portefeuille=ModelPortefeuilles.Portefeuille 
WHERE Portefeuilles.Portefeuille='".mysql_real_escape_string($pdata['ModelPortefeuille']) ."'";
				$db->SQL($query);
				$modelPdata=$db->lookupRecord();

				if($modelPdata['Fixed']==1)
					$fondswaarden = berekenFixedModelPortefeuille($modelPdata['Portefeuille'],$einddatum);
				elseif($modelPdata['Fixed']==3)
					$fondswaarden = berekenMeervoudigeModelPortefeuille($modelPdata['Portefeuille'], $einddatum,$pdata['ModelPortefeuille']);
				else
			  	$fondswaarden =  berekenPortefeuilleWaarde($modelPdata['Portefeuille'],  $einddatum);
				vulTijdelijkeTabel($fondswaarden ,$modelPdata['Portefeuille'], $einddatum);
				runPreProcessor($modelPdata['Portefeuille']);

				if($this->selectData['ZpMethodeKeuze']=='afm' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==2))
					$zorgPlichtModelResultaat=$this->standaarddeviatieMeting($modelPdata,$einddatum);
				elseif($this->selectData['ZpMethodeKeuze']=='aandelen' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==1))
					$zorgPlichtModelResultaat=$this->zorgplichtMeting($modelPdata,$einddatum);
				elseif($this->selectData['ZpMethodeKeuze']=='stdev' || ($this->selectData['ZpMethodeKeuze']=='contractueel' && $pdata['ZpMethode']==3))
					$zorgPlichtModelResultaat=$this->werkelijkeStandaarddeviatieMeting($modelPdata,$einddatum);
				elseif($pdata['ZpMethode']==0)
					$zorgPlichtModelResultaat=array('zorgMetingReden'=>'geen parameters');
				else
					$zorgPlichtModelResultaat=array('zorgMetingReden'=>'niet uitgevoerd');

			}


      if(!isset($this->selectData['zorgplichtVoldoetNiet']) || trim($zorgPlichtResultaat['zorgMeting']) == 'Voldoet niet')
      {
        
        if($this->selectData['zorgplichtVoldoetNietCategorie']==1)
          $zorgPlichtResultaat['zorgMetingReden']=$zorgPlichtResultaat['voldoetNietReden'];

      if(strlen($pdata['Naam']) > 30)
        $pdata['Naam']=substr($pdata['Naam'],0,30).'...';

      $regels=substr_count($zorgPlichtResultaat['zorgMetingReden'],"\n");
      if($regels*$this->pdf->rowHeight+$this->pdf->GetY() > $this->pdf->PageBreakTrigger)
        $this->pdf->AddPage();


 			$this->pdf->Cell(30 , 4 , $pdata['Client'] , 0, 0, "L");
			$this->pdf->Cell(55,  4 , $pdata['Naam'] , 0, 0, "L");
			$this->pdf->Cell(20,  4 , $pdata['Depotbank'] , 0, 0, "L");
			$this->pdf->Cell(30 , 4 , $pdata['Portefeuille'] , 0, 0, "L");
			$this->pdf->Cell(25 , 4 , $this->formatGetal($zorgPlichtResultaat['totaalWaarde'],2) , 0, 0, "R");
			$this->pdf->Cell(25 , 4 , $pdata['Risicoklasse'] , 0, 0, "L");
			$this->pdf->Cell(20 , 4 , $zorgPlichtResultaat['zorgMeting'] , 0, 0, "R");
      $xBegin=$this->pdf->GetX();
      $yBegin=$this->pdf->GetY();
			$this->pdf->MultiCell(70 , 4 , $zorgPlichtResultaat['zorgMetingReden'],0, "L",false);
      $xEind=$this->pdf->GetX();
      $yEind=$this->pdf->GetY();
      $this->pdf->SetXY($xBegin+50,$yBegin);
      $normTxt='';
      $categorienFound=array();
      foreach($zorgPlichtResultaat['categorien'] as $cat=>$catData)
      {
        if((strpos($zorgPlichtResultaat['zorgMetingReden'],$cat.'=')!==false || 
            strpos($zorgPlichtResultaat['zorgMetingReden'],$cat.' ')!==false )&& !in_array($cat,$categorienFound))
        {
          $normTxt.=substr($cat,0,4).'='.$this->formatGetal($catData['Norm'])."%\n";
          $categorienFound[]=$cat;
        }  
      }
      //$this->pdf->Cell(20 , 4 ,$normTxt, 0, 1, "L");
      $this->pdf->MultiCell(25 , 4 , $normTxt,0, "L",false);
      $this->pdf->SetXY($xEind,$yEind);
  
      
      $xlsUitvoer='';
      ksort($zorgPlichtResultaat['categorien']);
      foreach($zorgPlichtResultaat['categorien'] as $categorie=>$categorieData)
      {
        
        if($categorieData['fondsGekoppeld']==0)
        {
          $categorieFound=false;
          foreach($zorgPlichtResultaat['conclusie'] as $conclusieData)
          {
            if($categorie==$conclusieData[0])
            {
              $categorieFound=true;
              $xlsUitvoer.=$conclusieData[4].";";
              break;
            }
          }
          if($categorieFound==false)
            $xlsUitvoer.=" ;";
        }  
      }
      //listarray($zorgPlichtResultaat);
      $xlsUitvoerNorm='';
      foreach($zorgPlichtResultaat['conclusieDetail'] as $categorie=>$categorieData)
      {   
        $xlsUitvoerNorm.=$categorie.'='.$categorieData['norm'].';';
        $preExcelCategorien[$categorie]=$categorie;
      }
			foreach($zorgPlichtResultaat['conclusieDetailFonds'] as $categorie=>$categorieData)
			{
				$preExcelCategorien[$categorie]=$categorie;
				$zorgPlichtResultaat['conclusieDetail'][$categorie]['percentage']=$categorieData['percentage'];
			}

      $preExcel[$pdata['Portefeuille']]['details']=$zorgPlichtResultaat['conclusieDetail'];
			foreach($zorgPlichtModelResultaat['conclusieDetail'] as $categorie=>$categorieData)
			{
				$preExcel[$pdata['Portefeuille']]['details'][$categorie]['percentageModel'] = $zorgPlichtModelResultaat['conclusieDetail'][$categorie]['percentage'];
				$preExcel[$pdata['Portefeuille']]['details'][$categorie]['afwijkingModel'] = $zorgPlichtModelResultaat['conclusieDetail'][$categorie]['percentage']-$zorgPlichtResultaat['conclusieDetail'][$categorie]['percentage'];
			}

      $preExcel[$pdata['Portefeuille']]['begin']=array($pdata['Client'],
													$pdata['Naam'],
                          $pdata['Portefeuille'],
                          $pdata['Accountmanager'],
                          $pdata['SoortOvereenkomst'], 
                          $pdata['Risicoklasse'],
													$pdata['Depotbank'],
													round($zorgPlichtResultaat['totaalWaarde'],2),
													$pdata['Risicoklasse'],
													$zorgPlichtResultaat['zorgMeting'],
													$zorgPlichtResultaat['zorgMetingReden'],
                          str_replace("\n"," ",$xlsUitvoer),
                          $xlsUitvoerNorm,
				$this->TijdelijkUitsluitenZpConversie[$pdata['TijdelijkUitsluitenZp']]
				);

				$preExcel[$pdata['Portefeuille']]['db']=array('Rapport'=>'Management',
																											'Client' => $pdata['Client'],
																											'Naam'=>$pdata['Naam'],
																											'Naam1'=>$pdata['Naam1'],
																											'Portefeuille'=>$pdata['Portefeuille'],
																											'Vermogensbeheerder'=>$pdata['Vermogensbeheerder'],
																											'totaalvermogen'=>round($zorgPlichtResultaat['totaalWaarde'],2),
																											'Risicoklasse'=>$pdata['Risicoklasse'],
																											'Conclusie'=>$zorgPlichtResultaat['zorgMeting'],
																											'Reden'=>$zorgPlichtResultaat['zorgMetingReden'],
																											'norm'=>$xlsUitvoerNorm,
					'TijdelijkUitsluitenZp'=>$this->TijdelijkUitsluitenZpConversie[$pdata['TijdelijkUitsluitenZp']]);

				$this->dbWaarden[]=array('Rapport'=>'Management',
																 'Client' => $pdata['Client'],
																 'Naam'=>$pdata['Naam'],
																 'Naam1'=>$pdata['Naam1'],
																 'Portefeuille'=>$pdata['Portefeuille'],
																 'Vermogensbeheerder'=>$pdata['Vermogensbeheerder'],
																 'totaalvermogen'=>round($zorgPlichtResultaat['totaalWaarde'],2),
																 'Risicoklasse'=>$pdata['Risicoklasse'],
																 'Conclusie'=>$zorgPlichtResultaat['zorgMeting'],
																 'Reden'=>$zorgPlichtResultaat['zorgMetingReden'],
																 'norm'=>$xlsUitvoerNorm,
                                 'TijdelijkUitsluitenZp'=>$this->TijdelijkUitsluitenZpConversie[$pdata['TijdelijkUitsluitenZp']]);

			$this->pdf->excelData[] = array($pdata['Client'],
													$pdata['Naam'],
                          $pdata['Portefeuille'],
                          $pdata['Accountmanager'],
                          $pdata['SoortOvereenkomst'], 
                          $pdata['Risicoklasse'],
													$pdata['Depotbank'],
													round($zorgPlichtResultaat['totaalWaarde'],2),
													$pdata['Risicoklasse'],
													$zorgPlichtResultaat['zorgMeting'],
													$zorgPlichtResultaat['zorgMetingReden'],
                          str_replace("\n"," ",$xlsUitvoer),
                          $xlsUitvoerNorm,
				$this->TijdelijkUitsluitenZpConversie[$pdata['TijdelijkUitsluitenZp']]);


       }
       
//			verwijderTijdelijkeTabel($portefeuille, $this->selectData[datumTm]); echo $portefeuille . "  ".$this->selectData[datumTm]; exit();
			//verwijderTijdelijkeTabel($portefeuille, $einddatum);
			
		}

       sort($preExcelCategorien);
       $this->pdf->excelData=array();
       $header=array("Client",
												"Naam",
                        "Portefeuille",
                        "Accountmanager",
                        'Soort overeenkomst',
                        'Risicoprofiel',
												"Depotbank",
												"Totale waarde portefeuille",
												"Risicoklasse",
												"Conclusie",
												"Reden(en) niet voldoen","Wegingen II","Wegingen Norm",'TijdelijkUitsluitenZp');
        $categorieDataVelden=array('minimum'=>'Min','norm'=>'Norm','maximum'=>'Max','percentageModel'=>'Model','percentage'=>'Werk','afwijking'=>'Afw.Werk','afwijkingModel'=>'Afw.Model');
        foreach($preExcelCategorien as $categorie)
        { 
          foreach($categorieDataVelden as $veld)
            $header[]=$categorie.' '.$veld;
        }
                          
        $this->pdf->excelData[] = $header;
		    $n=0;
        foreach($preExcel as $portefeuille=>$portefeuilleData)
        {
          $row=$portefeuilleData['begin'];
					$dbRow=$portefeuilleData['db'];
          foreach($preExcelCategorien as $categorie)
          {
            foreach($categorieDataVelden as $veld=>$veldOmschrijving)
						{
							$row[] = $portefeuilleData['details'][$categorie][$veld];
							$veldNaam=str_replace(' ','_',str_replace('.','',$categorie.'_'.$veld));
							$dbRow[$veldNaam]=$portefeuilleData['details'][$categorie][$veld];
							if($n==0)
							  $this->dbTable2.=" `$veldNaam` VARCHAR( 255 )  NOT NULL ,\n";
						}
          }
					$this->dbWaarden2[]=$dbRow;
          $this->pdf->excelData[] = $row;
          $n++;
        }

		if($this->progressbar)
			$this->progressbar->hide();
	}

	function werkelijkeStandaarddeviatieMeting($pdata,$einddatum)
	{
		global $__appvar;
		include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");

		$zorgMeting = "Voldoet ";
		$zorgMetingReden = "";
		$voldoetNietReden = '';
		$portefeuille = $pdata['Portefeuille'];
		$DB3 = new DB();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal,type ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$einddatum."' AND ".
			" portefeuille = '".$portefeuille."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY type";
		debugSpecial($query,__FILE__,__LINE__);
		$DB3->SQL($query);
		$DB3->Query();
		$totaalWaarde=0;
		$liqWaarde=0;
		while($data = $DB3->nextRecord())
		{
			if($data['type']=='rekening')
				$liqWaarde += $data['totaal'];
			$totaalWaarde += $data['totaal'];
		}

		$stdev=new rapportSDberekening($portefeuille,$einddatum);
		$stdev->addReeks('totaal');
		$stdev->berekenWaarden();
		$stdevOutput=$stdev->getUitvoer();
		$stdevWaarde=$stdevOutput['totaal'];


		$query = "SELECT Portefeuilles.Risicoklasse,
                       StandaarddeviatiePerRisicoklasse.Minimum,
                       StandaarddeviatiePerRisicoklasse.Maximum,
                       StandaarddeviatiePerRisicoklasse.Norm,
                       StandaarddeviatiePerRisicoklasse.debetNietToestaan
                FROM Portefeuilles
                Join StandaarddeviatiePerRisicoklasse ON Portefeuilles.Risicoklasse = StandaarddeviatiePerRisicoklasse.Risicoklasse AND
                     StandaarddeviatiePerRisicoklasse.Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."'
                WHERE Portefeuilles.Portefeuille= '".$portefeuille."' ";
		$DB3->SQL($query);
		$DB3->Query();
		$zpdata = $DB3->nextRecord();

		$query = "SELECT Minimum, Maximum, Norm
                FROM StandaarddeviatiePerPortefeuille
                WHERE StandaarddeviatiePerPortefeuille.Portefeuille= '".$portefeuille."' ";
		$DB3->SQL($query);
		$DB3->Query();

		if($DB3->records())
		{
			$tmp = $DB3->nextRecord();
			foreach($tmp as $key=>$value)
				$zpdata[$key]=$value;
		}

		$conclusie=array();
		$txt2='Voldoet';
		$txt='';
		//$zorgtotaal = $waardePerZorgplicht[$zpdata['Zorgplicht']];
		$zpdata['Zorgplicht']='stdev';
		$zorgPercentage = round($stdevWaarde,4);

		if($zorgPercentage < $zpdata['Minimum'] || $zorgPercentage > $zpdata['Maximum'])
		{
			$txt2='Voldoet niet';
			$zorgMeting = "Voldoet niet ";
			if($zorgPercentage < $zpdata['Minimum'])
			{
				$txt = $zpdata['Zorgplicht']." < ".$zpdata['Minimum']." % : ".round($zorgPercentage,2)." %\n";
				if(!isset($zpdata['fondsGekoppeld']))
				{
					$afwijking=$zorgPercentage-$zpdata['Minimum'];
					$zorgMetingReden .= $txt;
					$voldoetNietReden .= $txt;
				}
			}
			else
			{
				$txt = $zpdata['Zorgplicht']." > ".$zpdata['Maximum']." % : ".round($zorgPercentage,2)." %\n";
				if(!isset($zpdata['fondsGekoppeld']))
				{
					$afwijking=$zorgPercentage-$zpdata['Maximum'];
					$zorgMetingReden .= $txt;
					$voldoetNietReden .= $txt;
				}
			}
		}
		else
		{
			$txt=$zpdata['Zorgplicht']."=".round($zorgPercentage,2)." %\n";
			if(!isset($zpdata['fondsGekoppeld']))
				$zorgMetingReden .= $txt;
		}

		if($zpdata['debetNietToestaan']==1 && $liqWaarde<0)
		{
			$zorgMeting = "Voldoet niet";
			$txt2='Voldoet niet';
			$zorgMetingReden .=" Liquiditeiten negatief\n";
			$voldoetNietReden .=" Liquiditeiten negatief\n";
		}
		$conclusieDetail['stdev']=array('percentage'=>$zorgPercentage,
																	'minimum'=>$zpdata['Minimum'],
																	'maximum'=>$zpdata['Maximum'],
																	'norm'=>$zpdata['Norm'],
																	'afwijking'=>$afwijking);


		$conclusie[]=array($zpdata['Zorgplicht'],$zpdata['Minimum'].' < x < '.$zpdata['Maximum'], $this->formatGetal($zorgPercentage,1),$this->formatGetal($zorgtotaal,2),$txt,$txt2);

		if($zorgMeting=="Voldoet ")
			$voldoet="Ja";
		else
			$voldoet="Nee";

		return array('totaalWaarde'=>$totaalWaarde,'zorgMeting'=>$zorgMeting,'zorgMetingReden'=>$zorgMetingReden,'voldoetNietReden'=>$voldoetNietReden,'voldoet'=>$voldoet,'detail'=>$waardePerFonds,'categorien'=>$zpCategorien,'conclusie'=>$conclusie,'conclusieDetail'=>$conclusieDetail);

	}

  function standaarddeviatieMeting($pdata,$einddatum)
	{
	  global $__appvar;
	  	$zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
      $vodoetNietReden = '';
	    $portefeuille = $pdata['Portefeuille'];
	  	$DB3 = new DB();
	  	$DB4 = new DB();
			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal,type ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							  .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY type";
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);
			$DB3->Query();
      $totaalWaarde=0;
      $liqWaarde=0;
      while($data = $DB3->nextRecord())
      {
        if($data['type']=='rekening')
          $liqWaarde += $data['totaal'];
	  		$totaalWaarde += $data['totaal'];
      }
      $afm=AFMstd($portefeuille,$einddatum,$this->pdf->debug);
        
			$query = "SELECT Portefeuilles.Risicoklasse,
                       StandaarddeviatiePerRisicoklasse.Minimum,
                       StandaarddeviatiePerRisicoklasse.Maximum,
                       StandaarddeviatiePerRisicoklasse.Norm,
                       StandaarddeviatiePerRisicoklasse.debetNietToestaan
                FROM Portefeuilles
                Join StandaarddeviatiePerRisicoklasse ON Portefeuilles.Risicoklasse = StandaarddeviatiePerRisicoklasse.Risicoklasse AND
                     StandaarddeviatiePerRisicoklasse.Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."'
                WHERE Portefeuilles.Portefeuille= '".$portefeuille."' ";
			$DB3->SQL($query);
			$DB3->Query();
			$zpdata = $DB3->nextRecord();
      
     $query = "SELECT Minimum, Maximum, Norm
                FROM StandaarddeviatiePerPortefeuille
                WHERE StandaarddeviatiePerPortefeuille.Portefeuille= '".$portefeuille."' ";
			$DB3->SQL($query);
			$DB3->Query();

      if($DB3->records())
      { 
        $tmp = $DB3->nextRecord(); 
        foreach($tmp as $key=>$value)
          $zpdata[$key]=$value;
      }

			$conclusie=array();
		  $txt2='Voldoet';
		  $txt='';
			//$zorgtotaal = $waardePerZorgplicht[$zpdata['Zorgplicht']]; 
      $zpdata['Zorgplicht']='AFM stdev';
			$zorgPercentage = round($afm['std'],4);

					if($zorgPercentage < $zpdata['Minimum'] || $zorgPercentage > $zpdata['Maximum'])
					{
					  $txt2='Voldoet niet';
						$zorgMeting = "Voldoet niet ";
						if($zorgPercentage < $zpdata['Minimum'])
						{
						  $txt = $zpdata['Zorgplicht']." < ".$zpdata['Minimum']." % : ".round($zorgPercentage,2)." %\n";
						  if(!isset($zpdata['fondsGekoppeld']))
              {
                $afwijking=$zorgPercentage-$zpdata['Minimum'];
							  $zorgMetingReden .= $txt;
                $voldoetNietReden .= $txt;
              }
						}
						else
						{
						  $txt = $zpdata['Zorgplicht']." > ".$zpdata['Maximum']." % : ".round($zorgPercentage,2)." %\n";
						  if(!isset($zpdata['fondsGekoppeld']))
              {
                $afwijking=$zorgPercentage-$zpdata['Maximum'];
							  $zorgMetingReden .= $txt;
                $voldoetNietReden .= $txt;
              }
						}
					}
					else
					{
					  $txt=$zpdata['Zorgplicht']."=".round($zorgPercentage,2)." %\n";
					  if(!isset($zpdata['fondsGekoppeld']))
						  $zorgMetingReden .= $txt;
					}
  
			    if($zpdata['debetNietToestaan']==1 && $liqWaarde<0)
          {
            $zorgMeting = "Voldoet niet";
            $txt2='Voldoet niet';
            $zorgMetingReden .=" Liquiditeiten negatief\n";
            $voldoetNietReden .=" Liquiditeiten negatief\n";
          }
          $conclusieDetail['AFM']=array('percentage'=>$zorgPercentage,
                                                      'minimum'=>$zpdata['Minimum'],
                                                      'maximum'=>$zpdata['Maximum'],
                                                      'norm'=>$zpdata['Norm'],
                                                      'afwijking'=>$afwijking);
                                                      

		    $conclusie[]=array($zpdata['Zorgplicht'],$zpdata['Minimum'].' < x < '.$zpdata['Maximum'], $this->formatGetal($zorgPercentage,1),$this->formatGetal($zorgtotaal,2),$txt,$txt2);
		    $zorgPercentage=0;
		    $zorgtotaal=0;



			if($zorgMeting=="Voldoet ")
			  $voldoet="Ja";
			else
			  $voldoet="Nee";

			return array('totaalWaarde'=>$totaalWaarde,'zorgMeting'=>$zorgMeting,'zorgMetingReden'=>$zorgMetingReden,'voldoetNietReden'=>$voldoetNietReden,'voldoet'=>$voldoet,'detail'=>$waardePerFonds,'categorien'=>$zpCategorien,'conclusie'=>$conclusie,'conclusieDetail'=>$conclusieDetail);
	}

	function zorgplichtMeting($pdata,$einddatum)
	{
	  global $__appvar;
	  	$zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
      $vodoetNietReden = '';
	    $portefeuille = $pdata['Portefeuille'];
	  	$DB3 = new DB();
	  	$DB4 = new DB();
			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							  .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);
			$DB3->Query();
			$totaalWaarde = $DB3->nextRecord();
			$totaalWaarde = $totaalWaarde['totaal'];

			if ($this->pdf->rapport_layout == 8)//RVV 29-08-06 liquiditeiten ophalen
			{
			 $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			    	  "FROM TijdelijkeRapportage WHERE ".
					  " type <> 'fondsen' AND ".
					  " rapportageDatum ='".$einddatum."' AND ".
					  " portefeuille = '".$portefeuille."' "
					   .$__appvar['TijdelijkeRapportageMaakUniek'];
			 debugSpecial($query,__FILE__,__LINE__);
			 $DB2 = new DB();
			 $DB2->SQL($query);
			 $DB2->Query();
			 $zorgtotaaldata = $DB2->nextRecord();
			 $liquiditeitentotaal = $zorgtotaaldata['totaal'];
			}//end rvv liquiditeiten ophalen

      if ($pdata['Layout'] == 58)
        $koppeling='hoofdcategorie';
      else
        $koppeling='beleggingscategorie';  

			$waardePerZorgplicht=array();
      $query = "SELECT Zorgplicht FROM ZorgplichtPerPortefeuille 
                WHERE Portefeuille = '".$portefeuille."' AND Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."' AND vanaf < '$einddatum'
                  ORDER BY extra desc, Zorgplicht";
      $DB3->SQL($query); 
			$DB3->Query();
		  $ZorgplichtCategorien=array();
			while($zpdata = $DB3->nextRecord())
      {
			  $ZorgplichtCategorien[]=$zpdata['Zorgplicht'];
      }      
      
      
      foreach($ZorgplichtCategorien as $categorie)
      {
        $query = "SELECT id,Portefeuille,Vermogensbeheerder,Zorgplicht,Minimum,Maximum,add_date,add_user,
                         change_date,change_user,Norm,Vanaf,extra FROM ZorgplichtPerPortefeuille 
                  WHERE Portefeuille = '".$portefeuille."' AND Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."' AND Zorgplicht='$categorie'
                  AND vanaf < '$einddatum'
                  ORDER BY vanaf desc limit 1";
			  $DB3->SQL($query); 
			  $DB3->Query();
			  $zpdata = $DB3->nextRecord();
        $ZorgplichtPerPortefeuilleCategorien[]=$zpdata;
        $zpCategorien[$zpdata['Zorgplicht']]=array();
      }
  //  echo $query;exit;
        
 			$query= "SELECT
			ifnull(ZorgplichtPerFonds.Zorgplicht, ifnull(ZorgplichtPerBeleggingscategorie.Zorgplicht,  'Overige')) AS Zorgplicht,
			ifnull(ZorgplichtPerFonds.Percentage,100) as Percentage,
	    sum(if(ZorgplichtPerFonds.Percentage IS NOT NULL ,actuelePortefeuilleWaardeEuro/100 * ZorgplichtPerFonds.Percentage, actuelePortefeuilleWaardeEuro)) as totaal,
	    sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
	    TijdelijkeRapportage.fondsOmschrijving,
      TijdelijkeRapportage.type,
      TijdelijkeRapportage.Fonds,
      TijdelijkeRapportage.rekening,
      SUM(if(TijdelijkeRapportage.type='fondsen',TijdelijkeRapportage.totaalAantal,0)) as totaalAantal,
      TijdelijkeRapportage.actueleFonds,
      TijdelijkeRapportage.hoofdcategorie
      FROM TijdelijkeRapportage
      LEFT JOIN ZorgplichtPerFonds ON TijdelijkeRapportage.fonds = ZorgplichtPerFonds.Fonds AND ZorgplichtPerFonds.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'
      LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.".$koppeling." = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'
      WHERE  rapportageDatum ='".$einddatum."' AND portefeuille = '".$portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
      GROUP BY Zorgplicht,Fonds,Rekening
      ORDER BY Zorgplicht, TijdelijkeRapportage.fondsOmschrijving "; //
			$DB3->SQL($query); //echo "<br>\n". $query."<br>\n<br>\n";exit;
			$DB3->Query();
			//$totaalWaarde=0;

			while($data = $DB3->nextRecord())
			{
				/*
			  $query="SELECT SUM(actuelePortefeuilleWaardeEuro) as rente FROM TijdelijkeRapportage WHERE 
        Fonds='".$data['Fonds']."' AND 
        rekening='".mysql_real_escape_string($data['rekening'])."' AND 
        TijdelijkeRapportage.type = 'rente' AND
        rapportageDatum ='".$einddatum."' AND 
        portefeuille = '".$portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
			  $DB4->SQL($query);
			  $rente=$DB4->lookupRecord();
			  $data['actuelePortefeuilleWaardeEuro']= $data['actuelePortefeuilleWaardeEuro'] + $rente['rente'];
			  $data['totaal']= $data['totaal'] + ($rente['rente'] * $data['Percentage']/100);
				*/
			  //$totaalWaarde+=$data['actuelePortefeuilleWaardeEuro'];
			  if($data['type'] == 'rekening')
			  {
			    if($this->pdf->rapport_layout == 8)
			      $waardePerZorgplicht["NON DIR"]+=$data['totaal'];
			    else
		        $waardePerZorgplicht["Overigen"]+=$data['totaal'];
			  }
			  if($data['Zorgplicht'] == '')
			    $data['Zorgplicht']='Overige';

			  $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['Zorgplicht']=$data['Zorgplicht'];
			  $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['Percentage']=$data['Percentage'];
        $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['totaalAantal']=$data['totaalAantal'];
        $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['actueleFonds']=$data['actueleFonds'];
			  $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['totaal']+=$data['totaal'];
			  $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
			  $waardePerFonds[$data['Zorgplicht']][$data['Fonds']]['fondsOmschrijving']=$data['fondsOmschrijving'];
			  $waardePerZorgplicht[$data['Zorgplicht']]+=$data['totaal'];
			  $zorgplichtCategorien[$data['Zorgplicht']]=$data['Zorgplicht'];
			  $zpCategorien[$data['Zorgplicht']]=array('Minimum'=>0,'Maximum'=>100,'Zorgplicht'=>$data['Zorgplicht'],'fondsGekoppeld'=>true);
			}

      if(is_array($__appvar['consolidatie']) || is_array($pdata['portefeuilles']) || $this->selectData['geconsolideerd']==1)
      {
        $db=new DB();
        $query="SELECT Minimum,Maximum,Zorgplicht,Risicoklasse,Norm FROM GeconsolideerdePortefeuilles 
        JOIN ZorgplichtPerRisicoklasse ON GeconsolideerdePortefeuilles.risicoprofiel=ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.vermogensbeheerder='".$pdata['Vermogensbeheerder']."'
        WHERE VirtuelePortefeuille='".$portefeuille."'";
        $db->SQL($query);
        $db->Query();
        while($profiel=$db->nextRecord())
				{
					if ($profiel['Zorgplicht'] <> '')
					{
						$zpCategorien[$profiel['Zorgplicht']] = $profiel;
					}
				}
      }

			$query = "SELECT Portefeuilles.Risicoklasse,ZorgplichtPerRisicoklasse.Zorgplicht,ZorgplichtPerRisicoklasse.Minimum,ZorgplichtPerRisicoklasse.Maximum,ZorgplichtPerRisicoklasse.Norm
                FROM Portefeuilles
                Join ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND
                                                  ZorgplichtPerRisicoklasse.Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."'
                WHERE Portefeuilles.Portefeuille= '".$portefeuille."' ";
			$DB3->SQL($query);
			$DB3->Query();
			while($zpdata = $DB3->nextRecord())
			  $zpCategorien[$zpdata['Zorgplicht']]=$zpdata;

			foreach($ZorgplichtPerPortefeuilleCategorien as $zpdata)
			  $zpCategorien[$zpdata['Zorgplicht']]=$zpdata;

			$conclusie=array();
		  $conclusieDetailFonds=array();
			foreach ($zpCategorien as $zpdata)
			{
			  $txt2='Voldoet';
				$zorgtotaal = $waardePerZorgplicht[$zpdata['Zorgplicht']];
				$txt='';
				if(1)//$zorgtotaal <> 0 || 
				{
					$zorgPercentage = round($zorgtotaal / ($totaalWaarde / 100),4);
//echo "<br>\n ".$zpdata['Zorgplicht']."<br>\n $zorgPercentage = round($zorgtotaal / ($totaalWaarde / 100),4);<br>\n";
//echo "$zorgPercentage > ".$zpdata['Maximum']." || $zorgPercentage < ".$zpdata['Minimum'] ."<br>\n";
//listarray($zpdata);
          $afwijking=0;
					if($zorgPercentage < $zpdata['Minimum'] || $zorgPercentage > $zpdata['Maximum'])
					{ 
					  $txt2='Voldoet niet';
						$zorgMeting = "Voldoet niet ";
						if($zorgPercentage < $zpdata['Minimum'])
						{
						  $txt = $zpdata['Zorgplicht']." < ".$zpdata['Minimum']." % : ".round($zorgPercentage,2)." %\n";
              $afwijking=$zorgPercentage-$zpdata['Minimum'];
						  if(!isset($zpdata['fondsGekoppeld']))
              {
							  $zorgMetingReden .= $txt;
                $voldoetNietReden .= $txt;
              }
						}
						else
						{
						  $txt = $zpdata['Zorgplicht']." > ".$zpdata['Maximum']." % : ".round($zorgPercentage,2)." %\n";
              $afwijking=$zorgPercentage-$zpdata['Maximum'];
						  if(!isset($zpdata['fondsGekoppeld']))
              {
							  $zorgMetingReden .= $txt;
                $voldoetNietReden .= $txt;
              }
						}
					}
					else
					{
					  $txt=$zpdata['Zorgplicht']."=".round($zorgPercentage,2)." %\n";
					  if(!isset($zpdata['fondsGekoppeld']))
						  $zorgMetingReden .= $txt;
					}
				}

				//$zorgMetingReden.=$zpdata['Zorgplicht']." => $zorgtotaal / $totaalWaarde = ".round($zorgtotaal / ($totaalWaarde / 100),2)."\n";

        if(!isset($zpdata['fondsGekoppeld']))
          $conclusieDetail[$zpdata['Zorgplicht']]=array('percentage'=>$zorgPercentage,
                                                      'minimum'=>$zpdata['Minimum'],
                                                      'maximum'=>$zpdata['Maximum'],
                                                      'norm'=>$zpdata['Norm'],
																											'Norm'=>$zpdata['Norm'],
                                                      'afwijking'=>$afwijking);
        else
					$conclusieDetailFonds[$zpdata['Zorgplicht']]=array('percentage'=>$zorgPercentage,
																												'minimum'=>$zpdata['Minimum'],
																												'maximum'=>$zpdata['Maximum'],
																												'norm'=>$zpdata['Norm'],
																												'Norm'=>$zpdata['Norm'],
																												'afwijking'=>$afwijking);

		    $conclusie[]=array($zpdata['Zorgplicht'],$zpdata['Minimum'].' < x < '.$zpdata['Maximum'], $this->formatGetal($zorgPercentage,1),$this->formatGetal($zorgtotaal,2),$txt,$txt2);
		    $zorgPercentage=0;
		    $zorgtotaal=0;

			}

			if ($this->pdf->rapport_layout == 8 && $zorgMetingReden == "") //rvv 29-08-06
			{
				$zorgMetingReden= "Geen waarden ";
			}

			if($zorgMeting=="Voldoet ")
			  $voldoet="Ja";
			else
			  $voldoet="Nee";

			return array('totaalWaarde'=>$totaalWaarde,'zorgMeting'=>$zorgMeting,'zorgMetingReden'=>$zorgMetingReden,'voldoetNietReden'=>$voldoetNietReden,'voldoet'=>$voldoet,'detail'=>$waardePerFonds,'categorien'=>$zpCategorien,
									 'conclusie'=>$conclusie,'conclusieDetail'=>$conclusieDetail,'conclusieDetailFonds'=>$conclusieDetailFonds);
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

		$this->dbTable2.="
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)
)";


    if($this->dbTable2)
    {
      $db->SQL($this->dbTable2);
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

    if(is_array($this->dbWaarden2))
    {
      foreach ($this->dbWaarden2 as $rege=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          $query.=",`$key`='".addslashes($value)."' ";
        }
        $db->SQL($query); echo $query."<br>\n";
	      $db->Query();
      }
    }

	}


}
?>
