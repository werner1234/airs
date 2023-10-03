<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/11/10 18:23:09 $
 		File Versie					: $Revision: 1.27 $

 		$Log: Fondskoersen.php,v $
 		Revision 1.27  2018/11/10 18:23:09  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2017/07/09 07:29:38  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/07/08 17:14:42  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/11/11 13:05:13  rm
 		4032
 		
 		Revision 1.23  2015/04/29 15:19:33  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2015/04/15 18:12:38  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2015/04/13 07:54:29  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/04/11 17:03:20  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/01/07 14:22:50  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/08/13 15:52:32  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/08/09 14:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/05/21 15:17:28  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/03/27 17:06:23  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2012/12/30 14:23:54  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2011/09/08 07:15:32  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2011/09/03 14:27:42  rvv
 		*** empty log message ***

 		Revision 1.11  2011/08/31 15:18:50  rvv
 		*** empty log message ***

 		Revision 1.10  2010/07/25 14:40:47  rvv
 		*** empty log message ***

 		Revision 1.9  2009/10/14 15:54:22  rvv
 		*** empty log message ***

 		Revision 1.8  2008/07/02 07:21:31  rvv
 		*** empty log message ***

 		Revision 1.7  2008/06/30 06:56:33  rvv
 		*** empty log message ***


*/

class Fondskoersen extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Fondskoersen()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	function checkAccess($type)
	{
	  $auth=checkAccess($type);
	  if($type=='delete' && $auth>0 )
    {
      return $this->validate();
    }
   
		return $auth;
	}

	function validate()
	{
	  global $__appvar;
		($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
		($this->get("Koers")=="")?$this->setError("Koers",vt("Mag niet leeg zijn!")):true;
		(!isNumeric($this->get("Koers")))?$this->setError("Koers",vt("Moet een getal zijn.")):true;
    
//    $this->set('oorspKrsDt',$this->get("Datum"));

    $DB = new DB();
    $query = "SELECT
Vermogensbeheerders.koersExport
FROM
Vermogensbeheerders
INNER JOIN VermogensbeheerdersPerBedrijf ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE 
 VermogensbeheerdersPerBedrijf.Bedrijf='".$__appvar["bedrijf"]."'";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
    if(isset($data['koersExport']))
      $koersExportOnly=$data['koersExport'];
    else  
      $koersExportOnly=0;
      
     
		// check of Fonds al bestaat op deze datum. (alleen bij nieuwe Fondsen).
		$query = "SELECT id,Koers FROM Fondskoersen ".
						 " WHERE Fonds = '".$this->get("Fonds")."' ".
						 " AND Datum = '".$this->get("Datum")."' ";
		$DB->SQL($query);
		$DB->Query();
		$data = $DB->NextRecord();
 
		if($DB->Records() > 0 && $this->get("id") <> $data['id'])
		{
			$this->setError("Datum", vtb("Op deze datum is al een Koers toegevoegd (%s)", array($data['Koers'])));
		}

		$cfg=new AE_config();
		$lockDatum=$cfg->getData('fondskoersLockDatum');

    $lockJul=db2jul($lockDatum);
    $time=time();
    $lockUit=false;
    $forceerTxt='';

    $maand=date('m',$time);
    $jaar=date('Y',$time);
    if(date('d',$time) < 8)
      $maand-=1;
    $lockTestJul=mktime(0,0,0,$maand,0,$jaar);
    if($lockTestJul>$lockJul)
    {
      if(db2jul($this->get('Datum'))>$lockJul)
        $lockUit=true;
    }
    else
      $lockTestJul=$lockJul;
    
    if(db2jul($this->get('Datum'))>time())
    {
      $this->setError("Datum",vt("De opgegeven datum ligt in de toekomst."));
    }
    
		if($lockTestJul >= db2jul($this->get('Datum')) && $koersExportOnly==0 )
		{
		  $query="SELECT Vermogensbeheerder,max(jaar) as laatsteJaar FROM
(
SELECT
Rekeningmutaties.Fonds,
Portefeuilles.Vermogensbeheerder,
year(Rekeningmutaties.Boekdatum) as jaar
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE Rekeningmutaties.Fonds='".$this->get('Fonds')."'
GROUP BY Portefeuilles.Vermogensbeheerder,jaar
) as historie
GROUP BY Vermogensbeheerder order by laatsteJaar desc, Vermogensbeheerder ";
	  	$DB->SQL($query);
		  $DB->Query();
      $aanwezigTxt="<br>\nAanwezig bij";
      if($this->get('id') < 1 || $lockUit)
        $forceerTxt="<input type='checkbox' name='forceerUpdate' id='forceerUpdate' value='1'> Toch opslaan?";
      $n=0;
      while($data=$DB->nextRecord())
      {
        $aanwezigTxt.=",".$data['Vermogensbeheerder']."(".$data['laatsteJaar'].")";
        $n++;
        if($n%5==0)
          $aanwezigTxt.="<br>\n";
      }
    
      if(isset($_GET['forceerUpdate']) && $_GET['forceerUpdate']==1)
      {
        //Forceer update.
      }
      else
      {
        if($forceerTxt <> '')
          $this->setError("Datum", vtb("Klopt het dat er een koers in een vorige periode moet worden opgeslagen? %s", array($aanwezigTxt)) . $forceerTxt);
        else
		      $this->setError("Datum", vtb("Het aanpassen van koersen met een datum <= '%s' is niet meer mogelijk. %s", array($lockDatum, $aanwezigTxt)));
      }  
		}
    else
    {
      $query="SELECT benchmark FROM benchmarkverdeling WHERE fonds='".$this->get("Fonds")."'";
	  	$DB->SQL($query);
		  $DB->Query();
      $records=$DB->records();
      if($records > 0)
      {
        include_once("../classes/benchmarkverdelingBerekening.php");
        $berekening=new benchmarkverdelingBerekening();
      }
      while($data=$DB->nextRecord())
      {
        $berekening->bereken($data['benchmark'],$this->get('Datum'));
      }
      if($records > 0)
      {
        $berekening->updateKoersen();
        //listarray($berekening->error);
      }
    }
    


		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
  * Table definition
  */
  function defineData()
  {
		global $__appvar;
    $this->data['table']  = "Fondskoersen";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													'select_query' => "SELECT Fonds,Fonds FROM Fondsen ORDER BY Fonds",
													'select_query_ajax' => "SELECT Fonds,Fonds FROM Fondsen WHERE Fonds='{value}'",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>true,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Datum',
													array("description"=>"Datum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"lastworkday",
													"form_type"=>"calendar",
                          "form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
													"form_extra"=>" onchange=\"date_complete(this);\"",  
													
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
                          "noHtmlspecialchars"=>true,
													"list_search"=>false,
													"list_order"=>"true"));

if($__appvar['bedrijf']=='BOX' || $__appvar['bedrijf']=='TEST')
		$this->addField('oorspKrsDt',
													array("description"=>"Oorspronkelijke koersdatum",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"default_value"=>"lastworkday",
													"form_type"=>"calendar",
													"form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
													"form_extra"=>" onchange=\"date_complete(this);\"",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Koers',
													array("description"=>"Koers",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_visible"=>true,
													"form_format"=>"%01.8f",
													"list_format"=>"%01.8f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>