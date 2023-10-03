<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 16 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/12/03 10:32:36 $
    File Versie         : $Revision: 1.24 $

    $Log: CRM_naw_dossier.php,v $
    Revision 1.24  2017/12/03 10:32:36  rvv
    *** empty log message ***

    Revision 1.23  2017/02/15 16:44:14  rvv
    *** empty log message ***

    Revision 1.22  2017/02/13 06:40:22  rvv
    *** empty log message ***

    Revision 1.21  2017/02/11 17:36:22  rvv
    *** empty log message ***

    Revision 1.20  2016/11/21 06:30:24  rvv
    *** empty log message ***

    Revision 1.19  2015/11/08 16:39:40  rvv
    *** empty log message ***

    Revision 1.18  2015/11/07 16:41:11  rvv
    *** empty log message ***

    Revision 1.17  2015/08/05 15:57:18  rvv
    *** empty log message ***

    Revision 1.16  2014/01/22 13:37:20  rvv
    *** empty log message ***

    Revision 1.15  2013/08/10 15:44:59  rvv
    *** empty log message ***

    Revision 1.14  2013/08/07 17:13:24  rvv
    *** empty log message ***

    Revision 1.13  2013/08/04 10:44:18  rvv
    *** empty log message ***

    Revision 1.12  2011/10/16 14:26:53  rvv
    *** empty log message ***

    Revision 1.11  2011/07/27 16:24:13  rvv
    *** empty log message ***

    Revision 1.10  2011/03/13 18:33:46  rvv
    *** empty log message ***

    Revision 1.9  2010/09/15 09:44:43  rvv
    *** empty log message ***

    Revision 1.8  2010/09/11 15:40:46  rvv
    *** empty log message ***

    Revision 1.7  2010/07/25 14:41:11  rvv
    *** empty log message ***

    Revision 1.6  2010/02/14 12:32:09  rvv
    *** empty log message ***

    Revision 1.5  2009/12/15 13:19:26  rvv
    *** empty log message ***

    Revision 1.4  2008/06/30 06:55:48  rvv
    *** empty log message ***

    Revision 1.3  2007/11/02 11:45:18  cvs
    gebruiker konden geen nieuw toevoegen, aangepast

    Revision 1.2  2007/10/09 06:17:58  cvs
    CRM rechten

    Revision 1.1  2006/01/05 16:00:34  cvs
    *** empty log message ***

    Revision 1.2  2005/12/20 09:04:55  cvs
    *** empty log message ***

    Revision 1.1.1.1  2005/12/06 18:20:55  cvs
    no message

    Revision 1.2  2005/11/21 10:08:25  cvs
    *** empty log message ***

    Revision 1.1  2005/11/17 08:10:04  cvs
    *** empty log message ***



*/

class Naw_dossier extends Table
{
  /*
  * Object vars
  */

  var $data = array();

  /*
  * Constructor
  */
  function Naw_dossier()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
		$this->omschrijving="Gespreksverslagen";
    if(strpos($_SERVER['PHP_SELF'],'CRM_nawList.php') > 0)
    {
			$this->omschrijving="Ltst Gespr.Verslag";
    }

  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
	  (db2jul($this->get("datum")) > time())?$this->setError("datum",vt("Datum mag niet in de toekomst liggen.")):true;
		($this->get("kop")=="")?$this->setError("kop",vt("Mag niet leeg zijn!")):true;

		$valid = ($this->error==false)?true:false;
		return $valid;
	}

	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
		if($_SESSION['usersession']['superuser'])
		  return true;
		else
		{
		  switch ($type)
		  {
		    case "edit":
          return GetCRMAccess(0);
          break;
        case "delete":
          return GetCRMAccess(2);
          break;
        default:
          return false;
          break;
      }
		}

	}

	/*
  * Table definition
  */
  function defineData()
  {
    $this->data['name']  = "dossiers";
    $this->data['table']  = "CRM_naw_dossier";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('rel_id',
													array("description"=>"rel_id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

	  $this->addField('dd_reference_id',
													array("description"=>"dd_reference_id",
													"default_value"=>"",
													"db_size"=>"20",
													"db_type"=>"bigint",
													"form_type"=>"text",
													"form_size"=>"20",
													"form_visible"=>false,
													"list_visible"=>false,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('datum',
													array("description"=>"datum",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"right",
													"list_search"=>false,
													"list_order"=>"true",
													"form_extra"=>""));

		$this->addField('type',
													array("description"=>"type",
													"default_value"=>"",
													"db_size"=>"50",
													"db_type"=>"varchar",
													"form_type"=>"selectKeyed",
													"form_extra"=>"onchange='checkContact();'",
													"form_size"=>"50",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('kop',
													array("description"=>"betreft",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"tinytext",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('aanwezig',
													array("description"=>"Aanwezig",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"tinytext",
													"form_type"=>"text",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
                          
		$this->addField('txt',
													array("description"=>"tekst",
													"default_value"=>"",
													"db_size"=>"60",
													"db_type"=>"text",
													"form_type"=>"htmlarea4.14",
													"form_size"=>"60",
													"form_rows"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('memo',
													array("description"=>"memo",
													"default_value"=>"",
													"db_size"=>"255",
													"db_type"=>"tinytext",
													"form_type"=>"textarea",
                          "form_rows"=>"6",
													"form_size"=>"60",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('clientGesproken',
													array("description"=>"Client gesproken",
													"default_value"=>"",
													"db_size"=>"4",
													"db_type"=>"tinyint",
													"form_type"=>"checkbox",
													"form_size"=>"4",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"150",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_usr",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"datum",
													"form_size"=>"0",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('duur',
													array("description"=>"Gespreksduur (hh:mm)",
													"default_value"=>"00:00",
													"db_size"=>"5",
													"db_type"=>"text",
													"form_type"=>"text",
													"form_size"=>"5",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));



  }
}
?>