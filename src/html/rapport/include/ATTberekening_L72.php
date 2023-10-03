<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/03 15:54:21 $
 		File Versie					: $Revision: 1.46 $

 		$Log: ATTberekening_L72.php,v $
 		Revision 1.46  2019/04/03 15:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2019/01/26 19:33:28  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2019/01/20 12:14:00  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2018/12/15 17:49:14  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2018/10/17 15:37:17  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2018/09/13 06:53:59  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2018/09/12 14:50:18  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2018/07/07 17:35:19  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2018/03/31 18:06:01  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2018/02/24 18:33:46  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2018/02/21 17:15:09  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2018/02/17 19:18:57  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2018/02/10 18:09:11  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2017/12/13 17:03:35  rvv
 		*** empty log message ***

 		Revision 1.31  2017/12/09 17:54:25  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2017/11/11 18:23:47  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2017/10/25 16:00:03  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/10/08 14:09:23  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/10/02 10:39:42  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2017/08/19 18:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/08/12 12:17:24  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2017/07/19 19:30:54  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/06/10 18:08:40  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/06/09 06:05:51  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/06/07 16:28:08  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2017/05/26 05:39:13  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2017/05/25 14:35:58  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2017/05/03 14:35:54  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/04/15 19:11:50  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/04/05 15:39:45  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/03/29 15:57:04  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/03/23 11:44:51  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/03/15 16:36:10  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/03/08 16:53:32  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/02/25 18:02:28  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/02/22 17:15:06  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/02/15 11:25:53  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/02/01 08:58:17  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/01/29 10:25:25  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/12/30 15:31:00  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/11/30 12:26:19  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/10/09 14:45:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/09/28 15:53:55  rvv
 		*** empty log message ***
 		
 	
 */
class rapportHulp_L72
{
  function rapportHulp_L72()
  {
    $this->test='';
  }
}

class ATTberekening_L72
{

	function ATTberekening_L72($rapportData,$doorkijk=false)
	{
    $this->doorkijk=$doorkijk;
    $this->huidigeCategorie='';
    $this->huisfondsen=array();
    $this->huisfondsVerdeling=array();
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
	  $this->indexPerformance=false;
    if(is_array($this->rapport->pdf->portefeuilles))
      $this->portefeuilleFilter="Portefeuille IN('".implode("','",$this->rapport->pdf->portefeuilles)."')";
    else
      $this->portefeuilleFilter="Portefeuille='".$this->rapport->portefeuille."'";  

	}
  
  function getPerf($portefeuille, $datumBegin, $datumEind,$valuta='')
  {
    $this->rapport = new rapportHulp_L72();
    $db=new DB();
    $query="SELECT * FROM Portefeuilles WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    
    $this->rapport->pdf->portefeuilledata=$db->lookupRecord();
    $this->rapport->portefeuille=$portefeuille;
   	$this->rapport_datumvanaf=db2jul($datumBegin);
	  $this->rapport_datum=db2jul($datumEind);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
    $perfData=$this->bereken($datumBegin, $datumEind,$valuta);
    return $perfData['totaal']['procent'];
  }

  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {

    if ($VierDecimalenZonderNullen)
    {

      $getal = explode('.',$waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000' )
      {
        for ($i = strlen($decimaalDeel); $i >=0; $i--)
        {
          $decimaal = $decimaalDeel[$i-1];
          if ($decimaal != '0' && !isset($newDec))
          {
            //  echo $this->portefeuille." $waarde <br>";exit;
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

	function bereken($van,$tot,$valuta='EUR')
	{
	  global $__appvar;
 		$DB=new DB();

  
    $categorieFilter='Beleggingscategorien';
    $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
    $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    $alleCategorien=array();
    $query="SELECT waarde $selectOmschrijving FROM KeuzePerVermogensbeheerder $join
    WHERE categorie='$categorieFilter' AND 
    Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
    ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde asc";
    $DB->SQL($query); 
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      $alleCategorien[$data['waarde']]=$data['Omschrijving'];
    }
    $this->alleCategorien=$alleCategorien;
//listarray($alleCategorien);
    
    $query="SELECT
    ZorgplichtPerPortefeuille.norm,
    Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving
FROM
Beleggingscategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
WHERE 
ZorgplichtPerPortefeuille.".$this->portefeuilleFilter." AND ZorgplichtPerPortefeuille.extra=0 
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde"; //AND ZorgplichtPerPortefeuille.norm > 0

    $DB->SQL($query);
    $DB->Query();
    
    while($data=$DB->nextRecord())
    {
      $alleCategorienNorm[$data['Beleggingscategorie']]=$data['norm'];
      if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
      $alleCategorien[$data['Beleggingscategorie']]=$data['categorieOmschrijving'];
      /*
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
      */
    }

		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
			$DB->SQL($query);
		  $DB->Query();
      $DB2 = new DB();
		  while($data = $DB->NextRecord())
		  {
        $query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$data['Fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";//" AND Fondsen.Portefeuille IN('GAF')";
        if( $this->doorkijk && $DB2->QRecords($query)==1)
        {
          $huisfonds = $DB2->nextRecord();
          $this->huisfondsen[$data['Fonds']] = $huisfonds;
        }
      if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
        
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
		    $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
		    $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
		    $alleData['fondsen'][]=$data['Fonds'];

		  }

		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		  if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

    $this->huisfondsVerdeling=array();
    foreach($this->huisfondsen as $huisfonds=>$portefeuille)
    {
      $tmpRapport=clone($this->rapport);
      $tmpRapport->portefeuille=$portefeuille['Portefeuille'];
      $huisfondsAtt=new ATTberekening_L72($tmpRapport,false);
      $huisfondsAtt->totalen=array();
      $huidFondsAttWaarden=$huisfondsAtt->bereken($van,$tot,$valuta);
      foreach($huidFondsAttWaarden as $categorie=>$categorieData)
      {
        if (!isset($categorien[$categorie]))
        {
          $categorien[$categorie] = array('omschrijving' => $categorie);
        }
       // $alleCategorienNorm
      }
      $this->huisfondsWaarde[$huisfonds]=$huidFondsAttWaarden;
     foreach($huidFondsAttWaarden as $categorie=>$categorieWaarden)
     {

       foreach($categorieWaarden['perfWaarden'] as $datum=>$maandWaarden)
       {
         if ($maandWaarden['beginwaarde'] <> 0 || $maandWaarden['eindwaarde'] <> 0 || $maandWaarden['gemWaarde'] <> 0)
         {

           $this->huisfondsVerdeling[$datum][$huisfonds]['weging'][$categorie] = $maandWaarden['weging'];
           $this->huisfondsVerdeling[$datum][$huisfonds]['procent'][$categorie] = $maandWaarden['procent'];
         }
       }
     }
    }
    $this->totalen=array();

  //  $this->totalen['gemiddeldeWaarde']=0;
   // $perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true,$valuta);

   // $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];

    foreach($alleCategorien as $categorie=>$omschrijving)
    {
     // if($alleCategorienNorm[$categorie] > 0 && !isset($perCategorie[$categorie]))
        $perCategorie[$categorie]['categorie']=$categorie;
    }

    foreach ($perCategorie as $categorie=>$categorieData)
    {
        $this->huidigeCategorie=$categorie;
      if($this->doorkijk)
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,false,$valuta,$categorie);
      else
        $perfData[$categorie] = $this->fondsPerformanceOld($categorieData,$van,$tot,true,false,$valuta,$categorie);
		    //$this->categorien[$categorie]=$categorieData['omschrijving'];
    }

    foreach($alleCategorien as $categorie=>$omschrijving)
    {
      if(isset($perfData[$categorie]))
        $this->categorien[$categorie]=$omschrijving;
    }

    $alleData['categorie']='totaal';
    $this->huidigeCategorie='totaal';
    if($this->doorkijk)
      $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,true,$valuta,'totaal');
    else
      $perfData['totaal'] = $this->fondsPerformanceOld($alleData,$van,$tot,true,true,$valuta,'totaal');
    //$this->categorien['totaal']='Totaal';
    //listarray($perfData);

    return $perfData;
	}

  function getFondsAandeel($fonds,$datumBegin,$datumEind)
  {
    $beginWaarde=0;
    $eindWaarde=0;
    if(substr($datumBegin,5,5)=='01-01')
      $startJaar=1;
    else
      $startJaar=0;
    $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $datumBegin,$startJaar);
    foreach($fondswaarden as $index=>$fondsData)
    {
      if($fondsData['type']=='fondsen' && $fondsData['fonds']==$fonds)
        $beginWaarde=$fondsData['actuelePortefeuilleWaardeEuro'];
    }
    if(substr($datumEind,5,5)=='01-01')
      $startJaar=1;
    else
      $startJaar=0;
    $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $datumEind,$startJaar);
    foreach($fondswaarden as $index=>$fondsData)
    {
      if($fondsData['type']=='fondsen' && $fondsData['fonds']==$fonds)
        $eindWaarde=$fondsData['actuelePortefeuilleWaardeEuro'];
    }

    $DB=new DB();
    $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."'))  * 
      ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 )  AS onttrekking ".
      "FROM  Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
	               WHERE ".
      " Rekeningmutaties.Verwerkt = '1' AND Portefeuille='" . $this->rapport->portefeuille . "' AND ".
      " Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               Rekeningmutaties.Fonds='$fonds' ";
    $DB->SQL($query);
    $DB->Query();
    $data = $DB->NextRecord();


    $aandeel=0;
    if($beginWaarde==0 && $eindWaarde<>0)
      $aandeel= ($data['gewogen']*-1/$eindWaarde);
    elseif($beginWaarde!=0 && $eindWaarde!=0)
      $aandeel=1;// ($beginWaarde+$eindWaarde)/2/$beginWaarde;
    elseif($beginWaarde!=0)
      $aandeel= ($data['gewogen']/$beginWaarde);
    else
      $aandeel=($data['gewogen']*-1/$data['totaal']);

    //echo "|$datumBegin|$datumEind|$fonds|$beginWaarde|$eindWaarde|".$data['gewogen']."|$aandeel<br>\n";

    return $aandeel;

  }
	function indexPerformance($categorie,$van,$tot,$valuta='EUR')
	{
	  global $__appvar;
    $DB = new DB();


    if($categorie=='totaal')
    {
      $fondsData['Fonds']=$this->specifiekeIndex;
      $fondsData['Percentage']=1;
    }
    else
    {
      $query = "SELECT IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerPortefeuille.norm / 100 as Percentage
FROM
IndexPerBeleggingscategorie
 Join ZorgplichtPerBeleggingscategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '" . $this->rapport->pdf->portefeuilledata['Vermogensbeheerder'] . "'
 Join ZorgplichtPerPortefeuille ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht AND ZorgplichtPerPortefeuille.Vermogensbeheerder='" . $this->rapport->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND ZorgplichtPerPortefeuille.Portefeuille='" . $this->rapport->portefeuille . "'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='" . $this->rapport->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf <= '$van' AND IndexPerBeleggingscategorie.Vanaf <= '$van' AND
ZorgplichtPerPortefeuille.extra=0 AND IndexPerBeleggingscategorie.Portefeuille='" . $this->rapport->portefeuille . "'
order by IndexPerBeleggingscategorie.vanaf desc, ZorgplichtPerPortefeuille.vanaf desc limit 1 ";
      $DB->SQL($query);
      $fondsData = $DB->lookupRecord();
    }
if($fondsData['Fonds']=='')
{
    $query="SELECT IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerPortefeuille.norm / 100 as Percentage
FROM
IndexPerBeleggingscategorie
 Join ZorgplichtPerBeleggingscategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
 Join ZorgplichtPerPortefeuille ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf <= '$van' AND IndexPerBeleggingscategorie.Vanaf <= '$van' AND
ZorgplichtPerPortefeuille.extra=0 AND IndexPerBeleggingscategorie.Portefeuille = ''
order by IndexPerBeleggingscategorie.vanaf DESC, ZorgplichtPerPortefeuille.vanaf desc limit 1 ";
	  $DB->SQL($query);
	  $fondsData=$DB->lookupRecord();

    if($_POST['debug']==1)
    {
      echo "$query <br>\n";
      listarray($fondsData);
    }
}
$fonds=$fondsData['Fonds'];

//    echo "$query <br>\n";

   //  $query="SELECT ModelPortefeuilleFixed.Percentage/100 as Percentage
   //           FROM ModelPortefeuilleFixed
   //           Join Portefeuilles ON Portefeuilles.ModelPortefeuille = ModelPortefeuilleFixed.Portefeuille
   //           WHERE Portefeuilles.Portefeuille='".$this->rapport->portefeuille."' AND Fonds='".$fondsData['Fonds']."' AND Datum <='$tot' ORDER BY Datum DESC LIMIT 1";
	 //  	$DB->SQL($query);
	 //   $percentage=$DB->lookupRecord();
    /*
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
	   	$DB->SQL($query);
	    $startKoers=$DB->lookupRecord();
	    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
		  $DB->SQL($query);
	    $eindKoers=$DB->lookupRecord();
	    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);

    if($categorie=='totaal')
    {
    //  listarray($startKoers);
      echo  " $categorie ".$fondsData['Fonds']." $van $tot | $perf <br>\n";

    }
*/
     // echo ($perf * $fondsData['Percentage']) . " " . $perf . "*" . $fondsData['Percentage'] . "<br>\n";


    $query="SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Valuta as fondsValuta FROM benchmarkverdeling JOIN Fondsen ON benchmarkverdeling.fonds=Fondsen.Fonds WHERE benchmarkverdeling.benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    $fondsValuta=array();
    while($data=$DB->nextRecord())
    {
      $verdeling[$data['fonds']] = $data['percentage'];
      $fondsValuta[$data['fonds']] = $data['fondsValuta'];
    }
    if(count($verdeling)==0)
    {
      $verdeling[$fonds] = 100;
      $query="SELECT Fondsen.Fonds,Fondsen.Valuta as fondsValuta FROM Fondsen  WHERE Fondsen.Fonds='$fonds'";
      $DB->SQL($query);
      $DB->Query();
      $data=$DB->nextRecord();
      $fondsValuta[$data['Fonds']] = $data['fondsValuta'];
    }
   // listarray($fondsValuta);
    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {

      if($fonds!='')
      {
        $koersQuery = '1';

        if ($this->rapport->pdf->rapportageValuta <> '' && $this->rapport->pdf->rapportageValuta <> 'EUR')
        {
          if($fondsValuta[$fonds]<>$this->rapport->pdf->rapportageValuta)
          {
            $koersQuery = "(SELECT koers FROM Valutakoersen WHERE Valuta='" . $this->rapport->pdf->rapportageValuta . "' AND datum <=Fondskoersen.Datum order by datum desc limit 1) ";
          }
        }
        else
        {
          $koersQuery = '1';
        }


        //$query = "SELECT Fondskoersen.Fonds, Fondskoersen.Datum, Fondskoersen.Koers / $koersQuery as Koers FROM Fondskoersen WHERE Fondskoersen.datum  <= '" . substr($tot, 0, 4) . "-01-01' AND Fondskoersen.Fonds='" . $fonds . "' ORDER BY Fondskoersen.Datum DESC LIMIT 1";
        //$DB->SQL($query);
        //$janKoers = $DB->lookupRecord();
        //echo "$fonds  $percentage " . $valuta['valuta'] . " $query <br>\n";
        $query = "SELECT Fondskoersen.Fonds, Fondskoersen.Datum, Fondskoersen.Koers / $koersQuery as Koers FROM Fondskoersen WHERE Fondskoersen.datum  <= '$van' AND Fondskoersen.Fonds='" . $fonds . "' ORDER BY Fondskoersen.Datum DESC LIMIT 1";
        $DB->SQL($query);
        $startKoers = $DB->lookupRecord();

        $query = "SELECT Fondskoersen.Fonds, Fondskoersen.Datum, Fondskoersen.Koers / $koersQuery as Koers FROM Fondskoersen WHERE Fondskoersen.datum  <= '$tot' AND Fondskoersen.Fonds='" . $fonds . "' ORDER BY Fondskoersen.Datum DESC LIMIT 1";
        $DB->SQL($query);
        $eindKoers = $DB->lookupRecord();

        //$perfVoorPeriode = ($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
        //$perfJaar = ($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
        //$perf = $perfJaar - $perfVoorPeriode;
        $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
        $totalPerf += ($perf * $percentage / 100);
        //echo "$categorie $fonds $van -> $tot |  $perf ".$fondsValuta[$fonds]." = $koersQuery ".$this->rapport->pdf->rapportageValuta."<br>\n";
      }
    }
    
    $query="SELECT Normweging as Percentage
FROM NormwegingPerBeleggingscategorie
WHERE
Portefeuille='".$this->rapport->portefeuille."' AND Beleggingscategorie='$categorie'";
    $DB->SQL($query);
    $DB->query();
    if($DB->records()>0)
    {
      $percentage = $DB->nextRecord();
      if($percentage['percentage'])
        $this->normData[$categorie]=$percentage['percentage'];
    }
    else
    {
      $query="SELECT
NormPerRisicoprofiel.Beleggingscategorie,
Portefeuilles.Portefeuille,
NormPerRisicoprofiel.norm as percentage
FROM
NormPerRisicoprofiel
INNER JOIN Portefeuilles ON NormPerRisicoprofiel.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND NormPerRisicoprofiel.Risicoklasse = Portefeuilles.Risicoklasse
WHERE Portefeuilles.Portefeuille='".$this->rapport->portefeuille."'  AND NormPerRisicoprofiel.Beleggingscategorie='$categorie'";
      $DB->SQL($query);
      $DB->query();
      if($DB->records()>0)
      {
        $percentage = $DB->nextRecord();
        if($percentage['percentage'])
          $this->normData[$categorie]=$percentage['percentage'];
      }
      else
        $this->normData[$categorie] = $fondsData['Percentage'] * 100;
    }
    $perf= $totalPerf;
    $tmp= array('perf'=>$perf,'bijdrage'=>$perf*$this->normData[$categorie]/100,'datum'=>$tot,'percentage'=>$this->normData[$categorie]/100,'categorie'=>$categorie);//'koersVan'=>$startKoers['Koers'],'koersEind'=>$eindKoers['Koers'] //,'waarden'=>$waarden)

    return $tmp;
  }



	function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$totaal=false,$valuta='EUR',$categorie)
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));

    global $__appvar;
	  $DB=new DB();

    $debug=false;

    $huisfondsen=array_keys($this->huisfondsen);
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
        if(substr($rapDatum,5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;

        if(!isset($this->totalen[$rapDatum]))
        {
          if ($valuta <> 'EUR' && $valuta <> '')
          {

            $startValutaKoers= getValutaKoers($valuta,$rapDatum);
            $eindValutaKoers= getValutaKoers($valuta,$rapDatum);
          }
          else
          {
            $startValutaKoers= 1;
            $eindValutaKoers= 1;
          }

          $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar,$valuta);
          if($this->doorkijk==true|| $this->rapport->pdf->lastPOST['doorkijk']==1)
          {
            foreach($fondswaarden as $id=>$fondsWaarde)
            {
              if (in_array($fondsWaarde['fonds'], $huisfondsen))
              {
                $periodeAandeel=$this->getFondsAandeel($fondsWaarde['fonds'],$periode['start'],$periode['stop']);

               // echo "Pa $periodeAandeel<br>\n";
                $this->huisfondsVerdeling[$rapDatum][$fondsWaarde['fonds']]['periodeAandeel']=$periodeAandeel;
                $this->huisfondsVerdeling[$rapDatum][$fondsWaarde['fonds']]['huisfondsWaarde']+=$fondsWaarde['actuelePortefeuilleWaardeEuro']/$eindValutaKoers;

                foreach($this->huisfondsVerdeling[$rapDatum][$fondsWaarde['fonds']]['weging'] as $cat=>$aandeel)
                {

                  $this->totalen[$rapDatum]['categorieWaarde'][$cat]+=0;//$fondsWaarde['actuelePortefeuilleWaardeEuro']*$aandeel*0.01;

                }

                $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro']/$eindValutaKoers;
                unset($fondswaarden[$id]);
              }

             // $this->totalen[$rapDatum]['categorieWaarde'][$fondsWaarde['beleggingscategorie']]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];

            }

            //  $fondswaarden = bepaalHuidfondsenVerdeling($this->rapport->portefeuille, $rapDatum);
          }

          foreach($fondswaarden as $id=>$fondsWaarde)
          {
            if($fondsWaarde['type']=='fondsen')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rente')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rekening')
              $instrument=$fondsWaarde['rekening'];
            else
              $instrument='geen';
           // echo "$rapDatum ".$this->totalen[$rapDatum]['totaalWaardeEur']."<br>\n";
            $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro']/$eindValutaKoers;
            $this->totalen[$rapDatum]['WaardeEur'][$instrument]+=$fondsWaarde['actuelePortefeuilleWaardeEuro']/$eindValutaKoers;
            $this->totalen[$rapDatum]['categorieWaarde'][$fondsWaarde['beleggingscategorie']]+=$fondsWaarde['actuelePortefeuilleWaardeEuro']/$eindValutaKoers;

          }
        }

        if(!isset($this->totalen[$rapDatum]['WaardeEur'][$categorie]))
        {
          foreach($this->totalen[$rapDatum]['WaardeEur'] as $instrument=>$waarde)
          {
            if(in_array($instrument,$fondsData['fondsen']) || in_array($instrument,$fondsData['rekeningen']))
            {
              $this->totalen[$rapDatum]['WaardeEur'][$categorie]+=$waarde;
            }
          }
        }


      }
    }
    //listarray($fondsData);

if($this->doorkijk==true|| $this->rapport->pdf->lastPOST['doorkijk']==1 )
{
  if($categorie <> 'totaal1')
  {
    foreach ($fondsData['fondsen'] as $index => $fonds)
    {
      if (in_array($fonds, $huisfondsen))
      {
        unset($fondsData['fondsen'][$index]);
      }
    }
  }
}
/*
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
        $query ="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$rapDatum' ".$__appvar['TijdelijkeRapportageMaakUniek'];
        if(!$DB->QRecords($query))
        {
          if(substr($rapDatum,0,5)=='01-01')
            $startJaar=1;
          else
            $startJaar=0;
	        $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
	       vulTijdelijkeTabel($fondswaarden ,$this->rapport->portefeuille,$rapDatum);
        }
     }
   }
  */
//if($this->doorkijk==true)
//  listarray($this->totalen);

foreach ($perioden as $periode)
{
    $datumBegin=$periode['start'];
    if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
      $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
    else
      $weegDatum=$datumBegin;
    $datumEind=$periode['stop'];

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

  $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
  $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];



  if ($valuta <> 'EUR' && $valuta <> '')
  {
    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    $startValutaKoers= getValutaKoers($valuta,$datumBegin);
    $eindValutaKoers= getValutaKoers($valuta,$datumEind);
    //echo "$startValutaKoers= getValutaKoers($valuta,$datumBegin);<br>\n";
    //echo "$eindValutaKoers= getValutaKoers($valuta,$datumEind);<br>\n";
  }
  else
  {
    $koersQuery = "";
    $startValutaKoers= 1;
    $eindValutaKoers= 1;
  }

      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";


      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	    $beginwaarde = round($start['actuelePortefeuilleWaardeEuro'],2);



      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro))/$startValutaKoers as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	    $eindwaarde = round($eind['actuelePortefeuilleWaardeEuro'],2);
	    //echo "$query <br>\n$eindwaarde $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere <br>\n";


  if($categorie=='totaal')
  {
    $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
    $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];

    if($this->doorkijk==true|| $this->rapport->pdf->lastPOST['doorkijk']==1 )
    {
      $beginwaarde=$this->totalen[$datumBegin]['WaardeEur'][$fondsData['categorie']];
      $eindwaarde=$this->totalen[$datumEind]['WaardeEur'][$fondsData['categorie']];
     // echo "$resultaat=($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
     // listarray($this->totalen[$datumEind]);
     // listarray($this->huisfondsVerdeling);
     // exit;
    }
  }
  else
  {
   // listarray($this->totalen[$datumBegin]['categorieWaarde']);
   // echo $datumBegin." | $categorie | ".$this->totalen[$datumBegin]['categorieWaarde'][$categorie]."<br>\n";
      $beginwaarde = $this->totalen[$datumBegin]['categorieWaarde'][$categorie];
      $eindwaarde = $this->totalen[$datumEind]['categorieWaarde'][$categorie];
  }



	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQuery AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	    $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query();
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten);
       $DB->Query();
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	     $DB->SQL($queryAttributieStortingenOntrekkingen);// echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
  //listarray($queryAttributieStortingenOntrekkingen);
 // listarray($AttributieStortingenOntrekkingen);
   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Kruispost=1 OR Rekeningmutaties.Fonds <> ''  )"; //
	     $DB->SQL($query);//echo "$query <br><br>\n";
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
  //listarray($AttributieStortingenOntrekkingen);

      $queryKostenOpbrengsten = "SELECT SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
	    $DB->SQL($queryKostenOpbrengsten); //echo "$catnaam <br>\n  $queryFondsDirecteKostenOpbrengsten <br>\n $queryKostenOpbrengsten <br>\n";
	     $DB->Query();
	     $nietToegerekendeKosten = $DB->NextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];


  if(count($fondsData['fondsen']) <> 1)
  {
    if (round($eindwaarde, 2) == 0 && round($beginwaarde, 2) == 0)
    {
      $AttributieStortingenOntrekkingen['gewogen'] += 0;
    }
    elseif (round($beginwaarde, 2) == 0)
    {
      $AttributieStortingenOntrekkingen['gewogen'] = $AttributieStortingenOntrekkingen['totaal'] * -1;
    }
    elseif (round($eindwaarde, 2) == 0)
    {
      $AttributieStortingenOntrekkingen['gewogen'] = 0;
    }
  }

  if(!isset($this->totalen[$datumEind]['gemiddeldeWaarde']))
  {
    if($fondsData['categorie']=='totaal')
    {
      $this->totalen[$datumEind]['gemiddeldeWaarde']=$totaalBeginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      //echo $this->totalen[$datumEind]['gemiddeldeWaarde']." = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']." <br>\n";exit;
    }
    else
    {

    $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))$koersQuery AS totaal,
	      SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 
	      FROM Rekeningen
	      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	      JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	      WHERE
        Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	      Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    if($totaalBeginwaarde==0)
      $totaalGemiddelde = $weging['totaal2'];
    else
      $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal'];


    //echo "|rvv| $datumEind $totaalGemiddelde   <br>\n";
    $this->totalen[$datumEind]['gemiddeldeWaarde']=$totaalGemiddelde;
    }
  }

 // listarray($AttributieStortingenOntrekkingen);
//  $AttributieStortingenOntrekkingen=array();
      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
 //echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
//echo $fondsData['categorie']." ". round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
  if($debug && $categorie=='totaal')
  {
  //  listarray($this->totalen);
  //  echo $this->rapport->portefeuille." ".$fondsData['categorie'] . " <br>" . round($performance * 100, 2) . "= ((($eindwaarde - $beginwaarde) - " . $AttributieStortingenOntrekkingen['totaal'] . ") / $gemiddelde) <br>\n";
  }
      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);
$indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);

     // if($totaal==true)
     //   $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;

      $weging=$gemiddelde/$this->totalen[$datumEind]['gemiddeldeWaarde'];
  // echo $fondsData['categorie']." $datumEind | $weging = $gemiddelde / ".$this->totalen[$datumEind]['gemiddeldeWaarde']. " | ".($gemiddelde-$this->totalen[$datumEind]['gemiddeldeWaarde'])." | <br>\n";
    //  $weging=$eindwaarde/$this->totalen[$datumEind]['eindWaarde'];
//if($datumEind=='2017-05-31')
  //exit;
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];

  if($fondsData['categorie']=='LIQ')
  {
    //echo "$datumEind  $resultaat=($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
   // listarray($AttributieStortingenOntrekkingen);
  }
      $bijdrage=$resultaat/$gemiddelde*$weging;


  //listarray($fondsData);
  //  echo $fondsData['categorie']." $bijdrage = $resultaat / $gemiddelde * $weging; <br>\n";
      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=(($performance*$weging)-($indexData['perf']*$indexData['percentage']));//$overPerfPeriode*$weging;
if($debug && $categorie=='totaal')
{
  echo "$weging=$gemiddelde/$totaalGemiddelde $datumBegin -> $datumEind $weegDatum<br>\n";
  echo $indexData['categorie'] . " $performance * $weging = " . ($performance * $weging) . "<br>\n PERF: $performance";//." - ".($indexData['perf']*$indexData['percentage'])." <br>\n";
}
      $waarden[$datumEind]=array('begin'=>$datumBegin,'eind'=>$datumEind,
  'indexPerf'=>$indexData['perf'],
  'indexBijdrage'=>$indexData['bijdrage'],
  'indexBijdrageWaarde'=>$indexData['percentage'],
  'overPerf'=>$overPerfPeriode,
  'relContrib'=>$relContrib,
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage);




  if($this->verdiept==false)
  {
    if (count($this->huisfondsVerdeling) >0)
    {
      if($categorie=='totaal'||1)
        $waarden[$datumEind]['bijdrage']=0; //Later berekenen om huisfondswaarde uit te sluiten.
      unset($waarden[$datumEind]['procentBruto']);

      $totaalHuisFondsWaarde=0;

      foreach($this->huisfondsVerdeling[$datumEind] as $fonds=>$huisFondsWaarden)
      {
        $totaalHuisFondsWaarde+=($huisFondsWaarden['huisfondsWaarde']*$huisFondsWaarden['periodeAandeel']);
      }
      $huisFondsAandeel=$totaalHuisFondsWaarde/$totaalEindwaarde;

      //echo    "$categorie hfa  $totaalHuisFondsWaarde/$totaalEindwaarde;| ".($waarden[$datumEind]['procent'] * (1-$huisFondsAandeel))." =(".$waarden[$datumEind]['procent']." * (1-$huisFondsAandeel))<br>\n";
      $waarden[$datumEind]['procent'] =($waarden[$datumEind]['procent'] * (1-$huisFondsAandeel));
      $waarden[$datumEind]['bijdrage']=($waarden[$datumEind]['bijdrage'] * (1-$huisFondsAandeel));
  //    echo    "$categorie initieel ".$waarden[$datumEind]['procent']."<br>\n";
      foreach($this->huisfondsVerdeling[$datumEind] as $fonds=>$huisFondsWaarden)
      {

        $this->huisfondsVerdeling[$datumEind][$fondsWaarde['fonds']]['huisfondsWaarde'] += ($fondsWaarde['actuelePortefeuilleWaardeEuro']*$huisFondsWaarden['periodeAandeel']);
        $huisfondsAandeel=$huisFondsWaarden['huisfondsWaarde']/$totaalEindwaarde;
if($huisFondsWaarden['procent'][$categorie]<>0)
{
 // echo "$fonds|$categorie|" . ($huisFondsWaarden['procent'][$categorie] * $huisfondsAandeel * $huisFondsWaarden['periodeAandeel']) . "=" . $huisFondsWaarden['procent'][$categorie] . " * $huisfondsAandeel *" . $huisFondsWaarden['periodeAandeel'] . "<br>";
 // echo "$categorie huisfonds :" . $huisFondsWaarden['procent'][$categorie] . " * $huisfondsAandeel *" . ($huisFondsWaarden['periodeAandeel']) . "<br>\n";
}
        $waarden[$datumEind]['procent']+=($huisFondsWaarden['procent'][$categorie] * $huisfondsAandeel *$huisFondsWaarden['periodeAandeel']);



        $waarden[$datumEind]['bijdrage']+=($huisFondsWaarden['procent'][$categorie] *$huisFondsWaarden['weging'][$categorie]*$huisfondsAandeel);

    //    if($datumEind=='2018-01-31' && ($huisFondsWaarden['procent'][$categorie] *$huisFondsWaarden['weging'][$categorie]*$huisfondsAandeel) <> 0)
     //     echo "$categorie $datumEind $fonds bijdrage:".($huisFondsWaarden['procent'][$categorie] *$huisFondsWaarden['weging'][$categorie]*$huisfondsAandeel)."=".$waarden[$datumEind]['procent']." * ". $huisFondsWaarden['weging'][$categorie]." * ".$huisfondsAandeel."<br>\n";
      }

      //$bijdrage=$waarden[$datumEind]['procent'] * $waarden[$datumEind]['aandeelOpTotaal'];
     // echo "$categorie $bijdrage=".$waarden[$datumEind]['procent']." * ".$waarden[$datumEind]['aandeelOpTotaal']."<br>\n";
     // $waarden[$datumEind]['bijdrage']=$bijdrage;


      if($categorie=='totaal' ||1)
      {
        $weging=1-$huisFondsAandeel;
       // echo " $weging =1-$huisFondsAandeel; <br>\n";
        $bijdrage = $resultaat / $gemiddelde * $weging;
       // echo " $bijdrage = $resultaat / $gemiddelde * $weging;<br>\n";
        $waarden[$datumEind]['bijdrage']+=$bijdrage;
      }
  
 //     if($waarden[$datumEind]['procent']<>0)
  //      echo "$categorie $datumEind totaal ".$waarden[$datumEind]['procent']."%| $bijdrage| bijdrage: ".$waarden[$datumEind]['bijdrage']."<br>\n";

    }

//listarray( $this->huisfondsVerdeling);exit;
  }



  if($datumEind=='2018-05-31' && $waarden[$datumEind]['bijdrage']<>0)
  {
  //  echo $fondsData['categorie']." $datumEind Begin bijdrage $bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";
  //  echo $fondsData['categorie']." $datumEind Nieuwe bijdrage ".($waarden[$datumEind]['bijdrage'])."<br>\n";
  }
/*
  if($indexData['categorie']=='Liquiditeiten' && $datumEind=='2012-11-30') 
  {
    echo "$datumEind <br>\n";
    listarray($waarden[$datumEind]);
    exit;
  }
*/  
}
//listarray($fondsData);
if($debug && $categorie=='totaal')
  listarray($waarden);

$stapelItems=array('procent','bijdrage','indexPerf','indexBijdrage','overPerf','relContrib');
$gemiddeldeItems=array('weging','indexBijdrageWaarde');

foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

foreach ($waarden as $datum=>$waarde)
{
  if(!isset($perfData['totaal']['beginwaarde']))
    $perfData['totaal']['beginwaarde'] =$waarde['beginwaarde'];
  $perfData['totaal']['eindwaarde'] =$waarde['eindwaarde'];

  $perfData['totaal']['stort'] +=$waarde['stort'];
  $perfData['totaal']['gemWaarde'] +=$waarde['gemWaarde'];


  $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  foreach($gemiddeldeItems as $item)
   $perfData['totaal'][$item]+=$waarde[$item];
}
foreach($gemiddeldeItems as $item)
  $perfData['totaal'][$item]=$perfData['totaal'][$item]/count($waarden)*100;


  $perfData['totaal']['gemWaarde']=$perfData['totaal']['gemWaarde']/count($waarden);

foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
$perfData['totaal']['categorie']=$fondsData['categorie'];

 if($stapeling == true)
{
  $perfData['totaal']['perfWaarden']=$waarden;
  //listarray($perfData['totaal']);
  return $perfData['totaal'];
}
else
  return array(
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance*100,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage*100);
	}

	function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
         $datum[$i]['start'] = date('Y-m-d',$julBegin);
      else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);

	    if(substr($datum[$i]['start'],5,5)=='12-31')
	     $datum[$i]['start']=(substr($datum[$i]['start'],0,4)+1)."-01-01";

       $i++;
	  }
	  return $datum;
  }
  
function getKwartalen($julBegin, $julEind)
{
   if($julBegin > $julEind )
     return array();
   $beginjaar = date("Y",$julBegin);
   $eindjaar = date("Y",$julEind);
   $maandenStap=3;
   $stap=1;
   $n=0;
   $teller=$julBegin;
   $kwartaalGrenzen=array();
   $datum=array();

   while ($teller < $julEind)
   {
     $teller = mktime (0,0,0,$stap,0,$beginjaar);
     $stap +=$maandenStap;
     if($teller > $julBegin && $teller < $julEind)
     {
     $grensDatum=date("d-m-Y",$teller);
     $kwartaalGrenzen[] = $teller;
     }
   }
   if(count($kwartaalGrenzen) > 0)
   {
     $datum[$n]['start']=date('Y-m-d',$julBegin);
     foreach ($kwartaalGrenzen as $grens)
     {
       $datum[$n]['stop']=date('Y-m-d',$grens);
       $n++;
       $start=date('Y-m-d',$grens);
       if(substr($start,-5)=='12-31')
        $start=(substr($start,0,4)+1).'-01-01';

       $datum[$n]['start']=$start;
     }
     $datum[$n]['stop']=date('Y-m-d',$julEind);
   }
   else
   {
     $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
   }
         return $datum;
}

  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='',$valuta='EUR')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';

      if($valuta=='EUR')
        $koersQuery=",(SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";
      else $koersQuery=', 1 as Rapportagekoers ';


		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers ".
		" $koersQuery ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND $fondsenWhere AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen
		$buffer = array();
		$sortBuffer = array();
		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

	  foreach ($buffer as $mutaties)
		{
			$mutaties['Aantal'] = abs($mutaties['Aantal']);
			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;
      //$mutaties['Rapportagekoers']=1;

			switch($mutaties['Transactietype'])
			{
					case "A" :
					case "A/O" :
					case "A/S" :
 					case "D" :
					case "S" :
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];
						$totaal_aankoop_waarde += $t_aankoop_waarde;
						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "B" :
						// Beginstorting
					break;
					case "L" :
					case "V" :
					case "V/O" :
					case "V/S" :
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];
						$totaal_verkoop_waarde += $t_verkoop_waarde;
						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/
			if($mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V" || $mutaties['Transactietype'] == "V/S" || $mutaties['Transactietype'] == "A/S")
			{
				$historie = berekenHistorischKostprijs($this->rapport->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->rapport->pdf->rapportageValuta);
				if($mutaties['Transactietype'] == "A/S")
				  $rekenAantal=($mutaties['Aantal'] * -1) ;
				else
				  $rekenAantal=$mutaties['Aantal'];

				$historischekostprijs = $rekenAantal       * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
				$beginditjaar         = $rekenAantal       * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];


        if($this->rapport->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->rapport->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
        }
        elseif ($this->rapport->pdf->rapportageValuta != 'EUR')
		    {
		      $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
		    }

				if($historie['voorgaandejarenActief'] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}
				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;
				$totaal_resultaat_waarde += $resultaatlopende;
			}
			else
			{
				$result_historischkostprijs = 0;
				$result_voorgaandejaren = 0;
				$result_lopendejaar = 0;
			}
        //	listarray($mutaties);
				$data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
		  	$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
				$data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
				$data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
				$data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
				$data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
				$data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];
		}
		return $data;
	}



  function fondsPerformanceOld($fondsData,$van,$tot,$stapeling=false,$totaal=false,$valuta='EUR')
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));

    global $__appvar;
    $DB=new DB();

    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
        $query ="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$rapDatum' ".$__appvar['TijdelijkeRapportageMaakUniek'];
        if(!$DB->QRecords($query))
        {
          if(substr($rapDatum,0,5)=='01-01')
            $startJaar=1;
          else
            $startJaar=0;
          $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
          vulTijdelijkeTabel($fondswaarden ,$this->rapport->portefeuille,$rapDatum);
        }
      }
    }


    foreach ($perioden as $periode)
    {
      $datumBegin=$periode['start'];
      if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
        $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
      else
        $weegDatum=$datumBegin;
      $datumEind=$periode['stop'];

      if(!$fondsData['fondsen'])
        $fondsData['fondsen']=array('geen');
      if(!$fondsData['rekeningen'])
        $fondsData['rekeningen']=array('geen');



      if ($valuta <> 'EUR' && $valuta <> '')
      {
        $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
        $startValutaKoers= getValutaKoers($valuta,$datumBegin);
        $eindValutaKoers= getValutaKoers($valuta,$datumEind);
        //echo "$startValutaKoers= getValutaKoers($valuta,$datumBegin);<br>\n";
        //echo "$eindValutaKoers= getValutaKoers($valuta,$datumEind);<br>\n";
      }
      else
      {
        $koersQuery = "";
        $startValutaKoers= 1;
        $eindValutaKoers= 1;
      }




      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";


      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $start = $DB->NextRecord();
      $beginwaarde = round($start['actuelePortefeuilleWaardeEuro'],2);



      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro))/$startValutaKoers as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
      $DB->SQL($query);
      $DB->Query();
      $eind = $DB->NextRecord();
      $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
      $eindwaarde = round($eind['actuelePortefeuilleWaardeEuro'],2);
      //echo "$query <br>\n$eindwaarde $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere <br>\n";

      $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQuery AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking ".
        "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
      $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
      $DB->Query();
      $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

      $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
      $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
      $DB->Query();
      $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
      $DB->SQL($queryFondsDirecteKostenOpbrengsten);
      $DB->Query();
      $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


      $queryAttributieStortingenOntrekkingen = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS onttrekking ".
        "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
      $DB->SQL($queryAttributieStortingenOntrekkingen);// echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
      $DB->Query();
      $AttributieStortingenOntrekkingen = $DB->NextRecord();
      $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

      $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Kruispost=1 OR Rekeningmutaties.Fonds <> ''  )"; //
      $DB->SQL($query);//echo "$query <br><br>\n";
      $DB->Query();
      $data = $DB->nextRecord();
      $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
      $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
      $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];


      $queryKostenOpbrengsten = "SELECT SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
      $DB->SQL($queryKostenOpbrengsten); //echo "$catnaam <br>\n  $queryFondsDirecteKostenOpbrengsten <br>\n $queryKostenOpbrengsten <br>\n";
      $DB->Query();
      $nietToegerekendeKosten = $DB->NextRecord();
      $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];


      if(count($fondsData['fondsen']) <> 1)
      {
        if (round($eindwaarde, 2) == 0 && round($beginwaarde, 2) == 0)
        {
          $AttributieStortingenOntrekkingen['gewogen'] += 0;
        }
        elseif (round($beginwaarde, 2) == 0)
        {
          $AttributieStortingenOntrekkingen['gewogen'] = $AttributieStortingenOntrekkingen['totaal'] * -1;
        }
        elseif (round($eindwaarde, 2) == 0)
        {
          $AttributieStortingenOntrekkingen['gewogen'] = 0;
        }
      }

      if(!isset($this->totalen[$datumEind]['gemiddeldeWaarde']))
      {
        if($fondsData['categorie']=='totaal')
        {
          $this->totalen[$datumEind]['gemiddeldeWaarde']=$beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
          //echo $this->totalen[$datumEind]['gemiddeldeWaarde']." = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']." <br>\n";
        }
        else
        {
          $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / $startValutaKoers as actuelePortefeuilleWaardeEuro  FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin'".$__appvar['TijdelijkeRapportageMaakUniek'];
          $DB->SQL($query);
          $DB->Query();
          $start = $DB->NextRecord();
          $totaalBeginwaarde = $start['actuelePortefeuilleWaardeEuro'];

          $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))$koersQuery AS totaal,
	      SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 
	      FROM Rekeningen
	      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	      JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	      WHERE
        Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	      Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
          $DB->SQL($query);
          $DB->Query();
          $weging = $DB->NextRecord();
          if($totaalBeginwaarde==0)
            $totaalGemiddelde = $weging['totaal2'];
          else
            $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal'];


          //echo "|rvv| $datumEind $totaalGemiddelde   <br>\n";
          $this->totalen[$datumEind]['gemiddeldeWaarde']=$totaalGemiddelde;
        }
      }


      // listarray($AttributieStortingenOntrekkingen);
//  $AttributieStortingenOntrekkingen=array();
      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      //echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
//echo $fondsData['categorie']." ". round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
//echo $fondsData['categorie']. " ".round($performance*100,2)."= ((($eindwaarde - $beginwaarde) - ". $AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde) <br>\n";
  
      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);
      $indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);

      // if($totaal==true)
      //   $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;

      $weging=$gemiddelde/$this->totalen[$datumEind]['gemiddeldeWaarde'];
      // echo $fondsData['categorie']." $datumEind | $weging = $gemiddelde / ".$this->totalen[$datumEind]['gemiddeldeWaarde']. " | ".($gemiddelde-$this->totalen[$datumEind]['gemiddeldeWaarde'])." | <br>\n";
      //  $weging=$eindwaarde/$this->totalen[$datumEind]['eindWaarde'];
//if($datumEind=='2017-05-31')
      //exit;
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      if($fondsData['categorie']=='LIQ')
      {
        //echo "$datumEind  $resultaat=($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
        // listarray($AttributieStortingenOntrekkingen);
      }
      $bijdrage=$resultaat/$gemiddelde*$weging;
      //listarray($fondsData);
      //  echo $fondsData['categorie']." $bijdrage = $resultaat / $gemiddelde * $weging; <br>\n";
      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=(($performance*$weging)-($indexData['perf']*$indexData['percentage']));//$overPerfPeriode*$weging;
//echo "$weging=$gemiddelde/$totaalGemiddelde $datumBegin -> $datumEind $weegDatum<br>\n";
//      echo $indexData['categorie']." $performance * $weging = ".($performance*$weging)."<br>\n";//." - ".($indexData['perf']*$indexData['percentage'])." <br>\n";

      $waarden[$datumEind]=array(
        'indexPerf'=>$indexData['perf'],
        'indexBijdrage'=>$indexData['bijdrage'],
        'indexBijdrageWaarde'=>$indexData['percentage'],
        'overPerf'=>$overPerfPeriode,
        'relContrib'=>$relContrib,
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
        'resultaat'=>$resultaat,
        'gemWaarde'=>$gemiddelde,
        'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
        'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
        'weging'=>$weging,
        'bijdrage'=>$bijdrage);
      /*
        if($indexData['categorie']=='Liquiditeiten' && $datumEind=='2012-11-30')
        {
          echo "$datumEind <br>\n";
          listarray($waarden[$datumEind]);
          exit;
        }
      */
    }
//listarray($fondsData);
//listarray($waarden);

    $stapelItems=array('procent','bijdrage','indexPerf','indexBijdrage','overPerf','relContrib');
    $gemiddeldeItems=array('weging','indexBijdrageWaarde');

    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=1;

    foreach ($waarden as $datum=>$waarde)
    {
      if(!isset($perfData['totaal']['beginwaarde']))
        $perfData['totaal']['beginwaarde'] =$waarde['beginwaarde'];
      $perfData['totaal']['eindwaarde'] =$waarde['eindwaarde'];

      $perfData['totaal']['stort'] +=$waarde['stort'];
      $perfData['totaal']['gemWaarde'] +=$waarde['gemWaarde'];


      $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
      foreach ($stapelItems as $item)
        $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
      foreach($gemiddeldeItems as $item)
        $perfData['totaal'][$item]+=$waarde[$item];
    }
    foreach($gemiddeldeItems as $item)
      $perfData['totaal'][$item]=$perfData['totaal'][$item]/count($waarden)*100;


    $perfData['totaal']['gemWaarde']=$perfData['totaal']['gemWaarde']/count($waarden);

    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
    $perfData['totaal']['categorie']=$fondsData['categorie'];

    if($stapeling == true)
    {
      $perfData['totaal']['perfWaarden']=$waarden;
      //listarray($perfData['totaal']);
      return $perfData['totaal'];
    }
    else
      return array(
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance*100,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
        'resultaat'=>$resultaat,
        'gemWaarde'=>$gemiddelde,
        'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
        'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
        'weging'=>$weging,
        'bijdrage'=>$bijdrage*100);
  }
}


?>