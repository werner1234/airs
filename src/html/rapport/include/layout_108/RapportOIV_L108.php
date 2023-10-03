<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.3 $

$Log: RapportOIB_L51.php,v $
Revision 1.3  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.2  2014/07/06 12:38:11  rvv
*** empty log message ***

Revision 1.1  2013/09/18 15:23:07  rvv
*** empty log message ***

Revision 1.17  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:23:35  rvv
*** empty log message ***

Revision 1.15  2013/08/10 15:48:01  rvv
*** empty log message ***

Revision 1.14  2013/07/28 09:59:15  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportOIV_L108
{
  
  function RapportOIV_L108($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "OIV";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
    $this->pdf->rapport_titel = "Maandelijks overzicht ".date(' m-Y',$this->pdf->rapport_datum);
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->jaarGeleden=date("Y-m-d",mktime(0,0,0,date('m',$this->pdf->rapport_datum),date('d',$this->pdf->rapport_datum),date('Y',$this->pdf->rapport_datum)-1));
    
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
    global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $DB = new DB();

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(20,160,20,100));
    $this->pdf->SetAligns(array('L','L','R','L'));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam'],'Email:',$this->pdf->portefeuilledata['email']));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Land']));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(40,120,30,100));
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->row(array('Investeerders ID',$this->pdf->portefeuilledata['Portefeuille']));
    $this->pdf->row(array('Investeerders naam',$this->pdf->portefeuilledata['Naam']));
    $this->pdf->ln();
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->underlinePercentage=0.8;
    
    //$header=array('ultimo','Begin aantal','Begin Koers','Waarde','Stortingen / Onttrekkingen EUR','Waarde Part. ultimo','Aantal Stortingen / Onttrekkingen','Aantal ultimo','Waarde ultimo','Beleggingsresultaat','Rendement Mnd','Rendement Cum');
    $header=array('Portefeuille','','Participaties','Koers waarde','Markt waarde');
//    $widths=array(20,22,22,22,25,25,25,25,25,25,22,22);
		$widths=array(60,35,35,35,35);
    $aligns=array('L','R','R','R','R');
    //$headerBorders=array('U','U','U','U','U','U','U','U','U','U','U','U');
    $headerBorders=array(array('L','T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
    //$this->pdf->setY(25);
    $this->pdf->setWidths($widths);
    $this->pdf->SetAligns($aligns);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $index=new indexHerberekening();
    $maanden=$index->getJaren($this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum);
    //listarray($maanden);exit;
    $cumulatief=0;
    $barGraph=array();
    $aanwezigeCategorieen=array();
    
    foreach($maanden as $periode)
    {
      //function berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum='',$afronding=2,$bewaarders=false)
      $fondsenDatabegin=berekenPortefeuilleWaarde($this->portefeuille,$periode['start'],(substr($periode['start'], 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$periode['start']);
      $fondsenDataEind=berekenPortefeuilleWaarde($this->portefeuille,$periode['stop'],(substr($periode['stop'], 5, 5) == '01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$periode['start']);
      $fondsenData=array();
      foreach($fondsenDatabegin as $fondsRegel)
      {
        $fondsenData[$fondsRegel['fonds']]['start']=$fondsRegel;
      }
      foreach($fondsenDataEind as $fondsRegel)
      {
        $fondsenData[$fondsRegel['fonds']]['stop']=$fondsRegel;
      }
      $rendement=$index->BerekenMutaties2($periode['start'],$periode['stop'],$this->portefeuille,$this->pdf->portefeuilledata['RapportageValuta']);
      $cumulatief=((1+$cumulatief/100)*(1+$rendement['performance']/100)-1)*100;
      
      foreach($fondsenData as $fondsData)
      {
        $fonds='';
        if(isset($fondsData['start']['fonds']))
          $fonds=$fondsData['start']['fonds'];
        elseif(isset($fondsData['stop']['fonds']))
          $fonds=$fondsData['stop']['fonds'];
        
        $query="SELECT sum(Rekeningmutaties.Bedrag) as Bedrag,
sum(Rekeningmutaties.Aantal) as Aantal,
sum(if(Rekeningmutaties.Aantal>0,Rekeningmutaties.Aantal,0)) as storting,
sum(if(Rekeningmutaties.Aantal<0,Rekeningmutaties.Aantal,0)) as onttrekking
FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$periode['start']."' AND Rekeningmutaties.Boekdatum<='".$periode['stop']."' AND
Rekeningmutaties.Fonds='".mysql_real_escape_string($fonds)."' AND Rekeningmutaties.transactieType IN('A','V','D','L')";
        $DB->SQL($query);
        $DB->Query();
        $storting = $DB->LookupRecord();
        //listarray($storting);
        $query="SELECT ISINCode,Omschrijving FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."'";
        $DB->SQL($query);
        $DB->Query();
        $fondsQueryData = $DB->LookupRecord();
        if($fondsQueryData['Omschrijving']=='')
          $fondsQueryData['Omschrijving']=$fondsData['stop']['fondsOmschrijving'];
  
        $this->pdf->setWidths(array(150));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array($fondsQueryData['Omschrijving'].', ISIN:'.$fondsQueryData['ISINCode']));
        $this->pdf->Row(array('Marktwaarde in:'.$this->pdf->portefeuilledata['RapportageValuta']));
        $this->pdf->setWidths($widths);
  
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders=$headerBorders;
        $this->pdf->row($header);
        unset($this->pdf->CellBorders);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->Row(array('Beginwaarde',date('d-M-Y',db2jul($periode['start'])),$this->formatGetal($fondsData['start']['totaalAantal'],4),$this->formatGetal($fondsData['start']['actueleFonds'],2),$this->formatGetal($fondsData['start']['actuelePortefeuilleWaardeEuro'],2)));
        $this->pdf->ln(2);
        $this->pdf->Row(array('Stortingen in de maand','',$this->formatGetal($storting['storting'],4)));
        $this->pdf->Row(array('Onttrekkingen in de maand','',$this->formatGetal($storting['onttrekking'],4)));
        $this->pdf->ln(2);
        $this->pdf->Row(array('Eindwaarde',date('d-M-Y',db2jul($periode['stop'])),$this->formatGetal($fondsData['stop']['totaalAantal'],4),$this->formatGetal($fondsData['stop']['actueleFonds'],2),$this->formatGetal($fondsData['stop']['actuelePortefeuilleWaardeEuro'],2)));
  
        if(count($fondsenData)>1)
        {
          if(round($fondsData['start']['actuelePortefeuilleWaardeEuro'], 2)==0)
          {
            $rendement = ($fondsData['stop']['actuelePortefeuilleWaardeEuro'] - $fondsData['start']['actuelePortefeuilleWaardeEuro'] + $storting['Bedrag']) / round($fondsData['start']['actuelePortefeuilleWaardeEuro'] - $storting['Bedrag'], 2) * 100;
           // echo $fondsQueryData['Omschrijving']." | $rendement = (".$fondsData['stop']['actuelePortefeuilleWaardeEuro']." - ".$fondsData['start']['actuelePortefeuilleWaardeEuro']." + ".$storting['Bedrag'].") / round(".$fondsData['start']['actuelePortefeuilleWaardeEuro']." - ".$storting['Bedrag'].", 2) * 100; <br>\n";
          }
          else
            $rendement = ($fondsData['stop']['actuelePortefeuilleWaardeEuro'] - $fondsData['start']['actuelePortefeuilleWaardeEuro'] + $storting['Bedrag']) / round($fondsData['start']['actuelePortefeuilleWaardeEuro'], 2) * 100;
        }
        else
        {
          $rendement = $rendement['performance'];
        }
        //echo $fondsQueryData['Omschrijving']." $rendement=(".$fondsData['stop']['actuelePortefeuilleWaardeEuro']."-".$fondsData['start']['actuelePortefeuilleWaardeEuro']."+".$storting['Bedrag'].")/".$fondsData['start']['actuelePortefeuilleWaardeEuro']."*100;<br>\n";
        $this->pdf->Row(array('Waarde verandering in %:','','','',$this->formatGetal($rendement,2)."%"));//$rendement['performance']
        $this->pdf->ln(10);
        //echo $query."<br><br>";
				/*
        $this->pdf->Row(array($periode['stop'],
                          $this->formatGetal($fondsData['start']['totaalAantal'],4),
                          $this->formatGetal($fondsData['start']['actueleFonds'],2),
                          $this->formatGetal($fondsData['start']['actuelePortefeuilleWaardeEuro'],2),
                          $this->formatGetal($storting['Bedrag']*-1,2),
                          $this->formatGetal($fondsData['stop']['actueleFonds'],2),
                          $this->formatGetal($storting['Aantal'],4),
                          $this->formatGetal($fondsData['stop']['totaalAantal'],4),
                          $this->formatGetal($fondsData['stop']['actuelePortefeuilleWaardeEuro'],2),
                          $this->formatGetal($fondsData['stop']['actuelePortefeuilleWaardeEuro']-$fondsData['start']['actuelePortefeuilleWaardeEuro']+$storting['Bedrag'],2),
                          $this->formatGetal($rendement['performance'],2)."%",
                          $this->formatGetal($cumulatief,2)."%"
                        )
				
        );
				*/
      }
//listarray($fondsData);
    }
  
    if($this->pdf->getY()<$this->pdf->h-30)
      $this->pdf->setY($this->pdf->h-30);
    $marge=($this->pdf->w-150-$this->pdf->marge)/2;
    $this->pdf->setWidths(array($marge,150,$marge));
    $this->pdf->SetAligns(array('L','C'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('','Mocht u vragen hebben naar aanleiding van dit bericht, verzoeken wij u contact op te nemen met Cedar Accountancy & Belastingadvies B.V. per e-mail naar: investors@cederadministratie.nl'));
    

  }
  
  
}
?>