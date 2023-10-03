<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/18 17:44:11 $
File Versie					: $Revision: 1.25 $

$Log: RapportFRONT_L22.php,v $
Revision 1.25  2020/03/18 17:44:11  rvv
*** empty log message ***

Revision 1.24  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.23  2017/10/07 16:53:19  rvv
*** empty log message ***

Revision 1.22  2017/09/30 16:31:15  rvv
*** empty log message ***

Revision 1.21  2016/08/17 16:01:13  rvv
*** empty log message ***

Revision 1.20  2016/08/13 16:55:26  rvv
*** empty log message ***

Revision 1.19  2016/03/30 10:35:05  rvv
*** empty log message ***

Revision 1.18  2015/10/19 14:23:56  rvv
*** empty log message ***

Revision 1.17  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.16  2015/02/11 16:49:55  rvv
*** empty log message ***

Revision 1.15  2014/10/22 15:50:27  rvv
*** empty log message ***

Revision 1.14  2014/05/25 14:38:33  rvv
*** empty log message ***

Revision 1.13  2014/04/12 16:28:12  rvv
*** empty log message ***

Revision 1.12  2013/06/19 15:54:30  rvv
*** empty log message ***

Revision 1.11  2013/06/16 12:36:10  rvv
*** empty log message ***

Revision 1.10  2013/06/16 11:47:15  rvv
*** empty log message ***

Revision 1.9  2013/03/27 17:02:38  rvv
*** empty log message ***

Revision 1.8  2012/06/06 18:18:25  rvv
*** empty log message ***

Revision 1.7  2011/12/11 10:58:53  rvv
*** empty log message ***

Revision 1.6  2011/09/25 16:23:28  rvv
*** empty log message ***

Revision 1.5  2011/04/12 09:05:54  cvs
telefoonnr en BTW nr aanpassen

Revision 1.4  2011/01/11 08:23:38  cvs
*** empty log message ***

Revision 1.3  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.2  2011/01/05 18:53:09  rvv
*** empty log message ***

Revision 1.1  2010/12/05 09:54:08  rvv
*** empty log message ***

Revision 1.4  2010/07/04 15:24:39  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L22
{
  function RapportFront_L22($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "FRONT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    
    if($this->pdf->rapport_FRONT_titel)
      $this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
    else
      $this->pdf->rapport_titel = "Titel pagina";
    
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
    
    /*
    $lichtblauw=array(0,170,236);
    $donkerblauw=array(0,55,124);
    $steps=100;
    $gstep=($lichtblauw[1]-$donkerblauw[1])/$steps;
    $bstep=($lichtblauw[2]-$donkerblauw[2])/$steps;
    $xstep=210/$steps;
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor[r],$this->pdf->rapport_kop_bgcolor[g],$this->pdf->rapport_kop_bgcolor[b]);
    $this->pdf->Rect(0, 0, 210, 100 , 'F');
    $R=$donkerblauw[0];
    $G=$donkerblauw[1];
    $B=$donkerblauw[2];
    for($x=0;$x<210;$x+=$xstep)
    {
      $this->pdf->SetFillColor($R,$G,$B);
      $G+=$gstep;
      $B+=$bstep;
      $this->pdf->Rect($x,0,$x+$xstep,5, 'F');
    }
    */
    
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
    
    $this->pdf->Row(array("Sequoia Vermogensbeheer N.V. - Stationsweg 6 - 6861 EG Oosterbeek - Nederland - T +31(0)88-2057979"));
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
    $this->pdf->addPage('P');
    $this->pdf->frontPage = true;
    $this->kopEnVoet();
    
    /*
		if(is_file($this->pdf->rapport_logo))
		{
		   $factor=0.06;
		   $x=885*$factor;//$x=885*$factor;
		   $y=386*$factor;//$y=849*$factor;
		   $this->pdf->Image($this->pdf->rapport_logo, 142,3, $x, $y);
		}
   */
    
    $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.pc,
                Clienten.Woonplaats,
                Clienten.Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Portefeuilles.Risicoklasse,
                Portefeuilles.BeheerfeeAantalFacturen,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Naam as VermogensbeheerderNaam,
                Vermogensbeheerders.Adres as VermogensbeheerderAdres,
                Vermogensbeheerders.Woonplaats as VermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon as VermogensbeheerderTelefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                acc.naam as Accountmanager,
                acc.titel as AccountmanagerTitel,
                acc.titel2 as AccountmanagerTitel2,
                acc.Handtekening as Handtekening,
                acc2.naam as AccountmanagerTwee,
                acc2.titel as AccountmanagerTweeTitel,
                acc2.titel2 as AccountmanagerTweeTitel2,
                acc2.Handtekening as tweedeHandtekening
		          FROM
		            (Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders)
                LEFT JOIN Accountmanagers as acc ON Portefeuilles.Accountmanager = acc.Accountmanager
                LEFT JOIN Accountmanagers as acc2 ON Portefeuilles.Tweedeaanspreekpunt = acc2.Accountmanager
		          WHERE
		            Portefeuille = '".$this->portefeuille."' AND
		            Portefeuilles.Client = Clienten.Client AND
                Accountmanagers.Accountmanager = Portefeuilles.Accountmanager AND
                Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder";
    $this->DB->SQL($query);
    $this->DB->Query();
    $portefeuilledata = $this->DB->nextRecord();
    $portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
    $portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
    $portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'];
    $portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
    $portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];
    
    
    $this->pdf->SetY(30);
    $this->pdf->SetWidths(array(153,65));
    $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 3.5;
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    //
    //$this->pdf->SetTextColor(0,69,132);
    //$this->pdf->row(array('',$portefeuilledata["VermogensbeheerderNaam"]));
    //$this->pdf->row(array('',$portefeuilledata["VermogensbeheerderAdres"]));
    //$this->pdf->row(array('',$portefeuilledata["VermogensbeheerderWoonplaats"]));
    //$this->pdf->row(array('',$portefeuilledata["VermogensbeheerderTelefoon"]));
    //$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->SetWidths(array(25-$this->pdf->marge,140));
    $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeight = 5;
    
    
    $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres,
CRM_naw.verzendPc,
CRM_naw.verzendPlaats,
CRM_naw.verzendLand,
CRM_naw.verzendAanhef,
CRM_naw.ondernemingsvorm,
CRM_naw.titel,
CRM_naw.voorletters,
CRM_naw.tussenvoegsel,
CRM_naw.achternaam,
CRM_naw.achtervoegsel,
CRM_naw.part_naam,
CRM_naw.part_voorvoegsel,
CRM_naw.part_titel,
CRM_naw.part_voorletters,
CRM_naw.part_tussenvoegsel,
CRM_naw.part_achternaam,
CRM_naw.part_achtervoegsel,
CRM_naw.enOfRekening
FROM CRM_naw WHERE Portefeuille = '".$this->portefeuille."'  ";
    
    $this->DB->SQL($query);
    $crmData = $this->DB->lookupRecord();
    
    $this->pdf->SetY(60);
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    
    if($crmData)
    {
      $this->pdf->row(array('',$crmData['naam']));
      if($crmData['naam1'] <> '')
        $this->pdf->row(array('',$crmData['naam1']));
      $this->pdf->row(array('',$crmData['verzendAdres']));
      $plaats=$crmData['verzendPc'];
      if($crmData['verzendPlaats'] != '')
        $plaats.=" ".$crmData['verzendPlaats'];
      $this->pdf->row(array('',$plaats));
      $this->pdf->row(array('',$crmData['verzendLand']));
    }
    else
    {
      $this->pdf->row(array('',$portefeuilledata['Naam']));
      if($portefeuilledata['Naam1'] <> '')
        $this->pdf->row(array('',$portefeuilledata['Naam1']));
      $this->pdf->row(array('',$portefeuilledata['Adres']));
      $plaats=$portefeuilledata['pc'];
      if($portefeuilledata['Woonplaats'] != '')
        $plaats.=" ".$portefeuilledata['Woonplaats'];
      $this->pdf->row(array('',$plaats));
      $this->pdf->row(array('',$portefeuilledata['Land']));
    }
    
    
    $this->pdf->SetY(95);
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetWidths(array(25-$this->pdf->marge,100,85));
    $this->pdf->SetAligns(array('L','L','L'));
    $kwartaal = floor(date("n",$this->rapportageDatumJul)/3).'e';
    if($kwartaal=="0e")
      $kwartaal="1e";
    $this->pdf->row(array('','','Oosterbeek, '.(date("j"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->SetY(120);
    $aanhef=$crmData['verzendAanhef'];
    
    /*
    if($crmData)
    {
      if($crmData['titel'] != '') $aanhef.=" ".$crmData['titel'];
      if($crmData['achtervoegsel'] != '') $aanhef.=" ".$crmData['achtervoegsel'];
      if($crmData['part_titel'] != '') $aanhef.=" ".$crmData['part_titel'];
      if($crmData['tussenvoegsel'] != '') $aanhef.=" ".$crmData['tussenvoegsel'];
      if($crmData['achternaam'] != '') $aanhef.=" ".$crmData['achternaam'];
    }
    else
    {
      if($crmData['titel'] != '') $aanhef.=" ".$portefeuilledata['Naam'];
    }
    Stationsweg 6 6861 EG   OOSTERBEEK
    */
    $this->pdf->row(array('',"$aanhef\n\n"));
    
    $vermogensbeheerders=array(''=>'Sequoia Vermogensbeheer N.V.','topcapital'=>'Top Capital Vermogensbeheer');
    // $voettekst=array(''=>'Stationsweg 6  6861 EG Oosterbeek     tel. 088-2057979     e-mail  sequoia@sequoiabeheer.nl     hr. 62851799    btw. nr. NL.8549.83.685.B01',
    //        'topcapital'=>'Stationsweg 6  6861 EG Oosterbeek     tel. 088-2057979     e-mail  sequoia@sequoiabeheer.nl     hr. 62851799    btw. nr. NL.8549.83.685.B01');
    
    
    $kwartalen=array('1e'=>'eerste','2e'=>'tweede','3e'=>'derde','4e'=>'vierde');
    
    if(!isset($this->pdf->selectData['periode']) || $this->pdf->selectData['periode']=='Kwartaalrapportage')
    {
      $briefData = "Hierbij ontvangt u de rapportage over het " . $kwartalen[$kwartaal] . " kwartaal " . date("Y", $this->rapportageDatumJul) . ".

Heeft u hierover vragen of opmerkingen? Neemt u dan gerust contact met mij op.";
    }
    else
    {
      $briefData="Hierbij treft u de recente rapportage aan.

Indien u naar aanleiding van deze rapportage vragen of opmerkingen heeft, dan vernemen wij dat graag van u.";
    }
//if($portefeuilledata['BeheerfeeAantalFacturen']==4)
//  $briefData .="\n\nTevens zenden wij u de factuur over de genoemde periode.";
    
    $briefData .="\n
Met vriendelijke groet,
".$vermogensbeheerders[$this->pdf->portefeuilledata['Remisier']]."\n";
    
    $this->pdf->SetWidths(array(25-$this->pdf->marge,160));
    $this->pdf->row(array('',$briefData));
    
    if($portefeuilledata['Handtekening'] <> '')
      $this->pdf->MemImage(base64_decode($portefeuilledata['Handtekening']), $this->pdf->getX()+20, $this->pdf->getY(), 60);
    
    $this->pdf->row(array('',"\n\n\n\n".$portefeuilledata["accountManager"]));
    
    
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',7);
    $old=$this->pdf->PageBreakTrigger;
    $this->pdf->PageBreakTrigger=290;
    
    $this->pdf->setXY(0,285);
//$this->pdf->MultiCell(210,4,$voettekst[$this->pdf->portefeuilledata['Remisier']],0, "C");
    $this->pdf->PageBreakTrigger=$old;
    
    $this->pdf->rowHeight = 4;
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->front2();
  }
  
  
  function front2($viaVar=false)
  {
    global $__appvar;
    
    
    $query = "SELECT
    CRM_naw.Adres,
CRM_naw.Pc,
CRM_naw.Plaats,
CRM_naw.Land,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
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
      
      
      if (is_file($this->pdf->rapport_logo))
      {
        // $pdfObject->Image($pdfObject->rapport_logo, 18, 3.5, 52, 20.6);
        $factor = 0.06;
        $xSize = 885 * $factor;//$x=885*$factor;
        $ySize = 386 * $factor;//$y=849*$factor;
        $this->pdf->Image($this->pdf->rapport_logo, 18, 13, $xSize, $ySize);
      }
    }
    
    $this->pdf->widthA = array(30,180);
    $this->pdf->alignA = array('L','L','L');
    
    $fontsize = 10; //$this->pdf->rapport_fontsize
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->SetY(58);
    
    if($viaVar==false)
    {
      $rapportagePeriode = vertaalTekst('Verslagperiode', $this->pdf->rapport_taal) . ' ' . date("d", $this->rapportageDatumVanafJul) . " " .
        vertaalTekst($__appvar["Maanden"][date("n", $this->rapportageDatumVanafJul)], $this->pdf->rapport_taal) . " " .
        date("Y", $this->rapportageDatumVanafJul) .
        ' ' . vertaalTekst('t/m', $this->pdf->rapport_taal) . ' ' .
        date("d", $this->rapportageDatumJul) . " " .
        vertaalTekst($__appvar["Maanden"][date("n", $this->rapportageDatumJul)], $this->pdf->rapport_taal) . " " .
        date("Y", $this->rapportageDatumJul);
      $this->pdf->row(array('', $rapportagePeriode));
      $this->pdf->ln(6);
      
      $this->pdf->SetWidths(array(30, 40, 5, 50));
      $this->pdf->row(array(' ', vertaalTekst('Vermogensrapportage', $this->pdf->rapport_taal), ':', $portefeuilledata['Portefeuille']));
      $this->pdf->ln();
      $this->pdf->row(array(' ', 'Portefeuilleprofiel', ':', $this->pdf->portefeuilledata['Risicoklasse']));
      $this->pdf->ln();
    }
    else
      $this->pdf->ln(20);
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8);
    $this->pdf->row(array(' ','PERSOONLIJK EN VERTROUWELIJK'));
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['Plaats']));
    
    
    $this->pdf->SetY(133);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->ln(2);
    $this->pdf->row(array('',''));
    
    
    $this->pdf->SetY(160);
    
    $explodedName=explode(" ",$portefeuilledata['vermogensbeheerderNaam']);
    foreach ($explodedName as $key=>$word)
      $explodedName[$key]=vertaalTekst($word,$this->pdf->rapport_taal);
    $portefeuilledata['vermogensbeheerderNaam']=implode(" ",$explodedName);
    
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['Email']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$portefeuilledata['Telefoon']));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->frontPage = true;

//    $this->pdf->AutoPageBreak=false;
//    $this->pdf->SetY(-10);
//    $this->pdf->MultiCell(290,4,"Via onze website kunt u dagelijks uw portefeuille inzien.",0,'C');
//    $this->pdf->AutoPageBreak=true;
  
  
  }
}
?>