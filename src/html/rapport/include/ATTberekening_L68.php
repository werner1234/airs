<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/24 15:15:19 $
 		File Versie					: $Revision: 1.41 $

 		$Log: ATTberekening_L68.php,v $
 		Revision 1.41  2020/06/24 15:15:19  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2020/06/18 05:47:16  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2020/05/30 15:31:00  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2020/05/20 17:13:47  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2020/05/04 16:41:40  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2020/03/25 16:43:07  rvv
 		*** empty log message ***

 		Revision 1.34  2019/12/21 14:08:32  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2019/12/07 17:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2019/11/27 15:55:39  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2019/11/20 16:19:15  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2019/10/13 09:31:14  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2019/10/02 15:12:58  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/09/14 17:09:05  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2019/09/11 15:48:05  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/09/07 16:08:10  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/08/24 16:59:19  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/08/17 18:24:00  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2019/07/17 15:34:55  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2019/06/29 18:24:12  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2019/06/26 15:11:21  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2019/06/22 16:32:52  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/05/16 15:32:27  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/03/17 18:48:55  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/02/04 15:47:34  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/11/27 06:47:10  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/09/02 17:15:13  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/07/23 13:36:28  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/07/19 19:30:24  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/07/03 11:27:20  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/07/01 13:24:28  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/06/18 09:18:24  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/04/29 17:26:01  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/02/25 18:02:28  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/01/04 16:22:50  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/12/17 16:33:26  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/02 12:38:58  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/09/25 18:53:45  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/05/08 19:24:24  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/05/04 16:08:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/19 16:51:09  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/12/19 09:03:50  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/10/04 11:52:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		

 */


class ATTberekening_L68
{

	function ATTberekening_L68($rapportData,$periode='maanden',$verdiept=false)
	{
    $this->rapport=&$rapportData;

   	$this->rapportageDatumVanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar=date('Y',$this->rapportageDatumVanaf);
	  $this->indexPerformance=false;
    $this->perioden=$periode;
    $this->totalen=array();
    $this->huisfondsen=array();
    $this->huisfondsWaarde=array();
    $this->categorien=array('totaal'=>'Totaal');
    $this->huidigeCategorie='';
    $this->verdiept=$verdiept;
    $this->huisfondsAandeelOpDatum=array();
    $this->hpiData=array();
    $this->periodenOrigineel=array();
    $this->gebruikHPI=true;
    $this->vanOrigineel='';
    $this->benchmarkPortefeuille='';//$this->rapport->portefeuille;
    $this->hpiMissendePerioden=array();
	}

	function bereken($van,$tot,$verdeling='Hoofdcategorie',$gebruikHPI=true)
	{
	  global $__appvar;
 		$DB=new DB();
    //$data=$this->getAllHPIData($van,$tot,$this->rapport->portefeuille);
    //if(count($data)>0)
    //  return $data;
    $this->vanOrigineel=$van;
    
    if($this->perioden=='jaar')
      $this->periodenOrigineel=$this->getJaren(db2jul($van),db2jul($tot));
    elseif($this->perioden=='kwartalen')
      $this->periodenOrigineel=$this->getKwartalen(db2jul($van),db2jul($tot));
    elseif($this->perioden=='weken')
      $this->periodenOrigineel=$this->getWeken(db2jul($van),db2jul($tot));
    else
      $this->periodenOrigineel=$this->getMaanden(db2jul($van),db2jul($tot));
    
    $query="SELECT date(Startdatum) as Startdatum,Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".mysql_real_escape_string($this->rapport->portefeuille) ."'";
    $DB->SQL($query);
    $startdatum=$DB->lookupRecord();
    //echo "voor: $gebruikHPI ||";

    $beginMaanden=array('01-01','01-31','02-28','02-29','03-31','04-30','05-31','06-30','07-31','08-31','09-30','10-31','11-30');
    $eindMaanden=array('01-01','01-31','02-28','02-29','03-31','04-30','05-31','06-30','07-31','08-31','09-30','10-31','11-30','12-31');
    if($van!=$startdatum['Startdatum'])
    {
      if (!in_array(substr($van, 5, 5), $beginMaanden) && substr($van, 8, 2) <> '01')
      {
        $gebruikHPI = false;
      }
      if (!in_array(substr($van, 5, 5), $eindMaanden))
      {
        $gebruikHPI = false;
      }
    }
    //$eindMaanden
    $this->gebruikHPI=$gebruikHPI;
 
    $teller=0;
    if($gebruikHPI==true)
    {
      foreach($this->periodenOrigineel as $periode)
      {
        $rekenPeriode=$periode;
        $beginDatum=$periode['start'];
        $eindDatum=$periode['stop'];
        $periode='geen';
        $dagen = round((db2jul($eindDatum) - db2jul($beginDatum)) / 86400);
        if ($dagen == 7)
        {
          $periode = 'w';
        }
        elseif ($dagen == 14)
        {
          $periode = '2w';
        }
        elseif ($dagen >= 28 && $dagen<=31)
        {
          $beginMaandDag=substr($beginDatum,5,57);
          if(in_array($beginMaandDag,$beginMaanden))
            $periode = 'm';
        }
        elseif ($dagen>60 && $dagen < 100)
        {
          $periode = 'k';
        }
        elseif($dagen>200)
        {
          $periode = 'j';
        }
        else
        {
          //if($teller==0)
          //  $periode=substr($this->perioden,0,1);
          //else
            $periode='geen';
        }
        $teller++;
//echo "$beginDatum -> $eindDatum = $dagen | $periode $beginMaandDag <br>";
        $somVelden=array('resultaat','storting','onttrekking','kosten','opbrengst','stort','fondsMutaties','rente');
        $somArrayVelden=array('grootboekOpbrengsten','grootboekKosten');
        $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, stortingen, onttrekkingen, opbrengsten, kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra,gemiddelde
	  	            FROM HistorischePortefeuilleIndex
	  	            WHERE periode='$periode' AND
	  	            portefeuille = '" . $this->rapport->portefeuille . "' AND
	  	            Datum = '" . substr($eindDatum, 0, 10) . "' ";
        if ($DB->QRecords($query) > 0)
        {
          while($data = $DB->nextRecord())
          {
            $hpiData = unserialize($data['extra']);
          //  listarray($hpiData);exit;
            if(!isset($hpiData['periode']))
              $hpiData['periode'] = $beginDatum . "->" . $eindDatum;
            $hpiData['periodeForm'] = date("d-m-Y", db2jul($beginDatum)) . " - " . date("d-m-Y", db2jul($eindDatum));
            $hpiData['database'] = 1;
            $hpiData['valuta'] = 'EUR';
//listarray($hpiData);
            if(!isset($this->hpiData[$data['Categorie']]['beginwaarde']))
              $this->hpiData[$data['Categorie']]['beginwaarde']=$hpiData['beginwaarde'];
            $this->hpiData[$data['Categorie']]['eindwaarde']=$hpiData['eindwaarde'];
            foreach($somVelden as $veld)
              $this->hpiData[$data['Categorie']][$veld]+=$hpiData[$veld];
            foreach($somArrayVelden as $veld)
            {
              foreach ($hpiData[$veld] as $key => $value)
              {
                $this->hpiData[$data['Categorie']][$veld][$key] += $value;
                //if($veld=='grootboekKosten'){ echo $eindDatum." $veld:$key:". $this->hpiData[$data['Categorie']][$veld][$key]."<br>\n"; listarray($hpiData[$veld]);};
              }
            
            }
            if(isset($hpiData['perfWaarden'][$eindDatum]))
            {
              $this->hpiData[$data['Categorie']]['perfWaarden'][$eindDatum] = $hpiData['perfWaarden'][$eindDatum];

            }
 //           $van = $eindDatum;
          }
        }
        else
        {
          $this->hpiMissendePerioden[$eindDatum]=$rekenPeriode;
        }
      }
    }

//listarray($this->hpiMissendePerioden);exit;

    
    $orderBy='';
    $selectOmschrijving='';
    $join='';
    if($verdeling=='Hoofdcategorie')
    {
      $categorieFilter='Hoofdcategorie';
      //$categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }
    elseif($verdeling=='totaal')  
    {  
      $categorieFilter='geen';
    }
    elseif($verdeling=='sector')
    {
      $categorieFilter='Beleggingssectoren';
      $join="LEFT JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector";
      $selectOmschrijving=',Beleggingssectoren.Omschrijving';
    }
    elseif($verdeling=='regio')
    {
      $categorieFilter='Regios';
      $join="LEFT JOIN Regios ON KeuzePerVermogensbeheerder.waarde = Regios.Regio";
      $selectOmschrijving=',Regios.Omschrijving';
    }
    elseif($verdeling=='attributie')
    {
      $categorieFilter='AttributieCategorien';
      $join="LEFT JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde = AttributieCategorien.AttributieCategorie";
      $selectOmschrijving=',AttributieCategorien.Omschrijving';
      $orderBy="AttributieCategorien.Afdrukvolgorde, ";
    }
    else
    { 
      $categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }

    $query="SELECT waarde $selectOmschrijving FROM KeuzePerVermogensbeheerder $join
    WHERE categorie='$categorieFilter' AND 
    Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
    ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query); 
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      $tmp[$data['waarde']]=array('categorie'=>$data['waarde'],'omschrijving'=>$data['Omschrijving']);
    }
    $perHoofdcategorie=$tmp;
    $perRegio=$tmp;
    $perSector=$tmp;
    $perCategorie=$tmp;  

    $query="SELECT
    Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving
FROM
Beleggingscategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
WHERE 
ZorgplichtPerPortefeuille.Portefeuille = '".$this->rapport->portefeuille."' AND ZorgplichtPerPortefeuille.extra=0 AND
ZorgplichtPerPortefeuille.norm > 0
ORDER BY Beleggingscategorien.Afdrukvolgorde";

    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
    } 

		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
BeleggingssectorPerFonds.Beleggingssector,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
BeleggingssectorPerFonds.AttributieCategorie,
AttributieCategorien.Omschrijving as attributieOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$startdatum['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$startdatum['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT JOIN AttributieCategorien ON BeleggingssectorPerFonds.AttributieCategorie = AttributieCategorien.AttributieCategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$startdatum['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$startdatum['Startdatum']."' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY $orderBy  HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";

			$DB->SQL($query); 
		  $DB->Query();
      $DB2=new DB();
		  while($data = $DB->NextRecord())
		  {
        $query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$data['Fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
        if($this->rapport->pdf->lastPOST['doorkijk']==1 && $DB2->QRecords($query)==1)
        {
          $huisfonds = $DB2->nextRecord();
          $this->huisfondsen[$data['Fonds']] = $huisfonds;
        }

		    if($data['Hoofdcategorie']=='')
          $data['Hoofdcategorie']='Geen H-cat';

 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }

        if($data['AttributieCategorie']=='')
        {
            $data['AttributieCategorie']='Geen attributiecategorie';
            $data['attributieOmschrijving']=$data['Geen attributiecategorie'];
        }
        
        if($data['Beleggingscategorie']=='')
          $data['Beleggingscategorie']='Geen cat';     
                            
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perInstrument[$data['Fonds']]['omschrijving']=$data['FondsOmschrijving'];
        $perInstrument[$data['Fonds']]['fondsen'][]=$data['Fonds'];
        $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		    $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
		    $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
        $perAttributie[$data['AttributieCategorie']]['omschrijving']=$data['attributieOmschrijving'];
        $perAttributie[$data['AttributieCategorie']]['fondsen'][]=$data['Fonds'];
        $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
		    $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['hoofdOmschrijving']=$data['hoofdCategorieOmschrijving'];
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
        $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
        $alleData['fondsen'][]=$data['Fonds'];

		  }

		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Rekeningen.AttributieCategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
AttributieCategorien.Omschrijving AS attributieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$startdatum['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$startdatum['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$startdatum['Vermogensbeheerder']."'
LEFT JOIN AttributieCategorien ON Rekeningen.AttributieCategorie = AttributieCategorien.AttributieCategorie
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$startdatum['Startdatum']."' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		  if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }
      if($data['Beleggingscategorie']=='')
        $data['Beleggingscategorie']='Geen cat';

      if($data['AttributieCategorie']=='')
      {
        $data['AttributieCategorie']='Liquiditeiten';
        $data['attributieOmschrijving']='Liquiditeiten';
      }

		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perInstrument[$data['rekening']]['omschrijving']=$data['rekening'];
      $perInstrument[$data['rekening']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		  $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
      $perAttributie[$data['AttributieCategorie']]['omschrijving']=$data['attributieOmschrijving'];
      $perAttributie[$data['AttributieCategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      
      $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['hoofdOmschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
      $perHcatCat[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      
		  $alleData['rekeningen'][]=$data['rekening'];
	  }
 
    //$this->totalen['gemiddeldeWaarde']=0;
    //$perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true);
    
    if($verdeling=='Hoofdcategorie')
      $categorien=$perHoofdcategorie;
    elseif($verdeling=='totaal')  
      $categorien=array();
    elseif($verdeling=='attributie')
      $categorien=$perAttributie;
    elseif($verdeling=='regio')
      $categorien=$perRegio;
    elseif($verdeling=='instrument')
      $categorien=$perInstrument;
    elseif($verdeling=='sector')
      $categorien=$perSector;
    else
      $categorien=$perCategorie;

    $this->verdelingen=array('Hoofdcategorie'=>$perHoofdcategorie,'attributie'=>$perAttributie,'regio'=>$perRegio,'instrument'=>$perInstrument,'sector'=>$perSector,'beleggingscategorie'=>$perCategorie,'perHcatCat'=>$perHcatCat);
    foreach($this->huisfondsen as $huisfonds=>$portefeuille)
    {
      $tmpRapport=clone($this->rapport);
      $tmpRapport->portefeuille=$portefeuille['Portefeuille'];
      $huisfondsAtt=new ATTberekening_L68($tmpRapport,$this->perioden,true);
      $huisfondsAtt->hpiMissendePerioden=$this->hpiMissendePerioden;
      //echo "$huisfonds $van,$tot,$verdeling, <br>\n";
      $huidFondsAttWaarden=$huisfondsAtt->bereken($van,$tot,$verdeling,$gebruikHPI);
      foreach($huidFondsAttWaarden as $categorie=>$categorieData)
        if(!isset($categorien[$categorie]))
          $categorien[$categorie]=array('omschrijving'=>$categorie);
      $this->huisfondsWaarde[$huisfonds]=$huidFondsAttWaarden;
      //echo "$huisfonds=> ".$portefeuille['Portefeuille']." $van,$tot,$verdeling,$gebruikHPI <br>\n";
     // listarray($huidFondsAttWaarden);
    }
   // if($this->verdiept==false)
   //   listarray($this->huisfondsen);


    $this->totalen['gemiddeldeWaarde']=0;
    foreach ($categorien as $categorie=>$categorieData)
    { 
        $this->huidigeCategorie=$categorie;
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,$categorie);
        if($categorieData['omschrijving']=='')
          $categorieData['omschrijving']=$categorie;
		    $this->categorien[$categorie]=$categorieData['omschrijving'];
    }
    $this->huidigeCategorie='totaal';
    $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,'totaal');
    //$this->categorien['totaal']='Totaal';
 
    return $perfData;
	}

	function indexPerformance($categorie,$van,$tot,$portefeuille)
	{
    $tmp=$this->benchmarkVerdelingOpDatum($tot,$categorie,$portefeuille);

    if(count($tmp)==0)
      $this->planVerdelingNotSet[$categorie][]=$tot;
    $perf=$this->getBenchmarkPerformance($tmp,$van,$tot);
  //  echo  "$categorie $van,$tot $perf <br>\n";listarray($tmp);//exit;
    //$perf=getFondsPerformanceGestappeld($fonds,$this->rapport->portefeuille,$van,$tot,'maanden');
    return array('perf'=>($perf/100),'verdeling'=>$tmp);
  }
  
  
  function getBeleggingsplan($portefeuille,$datum)
  {
    $DB=new DB();
    $query="SELECT Beleggingsplan.ProcentRisicoDragend/100 as ZAK,
Beleggingsplan.ProcentRisicoMijdend/100 as VAR,
(100-Beleggingsplan.ProcentRisicoDragend-Beleggingsplan.ProcentRisicoMijdend)/100 as Liquiditeiten
FROM
Beleggingsplan
WHERE  Beleggingsplan.Portefeuille='$portefeuille' AND (datum <= '".$datum."' OR datum='0000-00-00') ORDER by datum desc limit 1";
    $DB->SQL($query);
    $DB->Query();
    $data=$DB->nextRecord();
    return $data;
  }
  
  
  function benchmarkVerdelingOpDatum($datum,$categorie,$inputPortefeuille='')
  {
    if($inputPortefeuille<>'')
      $portefeuille=$inputPortefeuille;
    else
      $portefeuille=$this->rapport->portefeuille;
//echo "$datum  |  $categorie | $portefeuille |  " . $this->rapport->portefeuille."<br>\n";
    if($categorie=='ZAK')
    {
      $fondsVerdeling=$this->getIndexPerBeeleggingscategorie($this->rapport->portefeuille, $datum, $categorie);
      return $fondsVerdeling;
    }

    $doorkijkfondsen=array();
    if($this->rapport->pdf->lastPOST['doorkijk']==1)
    {
      if(count($this->doorkijkfondsen)==0)
        $this->getDoorkijkfondsen();
      $doorkijkfondsen=$this->doorkijkfondsen;
    }

    $portefeuilles=array();
    if(count($this->rapport->pdf->portefeuilles) > 1 && $inputPortefeuille=='')
      $portefeuilles=$this->rapport->pdf->portefeuilles;
    else
      $portefeuilles[]=$portefeuille;
    
    $portefeuillesAandeel=array();
    if(count($portefeuilles)>0)
    {
      $portefeuilleWaarde=array();
      $portefeuilleWaardeAbs=array();
      $totaleWaarde=0;
      $totaleWaardeAbs=0;
      foreach($portefeuilles as $portefeuille)
      {
        $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false, $this->rapport->pdf->rapportageValuta, $datum);
        foreach ($fondsRegels as $regel)
        {
          if(isset($doorkijkfondsen[$regel['fonds']]))
          {
            $portefeuilleWaarde[$doorkijkfondsen[$regel['fonds']]]+=$regel['actuelePortefeuilleWaardeEuro'];
          }
          else
          {
            $portefeuilleWaarde[$portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
          }
          $totaleWaarde+= $regel['actuelePortefeuilleWaardeEuro'];
        }
        if(count($portefeuilleWaarde)==0)
          $portefeuilleWaarde[$portefeuille]=0;
      }

      foreach($portefeuilleWaarde as $portefeuille=>$waarde)
      {
        $portefeuilleWaardeAbs[$portefeuille] += abs($waarde);
        $totaleWaardeAbs += abs($waarde);
      }

      foreach($portefeuilleWaarde as $portefeuille=>$waarde)
      {
        if($waarde==0 && $totaleWaarde==0)
          $portefeuillesAandeel[$portefeuille]=1;
        else
          $portefeuillesAandeel[$portefeuille]=($waarde)/$totaleWaarde;
      }
    }
    else
    {
      $portefeuillesAandeel[$this->rapport->portefeuille] = 1;
    }
    
    if(count($portefeuillesAandeel)==0)
      $portefeuillesAandeel[$this->rapport->portefeuille] = 1;

    $becnhmarkVerdelingTotaal=array();
    $planVerdeling=array();
    $planTotalen=array();
    foreach($portefeuillesAandeel as $portefeuille=>$portefeuilleAandeel)
    {

      $plan=$this->getBeleggingsplan($portefeuille,$datum);
 /*
      if($categorie=='totaal')
      {
        echo "$portefeuille plansum:".array_sum($plan)." <br>\n";
      listarray($plan);
      }
   */
      foreach($plan as $categoriePlan=>$categorieAandeel)
      {
        $benchmark=$this->getIndexPerBeeleggingscategorie($portefeuille, $datum, $categoriePlan);
        $planVerdeling[$categoriePlan][$benchmark]+=$portefeuilleAandeel*$categorieAandeel;
        $planTotalen[$categoriePlan]+=$portefeuilleAandeel*$categorieAandeel;
        $becnhmarkVerdelingTotaal[$benchmark]+=$portefeuilleAandeel*$categorieAandeel;
     /*
        if($categorie=='totaal')
        {
          echo "$datum | $portefeuille | $benchmark | " . $becnhmarkVerdelingTotaal[$benchmark] . " | " . ($portefeuilleAandeel * $categorieAandeel) . " = $portefeuilleAandeel*$categorieAandeel <br>\n";
        }
     */
     
      }
    }

    if($categorie=='totaal')
    {
      /*
     listarray($portefeuillesAandeel); listarray(array_sum(array_values($portefeuillesAandeel)));
     echo $portefeuillesAandeel[$portefeuille]." | $portefeuille, $datum <br>\n";
     listarray($becnhmarkVerdelingTotaal); echo (array_sum($becnhmarkVerdelingTotaal)*100)."%<br>\n";//exit;
        */
      if(round(array_sum($becnhmarkVerdelingTotaal)*100) <> 100)
        $becnhmarkVerdelingTotaal=array();
      $this->planTotalen[$datum]=$planTotalen;

      return $becnhmarkVerdelingTotaal;
    }
    if($categorie=='VAR')
    {
      $becnhmarkVerdelingVAR=array();
      $varPlanTotaal=$planTotalen['VAR']+$planTotalen['Liquiditeiten'];
      //listarray($planVerdeling);
      foreach($planVerdeling as $categoriePlan=>$fondsData)
      {
        if($categoriePlan=='VAR'||$categoriePlan=='Liquiditeiten')
        {
          foreach($fondsData as $fonds=>$fondsAandeel)
          {
            $becnhmarkVerdelingVAR[$fonds]+=$fondsAandeel/$varPlanTotaal;
            //echo $becnhmarkVerdelingVAR[$fonds]."+=$fondsAandeel/$varPlanTotaal;<br>\n";
          }
        }
      }
      return $becnhmarkVerdelingVAR;
    }
  }
  
  function getDoorkijkfondsen()
  {
    $DB=new DB();
    $query="SELECT
Fondsen.Fonds,
Fondsen.Portefeuille,
Portefeuilles.Vermogensbeheerder
FROM
Fondsen
INNER JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
LEFT JOIN Beleggingsplan ON Portefeuilles.Portefeuille = Beleggingsplan.Portefeuille
WHERE Fondsen.Portefeuille<>'' AND Portefeuilles.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $huisfondsen[$data['Fonds']]=$data['Portefeuille'];
    }
    $this->doorkijkfondsen=$huisfondsen;
  }
  
  
  function getIndexPerBeeleggingscategorie($portefeuille,$datum,$categorie)
  {
    $DB=new DB();
    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='') AND (vanaf < '$datum' OR vanaf ='0000-00-00') AND Beleggingscategorie='$categorie'
            ORDER BY IndexPerBeleggingscategorie.Portefeuille, vanaf desc limit 1";
    $DB->SQL($query);
    $DB->Query();
    $index=$DB->nextRecord();
    // echo "$datum $categorie ".$index['Fonds']."<br>\n";
    return $index['Fonds'];
    
  }
  
  function getBenchmarkPerformance($fonds,$beginDatum,$eindDatum)
  {
    if(is_array($fonds))
    {
      $perf=0;
      foreach($fonds as $fondsDetail=>$percentage)
      {
        $beginKoers = globalGetFondsKoers($fondsDetail, $beginDatum);
        $eindKoers = globalGetFondsKoers($fondsDetail, $eindDatum);
        
        $perf += ($eindKoers - $beginKoers) / ($beginKoers) *$percentage*100;
        // echo "$beginDatum->$eindDatum  $fondsDetail ".(($eindKoers - $beginKoers) / ($beginKoers) )."  | $percentage;<br>\n";
        // echo "$eindDatum $fondsDetail |  som=$perf |  ".(($eindKoers - $beginKoers) / ($beginKoers) *$percentage)." = ($eindKoers - $beginKoers) / ($beginKoers) *$percentage;<br>\n";
      }
    }
    else
    {
      $beginKoers = globalGetFondsKoers($fonds, $beginDatum);
      $eindKoers = globalGetFondsKoers($fonds, $eindDatum);
      $perf = ($eindKoers - $beginKoers) / ($beginKoers / 100);
    }
    //echo $perf."<br>\n";
    return $perf;
  }



	function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$categorie='')
  {
    global $__appvar;

    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
    {
      if($this->perioden=='jaar')
        $perioden=$this->getJaren(db2jul($van),db2jul($tot));
      elseif($this->perioden=='kwartalen')
        $perioden=$this->getKwartalen(db2jul($van),db2jul($tot));
      else
        $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    }
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

   //
  //    $grootboekKostenFilter='OR Grootboekrekeningen.Kosten =1';
   // else
   //   $grootboekKostenFilter='';  

	  $DB=new DB();

    $huisfondsen=array_keys($this->huisfondsen);
    
    foreach ($this->periodenOrigineel as $periode)
    {

      if(isset($this->hpiData[$categorie]['perfWaarden'][$periode['stop']]))
      {
        continue;
      }
      if($this->verdiept==true && $this->gebruikHPI==true && !isset($this->hpiMissendePerioden[$periode['stop']]))
        continue;
      foreach ($periode as $rapDatum)
      { 
        if(substr($rapDatum,5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;
        if(!isset($this->totalen[$rapDatum]))
        {
          $this->totalen[$rapDatum]['totaalWaardeEur']=0;
          $fondswaarden = berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
          if($this->rapport->pdf->lastPOST['doorkijk']==1)
          {
            foreach($fondswaarden as $id=>$fondsWaarde)
            {
              $this->totalenWaarde[$rapDatum]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
              if (in_array($fondsWaarde['fonds'], $huisfondsen))
              {
                $aandeel = bepaalHuisfondsAandeel($fondsWaarde['fonds'], $this->rapport->portefeuille, $rapDatum);
                //echo $fondsWaarde['fonds']." | ".$this->rapport->portefeuille." | $rapDatum | $aandeel<br>\n";
                $this->huisfondsAandeelOpDatum[$rapDatum][$fondsWaarde['fonds']] = $aandeel;//$this->huisfondsen[$fonds]['Portefeuille']
                unset($fondswaarden[$id]);
              }
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
            $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
            $this->totalen[$rapDatum]['WaardeEur'][$instrument]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
          }
  

        //  listarray($periode);exit;
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
   
    foreach($fondsData['fondsen'] as $index=>$fonds)
    {
      if(in_array($fonds,$huisfondsen))
      {
        unset($fondsData['fondsen'][$index]);
      }
    }
    /*
     *     $this->gebruikHPI=$gebruikHPI;
    $this->vanOrigineel=$van;
    
    if($this->perioden=='jaar')
      $this->periodenOrigineel
     */

  foreach ($this->periodenOrigineel as $periode)
  {
    if(isset($this->hpiData[$categorie]['perfWaarden'][$periode['stop']]))
    {
      $waarden[$periode['stop']]=$this->hpiData[$categorie]['perfWaarden'][$periode['stop']];
      //echo "Overslaan ".$periode['stop']."<br>\n";
      continue;
    }
    //else
    //  echo "Brekenen ".$periode['stop']."<br>\n";
    
    $grootboekKosten=array();
    $grootboekOpbrengsten=array();
    $FondsDirecteKostenOpbrengsten=array();
    $RekeningDirecteKostenOpbrengsten=array();
    $datumBegin=$periode['start'];
    $datumEind=$periode['stop'];

    $portefeuilleStartJul=db2jul($this->rapport->pdf->PortefeuilleStartdatum);
    if($portefeuilleStartJul > db2jul($datumBegin) && $portefeuilleStartJul < db2jul($datumEind))
    {
      $datumBegin=substr($this->rapport->pdf->PortefeuilleStartdatum,0,10);
    }
    
    if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
      $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
    else
      $weegDatum=$datumBegin;
  


    
	  $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	  $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
  
    //echo "$datumBegin $datumEind  $totaalBeginwaarde  $totaalEindwaarde<br>\n";
  /*
    if($totaalBeginwaarde==0)
    {
      $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 1 DAY as Boekdatum
        FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	                WHERE (Rekeningmutaties.Fonds IN('" . implode('\',\'', $fondsData['fondsen']) . "')  OR Rekeningmutaties.rekening IN('" . implode('\',\'', $fondsData['rekeningen']) . "')  ) AND
	                Rekeningen.Portefeuille ='".$this->rapport->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '" . $datumBegin . "' AND Rekeningmutaties.Boekdatum <= '" . $datumEind . "' ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.id LIMIT 1 ";
      $DB->SQL($query);
      $DB->Query();
      $start = $DB->NextRecord();
  
      if ($start['Boekdatum'] != '')
      {
        $weegDatum = $start['Boekdatum'];
      }
    }
    */
     $query="SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1";
     $DB->SQL($query);
     $DB->Query();
     $grootboekrekeningen=array();
     while($grootboekrekening=$DB->nextRecord())
       $grootboekrekeningen[]=$grootboekrekening['Grootboekrekening'];
      
   	  $query = "SELECT ".
	    "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	    "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
	    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal
      ".
	    "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	    "WHERE ".
      "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningmutaties.transactieType <> 'B' AND ".
	    "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	    "Rekeningmutaties.Verwerkt = '1' AND ".
	    "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	    "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	    "Rekeningmutaties.Grootboekrekening IN ('".implode("','",$grootboekrekeningen)."')";
      $DB->SQL($query); //echo "$query <br>\n";
      $DB->Query();
      $storting = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;
    //echo $this->rapport->portefeuille."| $categorie $totaalGemiddelde = $totaalBeginwaarde - ".$storting['gewogen']."<br>\n";
    $ongerealiseerd=0;
    $gerealiseerd=0;
    $rente=0;
    if($categorie=='totaal')
    {
      $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	    $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];  
      $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
      $performanceBruto=0;
      //echo "$categorie | $datumBegin | $datumEind | $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - ".$storting['totaal'].") / ".$this->totaalGemiddelde.");<br>\n"; ob_flush();
      $tmp=$this->BerekenResultaatTotaal($datumBegin,$datumEind,$this->rapport->portefeuille);
      $stortingen 			 	= $tmp['stortingen'];//getStortingen($this->rapport->portefeuille,$datumBegin,$datumEind);
	  	$onttrekkingen 		 	= $tmp['onttrekkingen'];//getOnttrekkingen($this->rapport->portefeuille,$datumBegin,$datumEind);
      $ongerealiseerd= $tmp['ongerealiseerd'];
      $gerealiseerd= $tmp['gerealiseerd'];
      $rente= $tmp['rente'];
      $FondsDirecteKostenOpbrengsten['kostenTotaal']=$tmp['kosten'];
      $RekeningDirecteKostenOpbrengsten['kostenTotaal']=0;
      $FondsDirecteKostenOpbrengsten['opbrengstTotaal']=$tmp['opbrengsten'];
      $RekeningDirecteKostenOpbrengsten['opbrengstTotaal']=0;
      $AttributieStortingenOntrekkingen['storting']=$stortingen;
      $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
      $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
      $gemiddelde = $totaalGemiddelde;
//listarray($AttributieStortingenOntrekkingen);
      if($this->verdiept==false && $this->rapport->pdf->lastPOST['doorkijk']==1)
      {
        
        $query = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
        "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Fonds <> '' AND Rekeningmutaties.transactieType <> 'B' AND Rekeningmutaties.Fonds IN ('".implode("','",array_keys($this->huisfondsen))."') AND Rekeningmutaties.Grootboekrekening='FONDS'";
      $DB->SQL($query);
      $DB->Query();
      $storting = $DB->NextRecord();
        
      //  listarray($storting);exit;
     //   echo $this->rapport->portefeuille . " Totaal:";
       // listarray($storting);
       $AttributieStortingenOntrekkingen['fondsMutaties']=$storting['totaal'];
       
      //  listarray($AttributieStortingenOntrekkingen);
      }
    }
    else
    {
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie];
	    $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie];//$eind['actuelePortefeuilleWaardeEuro'];
      //echo "$categorie $datumEind $eindwaarde <br>\n";

	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND Rekeningmutaties.transactieType <> 'B' AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	     //$DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     //$DB->Query();
	     //$AttributieStortingenOntrekkingenRekening = $DB->NextRecord();listarray($AttributieStortingenOntrekkingenRekening);

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT 
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.transactieType <> 'B' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query(); 
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
    
      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.transactieType <> 'B' AND
                $rekeningFondsenWhere ";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten);
       $DB->Query();  
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();

	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND Rekeningmutaties.transactieType <> 'B' AND ".
	              "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere ";//
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     //$DB->SQL($queryAttributieStortingenOntrekkingen." AND Rekeningmutaties.Grootboekrekening='FONDS' "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";

	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
       
       $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
       $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
     
       
 	 //   $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) 
              * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, 
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles) 
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningmutaties.transactieType <> 'B' AND
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Rekeningmutaties.Fonds <> '')";
  
      if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
      {
        $DB->SQL($query);// logScherm($query);
        $DB->Query();
        $data = $DB->nextRecord();//echo "--- $categorie <br> ".$this->rapport->portefeuille." $rekeningRekeningenWhere <br>";listarray($AttributieStortingenOntrekkingen);listarray($data);echo "---<br>\n";
        $AttributieStortingenOntrekkingen['gewogen'] -= $data['gewogen'];
        $AttributieStortingenOntrekkingen['totaal'] += $data['totaal'];
        $AttributieStortingenOntrekkingen['storting'] += $data['storting'];
        $AttributieStortingenOntrekkingen['onttrekking'] += $data['onttrekking'];
      }
    
       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
         $DB->SQL($query);
       else
         $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
	     $DB->Query(); 
	     $data = $DB->nextRecord();

	     $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];


       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
       {
         $RekeningDirecteKostenOpbrengsten['kostenTotaal']=0;
       }
       $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
       $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] +
       $RekeningDirecteKostenOpbrengsten['kostenTotaal']+$AttributieStortingenOntrekkingen['fondsMutaties']) / $gemiddelde);
       $performanceBruto = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal']- $RekeningDirecteKostenOpbrengsten['kostenTotaal']+$AttributieStortingenOntrekkingen['fondsMutaties']) / $gemiddelde);
     }

      //$mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
      if($this->benchmarkPortefeuille<>'')
      {
        $gebruiktePortefeuille=$this->benchmarkPortefeuille;
      }
      else
      {
        $gebruiktePortefeuille='';
      }
      $indexData=$this->indexPerformance($categorie,$datumBegin,$datumEind,$gebruiktePortefeuille);
      /*
    if($categorie=='totaal')
    {
      listarray($indexData['verdeling']);
      echo "$categorie | $datumEind | " . $this->rapport->portefeuille . " |" . ($indexData['perf'] * 100) . "<br>\n";
    }
      */
      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']+ $RekeningDirecteKostenOpbrengsten['kostenTotaal']+$AttributieStortingenOntrekkingen['fondsMutaties'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
  //echo $this->verdiept."|".$this->rapport->portefeuille."| $categorie $datumEind  $bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";

      $waarden[$datumEind]=array(
   'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance,
  'procentBruto'=>$performanceBruto,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
  'resultaat'=>$resultaat,
  'ongerealiseerd'=>$ongerealiseerd,
  'gerealiseerd'=>$gerealiseerd,
  'rente'=>$rente,
  'fondsMutaties'=>$AttributieStortingenOntrekkingen['fondsMutaties'],
  'gemWaarde'=>$gemiddelde,
  'indexPerf'=>$indexData['perf'],
  'indexVerdeling'=>$indexData['verdeling'],
  'weging'=>$weging,
  'aandeelOpTotaal'=>$aandeelOpTotaal,
  'bijdrage'=>$bijdrage);

   if(isset($this->planTotalen[$datumEind]))
   {
     $waarden[$datumEind]['planTotalen']=$this->planTotalen[$datumEind];
   }
//listarray($waarden);
//echo $datumEind." ".$this->huidigeCategorie;
  
  if($this->verdiept==false)
  {
  
 //listarray($waarden);  echo $this->huidigeCategorie; //listarray($this->huisfondsAandeelOpDatum[$datumEind]);
  $somItems=array('beginwaarde','eindwaarde','onttrekking','opbrengst','resultaat','gemWaarde','storting','kosten','rente');//'stort',
  $huisfondsgebruik=false;
  foreach($this->huisfondsWaarde as $huisfonds=>$huisfondsWaarden)
  {

    if($this->huisfondsAandeelOpDatum[$datumEind][$huisfonds] <> 0)
    {
      $aandeel=$this->huisfondsAandeelOpDatum[$datumEind][$huisfonds];

      foreach($somItems as $veld)
      {
        if(!isset($this->huisfondsAandeelOpDatum[$datumBegin][$huisfonds]))
        {
          if($veld=='eindwaarde')
            $waarden[$datumEind][$veld] += ($huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind][$veld] * $aandeel);
          
        }
        else
        {
          if ($veld == 'beginwaarde')// && isset($this->huisfondsAandeelOpDatum[$datumBegin][$huisfonds]))
          {
            $waarden[$datumEind][$veld] += ($huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind][$veld] * $this->huisfondsAandeelOpDatum[$datumBegin][$huisfonds]);
       //   echo  $this->huidigeCategorie." | $datumBegin $datumEind |  ".$waarden[$datumEind][$veld]." += (".$huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind][$veld]." * ".$this->huisfondsAandeelOpDatum[$datumBegin][$huisfonds].")<br>\n";
          }
          else
          {
          $waarden[$datumEind][$veld] += ($huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind][$veld] * $aandeel);
          }
        }
        //if($veld=='eindwaarde')
         // echo "vd:".$this->verdiept." ".$this->huidigeCategorie. " | ".$huisfonds." | ".$aandeel." | $veld | $datumEind | ".$huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind][$veld]*$aandeel. "<br>\n";
      }
    //  $waarden[$datumEind]['stort'] = ($waarden[$datumEind]['eindwaarde'] - $waarden[$datumEind]['beginwaarde'] )-$waarden[$datumEind]['resultaat'] ;
      $huisfondsgebruik=true;
//echo $this->rapport->portefeuille." $aandeel vd:".$this->verdiept." $datumEind ".$waarden[$datumEind]['beginwaarde']."->".$waarden[$datumEind]['eindwaarde']." hfwaarde:".$huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind]."<br>\n";
//listarray($huisfondsWaarden[$this->huidigeCategorie]['perfWaarden'][$datumEind]);
    }
  
//    listarray($waarden);  echo $this->huidigeCategorie."<br>------<br>";
  }
  if($huisfondsgebruik==true)
  {
    $waarden[$datumEind]['huisfondsGebruik']=true;
    unset($waarden[$datumEind]['procentBruto']);
  
    if($this->totaalGemiddelde==0)
      $waarden[$datumEind]['weging']=$waarden[$datumEind]['gemWaarde']/$this->totalenWaarde[$datumBegin];
    else
      $waarden[$datumEind]['weging']=$waarden[$datumEind]['gemWaarde']/$this->totaalGemiddelde;
    $waarden[$datumEind]['aandeelOpTotaal']=$waarden[$datumEind]['eindwaarde']/$this->totalenWaarde[$datumEind];

    $waarden[$datumEind]['procent']=$waarden[$datumEind]['resultaat']/$waarden[$datumEind]['gemWaarde'];
    $waarden[$datumEind]['bijdrage']=$waarden[$datumEind]['procent']*$waarden[$datumEind]['weging'];
    
    if($this->verdiept==false)// && $categorie=='totaal'
    {
     // echo "$datumEind $categorie";
     // listarray($waarden[$datumEind]);
     // listarray($this->totaalGemiddelde);
    }
  }
  }
//
  }
  
  
  
   // if($this->verdiept==false)
   //   listarray($waarden);
  //  echo $this->rapport->portefeuille." vd:".$this->verdiept."| $rapDatum,$startJaar ".$this->totalen[$rapDatum]['totaalWaardeEur']."<br>\n";
  
  
  
    $stapelItems=array('procent','bijdrage','indexPerf');//
$avgItems=array('weging','gemWaarde');
$somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','stort','fondsMutaties','rente');
$sum=array();
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

$eersteDatum=true;
foreach ($waarden as $datum=>$waarde)
{
  if($eersteDatum==true)
  {
    $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];
    $eersteDatum=false;
  }
  $perfData['totaal']['eindwaarde']=$waarde['eindwaarde'];
  $perfData['totaal']['aandeelOpTotaal']=$waarde['aandeelOpTotaal'];

  foreach ($somItems as $item)    
    $perfData['totaal'][$item] +=$waarde[$item];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  foreach ($avgItems as $item)  
    $sum[$item] += $waarde[$item];
}
foreach ($avgItems as $item)
  $perfData['totaal'][$item]=$sum[$item]/count($waarden);

foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
$perfData['totaal']['categorie']=$fondsData['categorie'];

  if($this->rapport->pdf->lastPOST['doorkijk']==1)
  {
    $perfData['totaal']['stort'] = ($perfData['totaal']['eindwaarde'] - $perfData['totaal']['beginwaarde']) - $perfData['totaal']['resultaat'];
    $perfData['totaal']['fondsMutaties'] = $perfData['totaal']['stort'] - ($perfData['totaal']['storting']-$perfData['totaal']['onttrekking']);
  }

//stapelen verwijderen
$stapelenVerwijderen=false;
if($stapelenVerwijderen==true)
{
 $perfData['totaal']['procent']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'];
 $perfData['totaal']['bijdrage']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'] * $perfData['totaal']['weging'] *100;
 
 if($categorie=='totaal')
 {
  $perfData['totaal']['procent']=$perfData['totaal']['procent']*100;
  $perfData['totaal']['bijdrage']=$perfData['totaal']['bijdrage']*100;
 }
}
if($stapeling == true)
{
  //if($categorie=='totaal'){listarray($this->hpiData); echo $this->vanOrigineel." ->$tot <br>\n";}
  if($this->gebruikHPI==true && $categorie=='totaal' && isset($this->hpiData['totaal']['perfWaarden'][$tot]) && (is_array($this->hpiData['totaal']['grootboekOpbrengsten']) || is_array($this->hpiData['totaal']['grootboekKosten'])) )
  {
    if(is_array($this->hpiData['totaal']['grootboekOpbrengsten']))
      $perfData['totaal']['grootboekOpbrengsten']=$this->hpiData['totaal']['grootboekOpbrengsten'];
    if(is_array($this->hpiData['totaal']['grootboekKosten']))
      $perfData['totaal']['grootboekKosten']=$this->hpiData['totaal']['grootboekKosten'];
  }
  else
  {
  
    if ($this->benchmarkPortefeuille <> '')
    {
      $fondsRegels = berekenPortefeuilleWaarde($this->rapport->portefeuille, $tot, (substr($this->vanOrigineel, 5, 5) == '01-01')?true:false, $this->rapport->pdf->rapportageValuta, $tot);
      vulTijdelijkeTabel($fondsRegels, $this->rapport->portefeuille, $tot);
      $fondsRegels = berekenPortefeuilleWaarde($this->rapport->portefeuille, $this->vanOrigineel, (substr($this->vanOrigineel, 5, 5) == '01-01')?true:false, $this->rapport->pdf->rapportageValuta, $this->vanOrigineel);
      vulTijdelijkeTabel($fondsRegels, $this->rapport->portefeuille, $this->vanOrigineel);
    }
    $mutaties = $this->genereerMutatieLijst($van, $tot, $fondsData['fondsen']);
    $perfData['totaal']['gerealiseerdFondsResultaat'] = $mutaties['totalen']['fonds'];
    $perfData['totaal']['gerealiseerdValutaResultaat'] = $mutaties['totalen']['valuta'];
  
    //historischeWaarde
    $fondsenWhere = "AND Fonds IN('" . implode('\',\'', $fondsData['fondsen']) . "')";
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro - beginPortefeuilleWaardeEuro) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta) as fondsresultaatEUR" .
      " FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='$tot' $fondsenWhere AND" .
      " portefeuille = '" . $this->rapport->portefeuille . "' AND "
      . " type = 'fondsen' " . $__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query); //echo $query;exit;
    $DB->Query();
    $totaal = $DB->nextRecord();
    $ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'];
    $ongerealiseerdValutaResultaat = $totaal['resultaatEUR'] - $totaal['fondsresultaatEUR'];
    $perfData['totaal']['ongerealiseerdFondsResultaat'] = $ongerealiseerdFondsResultaat;
    $perfData['totaal']['ongerealiseerdValutaResultaat'] = $ongerealiseerdValutaResultaat;
  
    if ($categorie == 'totaal')
    {
      $filter = "AND fonds<>''";
    }
    else
    {
      $filter = $fondsenWhere;
    }
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$tot' AND portefeuille = '" . $this->rapport->portefeuille . "' AND  type = 'rente' $filter " . $__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $totaalA = $DB->nextRecord();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$this->vanOrigineel' AND portefeuille = '" . $this->rapport->portefeuille . "' AND  type = 'rente' $filter " . $__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $totaalB = $DB->nextRecord();
    $perfData['totaal']['opgelopenrente'] = ($totaalA['totaal'] - $totaalB['totaal']);
  
    if ($categorie == 'totaal')
    {
      $filter = '';
    }
    else
    {
      $filter = $fondsenWhere;
    }
    
    if($this->verdiept==false)
    {
      $vanaf=$this->vanOrigineel;
    }
    else
    {
      $vanaf=date('Y-m-d',db2jul($this->vanOrigineel));//+86400);
    }
  
    $query = "SELECT  Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten, Grootboekrekeningen.Grootboekrekening," .
      "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, " .
      "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet " .
      "FROM Rekeningmutaties
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
         JOIN Grootboekrekeningen ON Grootboekrekeningen.Grootboekrekening=Rekeningmutaties.Grootboekrekening " .
      "WHERE Rekeningen.Portefeuille = '" . $this->rapport->portefeuille . "'  AND " .
      "Rekeningmutaties.Verwerkt = '1' AND " .
      "Rekeningmutaties.Boekdatum > '" . $vanaf. "' AND " .
      "Rekeningmutaties.Boekdatum <= '" . $tot . "' $filter AND
        (Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Kosten = '1') AND Rekeningmutaties.transactieType <> 'B' GROUP BY  Grootboekrekeningen.Grootboekrekening
        ORDER BY Grootboekrekeningen.Afdrukvolgorde";
  
    $DB2 = new DB();
    $DB2->SQL($query);
    $DB2->Query();
  
    while ($grootboek = $DB2->nextRecord())
    {
      if ($grootboek['Opbrengst'] == 1)
      {
        $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] = ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
      }
      if ($grootboek['Kosten'] == 1)
      {
        $kostenPerGrootboek[$grootboek['Grootboekrekening']] = ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        $totaalKosten += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
      }
    }
  
    if ($categorie <> 'totaal')
    {
      if ($rekeningRekeningenWhere <> '')
      {
        $filter = 'AND ' . $rekeningRekeningenWhere;
      }
      $query = "SELECT Rekeningmutaties.Grootboekrekening," .
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalcredit, " .
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaaldebet " .
        "FROM Rekeningmutaties
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille " .
        "WHERE Rekeningen.Portefeuille = '" . $this->rapport->portefeuille . "'  AND " .
        "Rekeningmutaties.Verwerkt = '1' AND " .
        "Rekeningmutaties.Boekdatum > '" . $this->vanOrigineel . "' AND " .
        "Rekeningmutaties.Boekdatum <= '" . $tot . "'  $filter AND
        (Rekeningmutaties.Grootboekrekening='RENTE') AND Rekeningmutaties.transactieType <> 'B' GROUP BY  Rekeningmutaties.Grootboekrekening
        ";
      $DB2->SQL($query);
      $DB2->Query();
      while ($grootboek = $DB2->nextRecord())
      {
        $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
      }
    }
    else
    {
      if ($this->verdiept == false)
      {
      
        foreach ($this->huisfondsWaarde as $huisfonds => $huisfondsWaarden)
        {
          $aandeel = $this->huisfondsAandeelOpDatum[$tot][$huisfonds];
          foreach ($huisfondsWaarden['totaal']['grootboekOpbrengsten'] as $grootboek => $waarde)
          {
            $waarde = $waarde * $aandeel;
            $opbrengstenPerGrootboek[$grootboek] += $waarde;
            $totaalOpbrengst += $waarde;
          }
          foreach ($huisfondsWaarden['totaal']['grootboekKosten'] as $grootboek => $waarde)
          {
            $waarde = $waarde * $aandeel;
            $kostenPerGrootboek[$grootboek] += $waarde;
            $totaalKosten += $waarde;
          }
        
        }
      }
    }
  
    $perfData['totaal']['opbrengst'] = $totaalOpbrengst;
    $perfData['totaal']['grootboekOpbrengsten'] = $opbrengstenPerGrootboek;
    $perfData['totaal']['kosten'] = $totaalKosten;
    $perfData['totaal']['grootboekKosten'] = $kostenPerGrootboek;
  }

//listarray($perfData);
  $perfData['totaal']['perfWaarden']=$waarden;
  
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
  'ongerealiseerd'=> $FondsDirecteKostenOpbrengsten['RENMETotaal'] , //$ongerealiseerdResultaat
  'gerealiseerd'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],//$mutatieData['totalen']['gerealiseerdResultaat'] +
  'weging'=>$weging,
  'bijdrage'=>$bijdrage*100);
	}
  

  function getJaren($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);

    $i=0;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,1,0,$beginjaar+$i);
      $counterEnd   = mktime (0,0,0,1,0,$beginjaar+1+$i);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      else
        $datum[$i]['start'] =date('Y-m-d',$counterStart);

      $datum[$i]['stop']=date('Y-m-d',$counterEnd);

      if(db2jul($datum[$i]['stop']) < db2jul($datum[$i]['start']))
        unset($datum[$i]);

      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
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
  
  function getWeken($julBegin, $julEind,$beginVrijdag=true)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);
    $i=0;
    $iIndex=0;
    
    if($beginVrijdag<> false)
    {
      $julBeginOrg = $julBegin;
      $extraDagen = 0;
      $dagVanWeek = date('w', $julBegin);
      if ($dagVanWeek < 5)
      {
        $extraDagen = 5 - $dagVanWeek;
      }
      elseif ($dagVanWeek > 5)
      {
        $extraDagen = 12 - $dagVanWeek;
      }
      $begindag += $extraDagen;
      $julBegin = mktime(0, 0, 0, $beginmaand, $begindag, $beginjaar);
      
      
      if($julBegin<>$julBeginOrg && ($beginVrijdag===true || $beginVrijdag===1))
      {
        $datum[$iIndex]['start'] = date('Y-m-d', $julBeginOrg);
        $datum[$iIndex]['stop'] = date('Y-m-d', $julBegin);
        $iIndex++;
      }
    }
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
    $counterStart=0;
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      
      if($i == 0)
      {
        $datum[$iIndex]['start'] = date('Y-m-d',$julBegin);
      }
      else
      {
        $datum[$iIndex]['start'] =date('Y-m-d',$counterStart);
        if(substr($datum[$iIndex]['start'],5,5)=='12-31')
          $datum[$iIndex]['start']=(date('Y',$counterStart)+1)."-01-01";
      }
      
      $datum[$iIndex]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$iIndex]['start'] ==  $datum[$iIndex]['stop'] || db2jul($datum[$iIndex]['start']) > db2jul($datum[$iIndex]['stop']) || ($beginVrijdag!==true && $beginVrijdag==2 && date('w',$counterEnd)<>5 ))
        unset($datum[$iIndex]);
      $i=$i+7;
      $iIndex++;
    }
    
    return $datum;
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

  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='1';


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
    
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
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
      $mutaties['Rapportagekoers']=1;

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
    
      $fondsResultaat=0;
      $valutaResultaat=0;
      if($verkoop_koers <> '' )
      { //listarray($historie);
        $historischekostprijsValuta = $mutaties['Aantal']*$historie['historischeWaarde']* $mutaties['Fondseenheid'];//$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
        $historischekostprijsValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];  
        $fondsResultaat = ($t_verkoop_waardeinValuta-$historischekostprijsValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
	      $valutaResultaat=	($resultaatlopende)-$fondsResultaat;  //$resultaatvoorgaande
      }
      
				$data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
        $data['totalen']['fonds']+=$fondsResultaat;
        $data['totalen']['valuta']+=$valutaResultaat;
		}
		return $data;
	}
  
  function BerekenResultaatTotaal($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
  {
    if(substr($beginDatum,5,5)=='12-31')
      $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';
    
    if ($valuta != "EUR" )
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $totaalWaarde =array();
    $db = new DB();
    
    $query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
    $db->SQL($query);
    $startDatum=$db->lookupRecord();

    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
      if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
        return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));
      
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    else
      $wegingsDatum=$beginDatum;
    
    $startjaar=substr($beginDatum,0,4);
    if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
      $beginjaar = true;
    else
      $beginjaar = false;
    
    $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
    //echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";
    
    $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);
    $DB=new DB();
    
    if($valuta <> 'EUR')
      $valutaKoers=getValutaKoers($valuta,$beginDatum);
    else
      $valutaKoers=1;
    foreach ($fondswaarden['beginmaand'] as $regel)
    {
      $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      $query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$regel['fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
      if($this->pdf->lastPOST['doorkijk']==1 && $DB->QRecords($query)!=1)
      {
      
      }
      else
      {
        if($regel['type']=='rente' && $regel['fonds'] != '')
        {
          $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
        }
      }
    }
    
    $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;
    
    // listarray($categorieVerdeling);
    if($valuta <> 'EUR')
      $valutaKoers=getValutaKoers($valuta,$eindDatum);
    else
      $valutaKoers=1;
    
    $huisfondsOpbrengst=0;
    $huisfondsKosten=0;
    $huisfondsStortingen=0;
    $huisfondsOnttrekkingen=0;
    $huisfondsStortingenEnOnttrekkingenGewogen=0;
    $huisfondsWaardeOpDatum=array();
    
    foreach ($fondswaarden['eindmaand'] as $regel)
    {
      $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
      
      if($regel['type']=='fondsen')
      {
        $query="SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='".$regel['fonds']."' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
        if($this->pdf->lastPOST['doorkijk']==1 && $DB->QRecords($query)==1)
        {
          $huisfonds=$DB->nextRecord();
          $waarden=bepaalHuisfondsResultaat($regel['fonds'],$portefeuille,$huisfonds['Portefeuille'],$beginDatum,$eindDatum);
          $totaalWaarde['eindResultaat']+=$waarden['aandeelKoersresultaat'];
          $huisfondsOpbrengst+=$waarden['aandeelDirecteopbrengst'];
          $huisfondsKosten+=$waarden['aandeeldirectekosten'];
          $huisfondsStortingen+=$waarden['aandeelStortingen'];
          $huisfondsOnttrekkingen+=$waarden['aandeelOnttrekkingen'];
          $huisfondsStortingenEnOnttrekkingenGewogen+=$waarden['aandeelStortingenEnOnttrekkingenGewogen'];
          
          $huisfondsWaardeOpDatum[$regel['fonds']][$waarden['datum']]=$waarden['huisfondsWaarde'];
        }
        else
        {
          $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
          $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        }
        
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    
    
    $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
    $DB=new DB();
    
    $query = "SELECT ".
      "SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
      "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
      "FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1']+$huisfondsStortingenEnOnttrekkingenGewogen;
    $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta)+$huisfondsStortingen;
    $onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta)+$huisfondsOnttrekkingen;
    
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - ($stortingen-$onttrekkingen)) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    
    $query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND Rekeningmutaties.transactieType <> 'B' AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND Rekeningmutaties.transactieType <> 'B' AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();
    $opbrengsten['totaalOpbrengsten']+=$huisfondsOpbrengst;
    $kosten['totaalkosten']+=$huisfondsKosten;
    
    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];

    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;
  
    $huisfondsMutatie=array();
    /*
 $query="SELECT
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Rekeningmutaties.Aantal,
Rekeningmutaties.Transactietype,
Fondsen.Portefeuille
FROM
Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille = '$portefeuille'
AND Rekeningmutaties.Verwerkt = '1'
AND Rekeningmutaties.Boekdatum > '$beginDatum'
AND Rekeningmutaties.Boekdatum <= '$eindDatum'
AND Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype IN ('D','L')
AND Fondsen.Huisfonds=1";
 $DB->SQL($query);
 $DB->Query();
 $huisfondsMutatie=array();
while($huisfondsStort = $DB->NextRecord())
{
  if(isset($huisfondsWaardeOpDatum[$huisfondsStort['Fonds']][$huisfondsStort['Boekdatum']]))
  {
    $waarde = $huisfondsWaardeOpDatum[$huisfondsStort['Fonds']][$huisfondsStort['Boekdatum']];
  }
  else
  {
    $huisfondsRgels =  berekenPortefeuilleWaarde($huisfondsStort['Portefeuille'],$huisfondsStort['Boekdatum'],(substr($huisfondsStort['Boekdatum'],5,5)=='01-01'?true:false),$valuta,$huisfondsStort['Boekdatum']);
    $waarde=0;
    foreach($huisfondsRgels as $regel)
      $waarde+=$regel['actuelePortefeuilleWaardeEuro'];
  }

  $koers=bepaalHuisfondsKoers($huisfondsStort['Fonds'],$huisfondsStort['Portefeuille'],$huisfondsStort['Boekdatum']);
  //listarray($huisfondsStort);

  if($huisfondsStort['Transactietype']=='D')
  {
    if($huisfondsStort['Aantal'] < 0 )
      $huisfondsMutatie['onttrekking'] += $huisfondsStort['Aantal']  * $koers['Koers'];
    else
      $huisfondsMutatie['storting'] +=$huisfondsStort['Aantal'] * $koers['Koers'];
  }
  else
  {
    if($huisfondsStort['Aantal'] < 0 )
      $huisfondsMutatie['storting'] +=$huisfondsStort['Aantal'] * $koers['Koers'];
    else
      $huisfondsMutatie['onttrekking'] += $huisfondsStort['Aantal']  * $koers['Koers'];
  }

  //echo $huisfondsStort['Fonds']." $aandeel*$waarde; <br>\n";
 // listarray($huisfondsMutatie);
}
  */
   $huisfondsMutatie['totaal']=$huisfondsMutatie['storting']-$huisfondsMutatie['onttrekking'];
    
    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen+$huisfondsMutatie['storting'],2);
    $data['onttrekkingen']=round($onttrekkingen+$huisfondsMutatie['onttrekking'],2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    //$data['extra']['cat']=$categorieVerdeling;
    return $data;
    
  }
	
	function HPIBijwerken($attData,$pdata,$config)
  {
    global $USR;
    $DB2=new DB();
  
    foreach($attData as $categorie=>$data)
    {
      if(!isset($data['periode']))
        $data['periode'] = $config['start'] . "->" . $config['stop'];
      $qBody = " Portefeuille = '" . $pdata['Portefeuille'] . "' ,
			                Categorie = '$categorie',
			                PortefeuilleWaarde = '" . round($data['eindwaarde'], 2) . "' ,
			                PortefeuilleBeginWaarde = '" . round($data['beginwaarde'], 2) . "' ,
 		                  Stortingen = '" . round($data['storting'], 2) . "' ,
			                Onttrekkingen = '" . round($data['onttrekking'], 2) . "' ,
			                Opbrengsten = '" . round($data['opbrengst'], 2) . "' ,
			                Kosten = '" . round($data['kosten'], 2) . "' ,
			                Datum = '" . $config['stop'] . "',
			                IndexWaarde = '". round($data['procent'],4)." ' ,
                      periode='".$config['type']."',
			                gerealiseerd = '" . round($data['gerealiseerdFondsResultaat']+$data['gerealiseerdValutaResultaat'], 2) . "',
			                ongerealiseerd = '" . round($data['ongerealiseerdFondsResultaat']+$data['ongerealiseerdValutaResultaat'], 2) . "',
			                rente = '" . round($data['opgelopenrente'], 2) . "',
			                extra = '" . mysql_real_escape_string(serialize($data)) . "',
			                gemiddelde = '" . round($data['gemWaarde'], 2) . "',
			                ";
      $query = "SELECT id FROM HistorischePortefeuilleIndex WHERE periode='".$config['type']."' AND Portefeuille = '" . $pdata['Portefeuille'] . "' AND Datum = '".substr($config['stop'],0,10)."' AND Categorie = '$categorie' ";
      $DB2->SQL($query);
      $DB2->Query();
      $records = $DB2->records();
      if ($records > 0)
      {
        $id = $DB2->lookupRecord();
        $id = $id['id'];
    
    
        if ($this->indexSuperUser == false && date("Y", db2jul($config['stop'])) != date('Y'))
        {
          $query = "select 1";
          echo "Geen rechten om records in het verleden te vernieuwen. " . $pdata['Portefeuille'] . " " . $config['stop'] . "<br>\n";
        }
        else
        {
          $query = "UPDATE HistorischePortefeuilleIndex SET $qBody change_date = NOW(), change_user = '$USR' WHERE id = $id ";
        }
      }
      else
      {
        $query = "INSERT INTO HistorischePortefeuilleIndex SET $qBody change_date = NOW(),change_user = '$USR', add_date = NOW(), add_user = '$USR' ";
      }
      if ((db2jul($pdata['Startdatum']) < db2jul($config['stop'])))
      {
        $DB2->SQL($query);
        //logscherm($query);
        $DB2->Query();
      }
    }
    
  }
  
}
?>