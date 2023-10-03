<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/15 18:27:08 $
File Versie					: $Revision: 1.28 $

$Log: Factuur_L72.php,v $
Revision 1.28  2020/02/15 18:27:08  rvv
*** empty log message ***

Revision 1.27  2019/11/13 14:48:05  rvv
*** empty log message ***

Revision 1.26  2019/02/09 18:41:27  rvv
*** empty log message ***

Revision 1.25  2018/11/16 16:42:18  rvv
*** empty log message ***

Revision 1.24  2018/08/25 17:09:48  rvv
*** empty log message ***

Revision 1.23  2018/08/04 11:55:17  rvv
*** empty log message ***

Revision 1.22  2018/07/28 16:31:42  rvv
*** empty log message ***

Revision 1.21  2018/06/30 17:42:09  rvv
*** empty log message ***

Revision 1.20  2018/06/16 17:41:44  rvv
*** empty log message ***

Revision 1.19  2018/06/13 15:23:14  rvv
*** empty log message ***

Revision 1.18  2018/04/21 17:57:30  rvv
*** empty log message ***

Revision 1.17  2018/04/07 15:22:24  rvv
*** empty log message ***

Revision 1.16  2018/03/22 06:39:50  rvv
*** empty log message ***

Revision 1.15  2018/03/15 06:58:37  rvv
*** empty log message ***

Revision 1.14  2018/03/14 17:18:32  rvv
*** empty log message ***

Revision 1.13  2017/11/11 18:24:33  rvv
*** empty log message ***

Revision 1.12  2017/07/08 17:18:20  rvv
*** empty log message ***

Revision 1.11  2017/06/28 15:26:27  rvv
*** empty log message ***

Revision 1.10  2017/06/24 16:30:34  rvv
*** empty log message ***

Revision 1.9  2017/06/21 16:35:57  rvv
*** empty log message ***

Revision 1.8  2017/06/10 18:10:36  rvv
*** empty log message ***

Revision 1.7  2017/06/08 05:30:23  rvv
*** empty log message ***

Revision 1.6  2017/05/31 16:18:48  rvv
*** empty log message ***

Revision 1.5  2017/04/09 10:15:21  rvv
*** empty log message ***

Revision 1.4  2017/04/08 18:21:38  rvv
*** empty log message ***

Revision 1.3  2017/04/05 15:43:43  rvv
*** empty log message ***

Revision 1.2  2017/04/02 10:27:14  rvv
*** empty log message ***

Revision 1.1  2016/12/10 19:23:33  rvv
*** empty log message ***



*/



    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $extraMarge=25-8-6;
 		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');

$logo=$this->pdf->rapport_logo;
//$logo='./rapport/logo/Header-factuur-FL.png';
if(!function_exists('formatPercentage_L72'))
{
  function formatPercentage_L72($object, $getal, $min, $max)
  {
    $factuur =& $object;
    if (round($getal, $min) === round($getal, $max))
    {
      return $factuur->formatGetal($getal, $min);
    }
    else
    {
      return $factuur->formatGetal($getal, $max);
    }
  }
}
    if(is_file($logo))
		{



      if($this->waarden['Accountmanager']=='FL')
      {
        $logo = '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';
        $width = 100;
        $logopos = 210 / 2 - $width / 2;
        $this->pdf->memImage(base64_decode($logo),$logopos, 13,$width);
      }
      else
      {
        $factor = 0.030;
        $width = 40;
        $logopos = 210 / 2 - $width / 2;
        $this->pdf->Image($logo, $logopos, 13,$width);
      }

	   //
      $this->pdf->SetTextColor(0,0,0);
		
		}
    
    $db=new DB();
    $query = "desc CRM_naw";
    $db->SQL($query);
    $db->query();
    $extraVelden=array('SoortDienstverlening','FeeinfoVerbergen','FeeinfoSpecificatie','FactNaarTrust');
    $extraVeldSelect='';
    while($data=$db->nextRecord('num'))
    {
      if(in_array($data[0],$extraVelden))
        $extraVeldSelect.=','.$data[0];
    }
    $query = "SELECT id,CRM_naw.naam,CRM_naw.naam1,CRM_naw.ondernemingsvorm,CRM_naw.verzendPaAanhef,CRM_naw.verzendAanhef $extraVeldSelect FROM CRM_naw WHERE portefeuille='" . $this->waarden['portefeuille'] . "'";
    $db->SQL($query);
    $extraVeldInfo = $db->lookupRecord();

    $portefeuilles=array_keys($this->waarden['portefeuilleVerdeling']['eindWaarde']);
    if($portefeuilles[0]=='totaal')
      unset($portefeuilles[0]);
    foreach($portefeuilles as $portefeuille)
      $crmData[$portefeuille]=$portefeuille;
    $query="SELECT portefeuille,naam FROM CRM_naw WHERE portefeuille IN('".implode("','",$portefeuilles) ."')";
    $db->SQL($query);
    $db->query();
    while($naw = $db->nextRecord())
    {
      $crmData[$naw['portefeuille']]=$naw['naam'];
    }


    $query = "SELECT * FROM Portefeuilles WHERE portefeuille='" . $this->waarden['portefeuille'] . "'";
    $db->SQL($query);
    $extraPortefeuilleInfo = $db->lookupRecord();


    $valutaKoers=getValutaKoers($extraPortefeuilleInfo['RapportageValuta'], $this->waarden['datumTot']);
//echo "<br>\n".$extraPortefeuilleInfo['RapportageValuta']." ".$this->waarden['datumTot'];exit;

    if($this->waarden['o_clientNaam']<>''&& $this->waarden['o_clientAdres']<>'')
    {
      $this->waarden['clientNaam']               =   $this->waarden['o_clientNaam'];
      $this->waarden['clientNaam1']              =   $this->waarden['o_clientNaam1'];
      $this->waarden['clientAdres']              =   $this->waarden['o_clientAdres'];
      $this->waarden['clientPostcode']           =   $this->waarden['o_clientPostcode'];
      $this->waarden['clientWoonplaats']         =   $this->waarden['o_clientWoonplaats'];
      $this->waarden['clientLand']               =   $this->waarden['o_clientLand'];
      if($extraVeldInfo['FactNaarTrust']==1)
      {
        if($extraVeldInfo['verzendAanhef ']<>'')
          $portfolioText = "Portfolio: " . $this->waarden['verzendAanhef'];
        else
          $portfolioText = "Portfolio: " . $this->waarden['CRM_naam'];
      }
    }
    else
      $portfolioText='';

if($extraVeldInfo['ondernemingsvorm'] <>'Persoon')
  $this->waarden['clientNaam1']=$extraVeldInfo['verzendPaAanhef'];



		$this->pdf->SetY(51);
		$this->pdf->SetWidths(array($extraMarge,110,80));
		$this->pdf->SetAligns(array("L","L","L",'R'));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+1);
    $this->pdf->row(array('',vertaalTekst('VERTROUWELIJK',$this->pdf->rapport_taal)));

    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		$plaats=$this->waarden['clientWoonplaats'];
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " . $plaats;
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

		$this->pdf->SetY(95);

    if(date("n",db2jul($this->waarden['datumTot']) < 7))
      $periode='eerste';
    else
      $periode='tweede';

    $rapJul=db2jul($this->waarden['datumTot']);
    $productieDatum=DatumFull_L72($this->pdf,time());//date("j")." ".vertaalTekst($this->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
    $rapportageDatum=DatumFull_L72($this->pdf,$rapJul);//(date("j",$rapJul))." ".vertaalTekst($this->__appvar["Maanden"][date("n",$rapJul)],$this->pdf->rapport_taal)." ".date("Y",$rapJul);
    $this->pdf->ln(8);
    $this->pdf->SetWidths(array($extraMarge,135));
    $this->pdf->SetAligns(array("L",'L'));
    if($this->waarden['Accountmanager']=='FL')
      $this->pdf->row(array('',vertaalTekst("Basel",$this->pdf->rapport_taal).', '.$rapportageDatum));
    else
      $this->pdf->row(array('',vertaalTekst("Waalre",$this->pdf->rapport_taal).', '.$productieDatum));

    $this->pdf->row(array('',''.$portfolioText));

    $this->pdf->SetWidths(array($extraMarge,125,10,25));
    $this->pdf->SetAligns(array("L","L","L",'R'));
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    if($this->waarden['Accountmanager']=='FL')
      $this->pdf->row(array('',vertaalTekst("Factuurnummer",$this->pdf->rapport_taal).": ".vertaalTekst($extraVeldInfo['SoortDienstverlening'],$this->pdf->rapport_taal)."/".
        $this->waarden['kwartaal'].$this->waarden['rapportJaar'].$this->waarden['factuurNummer']));//sprintf("%05d",$this->waarden['factuurNummer'])
    else
      $this->pdf->row(array('',vertaalTekst("Factuurnummer",$this->pdf->rapport_taal).": ".$this->waarden['factuurNummer']."/".vertaalTekst($extraVeldInfo['SoortDienstverlening'],$this->pdf->rapport_taal)));//sprintf("%05d",$this->waarden['factuurNummer'])
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
		$this->pdf->ln(3);
    $this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
    $this->pdf->ln(3);


  if($this->waarden['BeheerfeeAantalFacturen']==1)
  {
    $this->waarden['rapportJaar'] = substr($this->waarden['datumTot'],0,4);
    $kwartaalTxt='';
  }
  else
  {
    $kwartaalTxt=$this->waarden['kwartaal'] . 'e '.vertaalTekst('kwartaal',$this->pdf->rapport_taal).' ';
  }


    if($this->waarden['administratieBedrag'] <> 0.00)
    {
      $this->pdf->row(array('', vertaalTekst('Fixed fee t.b.v. wealth management',$this->pdf->rapport_taal).' ' .$kwartaalTxt .
        $this->waarden['rapportJaar'], 'EUR', $this->formatGetal($this->waarden['administratieBedrag'], 2)));
      $this->pdf->ln(8);
    }
    $overigeFee=round($this->waarden['beheerfeePerPeriode']-$this->waarden['administratieBedrag'],2);
    if($overigeFee <> 0)
      $this->pdf->row(array('',vertaalTekst('Voor u verrichte werkzaamheden in het',$this->pdf->rapport_taal).' '.vertaalTekst($this->waarden['kwartaal'].'e',$this->pdf->rapport_taal).
     ' '.vertaalTekst('kwartaal',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar'],'EUR',$this->formatGetal($overigeFee,2)));

    $somExtraFactuurRegels=0;
    foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
    {
      $this->pdf->row(array('', vertaalTekst($regel['omschrijving'],$this->pdf->rapport_taal), "EUR", $this->formatGetal($regel['bedrag'], 2)));
      $somExtraFactuurRegels+=$regel['bedrag'];
    }

    $this->pdf->ln(4);
/*
    if(($this->waarden['BeheerfeeMethode']==3 && $extraVeldInfo['FeeinfoVerbergen']==0))
    {
      if(count($this->waarden['portefeuilleVerdeling']['eindWaarde']) > 2)
      {
        $this->pdf->SetWidths(array($extraMarge, 125, 10,25));
        $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
        $this->pdf->row(array('', vertaalTekst('Portefeuille',$this->pdf->rapport_taal), '',vertaalTekst('Vermogen',$this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
        foreach($this->waarden['portefeuilleVerdeling']['eindWaarde'] as $portefeuille=>$waarde)
        {
          if($portefeuille<>'totaal')
          {
            $query = "SELECT CRM_naw.naam FROM CRM_naw WHERE portefeuille='" . $portefeuille . "'";
            $db->SQL($query);
            $CRM = $db->lookupRecord();
            $this->pdf->row(array('', $portefeuille." ".$CRM['naam'], "EUR", $this->formatGetal($waarde, 2)));
          }
        }
        $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
        $this->pdf->row(array('', vertaalTekst('Totaal',$this->pdf->rapport_taal), "EUR", $this->formatGetal($this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal'], 2)));
        $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

      }
      $this->pdf->ln(4);

       $this->pdf->SetWidths(array($extraMarge, 35, 10,30));
       $this->pdf->row(array('', vertaalTekst('Tarief',$this->pdf->rapport_taal), '',vertaalTekst('Totaal Vermogen',$this->pdf->rapport_taal)));
       $this->pdf->ln(2);
      if($extraPortefeuilleInfo['valutaUitsluiten']==1)
      {

        if($extraPortefeuilleInfo['RapportageValuta']<>'EUR' && $extraPortefeuilleInfo['RapportageValuta'] <> '')
        {
          $this->pdf->row(array('', vertaalTekst('Totaal',$this->pdf->rapport_taal) ,$extraPortefeuilleInfo['RapportageValuta'], $this->formatGetal($this->waarden['totaalWaarde'] / $valutaKoers, 2)));
          $this->pdf->row(array('', vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal), $extraPortefeuilleInfo['RapportageValuta'], $this->formatGetal($this->waarden['waardeLiquiditeitenEind'] * $valutaKoers, 2)));
        }
        else
        {
          $this->pdf->row(array('', vertaalTekst('Totaal',$this->pdf->rapport_taal), "EUR", $this->formatGetal($this->waarden['totaalWaarde'], 2)));
          $this->pdf->row(array('', vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal), "EUR", $this->formatGetal($this->waarden['waardeLiquiditeitenEind'], 2)));
        }
        $this->pdf->ln(2);
      }

      if($extraPortefeuilleInfo['RapportageValuta']<>'EUR' && $extraPortefeuilleInfo['RapportageValuta'] <> '')
        $this->pdf->row(array('',"", $extraPortefeuilleInfo['RapportageValuta'], $this->formatGetal($this->waarden['rekenvermogen']/$valutaKoers, 2)));

      $this->pdf->row(array('', formatPercentage_L72($this,$this->waarden['BeheerfeePercentageVermogen'], 2,3) . "%", "EUR", $this->formatGetal($this->waarden['rekenvermogen'], 2)));

      if($this->waarden['BeheerfeeLiquiditeitenPercentage'])
      {
        $this->pdf->row(array('', $this->formatGetal($this->waarden['BeheerfeeLiquiditeitenPercentage'], 2) . "%", "EUR", $this->formatGetal($this->waarden['waardeLiquiditeitenEind'], 2)));
      }
    }
*/
    $this->pdf->ln(3);
    $this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
    $this->pdf->ln(3);
    $this->pdf->SetWidths(array($extraMarge,125,10,25));

    $this->pdf->row(array('',vertaalTekst('Totaal excl. BTW',$this->pdf->rapport_taal),'EUR',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
unset($this->pdf->CellBorders);
    $this->pdf->ln(2);
    $this->pdf->row(array('',vertaalTekst('BTW',$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0)."% ",'EUR',$this->formatGetal($this->waarden['btw'],2)));
  	$this->pdf->ln(2);
    unset($this->pdf->CellBorders);
$this->pdf->CellBorders=array('','','T','T');
$this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
if($this->waarden['btw']<>0)
  $totaalTxt='Totaal incl. BTW';
else
  $totaalTxt='Totaal';
  	$this->pdf->row(array('',vertaalTekst($totaalTxt,$this->pdf->rapport_taal),"EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);

$this->pdf->ln();
$this->pdf->SetWidths(array($extraMarge,150));
if($this->waarden['FactuurMemo'] <> '')
{
  $this->pdf->row(array('', $this->waarden['FactuurMemo']));
  $this->pdf->ln();
}


if($this->waarden['BetalingsinfoMee']==1)
  $this->pdf->row(array('',vertaalTekst('Wij verzoeken u het factuurbedrag binnen 14 dagen over te maken op onze rekening.',$this->pdf->rapport_taal) ));
else
  $this->pdf->row(array('',vertaalTekst('Deze factuur wordt binnenkort van uw rekening geïncasseerd.',$this->pdf->rapport_taal)));


    $this->pdf->setY(240);
    $this->pdf->SetWidths(array($extraMarge,80,80));
unset($this->pdf->CellBorders);

$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize+1);
if($this->waarden['Accountmanager']=='FL')
{
  $this->pdf->row(array('', "Bankrekening/Bank account:"));
  $this->pdf->row(array('', "Union Bancaire Privée, UBP SA, Basel"));
  $this->pdf->row(array('', "SWIFT/BIC: UBP GCHGGBSL"));
  $this->pdf->row(array('', "IBAN: CH44 0865 7009 0081 1705 6"));
}
else
{
  $this->pdf->row(array('', "Bankrekening/Bank account:", "Bankrekening/Bank account:"));
  $this->pdf->row(array('', "ABN Amro Bank", "Union Bancaire Privée, UBP SA, Basel"));
  $this->pdf->row(array('', "SWIFT/BIC: ABNANL2A ", "SWIFT/BIC: UBP GCHGGBSL"));
  $this->pdf->row(array('', "IBAN: NL16 ABNA 0415 6670 97 ", "IBAN: CH38 0865 7009 0B33 0777 3"));
}

$this->pdf->ln();
if($this->waarden['Accountmanager']!='FL')
  $this->pdf->row(array('',"BTW NR: NL8000.82.229.B.01",""));

$this->pdf->AutoPageBreak=false;
$this->pdf->SetWidths(array(210-$this->pdf->marge*2));
$this->pdf->SetAligns(array("C"));
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize-1);
if($this->waarden['Accountmanager']=='FL')
{
  $this->pdf->setY(297-(4*$this->pdf->rowHeight));
  $this->pdf->row(array("Quercus Vermögensverwaltungs AG, Rittergasse 1, 4051 Basel, Switzerland"));
  $this->pdf->row(array("Telephone +41 (0)61 273 13 85, VAT. no 516 495"));
  $this->pdf->row(array("Basel Chamber of Commerce, reg. no. CH-270.3.012.802-8"));
}
else
{
  $this->pdf->setY(297-(5*$this->pdf->rowHeight));
  $this->pdf->SetTextColor(191,191,191);
  $this->pdf->row(array("Box Consultants B.V."));
  $this->pdf->SetTextColor(12,37,119);
  $this->pdf->row(array("Burgemeester Mollaan 72, P.O. Box 60, 5580 AB Waalre, The Netherlands"));
  $this->pdf->row(array("Telephone +31 (0)88 600 16 00"));
  $this->pdf->row(array("Website www.boxconsultants.com E-mail info@boxconsultants.com"));
  $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
}
$this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
$this->pdf->AutoPageBreak=true;

if($this->waarden['BeheerfeeMethode']==1 && $extraVeldInfo['FeeinfoSpecificatie']==1)
{
  $kwartalen = array('null','eerste','tweede','derde','vierde');
  $jaar=date("Y",$rapJul);
  $this->pdf->addPage('P');
  if(is_file($this->pdf->rapport_logo))
  {
    $factor=0.030;
    $width=40;
    $logopos = 210/2-$width/2;
    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 13,$width);
    $this->pdf->SetTextColor(0,0,0);
  }
  $this->pdf->setY(64);

  $this->pdf->SetWidths(array(210-$this->pdf->marge*2));
  $this->pdf->SetAligns(array("C"));
  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
  $this->pdf->row(array("Specificatie factuur ".$extraVeldInfo['naam']));
  $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
  $this->pdf->ln(2);
  $this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
  $this->pdf->ln(2);

  $this->pdf->SetWidths(array($extraMarge,80+30,10,25));
  $this->pdf->SetAligns(array("L","L",'R','R'));



  if($extraPortefeuilleInfo['valutaUitsluiten']==1)
  {
    foreach($this->waarden['waardeVerdeling'] as $portefeuille=>$gegevens)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',$crmData[$portefeuille]));
      $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
      $this->pdf->row(array('', vertaalTekst('Vermogen per ',$this->pdf->rapport_taal).' '.$rapportageDatum,'EUR',$this->formatGetal($gegevens['eindWaarde']['totaal'],2)));
      $this->pdf->row(array('', vertaalTekst('Liquiditeiten/deposito\'s per',$this->pdf->rapport_taal).' '.$rapportageDatum,'EUR',$this->formatGetal($gegevens['beginWaardeDetail']['rekening'],2)));
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
      $this->pdf->row(array('', vertaalTekst('Belastbaar vermogen per',$this->pdf->rapport_taal).' '.$rapportageDatum,'EUR',$this->formatGetal($gegevens['eindWaarde']['totaal']-$gegevens['beginWaardeDetail']['rekening'],2)));
      $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
      $this->pdf->ln();
    }
  }
  else
  { 
    foreach($this->waarden['portefeuilleVerdeling']['eindWaarde'] as $portefeuille=>$waarde)
    {
      if($portefeuille<>'totaal')
      {
        $this->pdf->SetFont($this->pdf->rapport_font, "B", $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', $crmData[$portefeuille]));
        $this->pdf->SetFont($this->pdf->rapport_font, "", $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', vertaalTekst('Vermogen per ', $this->pdf->rapport_taal) . ' ' . $rapportageDatum, 'EUR', $this->formatGetal($waarde, 2)));
        $this->pdf->SetFont($this->pdf->rapport_font, "B", $this->pdf->rapport_fontsize);
        $this->pdf->row(array('', vertaalTekst('Belastbaar vermogen per', $this->pdf->rapport_taal) . ' ' . $rapportageDatum, 'EUR', $this->formatGetal($waarde, 2)));
        $this->pdf->SetFont($this->pdf->rapport_font, "", $this->pdf->rapport_fontsize);
        $this->pdf->ln();
      }
    }
  }

  $totaal=0;
  $vastBedrag=0;
  if($this->waarden['BeheerfeeBedragVast']<>0)
  {
    $vastBedrag=$this->waarden['BeheerfeeBedragVast']/$this->waarden['BeheerfeeAantalFacturen'];
    $this->pdf->row(array('', vertaalTekst('Vaste fee', $this->pdf->rapport_taal), 'EUR', $this->formatGetal($vastBedrag, 2)));
    $this->pdf->ln();
  }

  $this->pdf->SetWidths(array($extraMarge,80,30,10,25));
  $this->pdf->SetAligns(array("L","L","R",'R','R'));

 // $this->pdf->row(array('', vertaalTekst('Percentage',$this->pdf->rapport_taal) ,vertaalTekst('Beschrijving',$this->pdf->rapport_taal) ,'',vertaalTekst('Bedrag',$this->pdf->rapport_taal)));

 // $beginWaarde=0;
  foreach($this->waarden['staffelWaarden'] as $index=>$staffelData)
  {


    $waardeAlles=$staffelData['staffelEind']-$eindwaarde;
    $waardeStaffel=$waardeAlles*$staffelData['percentage']/100/$this->waarden['BeheerfeeAantalFacturen'];
    $this->pdf->row(array('',vertaalTekst('Fee',$this->pdf->rapport_taal).' < EUR '.$this->formatGetal($staffelData['staffelEind'],2)." (".$this->formatGetal($staffelData['percentage'],2)."%)",
                             "EUR ".$this->formatGetal($waardeAlles,2),
                             'EUR',
                              $this->formatGetal($waardeStaffel,2)));


   // $this->pdf->row(array('',$this->formatGetal($staffelData['percentage'],3),
   //                   vertaalTekst('Over',$this->pdf->rapport_taal).' '.$this->formatGetal($staffelData['staffelBegin'],2).' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.$this->formatGetal($staffelData['staffelEind'],2),
   //                   'EUR',$this->formatGetal($staffelData['feeDeel'],2)));
   $totaal+=$waardeStaffel;
   $eindwaarde=$staffelData['staffelEind'];
   // $beginWaarde=$staffelData['waarde'];

  }
  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
  $this->pdf->CellBorders=array('','T','T','T','T');
  $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),'EUR '.$this->formatGetal($eindwaarde,2),'EUR',$this->formatGetal($totaal,2)));
  $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
  unset($this->pdf->CellBorders);


  $this->pdf->ln();
  $this->pdf->SetWidths(array($extraMarge,80+30,10,25));
  $this->pdf->SetAligns(array("L","L",'R','R'));
  foreach($this->waarden['portefeuilleVerdeling']['eindWaarde'] as $portefeuille=>$waarde)
  {
    if($portefeuille<>'totaal')
    {

      $aandeelFee   =($waarde/$this->waarden['portefeuilleVerdeling']['eindWaarde']['totaal']);

      $this->pdf->row(array('',$crmData[$portefeuille],'EUR',$this->formatGetal(($aandeelFee*$totaal)+$vastBedrag,2)));
    }
  }



  $extraVoettekst=true;
}
elseif($extraVeldInfo['FeeinfoSpecificatie']==1)
{
  $this->pdf->addPage('P');
  if(is_file($this->pdf->rapport_logo))
  {
    $factor=0.030;
    $width=40;
    $logopos = 210/2-$width/2;
    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 13,$width);
    $this->pdf->SetTextColor(0,0,0);
  }
  $this->pdf->setY(84);

  $this->pdf->SetWidths(array(210-$this->pdf->marge*2));
  $this->pdf->SetAligns(array("C"));
  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
  $this->pdf->row(array("Specificatie factuur ".$extraVeldInfo['naam']));
  $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
  $this->pdf->ln(2);
  $this->pdf->line($this->pdf->marge+$extraMarge,$this->pdf->getY(),210-($this->pdf->marge+$extraMarge),$this->pdf->getY());
  $this->pdf->ln(2);
  $this->pdf->SetAligns(array("L","L",'L','L','L','R'));
  $this->pdf->SetWidths(array($extraMarge, 35, 40,30,30,35));
  $this->pdf->row(array('', vertaalTekst('Tarief per jaar',$this->pdf->rapport_taal) , vertaalTekst('Totaal Vermogen',$this->pdf->rapport_taal),'Datum',vertaalTekst('Fee per kwartaal',$this->pdf->rapport_taal)));
  $this->pdf->ln(2);
  $this->pdf->row(array('', $this->formatGetal($this->waarden['BeheerfeePercentageVermogen'], 2) . "%","EUR " . $this->formatGetal($this->waarden['rekenvermogen'], 2),date('d-m-Y',$rapJul),'EUR '.$this->formatGetal($this->waarden['beheerfeeBetalen']-$somExtraFactuurRegels,2)));
  $extraVoettekst=true;
}

if($extraVoettekst==true)
{

  $this->pdf->AutoPageBreak=false;
  $this->pdf->SetWidths(array(210-$this->pdf->marge*2));
  $this->pdf->SetAligns(array("C"));
  $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize-1);
  if($this->waarden['Accountmanager']=='FL')
  {
    $this->pdf->line($this->pdf->marge+$extraMarge,297-(4*$this->pdf->rowHeight)-15,210-($this->pdf->marge+$extraMarge),297-(4*$this->pdf->rowHeight)-15);
    $this->pdf->setY(297-(4*$this->pdf->rowHeight));
    $this->pdf->row(array("Quercus Vermögensverwaltungs AG, Rittergasse 1, 4051 Basel, Switzerland"));
    $this->pdf->row(array("Telephone +41 (0)61 273 13 85, Telefax +41 (0)61 273 13 87, VAT. no 516 495"));
    $this->pdf->row(array("Basel Chamber of Commerce, reg. no. CH-270.3.012.802-8"));
  }
  else
  {
    $this->pdf->line($this->pdf->marge+$extraMarge,297-(5*$this->pdf->rowHeight)-15,210-($this->pdf->marge+$extraMarge),297-(5*$this->pdf->rowHeight)-15);
    $this->pdf->setY(297-(5*$this->pdf->rowHeight));
    $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
    $this->pdf->row(array("Box Consultants B.V."));
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->row(array("Burgemeester Mollaan 72, P.O. Box 60, 5580 AB Waalre, The Netherlands"));
    $this->pdf->row(array("Telephone +31 (0)88 600 16 00, Telefax +31 (0)88 600 16 01"));
    $this->pdf->row(array("Website www.boxconsultants.com E-mail info@boxconsultants.com"));
  }
  $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
  $this->pdf->AutoPageBreak=true;

}




?>