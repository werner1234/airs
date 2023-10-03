<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/11/19 19:03:08 $
File Versie					: $Revision: 1.5 $

$Log: RapportMOD_L40.php,v $
Revision 1.5  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.4  2015/06/13 13:16:01  rvv
*** empty log message ***

Revision 1.3  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.2  2014/09/03 15:56:32  rvv
*** empty log message ***

Revision 1.1  2014/08/23 15:45:01  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMOD_L40
{
	function RapportMOD_L40($pdf, $portefeuille, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MOD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_naam1 = str_replace("Modelportefeuille ","",$this->pdf->rapport_naam1);
  	$this->pdf->rapport_koptext = "Portefeuille voorstel ".$this->pdf->rapport_naam1."\n".$this->pdf->selectData[mutatieportefeuille_customNaam];
		$this->pdf->rapport_titel = "";
    
    $this->pdf->excelData[]=array('Omschrijving','Valuta','Aantal','Actuele Koers in valuta','Actuele Waarde in euro');

		$this->portefeuille = $portefeuille;

		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function printTotaal($title, $type,$fontStyle='')
	{
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
	  }
	  if($type=='verdeling')
	  {
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 270)
        $this->pdf->CellBorders=$this->subtotaalCatBorders;
      else  
        $this->pdf->CellBorders=$this->subtotaalVerBorders;
	  }
    if($type=='alles')
	  {
	    $space='';
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
      $extraln=1;
      $this->totalen[$type]['beginPortefeuilleWaardeEuro']=0;
      $this->totalen[$type]['eurResultaat']=0;
      $this->totalen[$type]['procentResultaat']=0;
      $title="actuele vermogen";
	  }
    if($title=='Liquiditeiten')
      $this->totalen[$type]['eurResultaat']=0;

    
      $this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
  	  $this->pdf->SetX($this->pdf->marge);
		  $this->pdf->Cell(150,4, $space.'Totaal '.$title, 0, "L");
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  $this->pdf->setX($this->pdf->marge);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	  	$this->pdf->row(array("","",'','','',
													$this->formatGetal($this->totalen[$type]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),''));
    
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders); 
    if($extraln==1) 
      $this->pdf->Ln();                  
		$this->totalen[$type]=array();
    $this->totalenRente[$type]=array();
	}

	function printKop($title, $type, $fontStyle="")
	{
	  $fill=0;
	  if($type=='hoofdcategorie')
	  {
	    $space='';
      
      if($this->pdf->GetY() > 265)
        $this->pdf->addPage();
      
      $this->pdf->SetFillColor(0,78,58); 
      $this->pdf->SetTextColor(255,255,255);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='beleggingscategorie')
	  {
	    $space='  ';
      if($this->pdf->GetY() > 270)
        $this->pdf->addPage();
      $this->pdf->SetFillColor(200,200,200);
      $this->pdf->CellBorders=$this->subtotaalCatBorders;
      $this->pdf->fillCell=array(1,1,1,1,1,1,1);
      $this->pdf->row(array(""," ",' ','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
      unset($this->pdf->fillCell);
	  }
	  if($type=='verdeling')
	  {
	   	//   echo $title." ".$this->pdf->GetY()."<br>\n";
	    $space='    ';
      if($this->pdf->GetY() < 50 || $this->pdf->GetY() > 270 )
        $this->pdf->CellBorders=$this->kopVerBorders;
      else
        $this->pdf->CellBorders=$this->subtotaalFondsBorders;
      $this->pdf->row(array("","",'','','','',''));
      $this->pdf->setY($this->pdf->getY()-4);
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,$fontStyle,$this->pdf->rapport_fontsize);
		
		$this->pdf->SetX($this->pdf->marge);
    $width=array_sum($this->pdf->widthB);
		$this->pdf->MultiCell($width,4, $space.$title, 0, "L",$fill);
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if(round($waarde,2) == 0)
      return '';
	  elseif ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}
  
	function writeRapport()
	{
		global $__appvar;
    $this->verdeling='regio';

		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

	  $query="SELECT Vermogensbeheerders.VerouderdeKoersDagen
    FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];


	  $this->pdf->widthB = array(10,65,20,20,30,30,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');
    
    $this->subtotaalCatBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
    $this->subtotaalVerBorders=array(array('L','U'),array('U'),array('U'),array('U'),array('U'),array('U'),array('U','R'));
    $this->kopVerBorders=array(array('L','T'),array('T'),array('T'),array('T'),array('T'),array('T'),array('T','R'));
    $this->subtotaalFondsBorders=array(array('L'),'','','','','',array('R'));

	  $this->pdf->AddPage();

	

		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;

    if($categorien['Valuta'] == $this->pdf->rapportageValuta)
		  $beginQuery = 'beginwaardeValutaLopendeJaar';
		else
		  $beginQuery = $this->pdf->ValutaKoersBegin;

		$DB2 = new DB();

		$verdeling=$this->verdeling;
  	$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    
    if($verdeling=='Fonds')
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Omschrijving"; 
    else
     $verdelingVolgorde="TijdelijkeRapportage.".$verdeling."Volgorde"; 

			$query = "SELECT
			 TijdelijkeRapportage.beleggingscategorie,
			 TijdelijkeRapportage.valuta,
			 TijdelijkeRapportage.fondsOmschrijving,
       TijdelijkeRapportage.Fonds,
			 TijdelijkeRapportage.actueleValuta,
			 TijdelijkeRapportage.totaalAantal,
			 TijdelijkeRapportage.beginwaardeLopendeJaar,
			 TijdelijkeRapportage.beginPortefeuilleWaardeInValuta,
			 TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			 TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, 
       TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, 
       TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			 round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd,
			 TijdelijkeRapportage.hoofdsector,
       TijdelijkeRapportage.hoofdcategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
       TijdelijkeRapportage.beleggingscategorieVolgorde,
       TijdelijkeRapportage.type,
       TijdelijkeRapportage.".$verdeling." as verdeling,
       TijdelijkeRapportage.".$verdeling."Omschrijving as verdelingOmschrijving,
       $verdelingVolgorde,
       Fondsen.OptieBovenliggendFonds,
       if(Fondsen.OptieBovenliggendFonds='',TijdelijkeRapportage.Fonds ,Fondsen.OptieBovenliggendFonds) as onderliggendFonds
       ,TijdelijkeRapportage.Lossingsdatum,
       TijdelijkeRapportage.rekening
			 FROM TijdelijkeRapportage
			 LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
			 WHERE       
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, ".$verdelingVolgorde.",
         TijdelijkeRapportage.Lossingsdatum,
          onderliggendFonds,TijdelijkeRapportage.fondspaar, 
           TijdelijkeRapportage.Lossingsdatum, Fondsen.OptieBovenliggendFonds,
         TijdelijkeRapportage.type,TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__); //TijdelijkeRapportage.type <> 'rente' AND

			$DB2->SQL($query);
			$DB2->Query();
			$somVelden=array('actuelePortefeuilleWaardeInValuta','actuelePortefeuilleWaardeEuro','beginPortefeuilleWaardeEuro');
			$vedelingen=array('hoofdcategorie'=>'bi','beleggingscategorie'=>'b','verdeling'=>'','alles'=>'');
			$omschrijvingVelden=array('hoofdcategorieOmschrijving'=>'hoofdcategorie','beleggingscategorieOmschrijving'=>'beleggingscategorie','verdelingOmschrijving'=>$verdeling);
			$regels=array();
      //$regels[]=array('hoofdcategorieOmschrijving'=>'hoofdcategorieOmschrijving','hoofdsector'=>'hoofdsector','verdeling'=>'verdeling','beleggingscategorie'=>'beleggingscategorie','fondsOmschrijving'=>'fondsOmschrijving','verdelingOmschrijving'=>'verdelingOmschrijving');
      while($subdata = $DB2->NextRecord())
			{
			   $regels[]=$subdata;
      }
      //listarray($regels);
      
      foreach($regels as $subdata)
      {
        if($subdata['type']=='rekening' && $_POST['anoniem']!=1)
          $subdata['fondsOmschrijving'].=" ".ereg_replace("[^0-9]","",$subdata['rekening']);
       
        if($subdata['beleggingscategorie']=='AAND')
        {
          foreach ($omschrijvingVelden as $veldNaam=>$omschrijving)
	 	        if($subdata[$veldNaam]=='' )
              $subdata[$veldNaam] ="Geen $omschrijving";
        }
          
			  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
			  {
			    if($lastVerdeling[$type] <> $subdata[$type.'Omschrijving'] && isset($this->totalen[$type]))
          { 
     	      $this->printTotaal($lastVerdeling[$type],$type,$weergave);
          }
			  }

			  foreach ($vedelingen as $type=>$weergave)
  			  if($subdata[$type.'Omschrijving'] <> '' && $lastVerdeling[$type] <> $subdata[$type.'Omschrijving'])
          {
 	  		  	$this->printKop(vertaalTekst($subdata[$type.'Omschrijving'],$this->pdf->rapport_taal),$type, $weergave);
          }
   
        if($subdata['type']=='rente')
        {
          $subdata['fondsOmschrijving']='lopende rente';
        }
          
       // echo $this->pdf->GetY()." ".$subdata['fondsOmschrijving']." <br>\n";
        if($this->pdf->GetY() > 274)  
          $this->printKop(vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal),'beleggingscategorie', 'b');   

        if($this->pdf->GetY() > 270)
          $this->pdf->CellBorders=$this->subtotaalVerBorders;
        else
          $this->pdf->CellBorders=$this->subtotaalFondsBorders; 
          
				$this->pdf->setX($this->pdf->marge);
        
  			$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);
				$this->pdf->setX($this->pdf->marge);
        $eurResultaat=$subdata['actuelePortefeuilleWaardeEuro'] - $subdata['beginPortefeuilleWaardeEuro'];
       	$procentResultaat = ($eurResultaat / (abs($subdata['beginPortefeuilleWaardeEuro']) /100));
         
          
        if($subdata['type']=='rekening')
          $eurResultaat='';

        
          if($subdata['type']=='rente')
          {
          	$this->pdf->row(array("","","","",
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),''));
            $rowData=array($subdata['fondsOmschrijving'],'','',round($subdata['actuelePortefeuilleWaardeEuro'],2));
          }
          else
          {
			  	  $this->pdf->row(array("",
													"",
                          $subdata['valuta'],
                          $this->formatAantal($subdata['totaalAantal'],0,true),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],2),
													$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),''));
            $rowData=array($subdata['fondsOmschrijving'],$subdata['valuta'],round($subdata['totaalAantal'],0),
                           round($subdata['actuelePortefeuilleWaardeInValuta'],2),round($subdata['actuelePortefeuilleWaardeEuro'],2));              
          }
          $this->pdf->excelData[]=$rowData;    
        unset($this->pdf->CellBorders);


      foreach ($vedelingen as $type=>$weergave)
      {
        $lastVerdeling[$type]=$subdata[$type.'Omschrijving'];
       
        foreach ($somVelden as $veld)
        {
          $this->totalen[$type][$veld]+=$subdata[$veld];
          if($subdata['type']=='rente')
            $this->totalenRente[$type][$veld]+=$subdata[$veld];
        }
//
        $this->totalen[$type]['eurResultaat']=($this->totalen[$type]['actuelePortefeuilleWaardeEuro'] - $this->totalen[$type]['beginPortefeuilleWaardeEuro']) - $this->totalenRente[$type]['actuelePortefeuilleWaardeEuro']  ;
    	  $this->totalen[$type]['procentResultaat'] = ($this->totalen[$type]['eurResultaat'] / (abs($this->totalen[$type]['beginPortefeuilleWaardeEuro']) /100));
      }
 
  

		}
    	  foreach (array_reverse($vedelingen,true) as $type=>$weergave)
		{
		  if(isset($this->totalen[$type]) && $type <> 'alles' && $lastVerdeling[$type]<> '')
		     $this->printTotaal($lastVerdeling[$type],$type,$weergave);
		}

   	// print grandtotaal
		$this->pdf->ln();
		$this->printTotaal('','alles','B');
    $this->pdf->SetWidths(array(200));
		if($this->pdf->selectData['mutatieportefeuille_afm'])
		{
			$this->pdf->ln();
			$afm=AFMstd($this->portefeuille, $this->rapportageDatum);
			$this->pdf->Cell(150,4,"De AFM Standaarddeviatie voor deze portefeuille bedraagt ".$this->formatGetal($afm['std'],2).'%');

		}
    //$this->pdf->row(array("* Resultaat is exclusief lopende rente"));
    //$this->pdf->MultiCell(200,$this->pdf->rowHeight,"* Resultaat is exclusief lopende rente",0,'L');
      
  //    listarray($subdata['fondsOmschrijving']);
//listarray($this->totalenRente); 
	}
}
?>