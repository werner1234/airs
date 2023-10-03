<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/01/27 17:31:22 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PreProcessor_L72.php,v $
 		Revision 1.4  2018/01/27 17:31:22  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/12/07 06:44:03  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/12/06 16:50:06  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/07/22 18:22:05  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/07/19 19:30:24  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/07/15 16:13:43  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/07/12 15:46:42  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/05/29 06:28:02  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/05/21 09:55:30  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/02/19 10:59:55  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/10/04 11:52:21  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/06/13 13:16:01  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/11/07 17:07:29  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/11/03 18:14:13  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/09/09 17:35:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/09/01 14:27:48  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/07/29 10:24:33  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/07/22 12:56:44  rvv
 		*** empty log message ***


*/

class PreProcessor_L72
{
	function PreProcessor_L72($portefeuille,$datum='',$pdf)
  {
    global $__appvar;
    $this->portefeuille = $portefeuille;
	  $this->db = new DB();
    $this->categorieData = array();
    $this->pdf = $pdf;
    $dagen=array();
    if($datum <> '')
    {
      $dagen[]=$datum;
    }
    else
    {
	    $query="SELECT rapportageDatum FROM TijdelijkeRapportage WHERE portefeuille = '$portefeuille' ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY rapportageDatum";
	    $this->db->SQL($query);
	    $this->db->Query();
	    
	    while($data=$this->db->nextRecord())
	    {
	      $dagen[]=$data['rapportageDatum'];
	    }
    }

	  foreach ($dagen as $dag)
	  {
	    $this->bepaalRenteRekeningen($dag);
      $this->bepaalOBLOmschrijving($dag);

	  }
//exit;
  }

  function bepaalRenteRekeningen($dag)
  {
    global $__appvar;
    $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $query="SELECT TijdelijkeRapportage.id,TijdelijkeRapportage.Rekening,Rekeningen.Tenaamstelling FROM TijdelijkeRapportage 
JOIN Rekeningen on TijdelijkeRapportage.Rekening=Rekeningen.Rekening WHERE $vasteWhere";
    $this->db->SQL($query);
	  $this->db->Query();
    $rekenignen=array();
	  while($data=$this->db->nextRecord())
      $rekenignen[]=$data;

    foreach($rekenignen as $rekeningData)
    {
      $query = "SELECT id,Rentepercentage,DatumTot FROM
DepositoRentepercentages WHERE DatumVan <='$dag' AND DatumTot>='$dag' AND Rekening='".$rekeningData['Rekening']."' order by DatumVan desc limit 1";
      $this->db->SQL($query);
      $data=$this->db->lookupRecord();
      if($data['id']<>0)
      {
        if($data['Rentepercentage'] <> 0)
          $percentageTxt=$data['Rentepercentage']."% ";
        else
          $percentageTxt='';

        $queries[] = "UPDATE TijdelijkeRapportage SET 
         Fondsomschrijving='".mysql_real_escape_string($rekeningData['Tenaamstelling']." ".$percentageTxt.date('d-m-Y',db2jul($data['DatumTot'])))."'
         WHERE id='" . $rekeningData['id'] . "'";
      }
      else
      {
        $queries[] = "UPDATE TijdelijkeRapportage SET 
         Fondsomschrijving='".mysql_real_escape_string($rekeningData['Tenaamstelling'])."'
         WHERE id='" . $rekeningData['id'] . "'"; //." ".substr($rekeningData['Rekening'],0,strlen($rekeningData['Rekening'])-3)
      }
    }

    foreach($queries as $query)
    {
   	  $this->db->SQL($query); 
	    $this->db->Query();    
    }
    unset($queries);
  }

  function bepaalOBLOmschrijving($dag)
  {
    global $__appvar;
    $vasteWhere="TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $query="SELECT TijdelijkeRapportage.id,TijdelijkeRapportage.Fondsomschrijving, TijdelijkeRapportage.Fonds,
substr(TijdelijkeRapportage.Fondsomschrijving,INSTR(TijdelijkeRapportage.Fondsomschrijving ,'% ' )+1) as sortering FROM TijdelijkeRapportage 
JOIN Fondsen on TijdelijkeRapportage.Fonds=Fondsen.Fonds WHERE Fondsen.fondssoort='OBL' AND  $vasteWhere ORDER BY sortering";
    $this->db->SQL($query);
    $this->db->Query();
    $OBL=array();
    while($data=$this->db->nextRecord())
      $OBL[]=$data;

    $i=0;
    $lastSortering='';
    foreach($OBL as $obligatie)
    {
      if($obligatie['sortering']<>$lastSortering)
        $i++;
      //$query = "SELECT id,Rentepercentage,Fonds FROM Rentepercentages WHERE (GeldigVanaf >='$dag' OR GeldigVanaf='0000-00-00') AND Fonds='".$obligatie['Fonds']."'  AND Datum <= '".$dag."' order by GeldigVanaf desc limit 1";
      //$this->db->SQL($query);
      //$data=$this->db->lookupRecord();
      $data=getRentePercentage($obligatie['Fonds'],$dag);
      if($data['Datum']<>'')
      {
        $omschrijving=number_format($data['Rentepercentage'],3,",",".")."% ".trim($obligatie['sortering']);
        //listarray($obligatie);
        // listarray($data); echo $omschrijving;
        $queries[] = "UPDATE TijdelijkeRapportage SET fondspaar='$i',
         Fondsomschrijving='".mysql_real_escape_string($omschrijving)."' WHERE id='" . $obligatie['id'] . "'";
      }
      else
      {
        $queries[] = "UPDATE TijdelijkeRapportage SET fondspaar='$i' WHERE id='" . $obligatie['id'] . "'";
      }
      $lastSortering=$obligatie['sortering'];
    }
//listarray($queries);
    foreach($queries as $query)
    {
      $this->db->SQL($query);
      $this->db->Query();
    }
    unset($queries);
  }

  function verwijderKoppelingen($dag)
  {
    global $__appvar;

    $cat='AAND';
    $query="UPDATE TijdelijkeRapportage SET 
     beleggingscategorie='". $this->categorieData[$cat]['Beleggingscategorie']."',
     beleggingscategorieOmschrijving='". $this->categorieData[$cat]['Omschrijving']."',
     beleggingscategorieVolgorde='". $this->categorieData[$cat]['Afdrukvolgorde']."',
     TijdelijkeRapportage.add_date=now() WHERE fondspaar=100 AND
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $this->db->SQL($query);
    $this->db->Query();

    $velden=array('regio','beleggingssector');
    $veldenSql='';
    foreach ($velden as $veld)
      $veldenSql.="TijdelijkeRapportage.".$veld."='', TijdelijkeRapportage.".$veld."Volgorde='', TijdelijkeRapportage.".$veld."Omschrijving='', ";
    
    $query="UPDATE TijdelijkeRapportage SET $veldenSql TijdelijkeRapportage.add_date=now() WHERE beleggingscategorie <> 'AAND' AND
    TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$dag' ".$__appvar['TijdelijkeRapportageMaakUniek'];
	  $this->db->SQL($query);
	  $this->db->Query();


  }


}


?>