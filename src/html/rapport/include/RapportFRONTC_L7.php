<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/08/17 18:24:30 $
File Versie					: $Revision: 1.19 $

$Log: RapportFRONTC_L7.php,v $
Revision 1.19  2019/08/17 18:24:30  rvv
*** empty log message ***

Revision 1.18  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.17  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.16  2016/04/10 15:48:34  rvv
*** empty log message ***

Revision 1.15  2016/04/06 15:30:51  rvv
*** empty log message ***

Revision 1.14  2016/04/03 10:58:02  rvv
*** empty log message ***

Revision 1.13  2016/03/31 06:30:24  rvv
*** empty log message ***

Revision 1.12  2016/03/30 10:35:05  rvv
*** empty log message ***

Revision 1.11  2016/03/28 15:53:33  rvv
*** empty log message ***

Revision 1.10  2016/03/27 17:35:07  rvv
*** empty log message ***

Revision 1.9  2016/03/16 14:24:20  rvv
*** empty log message ***

Revision 1.8  2016/02/17 19:35:26  rvv
*** empty log message ***

Revision 1.7  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.6  2015/12/23 16:21:44  rvv
*** empty log message ***

Revision 1.5  2015/12/21 08:22:32  rvv
*** empty log message ***

Revision 1.4  2015/12/20 16:47:30  rvv
*** empty log message ***

Revision 1.3  2015/05/06 15:35:15  rvv
*** empty log message ***

Revision 1.2  2013/04/17 16:00:15  rvv
*** empty log message ***

Revision 1.1  2012/08/01 16:57:55  rvv
*** empty log message ***

Revision 1.1  2011/12/21 19:19:33  rvv
*** empty log message ***

Revision 1.3  2011/02/24 17:46:56  rvv
*** empty log message ***

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFrontC_L7
{
	function RapportFrontC_L7($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->rapportCounter = count($this->pdf->page);
    
		$this->DB = new DB();

	}


	function writeRapport()
	{
		global $__appvar;

if($this->pdf->lastPOST['anoniem']==1)
  $portefeuilledata=array();
else  
{
		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                CRM_naw.verzendaanhef,
                CRM_naw.naam as crmNaam,
                CRM_naw.naam1 as crmNaam1
		          FROM
		            Portefeuilles
                JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                JOIN Accountmanagers ON Accountmanagers.Accountmanager = Portefeuilles.Accountmanager 
                JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
                LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
              ORDER BY Clienten.Naam,Portefeuilles.Portefeuille  ";
		$this->DB->SQL($query);
		$this->DB->Query();
    $allePortefeuilles=array();
    while($data=$this->DB->nextRecord())
    {
      if($data['Depotbank']=='TGB')
        $data['Depotbank']='IGS';
      if($data['crmNaam'] <> '')
      {
        $data['Naam']=$data['crmNaam'];
        $data['Naam1']=$data['crmNaam1'];
      }
      if($this->pdf->portefeuilles[0]==$data['Portefeuille'])
      		$portefeuilledata=$data;
      
      $allePortefeuilles[]=$data;  
    }
    
    $query="SELECT verzendaanhef,naam,naam1 FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
		$this->DB->SQL($query);
		$this->DB->Query();
    $crmRecord=$this->DB->nextRecord();
    if($crmRecord['verzendaanhef']<>'')
      $portefeuilledata['verzendaanhef']=$crmRecord['verzendaanhef'];
    else
      $portefeuilledata['verzendaanhef']='relatie';  
      
    if($crmRecord['naam']<>'')
    {
      $portefeuilledata['Naam']=$crmRecord['naam'];
      $portefeuilledata['Naam1']=$crmRecord['naam1'];
			/*
			$portefeuilledata['Naam']=$this->pdf->portefeuilledata['Naam'];
			$portefeuilledata['Naam1']=$this->pdf->portefeuilledata['Naam1'];
			$portefeuilledata['Adres']=$this->pdf->portefeuilledata['Adres'] ;
			$portefeuilledata['Woonplaats']=$this->pdf->portefeuilledata['Woonplaats'];
			$portefeuilledata['Land']=$this->pdf->portefeuilledata['Land'];
			*/
    }
}
   //background
$this->pdf->AddPage();


/////



   	$this->pdf->widthA = array(15,250);
		$this->pdf->alignA = array('L','L','L');
 //array('r'=>180,'g'=>174,'b'=>167)
		$this->pdf->SetFillColor(195,190,184);// 
    $this->pdf->Polygon(array(0,0, 297,0, 297,85, 0,150),true,"D",array(195,190,184));
    
    
	//	$this->pdf->Rect(0, 0, 297, 124, 'F');
  /*
		$this->pdf->SetFillColor(17,86,140);
		$this->pdf->Rect(0, 124, 297, 18, 'F');
		$this->pdf->SetFillColor(85,147,90);
		$this->pdf->Rect(0, 124+18, 297, 4.25, 'F');
		$this->pdf->SetFillColor(255,255,255);
*/
		$fontsize = 16; //$this->pdf->rapport_fontsize
		$this->pdf->setWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetY(128);
		$kwartaal = floor(date("n",$this->rapportageDatumJul)/3).'de';
	  if($kwartaal=="0de")
	    $kwartaal="1ste";
	  if($kwartaal=="1de")
	    $kwartaal="1ste";
  
    $kwartaal=vertaalTekst($kwartaal,$this->pdf->rapport_taal);
	  $this->pdf->SetTextColor(61,82,101);
    $this->pdf->SetY(150);
    $this->pdf->SetAligns(array('L','C','L'));
	  $this->pdf->row(array('',$portefeuilledata['Naam']." ".$portefeuilledata['Naam1']));
	  $this->pdf->ln(3);
	  $this->pdf->row(array('',vertaalTekst("Geconsolideerde rapportage ",$this->pdf->rapport_taal)." $kwartaal ".vertaalTekst("kwartaal",$this->pdf->rapport_taal)." ".date("Y",$this->rapportageDatumJul)));
		$this->pdf->ln(15);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

    wiltonLogo($this->pdf,123,$this->pdf->GetY());
    /*
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
    $this->pdf->SetTextColor(61,82,101);
    $this->pdf->Cell(133,4,'Wilton',0,0,'R');
    $this->pdf->SetTextColor(201,134,89);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->Cell(50,4,'Family Office',0,0,'L');
    */
    
    $this->pdf->SetTextColor(61,82,101);
    $this->pdf->ln(6);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-4);
		//$this->pdf->row(array('','   '.date("d")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y").
    //                    ' - '.vertaalTekst('Strikt persoonlijk en vertrouwelijk',$this->pdf->rapport_taal)));
		$this->pdf->row(array('',vertaalTekst('Strikt persoonlijk en vertrouwelijk',$this->pdf->rapport_taal)));
		$this->pdf->SetY(191);
		$this->pdf->widthA = array(5,260);
		$this->pdf->setWidths($this->pdf->widthA);
		//$this->pdf->row(array('',vertaalTekst('Wilton Investment Services staat geregistreerd als beleggingsonderneming bij de Autoriteit Financi�le Markten',$this->pdf->rapport_taal)));

		if(is_file($this->pdf->rapport_logo))
		{
			 $factor=0.025*(922/840);
			 $x=840*$factor;
			 $y=837*$factor;
			 $this->pdf->Image($this->pdf->rapport_logo, 250, 165, $x, $y);
		}
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  $this->pdf->frontPage = true;
    $this->pdf->rapport_type = "FRONT2";
    

    $this->pdf->AddPage();
    $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    $this->pdf->SetAligns(array('L','L','L'));
    $this->pdf->setY(10);
		$this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+1);
    $this->pdf->row(array('',$portefeuilledata['Naam'].' '.$portefeuilledata['Naam1']));
    $this->pdf->setY(20);
    $rapportagePeriode = date("j",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' t/m '.
		                                          date("j",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
    $this->pdf->SetFont($this->pdf->rapport_font,'U',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Betreft vermogensrapportage over de periode '.$rapportagePeriode));

    $this->pdf->Ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',"Breda, ".date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if($portefeuilledata['verzendaanhef']<>'')
      $aanhef=$portefeuilledata['verzendaanhef'];
    else
      $aanhef='relatie';  
    
    $txt="Geachte $aanhef,
    
Hierbij treft u uw periodieke rapportage aan met een inzicht in de ontwikkeling van uw vermogen. 

Deze vermogensbeheerrapportage gaat over de volgende rekening(en) en vermogensbestanddelen:
";
    
    $this->pdf->Ln(10);
    $this->pdf->row(array('',$txt));
    $this->pdf->setWidths(array(5,40,45,140));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Ln();
    $this->pdf->row(array('','Depotbank','Portefeuille','Naam'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    foreach($allePortefeuilles as $pdata)
      $this->pdf->row(array('',$pdata['Depotbank'],$pdata['Portefeuille'],$pdata['Naam'].' '.$pdata['Naam1']));
    
    $maxAantal=8;
    if(count($allePortefeuilles)>$maxAantal)
    {
      
      $this->pdf->templateVars['inhoudsPaginaExtaY'] = -50;//(count($allePortefeuilles) - $maxAantal) * 4;
      $this->pdf->addPage();
      $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    }
    
    $this->pdf->setY(108+$this->pdf->templateVars['inhoudsPaginaExtaY']);
    $this->pdf->setWidths(array(5,200,10));
     $this->pdf->SetAligns(array('L','L','R'));
    $this->pdf->row(array('','Dit rapport bevat de volgende onderdelen:','Pag'));
    $this->pdf->setY(158+$this->pdf->templateVars['inhoudsPaginaExtaY']);
     $this->pdf->row(array('','Mocht u naar aanleiding van deze rapportage nog vragen en  opmerkingen hebben, dan horen wij dat graag van u.

Hoogachtend,

'.$portefeuilledata['accountManager']));

   $this->pdf->AddPage(); 
   printValutaoverzicht($this->pdf,$this->portefeuille, $this->rapportageDatum);
   $this->pdf->Ln(10);  
   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-1);
   $this->pdf->row(array('','Disclaimer')); 
   $this->pdf->Ln();
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
   $this->pdf->row(array('','Hoewel door Wilton Investment Services B.V. de grootst mogelijke zorgvuldigheid is betracht bij het samenstellen van de inhoud van deze rapportage, kan niet worden ingestaan voor de juistheid en volledigheid van deze informatie. U kunt geen rechten ontlenen aan de inhoud van deze rapportage. Bij een constatering van een onjuistheid of onvolledigheid in deze rapportage, verzoeken wij u vriendelijk om binnen 30 dagen na verzending dit schriftelijk aan ons kenbaar te maken. Na deze termijn wordt de client geacht Wilton Investment Services B.V. te hebben gedechargeerd terzake van de advisering door Wilton Investment Services B.V. gedurende de betreffende periode. De inhoud van deze rapportage is strikt persoonlijk en vertrouwelijk. Het heeft uitsluitend een informatieve functie en mag niet gelezen worden als een beleggingsadvies. Aan alle vormen van beleggen zijn risico\'s verbonden. De risico\'s zijn afhankelijk van de belegging. Een belegging kan in meer of mindere mate risicodragend zijn.

Meestal geldt dat een belegging met een hoger verwacht rendement grotere risico\'s met zich brengt. Zeker bij het beleggen in buitenlandse effecten kan de overheidspolitiek in het desbetreffende land gevolgen hebben voor de waarde van de belegging. Daarnaast dient bij het beleggen in buitenlandse effecten rekening te worden gehouden met het valutasrisico. Voor meer informatie vraag naar: KENMERKEN VAN EFFECTEN EN DAARAAN VERBONDEN SPECIFIEKE RISICO\'S van Wilton Investment Services B.V. Wilton Investment Services is opgenomen in het register van de Autoriteit Financiële Markten in het kader van de Wet Financieel Toezicht ( WFT ) Zie voor meer informatie www.afm.nl.
Algemene voorwaarden die van toepassing zijn op alle diensten die Wilton Investment Services B.V. verleent zijn op te vragen bij Wilton Investment Services B.V., Postbus 4667 4803 ER Breda, Nederland')); 
$this->pdf->Ln();
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize-2);
$this->pdf->row(array('','Berekenmethodiek netto rendement - TWRR')); 
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
$this->pdf->row(array('','In deze vermogensrapportage wordt uw netto rendement tijdgewogen gemeten, met een maandelijkse interval. Hiermee valt het effect van uw eventuele toevoegingen en/of onttrekkingen buiten de berekening van uw rendement, waardoor uw rendementspercentage zuiverder is. De methode is als volgt:

TWRR = (EMV-BMV-CF)/(BMV+xCF)

Waarin:
BMV = Beginvermogen
EMV = Eindvermogen
xCF = Tijdgewogen saldo opnamen en stortingen'));
$this->pdf->SetTextColor(61,82,101);

	}
}
?>