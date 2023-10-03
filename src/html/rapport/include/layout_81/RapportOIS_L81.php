<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/27 18:03:24 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIS_L81.php,v $
Revision 1.2  2019/07/27 18:03:24  rvv
*** empty log message ***

Revision 1.1  2019/07/06 15:43:06  rvv
*** empty log message ***

Revision 1.1  2018/12/08 18:28:30  rvv
*** empty log message ***

Revision 1.35  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.34  2010/06/30 16:10:10  rvv
*** empty log message ***

Revision 1.33  2010/06/02 09:13:01  rvv
*** empty log message ***

Revision 1.32  2009/11/20 09:37:51  rvv
*** empty log message ***

Revision 1.31  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.30  2008/06/30 07:58:44  rvv
*** empty log message ***

Revision 1.29  2007/10/04 11:57:04  rvv
*** empty log message ***

Revision 1.28  2007/06/29 12:16:31  rvv
*** empty log message ***

Revision 1.27  2007/06/29 11:38:56  rvv
L14 aanpassingen

Revision 1.26  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.25  2007/02/21 11:04:26  rvv
Client toevoeging

Revision 1.24  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.23  2006/11/27 13:33:02  rvv
Sortering werkt nu ook met eigen kleuren.

Revision 1.22  2006/11/27 09:27:15  rvv
grafiekkleuren uit vermogensbeheerder check

Revision 1.21  2006/11/10 11:56:12  rvv
Eigen kleuren aanpassing/toevoeging

Revision 1.20  2006/11/03 11:24:04  rvv
Na user update

Revision 1.19  2006/10/31 12:06:45  rvv
Voor user update

Revision 1.18  2006/10/20 14:55:53  rvv
*** empty log message ***

Revision 1.17  2006/05/09 07:48:11  jwellner
- afronding fondsaantal
- afronding controle bij afdrukken rapporten
- sorteren frontoffice selectie

Revision 1.16  2006/04/12 07:54:47  jwellner
*** empty log message ***

Revision 1.15  2005/12/19 13:23:27  jwellner
no message

Revision 1.14  2005/11/30 08:37:39  jwellner
layout stuff

Revision 1.13  2005/11/25 09:30:08  jwellner
- verdiept overzicht
- layout

Revision 1.12  2005/11/18 15:15:01  jwellner
no message

Revision 1.11  2005/11/17 07:25:02  jwellner
no message

Revision 1.10  2005/10/07 07:15:15  jwellner
rapportage

Revision 1.9  2005/09/30 09:45:45  jwellner
rapporten aangepast.

Revision 1.8  2005/09/29 15:00:18  jwellner
no message

Revision 1.7  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.6  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.5  2005/09/12 12:04:16  jwellner
bugs en features

Revision 1.4  2005/09/09 11:31:46  jwellner
diverse aanpassingen zie e-mails Theo

Revision 1.3  2005/08/05 12:08:04  jwellner
no message

Revision 1.2  2005/08/01 13:05:25  jwellner
diverse kleine bugfixes :
- beheerfee nooit < 0

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.3  2005/07/12 07:09:50  jwellner
no message

Revision 1.2  2005/07/08 13:52:01  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportOIV_L80.php");

class RapportOIS_L81
{
	function RapportOIS_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->oiv=new RapportOIV_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Vastrendende waarnde exclusief derivaten";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->excelData=array();
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

	function writeRapport()
	{
	  global $__appvar;
    $DB=new DB();
    $query="SELECT Fondsen.rating,Rating.Omschrijving as ratingOmschrijving,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
 sum(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro
    FROM TijdelijkeRapportage
INNER JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
INNER JOIN Rating ON Fondsen.rating = Rating.rating
    WHERE
		TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."' AND
		TijdelijkeRapportage.portefeuille='".$this->portefeuille."' AND hoofdcategorie IN('VAR','H-LIQ')
		".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY
  Rating.rating,
	beleggingscategorie,
	beleggingssector
ORDER BY
  Rating.Afdrukvolgorde,
	beleggingscategorieVolgorde,
	beleggingssectorVolgorde";
    
    $DB->SQL($query);//echo $query;exit;
    $DB->Query();
    $categorieen=array();
    $sectoren=array();
    $ratings=array();
    $categorieTotalen=array();
    $sectorTotalen=array();
    $ratingTotalen=array();
    
    while($dbdata = $DB->nextRecord())
    {
      $ratings[$dbdata['rating']]=$dbdata['ratingOmschrijving'];
      $categorieen[$dbdata['beleggingscategorie']]=$dbdata['beleggingscategorieOmschrijving'];
      $sectoren[$dbdata['beleggingssector']]=$dbdata['beleggingssectorOmschrijving'];
      
      $sectorTotalen[$dbdata['beleggingscategorie']][$dbdata['beleggingssector']][$dbdata['rating']] += $dbdata['actuelePortefeuilleWaardeEuro'];
      $categorieTotalen[$dbdata['beleggingscategorie']][$dbdata['rating']]+=$dbdata['actuelePortefeuilleWaardeEuro'];
      $ratingTotalen[$dbdata['rating']]+=$dbdata['actuelePortefeuilleWaardeEuro'];
    }
    
    $header=array('','');
    foreach($ratings as $rating=>$omschrijving)
    {
      $header[]=$omschrijving;
    }
    $aantalRatings=count($ratings);

    $cellWidth=(297-($this->pdf->marge*2+65))/$aantalRatings;
    $this->pdf->addPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $widths=array(15,50);
    $aligns=array('L','L');
    if($cellWidth>30)
      $cellWidth=25;
    for($i=0;$i<=$aantalRatings;$i++)
    {
      $widths[]=$cellWidth;
      $aligns[]='R';
    }
    $this->pdf->setWidths($widths);
    $this->pdf->setAligns($aligns);
    $this->pdf->row($header);
    $this->pdf->excelData[]=$header;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $i=0;
    
    foreach($categorieen as $categorie=>$categorieOmschrijving)
    {

      $j=0;
      $i++;
      $row=array('1.'.$i,$categorieOmschrijving);
      $xlsRow=$row;
      foreach($ratings as $rating=>$omschrijving)
      {
        $row[]=$this->formatGetal(round($categorieTotalen[$categorie][$rating],0),0);
        $xlsRow[]=round($categorieTotalen[$categorie][$rating],0);
      }
      $this->pdf->row($row);
      $this->pdf->excelData[]=$xlsRow;
      foreach($sectoren as $sector=>$sectorOmschrijving)
      {
        if(!isset($sectorTotalen[$categorie][$sector]))
        {
          continue;
        }
        $j++;
        $row=array('1.'.$i.'.'.$j,'w.v. '.$sectorOmschrijving);
        $xlsRow=$row;
        foreach($ratings as $rating=>$omschrijving)
        {
          $row[]=$this->formatGetal(round($sectorTotalen[$categorie][$sector][$rating],0),0);
          $xlsRow[]=round($sectorTotalen[$categorie][$sector][$rating],0);
        }
        $this->pdf->row($row);
        $this->pdf->excelData[]=$xlsRow;
        
      }
    }
    
    $row=array('','Totaal vastrentende waarden');
    $xlsRow=$row;
    foreach($ratings as $rating=>$omschrijving)
    {
      $row[]=$this->formatGetal(round($ratingTotalen[$rating],0),0);
      $xlsRow[]=round($ratingTotalen[$rating],0);
    }
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row($row);
    $this->pdf->excelData[]=$xlsRow;
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   // echo $query."<br>\n";exit;
    
	}
}
?>