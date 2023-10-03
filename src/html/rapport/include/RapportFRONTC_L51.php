<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/22 15:40:47 $
 		File Versie					: $Revision: 1.17 $

 		$Log: RapportFRONTC_L51.php,v $
 		Revision 1.17  2020/04/22 15:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2019/12/21 14:08:32  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/03/23 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/04/11 15:20:41  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/04/03 10:56:21  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/02/20 15:18:29  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/12/20 16:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/07/11 14:20:20  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/07/01 15:34:25  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/03/18 16:29:56  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/05/10 13:54:39  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/05/05 15:52:25  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/04/26 16:43:08  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/19 16:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/17 17:20:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/16 15:51:22  rvv
 		*** empty log message ***
 		

*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFRONTC_L51
{
	function RapportFRONTC_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

		foreach ($this->pdf->portefeuilles as $portefeuille)
		{
		  $query = "SELECT
	            	if(isnull(CRM_naw.naam),Clienten.Naam,CRM_naw.naam) as Naam,
                if(isnull(CRM_naw.naam),Clienten.Naam1,CRM_naw.naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Depotbanken.Omschrijving as depotbankOmschrijving,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.Clientvermogensbeheerder,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$portefeuille."'";

		  $this->DB->SQL($query);
	  	$this->DB->Query();
      $tmp=$this->DB->nextRecord();
      if($tmp['Depotbank']=='TGB')
        $tmp['Depotbank']='IGS';
      if($tmp['Portefeuille']=='')
      {
        $tmp['Portefeuille']=$portefeuille;
        $tmp['depotbankOmschrijving']="Niet gevonden? ";
	    }
      
      $perioden=array($this->rapportageDatumVanaf,$this->rapportageDatum);
      $totaalOpDatum=array();
      $skipPortefeuille=true;
      foreach($perioden as $datum)
      {
        $gegevens = berekenPortefeuilleWaarde($portefeuille,$datum, (substr($datum, 5, 5) == '01-01'?true:false),'EUR', $datum);
        foreach ($gegevens as $waarde)
        {
          $totaalOpDatum[$datum] += $waarde['actuelePortefeuilleWaardeEuro'];
        }
 
        if(round($totaalOpDatum[$datum])<>0)
          $skipPortefeuille=false;
        //echo $portefeuille." ".(substr($datum, 5, 5) == '01-01'?true:false)." $datum | $skipPortefeuille <br>\n";
      }
      //listarray($totaalOpDatum);
      if($skipPortefeuille==false)
        $portefeuilledata[] = $tmp;
		}

		$query = "SELECT
	            	CRM_naw.naam as Naam,
                CRM_naw.naam1 as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Depotbanken.Omschrijving as depotbankOmschrijving,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.Clientvermogensbeheerder,
                 Portefeuilles.Selectieveld1,
                Accountmanagers.Naam as accountManager,
                 Vermogensbeheerders.Naam as vermogensbeheerderNaam,
                 Vermogensbeheerders.Adres as vermogensbeheerderAdres,
                 Vermogensbeheerders.Woonplaats as vermogensbeheerderWoonplaats,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
                LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		          WHERE
		            Portefeuilles.Portefeuille = '".$this->portefeuille."'";

		$this->DB->SQL($query);
		$this->DB->Query();
		$hoofdPortefeuilleCRM=$this->DB->nextRecord();


		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
	    $factor=0.031;
		  $xSize=1417*$factor;
		  $ySize=591*$factor;
			$this->pdf->Image($this->pdf->rapport_logo, 230, 180, $xSize, $ySize);
		}


		$fontsize = 16; //$this->pdf->rapport_fontsize
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetAligns(array('C'));
    $this->pdf->SetWidths(array(297-2*$this->pdf->marge));
		$this->pdf->SetY(45);

		$rapportagePeriode = date("d",$this->rapportageDatumVanafJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumVanafJul).
		                     ' - '.
		                     date("d",$this->rapportageDatumJul)." ".
		                     vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                     date("Y",$this->rapportageDatumJul);
    //$this->pdf->row(array('Capital Support'));
//listarray($this->pdf->__appvar['consolidatie']);
//		listarray($this->pdf->portefeuilledata);

		if(isset($hoofdPortefeuilleCRM['Naam']) && $hoofdPortefeuilleCRM['Naam'] <> '')
			$this->pdf->row(array($hoofdPortefeuilleCRM['Naam']));
    elseif(isset($this->pdf->__appvar['consolidatie']['portefeuillenaam1']) && $this->pdf->__appvar['consolidatie']['portefeuillenaam1'] <> '')
      $this->pdf->row(array($this->pdf->__appvar['consolidatie']['portefeuillenaam1']));
    else
      $this->pdf->row(array($this->pdf->portefeuilledata['Naam']));
    $this->pdf->ln(5);
    //$this->pdf->row(array('Vermogensregierapportage'));
		if(isset($hoofdPortefeuilleCRM['Naam']) && $hoofdPortefeuilleCRM['Naam'] <> '')
			$this->pdf->row(array($hoofdPortefeuilleCRM['Naam1']));
    elseif(isset($this->pdf->__appvar['consolidatie']['portefeuillenaam1']) && $this->pdf->__appvar['consolidatie']['portefeuillenaam1'] <> '')
      $this->pdf->row(array($this->pdf->__appvar['consolidatie']['portefeuillenaam2']));
    else
      $this->pdf->row(array($this->pdf->portefeuilledata['Naam1']));
    $this->pdf->ln(5);
		$this->pdf->row(array($rapportagePeriode));
		$this->pdf->ln(6);

    $this->pdf->SetWidths(array(5,50,150));
    $this->pdf->SetAligns(array('L','L','L'));

    if(count($portefeuilledata)>7)
      $ystart=100;
    else
      $ystart=150;
    
    if(count($portefeuilledata)>30)
      $margeStap=95;
    else
      $margeStap=120;
    
    $this->pdf->SetY($ystart);
    $this->pdf->SetFont($this->pdf->rapport_font,'',11);
    $this->pdf->underline=true;
		$this->pdf->row(array('',vertaalTekst('Samenstelling vermogen',$this->pdf->rapport_taal)));
    $this->pdf->underline=false;
    $offset=0;
    foreach ($portefeuilledata as $index=>$port)
	  {
	   if($index > 0 && $index%15==0)
     {
	     $offset+=$margeStap;
       $this->pdf->SetWidths(array(5+$offset,50,150));
       $this->pdf->SetY($ystart+4);
	   }
      $this->pdf->ln(1);
	    $this->pdf->row(array('',$port['PortefeuilleVoorzet'].$port['Portefeuille'],($port['Selectieveld1']<>''?$port['Selectieveld1']:$port['depotbankOmschrijving'])));
	  }


	$this->pdf->SetY(170);
    $this->pdf->SetWidths(array(223,50));
    $this->pdf->row(array('','Den Haag'));
    $this->pdf->ln(1);
		$this->pdf->row(array('',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));


	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
	  $this->pdf->frontPage = true;

   	$this->pdf->rapport_type = "OIB";
	  $this->pdf->rapport_titel = "";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;

	}


}
?>