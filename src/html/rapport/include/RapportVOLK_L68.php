<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/27 16:14:13 $
File Versie					: $Revision: 1.8 $

$Log: RapportVOLK_L68.php,v $
Revision 1.8  2020/05/27 16:14:13  rvv
*** empty log message ***

Revision 1.7  2019/05/11 16:48:39  rvv
*** empty log message ***

Revision 1.6  2018/11/24 19:11:26  rvv
*** empty log message ***

Revision 1.5  2018/11/18 11:01:21  rvv
*** empty log message ***

Revision 1.4  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.3  2018/11/16 16:41:32  rvv
*** empty log message ***

Revision 1.2  2018/11/01 07:15:15  rvv
*** empty log message ***

Revision 1.1  2018/10/31 17:23:34  rvv
*** empty log message ***

Revision 1.2  2016/12/04 10:08:56  rvv
*** empty log message ***

Revision 1.1  2016/12/03 19:22:25  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L68.php");

class RapportVOLK_L68
{
	function RapportVOLK_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_jaar  =date('Y',$this->pdf->rapport_datumvanaf);
		$this->pdf->excelData 	= array();
		$this->pdf->rapport_titel = "Overzicht portefeuille - intern";
//		$this->pdf->rapport_titel = vertaalTekst("Overzicht",$this->pdf->rapport_taal);//." ".date("d-m-Y",$this->pdf->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".date("d-m-Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
    $this->portefeuilleOriginal = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
    $this->pdf->excelData=array();
    $this->totalen=array();
    $this->gebruikDoorkijk=false;
    $this->doorkijkRente=array();
    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $this->verdiept = new portefeuilleVerdiept($this->pdf, $this->portefeuille, $this->rapportageDatum);
      $this->verdiepteFondsen = $this->verdiept->getFondsen();

      if(count($this->verdiepteFondsen)>0)
        $this->gebruikDoorkijk=true;

      if($this->gebruikDoorkijk==true)
      {
        foreach($this->verdiept->FondsPortefeuilleData as $vport)
        {
          $fondswaarden=array();
          $fondswaarden[$this->rapportageDatumVanaf] = berekenPortefeuilleWaarde($vport,$this->rapportageDatumVanaf,(substr($this->rapportageDatumVanaf, 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatumVanaf);
          $fondswaarden[$this->rapportageDatum] = berekenPortefeuilleWaarde($vport,$this->rapportageDatum,(substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatumVanaf);
          foreach($fondswaarden as $datum=>$elementen)
          {
            foreach($elementen as $details)
            {
              if ($details['type'] == 'rente')
              {
                
                $this->doorkijkRente[$datum][$vport][$details['fonds']]['actuelePortefeuilleWaardeEuro'] += $details['actuelePortefeuilleWaardeEuro'];
              }
            }
          }
         }
        $this->att = new ATTberekening_L68($this, 'jaar', false);
        $this->waarden['fondsen'] = $this->att->bereken($rapportageDatumVanaf, $this->rapportageDatum, 'instrument', false);
  
  
        // listarray($this->waarden['fondsen']);exit;
      }
    }
	}
  
  function testLenghth($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }

	function formatGetal($waarde, $dec,$procent=false,$nulTonen=false)
	{
	  if($waarde==0 && $nulTonen==false)
	    return;
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
		  $data.="%";
		return $data;
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	    return number_format($this->pdf->ValutaKoersEind,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  else
	  {
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;
	    return number_format($this->pdf->ValutaKoersBegin,2,",",".") ." - ".number_format($waarde,$dec,",",".");
	  }
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($waarde==0)
	    return;
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if (!isset($newDec) && $decimaal != '0')
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



	function printSubTotaal($lastCategorieOmschrijving,$allData,$style='')
	{
	  if($lastCategorieOmschrijving != 'Totaal')
	  {
	    $prefix='Subtotaal';
	    $this->pdf->CellBorders =array('','','','','','','',array('TS'),'','');
	  }
	  else
	  {
	    $prefix='';
	    $this->pdf->CellBorders = array('','','','','','','',array('TS','UU'),'','');
	  }

    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

    $this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
    $this->pdf->setX($this->pdf->marge);

    $data=$allData['perf'];


    if($data['bijdrage'] < 0)
      $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
    else
      $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

   	$this->pdf->row(array('',//substr(vertaalTekst($categorieData['omschrijving'],$this->pdf->rapport_taal),0,25),
												'',//substr($categorieData['fondsOmschrijving'][$id],0,25),
                      '','','','',
										
												'',
												$this->formatGetal($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal,false,true),
                        '',//$this->formatGetal($data['resultaat']/$data['gemWaarde']*100,2,false,true),
                        ''));

    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,vertaalTekst($title,$this->pdf->rapport_taal),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
  
  function getCRMnaam($portefeuille='',$rekening='')
  {
    $db = new DB();
    if($rekening<>'')
    {
      $query = "SELECT CRM_naw.naam FROM Rekeningen JOIN CRM_naw ON Rekeningen.Portefeuille=CRM_naw.portefeuille WHERE Rekeningen.Rekening='$rekening' AND Rekeningen.consolidatie=0";
    }
    else
      $query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    $naamParts=explode('-',$crmData['naam'],2);
    $naam=trim($naamParts[1]);
    if($naam<>'')
      return $naam;
    else
      return $portefeuille;
  }
  
  function getDividend2($fonds,$portefeuille)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
  
  
    $rente=array();
    
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
  

    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
  

    }
  
    if(isset($this->doorkijkRente[$this->rapportageDatumVanaf][$portefeuille][$fonds]))
    {
      $rente[$this->rapportageDatumVanaf]=$this->doorkijkRente[$this->rapportageDatumVanaf][$portefeuille][$fonds]['actuelePortefeuilleWaardeEuro'];
    }
  
    if(isset($this->doorkijkRente[$this->rapportageDatum][$portefeuille][$fonds]))
    {
      $rente[$this->rapportageDatum]=$this->doorkijkRente[$this->rapportageDatum][$portefeuille][$fonds]['actuelePortefeuilleWaardeEuro'];
    }

    /*
    if($fonds=='0,125% Coca-Cola 21-29')
    {
      echo $this->rapportageDatumVanaf." | $portefeuille | $fonds | "."$query <br>\n";
      listarray($rente);
    }
  */
  
    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      if(!isset($aantal[$this->rapportageDatum]) || $aantal[$this->rapportageDatum]==0)
      {
        $fondsAantal=fondsAantalOpdatum($portefeuille,$fonds,$this->rapportageDatum);
        $aantal[$this->rapportageDatum]=$fondsAantal['totaalAantal'];
      }
      
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
      {
        $aandeel=$aantal[$this->rapportageDatum]/$aantal[$boekdatum];
      }
     /*$fonds=='0,125% Coca-Cola 21-29')
      {
        echo "<hr>$totaal <br>\n";
        echo $aantal[$boekdatum]." > ".$aantal[$this->rapportageDatum]."<br>";
        echo "$fonds | $aandeel | $boekdatum | " . $this->rapportageDatum . " | " . ($data['Credit'] - $data['Debet']) . "<br>\n";
        echo "<hr><br>\n";
ob_flush();
      }
     */
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }

    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }

	function writeRapport()
	{
		global $__appvar,$USR;
    $totaalSom=array();

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $gebruiktePortefeuilles=array($this->portefeuille);
    $doorkijkDividend=array();
    $doorkijkRente=array();
    if($this->pdf->lastPOST['doorkijk']==1 && $this->gebruikDoorkijk==true)
    {
     // $verdiepteFondsen = $this->verdiept->getFondsen();
      foreach ($this->verdiepteFondsen as $fonds)
        $this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$this->rapportageDatum,'',$this->rapportageDatumVanaf);

      $gebruiktePortefeuilles=array_values($this->verdiept->FondsPortefeuilleData);
      $gebruiktePortefeuilles[]=$this->portefeuille;
	//	listarray($verdiepteFondsen);
 //   listarray($this->verdiept->FondsPortefeuilleData);
      
      if(substr($this->rapportageDatum,5,5)=='01-01')
        $startjaar=true;
      else
        $startjaar=false;
      
      $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $this->rapportageDatum,$startjaar,'EUR',$this->rapportageDatumVanaf);
      $correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
      foreach($fondswaarden as $i=>$fondsData)
      {
        //
        if(isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
        {
          
          $fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
          $fondsWaardeHuis=$this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
          $aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
          
          //echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis aantal:".count($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'])." <br>\n";
          //listarray($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling']);
          unset($fondswaarden[$i]);
          foreach($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
          {
            foreach ($details as $element => $emementDetail)
            {
              //if($this->verdiept->FondsPortefeuilleData[$fondsData['fonds']]=='336251')
              //{
              //  echo $this->verdiept->FondsPortefeuilleData[$fondsData['fonds']]." $type $element," . $this->verdiept->FondsPortefeuilleData[$element] . "<br>\n";
              //}
              if($type=='fondsen')
              {
                
                $dividend = $this->getDividend2($element,$this->verdiept->FondsPortefeuilleData[$fondsData['fonds']]);
                $doorkijkDividend[$element]['totaal']+=$aandeel*$dividend['totaal'];
                $doorkijkDividend[$element]['corrected']+=$aandeel*$dividend['corrected'];
  
  
                //listarray($emementDetail['overige']);
                //$emementDetail['overige']['beginwaardeLopendeJaar']= globalGetFondsKoers($emementDetail['overige']['Fonds'],$this->rapportageDatumVanaf);
                //$emementDetail['overige']['beginPortefeuilleWaardeInValuta']=$emementDetail['overige']['totaalAantal']*$emementDetail['overige']['beginwaardeLopendeJaar']*$emementDetail['overige']['fondsEenheid'];
                //$emementDetail['overige']['beginPortefeuilleWaardeEuro']=$emementDetail['overige']['beginPortefeuilleWaardeInValuta']*$emementDetail['overige']['beginwaardeValutaLopendeJaar'];
                //listarray($emementDetail['overige']);
                //echo "-----<br>\n";
              }

              if(isset($emementDetail['overige']))
              {
                foreach($correctieVelden as $veld)
                  $emementDetail['overige'][$veld]=$emementDetail['overige'][$veld]*$aandeel;
                unset($emementDetail['overige']['WaardeEuro']);
                unset($emementDetail['overige']['koersLeeftijd']);
                unset($emementDetail['overige']['FondsOmschrijving']);
                unset($emementDetail['overige']['Fonds']);
                $fondswaarden[] = $emementDetail['overige'];
              }
            }
          }
        }
      }
      //listarray($doorkijkDividend);
     // exit;
      $fondswaarden  = array_values($fondswaarden);//listarray($fondswaarden);
      $tmp=array();
      foreach($fondswaarden as $mixedInstrument)
      {
        $instrument=array();
        foreach($mixedInstrument as $index=>$value)
          $instrument[strtolower($index)]=$value;
        unset($instrument['voorgaandejarenactief']);
        
        $key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
        if(isset($tmp[$key]))
        {
          foreach($correctieVelden as $veld)
          {
            $veld=strtolower($veld);
            $tmp[$key][$veld] += $instrument[$veld];
          }
        }
        else
          $tmp[$key]=$instrument;
        //	listarray($instrument);
      }
      $fondswaarden  = array_values($tmp);


//		listarray($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] );
      
      $this->portefeuille='v'.$this->portefeuille;
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille, $this->rapportageDatum);
    }
    
    
    $query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
      " TijdelijkeRapportage.fonds, ".
      " TijdelijkeRapportage.actueleValuta, ".
      " TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginwaardeLopendeJaar , ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
      " TijdelijkeRapportage.Valuta, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro /  ".$this->pdf->ValutaKoersBegin. " as beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
      " FROM TijdelijkeRapportage WHERE ".
      " TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.type =  'fondsen' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";//exit;
    
    // print detail (select from tijdelijkeRapportage)
    $DB2 = new DB();
    $DB2->SQL($query);
    $DB2->Query();
    $volkFondsData=array();
    while($data = $DB2->NextRecord())
    {
      $volkFondsData[$data['fonds']]=$data;
    }
    

		$this->pdf->AddPage();

    $this->pdf->templateVars['VOLKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['VOLKPaginas']=$this->pdf->rapport_titel;

		$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'],$this->pdf->rapport_lijn_rood['g'],$this->pdf->rapport_lijn_rood['b']);
		$this->pdf->SetLineWidth(0.1);

				// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde['begin'] = $totaalWaarde['totaal'];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$totaalWaarde['eind'] = $totaal['totaal'];
    
    $fondsen=array();
    if(isset($this->pdf->portefeuilles)&& count($this->pdf->portefeuilles)>0)
    {
      foreach ($this->pdf->portefeuilles as $port)
      {
        $gegevens=berekenPortefeuilleWaarde($port,$this->rapportageDatum,(substr($this->rapportageDatum,5,5)=='01-01'?true:false),'EUR',substr($this->rapportageDatum,0,4).'-01-01');
        foreach ($gegevens as $regel)
        {
          if($regel['type']=='fondsen')
          {
            $fondsen[$regel['fonds']][$port]=$regel;
          }
        }
      }
      $consolidatie=true;
    }
    else
    {
      $consolidatie=false;
    }
    foreach($fondsen as $fonds=>$portefeuilles)
    {
      if(count($portefeuilles)==1)
        unset($fondsen[$fonds]);
    }
  //  listarray($fondsen);

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$this->rapportageDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$this->rapportageDatum."') - TO_DAYS('".$this->rapportageDatumVanaf."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();
	$gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];

	$this->totalen['begin']=$totaalWaarde['begin'];
	$this->totalen['eind']=$totaalWaarde['eind'];
	$this->totalen['gemiddeldeWaarde']=$gemiddelde;

		$query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingssectorPerFonds.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
if(Fondsen.OptieBovenliggendFonds='',Fondsen.Omschrijving ,optie.Omschrijving) as onderliggendFonds,
Fondsen.Valuta,
Fondsen.FondsImportCode as ISINcode
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON BeleggingssectorPerFonds.Regio = Regios.Regio
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
LEFT JOIN KeuzePerVermogensbeheerder AS BeleggingscategorienKeuze ON Beleggingscategorien.Beleggingscategorie = BeleggingscategorienKeuze.waarde AND BeleggingscategorienKeuze.categorie = 'Beleggingscategorien' AND BeleggingscategorienKeuze.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
Left Join Fondsen as optie ON Fondsen.OptieBovenliggendFonds = optie.Fonds
WHERE
Rekeningen.Portefeuille IN('".implode("','",$gebruiktePortefeuilles)."')  AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> '' AND Rekeningmutaties.Grootboekrekening='FONDS'
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, BeleggingscategorienKeuze.Afdrukvolgorde,onderliggendFonds,Fondsen.Omschrijving ";
			$DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
				if($data['Hoofdcategorie']=='')
					$data['Hoofdcategorie']='geen';
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perHoofdcategorie[$data['Hoofdcategorie']]['ISINcode'][]=$data['ISINcode'];
		    $perRegio[$data['Hoofdcategorie']]['omschrijving']=$data['regioOmschrijving']; //$data['Regio']
		    $perRegio[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];  //$data['Regio']
        $perRegio[$data['Hoofdcategorie']]['ISINcode'][]=$data['ISINcode'];  //$data['Regio']
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];//[$data['Regio']]
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['Rekening'][]=$data['Rekening'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];//[$data['Regio']]
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['ISINcode'][]=$data['ISINcode'];//[$data['Regio']]
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
Regios.Afdrukvolgorde,
rekeningBank.Omschrijving as depotbankOmschrijving
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
WHERE
Rekeningen.Portefeuille IN('".implode("','",$gebruiktePortefeuilles)."')   AND Rekeningen.Memoriaal=0 AND
Rekeningmutaties.Boekdatum >= '".$this->pdf->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,depotbankOmschrijving,
Rekeningen.Valuta,Rekeningen.Rekening";

			$DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
				if($data['Hoofdcategorie']=='')
					$data['Hoofdcategorie']='geen';
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['Rekening'][]=$data['rekening'];//[$data['Regio']]
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		    $alleData['rekeningen'][]=$data['rekening'];
		  }

$this->totalen['gemiddeldeWaarde']=0;
$perfTotaal=$this->fondsPerformance($alleData,true);

$this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];



      foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
        $perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($hoofdcategorieData);
		/*
           foreach ($perRegio as $hoofdCategorie=>$regioData)
             foreach ($regioData as $regio=>$regioWaarden)
               $perRegio[$hoofdCategorie][$regio]['perf'] = $this->fondsPerformance($regioWaarden);
        */

	foreach ($perCategorie as $hoofdCategorie=>$regioData)
	    foreach ($regioData as $categorie=>$categorieData)
	       $perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->fondsPerformance($categorieData); //[$regio]

	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

foreach ($perHoofdcategorie as $hoofdcategorie=>$hoofdcategorieData)
{
  $data=$hoofdcategorieData['perf'];
  if($data['bijdrage'] < 0)
    $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
  else
    $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

$totaalSom['beginwaarde'] += $data['beginwaarde'];
$totaalSom['eindwaarde'] += $data['eindwaarde'];
$totaalSom['stort'] += $data['stort'];
$totaalSom['gerealiseerd'] += $data['gerealiseerd'];
$totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
$totaalSom['kosten'] += $data['kosten'];
$totaalSom['resultaat'] += $data['resultaat'];
$totaalSom['gemWaarde'] += $data['gemWaarde'];
$totaalSom['weging'] += $data['weging'];
$totaalSom['bijdrage'] += $data['bijdrage'];
}


$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);


$perfTotaal=$totaalSom;
    
    
    $valutaVerdeling=array();
    $valutaDepotVerdeling=array();


		$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T','T','T','T');

  //if($perfTotaal['bijdrage'] < 0)
 //   $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
 // else
 //   $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

 if($this->pdf->debug == true)
    listarray($perRegio);

unset($this->pdf->CellBorders);
    $lastCategorie='';
    
    $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
    $n=0;
    
    $widthsBackup=$this->pdf->widths;
    $newIndex=0;
    $newWidths=array();

    foreach ($this->pdf->widths as $index=>$waarde)
    {
      if($index < 2)
        $newIndex+=$waarde;
      else
        $newIndex=$waarde;
      if($index == 0)
        $newWidths[]=0;
      else
        $newWidths[]=$newIndex;
    }

    $this->pdf->widths=$newWidths;

//$tmp=$perCategorie;
//foreach ($this->att->verdelingen['perHcatCat'] as $hoofdcategorie=>$categorieData)
//{
// foreach($categorieData as $cat=>$details)
//    $tmp[$hoofdcategorie][$cat] = $details;
//}
//foreach($tmp as $hoofdcategorie=>$categorieData)
  foreach ($perCategorie as $hoofdcategorie=>$categorieData)
{

  //$this->printKop($perHoofdcategorie[$hoofdcategorie]['omschrijving'],'BI',true);
  //foreach ($regioData as $regio=>$categorieData)
  //{

   // if($lastHoofdcategorie!=$hoofdcategorie)
   //   $extraRegel=false;
  //  else
   //   $extraRegel=true;
  
  
 // listarray( );
 // listarray($this->waarden['fondsen'] );
  
  

    foreach ($categorieData as $categorie=>$fondsData)
    {

      if($categorie!=$lastCategorie)
        $this->printKop( $perCategorie[$hoofdcategorie][$categorie]['omschrijving'],'');
      $lastCategorie=$categorie;


      foreach ($fondsData['fondsen'] as $id=>$fonds)
      {
        $tmp=array();
        $tmp['fondsen']=array($fonds);
        
        if($this->pdf->lastPOST['doorkijk']==1 && $this->gebruikDoorkijk==true)
        {
          $data = $this->waarden['fondsen'][$fonds];
        }
        else
        {
          $data=$this->fondsPerformance($tmp);
        }
      //  listarray($data);
//echo $this->pdf->getY(). " ".$fondsData['fondsOmschrijving'][$id]."<br>\n";
        if($this->pdf->getY() > 185)
          $this->pdf->AddPage();
        $this->pdf->widths=$newWidths;
        
      //  echo $this->pdf->getX()." ".substr($fondsData['fondsOmschrijving'][$id],0,30)." <br>\n";

        if(round($data['beginwaarde'],2) <> 0 || round($data['eindwaarde'],2) <> 0 || round($data['stort'],2) <> 0 || round($data['resultaat'],2) <> 0 || round($data['gemWaarde'],2) <> 0 )
        {
       
          $query="SELECT TijdelijkeRapportage.totaalAantal,TijdelijkeRapportage.actueleFonds,TijdelijkeRapportage.koersDatum
FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' AND
TijdelijkeRapportage.fonds='".mysql_real_escape_string($fondsData['fondsen'][$id]) ."'";
          $DB->SQL($query);
          $DB->Query();
          $fondsDetails = $DB->NextRecord();
  
          $query="SELECT Rekeningmutaties.Boekdatum FROM Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE Rekeningen.Portefeuille IN('".implode("','",$gebruiktePortefeuilles)."')  AND Rekeningmutaties.Fonds='".mysql_real_escape_string($fondsData['fondsen'][$id]) ."' AND
 Rekeningmutaties.GrootboekRekening = 'FONDS' AND Rekeningmutaties.Transactietype <> 'B' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' order BY Rekeningmutaties.Boekdatum desc limit 1";
          $DB->SQL($query);
          $DB->Query();
          $laatsteTransactie= $DB->NextRecord();
          if($laatsteTransactie['Boekdatum']=='')
          {
            $query="SELECT Rekeningmutaties.Boekdatum FROM Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE Rekeningen.Portefeuille IN('".implode("','",$gebruiktePortefeuilles)."')  AND Rekeningmutaties.Fonds='".mysql_real_escape_string($fondsData['fondsen'][$id]) ."' AND
 Rekeningmutaties.GrootboekRekening = 'FONDS' AND Rekeningmutaties.Transactietype='B' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' order BY Rekeningmutaties.Boekdatum asc limit 1";
            $DB->SQL($query);
            $DB->Query();
            $laatsteTransactie= $DB->NextRecord();

          }
  
          $query="SELECT Fondsen.FondsImportCode as ISINcode FROM Fondsen WHERE Fondsen.Fonds='".mysql_real_escape_string($fondsData['fondsen'][$id]) ."'";
          $DB->SQL($query);
          $DB->Query();
          $isin= $DB->NextRecord();
          $fondsData['ISINcode'][$id]=$isin['ISINcode'];
          
          if($fondsDetails['koersDatum']<>'')
          {
            $koersDatum = date('d-m-Y', db2jul($fondsDetails['koersDatum']));
          }
          else
          {
            $koersDatum='';
          }
          if($n%2==0)
            $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
          else
            $this->pdf->fillCell = array();
          $n++;
          
          if($consolidatie==true)
          {
            if(isset($fondsen[$fondsData['fondsen'][$id]]))
            {
              foreach ($fondsen[$fondsData['fondsen'][$id]] as $port => $details)
              {
                $this->pdf->widths=$newWidths;
                $query = "SELECT Rekeningmutaties.Boekdatum FROM Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE Rekeningen.Portefeuille='" . $port . "' AND Rekeningmutaties.Fonds='" . mysql_real_escape_string($fondsData['fondsen'][$id]) . "' AND
 Rekeningmutaties.GrootboekRekening = 'FONDS' AND Rekeningmutaties.Transactietype <> 'B' AND  Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' order BY Rekeningmutaties.Boekdatum desc limit 1";
                $DB->SQL($query);
                $DB->Query();
                $laatsteTransactie = $DB->NextRecord();
                if ($laatsteTransactie['Boekdatum'] == '')
                {
                  $query = "SELECT Rekeningmutaties.Boekdatum FROM Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
WHERE Rekeningen.Portefeuille='" . $port . "' AND Rekeningmutaties.Fonds='" . mysql_real_escape_string($fondsData['fondsen'][$id]) . "' AND
 Rekeningmutaties.GrootboekRekening = 'FONDS' AND Rekeningmutaties.Transactietype='B' AND Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' order BY Rekeningmutaties.Boekdatum asc limit 1";
                  $DB->SQL($query);
                  $DB->Query();
                  $laatsteTransactie = $DB->NextRecord();
      
                }
                $naam = $this->getCRMnaam($port);
                $this->pdf->row(array('', '    ' . substr($details['fondsOmschrijving'], 0, 50),
                                  $fondsData['ISINcode'][$id],
                                  $fondsData['fondsValuta'][$id],
                                  $this->formatGetal($details['totaalAantal'], 2),
                                  $this->formatGetal($fondsDetails['actueleFonds'], 2),
                                  $koersDatum,
                                  $this->formatGetal($details['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),
                                  '',
                                  ($laatsteTransactie['Boekdatum']==''?'':date('d-m-Y', db2jul($laatsteTransactie['Boekdatum']))),
                                  $this->testLenghth($naam, 10)));
                if($n%2==0)
                  $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
                else
                  $this->pdf->fillCell = array();
                $n++;
              }
              $n++;
            }
            else
            {
  
              //$doorkijkDividend[$element]['totaal']+=$aandeel*$dividend['totaal'];
              //$doorkijkDividend[$element]['corrected']+=$aandeel*$dividend['corrected'];
              //listarray($doorkijkDividend[$fonds]);
              $fonds=$fondsData['fondsen'][$id];
              $dividend = $this->getDividend2($fonds,$this->portefeuilleOriginal);
              $dividend['corrected']+=$doorkijkDividend[$fonds]['corrected'];
              $subdata=$volkFondsData[$fonds];
              $procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
       //  echo "$fonds |  $procentResultaat = ((".$subdata['actuelePortefeuilleWaardeEuro']." - ".$subdata['beginPortefeuilleWaardeEuro']." + ".$dividend['corrected'].") / (".$subdata['beginPortefeuilleWaardeEuro']." /100));<br>\n";
              
              $this->pdf->widths=$newWidths;
              $naam = $this->getCRMnaam($this->portefeuille, $fondsData['Rekening'][$id]);
              $this->pdf->row(array('', '    ' . substr($fondsData['fondsOmschrijving'][$id], 0, 50),
                                $fondsData['ISINcode'][$id],
                                $fondsData['fondsValuta'][$id],
                                $this->formatGetal($fondsDetails['totaalAantal'], 2),
                                $this->formatGetal($fondsDetails['actueleFonds'], 2),
                                $koersDatum,
                                $this->formatGetal($data['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
                                $this->formatGetal($procentResultaat,2),//$this->formatGetal($data['resultaat'] / $data['gemWaarde'] * 100, 2),
                                ($laatsteTransactie['Boekdatum']==''?'':date('d-m-Y', db2jul($laatsteTransactie['Boekdatum']))),
                                $this->testLenghth($naam, 10)));
            }
          }
          else
          {
            $fonds=$fondsData['fondsen'][$id];
            //listarray($fondsData['fondsen'][$id]);
            $dividend = $this->getDividend2($fonds,$this->portefeuilleOriginal);
            $dividend['corrected']+=$doorkijkDividend[$fonds]['corrected'];
            $subdata=$volkFondsData[$fonds];
          	$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'] + $dividend['corrected']) / ($subdata['beginPortefeuilleWaardeEuro'] /100));
      	//echo "$fonds |  $procentResultaat = ((".$subdata['actuelePortefeuilleWaardeEuro']." - ".$subdata['beginPortefeuilleWaardeEuro']." + ".$dividend['corrected'].") / (".$subdata['beginPortefeuilleWaardeEuro']."/ /100));<br>\n";
				    if($subdata['beginPortefeuilleWaardeEuro'] < 0)
              $procentResultaat = -1 * $procentResultaat;
  
            $this->pdf->widths=$newWidths;
            $this->pdf->row(array('', '    ' . substr($fondsData['fondsOmschrijving'][$id], 0, 50),
                              $fondsData['ISINcode'][$id],
                              $fondsData['fondsValuta'][$id],
                              $this->formatGetal($fondsDetails['totaalAantal'], 2),
                              $this->formatGetal($fondsDetails['actueleFonds'], 2),
                              $koersDatum,
                              $this->formatGetal($data['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
                              $this->formatGetal($procentResultaat,2),//.' | '.$this->formatGetal($data['resultaat'] / $data['gemWaarde'] * 100, 2),
                              ($laatsteTransactie['Boekdatum']==''?'':date('d-m-Y', db2jul($laatsteTransactie['Boekdatum'])))));
          }
  

  
  
          $this->pdf->excelData[]=array($fondsData['fondsOmschrijving'][$id],
												$fondsData['fondsValuta'][$id],
												round($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
												round($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
												round($data['stort'],0),
												round($data['resultaat'],0),
                        round($data['gemWaarde'],0),
                        round($data['resultaat']/$data['gemWaarde']*100,2),
                        round($data['weging']*100,2),
                        round($data['bijdrage']*100,2));            
        }

      }
      $this->pdf->fillCell = array();
      $this->pdf->widths=$widthsBackup;

      $rekeningData=array();
      $totaalRekeningen=0;
      $rekeningWaarde=array();
  
      foreach ($fondsData['rekeningen'] as $id=>$rekening)
      {
        $tmp=array();
        $tmp['rekeningen']=array($rekening);
        $data=$this->fondsPerformance($tmp);
        $rekeningData[$id]=array('perf'=>$data,'rekening'=>$rekening);
        if($data['eindwaarde'] <> 0)
          $rekeningWaarde[$id]=$data['eindwaarde'];
        $totaalRekeningen+=$data['eindwaarde'];
      }

      $aantalRegels=0;
      foreach ($rekeningWaarde as $id=>$waarde)
      {
        $fullRekeningData=$rekeningData[$id];
        $rekening=$fullRekeningData['rekening'];
        $data=$fullRekeningData['perf'];

        $query="SELECT
Rekeningen.Rekening,Rekeningen.Valuta,
if(Rekeningen.Depotbank <> '',rekeningBank.Omschrijving, Depotbanken.Omschrijving) as Omschrijving
FROM
Rekeningen
Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
Inner Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
WHERE Rekeningen.Rekening='$rekening' AND Portefeuilles.consolidatie=0";
        $DB->SQL($query);
		    $depot=$DB->lookupRecord();

        //$tmp=array();
        //$tmp['rekeningen']=array($rekening);
        //$data=$this->fondsPerformance($tmp);
        if($data['bijdrage'] < 0)
          $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_rood);
        else
          $this->pdf->CellFontColor = array('','','','','','','','','','','','',$this->pdf->rapport_font_groen);

        if($_POST['anoniem'] !=1)
          $rekening=$depot['Omschrijving'].' '.substr($fondsData['rekeningen'][$id],0,strlen($fondsData['rekeningen'][$id])-3);
        else
          $rekening="Effectenrekening";
        $valutaVerdeling[$depot['Valuta']]+=$data['eindwaarde']/$totaalRekeningen*100;
        $valutaDepotVerdeling[$depot['Valuta']]['waarde']+=$data['eindwaarde'];
        $valutaDepotVerdeling[$depot['Valuta']]['percentage']+=$data['eindwaarde']/$totaalRekeningen*100;
        $valutaDepotVerdeling[$depot['Valuta']]['depotbanken'][$rekening]['waarde']=$data['eindwaarde'];
        $valutaDepotVerdeling[$depot['Valuta']]['depotbanken'][$rekening]['percentage']=$data['eindwaarde']/$totaalRekeningen*100;
        $aantalRegels++;

        if($this->pdf->getY() > 185)
          $this->pdf->AddPage();
        
        if($n%2==0)
          $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
        else
          $this->pdf->fillCell = array();
        $n++;
        $this->pdf->widths=$newWidths;
        if($consolidatie==true)
        {
          $naam = $this->getCRMnaam($this->portefeuille, $fondsData['Rekening'][$id]);
          $this->pdf->row(array('', '    ' . $rekening, '',
                            $depot['Valuta'], '', '', '',
    
                            $this->formatGetal($data['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
                            '',//$this->formatGetal($data['resultaat'] / $data['gemWaarde'] * 100, 2),
                            '', $this->testLenghth($naam, 10)));
        }
        else
        {
          $this->pdf->row(array('', '    ' . $rekening, '',
                            $depot['Valuta'], '', '', '',
    
                            $this->formatGetal($data['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
                            '',//$this->formatGetal($data['resultaat'] / $data['gemWaarde'] * 100, 2)
                            ''));
        }
            
        $this->pdf->excelData[]=array($rekening,
												$depot['Valuta'],
												round($data['beginwaarde'],$this->pdf->rapport_VOLK_decimaal),
												round($data['eindwaarde'],$this->pdf->rapport_VOLK_decimaal),
												round($data['stort'],0),
												round($data['resultaat'],0),
                        round($data['gemWaarde'],0),
                        round($data['resultaat']/$data['gemWaarde']*100,2),
                        round($data['weging']*100,2),
                        round($data['bijdrage']*100,2));           
      }
      $this->pdf->fillCell = array();
      if($this->pdf->getY() > 185)
      {
        $this->pdf->AddPage();
        $this->pdf->Ln();
      }

      $this->pdf->widths=$widthsBackup;
      $this->printSubTotaal($perCategorie[$hoofdcategorie][$categorie]['omschrijving'],$perCategorie[$hoofdcategorie][$categorie]);
    }

 }
 
 if($this->pdf->getY() > 185)
 {
   $this->pdf->AddPage();
   $this->pdf->Ln();
 }
 $this->printSubTotaal('Totaal',array('perf'=>$perfTotaal),'BI');
/*

 $benodigdeHoogte=($aantalRegels+count($valutaVerdeling))*4;
 $y = $this->pdf->getY()+10;
 if($y > 140 || ($y+$benodigdeHoogte > 180))
 {
   $this->pdf->addPage();
   $y=$this->pdf->getY()+10;
 }
*/

//$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
//$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);



unset($this->pdf->CellFontColor);



	}


	function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';

      if ($this->pdf->rapportageValuta <> 'EUR')
      {
	      $koersQuery =	", (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";

      }
	    else
	    {
	      $koersQuery = ", 1 as Rapportagekoers";

	    }

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
		"Rekeningmutaties.Valutakoers $koersQuery ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
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

//echo $query;exit;


		$DB->Query();

		// haal koersresultaat op om % te berekenen


		$buffer = array();
    $data=array();
		$sortBuffer = array();
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
    $totaal_resultaat_waarde=0;

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
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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

			if(	$mutaties['Transactietype'] == "L" ||
					$mutaties['Transactietype'] == "V" ||
					$mutaties['Transactietype'] == "V/S" ||
					$mutaties['Transactietype'] == "A/S")
			{

				$historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,$rapportageDatumVanaf);

				if($mutaties['Transactietype'] == "A/S")
				{
					$historischekostprijs  = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
					$beginditjaar          = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}
				else
				{
					$historischekostprijs = $mutaties['Aantal']        * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
				  $beginditjaar         = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
				}
        if($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
		    {
		    $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		    $beginditjaar         = $beginditjaar         / getValutaKoers($this->pdf->rapportageValuta ,date("Y",db2jul($this->rapportageDatum).'-01-01'));
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
				$result_historischkostprijs = "";
				$result_voorgaandejaren = "";
				$result_lopendejaar = "";
			}

	//	listarray($mutaties);
				$data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
				if($mutaties['Credit'])
				  $data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				else
			  	$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
				$data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
				$data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
				$data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
				$data['totalen']['gerealiseerdResultaat']+=$result_lopendejaar;//($result_voorgaandejaren+$result_lopendejaar);
				$data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];


		}
		return $data;
	}

	function getRekeningMutaties($rekening,$van,$tot)
	{
	  $db= new DB();
	  $query = "
	  SELECT
  SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal
 	FROM
	Rekeningmutaties ,  Rekeningen

	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Rekening =  '$rekening'  AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '$van' AND
	Rekeningmutaties.Boekdatum <= '$tot'";

	  $db->SQL($query);
	  $db->Query();
	  $data = $db->nextRecord();
return $data['totaal'];
	}



		function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
		{
		  $DB=new DB();
		  $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query); //echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


	function fondsPerformance($fondsData,$totaal=false)
  {

    $datumBegin=$this->rapportageDatumVanaf;

    $weegDatum=$datumBegin;
    $datumEind=$this->rapportageDatum;

    global $__appvar;
	  $DB=new DB();
    $totaalPerf = 100;

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

      if ($this->pdf->rapportageValuta <> 'EUR')
      {
	      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	      $startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
	      $eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
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
      $bFilter=" AND Rekeningmutaties.Transactietype <> 'B' ";

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
               SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as liqWaarde,
               SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];

	     $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2/$eindValutaKoers  as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];
	     // listarray($fondsData);



	    if($beginwaarde == 0)
	    {
	      $query = "SELECT Rekeningmutaties.Boekdatum - INTERVAL 0 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE ($rekeningFondsenWhere OR $rekeningRekeningenWhere ) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum asc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $start = $DB->NextRecord();
	      if($start['Boekdatum'] != '')
	        $weegDatum = $start['Boekdatum'];

	    }

 	    if($eindwaarde == 0)
 	    {
 	      $query = "SELECT Rekeningmutaties.Boekdatum + INTERVAL 0 DAY as Boekdatum FROM  (Rekeningen, Portefeuilles)
	                LEFT JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening  WHERE ($rekeningFondsenWhere OR  $rekeningRekeningenWhere ) AND
	                Rekeningen.Portefeuille = '".$this->portefeuille."' AND	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
	                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' ORDER BY Rekeningmutaties.Boekdatum desc LIMIT 1 ";
	      $DB->SQL($query);
	      $DB->Query();
	      $eind = $DB->NextRecord();
	      if($eind['Boekdatum'] != '')
	        $datumEind = $eind['Boekdatum'];
 	    }
 
      
	     $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))$koersQuery)*-1 AS gewogen, ".
	              "SUM(((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQuery) AS totaal,
	              SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery)  AS storting,
	              SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1) $koersQuery) AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".//(Grootboekrekeningen.Opbrengst=0 AND Grootboekrekeningen.Kosten =0)
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere $bFilter";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery) AS totaal,
	              SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery)  AS opbrengstTotaal,
	              SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query();
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM((if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
       SUM((if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0))) as opbrengstTotaal ,
       SUM((if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0))) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
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
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere $bFilter";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
	     //listarray($AttributieStortingenOntrekkingen);


	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Kruispost=1 OR   Rekeningmutaties.Fonds <> ''  ) $bFilter";
	     $DB->SQL($query);
	     $DB->Query();
	     $data = $DB->nextRecord();

	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];


      $queryKostenOpbrengsten = "SELECT
          SUM((if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
          SUM((if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere $bFilter";
	     $DB->SQL($queryKostenOpbrengsten);
	     $DB->Query();
	     $nietToegerekendeKosten = $DB->NextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
	     //echo $rekeningRekeningenWhere; listarray($nietToegerekendeKosten);
       //listarray($AttributieStortingenOntrekkingen);
  
  //Get dividend
    $query="SELECT Boekdatum,sum(Debet*Valutakoers) as Debet,sum(Credit*valutakoers) as Credit,sum(Bedrag),Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$datumBegin."' AND
     Rekeningmutaties.Boekdatum <= '".	$datumEind."' AND
     $rekeningFondsenWhere AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    $directeOpbrengstArray = $DB->NextRecord();
    $directeOpbrengst=($directeOpbrengstArray['Credit']-$directeOpbrengstArray['Debet']);
    //echo $query;
    //listarray($directeOpbrengst);

      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] + $directeOpbrengst) / $gemiddelde) * 100;


      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
//listarray($mutatieData);
      if($totaal==true)
      {
        $this->totalen['gemiddeldeWaarde']=$gemiddelde;
      }

      
      $weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] + $directeOpbrengst;
      $bijdrage=$resultaat/$gemiddelde*$weging;
      
    //  echo " $rekeningRekeningenWhere ".round($performance,2)." <br>\n";
/*
      if($fondsData['fondsen'][0]=='3,5% NL 10-20')
      {
        echo "$queryAttributieStortingenOntrekkingen <br>\n";
        listarray($fondsData['fondsen']);
 echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."; <br>\n";
      }

     if($fondsData['rekeningen'][0]=='233512EUR')
     {

       listarray($fondsData['rekeningen']);
       echo "$queryAttributieStortingenOntrekkingenRekening <br>\n";

        echo "$gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."; <br>\n";
     }
*/
  return array(
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
	}
  
  function getDividend($fonds,$van,$tot)
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
  
    $DB = new DB();
    $totaal=0;
    $totaalCorrected=0;
    /*
    $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
    

    $DB->SQL($query);
    $DB->Query();
    $totaal=0;
    while($data = $DB->nextRecord())
    {
      if($data['type']=='rente')
        $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
      elseif($data['type']=='fondsen')
        $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
    }
    
    $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
    $totaalCorrected=$totaal;
    */
    
    $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum >= '".	$van."' AND
     Rekeningmutaties.Boekdatum <= '".	$tot."' AND
     Rekeningmutaties.Fonds='$fonds' AND
     Grootboekrekeningen.Opbrengst=1";
    $DB->SQL($query);
    $DB->Query();
    //echo "$query <br>\n";
    while($data = $DB->nextRecord())
    {
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      
      if($aantal[$boekdatum] > $aantal[$tot])
      {
        $aandeel=$aantal[$tot]/$aantal[$boekdatum];
      }
      // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
      $totaal+=($data['Credit']-$data['Debet']);
      $totaalCorrected+=(($data['Credit']-$data['Debet'])*$aandeel);
    }
    
    
    return array('totaal'=>$totaal,'corrected'=>$totaalCorrected);
  }


}
