<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:15 $
File Versie					: $Revision: 1.49 $

$Log: RapportVOLK_L33.php,v $
Revision 1.49  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.48  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.47  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.46  2018/01/31 17:21:26  rvv
*** empty log message ***

Revision 1.45  2017/04/15 19:11:50  rvv
*** empty log message ***

Revision 1.44  2017/01/04 16:22:50  rvv
*** empty log message ***

Revision 1.43  2016/06/29 16:02:20  rvv
*** empty log message ***

Revision 1.42  2016/06/25 16:57:02  rvv
*** empty log message ***

Revision 1.41  2015/08/30 11:44:35  rvv
*** empty log message ***

Revision 1.40  2014/11/30 13:19:33  rvv
*** empty log message ***

Revision 1.39  2014/11/23 14:13:22  rvv
*** empty log message ***

Revision 1.38  2014/11/12 16:50:49  rvv
*** empty log message ***

Revision 1.37  2013/08/28 16:02:50  rvv
*** empty log message ***

Revision 1.36  2013/07/28 09:59:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once("rapport/rapportATTberekening.php");

class RapportVOLK_L33
{
	function RapportVOLK_L33($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
	  $this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	  $this->db=new DB();
    $this->pdf->excelData[]=array("Naam",'Valuta','Aantal','Koers','Begin koers','Marktwaarde','Beginwaarde','Ongerealiseer resultaat','% portf.','Opgelopen rente');

  }

	function formatGetal($waarde, $dec)
	{
	  if($waarde==0)
	    return '';
	  else
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

  function formatAantal($waarde, $dec )
  {
    if($dec==0)
      return number_format($waarde,$dec,",",".");

    $getal = explode('.',$waarde);
    $decimaalDeel = $getal[1];
    if ($decimaalDeel != str_repeat('0',$dec))
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
  
  function gemiddeldeTransactieValutaKoers($fonds)
  {
    $valutaKoers=$this->pdf->ValutaKoersBegin;
    if($fonds=='')
      return $this->pdf->ValutaKoersBegin;
      
     $query="SELECT Boekdatum,Debet,Credit,Bedrag,Omschrijving ,((Credit*Valutakoers)-(Debet*Valutakoers)) as BedragEur,Transactietype
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND 
     Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND 
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' AND Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype NOT IN('V','L','A/S','V/S')";
     $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $totaalEur=0;
    $waardeRapportageKoers=0;
    while($data = $DB->nextRecord())
    { 
      if($data['Transactietype']=='B')
      {
        $tmp=fondsWaardeOpdatum($this->portefeuille,$fonds,$data['Boekdatum'],'EUR');
   			$bedrag = ($tmp['fondsEenheid'] * $tmp['totaalAantal']) * $tmp['beginwaardeLopendeJaar'] *  $tmp['beginwaardeValutaLopendeJaar'];
      }
      else
        $bedrag=abs($data['BedragEur']);
        
      $valutaKoers=getValutaKoers($this->pdf->rapportageValuta,$data['Boekdatum']);
      if($valutaKoers=='')
        $valutaKoers=$this->pdf->ValutaKoersBegin;
      //$waardeRapportageKoers+=($bedrag*$valutaKoers);
      $waardeRapportageKoers+=($bedrag/$valutaKoers);
      
      //echo "$fonds $bedrag*$valutaKoers=".($bedrag*$valutaKoers)."<br>\n";
      $totaalEur+=$bedrag;  
    }
    //$gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur;
    //echo "$fonds $gemiddeldeValutakoers=$waardeRapportageKoers/$totaalEur; <br>\n";
    $gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers;
   // echo "$fonds $gemiddeldeValutakoers=$totaalEur/$waardeRapportageKoers; <br>\n";

    if($gemiddeldeValutakoers <> 0)
      return $gemiddeldeValutakoers;
    else    
      return $valutaKoers;
  }


	function writeRapport()
	{
	  $this->pdf->rapport_type = "VOLK";
		$this->pdf->rapport_titel = "Vermogensoverzicht (met beginkoers)";
		$this->vastWhere='';
	  $this->rapport();

	  if($this->pdf->lastPOST['vastrentend'])
		{
		  $this->vastRentend=true;
		  $this->pdf->rapport_type = "VOLKV";
		  $this->pdf->rapport_titel = "Overzicht vastrentende portefeuille";
		  $this->vastWhere=" AND ( TijdelijkeRapportage.hoofdcategorie='G-RISM' OR Fondsen.Lossingsdatum <> '0000-00-00')";
		  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		  $this->cashfow->genereerTransacties();
		  $this->cashfow->genereerRows();
      $this->rapport();
		}

	}

	function rapport()
	{
		global $__appvar;
    global $USR;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

				$query="SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM
				TijdelijkeRapportage
				Left Join Fondsen ON Fondsen.Fonds = TijdelijkeRapportage.Fonds WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."' ".$this->vastWhere.$__appvar['TijdelijkeRapportageMaakUniek']."";
		$DB->SQL($query); //echo $query."<br>\n";
		$DB->Query();
		$actueleWaarde = $DB->nextRecord();
		$portefeuilleWaarde=$actueleWaarde['actuelePortefeuilleWaardeEuro'];


		$this->pdf->AddPage();
		$this->pdf->templateVars['VOLKPaginas'] = $this->pdf->customPageNo;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		unset($this->pdf->CellBorders);
//SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta) AS historischeWaardeEuro,
	


$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
	  $query="SELECT
	  hoofdcategorien.Omschrijving AS HcategorieOmschrijving,
TijdelijkeRapportage.historischeValutakoers,
TijdelijkeRapportage.beginwaardeValutaLopendeJaar,
TijdelijkeRapportage.historischeRapportageValutakoers,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(beginPortefeuilleWaardeEuro),0 )) AS beginPortefeuilleWaardeEuro,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.beginwaardeLopendeJaar,0))  as beginwaardeLopendeJaar,
SUM(IF(TijdelijkeRapportage.type = 'fondsen',TijdelijkeRapportage.historischeWaarde,0)) as historischeWaarde,
SUM(IF(TijdelijkeRapportage.type = 'rente' , (actuelePortefeuilleWaardeEuro),0)) / ".$this->pdf->ValutaKoersEind." AS rente,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro ,
 SUM(IF(TijdelijkeRapportage.type = 'fondsen',(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.actueleValuta),0
 )) AS historischeWaardeEuro,
sum(IF(TijdelijkeRapportage.type = 'rekening' ,actuelePortefeuilleWaardeInValuta, IF(TijdelijkeRapportage.type = 'fondsen' ,totaalAantal, 0))) as totaalAantal, 
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.fonds,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.actueleValuta,
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.rekening,
TijdelijkeRapportage.beleggingscategorie,
Beleggingscategorien.Afdrukvolgorde,
TijdelijkeRapportage.type,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
rekeningBank.Omschrijving as depotbankOmschrijving,
if(TijdelijkeRapportage.type='Rekening',TijdelijkeRapportage.valuta,TijdelijkeRapportage.fondsOmschrijving) as volgordeOmschrijving
FROM
TijdelijkeRapportage
LEFT Join Beleggingscategorien ON TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON TijdelijkeRapportage.beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.vermogensbeheerder='$beheerder'
LEFT Join Beleggingscategorien  as hoofdcategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorien.Beleggingscategorie
LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.rekening AND Rekeningen.portefeuille='".$this->portefeuille."'
LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
WHERE
TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND TijdelijkeRapportage.portefeuille='".$this->portefeuille."'
".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
TijdelijkeRapportage.fonds,TijdelijkeRapportage.rekening
ORDER BY 	Beleggingscategorien.Afdrukvolgorde, depotbankOmschrijving, TijdelijkeRapportage.rekening, volgordeOmschrijving	";
		$DB->SQL($query);
		$DB->Query();
		$DB2=new DB();
    while($data = $DB->nextRecord())
    {
      
    //    		if($categorien['valuta'] == $this->pdf->rapportageValuta)
		//	  $beginQuery = 'beginwaardeValutaLopendeJaar';
		//	else
		//	  $beginQuery = 'historischeRapportageValutakoers';
//      if($data['valuta'] == $this->pdf->rapportageValuta)
//			  $data['beginPortefeuilleWaardeEuro'] = $data['beginPortefeuilleWaardeEuro']  / $data['historischeValutakoers'];
//			else
//listarray($data);
//listarray($this->gemiddeldeTransactieValutaKoers($data['fonds']));

        
        
      if($data['valuta'] == $this->pdf->rapportageValuta)
      {
			  $data['beginPortefeuilleWaardeEuro'] = $data['beginPortefeuilleWaardeEuro']  / $data['beginwaardeValutaLopendeJaar'];
			}
      elseif($this->pdf->rapportageValuta <> '' && $this->pdf->rapportageValuta <> 'EUR' )
      {
        //echo "VOLK ".$data['fonds']." ".$data['beginPortefeuilleWaardeEuro']." / ".$this->gemiddeldeTransactieValutaKoers($data['fonds'])."<br>\n";
        $data['beginPortefeuilleWaardeEuro'] = $data['beginPortefeuilleWaardeEuro'] / $this->gemiddeldeTransactieValutaKoers($data['fonds']);
        //echo "= ".$data['beginPortefeuilleWaardeEuro']."<br>\n";ob_flush();
      }
      else
      {
			  $data['beginPortefeuilleWaardeEuro'] = $data['beginPortefeuilleWaardeEuro'] / $this->pdf->ValutaKoersBegin;
      }
      
      
      

//echo $data['fonds']." -> ".$this->pdf->ValutaKoersEind." ".$data['actuelePortefeuilleWaardeEuro']." <br>\n";        
//echo $data['fonds']." -> ".$data['beginPortefeuilleWaardeEuro']." = ".$data['beginPortefeuilleWaardeEuro']."  / ".$data['historischeValutakoers']."<br>\n";        
      if($_POST['anoniem'] !=1 && $data['rekening'] <> '')
      {
        //$data['fondsOmschrijving'].=' '.substr($data['rekening'],0,strlen($data['rekening'])-3);
        $query="SELECT Rekeningen.Rekening,Rekeningen.Valuta,
                if(Rekeningen.Depotbank <> '',rekeningBank.Omschrijving, Depotbanken.Omschrijving) as Omschrijving
                FROM
                Rekeningen
                Inner Join Portefeuilles ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
                Inner Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
                LEFT Join Depotbanken as rekeningBank ON Rekeningen.Depotbank = rekeningBank.Depotbank
                WHERE Rekeningen.Rekening='".$data['rekening']."' AND Portefeuilles.Portefeuille <> 'C_$USR'";
        $DB2->SQL($query);
		    $depot=$DB2->lookupRecord();
		    $data['fondsOmschrijving'] = $depot['Omschrijving'].' '.substr($data['rekening'],0,strlen($data['rekening'])-3);


      }
      $Hcategorie=$data['HcategorieOmschrijving'];

      $data['actuelePortefeuilleWaardeEuro']=$data['actuelePortefeuilleWaardeEuro']-$data['rente'];
      if($data['type']=='rekening' || ($data['type']=='rente' && $data['rekening'] <> '') )
        $ongerealiseerdResultaat=0;
      else
        $ongerealiseerdResultaat=$data['actuelePortefeuilleWaardeEuro']-$data['beginPortefeuilleWaardeEuro'];

      $aandeel=$data['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde*100;

      $totalenCat[$data['categorieOmschrijving']]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenCat[$data['categorieOmschrijving']]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenCat[$data['categorieOmschrijving']]['aandeel'] += $aandeel;

      $totalenHcat[$Hcategorie]['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['historischeWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalenHcat[$Hcategorie]['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalenHcat[$Hcategorie]['aandeel'] += $aandeel;

      $totalen['actuelePortefeuilleWaardeEuro'] += $data['actuelePortefeuilleWaardeEuro'];
      $totalen['beginPortefeuilleWaardeEuro'] += $data['beginPortefeuilleWaardeEuro'];
      $totalen['ongerealiseerdResultaat'] += $ongerealiseerdResultaat;
      $totalen['aandeel'] += $aandeel;

      if($data['categorieOmschrijving'] <> $lastcategorieOmschrijving)
      {
        if(!empty($lastcategorieOmschrijving))
        {
          if($totalen['rente'] <> 0)
          {
            $renteAandeel=$totalen['rente']/$portefeuilleWaarde*100;
            $totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalenCat[$lastcategorieOmschrijving]['aandeel'] += $renteAandeel;
            $totalen['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalen['aandeel'] += $renteAandeel;
            $totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
            $totalenHcat[$lastHcategorie]['aandeel'] += $renteAandeel;
            $this->pdf->ln(2.5);
            $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
            $this->pdf->row(array('  '.vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','', '',$this->formatGetal( $totalen['rente'],0),'','',$this->formatGetal($renteAandeel ,1),''));
            $this->pdf->excelData[]=array(vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','',round( $totalen['rente'],0),'','',round($renteAandeel ,1),'');
            $totalen['rente']=0;
          }

          $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
          $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],0),
          $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1),''));
          unset($this->pdf->CellBorders);
        }
        if($this->pdf->getY() > 185)
          $this->pdf->addPage();

        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

        if($Hcategorie <> $lastHcategorie)
        {
          if(!empty($lastHcategorie))
          {
            $this->pdf->ln(5);
            $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
            $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['beginPortefeuilleWaardeEuro'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['ongerealiseerdResultaat'],0),
            $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],1),''));
            unset($this->pdf->CellBorders);
            $this->pdf->ln(10);
          }
          if($this->pdf->getY() > 185)
            $this->pdf->addPage();
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->row(array(vertaalTekst($Hcategorie,$this->pdf->rapport_taal)));
          $this->pdf->ln(2);
        }
        $lastHcategorie=$Hcategorie;

        $this->pdf->row(array('  '.vertaalTekst($data['categorieOmschrijving'],$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      $totalen['rente'] += $data['rente'];
      if($data['type']=='rekening')
        $afronding=0;
      else
        $afronding=6;
      $this->pdf->row(array(
        '  '.$data['fondsOmschrijving'],
        $data['valuta'],
        $this->formatAantal($data['totaalAantal'],$afronding),
        $this->formatGetal($data['actueleFonds'],2),
        $this->formatGetal($data['beginwaardeLopendeJaar'],2),
      '',$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($data['beginPortefeuilleWaardeEuro'],0),
      $this->formatGetal($ongerealiseerdResultaat,0),
      $this->formatGetal($aandeel,1),
      $this->formatGetal($data['rente'],0)));
      
      $this->pdf->excelData[]=array($data['fondsOmschrijving'],$data['valuta'],$data['totaalAantal'],round($data['actueleFonds'],2),round($data['beginwaardeLopendeJaar'],2),round($data['actuelePortefeuilleWaardeEuro'],0),round($data['beginPortefeuilleWaardeEuro'],0),round($ongerealiseerdResultaat,0),round($aandeel,1),round($data['rente'],0));
      $lastcategorieOmschrijving=$data['categorieOmschrijving'];
    }

    if(!empty($lastcategorieOmschrijving))
    {
      if($totalen['rente'] <> 0)
      {
        $renteAandeel=$totalen['rente']/$portefeuilleWaarde*100;
        $totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalenCat[$lastcategorieOmschrijving]['aandeel'] += $renteAandeel;
        $totalen['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalen['aandeel'] += $renteAandeel;
        $totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'] += $totalen['rente'];
        $totalenHcat[$lastHcategorie]['aandeel'] += $renteAandeel;
        $this->pdf->ln(2.5);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('  '.vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','', '',$this->formatGetal( $totalen['rente'],0),'','',$this->formatGetal($renteAandeel ,1),''));
        $this->pdf->excelData[]=array(vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal),'','','','',round( $totalen['rente'],0),'','',round($renteAandeel ,1),'');
        $totalen['rente']=0;
      }

      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      $this->pdf->row(array('','','','','','',$this->formatGetal($totalenCat[$lastcategorieOmschrijving]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['beginPortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['ongerealiseerdResultaat'],0),
      $this->formatGetal($totalenCat[$lastcategorieOmschrijving]['aandeel'],1),''));
      unset($this->pdf->CellBorders);
    }

    if(!empty($lastHcategorie))
    {
      $this->pdf->ln(5);
      $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastHcategorie,$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalenHcat[$lastHcategorie]['actuelePortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['beginPortefeuilleWaardeEuro'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['ongerealiseerdResultaat'],0),
      $this->formatGetal($totalenHcat[$lastHcategorie]['aandeel'],1),''));
      $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
      unset($this->pdf->CellBorders);
      $this->pdf->ln(10);
      if($this->pdf->getY() > 185)
        $this->pdf->addPage();
    }

    $this->pdf->CellBorders = array('','','','','','','T','T','T','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);
    $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),'','','','','',$this->formatGetal($totalen['actuelePortefeuilleWaardeEuro'],0),
    $this->formatGetal($totalen['beginPortefeuilleWaardeEuro'],0),
    $this->formatGetal($totalen['ongerealiseerdResultaat'],0),
    $this->formatGetal($totalen['aandeel'],1),''));
    unset($this->pdf->CellBorders);


    $this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    $this->pdf->Line($this->pdf->pageTop[0],$this->pdf->pageTop[1],$this->pdf->pageTop[0],$this->pdf->GetY());

    //$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);

		$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);

		//printSamenstellingResultaat_L33($this->pdf,$this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		//printAEXVergelijking_L33($this->pdf,$this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);

		

		$this->pdf->pageBottom=array($this->pdf->pageTop[0],$this->pdf->GetY());
    if($this->pdf->getY() > 185)
    {
      $this->pdf->addPage();
      unset($this->pdf->pageTop);
    }

    $this->pdf->SetWidths(array(280));
    $this->pdf->setY(190);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-1);
    $rowHeight=$this->pdf->rowHeight;
    $this->pdf->rowHeight=3.0;
    if($this->pdf->rapport_taal==1)
		  $this->pdf->row(array('The prices used in this report are all from sources that are deemed reliable. To be able to report as quickly as possible, especially at quarter end, we will use estimates of valuations, whenever a definitive valuation is not available. As soon as a final valuation is available, this value will be entered in our systems. As a result, the value of the investment portfolio and the calculated performance may, especially at quarter end, be slightly different in subsequent quarterly reports.'));
    else
		  $this->pdf->row(array('De in deze rapportage gebruikte effectenkoersen worden verkregen uit door ons betrouwbaar geachte bronnen. Omwille van de snelheid van rapporteren kan, met name op de kwartaalultimo, gebruik gemaakt worden van schatting van koersen, daar waar niet direct na de kwartaalultimo een definitieve koers beschikbaar is. Zodra de definitieve koers beschikbaar is, zal deze in onze systemen worden ingevoerd. Derhalve kan de waarde van de effectenportefeuille en daarmee ook het berekende beleggingsresultaat, met name per kwartaalultimo, in de opeenvolgende kwartaalrapportages enigszins van elkaar verschillen.'));
    $this->pdf->rowHeight=$rowHeight;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }
}
?>