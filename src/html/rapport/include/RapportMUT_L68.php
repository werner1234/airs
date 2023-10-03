<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/04 16:41:40 $
File Versie					: $Revision: 1.13 $

$Log: RapportMUT_L68.php,v $
Revision 1.13  2020/05/04 16:41:40  rvv
*** empty log message ***

Revision 1.11  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.10  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.9  2017/10/08 14:09:23  rvv
*** empty log message ***

Revision 1.8  2017/05/28 09:58:52  rvv
*** empty log message ***

Revision 1.7  2017/02/25 18:02:29  rvv
*** empty log message ***

Revision 1.6  2016/12/17 16:33:26  rvv
*** empty log message ***

Revision 1.5  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.4  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.3  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.2  2016/05/29 13:47:41  rvv
*** empty log message ***

Revision 1.1  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.27  2015/11/22 14:30:47  rvv
*** empty log message ***

Revision 1.26  2015/08/08 11:33:29  rvv
*** empty log message ***

Revision 1.25  2015/08/05 15:58:21  rvv
*** empty log message ***

Revision 1.24  2013/06/15 15:54:41  rvv
*** empty log message ***

Revision 1.23  2012/03/25 13:27:17  rvv
*** empty log message ***

Revision 1.22  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.21  2010/06/12 08:38:22  rvv
*** empty log message ***

Revision 1.20  2010/04/14 16:58:43  rvv
*** empty log message ***

Revision 1.19  2010/01/24 16:32:17  rvv
*** empty log message ***

Revision 1.18  2010/01/17 11:00:49  rvv
*** empty log message ***

Revision 1.17  2009/04/15 14:21:19  rvv
*** empty log message ***

Revision 1.16  2009/01/20 17:44:09  rvv
*** empty log message ***

Revision 1.15  2007/11/22 11:36:49  rvv
att aanpassing backoffice

Revision 1.14  2007/03/27 14:58:20  rvv
VreemdeValutaRapportage

Revision 1.13  2007/01/31 16:20:27  rvv
*** empty log message ***

Revision 1.12  2006/02/07 11:06:28  jwellner
- bugfix valuta in mutatievoorstel fondsen
- bugfix in MUT / TRANS layout 8

Revision 1.11  2006/02/03 08:00:27  jwellner
diverse bugfixen

Revision 1.10  2006/01/23 14:13:43  jwellner
no message

Revision 1.9  2006/01/03 15:41:53  cvs
kolom van 15 naar 19 breed

Revision 1.8  2005/11/17 07:25:02  jwellner
no message

Revision 1.7  2005/10/26 11:47:39  jwellner
no message

Revision 1.6  2005/10/21 08:08:56  jwellner
lock file bij complete database updates

Revision 1.5  2005/10/19 11:18:23  jwellner
focus op 1e veld in formulier bij editForm

Revision 1.4  2005/09/29 15:00:18  jwellner
no message

Revision 1.3  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.2  2005/09/13 14:49:18  jwellner
rapportage toevoegingen

Revision 1.1  2005/07/15 11:21:00  jwellner
Layout verwijderd, alles samengevoegd in PDFRapport

Revision 1.2  2005/07/12 07:09:50  jwellner
no message

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportMutatieoverzichtLayout.php");

class RapportMUT_L68
{
  function RapportMUT_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "MUT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Mutatie-overzicht";
    
    if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
      $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->excelData[]=array("Grootboek","Periode","Bank Afschrift","Omschrijving","Boekdatum","Rekening","Debet","Credit");
    
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function printSubTotaal($title, $totaalA, $totaalB)
  {
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
    $totaal2 = $totaal1 + $this->pdf->widthA[6];
    $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
    $this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
    
    if(!empty($totaalA))
    {
      $totaalAtxt = $this->formatGetal($totaalA,2);
    }
    
    if(!empty($totaalB))
    {
      $totaalBtxt = $this->formatGetal($totaalB,2);
    }
    
    $this->pdf->SetX($totaal1 - $this->pdf->widthA[5]);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->Cell($this->pdf->widthA[5],4,$title, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    return true;
  }
  
  function printTotaal($title, $totaalA, $totaalB, $grandtotal=false)
  {
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5];
    $totaal2 = $totaal1 + $this->pdf->widthA[6];

//		$this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
//		$this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
    
    if(!empty($totaalA))
    {
      $totaalAtxt = $this->formatGetal($totaalA,2);
    }
    
    if(!empty($totaalB))
    {
      $totaalBtxt = $this->formatGetal($totaalB,2);
    }
    
    $this->pdf->SetX($totaal1 - $this->pdf->widthA[5]- $this->pdf->widthA[4]);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    
    $this->pdf->Cell($this->pdf->widthA[4],4,$title, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[5],4,"", 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[6],4,$totaalAtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthA[7],4,$totaalBtxt, 0,0, "R");
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    
    if($grandtotal)
    {
      if(!empty($totaalA))
      {
        $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
        $this->pdf->Line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY()+1);
      }
      if(!empty($totaalB))
      {
        $this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
        $this->pdf->Line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY()+1);
      }
      $this->pdf->ln();
    }
    else
    {
      $this->pdf->setDash(1,1);
      if(!empty($totaalA))
        $this->pdf->Line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
      if(!empty($totaalB))
        $this->pdf->Line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[7],$this->pdf->GetY());
      $this->pdf->setDash();
      $this->pdf->ln();
    }
    return $totaalB;
  }
  
  function printKop($title, $type="default")
  {
    switch($type)
    {
      case "b" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'b';
        break;
      case "bi" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'bi';
        break;
      case "i" :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = 'i';
        break;
      default :
        $font = $this->pdf->rapport_font;
        $fontsize = $this->pdf->rapport_fontsize;
        $fonttype = '';
        break;
    }
    
    $this->pdf->SetFont($font,$fonttype,$fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->MultiCell(90,4, $title, 0, "L");
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
  }
  
  function writeRapport()
  {
    
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $DB = new DB();
    // voor data
    $this->pdf->widthA = array(5,25,90,25,40,20,30,30,15);
    $this->pdf->alignA = array('R','R','L','R','R','R','R','R');
    
    // voor kopjes
    $this->pdf->widthB = array(5,25,90,25,40,20,30,30,15);
    $this->pdf->alignB = array('R','R','L','R','R','R','R','R');
    
    if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['maandrapportage'] == 1 || $this->pdf->selectData['kwartaalrapportage'] == 1) )
    {
      $maand = date("n",db2jul($this->rapportageDatum));
      $kwartaal = floor(($maand / 4)+1);
      switch($kwartaal)
      {
        case 1 :
          $this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
          $this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
          break;
        case 2 :
          $this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
          $this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
          break;
        case 3 :
          $this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
          $this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
          break;
        case 4 :
          $this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
          $this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
          break;
      }
    }
    
    $this->pdf->AddPage();
    $this->pdf->templateVars['MUTPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['MUTPaginas']=$this->pdf->rapport_titel;
    
    // loopje over Grootboekrekeningen Opbrengsten = 1
    $extraquery='';
    if($this->pdf->selectData['GrootboekTm'])
      $extraquery .= " AND (Rekeningmutaties.Grootboekrekening >= '".$this->pdf->selectData['GrootboekVan']."' AND Rekeningmutaties.Grootboekrekening  <= '".$this->pdf->selectData['GrootboekTm']."') ";
    
    
    foreach ($this->pdf->lastPOST as $key=>$value)
    {
      if(substr($key,0,4)=='MUT_' && $value==1)
      {
        $grootboeken[]=substr($key,4);
        $filter = 1;
      }
    }
    
    if($filter == 1)
    {
      $grootboekSelectie = implode('\',\'',$grootboeken);
      $extraquery .= "AND Rekeningmutaties.Grootboekrekening IN('$grootboekSelectie')  ";
    }
  
    $fondsPerPortefeuille=array();
    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $this->verdiept = new portefeuilleVerdiept($this->pdf, $this->portefeuille, $this->rapportageDatum);
      //	$verdiepteFondsen = $this->verdiept->getFondsen();
      //listarray();exit;

      foreach($this->verdiept->FondsPortefeuilleData as $fonds=>$portefeuille)
        $fondsPerPortefeuille[$portefeuille]=$fonds;
      $portefeuilles=array_values($this->verdiept->FondsPortefeuilleData);
      $portefeuilles[]=$this->portefeuille ;
      
      $query = "SELECT Rekeningen.Portefeuille, " .
        "Rekeningmutaties.Boekdatum, " .
        "Rekeningmutaties.Omschrijving ," .
        "ABS(Rekeningmutaties.Aantal) AS Aantal, " .
        "Rekeningmutaties.Debet $koersQuery as Debet, " .
        "Rekeningmutaties.Credit $koersQuery as Credit, " .
        "Rekeningmutaties.Valutakoers, " .
        "Rekeningmutaties.Rekening, " .
        "Rekeningmutaties.Grootboekrekening, " .
        "Rekeningmutaties.Afschriftnummer, " .
        "Grootboekrekeningen.Omschrijving AS gbOmschrijving, " .
        "Grootboekrekeningen.Opbrengst, " .
        "Grootboekrekeningen.Kosten, " .
        "Grootboekrekeningen.Afdrukvolgorde " .
        "FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen " .
        "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening " .
        "AND Rekeningen.Portefeuille IN ('" . implode("','",$portefeuilles)."') " .
        "AND Rekeningmutaties.Verwerkt = '1' " .
        "AND Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' " .
        "AND Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' " . $extraquery .
        "AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL " .
        "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening " .
        "AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') " .
        "ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
      //	bepaalHuisfondsAandeel
    }
    else
    {
      $query = "SELECT " .
        "Rekeningmutaties.Boekdatum, " .
        "Rekeningmutaties.Omschrijving ," .
        "ABS(Rekeningmutaties.Aantal) AS Aantal, " .
        "Rekeningmutaties.Debet $koersQuery as Debet, " .
        "Rekeningmutaties.Credit $koersQuery as Credit, " .
        "Rekeningmutaties.Valutakoers, " .
        "Rekeningmutaties.Rekening, " .
        "Rekeningmutaties.Grootboekrekening, " .
        "Rekeningmutaties.Afschriftnummer, " .
        "Grootboekrekeningen.Omschrijving AS gbOmschrijving, " .
        "Grootboekrekeningen.Opbrengst, " .
        "Grootboekrekeningen.Kosten, " .
        "Grootboekrekeningen.Afdrukvolgorde " .
        "FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen " .
        "WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening " .
        "AND Rekeningen.Portefeuille = '" . $this->portefeuille . "' " .
        "AND Rekeningmutaties.Verwerkt = '1' " .
        "AND Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' " .
        "AND Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' " . $extraquery .
        "AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL " .
        "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening " .
        "AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') " .
        "ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
    }
    $DB->SQL($query);
    $DB->Query();
    //listarray($this->verdiept);exit;//->FondsPortefeuilleData
    $velden=array('Aantal'=>'Aantal','Debet'=>'Credit','Credit'=>'Debet');
    $aandeel=1;
    $actueleWaardePortefeuille=0;
    $subdebet=0;
    $subcredit=0;
    $totaalcredit=0;
    $totaaldebet=0;
    $fill=false;
    $fondsAandeelOpDatum=array();
    $pstartJul=db2jul($this->pdf->portefeuilledata['Startdatum'])+3600*24*2;
    
    while($mutaties = $DB->nextRecord())
    {
      if($this->pdf->lastPOST['doorkijk']==1)
      {
        if($mutaties['Portefeuille']<> $this->portefeuille )
        {
          if(db2jul($mutaties['Boekdatum']) < $pstartJul)
            continue;
          
          $fonds=$fondsPerPortefeuille[$mutaties['Portefeuille']];
          //$this->verdiept->FondsPortefeuilleData[]
          //
          if(isset($fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']]))
            $aandeel=$fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']];
          else
          {
            $aandeel=bepaalHuisfondsAandeel($fonds, $this->portefeuille, $mutaties['Boekdatum']);
            $fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']]=$aandeel;
          }
          $tmp=$mutaties;
          foreach($velden as $veldIn=>$veldUit)
          {
            if($aandeel<0)
              $tmp[$veldUit]=$mutaties[$veldIn]*$aandeel;
            else
              $tmp[$veldIn]=$mutaties[$veldIn]*$aandeel;
          }
          $mutaties=$tmp;
        }
      }
      // print totaal op hele categorie.
      if(!empty($lastCategorie) && $lastCategorie <> $mutaties['gbOmschrijving'])
      {
        
        
        $this->printSubTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal),$subdebet, $subcredit);
        
        $totaal=$subcredit-$subdebet;
        if($totaal < 0)
          $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), abs($totaal), 0);
        else
          $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), 0, abs($totaal));
        $subdebet = 0;
        $subcredit = 0;
      }
      
      if($lastCategorie <> $mutaties['gbOmschrijving'])
      {
        $this->printKop(vertaalTekst($mutaties['gbOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      
      $debet	= abs($mutaties['Debet']) * $mutaties['Valutakoers'];
      $credit	= abs($mutaties['Credit']) * $mutaties['Valutakoers'];
      
      $subdebet += round($debet,2);
      $subcredit += round($credit,2);
      
      if($debet <> 0)
        $debettxt = $this->formatGetal($debet,2);
      else
        $debettxt = "";
      
      if($credit <> 0)
        $credittxt = $this->formatGetal($credit,2);
      else
        $credittxt = "";
      
      
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      
      if($this->pdf->rapport_taal==0)
      {
        $omschrijving=$mutaties['Omschrijving'];
      }
      else
      {
        $omschrijving=vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal);
        if($omschrijving==$mutaties['Omschrijving'])
        {
          $omschrijvingDelen=explode(" ",$mutaties['Omschrijving']);
          $omschrijvingDelen[0]=vertaalTekst($omschrijvingDelen[0],$this->pdf->rapport_taal);
          $omschrijving=implode(" ",$omschrijvingDelen);
          
        }
      }
      
      if($fill==true)
      {
        $this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,0,1);
        //listarray($this->pdf->widths);
        $fill=false;
      }
      else
      {
        $this->pdf->fillCell=array();
        $fill=true;
      }
      
      //	$strWidth=$this->pdf->GetStringWidth($omschrijving);
      //	if($this->pdf->widths[2] < ($strWidth+5))
      //		$omschrijving=substr($omschrijving,0,30).'...';
      
      $stringWidth=$this->pdf->GetStringWidth($omschrijving);
      if($stringWidth >= $this->pdf->widths[2]-2)
      {
        $newString='';
        $omschrijvingRuimte=$this->pdf->widths[2]-$this->pdf->GetStringWidth('...')-2;
        for($i=0; $i<strlen($omschrijving); $i++)
        {
          $char=$omschrijving[$i];
          $omschrijvingRuimte-=$this->pdf->GetStringWidth($char);
          if($omschrijvingRuimte<0)
          {
            $newString=substr($omschrijving,0,$i);
            break;
          }
        }
        $omschrijving=$newString.'...';
      }
      
      
      
      //echo $this->pdf->widths[2]." ". $strWidth." $omschrijving <br>\n";
      
      $this->pdf->row(array('',
                        $mutaties['Afschriftnummer'],
                        $omschrijving,
                        date("d-m-Y",db2jul($mutaties['Boekdatum'])),
                        str_replace('REN','',$mutaties['Rekening']),
                        "",
                        $debettxt,
                        $credittxt,' '));
      $this->pdf->excelData[]=array($mutaties['gbOmschrijving'],date("n",db2jul($mutaties['Boekdatum'])),$mutaties['Afschriftnummer'],$omschrijving,	date("d-m-Y",db2jul($mutaties['Boekdatum'])),$mutaties['Rekening'],round($debet,2),round($credit,2));
      $totaalcredit += $credit;
      $totaaldebet += $debet;
      $lastCategorie = $mutaties['gbOmschrijving'];
    }
    
    $this->printSubTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal),$subdebet, $subcredit);
    
    $totaal=$subcredit-$subdebet;
    if($totaal < 0)
      $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), abs($totaal), 0);
    else
      $actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), 0, abs($totaal));
    
    
    unset(	$this->pdf->fillCell );
    
  }
}
?>