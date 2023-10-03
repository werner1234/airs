<?php
$baseDir = realpath(dirname(__FILE__)."/..");

include_once($baseDir . "/config/local_vars.php");
include_once($baseDir . "/config/vars.php");
include_once($baseDir . "/config/applicatie_functies.php");

class AE_CustomTemplate
{
  var $db = null;
  var $categorie = null;
  var $variableData = array();

  // Op basis van een van deze 2 velden kan de parse data opgehaald worden.
  var $parseSelector = array(
    'crm_id',
    'portefeuille_id'
  );
  /**
   * Sets the appvar to this class
   * set the recordlocation for including
   *
   * @global type $__appvar
   */
  function AE_CustomTemplate ($categorie = null)
  {
    GLOBAL $__appvar;

    $data = array_merge($_POST, $_GET);
    $action = (isset ($data['action'])?$data['action']:'');

    $this->AEArray = new AE_Array();
    $this->json = new AE_Json();
    $this->AETemplate =  new AE_template();
    $this->templateParser = new AE_cls_TemplateParser();


    // object gegevens instellen
    $this->object = new custom_templates();
    $this->editObject = new editObject($this->object);
    $this->editObject->controller('edit', $data);
    $this->emptyObject = new customTemplate();

    $this->categorie = ( ! empty ($categorie) ? $categorie : null );

    $this->templateValues = null;
    
    if ( $action === 'edit' ) {
      $json = $this->object->get('template');
      $json = utf8_encode($json);
      $json = $this->json->json_decode($json, true);
      foreach ( $json as $key => $value) {
        $value = utf8_decode($value);
        $json[$key] = $value;
      }
      $this->templateValues = $json;//$this->json->json_decode($json, true);
    }
    $this->getTemplateHtmlEditorAjax ();
    $this->db = new DB();
  }

  function getCategories () {
  
  }
  
  /**
   * Haal een lijst met templates op van de bijhorende categorie of alle wanneer categorie leeg is.
   * @param null $categorie
   * @return array
   */
  function getListTemplates ($categorie = null) {
    $where = '';
  
    $categorie = $this->getCategorie($categorie);
    if ( ! empty ($categorie) ) {
      $where = " `categorie` = '".$categorie."'";
    }

    return $this->object->getList($where, 'id', 'naam');
  }


  /**
   * Haal de template op op basis van het template id
   * @param null $templateId
   * @return array|null
   */
  function getTemplateById ($templateId = null) {
    $template = $this->object->parseByArray('all', array(
      'conditions' => array(
        'id' => $templateId
      ),
      'limit' => 1
    ));

    if ( $template ) {
      return $template;
    }
    return null;
  }


  function getDefaultVariable () {
    global $__appvar;
    global $USR;

    $this->variableData['emailHandtekening'] = $_SESSION['usersession']['gebruiker']['emailHandtekening'];
    $this->variableData['huidigeDatum'] = date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
    $this->variableData['huidigeGebruiker'] = $USR;

    $query="SELECT Naam,titel FROM Gebruikers WHERE Gebruiker='".$USR."'";
    $db = new DB();
    $db->SQL($query);
    $dataGebr=$db->lookupRecord();
    $this->variableData['GebruikerNaam'] = $dataGebr['Naam'];
    $this->variableData['GebruikerTitel'] = $dataGebr['titel'];
    $this->variableData['nieuwePagina'] = '<div style="page-break-before:always">&nbsp;</div>';
  }

  function getVariableByPportefeuille ($portefeuilleId = null) {
    if ( empty ($portefeuilleId) ) {return false;}

    $crmObj = new Naw();
    //CRM gegevens ophalen
    $crmNawData = $crmObj->parseBySearch(array ('portefeuille' => $portefeuilleId));
    foreach ($crmNawData as $key => $value) {
      $this->variableData[$key] = $value;
    }


    //portefeuille gegevens ophalen
    $portefeuilleObj = new Portefeuilles();
    $portefeuilleData = $portefeuilleObj->parseBySearch(array('Portefeuille' => $portefeuilleId));
    foreach ($portefeuilleData as $key => $value) {
      $this->variableData[$key] = $value;
    }

    $lpw = new laatstePortefeuilleWaarde();
    $lpwData = $lpw->parseBySearch(array ('portefeuille' => $portefeuilleId));
    foreach ($lpwData as $key => $value) {
      $this->variableData[$key] = $value;
    }

    $this->getDefaultVariable ();
    $this->getExtraFields ();

    return $this->variableData;
  }

  function getVariableByDebId ($debId = null) {
    global $USR;
    global $__appvar;

    $cfg=new AE_config();
    $standaardbrief = $cfg->getData('standaardbrief');
    $rtfDateFormat=$cfg->getData('rtfDateFormat');
    $rtfGetalFormat=$cfg->getData('rtfGetalFormat');
    if($rtfDateFormat=='')
      $rtfDateFormat='%d-%m-%Y';

    $dateConversion=array('%d-%m-%Y'=>'%d-%m-%Y','%d %M %Y'=>'%d %B %Y');
    $phpDateFormat = $dateConversion[$rtfDateFormat];

    $tables = array('CRM_naw',"Portefeuilles","laatstePortefeuilleWaarde");
    $db = new DB();

    $dateFields=array();
    foreach ($tables as $table)
    {
      $query = "SHOW fields FROM $table ";
      $db->SQL($query);
      $db->Query();
      while($data = $db->nextRecord())
      {

        $type=substr($data['Type'],0,4);

        switch ($type)
        {
          case 'date':
            if($rtfDateFormat=='%d-%m-%Y')
              $fields[$data['Field']] = array('select'=>"if($table".'.'.$data['Field']." <> '0000-00-00',DATE_FORMAT($table".'.'.$data['Field'].",\"$rtfDateFormat\"),'') as ".$data['Field']);
            else
            {
              $fields[$data['Field']] = array('select'=>"$table".'.'.$data['Field']." as ".$data['Field']);
              $dateFields[$data['Field']]=$data['Field'];
            }
            break;
          case 'char':
            if($data['Type']=='char(1)')
              $fields[$data['Field']] = array('select'=>"replace(replace($table.".$data['Field'].",'N','Nee'),'J','Ja')  as ".$data['Field']);
            else
              $fields[$data['Field']] = array('select'=>$table.'.'.$data['Field']);
            break;
          case 'doub':
          case 'int(':
            if($rtfGetalFormat=='1000')
              $fields[$data['Field']] = array('select'=>"replace(round(`$table`.`".$data['Field']."`, 0),'.',',')  as `".$data['Field']."`");
            elseif($rtfGetalFormat=='1000,00')
              $fields[$data['Field']] = array('select'=>"replace(round(`$table`.`".$data['Field']."`, 2),'.',',')  as `".$data['Field']."`");
            elseif($rtfGetalFormat=='1.000')
              $fields[$data['Field']] = array('select'=>"replace(replace(replace(format(convert(replace(`$table`.`".$data['Field']."`, ',', '.'), decimal(10,0)), 0), ',', 'x'), '.', ','), 'x', '.')  as `".$data['Field']."`");
            elseif($rtfGetalFormat=='1.000,00')
              $fields[$data['Field']] = array('select'=>"replace(replace(replace(format(convert(replace(`$table`.`".$data['Field']."`, ',', '.'), decimal(12,2)), 2), ',', 'x'), '.', ','), 'x', '.')  as `".$data['Field']."`");
            else
              $fields[$data['Field']] = array('select'=>'`'.$table.'`.`'.$data['Field'].'`');
            break;
          default:
            $fields[$data['Field']] = array('select'=>'`'.$table.'`.`'.$data['Field'].'`');
        }
      }
    }

    $unsetFields = array('add_date','change_date','add_user','change_user');
    foreach ($unsetFields as $field)
      unset($fields[$field]);
    ksort($fields);
    $n=0;


    $query = "SELECT (SELECT Naam FROM Gebruikers WHERE Gebruiker ='$USR') as Gebruiker, ";
    foreach ($fields as $key=>$waarden)
    {
      if($n >0)
        $query .= ",\n ";
      $query .= $waarden['select'];
      $n++;
    }
    $query .= " FROM CRM_naw ".
      //  LEFT JOIN CRM_naw_cf    ON CRM_naw.id          = CRM_naw_cf.rel_id
      " LEFT JOIN Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
                   LEFT JOIN laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";
    $query .= " WHERE CRM_naw.id = '".$debId."'";


    $NAW = new db();

    $NAW->SQL($query);
    $nawRec = $NAW->lookupRecord();

    foreach ($dateFields as $dateField)
    {
      if($nawRec[$dateField] == '0000-00-00')
      {
        $nawRec[$dateField]='';
      }
      else
      {
        $nawRec[$dateField]=adodb_db2jul($nawRec[$dateField]);
        $nawRec[$dateField]=adodb_date('d',$nawRec[$dateField])." ".$__appvar["Maanden"][adodb_date('n',$nawRec[$dateField])]." ".adodb_date('Y',$nawRec[$dateField]); //$nawRec[$dateField]=strftime ($phpDateFormat,$nawRec[$dateField]);
      }
    }
//      $nawRec['verzendAanhef']=$nawRec['verzendAanhef'];  //Moet het verzendAanhef veld ook worden gevuld?
//      $nawRec['verzendAdres']=$nawRec['adres'];
//      $nawRec['verzendPc']=$nawRec['pc'];
//      $nawRec['verzendPlaats']=$nawRec['plaats'];
//      $nawRec['verzendLand']=$nawRec['land'];

    $conversieVelden = array('ondernemingsvorm'=>'Rechtsvorm');
    foreach ($conversieVelden as $key=>$value)
    {
      $conversieData[$key] = GetSelectieVelden($value);
    }

    foreach ($conversieVelden as $key=>$value)
    {
      if(key_exists($key,$nawRec))
      {
        $nawRec[$key] =  $conversieData[$key][$nawRec[$key]];
      }
    }

    if($nawRec['Vermogensbeheerder'] != '')
    {
      $query= "SELECT CrmExtraSpatie FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$nawRec['Vermogensbeheerder']."'";
      $NAW->SQL($query);
      $vermogensbeheerder = $NAW->lookupRecord();
      $extraSpatie = $vermogensbeheerder['CrmExtraSpatie'];
    }
    if($extraSpatie > 0)
    {
      foreach ($nawRec as $key=>$value)
      {
        if($value != '')
          $nawRec[$key] = "$value ";
      }
    }

    $this->variableData = $nawRec;
    $this->getDefaultVariable ();
    $this->getExtraFields ();

    return $this->variableData;
  }

  function getExtraFields ()
  {
    $db=new DB();
    $data=array();
    global $__appvar,$USR;

    $velden=array('Vermogensbeheerder','Client','Depotbank','Accountmanager','tweedeAanspreekpunt','Remisier','RapportageValuta','accountEigenaar');
    foreach($velden as $veld)
      $this->variableData['*'.$veld]='';

    if($this->variableData['Vermogensbeheerder'])
    {
      $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->variableData['Vermogensbeheerder']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['Client'])
    {
      $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$this->variableData['Client']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['Depotbank'])
    {
      $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$this->variableData['Depotbank']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['custodian'])
    {
      $query="SELECT Omschrijving as `*custodian` FROM Depotbanken WHERE Depotbank='".$this->variableData['custodian']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['accountEigenaar'])
    {
      $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$this->variableData['accountEigenaar']."'";
      $db->SQL($query);
      $data=$db->lookupRecord();
      $this->variableData['*accountEigenaar']=$data['*accountEigenaar'];
    }
    if($this->variableData['Accountmanager'])
    {
      $query="SELECT Naam as `*Accountmanager` FROM Accountmanagers WHERE Accountmanager='".$this->variableData['Accountmanager']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['tweedeAanspreekpunt'])
    {
      $query="SELECT Naam as `*tweedeAanspreekpunt` FROM Accountmanagers WHERE Accountmanager='".$this->variableData['tweedeAanspreekpunt']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['Remisier'])
    {
      $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$this->variableData['Remisier']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }
    if($this->variableData['RapportageValuta'])
    {
      $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$this->variableData['RapportageValuta']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $this->variableData = array_merge($this->variableData,$value);
    }

    $this->variableData['huidigeDatum'] = date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
    $this->variableData['huidigeGebruiker'] = $USR;

  }

  /**
   * Controleer of een template combinatie categorie & naam al bestaat.
   * @param $categorie
   * @param $naam
   * @return bool
   */
  
  function templateExists ($categorie, $naam, $returnTemplate = false) {
  
    $template = $this->object->parseByArray('all', array(
      'conditions' => array(
        'naam' => $naam,
        'categorie' => $categorie
      )
    ));
    
    if ( ! empty ($template) ) {
      $template = $template[0];
      if ( $returnTemplate === true ) {
        return $template;
      }
      return true;
    }
    return false;
  }
  
  function getTemplateSelect ($categorie = null) {
    $templates = $this->getListTemplates($categorie);
    
    $templateSelect = '<option value="">-</option>';
    foreach ( $templates as $templateKey => $templateName ) {
      $templateSelect .= '<option value="' . $templateKey . '">' . $templateName. '</option>';
    }
    
    return $templateSelect;
  }
  
  function addContentToField($field = null, $function = null, $content = null)
  {
    return '
    
      function ' . $function . ' (element) {
        if ( element.checked === true ) {
          addToData = ' . json_encode(array('data' => $content)) . ';
        
            if ( typeof CKEDITOR != "undefined" && typeof CKEDITOR.instances["' . $field . '"] != "undefined" ) {
              curData = CKEDITOR.instances["' . $field . '"].getData();
              newData = curData + addToData.data;
              CKEDITOR.instances["' . $field . '"].setData(newData);
            } else {
  //            $("#" + ' . $field . ').html(fieldValue);
  //            $("#" + ' . $field . ').val(fieldValue);
            }
          }
        }
    
    ';
  }
  

  
  /**
   * Haalt de template
   * @param $templateId / $_GET['templateId']
   * @return Template Data
   */
  function getTemplate ($templateId = null, $categorie = null) {
    $template = null;
    if ( isset ($_GET['templateId']) && ! empty ($_GET['templateId']) ) {
      $template = (int) $_GET['templateId'];
    } elseif ( ! empty ($templateId) ) {
      $template = (int) $templateId;
    }

    if ( ! $template ) {
      $categorie = $this->getCategorie($categorie);

      if ( ! empty ($categorie) ) {
        $this->returnEmptyTemplate($categorie);
      } else {
        $this->returnData(false);
      }
    }
    
    $templateData = $this->object->parseById($template);

    $json = $templateData['template'];
    $json = utf8_encode($json);
    $json = $this->json->json_decode($json, true);
    foreach ( $json as $key => $value) {
      $value = utf8_decode($value);
      $json[$key] = $value;
    }

    $templateData = $json;//$this->json->json_decode($templateData['template'], true);

    //Controle of we de template willen parsen of niet
    if ( isset ($_GET['parseData']) && (int) $_GET['parseData'] === 1 ) {
      if ( isset ($_GET['portefeuille_id']) ) {
        $this->templateParser->setData($this->getVariableByPportefeuille($_GET['portefeuille_id']));
      }

      if ( isset ($_GET['crm_id']) ) {
        $this->templateParser->setData($this->getVariableByDebId($_GET['crm_id']));
      }

      foreach ( $templateData as $key => $value ) {
        if ( ! is_array($value) ) {
          $templateData[$key] = $this->templateParser->ParseData($value);
        }
      }

    }

    return $this->returnData($templateData);
  }
  
  
  /**
   * Ophalen van ajax code voor het vullen van de velden bij de dropdown.
   * @param null $categorie
   * @return string
   */
  function getTemplateSelectAjax ($categorie = null, $options = array()) {

    $parseData = 0;
    if ( isset ($options['parseData']) && $options['parseData'] === true ) {
      $parseData = 1;
    }

    $parseWith = '';
    foreach ( $this->parseSelector as $key ) {
      if ( isset ($options[$key]) ) {
        $parseWith .= $key . ': "' . $options[$key] . '",';
      }
    }

    return '
      $(document).on("change", "#templateSelect", function () {
        $.ajax({
          url : "lookups/ajaxLookup.php",
          type: "GET",
          dataType: "json",
          data : {
            parseData : "' . $parseData . '",
            ' . $parseWith . '
            fromClass : "AE_CustomTemplate",
            type : "getTemplate",
            templateId: $(this).val(),
            categorie: "' . $this->getCategorie($categorie) . '"
          },
          success:function(data, textStatus, jqXHR) {
            $.each( data, function (field, fieldValue) {
              $("#" + field).html(fieldValue);
              $("#" + field).val(fieldValue);
              if ( typeof CKEDITOR != "undefined" && typeof CKEDITOR.instances[field] != "undefined" ) {
                CKEDITOR.instances[field].setData(fieldValue);
              }
              

            });
          }
        });
      });
    ';
  }
  
  /**
   *
   */
  function getTemplateHtmlEditorAjax () {
    global $editcontent;
    global $content;
    $editcontent['jsincludes'] .= $this->AETemplate->loadJs('ckeditor_4.14.0/ckeditor');
    $content['jsincludes'] .= $this->AETemplate->loadJs('ckeditor_4.14.0/ckeditor');

    $content['javascript'] .= "
    $(function () {
      $('.textEditor').each(function (e) {
        CKEDITOR.replace(this.id,
          {
            enterMode: CKEDITOR.ENTER_BR,
            allowedContent: true,
            extraPlugins: 'pastebase64'
          });
      });
      })
    ";

    $editcontent['javascript'] .= "
    $(function () {
      $('.textEditor').each(function (e) {
        CKEDITOR.replace(this.id,
          {
            enterMode: CKEDITOR.ENTER_BR,
            allowedContent: true,
            extraPlugins: 'pastebase64'
          });
      });
      })
    ";

  }

  /**
   * Categorie ophalen bij bijhorend Template
   * @param null $categorie
   * @return null
   */
  function getCategorie ($categorie = null) {
    $returnCategorie = null;
    
    if ( isset ($_GET['categorie']) && ! empty ($_GET['categorie']) ) {
      $returnCategorie = $_GET['categorie'];
    } elseif ( ! empty ($categorie) ) {
      $returnCategorie = $categorie;
    } elseif ( ! empty ($this->categorie) ) {
      $returnCategorie = $this->categorie;
    }
    
    return $returnCategorie;
  }
  
  
  /**
   * Leeg template bij lege selectie van template
   * @param $categorie
   * @return false | template waarden
   */
  function returnEmptyTemplate ($categorie = null) {
    if ( isset ($this->object->template[$categorie]) ) {
      $returnData = array();
      $templateFields = $this->object->template[$categorie]['fields'];
      foreach ( $templateFields as $field => $fieldData ) {
        $returnData[$field] = '';
      }
      $this->returnData($returnData);
    } else {
      $this->returnData(false);
    }
  }
  
  /**
   * Stuur data terug, kan json of gewone data zijn.
   * @param $data
   * @return Array/Json
   */
  function returnData ($data) {
    if( requestType('ajax') ) {
      if ( ! is_array($data) ) {
        $data = array($data);
      }
      echo $this->json->json_encode($data);
      exit();
    } else {
      return $data;
    }
  }
  
  /**
   * Is een template meertalig
   * @param null $categorie
   * @return bool
   */
  function isTranslatable ($categorie = null)
  {
    if ( isset ($this->object->template[$categorie]['translatable']) ) {
      return (bool) $this->object->template[$categorie]['translatable'];
    }
    return false;
  }
  
  function getLanguages($categorie)
  {
    if ( isset ($this->object->template[$categorie]['translatable']) ) {
      return array('', 'eng');
    }
    return array('');
  }
  
  
  /**
   * Opbouwen van het het formulier
   * @param null $categorie
   * @param bool $asEdit
   * @return null
   */
  function buildForm ($categorie = null, $asEdit = true)
  {
    if ( ! $categorie ) {
      return null;
    }
    $returHtml = '';
    $returHtmlForm = '';
    if ($this->object->template[$categorie]['dbFieldSelectie'] === true)
    {
      $_SESSION['submenu'] = New Submenu();
      $_SESSION['submenu']->addItem($this->getSelectDbFields());
      $_SESSION['shortcut'] = null;
    }


    $returHtml .= '
      <div class="formHolder">
        ' . $this->getLangSwitcher($categorie) . '
        <div class="formTitle textB">' . vt('Template invoervelden') . '</div>
        <div class="formContent padded-10 pl-5">
          <div class="tab-content" id="myTabContent">
    ';

    $languageArray = $this->getLanguages($categorie);

    foreach ($languageArray as $type)
    {

      $returHtml .= '<div class="tab-pane fade ' . (empty ($type)?' show active':'') . '" id="' . (!empty ($type)?'' . $type:'def') . '" role="tabpanel" aria-labelledby="' . (!empty ($type)?'' . $type:'def') . '-tab">';
      foreach ($this->object->template[$categorie]['fields'] as $field => $data)
      {
        if ( isset ($data['translatable']) && ( ! empty ($type) && $data['translatable'] === false )  ) {
          continue;
        }
        $field = $field . (!empty ($type)?'_' . $type:'');
        $extraData = '';
        if (isset ($data['action']))
        {
          $extraData = $this->{'_' . $data['action']['function']}($data['action']);
        }

        $displayField = true;
        $inputField = 'templateVars[' . $field . ']';
        if ( $asEdit === false ) {
          $inputField = $field;
          if ( isset ($data['visibleInTemplate']) && $data['visibleInTemplate'] === false ) {
            $displayField = false;
          }
        }

        $this->emptyObject->data['fields'][$inputField] = array(
          "description"   => $data['description'],
          "default_value" => "",
          "form_type"     => ($data['type'] === 'textEditor'?'textarea':$data['type']),
          "form_size"     => ($data['type'] === 'textEditor' || $data['type'] === 'textarea'?'60':'40'),
          "form_rows"     => ($data['type'] === 'textEditor' || $data['type'] === 'textarea'?'7':'1'),
          "form_visible"  => true,
          'form_class'    => 'form-control ' . ($data['type'] === 'textEditor'?'textEditor':''),
          'value'         => (isset($this->templateValues[$field])?$this->templateValues[$field]:'') . $extraData
        );

        $returHtmlForm .=  '
          <div class="form-group row ' . ($displayField === false ? 'hidden':'') . '">
            <div class="formlinks"><label for="' . $field . '">' . $data['description'] . '</label> </div>
            <div class="formrechts">
              ' . $this->editObject->form->makeInput($inputField, $this->emptyObject) . '
            </div>
            ' . $extraData . '
          </div>
        ';

      }

      $returHtml .=  $returHtmlForm;
      $returHtml .=  '</div>';
    }
    $returHtml .=  '</div>';
    $returHtml .=  '</fieldset>';

    if ( $asEdit === false ) {
      return $returHtmlForm;
    }
    return $returHtml;
  }

  
  
  function getSelectDbFields($categorieVolgorde = array())
  {
    $html_opties = '';
    $categorieVolgorde = array(
      'Naw' => array(
        "Algemeen",
        "Adres",
        "Verzendadres",
        "Telefoon",
        "Internetgegevens",
        "Bedrijfinfo",
        "Persoonsinfo",
        "Legitimatie",
        "Informatie partner",
        "Legitimatie partner",
        "Adviseurs",
        "geen",
        'Extra algemeen',
        'Beleggen',
        'Rapportage',
        'Profiel',
        'Relatie geschenk'
      ),
      'Portefeuilles' => array(
        'Gegevens',
        'Beheerfee',
        'Staffels'
      ),
      'Speciale velden' => array(
        'Opmaak'
      )
    );
  
    $velden['Opmaak']['leegNietTonen']=array('description'=>'Indien leeg, deze regel niet tonen.');
    $velden['Opmaak']['huidigeDatum']=array('description'=>'De huidige datum.');
    $velden['Opmaak']['huidigeGebruiker']=array('description'=>'De huidige gebruiker.');
    $velden['Opmaak']['GebruikerNaam']=array('description'=>'Naam huidige gebruiker.');
    $velden['Opmaak']['GebruikerTitel']=array('description'=>'Titel huidige gebruiker.');
    
    $portefeuille = new Portefeuilles();
    foreach ($portefeuille->data['fields'] as $key=>$values)
    {
      $velden[$values['categorie']][$key]=$values;
    }
    $naw = new Naw();
    foreach ($naw->data['fields'] as $key=>$values)
    {
      $velden[$values['categorie']][$key]=$values;
    }
    $extraOpties=array('RapportageValuta','Remisier','tweedeAanspreekpunt','Accountmanager','Depotbank','Client','Vermogensbeheerder');
    
    $AccountmanagerVelden=array('Accountmanager'=>array('Titel','Titel2'),'tweedeAanspreekpunt'=>array('Titel','Titel2'));
    foreach ($categorieVolgorde as $table=>$categorien)
    {
      $html_opties .= "<b>$table</b>";
      foreach ($categorien as $categorie)
      {
        $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$table$categorie')\">$categorie</div><span class=\"submenu\" id=\"sub$table$categorie\">\n";
        foreach ($velden[$categorie] as $veld=>$waarden)
        {
          $html_opties .= "<label for=\"".$veld."\" title=\"".$waarden['description']."\"> {".$veld."} </label><br>\n";
          if($table == 'Portefeuilles' && substr($waarden['form_type'],0,6)=='select' && in_array($veld,$extraOpties))
          {
            $html_opties .= "<label for=\"*".$veld."\" title=\"*".$waarden['description']."\"> {*".$veld."} </label><br>\n";
            if(isset($AccountmanagerVelden[$veld]))
            {
              foreach($AccountmanagerVelden[$veld] as $index=>$veldNaam)
              {
                $html_opties .= "<label for=\"".$veld.$veldNaam."\" title=\"".$veld.$veldNaam."\"> {".$veld.$veldNaam."} </label><br>\n";
              }
            }
          }
        }
        $html_opties .= "</span>\n";
      }
    }
    
    $html = "
 <script language=\"JavaScript\" TYPE=\"text/javascript\">
function Aanpassen()
{
	document.kolForm.submit();
}
function Opslaan()
{
	document.kolForm.kolUpdate.value=\"2\";
	document.kolForm.submit();
}
function Herladen()
{
	document.kolForm.kolUpdate.value=\"3\";
	document.kolForm.submit();
}
</script>
<br><br><b>CRM velden</b>
<br>
<form name=\"kolForm\" target=\"content\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" >
<input type=\"hidden\" name=\"kolUpdate\" value=\"1\">

<style type=\"text/css\">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
/*/*/border:1px solid #000000;/* */
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
}
</style>

<script type=\"text/javascript\" src=\"javascript/menu.js\"></script>

<div id=\"masterdiv\">
";
    $html .= $html_opties;
    $html .="</div>";
    $html .="</form>";
    
    return $html;
  }
  
  /**
   * Wanneer beschikbaar het wisselen tussen standaard en engels mogelijk maken
   * @param $categorie -
   * @return string
   */
  function getLangSwitcher ($categorie)
  {
    if ( $this->isTranslatable($categorie) === true )
    {
      return '
        <div class="formTabGroup" id="langGroup" >
          <div class="btn-group nav nav-tabs" id="myTab" role="tablist">
            <span  class="btn btn-hover btn-default active"  id="def-tab" data-toggle="tab" href="#def" role="tab" aria-controls="def" aria-selected="true">Standaard</span>
            <span  class="btn btn-hover btn-default"  id="eng-tab" data-toggle="tab" href="#eng" role="tab" aria-controls="profile" aria-selected="false">Engels</span>
          </div>
        </div>
      ';
    }
  }
  
  
  function _addToField ($data = array()) {
    $thisVar = '';
    if ( isset ($data['data']) && ! empty ($data['data']) ) {
      $datas = explode('.', $data['data']);
      
      switch ($datas[0]) {
        case "_session":
          unset($datas[0]);
          $thisVar = $_SESSION;
          foreach ( $datas as $key ) {
            $thisVar = $thisVar[$key];
          }
          break;
          case 1:
          break;
      }
    }
    
    return $thisVar;
  }


  function fillPdfVars ($data = array()) {
    if ( empty($data)) {return null;}

    // Default pdf vars
    $pdfVars = array(
      'headerp1' => '',
      'headerp2' => '',
      'tekstblok' => '',
      'footerp1' => '',
      'footerp2' => ''
    );

    if ( isset ($data['template']) ) {
      $template = $this->getTemplateById($data['template']);
      if ( ! $template ) {return null;}
      $curTemplateCat = $template['categorie'];
    } else {
      if ( isset($this->object->template[$data['categorie']])) {
        $curTemplateCat = $data['categorie'];
      }
    }

    $useData = array();
    if ( isset($data['templateVars']) ) {
      $useData = $data['templateVars'];
    } else {
      $useData = $data;
    }

    // Check if tekstblok1 or textblok2 contains html if not convert new line to br tags
//    if( isset($useData['tekstblok1']) && $useData['tekstblok1'] == strip_tags($useData['tekstblok1'])) {
//      $needles = array("&#13;", "\n");
//      $replacement = "<br />";
//      $useData['tekstblok1'] = str_replace($needles, $replacement, $useData['tekstblok1']);
//    }
//
//    if( isset($useData['tekstblok2']) && $useData['tekstblok2'] == strip_tags($useData['tekstblok2'])) {
//      $needles = array("&#13;", "\n");
//      $replacement = "<br />";
//      $useData['tekstblok2'] = str_replace($needles, $replacement, $useData['tekstblok2']);
//    }


    switch ($curTemplateCat) {
      case 'factuurLos':
        $pdfVars['tekstblok'] =
          ( ! empty ($useData['factuuronderwerp']) ? '<strong>' . $useData['factuuronderwerp'] . '</strong><br/>' : '' ) .
          ( ! empty ($useData['tekstblok1']) ? $useData['tekstblok1'] . '<br/><br/>' : '' ) .
          ( ! empty ($useData['ond1']) ? $useData['ond1'] . '<br/>' : '' ) .
          ( ! empty ($useData['ond2']) ? $useData['ond2'] . '<br/>' : '' ) .
          ( ! empty ($useData['ond3']) ? $useData['ond3'] . '<br/>' : '' ) .
          ( ! empty ($useData['tekstblok2']) ?  '<br/><br/>' . $useData['tekstblok2']  : '' )
        ;

        break;
      case 'crmSjabloon':
        $pdfVars['headerp1'] = ( isset($useData['headerp1']) ? $useData['headerp1'] : '');
        $pdfVars['headerp2'] = ( isset ($useData['headerp2']) ? $useData['headerp2'] : '');
        $pdfVars['tekstblok'] = ( isset ($useData['tekstblok']) ? $useData['tekstblok'] : '' );
        $pdfVars['footerp1'] = ( isset ($useData['footerp1']) ? $useData['footerp1'] : '' );
        $pdfVars['footerp2'] = ( isset ($useData['footerp2']) ? $useData['footerp2'] : '');

        break;
      case 'crmEmailLos':
        $pdfVars['tekstblok'] =
          '<strong>CC:</strong> ' . ( ! empty ($useData['CC']) ? $useData['CC'] : '' ) . '<br/>' .
          '<strong>BCC:</strong> ' . ( ! empty ($useData['BCC']) ? $useData['BCC'] : '' ) . '<br/>' .
          '<strong>Onderwerp:</strong>' .( ! empty ($useData['onderwerp']) ? $useData['onderwerp']  : '' ) . '<br/><br/>' .

          '<strong>Bericht:</strong><br/>' .( ! empty ($useData['body']) ? $useData['body']  : '' ) . '<br/><br/>'
        ;
        break;
      case 'backofficeEmail':

        $pdfVars['headerp1'] = ( isset($useData['headerp1']) ? $useData['headerp1'] : '');
        $pdfVars['headerp2'] = ( isset ($useData['headerp2']) ? $useData['headerp2'] : '');
        $pdfVars['tekstblok'] =
          '<strong>Van:</strong> ' . ( ! empty ($useData['afzender']) ? $useData['afzender'] : '' ) .
          ( ! empty ($useData['afzenderEmail']) ? ' <'. $useData['afzenderEmail'] . '>'  : '' ) .'<br />' .

          '<strong>CC:</strong> ' . ( ! empty ($useData['ccEmail']) ? $useData['ccEmail'] : '' ) . '<br/>' .
          '<strong>BCC:</strong> ' . ( ! empty ($useData['bccEmail']) ? $useData['bccEmail'] : '' ) . '<br/>' .
          '<strong>Onderwerp:</strong>' .( ! empty ($useData['onderwerp']) ? $useData['onderwerp']  : '' ) . '<br/><br/>' .

          '<strong>Bericht:</strong>' .( ! empty ($useData['email']) ? $useData['email']  : '' ) . '<br/><br/>'
        ;
        $pdfVars['footerp1'] = ( isset ($useData['footerp1']) ? $useData['footerp1'] : '' );
        $pdfVars['footerp2'] = ( isset ($useData['footerp2']) ? $useData['footerp2'] : '');
        break;
      case 'crmEmailings':
        $pdfVars['tekstblok'] =
          '<strong>CC:</strong> ' . ( ! empty ($useData['ccEmail']) ? $useData['ccEmail'] : '' ) . '<br/>' .
          '<strong>BCC:</strong> ' . ( ! empty ($useData['bccEmail']) ? $useData['bccEmail'] : '' ) . '<br/>' .
          '<strong>Onderwerp:</strong>' .( ! empty ($useData['onderwerp']) ? $useData['onderwerp']  : '' ) . '<br/><br/>' .

          '<strong>Bericht:</strong><br/>' .( ! empty ($useData['body']) ? $useData['body']  : '' ) . '<br/><br/>'
        ;
        break;
    }

    return $pdfVars;

  }

}


class customTemplate
{
  function get($field)
  {
    return $this->data['fields'][$field]['value'];
  }
}