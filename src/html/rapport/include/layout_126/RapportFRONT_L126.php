<?php
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONT_L126
{
  function RapportFRONT_L126($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "FRONT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_titel = "";//"Titel pagina";
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
    $this->rapportageDatum = $rapportageDatum;
    $this->rapportageDatumJul=db2jul($this->rapportageDatum);
    //$this->pdf->extraPage =0;
    $this->DB = new DB();
    
    $this->rapportMaand 	= date("n",$this->rapportageDatumJul);
    $this->rapportDag 		= date("d",$this->rapportageDatumJul);
    $this->rapportJaar 		= date("Y",$this->rapportageDatumJul);
    
    $this->pdf->brief_font = $this->pdf->rapport_font;
    
  }
  
  
  function kopEnVoet()
  {
    $startY=$this->pdf->GetY();

    if(is_file($this->pdf->rapport_logo))
    {
      $factor=0.06;
      $x=885*$factor;//$x=885*$factor;
      $y=386*$factor;//$y=849*$factor;
      $this->pdf->Image($this->pdf->rapport_logo, 25,20, $x, $y);
    }
    
    $this->pdf->SetWidths(array(210-(2*$this->pdf->marge)));
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    $this->pdf->SetTextColor(0,55,124);
    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(280);
    
    $this->pdf->Row(array("Sequoia Vermogensbeheer B.V. - Stationsweg 6 - 6861 EG Oosterbeek - Nederland - T +31(0)88-2057979"));
    $this->pdf->Ln(2);
    $stringWidth=$this->pdf->GetStringWidth('www.sequoiabeheer.nl');
    $spaces=round(($stringWidth)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spaceText=str_repeat(' ',$spaces);
    
    $this->pdf->Row(array("E info@sequoiabeheer.nl - $spaceText - Rabobank NL66RABO0355054272 - KvK 62851799 - BTW nr. NL.8549.83.685.B01"));
    $stringWidthVoor=$this->pdf->GetStringWidth('E info@sequoiabeheer.nl - ');
    $stringWidthAchter=$this->pdf->GetStringWidth(' - Rabobank NL66RABO0355054272 - KvK 62851799 - BTW nr. NL.8549.83.685.B01');
    $this->pdf->SetTextColor(0,170,236);
    $spacesVoor=round(($stringWidthVoor)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $spacesAchter=round(($stringWidthAchter)/($this->pdf->CurrentFont['cw'][' ']*$this->pdf->FontSize/1000));
    $this->pdf->Ln(-4);
    $this->pdf->Row(array(str_repeat(' ',$spacesVoor).'www.sequoiabeheer.nl'.str_repeat(' ',$spacesAchter)));
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetTextColor(0);
    $this->pdf->SetY($startY);
  }
  
  
  function writeRapport()
  {
    global $__appvar;
  
    $this->front1();
    $this->pdf->rapport_type = "FRONT1";
    //$this->front2();
    $this->front3();
  }
  
  function front1()
  {
    global $__appvar;
    $this->pdf->AddPage('L');
    $this->pdf->Image($this->pdf->rapport_logo, 18, 8, $this->pdf->logoXsize*1.2);
    $this->pdf->MemImage($this->pdf->boomLogo,210,80,30);
    $this->pdf->SetWidths(array(8,200));
    $this->pdf->SetAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',32);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetY(150);
    if(isset($this->title) && $this->title<>'')
       $this->pdf->Row(array('',$this->title));
    else
       $this->pdf->Row(array('','Vermogensrapportage'));
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->line($this->pdf->marge+8,150+10,131,150+10);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'',14);
    $rapportagePeriode = date("j", $this->rapportageDatumVanafJul) . " " .
      vertaalTekst($__appvar["Maanden"][date("n", $this->rapportageDatumVanafJul)], $this->pdf->rapport_taal) . " " .
      date("Y", $this->rapportageDatumVanafJul) .
      ' ' . vertaalTekst('t/m', $this->pdf->rapport_taal) . ' ' .
      date("j", $this->rapportageDatumJul) . " " .
      vertaalTekst($__appvar["Maanden"][date("n", $this->rapportageDatumJul)], $this->pdf->rapport_taal) . " " .
      date("Y", $this->rapportageDatumJul);
    $this->pdf->SetY(165);
    $this->pdf->row(array('', $rapportagePeriode));
    
    
    $this->pdf->frontPage = true;
  }
  
  function front2()
  {
    global $__appvar;
    $this->pdf->AddPage('L');
    $this->pdf->SetY(175);
    $this->pdf->SetWidths(array(4,200));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',7);
    $this->pdf->Row(array('','Disclaimer:'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',7);
    $this->pdf->Row(array('','Deze Sequoia vermogensrapportages is samengesteld door Sequoia Vermogensbeheer B.V. Er kunnen aan deze publicatie geen rechten worden ontleend.
Sequoia staat niet in voor de juistheid en volledigheid van informatie en aanvaardt daarvoor geen aansprakelijkheid.'));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',7);
    $this->pdf->Row(array('','Copyright © '.date('Y').', Sequoia Vermogensbeheer B.V.'));
    
  }
  
  function front3($viaVar=false)
  {
    global $__appvar;
    
    
    $query = "SELECT
    CRM_naw.Adres,
CRM_naw.Pc,
CRM_naw.Plaats,
CRM_naw.Land,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                Gebruikers.emailAdres as accountManagerEmail,
                Gebruikers.mobiel as accountManagerTel,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
                JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
                LEFT JOIN  CRM_naw on Portefeuilles.portefeuille=CRM_naw.Portefeuille
                LEFT JOIN Gebruikers ON Accountmanagers.Accountmanager=Gebruikers.Accountmanager
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
    $this->DB->SQL($query);
    $this->DB->Query();
    $portefeuilledata = $this->DB->nextRecord();
    if($portefeuilledata['Pc']<>'')
      $portefeuilledata['Plaats']=$portefeuilledata['Pc'].' '.$portefeuilledata['Plaats'];
    
    if($viaVar==false)
    {
      $this->pdf->AddPage('L');
    }
    
    $this->pdf->widthA = array(20,180);
    $this->pdf->alignA = array('L','L','L');
    
    $fontsize = 12; //$this->pdf->rapport_fontsize
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
  
    $this->pdf->SetWidths($this->pdf->widthA);
    $beginY=58;
    $this->pdf->SetY($beginY);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(2);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(2);
      $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$portefeuilledata['Plaats']));
  
    $this->pdf->SetY($beginY);
    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
    $portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);
  
    $this->pdf->SetWidths(array(130,120));
    $this->pdf->SetAligns(array('L','R'));
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
    $this->pdf->ln(6);

    $this->pdf->row(array('',"Vermogensbeheerder: ".$portefeuilledata['accountManager']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',"Email: ".$portefeuilledata['accountManagerEmail']));
    $this->pdf->ln(2);
    $this->pdf->row(array('',"Tel: ".($portefeuilledata['accountManagerTel']==''?'088-2057979':$portefeuilledata['accountManagerTel'])));
    $this->pdf->ln(24);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).": ".date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->SetY(90);
    
    if($viaVar==false)
    {
//listarray($this->pdf->portefeuilledata);
      $this->pdf->SetWidths(array(20, 40,50));
      $this->pdf->row(array(' ', vertaalTekst('Depotbank', $this->pdf->rapport_taal). ':', $this->pdf->portefeuilledata['DepotbankOmschrijving']));
      $this->pdf->ln(2);
      $this->pdf->row(array(' ', vertaalTekst('Depotnummer', $this->pdf->rapport_taal). ':', $portefeuilledata['Portefeuille']));
      $this->pdf->ln(2);
      $this->pdf->row(array(' ', vertaalTekst('Risicoprofiel', $this->pdf->rapport_taal). ':', $this->pdf->portefeuilledata['Risicoklasse']));
      $this->pdf->ln(2);
      //$this->pdf->row(array(' ', vertaalTekst('Mandaat', $this->pdf->rapport_taal). ':', str_replace('SEQ ','Sequoia ',$this->pdf->portefeuilledata['ModelPortefeuille'])));
      //$this->pdf->ln(2);
      $this->pdf->row(array(' ', vertaalTekst('Rapportagevaluta', $this->pdf->rapport_taal). ':' ,($this->pdf->portefeuilledata['RapportageValuta']==''?'EUR':$this->pdf->portefeuilledata['RapportageValuta'])));
      $this->pdf->ln();
    }
    else
      $this->pdf->ln(20);
    

    
    
    $this->pdf->SetY(133);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

    

    

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
 

  
  }
}
?>