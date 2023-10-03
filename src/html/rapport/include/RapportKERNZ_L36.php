<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/08/02 18:23:27 $
File Versie					: $Revision: 1.1 $

$Log: RapportKERNZ_L36.php,v $
Revision 1.1  2017/08/02 18:23:27  rvv
*** empty log message ***

Revision 1.6  2016/02/20 15:18:29  rvv
*** empty log message ***

Revision 1.5  2015/12/02 16:16:29  rvv
*** empty log message ***

Revision 1.4  2015/11/29 13:14:46  rvv
*** empty log message ***

Revision 1.3  2015/11/25 16:56:13  rvv
*** empty log message ***

Revision 1.2  2015/03/04 16:30:29  rvv
*** empty log message ***

Revision 1.22  2014/12/31 18:09:06  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportKERNZ_L36
{

	function RapportKERNZ_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalNegatief($waarde, $dec)
  {
    if($waarde<0)
      return 'Negatief!';
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



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->DB = new DB();

		// voor data
		$this->pdf->widthA = array(5,80,30,5,30,5,30,120);
		$this->pdf->alignA = array('L','L','R','L','R');

		// voor kopjes
		$this->pdf->widthB = array(0,85,30,5,30,5,30,120);
		$this->pdf->alignB = array('L','L','R','L','R');


		$this->pdf->AddPage('P');
    //$this->pdf->geenBasisFooter=true;
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $fontsize=$this->pdf->rapport_fontsize+2;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

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
CRM_naw.enOfRekening,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";

    $this->DB->SQL($query);
    $crmData = $this->DB->lookupRecord();
    $rowHeightbackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
    $this->pdf->SetY(25);
    $this->pdf->SetWidths(array(10,170));
    $this->pdf->SetAligns(array("L","L","L"));
    $this->pdf->row(array('',$crmData['naam']));
    if ($crmData['naam1'] !='')
      $this->pdf->row(array('',$crmData['naam1']));
    $this->pdf->row(array('',$crmData['verzendAdres']));
    $plaats='';
    if($crmData['verzendPc'] != '')
      $plaats .= $crmData['verzendPc']." ";
    $plaats .= $crmData['verzendPlaats'];
    $this->pdf->row(array('',$plaats));

    $this->pdf->SetY(60);
    $this->pdf->row(array('',"..,".date('Y')));
    $this->pdf->ln(12);

    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->row(array('',"Betreft: geschiktheidsrapport"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->ln(12);
    $this->pdf->row(array('',"Geachte relatie,"));
    $this->pdf->ln();
    $this->pdf->row(array('',"Ten minste jaarlijks verstrekken wij u een rapportage, teneinde te vast te stellen of het door ons verrichte vermogensbeheer nog geschikt voor u is. In dit verband beoordelen wij of uw huidige beleggingsprofiel en de daarop gebaseerde beleggingsportefeuille nog steeds passen bij uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze geschiktheidsbeoordeling stelt ons in staat te handelen in uw belang."));
    $this->pdf->ln();
    $this->pdf->row(array('',"Hieronder is weergegeven of de op u van toepassing zijnde uitgangspunten voor het vermogensbeheer, zoals weergegeven in het inventarisatieformulier, het beleggingsvoorstel en/of nadien door ons vastgelegde gegevens naar aanleiding van onze contacten met u, de afgelopen periode zijn gewijzigd."));
    $this->pdf->ln();

    $crmData=$this->ophalenCRMRecord();

    $this->pdf->CellBorders=array('',array('T','L','R'),array('T','R'));
    $this->pdf->SetWidths(array(10,85,85));
    $this->pdf->row(array('',"Beleggingsprofiel","Ongewijzigd: ".$crmData['risicoprofiel']));
    $this->pdf->CellBorders=array('',array('L','R','U'),array('R','U'));
    $this->pdf->row(array('',"Beleggingsdoelstelling"                                                  ,"Ongewijzigd: ".$crmData['beleggingsdoelstelling']));
    $this->pdf->row(array('',"Beleggingshorizon"                                                       ,"Ongewijzigd: ".$crmData['beleggingsHorizon']));
    $this->pdf->row(array('',"Netto rendementsdoelstelling op langere termijn"                         ,"Ongewijzigd: ".$crmData['rendementsdoelstelling']));
    $this->pdf->row(array('',"Persoonlijke gegevens cliënt (NAW, beroep, burgerlijke staat)"           ,"Ongewijzigd:"));
    $this->pdf->row(array('',"Financiële situatie (inkomsten, uitgaven, bezittingen, schulden)"        ,"Ongewijzigd:"));
    $this->pdf->row(array('',"Risicobereidheid (emotioneel)"                                           ,"Ongewijzigd: ".$crmData['risicobereidheid_emo']));
    $this->pdf->row(array('',"Risicobereidheid (financieel / mogelijkheden verliezen te kunnen dragen)","Ongewijzigd: ".$crmData['risicobereidheid_fin']));
    $this->pdf->row(array('',"Kennis en ervaring"                                                      ,"Ongewijzigd:"));
    $this->pdf->row(array('',"Ons beleggingsbeleid"                                                    ,"Ongewijzigd:"));
    $this->pdf->row(array('',"",""));
    unset($this->pdf->CellBorders);
    $this->pdf->addPage("P");
    $this->pdf->rowHeight=$rowHeightbackup;
    $this->getScenario($crmData['id']);

    $this->pdf->rowHeight=5;
    $this->pdf->SetWidths(array(10,170));
    $this->pdf->SetAligns(array("L","L","L"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->ln(10);
    $this->pdf->row(array('',"Gelet op het hierboven gestelde zijn wij van mening dat onze vermogensbeheerdienst en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) nog steeds geschikt voor u is. "));
    $this->pdf->ln();
    $this->pdf->row(array('',"Mochten één of meer uitgangspunten niet overeen komen met uw persoonlijke en/of financiële situatie, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of dat gevolgen heeft voor het beheer van uw portefeuille en/of de met u afgesloten overeenkomst."));
    $this->pdf->ln();
    $this->pdf->row(array('',"Wij vertrouwen erop u hiermee voldoende te hebben geïnformeerd en zijn desgewenst graag bereid tot het geven van een nadere toelichting."));
    $this->pdf->ln();
    $this->pdf->row(array('',"Met vriendelijke groet,"));
    $this->pdf->rowHeight=$rowHeightbackup;

	}

  function ophalenCRMRecord()
  {
    $gebruikteCrmVelden=array(
      'risicoprofiel',
      'beleggingsdoelstelling',
      'beleggingsHorizon',
      'rendementsdoelstelling',
      'risicobereidheid_emo',
      'risicobereidheid_fin');

    $db = new DB();
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($data=$db->nextRecord())
      $crmVelden[]=strtolower($data['Field']);

    $nawSelect='';
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
        $nawSelect.=",CRM_naw.$veld ";
      else
        $nietgevonden[]=$veld;
    }

    if(count($nietgevonden) > 0)
    {
      $this->pdf->MultiCell($this->colWidth, 5, 'Niet gevonden crm velden: ');
      for ($x=0; $x < count($nietgevonden);$x++)
      {
        $this->pdf->MultiCell($this->colWidth, 5, '-> '.$nietgevonden[$x]);
      }

    }

    $query="SELECT CRM_naw.id $nawSelect FROM CRM_naw WHERE CRM_naw.portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    return $crmData;
  }

  function getScenario($crmId)
  {
     global $__appvar;
    $yBegin=15;
    $sc= new scenarioBerekening($crmId);

    /*
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='".$this->rapportageDatum."' AND ".
        " portefeuille = '".$this->portefeuille."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB=new DB();
      $DB->SQL($query);
      $DB->Query();
      $totaalWaarde = $DB->nextRecord();
      $totaalWaarde = $totaalWaarde['totaal'];
      if($totaalWaarde==0 && $this->totaalWaarde <> 0)
      {
        $totaalWaarde = $this->totaalWaarde;
      }
    $sc->CRMdata['startvermogen']=$totaalWaarde;
    $sc->CRMdata['startdatum']=$this->rapportageDatum;

    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    */
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
    //

  //  $this->pdf->setY(80);
//    if($this->pdf->portefeuilledata['Layout']==5)
//    {
    $sc->overigeRisicoklassen();
    /*
    $this->pdf->widthA = array(175,30);
    $this->pdf->widthB = array(150,30,25,25,25,25);
    $this->pdf->alignB = array('L','L','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Risicoprofiel','Kans op doel','Pessimistisch','Normaal','Optimistisch'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    */

    $maxKansTmp=0;
    $kansData=$sc->berekenKansBijOpgehaaldeRisicoklassen();
    /*
    foreach($kansData['risicoklassen'] as $risicoklasse=>$klasseData)
    {
      //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      //$this->pdf->SetWidths($this->pdf->widthA);
      //$this->pdf->row(array('',"(".$klasseData['risicoklasseData']['afkorting'].")"));
      //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //$this->pdf->Ln(-4);
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->row(array('',$klasseData['risicoklasseData']['afkorting'],$this->formatGetal($klasseData['uitkomstKans']['kans'],0).'%',
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Pessimistisch']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Normaal']),
                        $this->formatGetalNegatief($klasseData['uitkomstKans']['scenarioEindwaarden']['Optimistisch'])));

    }
    */
    $grafiekData=$kansData['grafiekData'];
    if(count($kansData['beste'])>0)
      $besteProfiel=$kansData['beste'];
    else
      $besteProfiel=$kansData['maxKans'];

    /*
    $this->pdf->setXY(160,125);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(130,0,'Kans op behalen doelstelling bij diverse profielen',0,0,'C');
    $this->pdf->setXY(160,130);
    $this->scatterplot(130,50,$grafiekData,$sc->profieldata['maximaalRisicoprofielStdev'],$besteProfiel);
*/
    $sc= new scenarioBerekening($crmId,$besteProfiel['risicoklasse']);
    /*
    if($this->pdf->lastPOST['scenario_portefeuilleWaardeGebruik']==1 )
    {
      $sc->CRMdata['startvermogen']=$totaalWaarde;
      $sc->CRMdata['startdatum']=$this->rapportageDatum;
    }

    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $sc->ophalenHistorie($this->portefeuille);
    }
    */
    if(!$sc->loadMatrix())
      $sc->createNewMatix(true);
//
    $this->pdf->widthA = array(10,40,30,20);
    $this->pdf->alignA = array('L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setY($yBegin);
    $this->scenarioKleur=$sc->scenarioKleur;
    $aantalSimulaties=10000;
    $sc->berekenSimulaties(0,$aantalSimulaties);
    $sc->berekenDoelKans();
    $sc->berekenVerdeling();


    $this->pdf->setXY($this->pdf->marge,$yBegin);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Uitgangswaarden'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Beginwaarde',"€ ".$this->formatGetal($sc->CRMdata['startvermogen'])));
    $this->pdf->row(array('','Doelvermogen',"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    $this->pdf->row(array('','Startjaar',substr($sc->CRMdata['startdatum'],0,4)));
    $this->pdf->row(array('','Doeljaar',substr($sc->CRMdata['doeldatum'],0,4)));
    $this->pdf->row(array('','Berekend profiel',$sc->CRMdata['gewenstRisicoprofiel']));
    $this->pdf->row(array('','Maximaal risicoprofiel',$sc->CRMdata['maximaalRisicoprofiel']));
    $this->pdf->row(array('','Verwacht rendement',$this->formatGetal(($sc->profieldata['verwachtRendement']-1)*100,1).'%'));
    $this->pdf->row(array('','Standaarddeviatie',$this->formatGetal($sc->profieldata['klasseStd']*100,1).'%'));
    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setY($yBegin+75);
    $this->pdf->widthB = array(10,40,30,20);
    $this->pdf->alignB = array('L','L','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->row(array('','Conclusies'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Kans op doelvermogen',$this->formatGetal($sc->doelKans,0).'%'));
    $this->pdf->row(array('','Risicoprofiel:',$sc->CRMdata['gewenstRisicoprofiel']));
    $this->pdf->row(array('','Doelvermogen:',"€ ".$this->formatGetal($sc->CRMdata['doelvermogen'])));
    //$this->pdf->row(array('','Gemiddeld eindvermogen',"€ ".$this->formatGetalNegatief($kansData['risicoklassen'][$sc->CRMdata['gewenstRisicoprofiel']]['uitkomstKans']['scenarioEindwaarden']['Normaal'])));
    $this->startJaar=$sc->CRMdata['startdatum'];


    foreach($sc->cashflow as $jaar=>$waarde)
      $cashflow[$jaar]['scenario']=$waarde;

    $this->pdf->setXY(30,$yBegin+100);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(125,0,'Scenario-analyse',0,0,'C');
    $this->pdf->setXY(30,$yBegin+105);
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      $scenarios=array_keys($sc->scenarioGemiddelde);
      $i=0;
      $laatsteJaar=2000;
      unset($this->startJaar);
      foreach($sc->werkelijkVerloop as $jaar=>$data)
      {
        $cashflow[$jaar]['werkelijk']=$data['stortingen'];
        if(!isset($this->startJaar))
          $this->startJaar=$jaar;
        if($jaar<$sc->CRMdata['startdatum'])
        {
          foreach($scenarios as $scenario)
            $grafiek[$scenario][]=$sc->CRMdata['startvermogen'];
        }
        else
        {
          foreach($scenarios as $scenario)
            $grafiek[$scenario][]=$sc->scenarioGemiddelde[$scenario][$i];
          $i++;
        }
        $laatsteJaar=$jaar;
      }

      foreach($sc->scenarioGemiddelde as $scenario=>$waarden)
      {
        foreach($waarden as $index=>$waarde)
        {
          if($sc->CRMdata['startdatum']+$index > $laatsteJaar)
            $grafiek[$scenario][]=$sc->scenarioGemiddelde[$scenario][$index];
        }
      }
      $this->LineDiagram(125,50,$grafiek,$sc->werkelijkVerloop,$sc->CRMdata['doelvermogen']);
    }
    else
      $this->LineDiagram(125,50,$sc->scenarioGemiddelde,'',$sc->CRMdata['doelvermogen']);


    $this->pdf->setY($yBegin);
    $n=0;
    if($this->pdf->lastPOST['scenario_werkelijkVerloop']==1)
    {
      ksort($cashflow);
      $this->pdf->widthB = array(80,18,20,20);
      $this->pdf->alignB = array('L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Cashflow'));
      $this->pdf->row(array('','Jaar','werkelijk €','scenario €'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      foreach($cashflow as $jaar=>$bedragen)
      {
        if($n > 5)
        {
          $cashflowOverig['werkelijk']+=$bedragen['werkelijk'];
          $cashflowOverig['scenario']+=$bedragen['scenario'];
        }
        else
          $this->pdf->row(array('',$jaar,$this->formatGetal($bedragen['werkelijk']),$this->formatGetal($bedragen['scenario'])));
        $n++;
      }
      if(isset($cashflowOverig))
        $this->pdf->row(array('','Restant',$this->formatGetal($cashflowOverig['werkelijk']),$this->formatGetal($cashflowOverig['scenario'])));
    }
    else
    {
      $this->pdf->widthB = array(100,18,25);
      $this->pdf->alignB = array('L','L','R','R');
      $this->pdf->SetWidths($this->pdf->widthB);
      $this->pdf->SetAligns($this->pdf->alignB);
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('','Cashflow'));
      $this->pdf->row(array('','Jaar','Bedrag in €'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($sc->cashflowText as $bedragData)
      {
        $this->pdf->row(array('',$bedragData[0],$this->formatGetal($bedragData[1],0)));
      }
    }



    $this->pdf->setY($yBegin+40);
    $this->pdf->widthB = array(15,35,30,30,30);
    $this->pdf->alignB = array('L','L','R','R','L');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',"\nScenario ".$sc->CRMdata['gewenstRisicoprofiel'],"\nKans","Eindvermogen\ntenminste",''));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($sc->verwachteWaarden as $scenario=>$eindvermogen)
    {
      $kleur=$this->scenarioKleur[$scenario];
      $this->pdf->Rect($this->pdf->getX()+15-3,$this->pdf->GetY()+1, 2, 2 ,'F','',$kleur);
      $this->pdf->row(array('',$scenario,$this->formatGetal( round((100-$sc->scenarios[$scenario])/5)*5,0).'%',$this->formatGetalNegatief($eindvermogen)));
    }

    $this->pdf->setY($yBegin+165);

  }


  function LineDiagram($w, $h, $data,$werkelijkVerloop,$doelVermogen)
  {
    global $__appvar;
    $color=null; $maxVal=0; $minVal=10000000; $horDiv=5; $verDiv=4;$jaar=0;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage  ;
    $lDiag = $w;

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    $aantalPunten=array();
    foreach($data as $reeks=>$waarden)
    {
      $tmp=ceil(max($waarden));
      if($tmp > $maxVal)
        $maxVal = $tmp;

      $tmp = floor(min($waarden));
      if($tmp < $minVal)
        $minVal=$tmp;

      foreach($waarden as $index=>$waarde)
        $aantalPunten[$index]=$index;
    }

    foreach($werkelijkVerloop as $jaar=>$waarden)
    {
      if($waarden['waarde'] > $maxVal)
        $maxVal = $waarden['waarde'];

      if($waarden['waarde'] < $minVal)
        $minVal=$waarden['waarde'];
    }

    if($minVal < 0)
      $minVal=0;

    if ($maxVal < 0)
      $maxVal = 1;


    $procentWhiteSpace = 0.1;
    $band=($maxVal - $minVal);
    $stepSize=round($band / $horDiv);
    //echo $band;exit;

    $stepSize=ceil($stepSize/(pow(10,strlen($stepSize))*5))*pow(10,strlen($stepSize))/5;
    $maxVal = ceil($maxVal * (1 + ($procentWhiteSpace))/$stepSize)*$stepSize;
    $minVal = floor($minVal * (1 - (0.3))/$stepSize)*$stepSize;

    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / (count($aantalPunten)-1);

    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);

    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ."");

      $this->pdf->setXY($XDiag-20, $i);
      if($n==0)
        $waarde=$minVal;
      else
        $waarde=0-($n*$stapgrootte);

      $this->pdf->Cell(20,0, $this->formatGetal($waarde,0)."", 0,0, "R");

      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      if($n*$stapgrootte >= $minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          //  $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ."");
          $this->pdf->setXY($XDiag-20, $i);
          $this->pdf->Cell(20,0, $this->formatGetal($n*$stapgrootte,0)."", 0,0, "R");
        }
      }
      $n++;

      if($n >20)
        break;
    }

    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $circleStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255,255,255));

    // $color=array(200,0,0);
    $datumPrinted=array();
    $xcorrectie=$unit;
    $data=array_reverse($data);
    $reeksCount=0;
    $lastReeks=count($data)-1;
    $polly=array();
    $pollyReverse=array();
    foreach($data as $reeks=>$waarden)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);

      $lines[$reeks]=array();
      $marks[$reeks]=array();

      //$polly[]=$XDiag;
      //$polly[]=$bodem;
      if(count($waarden)> 20)
        $modi=2;
      else
        $modi=1;

      for ($i=0; $i<count($waarden); $i++)
      {
        if($waarden[$i] < 0)
          $waarden[$i]=0;

        if(!isset($datumPrinted[$i]))
        {
          if($i%$modi==0)
            $this->pdf->TextWithRotation($XDiag+($i*$unit)-2,$YDiag+$hDiag+8,$this->startJaar+$i,25);
          $datumPrinted[$i]=1;
        }

        $yval2 = $YDiag + (($maxVal-$waarden[$i]) * $waardeCorrectie) ;

        if($i==0)
        {
          $yval = $bodem ;
        }
        else
        {

          //$this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
          $lines[$reeks][]=array($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2);
          $marks[$reeks][]=array($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5);
          //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5-$xcorrectie, $yval2-0.5, 1, 1 ,'F','',$color);
          if($reeksCount==0)
          {
            $polly[]=$XDiag+$i*$unit-$xcorrectie;
            $polly[]=$yval;
            $polly[]=$XDiag+($i+1)*$unit-$xcorrectie;
            $polly[]=$yval2;
          }
          elseif($reeksCount==$lastReeks)
          {
            $pollyReverse[]=$yval;
            $pollyReverse[]=$XDiag+$i*$unit-$xcorrectie;
            $pollyReverse[]=$yval2;
            $pollyReverse[]=$XDiag+($i+1)*$unit-$xcorrectie;

          }

        }
        $yval = $yval2;
      }

      $reeksCount++;
      //$polly[]=$XDiag+$w;
      // $polly[]=$bodem;
      //  $this->pdf->Polygon($polly, 'F', null, $color) ;
    }
    $pollyReverse=array_reverse($pollyReverse);
    // listarray($polly);
    foreach($pollyReverse as $value)
      $polly[]=$value;
    // listarray($polly);
    $this->pdf->Polygon($polly, 'F', null, array(200,200,200)) ;


    foreach($lines as $reeks=>$lineData)
    {
      $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
      $lineStyle = array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($lineData as $line)
      {
        $this->pdf->line($line[0],$line[1],$line[2],$line[3],$lineStyle);
      }
    }



    foreach($marks as $reeks=>$markData)
    {
      foreach($markData as $mark)
      {
        $color=array($this->scenarioKleur[$reeks][0],$this->scenarioKleur[$reeks][1],$this->scenarioKleur[$reeks][2]);
        $r=0.5;
        $this->pdf->Circle($mark[0]+$r,$mark[1]+$r, $r, 0,360, $style = 'DF', $circleStyle, $color);
      }
    }





    $yval = $YDiag + (($maxVal-$doelVermogen) * $waardeCorrectie) ;
    $xval=$XDiag+(count($waarden))*$unit-0.5-$xcorrectie+$r;
    $circleStyle = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $this->pdf->Circle($xval,$yval, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));

    $this->pdf->Circle($XDiag,$YDiag+$h+10, $r, 0,360, $style = 'DF', $circleStyle, array(0,0,0));
    $this->pdf->TextWithRotation($XDiag+2,$YDiag+$h+10+1,"Doelvermogen",0);

    $lineStyle = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0));
    $i=0;
    foreach($werkelijkVerloop as $jaar=>$waarden)
    {
      $yval2 = $YDiag + (($maxVal-$waarden['waarde']) * $waardeCorrectie) ;
      if($i==0)
      {
        $yval = $bodem ;
      }
      else
      {
        $this->pdf->line($XDiag+$i*$unit-$xcorrectie, $yval, $XDiag+($i+1)*$unit-$xcorrectie, $yval2,$lineStyle );
      }
      $yval = $yval2;
      $i++;
    }




    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

 
  
}
?>