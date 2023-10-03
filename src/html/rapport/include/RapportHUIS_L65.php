<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/03 15:52:48 $
File Versie					: $Revision: 1.10 $

$Log: RapportHUIS_L65.php,v $
Revision 1.10  2019/04/03 15:52:48  rvv
*** empty log message ***

Revision 1.9  2019/03/31 12:19:56  rvv
*** empty log message ***

Revision 1.8  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.7  2019/01/09 15:52:19  rvv
*** empty log message ***

Revision 1.6  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.5  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.4  2018/11/17 17:34:53  rvv
*** empty log message ***

Revision 1.3  2018/11/16 16:41:32  rvv
*** empty log message ***

Revision 1.2  2018/10/21 09:42:37  rvv
*** empty log message ***

Revision 1.1  2018/10/20 18:05:20  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportVOLK_L65.php");


class RapportHUIS_L65
{
	function RapportHUIS_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_titel = vertaalTekst("Portefeuilledetails*",$this->pdf->rapport_taal);
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }
  
	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


		$portefeuilles=array();
		$query = "SELECT Fondsen.Portefeuille,
              Portefeuilles.Startdatum,
              Portefeuilles.Einddatum,
              Fondsen.Omschrijving,
              TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
              FondsenBuitenBeheerfee.layoutNr
              FROM TijdelijkeRapportage
JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds
JOIN Fondsen ON FondsenBuitenBeheerfee.Fonds = Fondsen.Fonds
JOIN Portefeuilles ON Fondsen.Portefeuille = Portefeuilles.Portefeuille
              WHERE FondsenBuitenBeheerfee.Huisfonds = 1 AND rapportageDatum ='".$this->rapportageDatum."' AND
              TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' "
						  .$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
 	  {
      if($data['Portefeuille']<>'')
		    $portefeuilles[$data['Portefeuille']]=$data;
    }

    $kopBackup=$this->pdf->rapport_koptext;
    $blokken=array();
    foreach($portefeuilles as $portefeuille=>$pdata)
    {
      $rapportageDatum['a'] = date("Y-m-d",$this->pdf->rapport_datumvanaf); 
      $rapportageDatum['b'] = date("Y-m-d",$this->pdf->rapport_datum);

	    if($this->pdf->rapport_datumvanaf < db2jul($pdata['Startdatum']))
	      $rapportageDatum['a'] = $pdata['Startdatum'];
	  
  	  if($this->pdf->rapport_datum > db2jul($pdata['Einddatum']))
  	  {
	    	echo "<b>Fout: Portefeille '$portefeuille' heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
	  	  exit;
	    }
    	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	    {
	    	echo "<b>Fout: $portefeuille Van datum kan niet groter zijn dan  T/m datum! </b>";
		    exit;
	    }

     
      $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],0,$pdata['RapportageValuta'],$rapportageDatum['a']);
      vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
      $portefeuilleWaarde=0;
      $totaleVerdeling=array();
      $categorieVerdeling=array();
      $categorieVolgorde=array();
      $categorieOmschrijving=array();
      $categorieTotaal=array();
      
      
      foreach($fondswaarden['b'] as $fonds)
      {
        
        if($fonds['type']=='rente')
        {
          $fonds['fondsOmschrijving']=vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal);
          $fonds['beleggingscategorie']='Opgelopen rente';
          $fonds['beleggingscategorieOmschrijving']=vertaalTekst('Opgelopen rente',$this->pdf->rapport_taal);
          $fonds['beleggingscategorieVolgorde']=100;
        }
        elseif($fonds['type']=='rekening')
        {
          $fonds['fondsOmschrijving']=vertaalTekst('Cash positie',$this->pdf->rapport_taal);
        }

          $portefeuilleWaarde += $fonds['actuelePortefeuilleWaardeEuro'];
          $totaleVerdeling[$fonds['fondsOmschrijving']] += $fonds['actuelePortefeuilleWaardeEuro'];
          $categorieVerdeling[$fonds['beleggingscategorie']][$fonds['fondsOmschrijving']] += $fonds['actuelePortefeuilleWaardeEuro'];
          $categorieTotaal[$fonds['beleggingscategorie']] += $fonds['actuelePortefeuilleWaardeEuro'];
          $categorieVolgorde[$fonds['beleggingscategorieVolgorde']] = $fonds['beleggingscategorie'];
          $categorieOmschrijving[$fonds['beleggingscategorie']] = $fonds['beleggingscategorieOmschrijving'];
      
      }
  
      arsort($totaleVerdeling);
      
			$aandeelVanPortefeuille=$pdata['actuelePortefeuilleWaardeEuro']/$portefeuilleWaarde;
			if($aandeelVanPortefeuille <>0)
			{
				$kop=array();
        $rows=array();
        $totaal=array();
        if($pdata['layoutNr']==1)
        {
  
          $kop = array(array(vertaalTekst('De 20 grootste posities',$this->pdf->rapport_taal)), array('', vertaalTekst('Aandeel fonds',$this->pdf->rapport_taal), vertaalTekst('Waarde',$this->pdf->rapport_taal)));
          $n = 0;
          $line = 0;
          foreach ($totaleVerdeling as $fonds => $waarde)
          {
            if ($n > 20)
            {
              $fonds = vertaalTekst('Overig',$this->pdf->rapport_taal);
            }
            $aandeelFonds = $waarde / $portefeuilleWaarde;
            $waardeFonds = $waarde * $aandeelVanPortefeuille;
            $rows[$line][0] = $fonds;
            $rows[$line][1] += $aandeelFonds;
            $rows[$line][2] += $waardeFonds;
            $totaal['aandeelFonds'] += $aandeelFonds;
            $totaal['waardeFonds'] += $waardeFonds;
            if ($n<21)
              $line++;
            $n++;
          }
        }
        if($pdata['layoutNr']==2)
        {
            ksort($categorieVolgorde);
            $kop = array(array(vertaalTekst('De grootste posities per categorie',$this->pdf->rapport_taal)), array('', vertaalTekst('Aandeel fonds',$this->pdf->rapport_taal), vertaalTekst('Waarde',$this->pdf->rapport_taal)));

            $line = 0;
            foreach ($categorieVolgorde as $volgorde => $categorie)
            {
            	$n=0;
            	if($line>0)
                $line++;
              $rows[$line]=array(vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal),$categorieTotaal[$categorie]/$portefeuilleWaarde,$categorieTotaal[$categorie]*$aandeelVanPortefeuille,'categorieTotaal');
              $line++;
              ksort($categorieVerdeling[$categorie]);
              foreach($categorieVerdeling[$categorie] as $fonds=>$waarde)
              {
                if ($n>1)
                {
                  $fonds = vertaalTekst('Overig',$this->pdf->rapport_taal);
                }
             
                $aandeelFonds = $waarde / $portefeuilleWaarde;
                $waardeFonds = $waarde * $aandeelVanPortefeuille;
                $rows[$line][0] = " - ".$fonds;
                $rows[$line][1] += $aandeelFonds;
                $rows[$line][2] += $waardeFonds;
  
                //echo "$line | $n | $categorie | $fonds | ".round($aandeelFonds,4)." | ".round($rows[$line][1],4)." <br>\n";
                
                $totaal['aandeelFonds'] += $aandeelFonds;
                $totaal['waardeFonds'] += $waardeFonds;
                if ($n<2)
                  $line++;

                $n++;
              }
            }
           // listarray($rows);
          }
          $blokken[]=array('omschrijving'=>$pdata['Omschrijving'],'layout'=>$pdata['layoutNr'],'kop'=>$kop,'body'=>$rows,'totaal'=>$totaal);
        }
    }
    //$blokken[2]=$blokken[0];
    //$blokken[3]=$blokken[1];
    
		$paginas=array();
    $n=0;
    foreach($blokken as $blok)
		{
      $paginas[$n][]=$blok;
      if(count($paginas[$n])==3)
        $n++;
		}


		foreach($paginas as $paginanr=>$blokken)
    {
      $this->pdf->addPage();
      if($paginanr==0)
      {
        $this->pdf->templateVars[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->page;
        $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type . 'Paginas'] = $this->pdf->rapport_titel;
      }
      $xwidth = 297 - $this->pdf->marge * 2;
//      $tabelWidth = $xwidth / 3;
//echo $tabelWidth;exit; //93.66
      if (count($blokken) < 4)
      {
        if (count($blokken) == 1)
        {
          $xStart = $xwidth / 2 - 90 / 2;
        }
				elseif (count($blokken) == 2)
        {
          $xStart = $xwidth / 3 - 90 / 2;
        }
        else
        {
          $xStart = $xwidth / 6 - 90 / 2;
        }
        $n = 0;
        foreach ($blokken as $blokData)
        {
          $this->toonBlok($xStart + $n * 94, 50, $blokData);
          $n++;
        }
        
      }
    }
  
    $this->pdf->rapport_koptext=$kopBackup;
    
	}
	
	function toonBlok($x,$y,$blockData)
	{
    
    $this->pdf->SetXY($x+$this->pdf->marge,34);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize+2);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->MultiCell(90,4,$blockData['omschrijving'],0,'C');
    $this->pdf->SetTextColor(0);
	  $this->pdf->SetXY($this->pdf->marge,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);

    $this->pdf->setAligns(array($x,'L','R','R'));
    foreach($blockData['kop'] as $i=>$row)
    {
    	if($i==0)
        $this->pdf->setWidths(array($x,55+15,20));
    	else
      {
        $this->pdf->setWidths(array($x, 60, 15, 15));
      }
    	$this->pdf->row(array('', $row[0], $row[1], $row[2]));
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array($x,60,15,15));
    foreach($blockData['body'] as $row)
    {
      if(isset($row[3]) && $row[3]=='categorieTotaal')
        $this->pdf->SetFont($this->pdf->rapport_font, 'BI', $this->pdf->rapport_fontsize);
      else
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  
      $fondsOmschrijving=$row[0];
      $width=$this->pdf->GetStringWidth($fondsOmschrijving);
      if($width>58)
      {
        for($i=strlen($fondsOmschrijving);$i>0;$i--)
        {
          $newOmschschrijving = substr($row[0], 0, $i).'...';
          $width=$this->pdf->GetStringWidth($newOmschschrijving);
          if($width<58)
          {
            $row[0]=$newOmschschrijving;
            break;
          }
        }
      }
      $this->pdf->row(array('', $row[0], $this->formatGetal($row[1] * 100, 2) . '%', $this->formatGetal($row[2], 0)));
    }
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->formatGetal($blockData['totaal']['aandeelFonds']* 100,2).'%',$this->formatGetal($blockData['totaal']['waardeFonds'],0)));
	}

}
?>
