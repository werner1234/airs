<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/07/13 17:51:11 $
File Versie					: $Revision: 1.1 $

$Log: RapportFRONT_L84.php,v $
Revision 1.1  2019/07/13 17:51:11  rvv
*** empty log message ***

Revision 1.11  2018/04/11 11:20:06  rvv
*** empty log message ***

Revision 1.10  2018/04/04 15:48:38  rvv
*** empty log message ***

Revision 1.9  2016/03/06 14:37:43  rvv
*** empty log message ***

Revision 1.8  2016/03/02 16:59:05  rvv
*** empty log message ***

Revision 1.7  2014/09/10 15:54:54  rvv
*** empty log message ***

Revision 1.6  2014/08/06 15:41:01  rvv
*** empty log message ***

Revision 1.5  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.4  2014/06/08 15:27:58  rvv
*** empty log message ***

Revision 1.3  2014/05/17 16:35:44  rvv
*** empty log message ***

Revision 1.2  2014/04/30 16:03:17  rvv
*** empty log message ***

Revision 1.1  2014/04/19 16:16:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFront_L84
{
	function RapportFront_L84($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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

	}



	function writeRapport()
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

		//if($this->pdf->selectData['type'] != 'eMail')
		//  $this->voorBrief();
   //background

		///if ((count($this->pdf->pages) % 2))
		//{
		//  $this->pdf->frontPage=true;
  	//	$this->pdf->AddPage($this->pdf->CurOrientation);
		//}
		$this->pdf->frontPage = true;
    $this->pdf->AddPage('L');


		if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.0425;
      $xSize=798*$factor;
      $ySize=331*$factor;


	    $this->pdf->Image($this->pdf->rapport_logo, 245, 12, $xSize, $ySize);
      
      $pageWidth=$this->pdf->w;
      $pageHeight=$this->pdf->h;
      
      if($this->pdf->rapportCounter <> $this->pdf->rapportCounterLast)
        $this->pdf->customPageNo = 0;
      
      $this->pdf->rect(0,$pageHeight-18,$pageWidth,1,'F','F',$this->pdf->rapport_logoKleurPaars) ;
      $this->pdf->rect(0,$pageHeight-16,$pageWidth,12,'F','F',$this->pdf->rapport_logoKleurBlauw) ;
      $this->pdf->rect(0,$pageHeight-3,$pageWidth,3,'F','F',$this->pdf->rapport_logoKleurPaars) ;

/*
      $this->pdf->SetDrawColor(200,200,200);
      
      $yoffset=60;
      $poly=array(297,$yoffset, 260,$yoffset+10, 297,$yoffset+25);
      $this->pdf->Polygon($poly,'DF',null,array(220,220,220));
 
      $yoffset=120;
      $poly=array(297,$yoffset, 260,$yoffset-15, 210,$yoffset-5, 297,$yoffset+35);
      $this->pdf->Polygon($poly,'DF',null,array(220,220,220));     

      $yoffset=195;
      $poly=array(297,$yoffset, 210,$yoffset-40, 150,$yoffset-30, 240,210 ,297,210);
      $this->pdf->Polygon($poly,'DF',null,array(220,220,220));     

      $this->pdf->SetDrawColor(38,58,77);
      $this->pdf->Rect(0,0,20,210,"DF",null,array(73,107,152));
      $this->pdf->SetDrawColor(0,0,0);
*/
		}
    



		$fontsize = 14; //$this->pdf->rapport_fontsize

    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);

		$this->pdf->SetY(55);

		$rapportagePeriode = vertaalTekst('Verslagperiode',$this->pdf->rapport_taal).' '.date("j",$this->rapportageDatumVanafJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumVanafJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumVanafJul).
		                                          ' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		                                          date("j",$this->rapportageDatumJul)." ".
		                                          vertaalTekst($__appvar["Maanden"][date("n",$this->rapportageDatumJul)],$this->pdf->rapport_taal)." ".
		                                          date("Y",$this->rapportageDatumJul);


   
    $this->pdf->SetWidths(array(110,180));
		$this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$fontsize);
		$this->pdf->row(array(' ',vertaalTekst('Vermogensrapportage',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
    $this->pdf->ln(6);
    $this->pdf->row(array(' ','Portefeuille '.formatPortefeuille($this->pdf->portefeuilledata['Portefeuille'])));
    $this->pdf->ln(6);
  	$this->pdf->row(array('',$rapportagePeriode));


  	$this->pdf->SetY(140);
    $this->pdf->SetWidths(array(30,110));
    //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderNaam']));
    $this->pdf->ln(3);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderAdres']));
    $this->pdf->ln(3);
    $this->pdf->row(array('',$portefeuilledata['vermogensbeheerderWoonplaats']));
    $this->pdf->ln(3);
    $this->pdf->row(array('',$portefeuilledata['Telefoon']));
    $this->pdf->ln(3);
    $this->pdf->row(array('',$portefeuilledata['Email']));
    
	  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
/*
	  $this->pdf->frontPage = true;

   
    $this->pdf->rapport_type = "INHOUD";
	  $this->pdf->rapport_titel = "Inhoudsopgave";//Inhoudsopgave
	  $this->pdf->addPage('L');
	  $this->pdf->templateVars['inhoudsPagina']=$this->pdf->page;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
*/
	}
}
?>
