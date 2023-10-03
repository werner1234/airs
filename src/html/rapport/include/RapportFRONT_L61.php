<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/10 15:50:36 $
File Versie					: $Revision: 1.16 $

$Log: RapportFRONT_L61.php,v $
Revision 1.16  2019/04/10 15:50:36  rvv
*** empty log message ***

Revision 1.15  2017/05/10 14:44:58  rvv
*** empty log message ***

Revision 1.14  2017/01/21 17:48:04  rvv
*** empty log message ***

Revision 1.13  2016/11/23 16:55:20  rvv
*** empty log message ***

Revision 1.12  2016/10/30 13:02:59  rvv
*** empty log message ***

Revision 1.11  2016/10/29 15:41:46  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/08/21 08:52:52  rvv
*** empty log message ***

Revision 1.8  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.7  2016/01/28 15:27:48  rvv
*** empty log message ***

Revision 1.6  2016/01/28 15:00:18  rvv
*** empty log message ***

Revision 1.5  2016/01/17 18:10:27  rvv
*** empty log message ***

Revision 1.4  2015/11/11 17:28:10  rvv
*** empty log message ***

Revision 1.3  2015/11/08 16:35:01  rvv
*** empty log message ***

Revision 1.2  2015/10/04 11:52:21  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***

Revision 1.4  2015/05/31 10:15:24  rvv
*** empty log message ***

Revision 1.3  2015/03/01 14:08:16  rvv
*** empty log message ***

Revision 1.2  2015/02/18 17:09:13  rvv
*** empty log message ***

Revision 1.1  2015/02/15 10:26:57  rvv
*** empty log message ***

Revision 1.9  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.8  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.7  2013/07/10 16:01:24  rvv
*** empty log message ***

Revision 1.6  2013/06/09 18:01:53  rvv
*** empty log message ***

Revision 1.5  2012/10/24 15:45:39  rvv
*** empty log message ***

Revision 1.4  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.3  2012/10/07 14:57:17  rvv
*** empty log message ***

Revision 1.2  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***

Revision 1.2  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L61
{
	function RapportFront_L61($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FRONT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_OIS_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_FRONT_titel;
		else
			$this->pdf->rapport_titel = "Titel pagina";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
		$this->rapportageDatum = $rapportageDatum;
		$this->rapportageDatumJul=db2jul($this->rapportageDatum);
		$this->pdf->rapportCounter = count($this->pdf->page);

		$this->DB = new DB();
    $this->fontsize=$this->pdf->rapport_fontsize;

	}

	function voorBrief()
	{
	  global $__appvar;

    if($_POST['crmAfdruk'] > 0 || isset($_GET['counter']) || $this->pdf->selectData['type']=='portaal') //crm of frontoffice afdruk.
      return;

		$this->pdf->AddPage('P');
  	$this->pdf->frontPage = true;
    $this->pdf->emailSkipPages[]=$this->pdf->page;

		if(file_exists(FPDF_FONTPATH.'TCM_____.php'))
		{
  	  if(!isset($this->pdf->fonts['twcenmt']))
	    {
        $this->pdf->AddFont('twcenmt','','TCM_____.php');
        $this->pdf->AddFont('twcenmt','B','TCB_____.php');
        $this->pdf->AddFont('twcenmt','I','TCMI____.php');
        $this->pdf->AddFont('twcenmt','BI','TCBI____.php');
	    }
		 $font = 'twcenmt';
     $fontsize=$this->fontsize;
	  }

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $logoYpos=10;
		  //$xSize=70;
      $factor=0.065;
      $xSize=837*$factor;//1500 837
	    $this->pdf->Image($this->pdf->rapport_logo,210/2-$xSize/2, $logoYpos, $xSize);
      $this->pdf->SetXY(40,45);
      $this->pdf->SetFont($font,"",$fontsize);
      $this->pdf->Cell(100,4,'Wilhelminakade 1, 3072 AP  ROTTERDAM',0,1,'L');
 		}
    $this->pdf->SetY(55);
    $this->pdf->SetWidths(array(85,50,60));
    $this->pdf->SetAligns(array("L","R","L"));
    $this->pdf->SetFont($font,"",$fontsize);
    $this->pdf->Ln();
    $this->pdf->row(array('','Bezoekadres:',"Maastoren 43rd floor\nWilhelminakade 1, 3072 AP  ROTTERDAM"));
    $this->pdf->row(array('','Telefoon','+31 (0)10 302  71 00'));
    $this->pdf->row(array('','E-mail','info@blauwtulp.com'));
    $this->pdf->row(array('','Internetadres','www.blauwtulp.com'));
    $this->pdf->row(array('','Bankrekening','NL83 INGB 0007 3516 11'));

    $vanjul=$this->rapportageDatumVanafJul;
    $totjul=$this->rapportageDatumJul;

   	$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",$totjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$totjul)],$this->pdf->rapport_taal)." ".date("Y",$totjul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");
    $kwartaal=ceil(date("n",$totjul)/3);


   $this->DB = new DB();
   $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres as adres,
CRM_naw.verzendPc as pc,
CRM_naw.verzendPlaats as plaats,
CRM_naw.verzendLand as land,
CRM_naw.verzendAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    //$crmData=array('naam'=>'naam','naam1'=>'naam1','adres'=>'adres','pc'=>'1111aa','plaats'=>'plaats','land'=>'land');
    $extraMarge=40-$this->pdf->marge;
    $rowBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
		$this->pdf->SetY(55);
		$this->pdf->SetWidths(array($extraMarge,150));
    $this->pdf->SetFont($font,"",11);
		$this->pdf->SetAligns(array("R","L","L"));
		$this->pdf->row(array('',$crmData['naam']));
    $this->pdf->ln(1);
		if (trim($crmData['naam1']) !='')
    {
		  $this->pdf->row(array('',$crmData['naam1']));
      $this->pdf->ln(1);
		}
    $this->pdf->row(array('',$crmData['adres']));
    $this->pdf->ln(1);
		$plaats='';
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '')
      $plaats.="  ".$crmData['plaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$crmData['land']));

    $this->pdf->SetY(105);
    $this->pdf->SetFont($font,"",$fontsize);
    $this->pdf->row(array('Datum'));
    $this->pdf->SetY(105);
    $this->pdf->SetFont($font,"",11);
    $this->pdf->row(array('',$nu));

    $this->pdf->SetY(115);
    $this->pdf->SetFont($font,"",$fontsize);
    $this->pdf->row(array('Onderwerp'));
    $this->pdf->SetY(115);
    $this->pdf->SetFont($font,"B",11);
    $kwartalen = array('null','eerste','tweede','derde','vierde');
    $this->pdf->row(array('','Vermogensrapportage '.$kwartalen[$kwartaal].' kwartaal '.date('Y',$totjul)));
    $this->pdf->SetFont($font,"",11);

$this->pdf->SetY(125);

$txt=$crmData['verzendAanhef'].",

";

//Bijgaand treft u aan de overzichten per ultimo ".vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".date("Y").". Indien u vragen en/of opmerkingen heeft horen wij dat graag van u.
if($this->pdf->portefeuilledata['txtKoppeling'] !='')
{
  $koppeling = stripslashes($this->pdf->portefeuilledata[$this->pdf->portefeuilledata['txtKoppeling']]);
  $koppeling = stripslashes($koppeling);
  $query = "SELECT * FROM custom_txt WHERE
  type = '".$this->pdf->portefeuilledata['txtKoppeling']."' AND
  field = '".$this->pdf->rapport_type."_".$koppeling."' AND
  Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'  ";
  $this->DB->SQL($query);
  $txtData = $this->DB->lookupRecord();
  $titel = $txtData['title'];
  $briefData = html_entity_decode(strip_tags($txtData['txt']));
  $txt .= $briefData."\nMet vriendelijke groet,\n".$this->pdf->portefeuilledata['AccountmanagerNaam']."\n\n\n".$this->pdf->portefeuilledata['VermogensbeheerderNaam'];
}

$this->pdf->row(array('',$txt));
$this->pdf->ln();
$this->pdf->rowHeight=$rowBackup;


      $this->pdf->SetY(260);
      $this->pdf->SetFont($font,"",7);
$this->pdf->SetAligns(array('L','R'));
   $this->pdf->row(array('',"Blauwtulp B.V. staat ingeschreven bij de K.v.K. onder nr. 30222517"));

   //$this->pdf->Rect($this->pdf->marge+$extraMarge,$beginY,210-($extraMarge+$this->pdf->marge)*2,$this->pdf->GetY()-$beginY);

  $this->pdf->SetTextColor(0,0,0);
  $this->pdf->AddPage('P');
  $this->pdf->emailSkipPages[]=$this->pdf->page;


  }

	function writeRapport()
	{
		global $__appvar;

    if($this->pdf->selectData['allInOne']==1)
    {
      $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
    }
    else
    {
	  	if ((count($this->pdf->pages) % 2) && $this->pdf->selectData['type'] != 'eMail')
	  	{
	  	  $this->pdf->frontPage=true;
    	  $this->pdf->AddPage($this->pdf->CurOrientation);
        $this->pdf->emailSkipPages[]=$this->pdf->page;
		  }
    }

		if($this->pdf->selectData['type'] != 'eMail')
		  $this->voorBrief();

		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.07;//0.09;//0.522
      $xSize=837*$factor;//1500 837
      $ySize=400*$factor;//261 400
      $logopos=(297/2)-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 5, $xSize, $ySize);
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');

		$fontsize = $this->fontsize;//10; //$this->pdf->rapport_fontsize

    


    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $this->pdf->SetY(40);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam']));
    if($this->pdf->portefeuilledata['Naam1'] <> '')
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('',$this->pdf->portefeuilledata['Naam1']));
    }
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Adres']));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$this->pdf->portefeuilledata['Woonplaats']));
    
		$this->pdf->SetY(75);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);


    $this->pdf->SetWidths(array(30,40,5,120));
    $this->pdf->row(array('',vertaalTekst('Verslagperiode',$this->pdf->rapport_taal),":",$rapportagePeriode));
		$this->pdf->ln();
		if($this->consolidatie==true)
    {
      foreach($this->pdf->portefeuilles as $i=>$portefeuille)
      {
  
          $query = "SELECT
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet
		          FROM
		            Portefeuilles
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";
    
          $this->DB->SQL($query);
          $this->DB->Query();
          $portefeuilledata = $this->DB->nextRecord();
    

        
        if($i==0)
          $this->pdf->row(array(' ', vertaalTekst('Vermogensrapportage', $this->pdf->rapport_taal), ':', formatPortefeuille($portefeuille).' / '.$portefeuilledata['Depotbank']));
        else
          $this->pdf->row(array(' ','', '', formatPortefeuille($portefeuille).' / '.$portefeuilledata['Depotbank']));
      }
    }
    else
    {
      $this->pdf->row(array(' ', vertaalTekst('Vermogensrapportage', $this->pdf->rapport_taal), ':', formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
    }
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Mandaat',$this->pdf->rapport_taal),':',vertaalTekst($this->pdf->portefeuilledata['Risicoklasse'],$this->pdf->rapport_taal)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 




		$this->pdf->SetY(133);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
		$this->pdf->row(array('',vertaalTekst('Datum',$this->pdf->rapport_taal).': '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln(16);


    $query="SELECT Vermogensbeheerders.adres,
Vermogensbeheerders.woonplaats,
Vermogensbeheerders.telefoon,
Vermogensbeheerders.email,
Vermogensbeheerders.website FROM Vermogensbeheerders WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $this->DB->SQL($query);
    $verm = $this->DB->lookupRecord();
    foreach($verm as $key=>$value)
    {
      $this->pdf->ln(1);
      $this->pdf->row(array('', $value));
    }



	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}
}
?>
