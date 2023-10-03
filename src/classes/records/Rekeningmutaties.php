<?php
/*
 		Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2018/08/27 09:15:42 $
 		File Versie					: $Revision: 1.34 $
*/

class Rekeningmutaties extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Rekeningmutaties()
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
	  if($type=='verzenden')
	  {
	    global $USR;
	    $db=new DB();
      $query="SELECT MAX(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage FROM Vermogensbeheerders
              Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
      $db->SQL($query);
      $db->Query();
      $data=$db->lookupRecord();
      if($data['CrmTerugRapportage'] > 0)
         return true;
	  }
		return checkAccess($type);
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
	   global $__appvar;
		($this->get("Rekening")=="")?$this->setError("Rekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Afschriftnummer")=="")?$this->setError("Afschriftnummer",vt("Mag niet leeg zijn!")):true;
		($this->get("Volgnummer")=="")?$this->setError("Volgnummer",vt("Mag niet leeg zijn!")):true;
		($this->get("Grootboekrekening")=="")?$this->setError("Grootboekrekening",vt("Mag niet leeg zijn!")):true;
		($this->get("Omschrijving")=="")?$this->setError("Omschrijving",vt("Mag niet leeg zijn!")):true;
		($this->get("Valutakoers")=="")?$this->setError("Valutakoers",vt("Mag niet leeg zijn!")):true;
		($this->get("Grootboekrekening")=="FONDS" && $this->get("Transactietype") == '')?$this->setError("Transactietype",vt("Mag niet leeg zijn bij Fonds!")):true;

	  if($this->get("Grootboekrekening")!="FONDS")
	  {
  	  if($this->get("Grootboekrekening")=="VERM")
	      $this->set("Transactietype",'B');
	    else
	      $this->set("Transactietype",'');
  	}
		// controle of datum <= afschrift datum ligt
		$query = " SELECT Datum FROM Rekeningafschriften WHERE ".
						 " Rekening = '".$this->get("Rekening")."' AND ".
						 " Afschriftnummer = '".$this->get("Afschriftnummer")."' AND".
						 " Datum >= '".$this->get("Boekdatum")."' AND YEAR(Datum) = YEAR('".$this->get("Boekdatum")."') ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		if($DB->Records() < 1)
		{
		  if($__appvar['bedrijf']<>'RCN')
		  	$this->setError("Boekdatum",vt("Valt buiten afschriftdatum!"));
		}

    if($this->get('Transactietype')=='T')
    {
      $query="SELECT Portefeuille FROM Rekeningen WHERE Rekening = '".$this->get("Rekening")."'";
      $DB->SQL($query);
	  	$DB->Query();
		  $portefeuille=$DB->lookupRecord();
      
      if($this->get("Fonds") <> '')
      {
        $query="SELECT SUM(Rekeningmutaties.Aantal) as aantal
FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE 
Rekeningen.Portefeuille='".$portefeuille['Portefeuille']."' AND Rekeningmutaties.Fonds='".$this->get("Fonds")."' AND
YEAR(Rekeningmutaties.Boekdatum)=YEAR('".$this->get("Boekdatum")."') ";
        $DB->SQL($query);
	  	  $DB->Query();
		    $aantal=$DB->lookupRecord();
        if($aantal['aantal'] > 0)
        {
          $this->huidigAantal=$aantal['aantal'];
        }
        else
          $this->setError("Fonds",vt("niet gevonden in portefeuille."));
      }
      else
        $this->setError("Fonds",vt("niet gevonden"));
    }
    
		$DB = new DB();
		$DB->SQL("SELECT id FROM Rekeningmutaties WHERE Rekening = '".$this->get("Rekening")."' AND Afschriftnummer = '".$this->get("Afschriftnummer")."' AND Volgnummer = '".$this->get("Volgnummer")."' ");
		$DB->Query();
		$data = $DB->nextRecord();

		if($DB->records() >0 && $this->get("id") <> $data[id])
		{
			$this->setError("Volgnummer",vt("combinatie rekening, afschriftnummer, volgnummer bestaat al"));
		}

		$valid = ($this->error==false)?true:false;
		return $valid;
	}
  
  function HerrekenFactor()
  {
      $DB=new DB();
      $query="SELECT Portefeuille FROM Rekeningen WHERE Rekening = '".$this->get("Rekening")."'";
      $DB->SQL($query);
	  	$DB->Query();
		  $portefeuille=$DB->lookupRecord();
      
      $query="SELECT Rekeningmutaties.Aantal,Rekeningmutaties.Boekdatum, 
      (Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)-(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet) as terugkoop
FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE 
Rekeningen.Portefeuille='".$portefeuille['Portefeuille']."' AND Rekeningmutaties.Fonds='".$this->get("Fonds")."' 
ORDER BY Rekeningmutaties.Boekdatum";  
      $DB->SQL($query);
		  $DB->Query();
      $n=0;
      $lastFactor=1;
      $totaalAantal=0;
	    while($data = $DB->nextRecord())
      {
        $mutaties[]=$data; 
      }
     
      foreach($mutaties as $data)
      {
        if($data['Aantal'] <> 0 && $n==0)
        {
          $totaalAantal+=$data['Aantal'];
          $query="SELECT id,factor FROM factorVanafDatum WHERE fonds='".$this->get("Fonds")."' AND datum='".$data['Boekdatum']."'";
          if(!$DB->QRecords($query))
          {
            $query="INSERT INTO factorVanafDatum SET fonds='".mysql_real_escape_string($this->get("Fonds"))."',datum='".$data['Boekdatum']."',factor=1";
            $DB->SQL($query);
		        $DB->Query();
          }
        }
      
        if($data['Aantal'] == 0 && $data['terugkoop'] <> 0)
        {
          $lastFactor=($lastFactor - ($data['terugkoop'] / $totaalAantal));
          $query="SELECT id,factor FROM factorVanafDatum WHERE fonds='".$this->get("Fonds")."' AND datum='".$data['Boekdatum']."'";
          if(!$DB->QRecords($query))
          {
            $query="INSERT INTO factorVanafDatum SET fonds='".mysql_real_escape_string($this->get("Fonds"))."',datum='".$data['Boekdatum']."',factor='".$lastFactor."'";
            $DB->SQL($query);
		        $DB->Query();
          }
          else
          {
            $laatsteRecord=$DB->nextRecord();
            if($laatsteRecord['factor'] != $lastFactor)
            {
              echo "Afwijkende factor voor '".$this->get("Fonds")."' op '".$data['Boekdatum']."'  (".$laatsteRecord['factor']." != ".$lastFactor.")";
              exit;
            }
            
          }  
        }
        $n++;
      }
 
     
  }

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['table']  = "Rekeningmutaties";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Rekening',
													array("description"=>"Rekening",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Rekeningen"));

		$this->addField('Afschriftnummer',
													array("description"=>"Afschriftnummer",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Volgnummer',
													array("description"=>"Volgnummer",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Omschrijving',
													array("description"=>"Omschrijving",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"17",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Boekdatum',
													array("description"=>"Boekdatum",
													"default_value"=>"lastworkday",
													"db_size"=>"0",
													"db_type"=>"date",
													"form_type"=>"calendar",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Grootboekrekening',
													array("description"=>"Grootboekrekening",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"5",
													"form_visible"=>true,
													"form_extra"=>"onChange='javascript:grootboekChanged();'",
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Grootboekrekeningen"));

		$this->addField('Valuta',
													array("description"=>"Valuta",
													"default_value"=>"EUR",
													"db_size"=>"4",
													"db_type"=>"char",
													"form_type"=>"select",
													"form_size"=>"4",
													"form_visible"=>true,
													"form_extra"=>"onBlur='javascript:valutaChanged();'",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Valutas"));

		$this->addField('Valutakoers',
													array("description"=>"Valutakoers",
													"default_value"=>"1",
													"db_size"=>"4",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_extra"=>"onFocus=\"javascript:focusveld='Valutakoers';\" onBlur=\"javascript:focusveld='';\"",
													"form_format"=>"%01.8f",
													"list_format"=>"%01.5f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
													array("description"=>"Fonds",
													"default_value"=>"",
													"db_size"=>"25",
													"db_type"=>"varchar",
													"form_extra"=>"onBlur='javascript:fondsChanged();'",
													"form_type"=>"selectKeyed",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Fondsen"));

		$this->addField('Aantal',
													array("description"=>"Aantal",
													"default_value"=>"",
													"db_size"=>"9",
													"db_type"=>"decimal",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"list_visible"=>true,
                          "form_format"=>"%01.6f",
                          "list_format"=>"%01.6f",
													"list_align"=>"right",
													"form_extra"=>"onBlur='javascript:checkFondsAantal();'",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fondskoers',
													array("description"=>"Fondskoers",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"form_format"=>"%01.8f",
													"list_format"=>"%01.5f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Debet',
													array("description"=>"Debet",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_extra"=>"onFocus=\"javascript:focusveld='Debet';\" onBlur=\"javascript:focusveld='';setBedrag('Debet');\"",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Credit',
													array("description"=>"Credit",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_extra"=>"onFocus=\"javascript:focusveld='Credit';\"  onBlur=\"javascript:focusveld='';setBedrag('Credit');\"",
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bedrag',
													array("description"=>"Bedrag",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"double",
													"form_type"=>"text",
													"form_size"=>"8",
													"form_visible"=>true,
													"form_format"=>"%01.2f",
													"list_format"=>"%01.2f",
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Transactietype',
													array("description"=>"Transactietype",
													"default_value"=>"",
													"db_size"=>"5",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Transactietypes"));

		$this->addField('Verwerkt',
													array("description"=>"Verwerkt",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"check",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Memoriaalboeking',
													array("description"=>"Memoriaalboeking",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"check",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Bewaarder',
													array("description"=>"Bewaarder",
													"default_value"=>"",
													"db_size"=>"15",
													"db_type"=>"varchar",
													"form_type"=>"select",
													"form_size"=>"3",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true",
													"keyIn"=>"Bewaarders"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('settlementDatum',
													array("description"=>"settlementDatum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('bankTransactieId',
													array("description"=>"TransactieId",
													"default_value"=>"",
													"db_size"=>"40",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"40",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));    
  }
}
