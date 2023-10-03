<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/12/20 16:48:28 $
File Versie					: $Revision: 1.26 $

$Log: RapportFRONT_L5.php,v $
Revision 1.26  2015/12/20 16:48:28  rvv
*** empty log message ***

Revision 1.25  2015/10/19 06:55:31  rvv
*** empty log message ***

Revision 1.24  2015/10/18 13:46:20  rvv
*** empty log message ***

Revision 1.23  2015/04/22 15:25:19  rvv
*** empty log message ***

Revision 1.22  2015/04/19 08:35:38  rvv
*** empty log message ***

Revision 1.21  2014/07/02 15:56:02  rvv
*** empty log message ***

Revision 1.20  2014/06/29 15:38:56  rvv
*** empty log message ***

Revision 1.19  2014/03/29 16:22:37  rvv
*** empty log message ***

Revision 1.18  2014/02/08 17:42:08  rvv
*** empty log message ***

Revision 1.17  2013/11/09 16:20:41  rvv
*** empty log message ***

Revision 1.16  2013/08/18 12:24:51  rvv
*** empty log message ***

Revision 1.15  2011/08/04 18:39:35  rvv
*** empty log message ***

Revision 1.14  2011/07/13 07:16:00  rvv
*** empty log message ***

Revision 1.13  2010/12/05 09:54:08  rvv
*** empty log message ***

Revision 1.12  2009/12/23 15:00:43  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/factuur/Factuur.php");

class RapportFront_L5
{
	function RapportFront_L5($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		$this->pdf->extraPage =0;
		$this->DB = new DB();

		$this->pdf->brief_font = $this->pdf->rapport_font;
/*
		if(file_exists(FPDF_FONTPATH.'pala.php'))
		{
  	  if(!isset($this->pdf->fonts['palatino']))
	    {
		    $this->pdf->AddFont('palatino','','pala.php');
		    $this->pdf->AddFont('palatino','B','palab.php');
		    $this->pdf->AddFont('palatino','I','palai.php');
		    $this->pdf->AddFont('palatino','BI','palabi.php');
	    }
		  $this->pdf->brief_font = 'palatino';
		}
*/
	}

	function factuurTest()
	{
	  if(!isset($this->pdf->selectData['periode']))
	    return false;
    if($this->pdf->selectData['periode']=='Maandrapportage')
      return false;
      
      
	  $query = "SELECT Portefeuilles.* , Depotbanken.Omschrijving as depotbankOmschrijving ,Vermogensbeheerders.PerformanceBerekening
		          FROM
		          Portefeuilles
		          LEFT JOIN Depotbanken ON Portefeuilles.depotbank =  Depotbanken.depotbank
		          JOIN Vermogensbeheerders  ON Portefeuilles.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
		          WHERE  Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$this->portefeuilledata = $this->DB->NextRecord();

		if($this->portefeuilledata['BeheerfeeAantalFacturen'] == 4 ||
		    ($this->portefeuilledata['BeheerfeeAantalFacturen'] == 1 &&  substr($rapportageDatum,5,5) == '12-31') ||
		    ($this->portefeuilledata['BeheerfeeAantalFacturen'] == 2 && (substr($rapportageDatum,5,5) == '06-30' || substr($rapportageDatum,5,5) == '12-31'))
		    )
		{
		  
    $pdf=new FPDF(); 
    $pdf->FactuurDrempelPercentage=$this->pdf->FactuurDrempelPercentage;
    $factuur=new Factuur($pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    if(!$factuur->waarden && $factuur->berekening->afbreken==true && $factuur->factuurInXls==false)
      return false;
      
		if($this->rapportageDatumVanafJul < db2jul($this->portefeuilledata['Startdatum']))
		{
			$this->vandatum = $this->portefeuilledata['Startdatum'];
		}
		else
		{
			$this->vandatum = $this->rapportageDatumVanaf;
		}


		$this->tmdatum = $tmdatum;

		$this->julrapport 		= $this->rapportageDatumVanafJul;
		$this->rapportMaand 	= date("m",$this->julrapport);
		$this->rapportDag 		= date("d",$this->julrapport);
		$this->rapportJaar 		= date("Y",$this->julrapport);
		if($this->rapportageDatumVanafJul < db2jul($this->portefeuilledata['BeheerfeeFacturatieVanaf']))
		{
		   echo "Facturatiedatum < factuur vanaf datum. Factuur niet maken voor portefeuille ".$this->portefeuille.".";
			  $this->factuur = false;
			  return false;
		}

		if($this->portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		{
			$julvan = $this->rapportageDatumVanafJul;
			$jultm  = $this->rapportageDatumJul;

			if ($this->portefeuilledata['BeheerfeeFacturatieVooraf'] == 1 && date("d-m",$julvan) == "01-01")
			{
			  $this->vandatum  =  date('Y',$julvan).'-01-01' ;
  			$this->tmdatum   =  date('Y',$julvan).'-01-01' ;
			}
			else
			{
			  if(date("d-m",$jultm) == "31-12")
			  {
			  	$this->vandatum =  date('Y',$julvan).'-01-01' ;
			  }
		  	else
			  {
			    echo "Datum is niet 31-12. Factuur niet maken voor portefeuille ".$this->portefeuille.".";
		      $this->factuur = false;
			    return false;
		  	}
			}
		}
		elseif($this->portefeuilledata['BeheerfeeAantalFacturen'] == 2)
		{
			$julvan = $this->rapportageDatumVanafJul;
			$jultm  = $this->rapportageDatumJul;

			if(date("d-m",$jultm) == "30-06")
			{
				$this->vandatum =  date('Y',$julvan).'-01-01' ;
			}
			elseif (date("d-m",$jultm) == "31-12")
			{
			  $this->vandatum =  date('Y',$julvan).'-07-01' ;
			}
			else
			{
			  echo "Geen halfjaar factuur datum voor portefeuille ".$this->portefeuille.".";
			  $this->factuur = false;
			  return false;
			}
		}


		return true;
		}
		else
		return false;
	}

	function kopEnVoet()
	{
	  if(is_file($this->pdf->rapport_factuurHeader))
		{
			$this->pdf->Image($this->pdf->rapport_factuurHeader, 0, 10, 210, 34);
		}
		if(is_file($this->pdf->rapport_factuurFooter))
		{
			$this->pdf->Image($this->pdf->rapport_factuurFooter, 5, 255, 200, 37);
		}
	}

	function printBody($portefeuilledata,$alleenAdres = false,$weesp = false)
	{
	  global $__appvar;

	  $extraDagen = 0; //2
	  $this->pdf->SetY(50);
		$this->pdf->row(array('',$portefeuilledata['Naam']));
    if ($portefeuilledata['Naam1'] != '')
      $this->pdf->row(array('',$portefeuilledata['Naam1']));
    $this->pdf->row(array('',$portefeuilledata['Adres']));
    if ($portefeuilledata['verzendAdres2'] != '')
      $this->pdf->row(array('',$portefeuilledata['verzendAdres2']));
    $this->pdf->row(array('',$portefeuilledata['Woonplaats']));
    $this->pdf->row(array('',$portefeuilledata['Land']));

    if($alleenAdres == false)
    {
      $extraHoogte=10;
      $this->pdf->SetY(105-$extraHoogte);
      $this->pdf->cell($this->pdf->widths[0],$this->pdf->rowHeight,'',0,0,'L');

      if($weesp == true)
        $this->pdf->cell($this->pdf->widths[0],$this->pdf->rowHeight,'Weesp, '.(date("d")+$extraDagen)." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y"),0,0,'L');
      else
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'B',11);
        $this->pdf->cell($this->pdf->widths[0],$this->pdf->rowHeight,vertaalTekst('Datum',$this->pdf->rapport_taal).":",0,0,'L');
        $this->pdf->SetFont($this->pdf->rapport_font,'',11);
        $this->pdf->cell($this->pdf->widths[0],$this->pdf->rowHeight,(date("d"))." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y"),0,0,'L');
      }
      $this->pdf->SetY(115-$extraHoogte);
      $this->pdf->SetFont($this->pdf->brief_font,'I',11);
      $this->pdf->row(array('',vertaalTekst("Betreft: Rapportage",$this->pdf->rapport_taal)."\n"));
      $this->pdf->Ln();
      $this->pdf->SetFont($this->pdf->brief_font,'',11);

      if($portefeuilledata['aanhef'] != '')
        $portefeuilledata['aanhef'].=',';
      $this->pdf->row(array('', $portefeuilledata['aanhef']));
      $this->pdf->row(array('', $portefeuilledata['tekst']));
      $this->pdf->ln(12);
      $this->pdf->row(array('', str_replace('  ','          ',$portefeuilledata['accountManager'])));
    }
	}

function writeRapportNew()
	{
		global $__appvar;

		$query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
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
    
	  $query = "SELECT verzendAanhef FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilledata['aanhef']=$crmData['verzendAanhef'];

    $this->pdf->geenBasisFooter = true;
    $this->pdf->rowHeight = 5;
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
	  $this->pdf->SetWidths(array(15,140));
	  $this->pdf->SetAligns(array('L','L'));

	  $this->pdf->AddPage('P');
    $this->pdf->frontPage = true;
    $portefeuilledata['tekst']='';
    $this->printBody($portefeuilledata,true);

    $this->pdf->rowHeight = 4;
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.07;
		  $xSize=833*$factor;
		  $ySize=219*$factor;
		}

   	$this->pdf->widthA = array(30,180);
		$this->pdf->alignA = array('L','L','L');
		$fontsize = 10; //$this->pdf->rapport_fontsize

    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetY(58);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("d",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("d",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);
		$this->pdf->row(array('',$rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(30,40,5,50));
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal),':',$portefeuilledata['Portefeuille']));
    $this->pdf->ln();
    $this->pdf->row(array(' ',vertaalTekst('Portefeuilleprofiel',$this->pdf->rapport_taal),':',$this->pdf->portefeuilledata['Risicoklasse']));
    $this->pdf->ln();
  
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->ln(8); 
    $this->pdf->row(array(' ',vertaalTekst('PERSOONLIJK EN VERTROUWELIJK',$this->pdf->rapport_taal)));
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
    
  
    $this->pdf->AddPage();
	  $this->pdf->frontPage = true;
$this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;


	}
  

	function writeRapport()
	{
	  $this->pdf->SetFont($this->pdf->brief_font,'',11);
	  $query = "SELECT
		            Clienten.Naam,
                Clienten.Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Clienten.Land,
                Portefeuilles.Portefeuille,
                Portefeuilles.Risicoklasse,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles, Clienten , Accountmanagers, Vermogensbeheerders
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

    $velden=array();    
    $query = "desc CRM_naw";
    $this->DB->SQL($query);
    $this->DB->query();
    while($data=$this->DB->nextRecord('num'))
      $velden[]=$data[0];
    if(in_array('verzendAdres2',$velden))
      $extraVeld=',verzendAdres2';

	  $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilledata['aanhef']=$crmData['verzendAanhef'];
    $portefeuilledata['verzendAdres2']=$crmData['verzendAdres2'];

/*
    $notaTekst='
Hierbij treft u de rapportage aan van uw effectenportefeuille.

Deze rapportage bestaat uit:

• Overzicht portefeuille
• Performanceberekening
• Overzicht onderverdeling in beleggingscategorieën
• Nota
• Nieuwsbrief Stroeve & Lemberger Vermogensbeheer

Mocht deze rapportage aanleiding geven tot vragen, dan staan wij graag tot uw beschikking.

Hoogachtend,
Stroeve & Lemberger Vermogensbeheer


';

$geenNotaTekst ='
Hierbij treft u de rapportage aan van uw effectenportefeuille.

Deze rapportage bestaat uit:

• Overzicht portefeuille
• Performanceberekening
• Overzicht onderverdeling in beleggingscategorieën
• Nieuwsbrief Stroeve & Lemberger Vermogensbeheer

Mocht deze rapportage aanleiding geven tot vragen, dan staan wij graag tot uw beschikking.

Hoogachtend,
Stroeve & Lemberger Vermogensbeheer


';

$maandTekst ='
Hierbij ontvangt u onze rapportage over uw effectenportefeuille per ultimo van de vorige maand.

Wij verwachten u hiermee van dienst te zijn.


Hoogachtend,
Stroeve & Lemberger Vermogensbeheer



';
*/
if($this->factuurTest() == true)
{
  $nota= true;
  $geenNota=false;
}
else
{
  $nota= false;
  $geenNota=true;
}



$vertalingen=$this->pdf->__appvar['Rapporten'];
$vertalingen['HSE']='Overzicht portefeuille';
$vertalingen['PERF']='Performanceberekening';
$vertalingen['RISK']='Risico verdeling';
$vertalingen['OIB']='Overzicht onderverdeling in beleggingscategorieën';


foreach ($this->pdf->volgorde as $key=>$value)
{
  if(in_array($key,$this->pdf->rapport_typen) && !in_array($key,array('FRONT')))
    $rapporttext.="• ".vertaalTekst($vertalingen[$key],$this->pdf->rapport_taal)."\n";
}
if($nota)
  $rapporttext.="• ".vertaalTekst("Nota",$this->pdf->rapport_taal)."\n";

if($this->pdf->selectData['periode']=='Maandrapportage')
{
  $maandTekst =vertaalTekst("Hierbij ontvangt u onze rapportage over uw portefeuille per ultimo van de vorige maand.",$this->pdf->rapport_taal);
}
elseif($this->pdf->selectData['periode']=='Kwartaalrapportage')
{
  $rapporttext.="• ".vertaalTekst("Nieuwsbrief Stroeve & Lemberger Vermogensbeheer",$this->pdf->rapport_taal)."\n";
}


      $notaTekst="
".vertaalTekst("Hierbij treft u de rapportage aan van uw effectenportefeuille.",$this->pdf->rapport_taal)."

".vertaalTekst("Deze rapportage bestaat uit:",$this->pdf->rapport_taal)."

$rapporttext
".vertaalTekst("Mocht deze rapportage aanleiding geven tot vragen, dan staan wij graag tot uw beschikking.",$this->pdf->rapport_taal)."

".vertaalTekst("Hoogachtend",$this->pdf->rapport_taal).",
Stroeve & Lemberger Vermogensbeheer


";


//rapport_typen
  $this->pdf->geenBasisFooter = true;
  $this->pdf->rowHeight = 5;
  $this->pdf->SetFont($this->pdf->brief_font,'',11);
	$this->pdf->SetWidths(array(15,140));
	$this->pdf->SetAligns(array('L','L'));

	  $this->pdf->AddPage('P');
    $this->pdf->frontPage = true;
    $portefeuilledata['tekst']='';
    $this->printBody($portefeuilledata,true);


	  $this->pdf->AddPage('P');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    $this->pdf->frontPage = true;
	  $this->kopEnVoet();
    $portefeuilledata['tekst']=$notaTekst;
    $this->printBody($portefeuilledata,false,true);

  $this->pdf->rowHeight = 4;

   $this->pdf->SetFont($this->pdf->rapport_font,'',11);
			}

}
?>