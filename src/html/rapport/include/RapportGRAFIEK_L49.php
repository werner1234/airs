<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/28 17:20:17 $
File Versie					: $Revision: 1.5 $

$Log: RapportGRAFIEK_L49.php,v $
Revision 1.5  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.4  2019/08/03 17:03:12  rvv
*** empty log message ***

Revision 1.3  2017/06/25 14:49:37  rvv
*** empty log message ***

Revision 1.2  2017/06/07 16:27:49  rvv
*** empty log message ***

Revision 1.1  2017/05/21 09:55:30  rvv
*** empty log message ***

Revision 1.6  2017/05/13 16:27:34  rvv
*** empty log message ***

Revision 1.5  2016/04/23 15:33:07  rvv
*** empty log message ***

Revision 1.4  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.3  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.2  2013/12/14 17:22:13  rvv
*** empty log message ***

Revision 1.1  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.1  2013/06/05 15:56:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportGRAFIEK_L49
{

	function RapportGRAFIEK_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "GRAFIEK";
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
    if (is_array($this->pdf->__appvar['consolidatie']))
    {
      global $__appvar;
      $this->pdf->AddPage();
      //$this->maakNotities();
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);

      $DB=new DB();
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='".$this->rapportageDatum."' AND ".
        " portefeuille = '".$this->portefeuille."' "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      debugSpecial($query,__FILE__,__LINE__);
      $DB->SQL($query);
      $DB->Query();
      $totaalWaarde = $DB->nextRecord();
      $totaalWaarde = $totaalWaarde['totaal'];

      $query = "SELECT
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Portefeuilles.risicoklasse as risicoprofiel,
                Portefeuilles.clientVermogensbeheerder,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($tmp = $DB->nextRecord())
      {
        $portefeuilledata[$tmp['Portefeuille']] = $tmp;
      }
      $portefeuilleWaarden=array();
      $perProfiel=array();
      foreach ($portefeuilledata as $portefeuille=>$pdata)
      {
        if(substr($this->rapportageDatum,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;

        $waarden=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,$startjaar,$this->rapportageDatumVanaf);
        foreach ($waarden as $waarde)
        {
          $portefeuilleWaarden[$portefeuille]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
          $perProfiel[$pdata['risicoprofiel']]+=$waarde['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind;
        }
      }


      if(!is_array($this->pdf->grafiekKleuren))
      {
        $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
        $DB->SQL($q);
        $DB->Query();
        $kleuren = $DB->LookupRecord();
        $kleuren = unserialize($kleuren['grafiek_kleur']);
        $this->pdf->grafiekKleuren=$kleuren;
      }
      $randomKleur='';
      foreach($kleuren['OIB'] as $categorie=>$kleur)
      {
        $randomKleur[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
      }




      $portefeuilleKleur=array();
      $n=0;
      foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
      {
        $kleur='';
        if(unserialize($portefeuilledata[$portefeuille]['kleurcode']))
          $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
       
        if(!is_array($kleur)||($kleur[0]==''&&$kleur[1]=='' && $kleur[2]==''))
        {
          if(is_array($randomKleur[$n]) && ($randomKleur[$n][0]<>''||$randomKleur[$n][1]<>''||$randomKleur[$n][2]<>''))
          {
            $kleur=$randomKleur[$n];
            $n++;
          }
          else
          {
            $kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));
          }
        }

        if($portefeuilledata[$portefeuille]['clientVermogensbeheerder'] <> '')
          $omschrijving=$portefeuilledata[$portefeuille]['clientVermogensbeheerder']." ".$portefeuille;
        else
          $omschrijving=$portefeuilledata[$portefeuille]['depotbankOmschrijving']." ".$portefeuille;
  
        if($this->pdf->lastPOST['anoniem']==1)
        {
          $omschrijving=$portefeuilledata[$portefeuille]['depotbankOmschrijving'];
        }

        $portefeuilleAandeel[$omschrijving]=$waarde/$totaalWaarde*100;
        $portefeuilleKleur[]=$kleur;
      }

      $profielAandeel=array();
      $profielKleur=array();
      foreach($perProfiel as $profiel=>$waarde)
      {
        $profielAandeel[$profiel] = $waarde / $totaalWaarde * 100;
        $query="SELECT kleur FROM Risicoklassen WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND Risicoklasse='$profiel'";
        $DB->SQL($query);
        $DB->Query();
        $kleurenData = $DB->LookupRecord();
        $kleur = unserialize($kleurenData['kleur']);

        if(count($kleur)<>3)
          $kleur=array(rand(0,255),rand(0,255),rand(0,255));

        $profielKleur[]=$kleur;
      }

      $tmp=array(
      'grafiek'=>$portefeuilleAandeel,
      'grafiekKleur'=>$portefeuilleKleur
    );
    //  getTypeGrafiekData($this, 'regio');
      getTypeGrafiekData($this, 'beleggingssector');
    //  listarray($this->pdf->grafiekData);
      $this->pdf->setXY(20, $this->pdf->rapportYstart);
      $this->Categorieverdeling($tmp, $this->pdf->veldOmschrijvingen['regio'], 'Portefeuille');
      $this->pdf->setXY(160, $this->pdf->rapportYstart);

      $tmp=array(
        'grafiek'=>$profielAandeel,
        'grafiekKleur'=>$profielKleur
      );

     // $this->Categorieverdeling($this->pdf->grafiekData['beleggingssector'], $this->pdf->veldOmschrijvingen['beleggingssector'], 'Sector');
      $this->Categorieverdeling($tmp, $this->pdf->veldOmschrijvingen['beleggingssector'], 'Profiel');
    }
  }
  
  function maakNotities()
  {
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setY($this->pdf->rapportYstart);
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4,'Notites', 0, "L");
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->achtergrondlijn[0],$this->pdf->achtergrondlijn[1],$this->pdf->achtergrondlijn[2]),'dash'=>0));
    $stappen=26;
    $yStart=$this->pdf->rapportYstart+4;
    $yStop=190;
    $w=140;
    $stap=($yStop-$yStart)/$stappen;
    for($i=0;$i<=$stappen;$i++)
    {
      $this->pdf->Line($this->pdf->marge,$yStart+$i*$stap,$w,$yStart+$i*$stap);
    }
  }
  
  function switchColor($n)
  {
     $col1=$this->pdf->achtergrondLicht;
     $col2=$this->pdf->achtergrondDonker;

    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
  }
  
  
  
  function Categorieverdeling($data,$omschrijvingen,$titel)
	{
		global $__appvar;
    $startX=$this->pdf->GetX();
    $startY=$this->pdf->GetY();
    $this->pdf->setXY($startX,$this->pdf->rapportYstart);
    //$this->pdf->debug=true;

    PieChart($this->pdf,120, 70, $data['grafiek'], '%l', $data['grafiekKleur'],$titel.'verdeling '.getKwartaal($this->pdf->rapport_datum).' kwartaal '.date('Y',$this->pdf->rapport_datum),'R');
    $totalen=array();
    $witCell=$this->pdf->witCell;
    $this->pdf->setWidths(array($startX-20,100-$witCell,$witCell,20));
    $this->pdf->SetAligns(array('L','L','C','R'));
    $this->pdf->Ln(8);

	  $this->pdf->fillCell = array(0,1,0,1);
    $n=0;
	  foreach($data['grafiek'] as $categorie=>$percentage)
    {
      $this->switchColor($n);
      $n++;
      $this->pdf->row(array('',$categorie,'',
                            $this->formatGetal($percentage,0).'%'));
      $totalen['percentage']+=$percentage;

    }
    $this->switchColor($n);
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totalen['percentage'],0).'%'));
    unset($this->pdf->fillCell);
    checkPage($this->pdf);
    $eindY=$this->pdf->GetY();
  }
  
  
}



?>