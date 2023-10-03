<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/24 13:02:41 $
 		File Versie					: $Revision: 1.17 $

 		$Log: ATTberekening_L77.php,v $
 		Revision 1.17  2020/06/24 13:02:41  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2020/01/25 16:36:35  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2020/01/18 16:37:36  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2019/12/18 15:53:15  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/12/11 11:17:44  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/11/10 12:02:33  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/11/10 09:09:31  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/11/07 17:08:06  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/11/05 15:58:54  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/11/01 07:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/10/24 16:00:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/10/21 09:42:37  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/10/13 17:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/10/10 15:50:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/09/15 17:45:24  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/07/25 15:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2018/06/13 15:53:55  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2016/06/08 15:40:53  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2016/02/06 16:42:56  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/12/23 16:25:07  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2015/12/19 09:03:50  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2015/11/22 14:31:46  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/11/18 17:08:02  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/04/26 12:26:58  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/08/04 14:02:09  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/04/23 16:18:44  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2013/08/10 15:48:01  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/07/13 15:19:44  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/06/09 18:01:53  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/06/05 15:56:07  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/04/17 16:00:15  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/03/23 16:19:36  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/03/17 10:58:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/03/06 16:59:28  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/02/20 15:12:14  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/02/10 10:06:07  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/02/06 19:06:11  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/02/03 09:04:21  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/01/27 14:14:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/20 13:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/01/16 16:54:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/01/13 13:35:39  rvv
 		*** empty log message ***
 		

 */


class ATTberekening_L77
{
  
  function ATTberekening_L77($rapportData,$periode='maanden')
  {
    $this->rapport=&$rapportData;
    
    $this->rapportageDatumVanaf=db2jul($this->rapport->rapportageDatumVanaf);
    $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
    $this->rapport_jaar=date('Y',$this->rapportageDatumVanaf);
    $this->indexPerformance=false;
    $this->perioden=$periode;
    
  }
  
  function bereken($van,$tot,$verdeling='Hoofdcategorie')
  {
    global $__appvar;
    $DB=new DB();
    $this->categorien=array('totaal'=>'Totaal');
    
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
    elseif($verdeling=='Bewaarder')
    {
      $categorieFilter='geen';
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
    /*
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
    echo $query;exit;
        $DB->SQL($query);
        $DB->Query();
        while($data=$DB->nextRecord())
        {
          $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
          $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
          $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
          $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
        } 
    */
    $query="SELECT
Rekeningen.Depotbank as Bewaarder,
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
BeleggingssectorPerFonds.Beleggingssector,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";

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
      
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
      $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
      $perBewaarder[$data['Bewaarder']]['omschrijving']=$data['Bewaarder'];
      $perBewaarder[$data['Bewaarder']]['fondsen'][]=$data['Fonds'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
      $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $alleData['fondsen'][]=$data['Fonds'];
      
    }
    
    $query="SELECT
Rekeningen.Depotbank as Bewaarder,
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
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening AND Rekeningen.Memoriaal=0
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND 
Rekeningmutaties.Boekdatum >= '".$this->rapport->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";//
    
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
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
      $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perBewaarder[$data['Bewaarder']]['omschrijving']=$data['Bewaarder'];
      $perBewaarder[$data['Bewaarder']]['rekeningen'][]=$data['rekening'];
      $perBewaarder[$data['Bewaarder']]['categorie']=$data['Bewaarder'];
      $alleData['rekeningen'][]=$data['rekening'];
    }
    
    
    foreach($perCategorie as $categorie=>$categorieData)
    {
      if(count($categorieData['fondsen'])==0 &&  count($categorieData['rekeningen'])==0)
        unset($perCategorie[$categorie]);
    }
   // listarray($perCategorie);
    //$this->totalen['gemiddeldeWaarde']=0;
    //$perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true);
    
    if($verdeling=='Hoofdcategorie')
      $categorien=$perHoofdcategorie;
    elseif($verdeling=='totaal')
      $categorien=array();
    elseif($verdeling=='sector')
      $categorien=$perSector;
    elseif($verdeling=='Bewaarder')
      $categorien=$perBewaarder;
    else
      $categorien=$perCategorie;
    
    $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
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
    //listarray($perfData);
    
    return $perfData;
  }
  
  function indexPerformance($categorie,$van,$tot)
  {
    global $__appvar;
    $DB = new DB();
    
     if(!is_array($this->indexLookup) || count($this->indexLookup) < 1)
     {
       $query = "SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='" . $this->rapport->pdf->portefeuilledata['Vermogensbeheerder'] . "'
            AND (IndexPerBeleggingscategorie.Portefeuille='" . $this->rapport->portefeuille . "' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
       $DB->SQL($query);
       $DB->Query();
       while ($index = $DB->nextRecord())
       {
         $this->indexLookup[$index['Beleggingscategorie']] = $index['Fonds'];
       }
       $this->indexLookup['totaal'] = $this->rapport->pdf->portefeuilledata['SpecifiekeIndex'];
     }
    if(!is_array($this->normData) || count($this->normData) < 1)
    {
      $this->normData['totaal']=100;
      $q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht
       FROM
       ZorgplichtPerRisicoklasse
       Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       ORDER by ZorgplichtPerBeleggingscategorie.Beleggingscategorie";
      $DB->SQL($q);
      $DB->Query();
      while($data=$DB->nextRecord())
        $this->normData[$data['Beleggingscategorie']]=$data['norm'];
    
      $q="SELECT
      ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
      ZorgplichtPerPortefeuille.Zorgplicht,
      ZorgplichtPerPortefeuille.norm
      FROM ZorgplichtPerPortefeuille
      JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      ORDER by ZorgplichtPerBeleggingscategorie.Beleggingscategorie
      ";
      $DB->SQL($q);
      $DB->Query();
      while($data=$DB->nextRecord())
        $this->normData[$data['Beleggingscategorie']]=$data['norm'];
      

    }
 
    
    $query="SELECT IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerPortefeuille.norm / 100 as Percentage
FROM
IndexPerBeleggingscategorie
 Join ZorgplichtPerBeleggingscategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
 Join ZorgplichtPerPortefeuille ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf < '$van' AND 
ZorgplichtPerPortefeuille.extra=0
order by ZorgplichtPerPortefeuille.vanaf desc limit 1
";
    $DB->SQL($query); //echo $query;
    $fondsData=$DB->lookupRecord();
  
    //listarray( $this->indexLookup);
    if($fondsData['Fonds']=='')
    {
      $fondsData['Fonds'] = $this->indexLookup[$categorie];
      $fondsData['Percentage'] = $this->normData[$categorie]/100;
    }
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($query);
    $startKoers=$DB->lookupRecord();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
  
    $perf=getFondsPerformanceGestappeld2($fondsData['Fonds'],$this->rapport->portefeuille,$van,$tot,'maanden',false,true)/100;


    $tmp= array('perf'=>$perf,'bijdrage'=>$perf*$fondsData['Percentage'],'datum'=>$tot,'percentage'=>$fondsData['Percentage'],'categorie'=>$categorie,'koersVan'=>$startKoers['Koers'],'koersEind'=>$eindKoers['Koers']);//,'waarden'=>$waarden)
   // logscherm($fondsData['Fonds']." $perf");
    return $tmp;
  }
  
  function getJaren($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $counterStart=0;
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
  
  
  
  function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$categorie='')
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
    {
      if($this->perioden=='kwartalen')
        $perioden=$this->getKwartalen(db2jul($van),db2jul($tot));
      if($this->perioden=='jaar')
        $perioden=$this->getJaren(db2jul($van),db2jul($tot));
      else
        $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    }
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

    $DB=new DB();
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {

        if(!isset($this->totalen[$rapDatum]))
        {
          $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,(substr($rapDatum, 5, 5) == '01-01')?true:false);//,'EUR',$rapDatum,2,true
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
    
    
    foreach ($perioden as $periode)
    {
      $grootboekKosten=array();
      $grootboekOpbrengsten=array();
      $FondsDirecteKostenOpbrengsten=array();
      $RekeningDirecteKostenOpbrengsten=array();
      $datumBegin=$periode['start'];
      $datumEind=$periode['stop'];
      
      if($portefeuilleStartJul > db2jul($datumBegin) && $portefeuilleStartJul < db2jul($datumEind))
      {
        $datumBegin=substr($this->rapport->pdf->PortefeuilleStartdatum,0,10);
        $weegDatum=$datumBegin;
      }
      
      if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
        $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
      else
        $weegDatum=$datumBegin;
      
      
      $portefeuilleStartJul=db2jul($this->rapport->pdf->PortefeuilleStartdatum);
      
      $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
      $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
      
      
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
        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Grootboekrekening IN ('".implode("','",$grootboekrekeningen)."')";
      $DB->SQL($query);
      $DB->Query();
      $storting = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;
//echo "$categorie $totaalGemiddelde = $totaalBeginwaarde - ".$storting['gewogen']."<br>\n";
      $totaalKosten=0;
      if($categorie=='totaal')
      {
        $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
        $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
        $resultaat = ($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal'];
        $performance = ($resultaat / $this->totaalGemiddelde);
        $stortingen 			 	= getStortingen($this->rapport->portefeuille,$datumBegin,$datumEind);
        $onttrekkingen 		 	= getOnttrekkingen($this->rapport->portefeuille,$datumBegin,$datumEind);
        $AttributieStortingenOntrekkingen['storting']=$stortingen;
        $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
        $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
        $gemiddelde = $totaalGemiddelde;

        $query = "SELECT  ".
          "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet ".
          "FROM Rekeningmutaties
      JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening ".
          "WHERE ".
          "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
          "(Grootboekrekeningen.kosten=1)";
        $DB->SQL($query);
        $DB->Query();
        $kosten = $DB->nextRecord();
        $totaalKosten=$kosten['totaaldebet']-$kosten['totaalcredit'];

        $query = "SELECT  ".
          "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet ".
          "FROM Rekeningmutaties
      JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening ".
          "WHERE ".
          "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
          "(Grootboekrekeningen.opbrengst=1)";
        $DB->SQL($query);
        $DB->Query();
        $kosten = $DB->nextRecord();
        $totaalOpbrengst=$kosten['totaalcredit']-$kosten['totaaldebet'];
      
        $brutoResultaat=$resultaat+$totaalKosten;
        $gemiddeldVermogen=$resultaat/($performance*0.01);
        $performanceBruto=$brutoResultaat/$gemiddeldVermogen*100;
        

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
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
        $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
        $DB->Query();
        $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
        
        
        $queryRekeningDirecteKostenOpbrengsten = "SELECT 
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
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
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
  
        $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
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

//begin
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
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
          "Rekeningmutaties.Grootboekrekening = 'FONDS' AND $rekeningFondsenWhere ";//
        $DB->SQL($queryAttributieStortingenOntrekkingen);
        $DB->Query();
        $AttributieStortingenOntrekkingen = $DB->NextRecord();
        
        /*
        $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
        $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        $DB->Query();
        $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
        listarray($AttributieStortingenOntrekkingenBruto);
        */
        
        $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
        
        $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen,
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles) 
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                 
	              WHERE 	 
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND                
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
        $DB->SQL($query);
        //echo "$query <br>\n";
        $DB->Query();
        $data = $DB->nextRecord();
        $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
        $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
        $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
        $AttributieStortingenOntrekkingen['gewogen'] +=$data['gewogen'];

  
        $liqCategorie=false;
        $liqKosten=0;
        if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
        {
           $queryKostenOpbrengsten = "SELECT
            SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
            SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
            JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
            WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
          $DB->SQL($queryKostenOpbrengsten);
          $DB->Query();
          $nietToegerekendeKosten = $DB->NextRecord();
          $liqKosten-= $nietToegerekendeKosten['kostenTotaal'];
          $liqCategorie=true;
        }
        $stort=$AttributieStortingenOntrekkingen['onttrekking']+$AttributieStortingenOntrekkingen['storting'];
        $opbr=$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'];
        $kost=($FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal']);

        $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];

        if($liqCategorie==true)
          $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr + $liqKosten ) / $gemiddelde;
        else
          $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde;
        $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde;

//echo "$categorie || $totaalKosten || $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr - $kost ) / $gemiddelde; <br>\n"; ob_flush();
      }

      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      //echo "$categorie $weging=$gemiddelde/".$this->totaalGemiddelde."<br>\n";
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']+ $FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$FondsDirecteKostenOpbrengsten['kostenTotaal']+$liqKosten;
      $resultaatBruto=(($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] +  $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $totaalKosten +$liqKosten);
      if($this->indexPerformance==true)
      {
        $indexData = $this->indexPerformance($categorie, $datumBegin, $datumEind);
      }
    //  listarray($indexData);
      
      $bijdrage=$resultaat/$gemiddelde*$weging;

      $waarden[$datumEind]=array(
        'periode'=>$datumBegin."->".$datumEind,
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance,
        'procentBruto'=>$performanceBruto,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal']+$totaalKosten,
        'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
        'resultaat'=>$resultaat,
        'resultaatBruto'=>$resultaatBruto,
        'gemWaarde'=>$gemiddelde,
        'weging'=>$weging,
        'indexPerf'=>$indexData['perf'],
        'indexBijdrage'=>$indexData['bijdrage'],
        'indexBijdrageWaarde'=>$indexData['percentage'],
        'aandeelOpTotaal'=>$aandeelOpTotaal,
        'bijdrage'=>$bijdrage);
    }
    

    $stapelItems=array('procent','bijdrage','procentBruto');
    $avgItems=array('weging','gemWaarde');
    $somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','stort','resultaatBruto');
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=1;
    
    $eersteDatum=true;
    $sum=array();
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
      
      $mutaties=$this->genereerMutatieLijst($van,$tot, $fondsData['fondsen']);
      $perfData['totaal']['gerealiseerdFondsResultaat']=$mutaties['totalen']['fonds'];
      $perfData['totaal']['gerealiseerdValutaResultaat']=$mutaties['totalen']['valuta'];
      
      //historischeWaarde
      $fondsenWhere="AND Fonds IN('".implode('\',\'',$fondsData['fondsen'])."')";
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro - beginPortefeuilleWaardeEuro) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta) as fondsresultaatEUR".
        " FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='$tot' $fondsenWhere AND".
        " portefeuille = '".$this->rapport->portefeuille."' AND "
        ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query); //echo $query;exit;
      $DB->Query();
      $totaal = $DB->nextRecord();
      $ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'] ;
      $ongerealiseerdValutaResultaat = $totaal['resultaatEUR']-$totaal['fondsresultaatEUR'] ;
      $perfData['totaal']['ongerealiseerdFondsResultaat']=$ongerealiseerdFondsResultaat;
      $perfData['totaal']['ongerealiseerdValutaResultaat']=$ongerealiseerdValutaResultaat;
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$tot' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalA = $DB->nextRecord();
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$van' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalB = $DB->nextRecord();
      $perfData['totaal']['opgelopenrente'] = ($totaalA['totaal'] - $totaalB['totaal']) ;
      
      if($categorie=='totaal')
        $filter='';
      elseif($categorie=='H-Liq')
        $filter=" AND ( 1 $fondsenWhere  OR Rekeningmutaties.Fonds = '' ) ";
      else
        $filter=$fondsenWhere;



      $query = "SELECT  Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten, Grootboekrekeningen.Grootboekrekening,Rekeningmutaties.Fonds,".
        "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
        "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
        "FROM Rekeningmutaties 
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
         JOIN Grootboekrekeningen ON Grootboekrekeningen.Grootboekrekening=Rekeningmutaties.Grootboekrekening ".
        "WHERE Rekeningen.Portefeuille = '".$this->rapport->portefeuille."'  AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$van."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$tot."' $filter AND 
        (Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Kosten = '1') GROUP BY  Grootboekrekeningen.Grootboekrekening
        ORDER BY Grootboekrekeningen.Afdrukvolgorde";

      $DB2 = new DB();
      $DB2->SQL($query); //echo "$query <br>\n";
      $DB2->Query();
      $totaalOpbrengst=0;
      $totaalKosten=0;
      while($grootboek = $DB2->nextRecord())
      {
        if($grootboek['Opbrengst']==1 )
        {
          $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
        if($grootboek['Kosten']==1)
        {
          $kostenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalKosten += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);

        }

        if($categorie=='H-Liq' && $grootboek['Fonds'] == '' && $grootboek['Kosten']==1 )
        {
          $perfData['totaal']['resultaat'] += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
      }
/*
      if($categorie <> 'totaal')
      {
        $filter=$rekeningRekeningenWhere;
        $query = "SELECT Rekeningmutaties.Grootboekrekening,".
          "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
          "FROM Rekeningmutaties 
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille ".
          "WHERE Rekeningen.Portefeuille = '".$this->rapport->portefeuille."'  AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$van."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$tot."' AND $filter AND
        (Rekeningmutaties.Grootboekrekening='RENTE') GROUP BY  Rekeningmutaties.Grootboekrekening
        ";
        $DB2->SQL($query);
        $DB2->Query();
        while($grootboek = $DB2->nextRecord())
        {
          $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] +=  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
          $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
      }
     */

      $perfData['totaal']['opbrengst']=$totaalOpbrengst;
      $perfData['totaal']['grootboekOpbrengsten']=$opbrengstenPerGrootboek;
      $perfData['totaal']['kosten']=$totaalKosten;
      $perfData['totaal']['grootboekKosten']=$kostenPerGrootboek;
  
/*
      if($categorie<>'totaal' && $liqCategorie==false)
      {
        $waarden[$datum]['kosten']=0;
        $perfData['totaal']['kosten']=0;
      }
*/
      
      //listarray($perfData['totaal']['resultaat']);
      
      $perfData['totaal']['perfWaarden']=$waarden;
     // listarray($perfData['totaal']);
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
    
    
    $query = "SELECT Rekeningmutaties.id, Fondsen.Omschrijving, ".
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
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id";
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
        $historie = berekenHistorischKostprijs($this->rapport->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->rapport->pdf->rapportageValuta,$rapportageDatumVanaf,$mutaties['id']);
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
      
      if($mutaties['Transactietype'] == "V"  || $mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V/S")
      {
 
        $beginwaardeLopendeJaarValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];
        $fondsResultaat = ($t_verkoop_waardeinValuta-$beginwaardeLopendeJaarValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
        $valutaResultaat=	($resultaatlopende)-$fondsResultaat;  //$resultaatvoorgaande
      }
      elseif($mutaties['Transactietype'] == "A/S" )
      {
        $beginwaardeLopendeJaarValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];
        $fondsResultaat = ($beginwaardeLopendeJaarValuta-$aankoop_waardeinValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
  //      echo "fondsResultaat =  $fondsResultaat = ($beginwaardeLopendeJaarValuta-$aankoop_waardeinValuta)*".getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum'])."<br>\n";
        $valutaResultaat=	($resultaatlopende)-$fondsResultaat;
      }
  
      if($mutaties['Aantal']==0 && $mutaties['Fondskoers']==0)
      {
        $fondsResultaat=(abs($mutaties['Credit']) - abs($mutaties['Debet']) )* $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
      }
      if($valutaResultaat<>0 && $mutaties['Transactietype'] == "A/S" )
      {
//     echo " resultaatlopende=  $resultaatlopende = $t_aankoop_waarde - $historischekostprijs <br>\n";
//        listarray($mutaties);
       // echo "fondsResultaat  = $fondsResultaat <br>\n valutaResultaat = $valutaResultaat <br>\n -------- <br>\n";
      }
      $data['totalen']['gerealiseerdResultaat']+=($result_lopendejaar);//$result_voorgaandejaren
      $data['totalen']['fonds']+=$fondsResultaat;
      $data['totalen']['valuta']+=$valutaResultaat;
      // echo "$rapportageDatumVanaf,$rapportageDatum ($result_voorgaandejaren+$result_lopendejaar) $fondsResultaat $valutaResultaat <br>\n";
      
      // listarray($historie);
      $valutaResultaat=	($resultaatlopende)-$fondsResultaat;
      $totaalFondsResultaat+=$fondsResultaat;
      $totaalValutaResultaat+=$valutaResultaat;
//echo $mutaties['Omschrijving']. " $fondsResultaat = ($t_verkoop_waardeinValuta-$beginwaardeLopendeJaarValuta)*getValutaKoers(".$mutaties['Valuta']." ,".$mutaties['Boekdatum'].");<br>\n";
      
      
    }
    //   listarray($data);
    
    return $data;
  }
  
  
}

?>
