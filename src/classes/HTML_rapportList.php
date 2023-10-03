<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/06/12 14:09:06 $
 		File Versie					: $Revision: 1.43 $
*/

/**
 * Class rapportList
 */
class rapportList
{
  var $mainObject;
  var $tables;
  var $columns;
  var $objects;
  var $defaultObject;
  var $portefeuille;
  var $queryWhere;
  var $querySelect;
  var $queryOrder;
  var $queryDirection;
  var $queryJoin;
  var $queryGroupBy;

  var $postData;

  var $searchString;
  var $searchFields;

  var $sort;
  var $sortData;

  var $queryString;

  var $orderBreakArray = array();
  var $orderBreak      = array();

  var $reportTotals = array();
  var $breakTotals  = array();   // subtotalen v/e sectie
  var $sumColumns   = array();

  var $columnExtraData = array();
  var $visibleColumns;
  var $page;
  var $perPage;

  var $numOfVisibleColumns = 0;

  var $editScript;

  var $customEdit;
  var $debug;
  var $DB;
  var $db;
  var $user;
  var $extraButtons;


  function rapportList($rapportClass, $portefeuille)
  {
    // init stuff
    global $USR;
    $this->user = $USR;
    $file = getcwd() . "/../../classes/htmlReports/" . $rapportClass . ".php";
    $this->portefeuille = $portefeuille;

    if (!file_exists($file))
    {
      echo "rapportClass niet geledig";
      exit;
    }
    include_once($file);
    $this->mainObject = new $rapportClass($portefeuille);
    $this->sortData = $this->mainObject->sortData;
    $this->defaultObject = $rapportClass;
    $this->tables[] = $this->mainObject->tableName;
    $this->columns = array();
    $this->objects = array();
    $this->queryOrder = array();
    $this->queryDirection = array();
    $this->searchFields = array();

    $this->searchString = "";
    $this->sqlQuery = "";
    $this->queryString = $_GET;
    $this->perPage = 25;
    $this->RowCounter = 0;
    $this->Records = 0;
    $this->page = 1;
    $this->dbId = 1;

    $this->DB = new DB($this->dbId);
    $this->db = new DB($this->dbId);
    $this->debug = false;

  }



  function getPropArray($field)
  {
    return $this->mainObject->data[$field];
  }

  function setOption($field, $option ,$value)
  {
    foreach ( $this->columns as $key => $column ) {
      if ( $column['name'] === $field ) {
        $this->columns[$key]['options'][$option] = $value;
      }
    }
    return true;
  }


  function addColumn($objectName, $column, $options = array())
  {
    $tmp['name'] = $column;
    $tmp['objectname'] = $objectName;
    $tmp['options'] = $options;
    array_push($this->columns, $tmp);
    if (count($options) > 0)
    {
      $this->columnExtraData[$column] = $options;
    }
  }

  function removeColumn($column)
  {
    $tmpArray = array();
    foreach ( $this->columns as $x => $column )
//    for ($x = 0; $x < count($this->columns); $x++)
    {
      if ($this->columns[$x]["name"] <> "$column")
      {
        $tmpArray[] = $this->columns[$x];
      }

    }
    $this->columns = $tmpArray;
  }

  function setSelect($txt)
  {
    $this->querySelect = $txt;
  }

  function setWhere($where)
  {
    $this->queryWhere = $where;
  }

  function setSearch($search)
  {
    $this->searchString = $search;
  }

  function setOrder($sort, $direction)
  {
    for ($a = 0; $a < count($sort); $a++)
    {
      $this->sort[$sort[$a]] = $direction[$a];
      $this->queryOrder[$a + 1] = $sort[$a];
      $this->queryDirection[$a + 1] = $direction[$a];
    }
  }

  function setJoin($join)
  {
    $this->queryJoin = $join;
  }

  function setGroupBy($groupBy)
  {
    $this->queryGroupBy = $groupBy;
  }

  function getSQL()
  {
    // build table select
    foreach ( $this->columns as $a => $column ) {
//    for ($a = 0; $a < count($this->columns); $a++)
//    {

      if (!isset($this->objects[$this->columns[$a]["objectname"]]) && $this->columns[$a]["objectname"] != "")
      {
        $objectName = $this->columns[$a]["objectname"];
        $this->objects[$this->columns[$a]["objectname"]] = new $objectName();
      }

      if ($this->columns[$a]["objectname"] != "")
      {
        // dit is een database veld, maak een query selectie item (en add table)
        $selection[] = $this->objects[$this->columns[$a]["objectname"]]->tableName . "." . $this->columns[$a]["name"];
        $tables[] = $this->objects[$this->columns[$a]["objectname"]]->tableName;
      }
      else
      {
        if ($this->columns[$a]["options"]["sql_alias"])
        {
          // maak een database alias aan

          $selection[] = $this->columns[$a]["options"]["sql_alias"] . " AS " . $this->columns[$a]["name"];
        }
        else
        {
          // dit is geen database veld zet hem niet in de selectie.
          //$selection[] = $this->columns[$a][name];
        }
      }
    }
    $tables = array_unique($tables);

    if (isset($this->forceSelect))
    {
      $query = $this->forceSelect;
    }
    else
    {
      $query = "SELECT " . implode(", ", $selection);
    }

    if (isset($this->forceFrom))
    {
      $query .= $this->forceFrom;
    }
    else
    {
      $query .= "  FROM (" . implode(", ", $tables) . ") ";
    }

    if ($this->queryJoin)
    {
      $query .= " " . $this->queryJoin . " ";
    }

    if (isset($this->forceWhere))
    {
      $query .= $this->forceWhere;
    }
    else
    {
      $query .= " WHERE portefeuille = '".$this->portefeuille."' AND add_user = '".$this->user."'  ";
      if ($this->queryWhere)
      {
        $query .= " AND " . $this->queryWhere . " ";
      }
    }

    // set Where
    if ($this->searchString)
    {
      foreach ( $this->columns as $a => $column )
//      for ($a = 0; $a < count($this->columns); $a++)
      {
        if ($this->columns[$a]["options"]["search"] == true)
        {
          if ($this->columns[$a]["options"]["sql_alias"])
          {
            $fieldName = $this->columns[$a]["options"]["sql_alias"];
          }
          else
          {
            if (isset($this->noTables))
            {
              $fieldName = $this->columns[$a]['name'];
            }
            else
            {
              $fieldName = $this->objects[$this->columns[$a]['objectname']]->data['table'] . "." . $this->columns[$a]['name'];
            }
          }

          $search[] = $fieldName . " LIKE '%" . mysql_escape_string($this->searchString) . "%'";
        }
      }
      if (is_array($search))
      {
        $searchString = implode(" OR ", $search);
        $query .= " AND (" . $searchString . ") ";
      }
    }

    // group by
    if ($this->queryGroupBy)
    {
      $query .= " GROUP BY " . $this->queryGroupBy;
    }

    foreach ($this->orderBreakArray as $sortingKey)
    {
      if (isset ($this->sortData[$sortingKey]))
      {
        $order[] = $this->sortData[$sortingKey]['orderField'];
      }
    }

    // order by
    for ($a = 1; $a <= count($this->queryOrder); $a++)
    {
      $order[] = $this->queryOrder[$a] . " " . $this->queryDirection[$a];
    }


    if ($order)
    {
      $query .= " ORDER BY " . implode(", ", $order);
    }

    // limit perPage
    $from = ($this->page - 1) * ($this->perPage);
    $this->from = $from;
//		$query .= " LIMIT ".$from.",".$this->perPage;
    $this->sqlQuery = $query;
    //echo $query;//exit;

    return $query;
  }


  function selectPage($page)
  {
    if (empty($page))
    {
      $page = 1;
    }

    $this->page = $page;
    $this->DB = new DB($this->dbId);
    $this->DB->SQL($this->getSQL());

    return $this->DB->Query();
  }


  function recordsO()
  {
    if ($query = $this->getSQL())
    {
      $pos = strpos($query, "LIMIT");
      $qs = substr($query, 0, $pos);
      $tmp = new DB($this->dbId);
      $tmp->SQL($qs);
      $tmp->Query();

      return $tmp->Records();
    }

    return 0;
  }

  function records()
  {
    if ($query = $this->getSQL())
    {
      if (!$this->DB->resultReady())
      {
        $this->DB->SQL($query);
        $this->DB->Query();
      }

      $this->Records = $this->DB->Records();
      if ($this->Records > 0)
      {
        $this->DB->gotoRow($this->from);
      }

      return $this->Records;
    }

    return 0;
  }

  //  returns fetcharray
  function getRow()
  {
    $this->RowCounter++;

    if ($this->RowCounter > $this->perPage)
    {
      return false;
    }

    if ($result = $this->DB->nextRecord())
    {
      foreach ( $this->columns as $key => $column )
//      for ($b = 0; $b < count($this->columns); $b++)
      {
        $prop = array_merge($this->getPropArray($column["name"]), $column["options"]);

        $data[$column["name"]] = $prop;
        $data[$column["name"]]["field"] = $column["name"];

        $data[$column["name"]]["value"] = $result[$column["name"]];

      }
      return $data;
    }
    else
    {
      return false;
    }
  }

  function printHeader($extraData = array())
  {

    // rebuild querystring :  zonder sort, desc  zonder page
    foreach ($this->queryString as $keyname => $value)
    {
      if ($keyname != "sort" && $keyname != "direction" && $keyname != "page")
      {
        $str .= "&" . urlencode($keyname) . "=" . urlencode($value);
      }
    }

    //	$colDef = "<colgroup>";
    $header = "\n<thead id=\"tableHeader\">\n<tr class='list_kopregel'>";
    foreach ($this->columns as $colomnData)
    {
      $options = $colomnData["options"];
      $field = $colomnData["name"];

      $options = array_merge($this->getPropArray($field), $options);

      if (!$options["hideColumn"])
      {
        if ( $options['visible'] === true ) {
          $this->numOfVisibleColumns++;
        }
        /** Sorter richting bepalen **/
        $sortDirection = '';
        if (isset ($this->sort[$field]))
        {
          $sortDirection = $this->sort[$field];
        }

        $dragClass = '';
        if ( isset ($options['fixed']) && $options['fixed'] === true )
        {
          $dragClass = 'notdraggable';
        }

        $tdSection = "\n\t<th 
          title='" . strip_tags($options["description"]) . "' 
          data-toggle=\"tooltip\"
          data-container=\"body\"
          style=\" ".($options['visible'] === true ? '' : 'display:none' )." \" 
          data-sort=\"" . $sortDirection . "\" 
          data-field=\"" . $field . "\" 
          class='headerTD " . $options["formatClass"] . " " . $options["widthClass"] . " " . $options["headerClass"] . " " . $dragClass . "' 
        >

					<div   style=\"display: flex;\">
        ";
        // $colDef .= "\n\t<col class='".$options['widthClass']."' title='$field' />";

        if ($options["sort"])
        {
          $tdSection .= '
					<div class="sortableIcons" style="width: 15px;text-align: left;">
						<i data-sortdirection="none" class="' . ( ! empty($sortDirection) ? 'hidden' : '' ) . ' sortColumn fa fa-sort"></i>
						<i data-sortdirection="ASC" class="' . ( $sortDirection !== 'ASC'  ?'hidden' : '' ) . ' sortColumn fa fa-sort-asc"></i>
						<i data-sortdirection="DESC" class="' . ( $sortDirection !== 'DESC' ? 'hidden' : '' ) . ' sortColumn fa fa-sort-desc"></i>
					</div>
					';
        }
        $tdSection .= '<div>' . (($options["descriptionShort"] <> "")?$options["descriptionShort"]:$options["description"]) . '</div>';
        $tdSection .= "</div></th>";
        $header .= $tdSection;
      }
    }

    // $colDef .="\n</colgroup>";
    $header .= "\n<tr>\n</thead>";

    if ( ! empty ($this->postData) ) {
      foreach ( $this->postData as $dataKey => $dataValue ) {
        $header = str_replace( "{".$dataKey."}", $dataValue, $header);
      }
    }

    return $header;
  }


  /**
   * Headers naar array ter gebruik in een export kan zijn csv of xls
   * @param array $extraData
   * @return array
   */
  function getHeaderExport($extraData = array())
  {
    $header = array();
    foreach ($this->columns as $colomnData) {
      if ( $colomnData['name'] === 'id' ) {
        continue;
      }
      $options = $colomnData["options"];
      $options = array_merge($this->getPropArray($colomnData["name"]), $options);

      if ( ! $options["hideColumn"] && ( ! isset ($options["visible"]) || $options["visible"] === true  ) ) {
        $header[] = str_replace('<br />', '', (($options["descriptionShort"] <> "")?$options["descriptionShort"]:$options["description"]));
      }
    }

    if ( ! empty ($this->postData) ) {
      foreach ( $this->postData as $dataKey => $dataValue ) {
        $header = str_replace( "{".$dataKey."}", $dataValue, $header);
      }
    }

    return $header;
  }




  function buildRow($data)
  {
    global $__appvar;
    $ft = new AE_cls_formatter(",", ".");

    $trClass = 'dataRow';
    if (isset($data[0]['trClass']))
    {
      $trClass = $data[0]['trClass'];
    }

    $output = "\n<tr class='" . $trClass . "'>\n";

    $keyed = '';
    foreach ($data as $key => $row)
    {
      $buttonData = '';

      if ( $key === 'gbOmschrijving' ) {
        $row['value'] = vtbv($row['value']);
      }

      if ( in_array($data['type']['value'], array('rekening', 'geld')) && $key === 'fondsOmschrijving' ) {
        $row['value'] = str_replace('Effectenrekening', vtbv('Effectenrekening'), $row['value']);
      }
      
      
      $keyed .= " | " . $key;
      if (!isset($row["linkId"]) AND $key == "id")
      {
        $row["linkId"] = $row["value"];
        $id = $row["linkId"];
      }
      if (is_array($row))
      {
  
  
        if (isset ($row["displayFormat"][0]) && $row["displayFormat"][0] == "@")  // formatting defined
        {
          $printdata = $ft->format($row["displayFormat"], $row["value"]);
        }
        else
        {
          $printdata = $ft->format("@S{0}", $row["value"]);  // output ongeformateerd
        }

//
//        if ( ! empty($row['showOrderCheckbox']) && (isset ($data['type']) && $data['type']['value'] !== 'rekening') && $row['showOrderCheckbox'] === true ) {
//          if ( ! empty ($row['value']) ) {
////      if ( isset ($row['options']['clickableSumIf']) && ! empty($row['options']['clickableSumIf']) && isset ($row['options']['clickableSum']) && ! empty($row['options']['clickableSum'])) {
//            $buttonData .= '
//              <span data-toggle="tooltip" id="toOrder_' . $id . '" class="btn-new btn-darkblue btn-xxs" style="line-height: 1;">
//                <input class="toOrderCheckbox" value="' . $id . '" type="checkbox"/>
//              </span>
//            ';
//          }
//        }
  
  
        //controlleer of de link een conditie heeft
        if ( isset ($row['options']['clickableSumIf']) && ! empty($row['options']['clickableSumIf']) && isset ($row['options']['clickableSum']) && ! empty($row['options']['clickableSum'])) {
          $var1 = $this->postData[$row['options']['clickableSumIf'][0]];
          $var2 = $this->postData[$row['options']['clickableSumIf'][2]];

          //evt datums omzetten naar timestamp
          if ( $row['options']['clickableSumIf'][3] === 'date' ) {
            $var1 = strtotime($var1);
            $var2 = strtotime($var2);
          }

          //conditie uitvoeren return is true / false
          if ( $this->criteriaMet($var1, $row['options']['clickableSumIf'][1], $var2) ) {
            $printdata = '<a class="clickable" href="' . $row['options']['clickableSum'] . '">' . $printdata . '</a>';
          }

        } else {
          if ( isset ($row['options']['clickableSum']) && ! empty($row['options']['clickableSum'])) {
            $printdata = '<a class="clickable" href="' . $row['options']['clickableSum'] . '">' . $printdata . '</a>';
          }
        }

        if (  ! empty($row['clickable']) ) {
          $printdata = '<a class="clickable" href="' . $row['clickable'] . '">' . $printdata . '</a>';
        }


        if (isset($row["links"]) && count($row["links"]) > 0)
        {
          if ( ! empty ($row['value']) ) {
            $buttonData .= '<span data-toggle="tooltip" title="Fondsinformatie" id="extra_' . $id . '" class="btn-new btn-darkblue btn-xxs iBtn" style="line-height: 1;">I</span> ';
          }
//          $printdata = "
//          <span id='extra_$id' class='iBtn'  >
//          <img height='12px' src='../images/16/informationi.png'/>
//          </span> " . $printdata;
        }

        if ( ! empty($row['fondsLink']) && (isset ($data['type']) && $data['type']['value'] !== 'rekening') || $row['forceFondsLink'] === true ) {
          if ( ! empty ($row['value']) ) {
            $buttonData .= '
            <span class="btn-new btn-darkblue btn-xxs " data-toggle="tooltip"  title="Fondsoverzicht/Positie" style="line-height: 1;" >
              <a href="' . $row['fondsLink'] . '">F</a>
            </span> ';
          }
        }

        $orderRechten = (bool) checkOrderAcces ('handmatig_opslaan');
        $ordermoduleAccess = GetModuleAccess("ORDER");

        if ( $orderRechten === true && (int) $ordermoduleAccess === 2  && (int) $data['consolidatie']['value'] === 0 ) {
          
          if ( ! empty($row['showOrderLink']) && ( (isset ($data['type']) && ! in_array($data['type']['value'], array('rekening', 'geld')) ) || !isset ($data['type'])) ) {
            //Bij html rapport Model
            if ( $this->postData['report_type'] === 'htmlModel' ) {
              if ( $data['kopen']['value'] > 0 ) {
                $modelLink = $__appvar['baseurl'].'/ordersEditV2.php?action=edit&from_rapport=true&from_fonds={fonds}&from_portefeuille={portefeuille}&from_transactie=A&from_aantal={kopen}';
              } else {
                $modelLink = $__appvar['baseurl'].'/ordersEditV2.php?action=edit&from_rapport=true&from_fonds={fonds}&from_portefeuille={portefeuille}&from_transactie=V&from_aantal={verkopen}';
              }

              $buttonData .= '
                <a data-toggle="tooltip" title="' . vt('Order inleggen') . '" href="'.$modelLink.'" class="btn-new btn-darkblue btn-xxs" style="line-height: 1;">O</a>
            ';
            } else {
              $kopenNominaalKnop = '';
              if ( (int) $data['orderinlegInBedrag']['value'] === 1 ) {
                $kopenNominaalKnop = '<li><a href="'.$__appvar['baseurl'].'/ordersEditV2.php?action=edit&from_rapport=true&from_fonds={fonds}&from_portefeuille={portefeuille}&from_transactie=A&has_aantal={totaalAantal}&soort=N">' . vt('Kopen nominaal') . '</a></li>';
              }
              
              $defaultLinkString = '&fonds={fonds}&portefeuille={portefeuille}';
              $buttonData .= '
                <div class="btn-group" role="group">
                
                  <span type="button" class="btn-new btn-darkblue btn-xxs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="line-height: 1;">
                    O
                  </span>

                  <ul class="dropdown-menu">
                    <li><a href="'.$__appvar['baseurl'].'/ordersEditV2.php?action=edit&from_rapport=true&from_fonds={fonds}&from_portefeuille={portefeuille}&from_transactie=A&has_aantal={totaalAantal}">' . vt('Kopen') . '</a></li>
                   
                    ' . $kopenNominaalKnop . '
                    
                    <li><a href="'.$__appvar['baseurl'].'/ordersEditV2.php?action=edit&from_rapport=true&from_fonds={fonds}&from_portefeuille={portefeuille}&from_transactie=V&from_aantal={totaalAantal}">' . vt('Verkopen') . '</a></li>
                  </ul>
                </div>
            ';
              //          $printdata = '<span data-toggle="tooltip"  title="Fondsoverzicht/Positie" class="circleBase" ><a href="'.$__appvar['baseurl'].'/ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd" style="    margin: 1px 0px 0px 4px;">O</a></span> ' . $printdata;
            }

          }
        }

        $printdata = '<div class="btn-group" style="margin: 0px 0px -1px 0px;">' . $buttonData . '</div>&nbsp' . $printdata;
        preg_match_all('|{(.*?)}|', $printdata, $out);
        if ( ! empty ( $out[0]) ) {
          foreach ( $out[1] as $key => $value) {
            if ( isset ($data[$value]) ) {
              $printdata = str_replace($out[0][$key], $data[$value]['value'], $printdata);
            } elseif ( isset ($this->postData[$value]) ) {
              $printdata = str_replace($out[0][$key], $this->postData[$value], $printdata);
            }
          }
        }


        if (isset ($row["type"]))
        {
          switch ($row["type"])
          {
            case "checkbox" :
              $printdata = imagecheckbox($row["value"]);
              break;
            default :
          }
        }
        $style = $row["formatClass"];
        if (isset($row["type"]) && strtolower($row["type"]) == "number")
        {
          $style = ($row["negativeClass"] <> "" AND $row["value"] < 0)?$row["negativeClass"]:$row["formatClass"];
        }

        if (
        ( !isset($row["hideColumn"]) || !$row["hideColumn"] )
//          && ( $row['visible'] === true || isset ($row['isHeader']) )
        ) {
          //build headerList
          $group = '';
          foreach ($this->orderBreak as $key => $headerData)
          {
            if (!empty($group))
            {
              $group .= ';';
            }
            $group .= '' . $key . '-' . $headerData['name']['value'];
          }

          $output .= "\t<td style=\" ".($row['visible'] === true ? '' : 'display:none' )." \" " . (isset($row["colspan"])?$row["colspan"]:'') . " data-group=\"" . $group . "\" data-field=\"" . (isset ($row['field'])?$row['field']:'') . "\" data-value='" . $row["value"] . "' class='" . $style . "' >"; //
          $output .= $printdata . " &nbsp;";
          $output .= "</td>\n";
        }

      }
    }
    $output .= "\n</tr>\n";

    return $output;


  }



  function buildRowExport($data)
  {
    global $__appvar;
    $ft = new AE_cls_formatter(",", ".");

    $rowData = array();
    foreach ($data as $key => $row)
    {
      if ( $row['field'] === 'id' ) {
        continue;
      }
      if ( is_array ($row) )
      {
        $printdata = $ft->format("@S{0}", $row["value"]);  // output ongeformateerd

        preg_match_all('|{(.*?)}|', $printdata, $out);
        if ( ! empty ( $out[0]) ) {
          foreach ( $out[1] as $key => $value) {
            if ( isset ($data[$value]) ) {
              $printdata = str_replace($out[0][$key], $data[$value]['value'], $printdata);
            } elseif ( isset ($this->postData[$value]) ) {
              $printdata = str_replace($out[0][$key], $this->postData[$value], $printdata);
            }
          }
        }

        if ( ! $row["hideColumn"] && ( ! isset ($row["visible"]) || $row["visible"] === true  ) ) {
          $rowData[] = $printdata;
        }

      }
    }

    return $rowData;


  }

  function printRow($template = "")
  {
    if ($data = $this->getRow())
    {
      return $this->buildRow($data, $template, "");
    }

    return false;
  }


  function fillTemplate($data, $template)
  {
    return $this->buildRow($data, $template, "");
  }

  //todo: moet excel uitvoer mogelijk zijn in deze class??
  function setXLS($title = '')
  {
    include_once("AE_cls_xls.php");
    $this->xlsData = array();
    $this->xls = new AE_xls();
    $this->selectAllPage();

    foreach ( $this->columns as $x => $column )
//    for ($x = 0; $x < count($this->columns); $x++)
    {
      $this->xlsData[0][] = array($this->columns[$x]['name'], 'header');
    }

    $x = 1;
    while ($data = $this->getRow())
    {
      foreach ($data as $key => $dataArray)
      {
        $this->xlsData[$x][] = array($dataArray['value'], 'body');
      }
      $x++;
    }
    $this->xls->excelOpmaak['header'] = array('setAlign' => 'centre', 'setBgColor' => '22', 'setBorder' => '1');
    $this->xls->excelOpmaak['body'] = array('setBorder' => '1');
    $this->xls->setColumn[] = array(0, 0, 6);
    $this->xls->setColumn[] = array(2, 5, 12);
    $this->xls->setData($this->xlsData);
  }

  function getXLS()
  {
    $this->xls->OutputXls('xls_' . date('Ymd') . '.xls');

  }

  function selectAllPage()
  {
    $this->DB = new DB($this->dbId);
    $query = $this->getSQL();
    $this->DB->SQL($query);

    return $this->DB->Query();
  }

  /**
   * zet de volgorde van het rapport
   * @param array $orderArray
   */
  function setOrderBreak($orderArray = array())
  {
    $this->orderBreakArray = $orderArray;
    foreach ($this->orderBreakArray as $item)
    {
      $this->orderBreak[$item]["previous"] = "empty";
      $this->orderBreak[$item]["total"] = 0;
      $this->orderBreak[$item]["name"] = 0;
    }
  }

  /**
   *  bepaal ordervolgorde, totaliseerbare kolom etc
   */
  function initRows($hideOrderBreak = true)
  {
  
    /**
     * Bij de html mut nieuwe groeperingen instellen wanneer op bepaalde velden gegroepeerd wordt
     */
    if ( $this->defaultObject === 'htmlMUT' ) {
      if ( in_array('Rekening', $this->filter['groupings']) ) {
        $this->mainObject->data['bedragEUR']['sumTotal'] = true;
        $this->mainObject->data['bedragVV']['sumTotal'] = true;
      }

      if ( in_array('Valuta', $this->filter['groupings']) ) {
        $this->mainObject->data['Debet']['sumTotal'] = true;
        $this->mainObject->data['Credit']['sumTotal'] = true;
      }
    }

    // reset arrays
    $seenFields = array();
    $this->visibleColumns = 0;
    $this->sumColumns = array();
    $this->sumRowTotals = array();
    $this->reportTotals = array();
    foreach ($this->mainObject->data as $fieldName => $properties)
    {
      if (isset($this->columnExtraData["$fieldName"]))  // als er opties gezet zijn in addColumn deze samenvoegen
      {
        $properties = array_merge($properties, $this->columnExtraData["$fieldName"]);
      }
      if ($properties["sumTotal"] == true)
      {
        $this->sumColumns[$fieldName] = array("class" => $properties["sumClass"], "format" => $properties["sumFormat"]);
      }
      //$seenFields[] = $field;

    }
    $this->visibleColumns = count($this->columns);

    if ( $hideOrderBreak == true ) {
      foreach ($this->orderBreakArray as $sortColumn)
      {
        //if (!in_array($sortColumn, $seenFields))
        //{
        $this->addColumn($this->defaultObject, $this->sortData[$sortColumn]["orderBreakField"], array("hideColumn" => true));
        $this->addColumn($this->defaultObject, $this->sortData[$sortColumn]["descriptionField"], array("hideColumn" => true));
        //}
      }
    }
  }

  /**
   * stel de order blokken in als <tbody>
   * @param $startOn
   * @param $endBefore
   */
  function setOrderBlock($startOn, $endBlockOn)
  {
    $this->startOn = $startOn;
    $this->endBlockOn = $endBlockOn;

    $this->orderHeaderBreak = $this->orderBreak;
  }




  function checkOrderBreak ($listData, $lastRecord) {
    $filter = $this->filter['groupings'];
    krsort  ($filter); // groeperingen omdraaien laatste groepering moet als eerst geprint worden

    foreach ( $filter as $breakFieldKey => $breakField ) {

      $fldName = $this->sortData[$breakField]["orderBreakField"];
      $prevBreakFieldValue = trim($this->orderBreak[$breakField]['previous']);
      $isPrinted = $this->orderBreak[$breakField]['printed'];
      $currentBreakFieldValue = trim ($listData[$fldName]['value']);

      /**
       * Deze regel en de vorige regel met elkaar vergelijken
       * Controleren of de vorige geprinte header afwijkt in deze en de vorige regel
       * wanneer dit het geval is moet het huidige record deze breakfield ook geprint worden.
       */
      if ( isset ($this->sortData[$this->filter['groupings'][$breakFieldKey-1]]["orderBreakField"]) ) {
        $prevBreakField = $this->filter['groupings'][$breakFieldKey-1];
        $prevBreakKeyPrevValue = $lastRecord[$this->sortData[$prevBreakField]["descriptionField"]];
        $prevBreakKeyCurValue = $listData[$this->sortData[$prevBreakField]["descriptionField"]];
        if ( trim($prevBreakKeyPrevValue['value']) !== trim($prevBreakKeyCurValue['value']) ) {
          $isPrinted = true;
        }
      }

      if ($this->endBlockOn == $breakField) {
        $prevBreakKeyPrevValue1 = $lastRecord[$this->sortData[$this->startOn]["descriptionField"]];
        $prevBreakKeyCurValue1 = $listData[$this->sortData[$this->startOn]["descriptionField"]];

        if ( trim($prevBreakKeyPrevValue1['value']) !== trim($prevBreakKeyCurValue1['value']) ) {
          $isPrinted = true;
        }
      }

      if ($prevBreakFieldValue !== $currentBreakFieldValue || $isPrinted === true)
      {
        if ($prevBreakFieldValue !== 'empty')
        {
          $this->printOrderBreakFooter($listData, $breakField);  // print de footer
        }

        $this->breakTotals = array();                                                                         // reset de break totals
        $this->orderBreak[$breakField]["previous"] = $listData[$fldName]["value"];                                // zet nieuwe break value
        $this->orderBreak[$breakField]["name"] = $listData[$this->sortData[$breakField]["descriptionField"]]; // haal omschrijving op
        $this->orderBreak[$breakField]["total"] = array();                                                 // reset break totals
        $this->orderBreak[$breakField]["printed"] = false;                                                   // printflag

        foreach ($this->sumColumns as $sumField => $properties)
        {
          $this->orderBreak[$breakField]["total"][$sumField] = $listData[$sumField]["value"];                     // vul de breaktotals met de huidige regel
        }
      }
      else
      {
        foreach ($this->sumColumns as $sumField => $properties)
        {
          $this->orderBreak[$breakField]["total"][$sumField] += $listData[$sumField]["value"];          // update de breaktotals met de huidige regel
        }
      }
    }

    foreach ( $this->filter['groupings'] as $breakFieldKey => $breakField )
    {


      $fldName = $this->sortData[$breakField]["orderBreakField"];
      $prevBreakFieldValue = trim($this->orderHeaderBreak[$breakField]['previous']);
      $isPrinted = $this->orderHeaderBreak[$breakField]['printed'];
      $currentBreakFieldValue = trim ($listData[$fldName]['value']);

      /**
       * Deze regel en de vorige regel met elkaar vergelijken
       * Controleren of de vorige geprinte header afwijkt in deze en de vorige regel
       * wanneer dit het geval is moet het huidige record deze breakfield ook geprint worden.
       */
      if ( isset ($this->sortData[$this->filter['groupings'][$breakFieldKey-1]]["orderBreakField"]) ) {
        $prevBreakField = $this->filter['groupings'][$breakFieldKey-1];
        $prevBreakKeyPrevValue = $lastRecord[$this->sortData[$prevBreakField]["descriptionField"]];
        $prevBreakKeyCurValue = $listData[$this->sortData[$prevBreakField]["descriptionField"]];
        $prevBreakFieldValuetest = trim($this->orderHeaderBreak[$prevBreakField]['previous']);

        if ( trim($prevBreakKeyPrevValue['value']) !== trim($prevBreakKeyCurValue['value']) ) {
          $isPrinted = true;
        }
      }

      if ($this->startOn == $breakField) {
        $prevBreakKeyPrevValue1 = $lastRecord[$this->sortData[$breakField]["descriptionField"]];
        $prevBreakKeyCurValue1 = $listData[$this->sortData[$breakField]["descriptionField"]];

        if ( trim($prevBreakKeyPrevValue1['value']) !== trim($prevBreakKeyCurValue1['value']) ) {
          $filterCount = 1;
          while ( (int) count($this->filter['groupings']) >= (int) $filterCount ) {
            $this->orderHeaderBreak[$this->filter['groupings'][$breakFieldKey+$filterCount]]['printed'] = true;
            $filterCount++;
          }
        }
      }

      if ($prevBreakFieldValue !== $currentBreakFieldValue || $isPrinted === true)
      {
        if ($this->startOn == $breakField)
        {
          echo '<tbody>';
        }
        $this->orderHeaderBreak[$breakField]["previous"] = $listData[$fldName]["value"];                                // zet nieuwe break value
        $this->orderHeaderBreak[$breakField]["name"] = $listData[$this->sortData[$breakField]["descriptionField"]]; // haal omschrijving op
        $this->orderHeaderBreak[$breakField]['printed'] = false;
        $this->printOrderBreakHeader($breakField);

        if ($this->endBlockOn == $breakField)
        {
          echo '</tbody>';
        }

      }
    }
  }




















  function orderBreakEnd($data)
  {
    $start = count($this->orderBreakArray) - 1;
    for ($x = $start; $x >= 0; $x--)
    {
      $this->printOrderBreakFooter($data, $this->orderBreakArray[$x]);
    }
  }

  /**
   * print de order overgang kop
   */
  function printOrderBreakHeader($breakField)
  {
    $ft = new AE_cls_formatter(",", ".");

    $printdata = $this->orderHeaderBreak[$breakField]["name"]["value"];
    if ( isset ($this->orderHeaderBreak[$breakField]["name"]["displayFormatOrderBreakHeader"][0]) && $this->orderHeaderBreak[$breakField]["name"]["displayFormatOrderBreakHeader"][0] === "@")  // formatting defined
    {
      $printdata = $ft->format($this->orderHeaderBreak[$breakField]["name"]["displayFormatOrderBreakHeader"], $this->orderHeaderBreak[$breakField]["name"]["value"]);
    }

    $subTotalRow[] = array(
      "value"       => "" . vtbv($printdata),
      "field"       => $breakField,
      "formatClass" => $this->sortData[$breakField]["headerClass"] . ' groupHeader',
      "colspan"     => "colspan='" . ($this->numOfVisibleColumns) . "'",
      "trClass"     => 'headerRow',
      'visible'    => true
    );
    echo $this->buildRow($subTotalRow);
  }

  /**
   * print de order overgang voet
   */
  function printOrderBreakFooter($data, $breakField)
  {

//    if ($this->orderBreak[$breakField]["printed"])
//    {
//      return;   // was al eerder geprint
//    }

    $ft = new AE_cls_formatter(",", ".");
    $printdata = $this->orderBreak[$breakField]["name"]["value"];
    if ( isset ($this->orderBreak[$breakField]["name"]["displayFormatOrderBreakFooter"][0]) && $this->orderBreak[$breakField]["name"]["displayFormatOrderBreakFooter"][0] === "@")  // formatting defined
    {
      $printdata = $ft->format($this->orderBreak[$breakField]["name"]["displayFormatOrderBreakFooter"], $this->orderBreak[$breakField]["name"]["value"]);
    }

    $subTotalRow = array();
    foreach ($data as $field => $properties)
    {

      if ($field === 'id')
      {
        continue;
      }

      if (count($subTotalRow) == 0)   // eerste kolom
      {
        $subTotalRow[] = array(
          "value"           => vt('Subtotaal') . ": <b>" . vtbv($printdata) . "</b>",
          "formatClass"     => "sumClass",
          "trClass"         => 'footerRow',
          'visible'         => true
        );
        continue;
      }

      /**
       * Controlleren of de header aan of uit staat
       * Anders ook de totalen niet tonen
       */
      $inHeader = false;
      foreach ($this->columns as $column)
      {
        if ($column['name'] == $field)
        {
          if (
            !isset ($column['options']['hideColumn'])
            || (isset($column['options']['hideColumn']) && $column['options']['hideColumn'] === false)
          )
          {
            /** Header gevonden en deze mag getoond worden **/
            $inHeader = true;
          }
        }
      }
      /** Geen header gevonden of header is verborgen skip deze column **/
      if ($inHeader === false)
      {
        continue;
      }

      if (array_key_exists($field, $this->sumColumns))
      {
        $fldClass = ($this->orderBreak[$breakField]["total"][$field] < 0)?$properties["sumClass"] . "Negative":$properties["sumClass"];

        $subTotalRow[] = array(
          "field"         => $field,
          "value"         => $this->orderBreak[$breakField]["total"][$field],
          "formatClass"   => $field . ' ' . $fldClass,
          "displayFormat" => $properties["sumFormat"],
          "hideColumn"    => false,
          "trClass"       => 'footerRow',
          'visible'       => $properties['visible']);
      }
      else
      {
        $subTotalRow[] = array(
          "field"       => $field,
          "value"       => " ",
          "formatClass" => $field . ' ' . $properties["sumClass"],
          "hideColumn"  => false,
          "trClass"     => 'footerRow',
          'visible'       => $properties['visible']
        );
      }


    }
    $this->debug = false;

    echo $this->buildRow($subTotalRow);
    $this->debug = false;
    // echo "<tr><td colspan='20'>subtotal:$breakField ".implode(", ",$this->breakTotals)."</td></tr>";
  }

  /**
   * print eindtotalen
   * @param $data
   */
  function printTotalFooter($data)
  {

    $endTotal = $this->mainObject->endTotal;
    foreach ($data as $field => $properties)
    {
      if ($field === 'id')
      {
        continue;
      }

      if (isset ($subTotalRow) && count($subTotalRow) == 0)   // eerste kolom
      {
        $subTotalRow[] = array(
          "value" => vt('Rapport totalen') . ": ",
          "formatClass" => $field . ' ' . $endTotal["class"],
          "trClass" => "footerRow footerEndRow",
        );
        continue;
      }

      /**
       * Controlleren of de header aan of uit staat
       * Anders ook de totalen niet tonen
       */
      $inHeader = false;
      foreach ($this->columns as $column)
      {
        if ($column['name'] == $field)
        {
          if (
            !isset ($column['options']['hideColumn'])
            || (isset($column['options']['hideColumn']) && $column['options']['hideColumn'] === false)

          )
          {
            /** Header gevonden en deze mag getoond worden **/
            $inHeader = true;
          }
        }
      }
      /** Geen header gevonden of header is verborgen skip deze column **/
      if ($inHeader === false)
      {
        continue;
      }

      if (array_key_exists($field, $this->reportTotals) AND !$properties["hideEndTotal"] && $properties['visible'] === true)
      {//displayFormat
        $fldClass = ($this->reportTotals[$field] < 0)?$endTotal["negativeClass"]:$endTotal["class"];

        $subTotalRow[] = array(
          "field"         => $field,
          "value"         => $this->reportTotals[$field],
          "trClass"       => "footerRow footerEndRow",
          "formatClass"   => $field . ' ' . $fldClass,
          "displayFormat" => ( isset ($properties['sumTotalFormat']) ? $properties['sumTotalFormat'] : $this->mainObject->endTotal["format"]),
          "hideColumn"    => false,
          'visible'       => $properties['visible'],
          'reportTotal'   => true,
          'options'       => $properties
        );
      }
      else
      {
        $subTotalRow[] = array(
          "field"       => $field,
          "value"       => " ",
          "trClass"     => "footerRow footerEndRow",
          "formatClass" => $field . ' ' . $endTotal["class"],
          "hideColumn"  => false,
          'visible'       => $properties['visible']
        );
      }


    }
    echo '<tfoot class="tableFoot">'.$this->buildRow($subTotalRow) . '</tfoot>';
  }


  /**
   * totaliseer de kolommen voor het eindtotaal
   */
  function sumRowTotals($data)
  {
    foreach ($this->sumColumns as $field => $properties)
    {
      if (!isset ($this->reportTotals[$field]))
      {
        $this->reportTotals[$field] = 0;
      }
      $this->reportTotals[$field] += $data[$field]["value"];
    }
  }



  function setcolumnOrder ($sortArray = array())
  {
    $AeArray = new AE_Array();
    if ( empty ($sortArray) ) {return null;}
    else {
      //kopie van huidige maken
      $thisColumns = $this->columns;

      $newColumnsOrder = array();
      //lijst van velden ophalen
      $nameList = array_flip ($AeArray->extractValuesByKey($this->columns, 'name', true));

      //veldvolgorde bepalen
      foreach ( $sortArray as $fieldName ) {
        if ( isset ($nameList[$fieldName]) ) {
          $newColumnsOrderHolder[] = $thisColumns[$nameList[$fieldName]];
          unset($nameList[$fieldName]);
        }
      }

      //Fixed of niet sorteer bare velden eerst
      foreach ( $nameList as $fieldName => $fieldKey ) {
        $newColumnsOrder[$fieldKey] = $thisColumns[$fieldKey];
        unset($nameList[$fieldName]);
      }

      //Voeg gesorteerde velden toe aan niet sorteerbare
      $counter = 0;
      while ( ! empty($newColumnsOrderHolder) ) {
        if ( isset($newColumnsOrder[$counter])) {$counter++;}
        else{
          $firstSlice = array_splice($newColumnsOrderHolder,0,1);
          $newColumnsOrder[$counter] = $firstSlice[0];
          $counter++;
        }
      }
      ksort($newColumnsOrder);
      //Overschrijv de huidige columns array met de nieuwe volgorde
      $this->columns = $newColumnsOrder;
    }
  }



  /**
   * @param $rapportType rapport type
   * @param $data gehele array van gebruikers standaarden
   */
  function setDefaults ($rapportType, $data)
  {
    global $USR;
    $aeConfig = new AE_config();
    $aeConfig->addItem($USR.'_'.$rapportType, serialize($data));
  }

  /**
   * @param $rapportType rapport type
   * @return array van gebruikersstandaarden
   */
  function getDefaults ($rapportType)
  {
    global $USR;
    $aeConfig = new AE_config();
    $userDefaults = $aeConfig->getData($USR.'_'.$rapportType);
    if ( $userDefaults === false ) {return false;}
    return unserialize($userDefaults);
  }

  function getSortableFields ()
  {
    $AeArray = new AE_Array();
    $sortArray = $AeArray->extractByKeyValue($this->mainObject->data, array('sort' => true), true);
    if ( ! empty($sortArray) ) {
      return $sortArray;
    }
    return array();
  }
  function listSortableFields () {
    $sortableFields = $this->getSortableFields ();

    $sortFieldList = array();
    if ( ! empty($sortableFields) ) {
      foreach ( $sortableFields as $field => $sortData) {
        $sortFieldList[$field] = $sortData['descriptionShort'];
      }
    }
    return $sortFieldList;
  }


  function getRapportJsCss ($content, $jsType) {
    global $__appvar;
    $aeTemplate = new AE_template();

    $content['jsincludes'] .= $aeTemplate->loadJs ($jsType.'Rapport', 'js');
    $content['jsincludes'] .= $aeTemplate->loadJs ('dragtable/jquery.dragtable', 'javascript');
    $content['jsincludes'] .= $aeTemplate->loadJs ('dragtable/jquery.dragtable', 'javascript');
    $content['jsincludes'] .= $aeTemplate->loadJs ('bootstrapTooltip', 'javascript');
    $content['jsincludes'] .= $aeTemplate->loadJs ('waitMe.min', 'javascript');
    $content['jsincludes'] .= $aeTemplate->loadJs ('dropdown', 'javascript');
    $content['style2'] .= $aeTemplate->loadCss('fontAwesome/font-awesome.min');
    $content['style2'] .= $aeTemplate->loadCss('HTMLrapportes', 'css');
    $content['style2'] .= $aeTemplate->loadCss('aeStyle');
    $content['style2'] .= $aeTemplate->loadCss('waitMe.min');
    $content['style2'] .= '<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">';
    $content['style2'] .= $aeTemplate->loadCss('jquery');
    $content['style2'] .= $aeTemplate->loadCss('smoothness/jquery-ui-1.11.1.custom');

    $templ= $aeTemplate->template("../".$__appvar["templateContentHeader"],$content);

    $templ = str_replace('"javascript', '"../javascript', $templ);
    $templ = str_replace('"style', '"../style', $templ);
    $templ = str_replace('rel="../style', 'rel="style', $templ);

    return $templ;
  }

  function setupFilter ($rapportType, $setup = array()) {
    $data = $this->postData;

    if ( isset ($data['type']) && $data['type'] === 'xls' ) {
      $data = $_SESSION[$rapportType.'tempData'];
    } else {
      $_SESSION[$rapportType.'tempData'] = $data;
    }

    /** User standaarden instellen */
    $userDefaults = $this->getDefaults($rapportType);

    if ( isset($_SESSION[$rapportType])) {
      $userDefaults = $_SESSION[$rapportType];
      $userDefaults = $_SESSION[$rapportType];
    }


    /** Zichtbaarbeid opslaan */
    if ( isset ($data['saveBtn']) && $data['saveBtn'] === 'saveVisibility' ) {
      foreach ( $data['columnVisible'] as $fieldName => $fieldState ) {
        $userDefaults['columnVisibility'][$fieldName] = $fieldState;
      }

      $userDefaults['columnOrder'] = array();
      foreach ( $data['columnOrder'] as $fieldName ) {
        $userDefaults['columnOrder'][] = $fieldName;
      }

      //nieuwe gegevens opslaan
      $this->setDefaults($rapportType, $userDefaults);

      //opnieuw alle gegevens ophalen
      $userDefaults = $this->getDefaults($rapportType);
    }
    /** Einde zichtbaarheid */


    /** sortering opslaan */
    if ( isset ($data['saveBtn']) && $data['saveBtn'] === 'saveSorting' ) {

      $userDefaults['sortFields'] = array(
        0 => ( isset($data['sort']['first']) ? $data['sort']['first'] : '' ),
        1 => ( isset($data['sort']['second']) ? $data['sort']['second'] : '' ),
      );

      $userDefaults['sortOrder'] = array(
        0 => ( isset($data['order']['first']) ? $data['order']['first'] : '' ),
        1 => ( isset($data['order']['second']) ? $data['order']['second'] : '' ),
      );

      //sortOrder
      //nieuwe gegevens opslaan
      $this->setDefaults($rapportType, $userDefaults);

      //opnieuw alle gegevens ophalen
      $userDefaults = $this->getDefaults($rapportType);
    }
    /** einde sortering opslaan */


    /** Groepering opslaan */
    if ( isset ($data['saveBtn']) && $data['saveBtn'] === 'saveGrouping' ) {
      $userDefaults['grouping'] = '';
      foreach ( $data['group'] as $fieldName ) {
        $userDefaults['grouping'][] = $fieldName;
      }
      //nieuwe gegevens opslaan
      $this->setDefaults($rapportType, $userDefaults);

      //opnieuw alle gegevens ophalen
      $userDefaults = $this->getDefaults($rapportType);
    }
    /** einde groepering opslaan */


    /**
     * Groepering instellen
     */

    //standaard groepen
    $groupings = array();
    if ( isset ( $setup['groupings']) && is_array($setup['groupings'])) {
      $groupings = $setup['groupings'];
    }
    if ( isset ( $setup['groupings']) && ! is_array($setup['groupings'])) {
      $groupings = array($setup['groupings']);
    }

//Wanneer de gebruiker een sortering heeft ingesteld
    if ( isset($userDefaults['grouping']) && ! empty($userDefaults['grouping']) ) {
      $groupings = null; //unset default grouping
      foreach ( $userDefaults['grouping'] as $groupingkey ) {
        if ( empty($groupingkey) ){continue;}
        $groupings[] = $groupingkey;
      }
    }

//wanneer de gebruiker handmatig een groepering heeft geselecteerd
    if ( isset ($data['group']) && ! empty ($data['group']) ) {
      $groupings = null; //unset default grouping
      foreach ( $data['group'] as $groupingkey ) {
        if ( empty($groupingkey) ){continue;}
        $groupings[] = $groupingkey;
      }
      $_SESSION[$rapportType]['group'] = $groupings;
    } elseif ( isset ($_SESSION[$rapportType]['group']) ) {
      $groupings = $_SESSION[$rapportType]['group'];
    }

    $this->setOrderBreak($groupings);
    /**
     * Einde groepering instellen
     */

    /** kolom volgorde instellen */
    $columnOrder = array();

//Wanneer de gebruiker een sortering heeft ingesteld
    if ( isset($userDefaults['columnOrder']) && ! empty($userDefaults['columnOrder']) ) {
      foreach ( $userDefaults['columnOrder'] as $field ) {
        $columnOrder[] = $field;
      }
    }

    if ( isset ($data['columnOrder'])  && ! empty($data['columnOrder']) ) {
      $columnOrder = $data['columnOrder'];
    }
//krsort($this->columns);
    $this->setcolumnOrder ($columnOrder);
  
    $this->filter['groupings'] = $groupings;
    // na kolomopmaak def initRow() oproepen
    $this->initRows(( isset ($setup['hideOrderBreak']) ? $setup['hideOrderBreak'] : false ));



    /** sortering instellen */
//standaard sortering
//$sortFields = array('Omschrijving');
//$sortOrder = array('ASC');
    if ( isset ( $setup['sortFields']) && is_array($setup['sortFields'])) {
      $sortFields = $setup['sortFields'];
    }
    if ( isset ( $setup['sortOrder']) && is_array($setup['sortOrder'])) {
      $sortOrder = $setup['sortOrder'];
    }


    if ( isset ($data['sort']) && ! empty ($data['sort']) ) {
      //wanneer de gebruiker handmatig een groepering heeft geselecteerd
      $sortFields = null; //unset default grouping
      foreach ( $data['sort'] as $sortField ) {
        if ( empty($sortField) ){continue;}
        $sortFields[] = $sortField;
      }

      $_SESSION[$rapportType]['sort'] = $sortFields;
    }  elseif ( isset ($_SESSION[$rapportType]['sort']) ) {
      $sortFields = $_SESSION[$rapportType]['sort'];
    }
    elseif ( isset($userDefaults['sortFields']) && ! empty($userDefaults['sortFields']) ) {
      //Wanneer de gebruiker een sortering heeft ingesteld
      $sortFields = null; //unset default grouping
      foreach ( $userDefaults['sortFields'] as $sortinKey ) {
        if ( empty($sortinKey) ){continue;}
        $sortFields[] = $sortinKey;
      }
    }


    if ( isset ($data['order']) && ! empty ($data['order']) ) {
      //wanneer de gebruiker handmatig een groepering heeft geselecteerd
      $sortOrder = null; //unset default grouping
      foreach ( $data['order'] as $sortOrderKey ) {
        if ( empty($sortOrderKey) ){continue;}
        $sortOrder[] = $sortOrderKey;
      }

      $_SESSION[$rapportType]['order'] = $sortOrder;
    }  elseif ( isset ($_SESSION[$rapportType]['order']) ) {
      $sortOrder = $_SESSION[$rapportType]['order'];
    } elseif ( isset($userDefaults['sortOrder']) && ! empty($userDefaults['sortOrder']) ) {
      //Wanneer de gebruiker een sortering heeft ingesteld
      $sortOrder = null; //unset default grouping
      foreach ( $userDefaults['sortOrder'] as $sortinOrder ) {
        if ( empty($sortinOrder) ){continue;}
        $sortOrder[] = $sortinOrder;
      }
    }


    
//$_sortField = array("fondsOmschrijving");
//$_sortOrder = array("ASC");

    $this->setOrder($sortFields,$sortOrder);


    /**
     * Zichtbaarheid instellen
     */
    $columnVisibility = '';
    if ( isset ($data['columnVisible']) ) {
      $columnVisibility = $data['columnVisible'];
      $_SESSION[$rapportType]['columnVisible'] = $columnVisibility;
    }  elseif ( isset ($_SESSION[$rapportType]['columnVisible']) ) {
      $columnVisibility = $_SESSION[$rapportType]['columnVisible'];
    } elseif ($userDefaults['columnVisibility']) {
      $columnVisibility = $userDefaults['columnVisibility'];
    }

    if ( ! empty ($columnVisibility) ) {
      foreach ( $columnVisibility as $fieldName => $fieldState ) {
        $this->setOption($fieldName,'visible', ($fieldState === 'on' ? true : false));
      }
    }
    /**
     * einde Zichtbaarheid instellen
     */

   
  }

  /**
   * Ophalen van rapport data
   */
  function setRapportData () {
    $data = $this->postData;

    /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
    $portefeuilleObject = new Portefeuilles ();
    $portefeuilleData = $portefeuilleObject->parseBySearch(
      array('Portefeuille' => $data['portefeuille']),
      array('Vermogensbeheerder', 'Depotbank', 'RapportageValuta', 'portefeuille', 'Client', 'Startdatum', 'Risicoklasse', 'SoortOvereenkomst', 'ModelPortefeuille')
    );
    $modelPortefeuilleObj = new ModelPortefeuilles ();
    $modelPortefeuilleData = $modelPortefeuilleObj->parseBySearch(array('portefeuille' => $portefeuilleData['ModelPortefeuille']));

    $portefeuilleData['ModelPortefeuilleOmschrijving'] = $modelPortefeuilleData['Omschrijving'];
    //depotbank
    $depotDB = new DB();
    $depotQuery = "SELECT * FROM `Depotbanken` WHERE `Depotbanken`.`Depotbank` = '" . $portefeuilleData['Depotbank'] . "'";
    $depotDB->executeQuery($depotQuery);
    $depotbankData = $depotDB->nextRecord();

    $portefeuilleData['Startdatum'] = date('Y-m-d', strtotime($portefeuilleData['Startdatum']));
    $portefeuilleData['Omschrijving'] = $depotbankData['Omschrijving'];

    $portefeuilleData['RapportageValuta'] = ( empty ($portefeuilleData['RapportageValuta']) ? 'EUR' : $portefeuilleData['RapportageValuta'] );




    $crmDB = new DB();
    $crmQuery = "SELECT * FROM `CRM_naw` WHERE `CRM_naw`.`portefeuille` = '" . $portefeuilleData['portefeuille']. "'";
    $crmDB->executeQuery($crmQuery);
    $crmData = $crmDB->nextRecord();

    $portefeuilleData['crm'] = $crmData;
    $this->postData = array_merge($this->postData, $portefeuilleData);
  }

  function getHeaderButtons ($rapportBackButtons, $setup) {
    $data = $this->postData;
    global $__appvar;
//$this->postData['currentHtmlRapportUrl']


    $exportBtns = '';
    if ( isset ( $data['allowExport']) && $data['allowExport'] === true ) {
      $exportBtns = '<a href="'.$__appvar['baseurl'].'/'.$this->postData['currentHtmlRapportUrl'].'&type=xls" target="_blank" class="btn-new btn-default pull-right" id=""><i class="fa fa-file-excel-o" aria-hidden="true"></i> ' . vt('xls export') . '</a>';
    }

    $modelBtn = '';
    if ( isset($data['ModelPortefeuilleOmschrijving']) && ! empty ($data['ModelPortefeuilleOmschrijving']) ) {
      $modelBtn = '<li><a href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=MODEL&datum_van='.$data['start'].'&datum_tot='.getLaatsteValutadatum().'&Portefeuille='.$data['portefeuille'].'">' . vt('Model') . ' </a></li>
';
    }

    return '
      
      <button class="btn-new btn-default pull-right" id="filterDialogBtn"><i class="fa fa-filter" aria-hidden="true"></i> ' . vt('Filters instellen') . '</button>
  
          <div class="btn-group pull-right " style="margin-left: 5px;">
            <button type="button" class="btn-new btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-line-chart" aria-hidden="true"></i> ' . vt('Rapportages') . '
            </button>
            <ul class="dropdown-menu">
              <li><a href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=ATT&datum_van='.$data['start'].'&datum_tot='.$data['stop'].'&Portefeuille='.$data['portefeuille'].'">' . vt('Rendementen') . '</a></li>
              <li><a href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=VOLK&datum_van='.$data['start'].'&datum_tot='.$data['stop'].'&Portefeuille='.$data['portefeuille'].'">' . vt('Vermogensoverzicht') . ' </a></li>
              <li><a href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=TRANS&datum_van='.$data['start'].'&datum_tot='.$data['stop'].'&Portefeuille='.$data['portefeuille'].'">' . vt('Transacties') . '</a></li>
              <li><a href="'.$__appvar['baseurl'].'/rapportFrontofficeClientAfdrukkenHtml.php?rapport_types=MUT&datum_van='.$data['start'].'&datum_tot='.$data['stop'].'&Portefeuille='.$data['portefeuille'].'">' . vt('Mutaties') . ' </a></li>
              ' . $modelBtn . '
            </ul>
          </div>

          <a href="'.$__appvar['baseurl'].'/HTMLrapport/dashboard.php?port='.$data['portefeuille'].'" class="btn-new btn-default pull-right">
            <i class="fa fa-book" aria-hidden="true"></i> ' . vt('Dashboard') . '
          </a>
          ' . $rapportBackButtons . '
          ' . $exportBtns . '
          ' . $this->extraButtons . '
    
    ';
  }


  function getRapportHeader ($rapportBackButtons, $setup = array(), $infoText = '' ) {
    $data = $this->postData;
    global $__appvar;
    $header = '';
    $header .= '<br />
      <div class="formHolder box box12">
        <div style="height:40px;" class="formTitle textB">
          <span>' . vt('Rapportage') . '</span>
          '.$this->getHeaderButtons($rapportBackButtons, $setup).'
        </div>

        <div class="formContent" style="overflow: hidden;">
          <div style="margin-bottom: -3px;">
            <span '.(! empty ($infoText) ? 'style="padding: 5px; display: block;"':'').' class="letop">'.$infoText.'</span>
            <table class="table-striped table-hover" style="width:100%;">
              <tr>
                <td class="w150 bold">' . vt('Portefeuille') . '</td>
                <td class="w150">'.$data['portefeuille'].'</td>
                <td class="w50"></td>
                <td class="w150 bold">' . vt('Depotbank') . '</td>
                <td class="w150">'.$data['Omschrijving'].'</td>
                <td class="w50"></td>
                <td class="w150 bold">' . vt('Startdatum portefeuille') . '</td>
                <td class="w150">'.jul2form(db2jul($data['Startdatum'])).'</td>
              </tr>
      ';

    if ( ! empty($data['crm']) && isset ($data['crm']['Naam1']) && ! empty($data['crm']['Naam1']) ) {
      $header .= '<tr>
          <td class="bold">' . vt('Naam') . '</td>
          <td>'.$data['crm']['Naam1'].'</td>
          <td></td>
          <td class="bold">' . vt('Soort overeenkomst') . '</td>
          <td>'.$data['SoortOvereenkomst'].'</td>
          <td></td>
          <td class="bold">' . vt('Rapportagevaluta') . '</td>
          <td>'.$data['RapportageValuta'].'</td>
          <td></td>
          <td class="w150 bold">' . vt('Rapportagevaluta') . '</td>
          <td class="w150" >'.$data['rapportagevaluta'].'</td>
        </tr>';
    } else {
      $header .= '
          <tr>
            <td class="bold">' . vt('Client') . '</td>
            <td>' . $data['Client'] . '</td>
            <td></td>
            <td class="bold">' . vt('Soort overeenkomst') . '</td>
            <td>' . $data['SoortOvereenkomst'] . '</td>
            <td></td>
            <td class="bold">' . vt('Rapportagevaluta') . '</td>
            <td>' . $data['RapportageValuta'] . '</td>
          </tr>
        ';
    }

    $header .= '
          <tr>
            <td class="bold">' . vt('Rapportagedatum') . ' </td>
            <td><input type="hidden" name="stop" id="stop" value="'.$data['stop'].'"> '. $data['reportDate'] .'</td>
            <td></td>
            <td class="bold">' . vt('Risicoprofiel') . '</td>
            <td>'.$data['Risicoklasse'].'</td>
            <td></td>
            <td  class="bold">' . ( isset($data['ModelPortefeuilleOmschrijving']) ? vt('Modelportefeuille') : ''  ) . '</td>
            <td>' . ( isset($data['ModelPortefeuilleOmschrijving']) ? $data['ModelPortefeuilleOmschrijving'] : '' ) . '</td>
          </tr>
        </table>
        ' . (isset($data['getMessage']) ? $data['getMessage']:'') . '
      </div>
    </div>
    </div>
    ';

    return $header;
  }

  function criteriaMet($value1, $operator, $value2)
  {
    switch ($operator) {
      case '<':
        return $value1 < $value2;
        break;
      case '<=':
        return $value1 <= $value2;
        break;
      case '>':
        return $value1 > $value2;
        break;
      case '>=':
        return $value1 >= $value2;
        break;
      case '==':
        return $value1 == $value2;
        break;
      case '!=':
        return $value1 != $value2;
        break;
      default:
        return false;
    }
    return false;
  }


  function makeCsv($header, $rows, $fileName = 'test')
  {
    $output = fopen("php://output", 'w') or die("Can't open php://output");
    header("Content-Type:application/csv");
    header("Content-Disposition:attachment;filename=" . $fileName . ".csv");

    fputcsv($output, $header);

    foreach ($rows as $row ) {
      fputcsv($output, $row);
    }

    fclose($output) or die("Can't close php://output");
  }


  function makeXls($header, $rows, $fileName = 'test')
  {
    include_once("AE_cls_xls.php");
    $xls = new AE_xls();

    $xlsData[] = $header;
    foreach ($rows as $row ) {
      $xlsData[] = $row;
    }

    $xls->setData($xlsData);
    $xls->OutputXls(date('d-m-Y_H-i-s_') . '_' . $fileName.'.xls');
  }


}

?>