<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/20 12:13:32 $
File Versie					: $Revision: 1.123 $

$Log: Modelcontrole.php,v $
Revision 1.123  2020/06/20 12:13:32  rvv
*** empty log message ***

Revision 1.122  2020/04/22 15:40:13  rvv
*** empty log message ***

Revision 1.121  2020/04/18 17:05:42  rvv
*** empty log message ***

Revision 1.120  2020/03/11 16:20:55  rvv
*** empty log message ***

Revision 1.119  2020/02/29 16:21:30  rvv
*** empty log message ***

Revision 1.118  2019/11/16 17:36:34  rvv
*** empty log message ***

Revision 1.117  2019/11/09 16:44:02  rvv
*** empty log message ***

Revision 1.116  2019/09/21 16:30:52  rvv
*** empty log message ***

Revision 1.115  2019/08/28 15:43:33  rvv
*** empty log message ***

Revision 1.114  2019/08/24 16:58:46  rvv
*** empty log message ***

Revision 1.113  2019/08/18 07:27:39  rvv
*** empty log message ***

Revision 1.112  2019/08/17 18:10:40  rvv
*** empty log message ***

Revision 1.111  2019/08/14 16:31:29  rvv
*** empty log message ***

Revision 1.110  2019/04/27 18:32:35  rvv
*** empty log message ***

Revision 1.109  2019/04/20 17:31:48  rvv
*** empty log message ***

Revision 1.108  2019/01/26 19:32:49  rvv
*** empty log message ***

Revision 1.107  2018/12/14 16:42:17  rvv
*** empty log message ***

Revision 1.106  2018/11/21 14:09:28  rvv
*** empty log message ***

Revision 1.105  2018/11/21 08:34:26  rvv
*** empty log message ***

Revision 1.104  2018/11/17 17:33:40  rvv
*** empty log message ***

Revision 1.103  2018/11/07 17:07:17  rvv
*** empty log message ***

Revision 1.102  2018/10/31 17:22:23  rvv
*** empty log message ***

Revision 1.101  2018/10/25 05:45:03  rvv
*** empty log message ***

Revision 1.100  2018/09/16 08:05:18  rvv
*** empty log message ***

Revision 1.99  2018/09/15 17:44:48  rvv
*** empty log message ***

Revision 1.98  2018/09/12 14:48:38  rvv
*** empty log message ***

Revision 1.97  2018/09/08 17:45:43  rvv
*** empty log message ***

Revision 1.96  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.95  2018/07/22 12:51:02  rvv
*** empty log message ***

Revision 1.94  2018/06/30 17:41:23  rvv
*** empty log message ***

Revision 1.93  2018/06/10 14:41:34  rvv
*** empty log message ***

Revision 1.92  2018/05/28 05:44:49  rvv
*** empty log message ***

Revision 1.91  2018/05/27 10:20:41  rvv
*** empty log message ***

Revision 1.90  2018/05/26 17:21:55  rvv
*** empty log message ***

Revision 1.89  2018/05/16 15:31:15  rvv
*** empty log message ***

Revision 1.88  2018/05/12 15:45:14  rvv
*** empty log message ***

Revision 1.87  2018/03/19 11:13:57  rvv
*** empty log message ***

Revision 1.86  2018/03/17 18:47:40  rvv
*** empty log message ***

Revision 1.85  2018/02/21 17:12:31  rvv
*** empty log message ***

Revision 1.84  2017/11/12 13:26:02  rvv
*** empty log message ***

Revision 1.83  2017/11/11 18:23:19  rvv
*** empty log message ***

Revision 1.82  2017/10/22 05:49:39  rvv
*** empty log message ***

Revision 1.81  2017/10/12 05:40:49  rvv
*** empty log message ***

Revision 1.80  2017/09/24 10:03:01  rvv
*** empty log message ***

Revision 1.79  2017/09/10 14:30:58  rvv
*** empty log message ***

Revision 1.78  2017/09/06 16:29:31  rvv
*** empty log message ***

Revision 1.77  2017/09/03 11:40:51  rvv
*** empty log message ***

Revision 1.76  2017/07/30 10:19:17  rvv
*** empty log message ***

Revision 1.75  2017/07/05 16:04:57  rvv
*** empty log message ***

Revision 1.74  2017/07/02 12:12:06  rvv
*** empty log message ***

Revision 1.73  2017/07/01 17:05:07  rvv
*** empty log message ***

Revision 1.72  2017/06/29 05:31:01  rvv
*** empty log message ***

Revision 1.71  2017/06/26 05:53:07  rvv
*** empty log message ***

Revision 1.70  2017/06/25 10:33:55  rvv
*** empty log message ***

Revision 1.69  2017/06/24 16:31:04  rvv
*** empty log message ***

Revision 1.68  2017/06/21 16:09:21  rvv
*** empty log message ***

Revision 1.67  2017/05/14 09:55:35  rvv
*** empty log message ***

Revision 1.66  2017/05/13 16:26:32  rvv
*** empty log message ***

Revision 1.65  2017/05/06 17:28:05  rvv
*** empty log message ***

Revision 1.64  2017/04/08 18:21:06  rvv
*** empty log message ***

Revision 1.63  2017/03/05 12:06:19  rvv
*** empty log message ***

Revision 1.62  2017/03/01 17:15:58  rvv
*** empty log message ***

Revision 1.61  2017/02/05 16:22:11  rvv
*** empty log message ***

Revision 1.60  2016/12/18 13:19:28  rvv
*** empty log message ***

Revision 1.59  2016/12/14 16:52:13  rvv
*** empty log message ***

Revision 1.58  2016/12/10 19:24:07  rvv
*** empty log message ***

*/
include_once("rapportRekenClass.php");

class Modelcontrole
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function Modelcontrole( $selectData )
	{
		$this->selectData = $selectData;
    //$this->selectData["modelcontrole_afronding"]=4;
    // $this->selectData["externeBatchId"]=time();
		$this->pdf->excelData 	= array();

		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->rapport_type = "modelcontrole";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;

		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);

		$this->pdf->tmdatum = $this->selectData['datumTm'];
		// selectdata ook aan PDF geven
		$this->pdf->selectData = $this->selectData;

		$this->orderby = " Portefeuilles.ClientVermogensbeheerder ";

		if($this->selectData['modelcontrole_level'] != 'fonds')
			$this->pdf->excelData[] = array("Regel","Portefeuille","Accountmanager","Client","Depotbank","Fonds",'ISINcode','Valuta',"Model percentage","Werkelijk Percentage","Afwijkings Percentage","Afwijking in EUR","","Waarde volgens percentage Model","Huidige Waarde",'Modelportefeuille','SoortOvereenkomst','Overige beperkingen'); //rvv huidige waarde toegevoegd.
		else
			$this->pdf->excelData[] = array("Regel","Portefeuille","Accountmanager","Client","Depotbank","Fonds",'ISINcode','Valuta',"Model percentage","Werkelijk Percentage","Afwijkings Percentage","Afwijking in EUR","Kopen","Verkopen","Waarde volgens percentage Model","Koers in locale valuta","Huidige Waarde","Geschat orderbedrag",'Beleggingscategorie','Modelportefeuille','SoortOvereenkomst','Overige beperkingen'); //rvv huidige waarde toegevoegd.

		$this->orderData=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = substr($getal[1],0,4);
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}
  
	function printKop($title)
	{
		$this->pdf->SetFont("Times", "bi", 10);
		$this->pdf->Cell(100 , 4 , $title , 0, 1, "L");
		$this->pdf->SetFont("Times", "", 10);
	}

	function writeRapport()
	{
		global $__appvar;
		
		if($this->selectData["modelcontrole_rapport"] == "liquideren")
			$this->selectData['modelcontrole_filter']='geen filter';

		$DB = new DB();
		$counter=0;
    
    $ordermoduleAccess=GetModuleAccess("ORDER");


		if($this->selectData['modelcontrole_level'] != 'fonds')
		{
		  if($this->selectData['modelcontrole_level'] == 'beleggingscategorie'||$this->selectData['modelcontrole_level'] == 'hoofdcategorie')
  	    $query = "SELECT Beleggingscategorie as id,Omschrijving as value FROM Beleggingscategorien";
		  elseif($this->selectData['modelcontrole_level'] == 'beleggingssector')
		    $query = "SELECT Beleggingssector as id,Omschrijving as value FROM Beleggingssectoren";
		  else
		    $query = "SELECT Regio as id,Omschrijving as value FROM Regios";
	    $DB->SQL($query);
      $DB->Query();
	    while($data=$DB->nextRecord())
	      $omschrijving[$data['id']]=$data['value'];
		}
    
    

		$this->pdf->__appvar = $__appvar;

		$einddatum = jul2sql($this->selectData['datumTm']);

		$jaar = date("Y",$this->selectData['datumTm']);
  if($this->selectData['skipPortefeuilleSelectie']==true)
  {
    $portefeuilleList = $this->selectData['selectedPortefeuilles'];
  }
  else
  {
    $selectie = new portefeuilleSelectie($this->selectData, $this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $verwijderdePortefeuilles=array();
    if($__appvar['bedrijf'] <> 'JAN')
		{
      foreach($portefeuilles as $portefeuille=>$pData)
      {
        if($this->selectData['filetype']=='order' && $pData['consolidatie']==1
          && $DB->QRecords("SELECT orderViaConsolidatie FROM Vermogensbeheerders WHERE Vermogensbeheerder='".mysql_real_escape_string($pData['Vermogensbeheerder'])."' AND orderViaConsolidatie=1")==0)
        {
          $verwijderdePortefeuilles[]=$portefeuille;
          unset($portefeuilles[$portefeuille]);
        }
      }
		}
    if(count($verwijderdePortefeuilles)>0)
    {
      echo "<script>alert('Geconsolideerde portefeuilles uit selectie verwijderd.');</script>";
      //echo "<script>parent.AEMessage('Geconsolideerde portefeuilles uit selectie verwijderd.', function () {  }); </script>";
      logscherm("Geconsolideerde portefeuilles (".implode(",",$verwijderdePortefeuilles).") verwijderd.");
    }
    
    $portefeuilleList = array_keys($portefeuilles);
  }
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";

		// selectie Fonds
		if(!empty($this->selectData['depotbank']))
			$extraquery .= " AND Portefeuilles.Depotbank = '".$this->selectData['depotbank']."' ";


		if($this->selectData['modelcontrole_rapport'] == "vastbedrag")
		{
		//	$extraquery = " AND Portefeuilles.Portefeuille = '".$this->selectData['modelcontrole_portefeuille']."' ";
		}

    if($this->selectData['modelcontrole_portefeuille']=='Allemaal')
      $extraquery .= " AND Portefeuilles.ModelPortefeuille <> '' ";
		elseif($this->selectData['modelcontrole_filter'] == "gekoppeld")
			$extraquery .= " AND Portefeuilles.ModelPortefeuille = '".$this->selectData['modelcontrole_portefeuille']."' ";

		// selecteer alleen portefeuilles waar het fonds voorkomt!
		$q = " SELECT ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank,
						   Portefeuilles.Accountmanager,
						   Portefeuilles.ModelPortefeuille,
               Portefeuilles.Risicoklasse,
               Portefeuilles.Vermogensbeheerder, 
               Portefeuilles.SoortOvereenkomst,
               Clienten.Naam,
				       CRM_naw.profielOverigeBeperkingen ".
					 " FROM Portefeuilles 
					 JOIN Clienten ON Portefeuilles.Client = Clienten.Client
					 LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
					 WHERE 1 ".$extraquery.
					 " ORDER BY Portefeuilles.Client"; //ORDER BY Portefeuilles.ModelPortefeuille, ".$this->orderby

		$DB->SQL($q);
		$DB->Query();
		$records = $DB->records();


		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

    $first=true;
		while($portefeuille = $DB->NextRecord())
		{
      $portefTotaal=0;
			$portefeuilleRegels=array();
			$portefeuilleTonen=false;
      $this->pdf->overigeBeperkingen=$portefeuille['profielOverigeBeperkingen'];
		  $DB3 = new DB();
			$query="SELECT Vermogensbeheerders.OrderuitvoerBewaarder, Vermogensbeheerders.orderViaConsolidatie FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='".$portefeuille['Portefeuille']."'";
			$DB3->SQL($query);
			$bewaarder=$DB3->lookupRecord();

      if(($this->selectData['modelcontrole_portefeuille'] <> $portefeuille['ModelPortefeuille'] || $first) && $this->selectData["modelcontrole_rapport"] != "liquideren" )
		  {
		    $first=false;
		    verwijderTijdelijkeTabel("m".$this->selectData['modelcontrole_portefeuille'],$einddatum);

		    if($portefeuille['ModelPortefeuille'] <> '' && $this->selectData['modelcontrole_portefeuille'] == 'Allemaal')
		      $this->pdf->selectData['modelcontrole_portefeuille']=$portefeuille['ModelPortefeuille'];

        
		    $query="SELECT Fixed,Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$this->pdf->selectData['modelcontrole_portefeuille']."'";
        $DB3->SQL($query);
	    	$DB3->Query();
	    	$modelType = $DB3->nextRecord();

				$this->pdf->selectData['modelcontrole_portefeuille_naam']=$this->pdf->selectData['modelcontrole_portefeuille'];
        if($modelType['Fixed']==1||$modelType['Fixed']==2)
				{
					$portefeuilleData = berekenFixedModelPortefeuille($this->pdf->selectData['modelcontrole_portefeuille'], $einddatum);
				}
        elseif($modelType['Fixed']==3)
				{
					$first=true;
					$verdeling=meervoudigeModelPortefeuilleVerdeling($portefeuille['Portefeuille'], $einddatum,$this->pdf->selectData['modelcontrole_portefeuille']);
					//listarray($verdeling);echo $portefeuille['Portefeuille'];
					if(count($verdeling)>0)
				  	$this->pdf->selectData['modelcontrole_portefeuille_naam']='';
					ksort($verdeling);
					foreach($verdeling as $modelp=>$percentage)
					{
						if($this->pdf->selectData['modelcontrole_portefeuille_naam']<>'')
							$this->pdf->selectData['modelcontrole_portefeuille_naam'].=', ';
						$this->pdf->selectData['modelcontrole_portefeuille_naam'] .= ($percentage * 100) . '% ' . $modelp;
					}

					$portefeuilleData = berekenMeervoudigeModelPortefeuille($portefeuille['Portefeuille'], $einddatum,$this->pdf->selectData['modelcontrole_portefeuille']);
				}
        else
		      $portefeuilleData = berekenPortefeuilleWaarde($this->pdf->selectData['modelcontrole_portefeuille'], $einddatum);


				if($modelType['Fixed']!=2)
					$leftJoinPortef="LEFT ";

        $extraCategorieFilter='';
        if($modelType['Beleggingscategorie'] <> '')
        {
          $extraCategorieFilter=" AND TijdelijkeRapportage.Beleggingscategorie='".$modelType['Beleggingscategorie']."' ";
        }

		    vulTijdelijkeTabel($portefeuilleData,"m".$this->pdf->selectData['modelcontrole_portefeuille'],$einddatum);

		  		// bereken totaal waarde model
	    	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
		  					 " rapportageDatum ='".$einddatum."' AND ".
			  				 " portefeuille = '"."m".$this->pdf->selectData['modelcontrole_portefeuille']."'  AND type <> 'rente' 
                 $extraCategorieFilter "
			  				 .$__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
	    	
	    	$DB3->SQL($query);
	    	$DB3->Query();
	    	$modelwaarde = $DB3->nextRecord();

	    	if($modelwaarde['totaal'] <> 0)
				{
					$modelTotaal = $modelwaarde['totaal'];
					//$melding="Portefeuille ".$portefeuille['Portefeuille']." met modelportefeuill (".$this->pdf->selectData['modelcontrole_portefeuille']." , waarde $modelTotaal) berekend.";
					//logScherm($melding);
				}
		    else
				{
					$modelTotaal = 0.001;
					$melding="Portefeuille ".$portefeuille['Portefeuille']." kan niet worden verwerkt omdat er geen modelportefeuillewaarde voor (".$this->pdf->selectData['modelcontrole_portefeuille'].") bepaald kan worden.";
					logScherm($melding);
					echo "<script>parent.AEMessage('".$melding."', '".$portefeuille['Portefeuille']." overgeslagen', function () {  }); </script>";
          continue;
				}

				if($modelType['Fixed']==2)
				{
					$modelwaarde['totaal'] = 100000;
					$modelTotaal=$modelwaarde['totaal'];
				}

		  }

		  $crmNaam=getCrmNaam($portefeuille['Portefeuille']);
      if($crmNaam)
      {
        $portefeuille['Naam'] = $crmNaam['naam'];
        $portefeuille['Naam1'] = $crmNaam['naam1'];
      }
			if($this->selectData["modelcontrole_rapport"] == "vastbedrag" && $this->selectData["modelcontrole_rebalance"] <> "1" )
			{

        if($this->selectData["modelcontrole_vastbedrag"]<0)
				{
					$portefeuilleData = berekenPortefeuilleWaarde($portefeuille['Portefeuille'], $einddatum);
					$pwaarde=0;
					foreach($portefeuilleData as $regel)
						$pwaarde+=$regel['actuelePortefeuilleWaardeEuro'];

					$factor=abs($this->selectData["modelcontrole_vastbedrag"])/$pwaarde;
					//echo "$pwaarde  $factor ".(1 - $factor);
					$modelTotaal=0;
					foreach($portefeuilleData as $i=>$regel)
					{
						$portefeuilleData[$i]['actuelePortefeuilleWaardeEuro'] = $regel['actuelePortefeuilleWaardeEuro'] * (1 - $factor);
						$portefeuilleData[$i]['totaalAantal'] = $regel['totaalAantal'] * (1 - $factor);
						$modelTotaal += $portefeuilleData[$i]['actuelePortefeuilleWaardeEuro'];
					}
					vulTijdelijkeTabel($portefeuilleData, "m" . $this->pdf->selectData['modelcontrole_portefeuille'], $einddatum);
				}
				else
				{
					$portefeuille['Portefeuille']=" ".$portefeuille['Portefeuille'];
				}
				//$this->selectData["modelcontrole_vastbedrag"]
			//	$portefeuille = array();
			}
			// set pdf vars
			$this->pdf->naamOmschrijving = $portefeuille['Naam'];
			$this->pdf->clientOmschrijving = $portefeuille['Client']." / ".$portefeuille['Portefeuille']." / ".$portefeuille['Depotbank'];

		//	$this->pdf->excelData[] = array();
		//	$this->pdf->excelData[] = array("Client:",
		//								$this->pdf->clientOmschrijving);

		//	$this->pdf->excelData[] = array("Naam:",
		//								$this->pdf->naamOmschrijving);
	//		$this->pdf->excelData[] = array();
			// Maak header voor CSV bestand //"Overschrijding in stuks / nominaal"

		//	$this->pdf->AddPage();
		//	$this->pdf->SetFont("Times","",10);

			//verwijderTijdelijkeTabel($portefeuille['Portefeuille'],$einddatum);
			$portefeuilleData = berekenPortefeuilleWaarde($portefeuille['Portefeuille'], $einddatum);
			vulTijdelijkeTabel($portefeuilleData,$portefeuille['Portefeuille'],$einddatum);
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum = '".$einddatum."' AND ".
        " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' " //$extraCategorieFilter
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB3 = new DB();
      $DB3->SQL($query);
      $DB3->Query();
      $portefwaarde = $DB3->nextRecord();
      $this->pdf->portefwaardeHeader=$portefwaarde['totaal'];
      
      $query="SELECT uitsluitingenModelcontrole.fonds,uitsluitingenModelcontrole.rekening,uitsluitingenModelcontrole.bedrag,Fondsen.Omschrijving as fondsOmschrijving, uitsluitingenModelcontrole.Beleggingscategorie
FROM uitsluitingenModelcontrole
LEFT JOIN Fondsen ON uitsluitingenModelcontrole.fonds=Fondsen.Fonds
WHERE uitsluitingenModelcontrole.portefeuille='".$portefeuille['Portefeuille']."'";
      $DB3 = new DB();
      $DB3->SQL($query);
      $DB3->Query();
      $uitsluitingen=array();
      $gecorigeerdeRekeningen=array();
      while($data = $DB3->nextRecord())
      {
        $uitsluitingen[]=$data;
      }
      if(count($uitsluitingen)>0)
      {
        $portefeuilleRegels[]=array('Uitgesloten:');
      }
      foreach($uitsluitingen as $regel)
      {
  
        if($regel['Beleggingscategorie']<>'')
        {
          $query = "DELETE FROM TijdelijkeRapportage WHERE Beleggingscategorie='" . mysql_real_escape_string($regel['Beleggingscategorie']) . "' AND portefeuille IN ('" . $portefeuille['Portefeuille'] . "') " . $__appvar['TijdelijkeRapportageMaakUniek'];
          $txt=$regel['Beleggingscategorie'];
        }
        elseif($regel['fonds']<>'')
        {
          $query = "DELETE FROM TijdelijkeRapportage WHERE fonds='" . mysql_real_escape_string($regel['fonds']) . "' AND portefeuille IN ('" . $portefeuille['Portefeuille'] . "') " . $__appvar['TijdelijkeRapportageMaakUniek'];
          if($regel['fondsOmschrijving']<>'')
            $txt=$regel['fondsOmschrijving'];
          else
            $txt=$regel['fonds'];
        }
        elseif($regel['rekening']=='alle')
        {
          $query = "DELETE FROM TijdelijkeRapportage WHERE type='rekening' AND portefeuille='" . $portefeuille['Portefeuille'] . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
          $txt='Alle rekeningen';
        }
        elseif($regel['bedrag']<>0)
        {
          $query = "UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-" . doubleval($regel['bedrag']) . " WHERE portefeuille='" . $portefeuille['Portefeuille'] . "' AND rekening='" . mysql_real_escape_string($regel['rekening']) . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
          $txt=$regel['rekening'].', bedrag €'.$this->formatGetal($regel['bedrag'],2);
          $gecorigeerdeRekeningen[$regel['rekening']]=$regel['rekening'];
        }
        elseif($regel['rekening']<>'')
        {
          $query = "DELETE FROM TijdelijkeRapportage WHERE type='rekening' AND portefeuille='" . $portefeuille['Portefeuille'] . "' AND rekening='" . mysql_real_escape_string($regel['rekening']) . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
          $txt=$regel['rekening'];
        }
        else
        {
          $query = '';
          $txt='';
        }
        if($query<>'')
        {
          $portefeuilleRegels[]=array($txt);
//          logscherm($query);
          $DB3->SQL($query);
          $DB3->Query();
        }
      }
      if(count($uitsluitingen)>0)
      {
        $portefeuilleRegels[]=array('');
      }

    //  if($this->selectData["modelcontrole_rapport"] <> "liquideren")
      runPreProcessor($portefeuille['Portefeuille']);

			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$einddatum."' AND ".
								 " portefeuille = '".$portefeuille['Portefeuille']."' AND type <> 'rente' " //$extraCategorieFilter
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($query);
			$DB3->Query();
			$portefwaarde = $DB3->nextRecord();
      /*
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum = '".$einddatum."' AND ".
        " portefeuille = 'm" . $this->pdf->selectData['modelcontrole_portefeuille']."' AND type <> 'rente' " //$extraCategorieFilter
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB3 = new DB();
      $DB3->SQL($query);
      $DB3->Query();
      $modelTotaalDb = $DB3->nextRecord();
      $modelTotaal=$modelTotaalDb['totaal'];
      */
      $query = "SELECT norm FROM NormPerRisicoprofiel WHERE Risicoklasse='".$portefeuille['Risicoklasse']."' 
                                                    AND Vermogensbeheerder='".$portefeuille['Vermogensbeheerder']."'
                                                    AND Beleggingscategorie='".$modelType['Beleggingscategorie']."'";
 			$DB3->SQL($query);
			$DB3->Query();
			$norm = $DB3->nextRecord();

      if($norm['norm'] <> '')
        $portefwaarde['totaal']=$portefwaarde['totaal']*($norm['norm']/100);
                                         


			if($portefwaarde['totaal'] <> 0)
		  	$portefTotaal = $portefwaarde['totaal'];

			if($this->selectData["modelcontrole_rapport"] == "vastbedrag" && $portefTotaal>=0)
				$portefTotaal = $this->selectData["modelcontrole_vastbedrag"]+$portefTotaal;

			if($portefTotaal == 0)
			{
				$melding="Portefeuille (".$portefeuille['Portefeuille'].") kan niet worden verwerkt omdat er geen portefeuillewaarde bepaald kan worden.";
				logScherm($melding);
				echo "<script>parent.AEMessage('".$melding."', '".$portefeuille['Portefeuille']." overgeslagen', function () {  }); </script>";
				continue;
			}


			if($this->selectData['modelcontrole_uitvoer'] == "afwijkingen")
			{
				$afwijking = " HAVING afwijking <> 0 ";

				if ($this->selectData['modelcontrole_percentage'] > 0)
				{
					$afwijking = " HAVING ABS(afwijking) > " . $this->selectData['modelcontrole_percentage'] . " ";
				}
			}

			if($this->selectData["modelcontrole_rapport"] == "liquideren")
				$modelTotaal="0";

			if($this->selectData['modelcontrole_level'] != 'fonds')
			  $orderby="TijdelijkeRapportage.".$this->selectData['modelcontrole_level'].'Volgorde';
			else
				$orderby='afwijking DESC , RegelOmschrijving ';

			$query = "SELECT
			SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
		  SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.totaalAantal,0)) AS aanwezigeAantal,
  		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
	  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			  SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,model.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
		  	SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,portef.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
      TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." as RegelOmschrijving,
			TijdelijkeRapportage.fonds,
      TijdelijkeRapportage.valuta,
			TijdelijkeRapportage.fondsOmschrijving,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid,
      sum(TijdelijkeRapportage.totaalAantal) as totaalAantal,
      TijdelijkeRapportage.Beleggingscategorie,
      TijdelijkeRapportage.BeleggingscategorieOmschrijving,
			Fondsen.ISINCode,
			Fondsen.Valuta as fondsValuta,Fondsen.OptieType,Fondsen.OptieExpDatum,Fondsen.OptieUitoefenPrijs,Fondsen.optieCode,Fondsen.fondssoort,Fondsen.Fondseenheid
			FROM TijdelijkeRapportage
			JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			LEFT JOIN TijdelijkeRapportage AS model ON model.fonds = TijdelijkeRapportage.fonds 
          AND model.portefeuille = \""."m".$this->pdf->selectData['modelcontrole_portefeuille']."\" 
          AND model.type = 'fondsen'  
          AND model.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'model',$__appvar['TijdelijkeRapportageMaakUniek'])."
			$leftJoinPortef JOIN TijdelijkeRapportage AS portef ON portef.fonds = TijdelijkeRapportage.fonds
			    AND portef.fondspaar = TijdelijkeRapportage.fondspaar
          AND portef.portefeuille = \"".$portefeuille['Portefeuille']."\" 
          AND portef.type = 'fondsen'  
          AND portef.rapportageDatum = '".$einddatum."'"
          .str_replace("TijdelijkeRapportage",'portef',$__appvar['TijdelijkeRapportageMaakUniek'])."
			WHERE
			TijdelijkeRapportage.type = 'fondsen' AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(
        (TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" )
        OR 
        (TijdelijkeRapportage.portefeuille = \""."m".$this->pdf->selectData['modelcontrole_portefeuille']."\")
      )
      $extraCategorieFilter
      "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.".$this->selectData['modelcontrole_level']." ".$afwijking."
			ORDER BY $orderby ";
			debugSpecial($query,__FILE__,__LINE__);

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();
			while($fdata = $DB2->nextRecord())
			{
				$aankoopStuks=0;
				$verkoopStuks=0;
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				
				$aankoopStuks 	= round(($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'],4);
				//echo $fdata['fondsOmschrijving']." $aankoopWaarde 	= ((($portefTotaal) / 100) * ".$fdata['percentageModel'].") - ".$fdata['portefeuilleWaarde']." <br>\n";
			  if($fdata['fondsEenheid'] == '0.01')
		    {
          if($aankoopStuks > 0)
		        $aankoopStuks=floor($aankoopStuks/100)*100;
          else
            $aankoopStuks=ceil($aankoopStuks/100)*100;
				}

				if($modelType['Fixed']==2)
				{
					if($aankoopStuks>0)
						$aankoopStuks=0;
				}

				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if($this->selectData['modelcontrole_level'] != 'fonds')
				{
				  $fdata['fondsOmschrijving']=$omschrijving[$fdata['RegelOmschrijving']];
				  $fdata['actueleFonds']=0;
				  $aankoopStuks=0;
				  $verkoopStuks=0;
				}
				$verkoopNaarNul=false;

				if(isset($this->selectData["modelcontrole_afronding"]) &&  $this->selectData["modelcontrole_afronding"]>0)
				{
					$aankoopStuks = round($aankoopStuks, $this->selectData["modelcontrole_afronding"]);
					$verkoopNaarNul=true;
				}
        //echo "Verkoop $verkoopNaarNul | ".round($fdata['percentageModel'],1)." $aankoopStuks ".$fdata['fondsOmschrijving']."<br>\n";
				if($aankoopStuks < 0)
				{
					
					if(round($fdata['percentageModel'],1)==0)
					{
						$verkoopStuks = $fdata['totaalAantal'];
						$verkoopNaarNul=true;
					}
					else
          {
            if(isset($this->selectData["modelcontrole_afronding"]) &&  $this->selectData["modelcontrole_afronding"]>0 )
            {
              $verkoopStuks = round($aankoopStuks * -1, $this->selectData["modelcontrole_afronding"]);
            }
            else
            {
              $verkoopStuks = round($aankoopStuks * -1, 0);
            }
          }
					$aankoopStuks = 0;

					if($fdata['portefeuilleWaarde']==0 && $this->selectData["modelcontrole_rebalance"]==1)
						$verkoopStuks=0;
				}
				else
				{
					//echo $fdata['fonds']." huidige waarde: ".$fdata['portefeuilleWaarde']."<br>\n";
					if(round($fdata['percentageModel'],1)==0 && round($fdata['portefeuilleWaarde'],1)==0.00)
					{
						$aankoopStuks = $fdata['totaalAantal']*-1;
						$verkoopStuks = 0;
						$verkoopNaarNul=true;
					}
					else
					{
						if(isset($this->selectData["modelcontrole_afronding"]) &&  $this->selectData["modelcontrole_afronding"]>0 )
						{
              $aankoopStuks = round($aankoopStuks, $this->selectData["modelcontrole_afronding"]);
						}
						else
            {
              $aankoopStuks = round($aankoopStuks, 0);
            }
            if(round($fdata['percentageModel'],1)==0 && $this->selectData["modelcontrole_rapport"] == "liquideren")
            {
              $verkoopNaarNul = true;
              $aankoopStuks = $fdata['totaalAantal']*-1;
              $verkoopStuks = 0;
            }
          }
				}
				
				
				$geschatOrderbedrag 	= (($verkoopStuks-$aankoopStuks) * ($fdata['actueleFonds'] * $fdata['actueleValuta'])) * $fdata['fondsEenheid'];

				$data = array($fdata['fondsOmschrijving'],
											$this->formatGetal($fdata['percentageModel'],2),
											$this->formatGetal($fdata['percentagePortefeuille'],2),
											$this->formatGetal($fdata['afwijking'],2),
				              $this->formatGetal($aankoopWaarde,2),
                      $this->formatAantal($aankoopStuks,0,true),
									    $this->formatAantal($verkoopStuks,0,true),
				            	$this->formatGetal($waardeVolgensModel,2),
				            	$this->formatGetal($fdata['actueleFonds'],2),
											$this->formatGetal($geschatOrderbedrag,2));
        $portefeuilleRegels[]=$data;
				$portefeuilleTonen=true;
				//$this->pdf->Row($data);
				$counter++;

if($this->selectData['modelcontrole_level'] != 'fonds')
	$this->pdf->excelData[] = array($counter,$portefeuille['Portefeuille'],$portefeuille['Accountmanager'],$portefeuille['Client'],$portefeuille['Depotbank'],
		                  $fdata['fondsOmschrijving'],$fdata['ISINCode'],$fdata['valuta'],
											round($fdata['percentageModel'],2),
											round($fdata['percentagePortefeuille'],2),
											round($fdata['afwijking'],2),
											round($aankoopWaarde,2),
											"",
											round($waardeVolgensModel,2),'',$this->pdf->selectData['modelcontrole_portefeuille_naam'],$portefeuille['SoortOvereenkomst'],$portefeuille['profielOverigeBeperkingen'] );

else
				$this->pdf->excelData[] = array($counter,$portefeuille['Portefeuille'],$portefeuille['Accountmanager'],$portefeuille['Client'],$portefeuille['Depotbank'],
					            $fdata['fondsOmschrijving'],$fdata['ISINCode'],$fdata['valuta'],
											round($fdata['percentageModel'],2),
											round($fdata['percentagePortefeuille'],2),
											round($fdata['afwijking'],2),
					            round($aankoopWaarde,2),
											$aankoopStuks,
											$verkoopStuks,
											round($waardeVolgensModel,2),
											round($fdata['actueleFonds'],2),
				             	round($fdata['portefeuilleWaarde'],2),
				 	            round($geschatOrderbedrag,2),
			            		$fdata['BeleggingscategorieOmschrijving'],$this->pdf->selectData['modelcontrole_portefeuille_naam'],$portefeuille['SoortOvereenkomst'],$portefeuille['profielOverigeBeperkingen'] );

			  if((round($fdata['afwijking'],2) <> 0.00 || $verkoopNaarNul==true) && ($aankoopStuks <> 0 || $verkoopStuks <> 0))
			  {
          
          if($ordermoduleAccess==2)
          {
						if($bewaarder['OrderuitvoerBewaarder']==1 || $bewaarder['orderViaConsolidatie']==1)
						{
						  /*
						   *         max(if(Rekeningmutaties.Bewaarder is null,'',Rekeningmutaties.Bewaarder)) as Depotbank,
        Rekeningen.Depotbank as rekDepot,
						   */
							$q="SELECT 
        Portefeuilles.Portefeuille,
        SUM(Rekeningmutaties.aantal) as aantal,
        max(if(Rekeningmutaties.Bewaarder='',Rekeningen.Depotbank,
if(Rekeningmutaties.Bewaarder is null,Rekeningen.Depotbank,Rekeningmutaties.Bewaarder))) as Depotbank
        FROM
        Portefeuilles 
        INNER JOIN Clienten ON Portefeuilles.Client = Clienten.Client
        INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
        LEFT JOIN Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening AND  year(Rekeningmutaties.Boekdatum)='".substr($einddatum,0,4)."' AND 
        Rekeningmutaties.Fonds='".mysql_real_escape_string($fdata['fonds'])."' AND(Rekeningmutaties.Grootboekrekening = 'FONDS' OR (Rekeningmutaties.Grootboekrekening = 'KRUIS'  AND Rekeningmutaties.Fonds <> ''))
        WHERE Portefeuilles.Portefeuille = '".trim($portefeuille['Portefeuille'])."' 
         GROUP BY Bewaarder,Rekeningen.Depotbank
         having aantal <> 0
        ORDER BY aantal desc";
							$DB3->SQL($q);
							$DB3->Query();
							$mutDepot='';

							while($rekData 	= $DB3->nextRecord())
							{
								if($rekData['Depotbank']<>'')
                {
                  $mutDepot = $rekData['Depotbank'];
                  $aantalInPositie=$rekData['aantal'];
                }

								if($mutDepot<>'')
								  break;
							}
							if($mutDepot<>'')
              {
                $depot = $mutDepot;
              }
							else
              {
                $depot = 'NB';
                $aantalInPositie=$fdata['aanwezigeAantal'];
              }
              //logScherm($depot);
						}
						else
            {
              $depot = $portefeuille['Depotbank'];
              $aantalInPositie=$fdata['aanwezigeAantal'];
            }
				//logscherm("$aantalInPositie ".$fdata['aanwezigeAantal']." ".$fdata['fonds']);
						$nieuwAantal=$aantalInPositie-$verkoopStuks+$aankoopStuks;
    			  $this->orderData[]=array('fonds'=>$fdata['fonds'],'modelPercentage'=>$fdata['percentageModel'],'portefeuillePercentage'=>$fdata['percentagePortefeuille'],
			                             'afwijking'=>$fdata['afwijking'],'aantal'=>($aankoopStuks-$verkoopStuks),'client'=>$portefeuille['Client'],'Depotbank'=>$depot,
			                             'modelWaarde'=>$waardeVolgensModel,'koers'=>$fdata['actueleFonds'],'portefeuille'=>trim($portefeuille['Portefeuille']),
                                   'ISINCode'=>$fdata['ISINCode'],'fondsOmschrijving'=>$fdata['fondsOmschrijving'],'orderbedrag'=>$geschatOrderbedrag,
																		 'fondsValuta'=>$fdata['fondsValuta'],'optieType'=>$fdata['OptieType'],'optieExpDatum'=>$fdata['OptieExpDatum'],'optieSymbool'=>$fdata['optieCode'],
																		 'fondssoort'=>$fdata['fondssoort'],'fondseenheid'=>$fdata['Fondseenheid'],'optieUitoefenprijs'=>$fdata['OptieUitoefenPrijs'],
							                      'Beleggingscategorie'=>$fdata['Beleggingscategorie'],'externeBatchId'=>$this->pdf->selectData['externeBatchId'],'afwijkingsbedrag'=>$aankoopWaarde,
                                     'aantalInPositie'=>$aantalInPositie,'nieuwAantal'=>$nieuwAantal);
		      }
          else
			      $this->orderData[]=array('fonds'=>$fdata['fonds'],'modelPercentage'=>$fdata['percentageModel'],'portefeuillePercentage'=>$fdata['percentagePortefeuille'],
			                             'afwijking'=>$fdata['afwijking'],'kopen'=>$aankoopStuks,'verkopen'=>$verkoopStuks,'overschrijding'=>$aankoopWaarde,'valuta'=>$fdata['valuta'],
			                             'modelWaarde'=>$waardeVolgensModel,'koers'=>$fdata['actueleFonds'],'portefeuille'=>trim($portefeuille['Portefeuille']));
			  }
			}

// RVV Toevoegen van een query die de rekeningen vergelijkt met de "Model rekeningen".
// De resultaten van deze query worden toegevoegd aan de al bestaande Fondsen list.
// Meeste code gekopieerd van bovenstaande query en while loop.
// Extra toegevoegd dat de huidige en modelwaarde van de rekening onder deze lijst wordt weergegeven.

			//if($this->selectData['modelcontrole_level'] != 'fonds')
				$group=' TijdelijkeRapportage.type ';
		//	else
		//		$group=' TijdelijkeRapportage.fondsOmschrijving , TijdelijkeRapportage.rekening ';

 			$query = "SELECT
					SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,

		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
      TijdelijkeRapportage.rekening,
      TijdelijkeRapportage.valuta,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->pdf->selectData['modelcontrole_portefeuille']."\")  "
			.$__appvar['TijdelijkeRapportageMaakUniek']." 
			GROUP BY $group ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__); //

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();


			//$this->pdf->Row(array(""));
			$portefeuilleRegels[]=array('');
			while($fdata = $DB2->nextRecord())
			{
				//if($this->selectData['modelcontrole_level'] != 'fonds')
				//{
					$fdata['fondsOmschrijving']='Liquiditeiten';
					$fdata['rekening']='';

			//	}
				$aankoopWaarde 	= ((($portefTotaal) / 100) * $fdata['percentageModel']) - $fdata['portefeuilleWaarde'];
				$aankoopStuks 	= ($aankoopWaarde / ($fdata['actueleFonds'] * $fdata['actueleValuta']))  / $fdata['fondsEenheid'];
				$verkoopStuks = 0;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];


				//if ($fdata['portefeuilleWaarde'] != 0)
				//{
				   $data = array($fdata['fondsOmschrijving'].' '.$fdata['rekening'],
						 $this->formatGetal($fdata['percentageModel'],2),
						 $this->formatGetal($fdata['percentagePortefeuille'],2),
						 $this->formatGetal($fdata['afwijking'],2),
						 $this->formatGetal($aankoopWaarde,2),
						 $this->formatAantal($aankoopStuks,0,true),
						 $this->formatAantal($verkoopStuks,0,true),
						 $this->formatGetal($waardeVolgensModel,2),
						 $this->formatGetal($fdata['actueleFonds'],2));
				   $counter++;
				   //$this->pdf->Row($data);
				   $portefeuilleRegels[]=$data;
				   $portefeuilleTonen=true;
           if($this->selectData['modelcontrole_level'] != 'fonds')
				      $this->pdf->excelData[] = array($counter,$portefeuille['Portefeuille'],$portefeuille['Accountmanager'],$portefeuille['Client'],$portefeuille['Depotbank'],
								$fdata['fondsOmschrijving'].' '.$fdata['rekening'],'',$fdata['valuta'],
											round($fdata['percentageModel'],2),
											round($fdata['percentagePortefeuille'],2),
											round($fdata['afwijking'],2),
											round($aankoopWaarde,2),
											"",
											round($waardeVolgensModel,2),
											round($fdata['portefeuilleWaarde'],2),$this->pdf->selectData['modelcontrole_portefeuille_naam'],$portefeuille['SoortOvereenkomst'],$portefeuille['profielOverigeBeperkingen'] ); //huidige waarde collom toegevoegd.
           else
				     $this->pdf->excelData[] = array($counter,$portefeuille['Portefeuille'],$portefeuille['Accountmanager'],$portefeuille['Client'],$portefeuille['Depotbank'],
							 $fdata['fondsOmschrijving'].' '.$fdata['rekening'],'',$fdata['valuta'],
											round($fdata['percentageModel'],2),
											round($fdata['percentagePortefeuille'],2),
											round($fdata['afwijking'],2),
							        round($aankoopWaarde,2),
											round($aankoopStuks,0),
											round($verkoopStuks,0),
											round($waardeVolgensModel,2),
											round($fdata['actueleValuta'],2),
											round($fdata['portefeuilleWaarde'],2),'','',$this->pdf->selectData['modelcontrole_portefeuille_naam'],$portefeuille['SoortOvereenkomst'],$portefeuille['profielOverigeBeperkingen'] ); //huidige waarde collom toegevoegd.

				//}
			}



if($modelType['Fixed']==3)
	$group='TijdelijkeRapportage.valuta ';
else
	$group=' TijdelijkeRapportage.fondsOmschrijving , TijdelijkeRapportage.rekening ';

			$query = "SELECT
					SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))  AS modelWaarde,
		SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 AS percentageModel,
		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) AS portefeuilleWaarde,
		SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100 AS percentagePortefeuille,
			(
			 SUM(IF(TijdelijkeRapportage.portefeuille ='m".$this->pdf->selectData['modelcontrole_portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $modelTotaal * 100 -
			 SUM(IF(TijdelijkeRapportage.portefeuille ='".$portefeuille['Portefeuille']."' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) / $portefTotaal * 100
			) AS afwijking,
			TijdelijkeRapportage.fonds,
			TijdelijkeRapportage.fondsOmschrijving,
      TijdelijkeRapportage.rekening,
      TijdelijkeRapportage.valuta,
			TijdelijkeRapportage.actueleFonds,
			TijdelijkeRapportage.actueleValuta,
			TijdelijkeRapportage.fondsEenheid
			FROM TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.type = 'rekening'  AND
			TijdelijkeRapportage.rapportageDatum = '".$einddatum."' AND
			(TijdelijkeRapportage.portefeuille = \"".$portefeuille['Portefeuille']."\" OR TijdelijkeRapportage.portefeuille = \""."m".$this->pdf->selectData['modelcontrole_portefeuille']."\")  "
				.$__appvar['TijdelijkeRapportageMaakUniek']." 
			GROUP BY $group ".$afwijking."
			ORDER BY afwijking DESC ";
			debugSpecial($query,__FILE__,__LINE__); //

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			$totaalRekeningen = array();
			if(count($gecorigeerdeRekeningen)>0)
			  array_push($totaalRekeningen,array("Liquiditeiten","Model waarde","Herziene waarde"));
			else
        array_push($totaalRekeningen,array("Liquiditeiten","Model waarde","Huidige waarde"));
			//$this->pdf->Row(array(""));
			$portefeuilleRegels[]=array('');
			while($fdata = $DB2->nextRecord())
			{
				$portefeuilleTonen=true;
				$waardeVolgensModel = (($portefTotaal) / 100) * $fdata['percentageModel'];

				if($modelType['Fixed']==3)
					$omschrijvingTxt=$fdata['valuta'];
				else
					$omschrijvingTxt=$fdata['fondsOmschrijving'].' '.$fdata['rekening'];
				array_push($totaalRekeningen,array($omschrijvingTxt,
					$this->formatGetal($waardeVolgensModel,2),
					$this->formatGetal($fdata['portefeuilleWaarde'],2)));
				//}
			}
			//$this->pdf->Row("");
			$portefeuilleRegels[]=array('');
 			foreach($totaalRekeningen as $rekeningRow)
 			{
				$portefeuilleRegels[]=$rekeningRow;
 			   //$this->pdf->Row($rekeningRow);
 			}
//RVV END

// verwijder Data van tijdelijke tabel
			verwijderTijdelijkeTabel($portefeuille['Portefeuille'],$einddatum);

			if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
			}

			if($this->selectData['modelcontrole_level'] != 'fonds') // De Giro Liq fondsen bij Liquiditeiten tellen.
			{
				$liqSom = array();
				$liqCount = array();
				foreach ($portefeuilleRegels as $index => $row)
				{
					if (trim($row[0]) == 'Liquiditeiten' && count($row) > 3)
					{
						$liqSom[0] = 'Liquiditeiten';
						for ($i = 1; $i < count($row); $i++)
						{
							$liqSom[$i] += str_replace(',','.',str_replace('.','',$row[$i]));
						}
						$liqCount[] = $index;
					}
				}
				$aantal = count($liqCount);
				for ($i = 0; $i < $aantal; $i++)
				{
					if ($i == $aantal - 1)
					{
						foreach($liqSom as $veldIndex=>$waarde)
						{
							if($veldIndex<1)
								$portefeuilleRegels[$liqCount[$i]][$veldIndex] =$waarde;
							else
							  $portefeuilleRegels[$liqCount[$i]][$veldIndex] = $this->formatGetal($waarde, 2);
						}
					}
					else
					{
						unset($portefeuilleRegels[$liqCount[$i]]);
					}
				}
			}

			if($portefeuilleTonen ==true || $this->selectData['modelcontrole_uitvoer'] != "afwijkingen")
			{
				$this->pdf->portefeuillewaarde=$portefTotaal;
				$this->pdf->AddPage();
				$this->pdf->SetFont("Times","",10);
				foreach($portefeuilleRegels as $row)
					$this->pdf->Row($row);
			}
		}
		verwijderTijdelijkeTabel("m".$this->pdf->selectData['modelcontrole_portefeuille'],$einddatum);
		
		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputOrder()
	{
	  global $USR;
	  $db=new DB();
    $ordermoduleAccess=GetModuleAccess("ORDER");
   if($ordermoduleAccess==2)
   {
      include_once('orderControlleRekenClassV2.php');
      $ordercheck=new orderControlleBerekeningV2(true);
		  $orderlog = new orderLogs();
		  $fix = new AE_FIXtransport();

     	foreach ($this->orderData as $orderregel)
		  {
      //  listarray($orderregel);
        $extraData=$ordercheck->getPortefeuilleOpties($orderregel['portefeuille'],$orderregel['fonds'],$orderregel['Depotbank']);
        $extraData['transactieSoort']=$ordercheck->getTransactieSoort($orderregel['portefeuille'],$orderregel['fonds'],$orderregel['aantal'],$this->pdf->tmdatum);
//listarray($extraData);
        $extraVelden=array('Depotbank','Rekening','transactieSoort','accountmanager','fondsValuta');
        $orderregel['bron'] = 'modelControle';
        $orderregel['aantal'] = abs($orderregel['aantal']);
        
        foreach($extraVelden as $veld)
        {
          if($extraData[$veld]=='' && $veld<>'Rekening')
            logScherm('Ordercontrole '.$orderregel['portefeuille'].' Veld '.$veld.' is leeg?');
          $orderregel[$veld] = $extraData[$veld];
        }
				$orderregel['fondsBankcode']=$fix->getFondscode($extraData['Depotbank'], $orderregel['fonds']);
        $orderregel['beurs']=$fix->getBeurs($extraData['Depotbank'], $orderregel['fonds']);
					
		    $query="INSERT INTO TijdelijkeBulkOrdersV2 SET add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()";
		    foreach ($orderregel as $veld=>$waarde)
		      $query.=" ,$veld='".mysql_real_escape_string($waarde)."'";
		  //  logscherm($query);
		  //  continue;
		    $db->SQL($query);
		    $db->Query();
				$lastId=$db->last_id();
				$insertedIds[]=$lastId;
				$orderlog->addToBulkLog($lastId,"Portefeuille ".$orderregel['portefeuille']." aangemaakt via Modelcontrole");

				global $__appvar;
        if(isset($__appvar['extraOrderLogging']))
          $extraLog=$__appvar['extraOrderLogging'];
        else
          $extraLog=false;
				if($extraLog)
			  	logIt("Portefeuille ".$orderregel['portefeuille']." fonds:".$orderregel['fonds']." aantal:".$orderregel['aantal']." aangemaakt via Modelcontrole");

			}
     
     if($_POST['extra']=='validatieUitvoeren')
     { 
       if($this->progressbar)
       {
      	 $this->progressbar->moveStep(0);
		  	 $pro_step = 0;
		  	 $pro_multiplier = 100 / count($insertedIds);
         $this->progressbar->show();
       }
       foreach($insertedIds as $id)
       { 
         if($this->progressbar)
			   {
			     $pro_step += $pro_multiplier;
			     $this->progressbar->moveStep($pro_step);
			   }
         $ordercheck->updateChecksByBulkorderregelId($id);
         logScherm('Ordercontrole '.$id. " (".round($pro_step,1)."%)");
       }
     } 
     return array('versie'=>'V2','aantal'=>count($this->orderData),'message'=>count($this->orderData)." orderregels aangemaakt in de tijdelijke orderregel tabel.");

     /*
     echo "<script language=\"JavaScript\" TYPE=\"text/javascript\">
    parent.AEConfirm('".count($this->orderData)." orderregels aangemaakt. Wilt u naar de orderregels gaan?', 'Orderregel verwerking', 
    function () 
    {
       top.frames['content'].location = 'tijdelijkebulkordersv2List.php?resetFilter=1&rapportageInvoer=1';
    }, function () { })
         
		  </script>";
	    exit;
      */

    }
   else
   {
    	$query = "show tables like 'TijdelijkeOrderRegels'";
		  $db->SQL($query);
		  if (!$db->lookupRecord())
		  {
         $table = "CREATE TABLE `TijdelijkeOrderRegels` (
  `id` int(11) NOT NULL auto_increment,
  `fonds` varchar(25) NOT NULL default '',
 			   `portefeuille` varchar(24) NOT NULL default '',
  `modelPercentage` double(8,4) NOT NULL,
  `portefeuillePercentage` double(8,4) NOT NULL,
  `afwijking` double(8,4) NOT NULL,
  			 `valuta` varchar(6) NOT NULL default '',
  		   `kopen` double(12,4) NOT NULL default '0.0000',
  			 `verkopen` double(12,4) NOT NULL default '0.0000',
  			 `overschrijding` double(12,4) NOT NULL default '0.0000',
  			 `modelWaarde` double(12,4) NOT NULL default '0.0000',
  `koers` double(12,4) NOT NULL default '0.0000',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
 			    PRIMARY KEY  (`id`))";
			  $db->SQL($table);
			  $db->Query();
		  }

		  $query="DELETE FROM TijdelijkeOrderRegels WHERE add_user='$USR'";
		  $db->SQL($query);
		  $db->Query();
		  foreach ($this->orderData as $orderregel)
		  {
		    $query="INSERT INTO TijdelijkeOrderRegels SET add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW() ";
		    foreach ($orderregel as $veld=>$waarde)
		      $query.=" ,$veld='".addslashes($waarde)."'";
		    $db->SQL($query);
		    $db->Query();
		  }

		  echo "<script language=\"JavaScript\" TYPE=\"text/javascript\">
		  top.frames['content'].location = 'tijdelijkeorderregelsList.php';
		  </script>";
	    exit;
	}
  }


}
?>