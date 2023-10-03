<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/04/30 05:37:37 $
File Versie					: $Revision: 1.5 $

$Log: RapportOIS_L76.php,v $
Revision 1.5  2018/04/30 05:37:37  rvv
*** empty log message ***

Revision 1.4  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.3  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.2  2018/04/21 17:55:51  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.7  2015/11/01 17:25:34  rvv
*** empty log message ***

Revision 1.6  2014/07/06 12:38:11  rvv
*** empty log message ***

Revision 1.5  2013/07/10 15:24:21  rvv
*** empty log message ***

Revision 1.4  2013/05/29 11:46:50  rvv
*** empty log message ***

Revision 1.3  2013/05/12 15:49:07  rvv
*** empty log message ***

Revision 1.2  2013/05/12 11:19:21  rvv
*** empty log message ***

Revision 1.1  2013/05/04 15:59:49  rvv
*** empty log message ***

Revision 1.4  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.3  2013/04/10 15:58:01  rvv
*** empty log message ***

Revision 1.2  2013/04/03 14:58:34  rvv
*** empty log message ***

Revision 1.1  2013/03/23 16:19:36  rvv
*** empty log message ***

Revision 1.32  2013/03/06 16:59:51  rvv
*** empty log message ***

Revision 1.31  2013/03/03 10:34:49  rvv
*** empty log message ***

Revision 1.30  2013/02/27 17:04:41  rvv
*** empty log message ***

Revision 1.29  2012/12/30 14:27:11  rvv
*** empty log message ***

Revision 1.28  2012/09/05 18:19:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportOIS_L76
{
	function RapportOIS_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_startDatum = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->zakelijk=true;
    $this->dataOnly=false;
    $this->oisData=array();

    $this->db=new DB();  
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
      $this->vastWhere="";
      if($this->dataOnly==false)
        $this->pdf->rapport_titel = vertaalTekst("Portefeuille analyse",$this->pdf->rapport_taal);

    $db=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $query = "SELECT doorkijkCategorie,doorkijkCategorieSoort,grafiekKleur, afdrukVolgorde
                   FROM doorkijk_categoriePerVermogensbeheerder 
                   WHERE Vermogensbeheerder='$beheerder'
                   ORDER BY doorkijkCategorieSoort,afdrukVolgorde 
                  ";

    $db->SQL($query);
    $db->Query();
    $this->kleuren=array();
    while($data = $db->nextRecord())
    {
      $this->kleuren[$data['doorkijkCategorieSoort']][$data['doorkijkCategorie']]=unserialize($data['grafiekKleur']);
    }


    $this->rapportZakelijk();
  }

  function bepaalWeging($doorkijkSoort,$belCategorien=array())
  {
    global $__appvar;
    if(is_array($belCategorien) && count($belCategorien)>0)
      $fondsFilter="AND Beleggingscategorie IN('".implode("','",$belCategorien)."')";
    else
      $fondsFilter='';

    $db = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
                  FROM TijdelijkeRapportage 
                  WHERE rapportageDatum ='" . $this->rapportageDatum . "' $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $totaalWaarde = $db->nextRecord();

    $vertaling=array('Beleggingscategorien'=>'Beleggingscategorie','Beleggingssectoren'=>'Beleggingssector','Regios'=>'Regio');
    $query = "SELECT fonds,rekening, actuelePortefeuilleWaardeEuro as waardeEUR, ".$vertaling[$doorkijkSoort]." as airsSoort
					FROM TijdelijkeRapportage	WHERE rapportageDatum ='".$this->rapportageDatum."'  $fondsFilter AND portefeuille = '" . $this->portefeuille . "'" .	$__appvar['TijdelijkeRapportageMaakUniek']." Order by fonds";

    $db=new DB();
    $db->SQL($query); //echo $query."<br>\n";exit;
    $db->Query();

    $doorkijkVerdeling=array();
    while($row = $db->nextRecord())
    {
      if($row['fonds']=='' && $doorkijkSoort <> 'Regios' && $doorkijkSoort <> 'Beleggingscategorien' )
      {
        $row['fonds'] = $row['rekening'];
        $verdeling=array('Geldrekeningen'=>array('weging'=>100,'waarde'=>$row['waardeEUR']));
      }
      else
      {
        if($row['fonds']=='')
          $row['fonds'] = $row['rekening'];
        //	listarray($row);
        $verdeling = $this->bepaalWegingPerFonds($row['fonds'], $doorkijkSoort, $row['airsSoort'], $row['waardeEUR'],$row['rekening']);
      }
      $totaalPercentage=0;
      if(is_array($verdeling))
      {
        $overige=false;
        $check=0;
        foreach($verdeling as $categorie=>$percentage)
        {
          $check+=$percentage['weging'];
          $totaalPercentage=($percentage['weging'] * ($row['waardeEUR']/$totaalWaarde['totaal']) );
          $doorkijkVerdeling['categorien'][$categorie]+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['percentage']+=$totaalPercentage;
          $doorkijkVerdeling['details'][$categorie]['waardeEUR']+=$percentage['waarde'];
        }
        if($check==0)
          $overige=true;
        elseif(round($check,5) <> 100)
        {
          if($this->debug)
            $this->debugData['afwijkingWeging'][$doorkijkSoort][$row['fonds']]['afwijking']['weging'] = $check-100;
        }
      }
      else
        $overige=true;

      if($overige==true)
      {
        $totaalPercentage=($row['waardeEUR'] / $totaalWaarde['totaal']) * 100;
        $doorkijkVerdeling['categorien']['Overige'] += $totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['percentage']+=$totaalPercentage;
        $doorkijkVerdeling['details']['Overige']['waardeEUR']+=$row['waardeEUR'];
      }
    }
    return $doorkijkVerdeling;
  }

  function bepaalWegingPerFonds($fonds,$doorkijkSoort,$airsCategorie,$waarde,$rekening)
  {
    $db=new DB();
    $query="SELECT MAX(datumVanaf) as vanafDatum FROM doorkijk_categorieWegingenPerFonds 
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND  datumVanaf <= '" . $this->rapportageDatum . "' ";
    $db->executeQuery($query);
    $vanafDatum=$db->nextRecord();//listarray($query);

    $query="SELECT msCategorie,weging FROM doorkijk_categorieWegingenPerFonds 
WHERE fonds='".mysql_real_escape_string($fonds)."' AND msCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND datumVanaf = '" . $vanafDatum['vanafDatum']. "'  ";
    $db->executeQuery($query);
    $wegingPerMsCategorie=array();
    while ($row = $db->nextRecord())
    {
      $wegingPerMsCategorie[$row['msCategorie']] = $row['weging'];
    }
//echo $query;
    $wegingDoorkijkCategorie=array();
    $airsKoppelingen=array('REGION_ZOTHERND','ZSECTOR_OTHERND');
    if(count($wegingPerMsCategorie)>0)
    {
      foreach($airsKoppelingen as $categorie)
      {
        if (isset($wegingPerMsCategorie[$categorie]))
        {
          $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
          $db->executeQuery($query);
          //		listarray($wegingPerMsCategorie);
          //		echo $wegingPerMsCategorie[$categorie]."| $fonds | $airsCategorie | $doorkijkSoort | $query<br>\n";

          if($db->records()>0)
          {

            while ($row = $db->nextRecord())
            {
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = $wegingPerMsCategorie[$categorie];
              $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde * $wegingPerMsCategorie[$categorie] / 100;

              if ($this->debug)
              {
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['weging'] = 100;
                $this->debugData['DoorkijkfondsWeging'][$doorkijkSoort][$fonds][$row['doorkijkCategorie']]['waarde'] = $waarde;
              }
            }
            unset($wegingPerMsCategorie[$categorie]);
          }
        }
      }

      $msCategorienWhere=" bronKoppeling IN ('".implode("','",array_keys($wegingPerMsCategorie))."')";
      $query = "SELECT doorkijkCategorie,bronKoppeling as msCategorie FROM doorkijk_koppelingPerVermogensbeheerder 
WHERE $msCategorienWhere AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='MS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);

      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] += $wegingPerMsCategorie[$row['msCategorie']];
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] += $waarde*$wegingPerMsCategorie[$row['msCategorie']]/100;
      }
    }
    else
    {
      $query = "SELECT doorkijkCategorie FROM doorkijk_koppelingPerVermogensbeheerder 
WHERE bronKoppeling='$airsCategorie' AND doorkijkCategoriesoort='".mysql_real_escape_string($doorkijkSoort)."' AND systeem='AIRS' AND vermogensbeheerder='". $this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $db->executeQuery($query);

      while ($row = $db->nextRecord())
      {
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['weging'] = 100;
        $wegingDoorkijkCategorie[$row['doorkijkCategorie']]['waarde'] = $waarde;
      }
    }


    return $wegingDoorkijkCategorie;
  }

  function vulPagina($belCategorien='')
  {
    $pieTeller = 0;

    $doorkijkCategorieSoorten=array('Beleggingscategorien','Regios','Beleggingssectoren');

    $doorkijkTitels=array('Beleggingscategorien'=>'Beleggingscategorie','Regios'=>'Regio','Beleggingssectoren'=>'Beleggingssector');//array();Beleggingscategorie
    foreach($doorkijkCategorieSoorten as $index=>$doorkijkCategorieSoort)
    {
      $xOffset =  $index * 98;
      $doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
      $grafiekdata=array();
      $grafiekTonen=true;
      foreach($this->kleuren[$doorkijkCategorieSoort] as $categorie=>$kleurdata) //foreach($doorKijk['categorien'] as $categorie=>$percentage)
      {
        if(isset($doorKijk['categorien'][$categorie]))
        {
          $percentage=$doorKijk['categorien'][$categorie];
          $grafiekdata[$categorie]['kleur'] = $this->kleuren[$doorkijkCategorieSoort][$categorie];//array('R' => array('value' => $kleuren[$categorie][0]),'G' => array('value' => $kleuren[$categorie][1]),'B' => array('value' => $kleuren[$categorie][2]));
          $grafiekdata[$categorie]['percentage'] = $percentage;
        }
      }
      if($grafiekTonen==true)
        $this->printPie( $grafiekdata,30+$xOffset,125,$doorkijkTitels[$doorkijkCategorieSoort]); //+$yOffset);
      $pieTeller++;
    }

  }


  
  function rapportZakelijk()
  {
    global $__appvar;
    if($this->dataOnly==false)
    {
      $this->pdf->AddPage();
     // $this->pdf->templateVars['KERNZPaginas'] = $this->pdf->customPageNo;//+$this->pdf->extraPage
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas'] = $this->pdf->rapport_titel;
      $this->pdf->SetDrawColor(0);
    }

    $query = "SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie
FROM
CategorienPerHoofdcategorie
WHERE
CategorienPerHoofdcategorie.Hoofdcategorie='ZAK' AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder'] ."'";
    debugSpecial($query,__FILE__,__LINE__);
    $DB=new DB();
    $DB->SQL($query); //echo $query."<br>\n";
    $DB->Query();
    $belCategorien=array();
    while($totaal = $DB->nextRecord())
    {
      $belCategorien[$totaal['Beleggingscategorie']]=$totaal['Beleggingscategorie'];
    }

    $doorkijkCategorieSoort='Beleggingssectoren';
    $doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
    foreach($doorKijk['categorien'] as $categorie=>$percentage)
    {
      $kleur=array('R'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][0]),'G'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][1]),'B'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][2]));
      $data['sectorverdeling']['kleurData'][$categorie] = $kleur;//$this->kleuren[$doorkijkCategorieSoort][$categorie];
      $data['sectorverdeling']['percentage'][$categorie]= $percentage;
    }

    $doorkijkCategorieSoort='Regios';
    $doorKijk= $this->bepaalWeging($doorkijkCategorieSoort,$belCategorien);
    foreach($doorKijk['categorien'] as $categorie=>$percentage)
    {
      $kleur=array('R'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][0]),'G'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][1]),'B'=>array('value'=>$this->kleuren[$doorkijkCategorieSoort][$categorie][2]));
      $data['regioverdelingZAK']['kleurData'][$categorie] = $kleur;//$this->kleuren[$doorkijkCategorieSoort][$categorie];
      $data['regioverdelingZAK']['percentage'][$categorie]= $percentage;
    }


//listarray($data['regioverdelingZAK']);

    $DB=new DB();
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
  	$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
  	$DB->SQL($q);
	  $DB->Query();
	  $kleuren = $DB->LookupRecord();
	  $this->allekleuren = unserialize($kleuren['grafiek_kleur']);
 
    //$this->vastWhere=" AND TijdelijkeRapportage.hoofdcategorie='G-RISD'";
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage 
           WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query."<br>\n";
  	$DB->Query();
  	while($totaal = $DB->nextRecord())
    {
  	  $totaalWaarde[$totaal['rapportageDatum']]=$totaal['totaal'];
    }
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage 
           WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek']." AND TijdelijkeRapportage.hoofdcategorie='VAR' GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query."<br>\n";
  	$DB->Query();
    while($totaal = $DB->nextRecord())
    {
  	  $totaalWaardeVar[$totaal['rapportageDatum']]=$totaal['totaal'];
    } 
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal, rapportageDatum ".
					 "FROM TijdelijkeRapportage 
           WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek']." AND TijdelijkeRapportage.hoofdcategorie='ZAK' GROUP BY rapportageDatum";
	  debugSpecial($query,__FILE__,__LINE__);
  	$DB->SQL($query); //echo $query."<br>\n";
  	$DB->Query();
    while($totaal = $DB->nextRecord())
    {
  	  $totaalWaardeZak[$totaal['rapportageDatum']]=$totaal['totaal'];
    } 
   //listarray($totaalWaardeVar);
    $rapportageDatum = $this->rapportageDatum;
  	$query="SELECT beleggingssector,beleggingssectorOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." AND TijdelijkeRapportage.hoofdcategorie='ZAK'
    GROUP BY beleggingssector ORDER BY actuelePortefeuilleWaardeEuro desc ";
	  $DB->SQL($query); //echo $query;exit;
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['beleggingssector']=='')
        $fonds['beleggingssector']='Geen sector';
      if($fonds['beleggingssectorOmschrijving']=='')
        $fonds['beleggingssectorOmschrijving']='Geen';  
 
    //  $data['sectorverdeling']['kleurData'][$fonds['beleggingssectorOmschrijving']]=$this->allekleuren['OIS'][$fonds['beleggingssector']];
    //  $data['sectorverdeling']['percentage'][$fonds['beleggingssectorOmschrijving']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaardeZak[$this->rapportageDatum]*100;
	  }  

  	$query="SELECT valuta,valutaOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage 
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']." 
    GROUP BY valuta ORDER BY actuelePortefeuilleWaardeEuro desc ";
	  $DB->SQL($query); 
	  $DB->Query();
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['beleggingssector']=='')
        $fonds['beleggingssector']='Geen sector';
      if($fonds['beleggingssectorOmschrijving']=='')
        $fonds['beleggingssectorOmschrijving']='Geen';  
 
      $kleur=$this->allekleuren['OIV'][$fonds['valuta']];
     // $data['valutaverdeling']['kleurData'][]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      $data['valutaverdeling']['kleurData'][$fonds['valutaOmschrijving']]=$this->allekleuren['OIV'][$fonds['valuta']];
      $data['valutaverdeling']['percentage'][$fonds['valutaOmschrijving']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$this->rapportageDatum]*100;
	  } 
//

  	$query="SELECT IF(Rating.Afdrukvolgorde IS NULL ,127,Rating.Afdrukvolgorde) as Afdrukvolgorde, Fondsen.rating as rating, Rating.Omschrijving as ratingOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage  
    LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
    LEFT JOIN Rating ON Fondsen.rating = Rating.rating
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
    $__appvar['TijdelijkeRapportageMaakUniek']." AND TijdelijkeRapportage.hoofdcategorie='VAR' 
    GROUP BY rating ORDER BY Afdrukvolgorde asc";
	  $DB->SQL($query); //echo "<br> $query <br>";exit;
	  $DB->Query(); 
	 	while($fonds = $DB->nextRecord())
  	{ 
      if($fonds['rating']=='')
        $fonds['rating']='Geen rating';
      if($fonds['ratingOmschrijving']=='')
        $fonds['ratingOmschrijving']='Geen rating';  
    //    echo $fonds['rating']." ";
      $data['ratingverdelingVAR']['kleurData'][$fonds['rating']]=$this->allekleuren['Rating'][$fonds['rating']];
      $data['ratingverdelingVAR']['percentage'][$fonds['rating']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaardeVar[$this->rapportageDatum]*100;
	  
	  
	 }

    $query="SELECT TijdelijkeRapportage.Regiovolgorde as Afdrukvolgorde, TijdelijkeRapportage.regio as regio, TijdelijkeRapportage.regioOmschrijving as regioOmschrijving,sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage  
    WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'".
      $__appvar['TijdelijkeRapportageMaakUniek']. $this->vastWhere." AND TijdelijkeRapportage.hoofdcategorie='ZAK' 
    GROUP BY regio ORDER BY Afdrukvolgorde asc";
    $DB->SQL($query); //echo "<br> $query <br>";exit;
    $DB->Query();
    while($fonds = $DB->nextRecord())
    {
      if($fonds['regio']=='')
        $fonds['regio']='Geen regio';
      if($fonds['regioOmschrijving']=='')
        $fonds['regioOmschrijving']='Geen regio';
      //    echo $fonds['regio']." ";
  //    $data['regioverdelingZAK']['kleurData'][$fonds['regio']]=$this->allekleuren['OIR'][$fonds['regio']];
  //    $data['regioverdelingZAK']['percentage'][$fonds['regio']]=$fonds['actuelePortefeuilleWaardeEuro']/$totaalWaardeZak[$this->rapportageDatum]*100;


    }

    
    $max=max(count($data['regioverdelingZAK']['percentage']),count($data['sectorverdeling']['percentage']));
    for($i=count($data['regioverdelingZAK']['percentage']); $i<$max; $i++)
      $data['regioverdelingZAK']['percentage'][]=0;
    for($i=count($data['sectorverdeling']['percentage']); $i<$max; $i++)
      $data['sectorverdeling']['percentage'][]=0;   

  // listarray($data);
  $this->oisData=$data;
  if($this->dataOnly==false)
  {
$this->pdf->setXY(40,32);
$this->BarDiagram(90,70,$data['sectorverdeling']['percentage'],'%l (%p)',$data['sectorverdeling']['kleurData'],vertaalTekst("Sectorverdeling zakelijke waarden",$this->pdf->rapport_taal));

$this->pdf->setXY(40,115);
$this->BarDiagram(90, 50,$data['valutaverdeling']['percentage'], '%l (%p)', $data['valutaverdeling']['kleurData'],vertaalTekst("Valutaverdeling",$this->pdf->rapport_taal));

$this->pdf->setXY(180,32);
$this->BarDiagram(90,70,$data['regioverdelingZAK']['percentage'],'%l (%p)',$data['regioverdelingZAK']['kleurData'],vertaalTekst("Regioverdeling zakelijke waarden",$this->pdf->rapport_taal));

$this->pdf->setXY(180,115);
$this->BarDiagram(90,70,$data['ratingverdelingVAR']['percentage'],'%l (%p)',$data['ratingverdelingVAR']['kleurData'],vertaalTekst("Ratingverdeling vastrentende waarden",$this->pdf->rapport_taal));
  }

  }
  
  function SetLegends2($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
      }
  }
  
  
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends2($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $nbDiv=5;
      $legendWidth=10;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage +  $legendWidth;
      $lDiag = floor($w - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal > 0)
        $minVal=0;
      $maxVal=ceil($maxVal/10)*10;  

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100; 
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      
      //echo "$hBar <br>\n";
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;

      $legendaStep=$unit/$nbDiv*$bandBreedte;
      //if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*5;
     // if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*2;
     // if($bandBreedte/$legendaStep > $nbDiv)
     //   $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          $i++;
          if($i>100)
            break;
        }

        $i=0;
        //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;

      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      
   
      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key]['R']['value'],$colorArray[$key]['G']['value'],$colorArray[$key]['B']['value']);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }
  

  function BarDiagram_new($w, $h, $data, $format,$colorArray,$titel)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends2($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $nbDiv=5;
      $legendWidth=10;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage +  $legendWidth;
      $lDiag = floor($w - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal > 0)
        $minVal=0;
      $maxVal=round($maxVal*10)/10 ;

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*10)/10; 
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      
      //echo "$hBar <br>\n";
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;

      $legendaStep=$unit/$nbDiv*$bandBreedte;
    //  if($bandBreedte/$legendaStep > $nbDiv)
    //    $legendaStep=$legendaStep*2;
     // if($bandBreedte/$legendaStep > $nbDiv)
    //    $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          $i++;
          if($i>100)
            break;
        }

        $i=0;
        //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $i=0;

      $this->pdf->SetXY($XDiag, $YDiag);
      $this->pdf->Cell($lDiag, $hval-4, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      
   $hLegend=3;
      foreach($data as $key=>$val)
      {
          if($this->pdf->legends[$i] <> '')
          {
          $this->pdf->SetFillColor($colorArray[$key]['R']['value'],$colorArray[$key]['G']['value'],$colorArray[$key]['B']['value']);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->formatGetal($val,1)."%",0,0,'R');
    
          $yval = $YDiag + $hDiag + ($i + 1) * $hBar - $eBaton / 2 +3;
          $this->pdf->Rect($XPage+$legendWidth, $yval, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($XPage+$legendWidth+5, $yval);
          $this->pdf->Cell(80 , $hval, $this->pdf->legends[$i],0,0,'L');
          }
          $i++;
      }
      
      
   /*   
         $x1=$xval-50;
   $y1=$nulpunt+8;
   $hLegend=3;
   $legendaMarge=2;
   $vertaling['rente']='Coupons';
   $vertaling['lossing']='Lossingen';

         foreach ($colors as $categorie=>$color)
      {
      		$this->pdf->SetFont($this->rapport_font, '', 6);
		      $this->pdf->SetTextColor($this->rapport_fonds_fontcolor['R'],$this->rapport_fonds_fontcolor['G'],$this->rapport_fonds_fontcolor['B']);
		      $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

          $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
          $this->pdf->Rect($x1-5, $y1, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x1  ,$y1);
          $this->pdf->Cell(0,4,$vertaling[$categorie]);
         // $y1+= $hLegend + $legendaMarge;
          $x1+=40;
         $i++;

      }
      */
      

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }

 
 


	function printPie($pieData,$kleurdata,$title='',$width=100,$height=100,$hcat)
	{

	  $col1=array(255,0,0); // rood
	  $col2=array(0,255,0); // groen
	  $col3=array(255,128,0); // oranje
	  $col4=array(0,0,255); // blauw
	  $col5=array(255,255,0); // geel
	  $col6=array(255,0,255); // paars
	  $col7=array(128,128,128); // grijs
	  $col8=array(128,64,64); // bruin
	  $col9=array(255,255,255); // wit
	  $col0=array(0,0,0); //zwart
	  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();

		if(isset($kleurdata))
		{
		  $grafiekKleuren = array();
		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
  			if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
	  		  $grafiekKleuren[]=$standaardKleuren[$a];
		  	else
			    $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
		  	$pieData[$key] = $value['percentage'];
		  	$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
			$this->pdf->setXY($startX+5,$y-3);
			$this->pdf->Cell(130,4,$title,0,0,"C");
			$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->setX($startX);
			$this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren,$hcat);
			$hoogte = ($this->pdf->getY() - $y) + 8;
			$this->pdf->setY($y);

			$this->pdf->SetLineWidth($this->pdf->lineWidth);
			$this->pdf->setX($startX);

		//	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);

	}

	function PieChart($w, $h, $data, $format, $colors=null,$hcat)
  {

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->pdf->NbVal; $i++) {
              $gray = $i * intval(255 / $this->pdf->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
   //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }

      //Legends
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $x1 = $XPage + $radius*2 + 25 ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag - ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);

      for($i=0; $i<$this->pdf->NbVal; $i++)
      {
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-20);
          if($hcat[$i] <> $lastHcat)
          {
            if(isset($lastHcat))
            {
              $extraY=8;
              //$y1+=3;
            }

            $this->pdf->SetXY($this->pdf->GetX(),$this->pdf->GetY()+$extraY);
            $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
            $this->pdf->Cell(0,$hLegend,$hcat[$i]);
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          }
          $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);

          $y1+=$hLegend + 2;
          $lastHcat=$hcat[$i];
      }
      $this->pdf->SetFillColor(0,0,0);

  }

    function SetLegends($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array(vertaalTekst($l,$this->pdf->rapport_taal),$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }

  function renteResultaat($portefeuille,$startDatum,$eindDatum)
  {
    global $__appvar;
    $DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$eindDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$startDatum' AND ".
						 " portefeuille = '$portefeuille' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		if($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta <> '')
       $koers=getValutaKoers($this->pdf->rapportageValuta,$data['datum']);
    else
       $koers=1;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $koers;
		return $opgelopenRente;
  }




}
?>