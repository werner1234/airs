<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/05/04 15:59:12 $
 		File Versie					: $Revision: 1.6 $

 		$Log: RapportAfmExport.php,v $
 		Revision 1.6  2013/05/04 15:59:12  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/02/06 19:04:49  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/12/02 11:05:23  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/11/14 16:47:47  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/11/11 09:23:07  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/11/10 15:41:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/05/20 06:43:32  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/04/25 15:19:32  rvv
 		*** empty log message ***

 		Revision 1.1  2012/04/22 07:50:18  rvv
 		*** empty log message ***

 		Revision 1.6  2011/02/24 17:41:18  rvv
 		*** empty log message ***

 		Revision 1.5  2010/05/20 17:56:16  rvv
 		*** empty log message ***

 		Revision 1.4  2010/05/19 16:23:11  rvv
 		*** empty log message ***

 		Revision 1.3  2009/07/12 09:31:17  rvv
 		*** empty log message ***

 		Revision 1.2  2009/06/24 14:43:11  rvv
 		*** empty log message ***

 		Revision 1.1  2008/10/06 12:25:54  rvv
 		*** empty log message ***


*/
	include_once("rapport/rapportRekenClass.php");
  include_once("rapport/include/RapportAFM_L19.php");

	include_once('../../classes/excel/Writer.php');


class RapportAfmExport
{
	/*
		PDF en CSV
	*/
	var $selectData;
	var $excelData;

	function RapportAfmExport(  $selectData )
	{

		$this->selectData = $selectData;
		$this->pdf->excelData = array();

		$this->pdf = new PDFOverzicht('L','mm');

		$this->pdf->rapport_type = "afmexport";
		$this->pdf->SetAutoPageBreak(true,15);
		$this->pdf->pagebreak = 190;
		$this->pdf->marge = 10;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Times","",10);
		$this->pdf->vandatum = $this->selectData['datumVan'];
		$this->pdf->tmdatum = $this->selectData['datumTm'];

 		$this->orderby  = " Portefeuilles.Remisier ";
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
	global $__appvar;
	$this->pdf->__appvar = $this->__appvar;

  $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
  $records = $selectie->getRecords();
  $portefeuilles = $selectie->getSelectie();

	if($records <= 0)
	{
		echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
		$this->progressbar->hide();
		exit;
	}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}

		$rapportageDatumStart= jul2sql($this->selectData[datumVan]);
		$rapportageDatumStop = jul2sql($this->selectData[datumTm]);
		// vul eerst de tijdelijketabel
		$this->pdf->AddPage();
    

    $afmCategorien=array('01'=>'LIQ','02'=>'AAA','03'=>'A','07'=>'IG','04'=>'Non-EUR AAA','05'=>'Non-EUR A','13'=>'RE Europa','09'=>'IG Non-EUR',
    '14'=>'Re ex-Europa','06'=>'Gov EMM','15'=>'HF','08'=>'HY','10'=>'HY Non-EUR','11'=>'EQ','17'=>'PE','12'=>'EQ EMM','16'=>'COMM');
   
    $header=array("Asset Manager","Account Code","Account Type","Account Number","Client");
    
    foreach($afmCategorien as $index=>$waarde)
      array_push($header,$waarde);
    
    $this->pdf->excelSheetName=date("F Y",$this->selectData['datumTm']);
    $this->pdf->excelData[] = $header;


    $db=new DB();
		foreach($portefeuilles as $pdata)
		{ 
		  $query="SELECT Naam FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'";
      $db->SQL($query);
      $vermogensbeheerder=$db->lookupRecord();
		  if($this->progressbar)
			{
				$pro_step += $pro_multiplier;
				$this->progressbar->moveStep($pro_step);
				flush();
			}

      vulTijdelijkeTabel(berekenPortefeuilleWaarde($pdata['Portefeuille'],$rapportageDatumStop),$pdata['Portefeuille'],$rapportageDatumStop);


    $afm=AFMstd($pdata['Portefeuille'],$rapportageDatumStop);
    $afmCategorieverdeling=getAFMWaarden($pdata['Portefeuille'],$rapportageDatumStop);
    
    
    	$tmp=array($vermogensbeheerder['Naam'],'','Managed Account',$pdata['Portefeuille'],$pdata['Client']);
    	foreach ($afmCategorien as $index=>$cat)
      {
        $afmCat=$afmCategorieverdeling['codeKoppel'][$index];
        $waardeEur=$afmCategorieverdeling['verdeling'][$afmCat]['actuelePortefeuilleWaardeEuro'];
        array_push($tmp,round($waardeEur,2));

      }
    	$this->pdf->excelData[] = $tmp;
      
     // verwijderTijdelijkeTabel($pdata['Portefeuille'],$rapportageDatumStop);

		}


		if($this->progressbar)
			$this->progressbar->hide();
	}

	function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$excelData = generateCSV($this->pdf->excelData);
			fwrite($fp,$excelData);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}
	}
  
  function iAMexport($gebruiker,$wachtwoord,$file)
  {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        //curl_setopt($ch, CURLOPT_URL, "http://fm.ilumy.com/api/csv");
        //curl_setopt($ch, CURLOPT_URL, "http://rvv.aeict.nl/AIRS/html/test.php");
        curl_setopt($ch, CURLOPT_URL, "https://app.iassetmonitor.com/api/csv");
        curl_setopt($ch, CURLOPT_POST, true);

        $post = array(
            "username" => $gebruiker,
            "password" => $wachtwoord,
            "file"=>"@$file"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
  }
}
?>