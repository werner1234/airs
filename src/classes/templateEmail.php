<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/24 15:45:05 $
 		File Versie					: $Revision: 1.7 $

 		$Log: templateEmail.php,v $
 		Revision 1.7  2019/07/24 15:45:05  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/04/13 16:27:43  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/07/02 16:01:36  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/07/10 14:17:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/06/13 14:33:38  rvv
 		*** empty log message ***

 		Revision 1.2  2011/05/25 17:23:06  rvv
 		*** empty log message ***

 		Revision 1.1  2010/11/14 10:52:20  rvv
 		*** empty log message ***

 		Revision 1.13  2010/10/17 09:28:02  rvv

 */



class templateEmail
{

  function templateEmail($body,$subject)
  {
    $this->subject=$subject;
    $this->body=$body;
  }

  function getPortefeuileValues($portefeuile)
  {
    $db=new DB();
    $query="SELECT CRM_naw.id as CRM_id, CRM_naw.*, Portefeuilles.*,
Accountmanagers.Naam as AccountmanagersNaam, Accountmanagers.Titel as AccountmanagersTitel, Accountmanagers.Titel2 as AccountmanagersTitel2
FROM Portefeuilles
LEFT JOIN CRM_naw on Portefeuilles.Portefeuille = CRM_naw.Portefeuille
LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager=Accountmanagers.Accountmanager WHERE Portefeuilles.Portefeuille='$portefeuile'";
    $db->SQL($query);
    $data=$db->lookupRecord(); 
    if($db->records()==0)
    {
      $query="SELECT CRM_naw.id as CRM_id, CRM_naw.*, Portefeuilles.*,
Accountmanagers.Naam as AccountmanagersNaam, Accountmanagers.Titel as AccountmanagersTitel, Accountmanagers.Titel2 as AccountmanagersTitel2
FROM CRM_naw
LEFT JOIN Portefeuilles on CRM_naw.Portefeuille=Portefeuilles.Portefeuille
LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager=Accountmanagers.Accountmanager WHERE CRM_naw.Portefeuille='$portefeuile'";
      $db->SQL($query);
      $data=$db->lookupRecord();
    }
    $data=$this->getAllFields($data);
    return $data;
  }

  function templateData($data)
  {
    $tmpBody=$this->body;
    $tmpSubject=$this->subject;
    foreach ($data as $key=>$value)
    {
      $tmpBody = str_replace( "{".$key."}", $value, $tmpBody);
      $tmpSubject = str_replace( "{".$key."}", $value, $tmpSubject);
    }
    $data=array('body'=>$tmpBody,'subject'=>$tmpSubject);
    return $data;
  }


	function getAllFields($keyValue)
	{
	  $db=new DB();
	  $data=array();
	  global $__appvar,$USR;
	  if($keyValue['Vermogensbeheerder'])
	  {
	    $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Vermogensbeheerder']=$data['*Vermogensbeheerder'];
	  }
	  if($keyValue['Client'])
	  {
	    $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Client']=$data['*Client'];
	  }
	  if($keyValue['Depotbank'])
	  {
	    $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Depotbank']=$data['*Depotbank'];
	  }
	  if($keyValue['Accountmanager'])
	  {
	    $query="SELECT Naam as `*Accountmanager`, Titel as AccountmanagerTitel, Titel2 as AccountmanagerTitel2  FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Accountmanager']=$data['*Accountmanager'];
      $keyValue['AccountmanagerTitel']=$data['AccountmanagerTitel'];
      $keyValue['AccountmanagerTitel2']=$data['AccountmanagerTitel2'];
	  }
	  if($keyValue['tweedeAanspreekpunt'])
	  {
	    $query="SELECT Naam as `*tweedeAanspreekpunt` , Titel as tweedeAanspreekpuntTitel, Titel2 as tweedeAanspreekpuntTitel2   FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*tweedeAanspreekpunt']=$data['*tweedeAanspreekpunt'];
      $keyValue['tweedeAanspreekpuntTitel']=$data['tweedeAanspreekpuntTitel'];
      $keyValue['tweedeAanspreekpuntTitel2']=$data['tweedeAanspreekpuntTitel2'];
	  }
	  if($keyValue['Remisier'])
	  {
	    $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Remisier']=$data['*Remisier'];
	  }
 	  if($keyValue['accountEigenaar'])
	  {
	    $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
	  }    
	  if($keyValue['RapportageValuta'])
	  {
	    $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*RapportageValuta']=$data['*RapportageValuta'];
	  }
	  $keyValue['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
	  $keyValue['huidigeGebruiker']=$USR;
	  return $keyValue;
	}
}
?>