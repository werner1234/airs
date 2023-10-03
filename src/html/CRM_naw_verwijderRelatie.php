<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/27 17:15:27 $
    File Versie         : $Revision: 1.9 $

    $Log: CRM_naw_verwijderRelatie.php,v $
*/
include_once("wwwvars.php");

if(GetCRMAccess(2)==false)
{
  echo vt("Onvoldoende rechten om deze module te gebruiken.");
  exit;
}

function getDeleteQueries($relId)
{
  $db=new DB();
  $nawDeleteQueries=array();
  $portaalDeleteQueries=array();
  $dataStores=array();
  $recordsPortaal=array();
  $dataStoresPortaal=array();
  $tabelIds=array();
  $records=array();

  $tabelArray=array('CRM_naw_rekeningen'=>'rel_id',
               'CRM_naw_kontaktpersoon'=>'rel_id',
               'CRM_naw_dossier'=>'rel_id',
               'CRM_naw_documenten'=>'rel_id',
               'CRM_naw_cashflow'=>'rel_id',
               'CRM_naw_adressen'=>'rel_id',
               'CRM_evenementen'=>'rel_id',
               'taken'=>'rel_id',
               'agenda'=>'rel_id',
               'CRM_naw'=>'id'); //, 'dd_mailRouter'=>'CRM_naw_id'

  foreach($tabelArray as $tabel=>$idVeld)
  {
    $query = "SELECT id FROM $tabel WHERE $idVeld= '" . $relId . "'";

    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $tabelIds[$tabel][]=$data['id'];
    }

    $records[$tabel]=count($tabelIds[$tabel]);
    if($records[$tabel]>0)
      $nawDeleteQueries[]="DELETE FROM $tabel WHERE $idVeld= '" . $relId . "'";
  }

  $trackAndTraceIds=array();
  foreach($tabelIds as $tabel=>$ids)
  {
    $query = "SELECT id FROM trackAndTrace WHERE tabel='$tabel' AND recordId IN('" . implode("','",$ids) . "')";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $trackAndTraceIds[]=$data['id'];
    }
  }
  $records['trackAndTrace']=count($trackAndTraceIds);
  if($records['trackAndTrace'] > 0)
  {
    $nawDeleteQueries[]="DELETE FROM trackAndTrace WHERE id IN('" . implode("','", $trackAndTraceIds) . "')";
  }


  $query="SELECT datastore,dd_id FROM dd_reference WHERE module='CRM_naw' AND module_id='" . $relId . "'";

  $db->SQL($query);
  $db->query();
  $ddRecords=0;
  while($data=$db->nextRecord())
  {
    $dataStores[$data['datastore']][]=$data['dd_id'];
    $ddRecords++;
  }
  if($ddRecords>0)
    $nawDeleteQueries[]="DELETE FROM dd_reference WHERE module='CRM_naw' AND module_id='" . $relId . "'";
  $records['dd_reference']=$ddRecords;
  foreach($dataStores as $dataStore=>$idData)
  {
    $records[$dataStore] = count($idData);
    $nawDeleteQueries[]="DELETE FROM $dataStore WHERE id IN('".implode("','",$idData)."')";
  }

  if(isset($_DB_resources[DBportaal]))
  {
    $db2 = new DB(DBportaal);
    $query="SELECT id FROM clienten WHERE rel_id='" . $relId . "'";
    $db2->SQL($query);
    $portaalClient=$db2->lookupRecord();
    $tables=array('clienten'=>'rel_id');

    foreach($tables as $tabel=>$idVeld)
    {
      $query = "SELECT count(id) as aantal FROM $tabel WHERE $idVeld= '" . $relId . "'";
      $db2->SQL($query);
      $recordData=$db2->lookupRecord();
      $recordsPortaal[$tabel]=$recordData['aantal'];
      if($recordData['aantal']>0)
        $portaalDeleteQueries[]="DELETE FROM $tabel WHERE $idVeld= '" . $relId . "'";
    }

    $tables=array('datastoreDaily'=>'clientID');
    foreach($tables as $tabel=>$idVeld)
    {
      if($portaalClient['id'] >0)
      {
        $query = "SELECT count(id) as aantal FROM $tabel WHERE $idVeld= '" . $portaalClient['id'] . "'";

        $db2->SQL($query);
        $recordData = $db2->lookupRecord();
        $recordsPortaal[$tabel] = $recordData['aantal'];
        if($recordData['aantal']>0)
          $portaalDeleteQueries[] = "DELETE FROM $tabel WHERE $idVeld= '" . $portaalClient['id'] . "'";
      }
    }

    if($portaalClient['id'] >0)
      $clientenWhere=" OR (module='clienten' AND module_id='" . $portaalClient['id'] . "')";
    else
      $clientenWhere='';

    $query="SELECT datastore,dd_id FROM dd_reference WHERE (module='CRM_naw' AND module_id='" . $relId . "') $clientenWhere ";

    $db2->SQL($query);
    $db2->query();
    $ddRecords=0;
    while($data=$db2->nextRecord())
    {
      $dataStoresPortaal[$data['datastore']][]=$data['dd_id'];
      $ddRecords++;
    }
    if($ddRecords>0)
      $portaalDeleteQueries[]="DELETE FROM dd_reference WHERE (module='CRM_naw' AND module_id='" . $relId . "') $clientenWhere ";
    $recordsPortaal['dd_reference']=$ddRecords;
    foreach($dataStoresPortaal as $dataStore=>$idData)
    {
      $recordsPortaal[$dataStore] = count($idData);
      if(count($idData)>0)
        $portaalDeleteQueries[]="DELETE FROM $dataStore WHERE id IN('".implode("','",$idData)."')";
    }

  }
  return array('nawDeleteQueries'=>$nawDeleteQueries,'portaalDeleteQueries'=>$portaalDeleteQueries,'records'=>$records,'recordsPortaal'=>$recordsPortaal);
}

function toonHandmatigVerwijderen()
{
  global $__appvar,$editcontent,$USR;
  $editcontent['javascript'] = '

function showLoading(text) {
  // add the overlay with loading image to the page
  $(\'#overlay\').remove();
  var over = \'<div id="overlay"><div id="loading-box">\' +
          \'<div id="loading-txt">\' + text + \'</div>\' +
          \'<img id="loading-img" src="images/ajax-loader.gif">\' +
          \'</div></div>\';
  $(over).appendTo(\'body\');
}
;
function removeLoading() {
  $(\'#overlay\').remove();
}


function clientDetailsOphalen(lookup)
{
  showLoading(\'Laden\');
  var postData = {  id : $(\'#id\').val() }
  var formURL = \'CRM_naw_verwijderRelatie.php\';
  return $.ajax({
    url : formURL + \'?lookup=\'+lookup,
    type: "POST",
    dataType: \'json\',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      if ( data.success == false ) {
        $(\'#ClientDetails\').html(\'<div class="alert alert-warning">Details konden niet worden opgehaald</div>\');
        removeLoading();
        return false;
      } else {
        $(\'#ClientDetails\').html(data.table);
        removeLoading();
        return true;
      }
    }
  });
}
';

  echo Template($__appvar["templateContentHeader"],$editcontent);


  $autocomplete = new Autocomplete();
  $autocomplete->resetVirtualField('zoekveld');


  /** set autocomplete velden **/

  $zoekveld = $autocomplete->addVirtuelField(
    'zoekveld',
    array(
      'autocomplete' => array(
        'table'        => 'CRM_naw',
        'order' => 'CRM_naw.zoekveld ASC',
        'label'        => array(
          'CRM_naw.zoekveld',
          'CRM_naw.naam'
        ),
        'searchable'   => array('CRM_naw.zoekveld','CRM_naw.naam' ,'CRM_naw.portefeuille' ),
        'field_value'  => array('CRM_naw.id'),
        'extra_fields' => array('CRM_naw.naam','CRM_naw.naam1','CRM_naw.zoekveld'),
        'value'        => 'id',
        'actions'      => array(
          'select' => '
        event.preventDefault(); 
        $("#id").val(ui.item.field_value);
        $("#zoekveld").val(ui.item.data.zoekveld);
        $("#naam").val(ui.item.data.naam);
        $("#naam1").val(ui.item.data.naam1);
        clientDetailsOphalen(\'clientDetails\');
      '
        ),
        'conditions'   => array(
          'AND' => array( '(aktief=0)' )
        )
      ),
      'form_size'    => '25',
      'validate'     => '',
      'form_value'   => '',
      'form_class'   => ''
    )
  );

  $editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('zoekveld');


  if($USR=='JBR'||$USR=='FEGT')
  {
    $bulklink = '<a href="?bulk=1">(bulk)</a>';
  }
  else
  {
    $bulklink = '';
  }
  ?>
  <br>
  <h2><?= vt('Client selectie'); ?> <?=$bulklink?> </h2>
  <div class="form">

    <div class="formblock">
      <div class="formlinks"><?= vt('Zoekveld'); ?> </div>
      <div class="formrechts"> <?=$zoekveld?> </div>
    </div>
    <br><br>
    <hr>
    <br><br>

    <div class="formblock">
      <div class="formlinks"><?= vt('RelatieId'); ?></div>
      <div class="formrechts">
        <input  class="" type="text"  size="25" value="" name="id" id="id" readonly>
      </div>
    </div>

    <div class="formblock">
      <div class="formlinks"><?= vt('Naam'); ?></div>
      <div class="formrechts">
        <input  class="" type="text"  size="25" value="" name="naam" id="naam" readonly>
      </div>
    </div>

    <div class="formblock">
      <div class="formlinks"><?= vt('Extra naam'); ?></div>
      <div class="formrechts">
        <input  class="" type="text"  size="25" value="" name="naam1" id="naam1" readonly>
      </div>
    </div>

    <div class="formblock">
      <div class="formlinks"><?= vt('Details'); ?></div>
      <div class="formrechts" id="ClientDetails">

      </div>
    </div>
  </div>

  <?
  echo Template($__appvar["templateRefreshFooter"],$editcontent);
}

function toonBulkVerwijderen()
{
  global $__appvar,$editcontent,$USR;
  echo Template($__appvar["templateContentHeader"],$editcontent);

?>
  <form enctype="multipart/form-data"  method="POST" name="editForm" >
    <input type="hidden" name="MAX_FILE_SIZE" value="256000000">
    <input type="hidden" name="posted" value="true" />

    <div class="form">
      <input type="hidden" name="action" value="{action}">
      <input type="hidden" name="returnUrl" value="{returnUrl}">
       <div class="formblock">
          <div class="formblock">
           <div class="formlinks"> <?= vt('Testrun'); ?> </div>
           <div class="formrechts"> <input value="1" type="checkbox" name="testrun" checked > </div>
          </div>
          <div class="formblock">
          <div class="formlinks"> <?= vt('Bestand met CRM_naw.Ids'); ?> </div>
          <div class="formrechts">
            <input type="file" name="CRM_nawIds" size="50">
          </div>
            <div class="formblock">
              <div class="formlinks"> &nbsp;</div>
              <div class="formrechts"><br><input type="submit"></div>
            </div>
        </div>


  </form></div>
<?
  echo Template($__appvar["templateRefreshFooter"],$editcontent);
}

if($_FILES['CRM_nawIds']['tmp_name'] <> '')
{
  $crmIds=array();
  if($_POST['testrun']==1)
    $skipQueries=true;
  else
    $skipQueries=false;

  if($skipQueries==true)
  {
    echo "Testrun: Queries worden niet uitgevoerd.";
  }
  echo "<table><tr><td><b>" . vt('id') . "</b></td><td><b>" . vt('zoekveld') . "</b></td><td><b>" . vt('naam') . "</b></td><td><b>" . vt('email') . "</b></td><td><b>" . vt('portefeuille') . "</b></td></tr>\n";
  $fileData=file_get_contents($_FILES['CRM_nawIds']['tmp_name']);
  $regels=explode("\n",$fileData);
  foreach($regels as $regelnr=>$regelData)
  {
    $regelData=trim($regelData);
    $ids=explode(",",$regelData);
    foreach($ids as $id)
    {
      $id= (int) $id;
      if($id>0)
        $crmIds[$id]=$id;
    }
    $ids=explode(";",$regelData);
    foreach($ids as $id)
    {
      $id= (int) $id;
      if($id>0)
        $crmIds[$id]=$id;
    }
  }
  $crmIds=array_values($crmIds);
  $query="SELECT id, zoekveld, naam, email, portefeuille FROM CRM_naw WHERE id IN('".implode("','",$crmIds) ."')";
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  $verwijderIds=array();
  while($data=$db->nextRecord())
  {

    $verwijderIds[]=$data['id'];
    echo "<tr><td>".$data['id']."</td><td>".$data['zoekveld']."</td><td>".$data['naam']."</td><td>".$data['email']."</td><td>".$data['portefeuille']."</td></tr>\n";
  }
  echo "</table><br>\n<br>\n";
  //listarray($verwijderIds);
  foreach($verwijderIds as $id)
  {
    $tmp=getDeleteQueries($id);
    $nawDeleteQueries=$tmp['nawDeleteQueries'];
    $portaalDeleteQueries=$tmp['portaalDeleteQueries'];
    $db = new DB();
    foreach($nawDeleteQueries as $query)
    {
      echo "$query ;<br>\n";
      if($skipQueries==false)
      {
        $db->SQL($query);
        $db->Query();
      }
    }
    if(isset($_DB_resources[DBportaal]))
    {
      $db2 = new DB(DBportaal);
      foreach ($portaalDeleteQueries as $query)
      {
        echo "$query ;<br>\n";
        if($skipQueries==false)
        {
          $db2->SQL($query);
          $db2->Query();
        }
      }
    }
  }
}


if($_GET['lookup']=='clientDetails' || $_GET['lookup']=='clientVerwijderen')
{
  global $USR;
  $AEJson = new AE_Json();

  $html='';

  $tmp=getDeleteQueries($_POST['id']);
  $nawDeleteQueries=$tmp['nawDeleteQueries'];
  $portaalDeleteQueries=$tmp['portaalDeleteQueries'];
  $records=$tmp['records'];
  $recordsPortaal=$tmp['recordsPortaal'];

  if($_GET['lookup']=='clientDetails')
  {
    $table = '<table>';
    $table .= "<tr class='list_kopregel' > <td><b>" . vt('Tabel') . "</b></td><td><b>" . vt('records') . "</b></td><td><b>" . vt('info') . "</b></td></tr>\n";
    $n = 0;
    foreach ($records as $tabel => $aantal)
    {
      $info = vt('Worden verwijderd.');
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>" . $tabel . "</td><td align='right'>" . $aantal . "</td> <td> " . $info . " </td></tr>\n";
    }
    if(count($recordsPortaal)>0)
      $table.="<tr><td><b>" . vt('portaal') . "</b></td></tr>";
    foreach ($recordsPortaal as $tabel => $aantal)
    {
      $info = vt('Worden uit portaal verwijderd.');
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>" . $tabel . "</td><td align='right'>" . $aantal . "</td> <td> " . $info . " </td></tr>\n";
    }
    $table .= '</table>';
    $html .= $table;

  }

  $html .= "<br>
<form action='CRM_naw_verwijderRelatie.php'>
<input type='button' value='Relatie verwijderen' onclick='clientDetailsOphalen(\"clientVerwijderen\");'>
</form>";

  if($_GET['lookup']=='clientVerwijderen')
  {
    $html ='';
    $db = new DB();
    foreach($nawDeleteQueries as $query)
    {
      $html .= "$query <br>\n";
      $db->SQL($query);
      $db->Query();
    }
    if(isset($_DB_resources[DBportaal]))
    {
      $db2 = new DB(DBportaal);
      foreach ($portaalDeleteQueries as $query)
      {
        $html .= "$query <br>\n";
        $db2->SQL($query);
        $db2->Query();
      }
    }
  }

    echo $AEJson->json_encode(
      array(
        'success' => true,
        'table'   => $html
      )
    );
    exit();

}

if($_GET['bulk']==1)
  toonBulkVerwijderen();
else
  toonHandmatigVerwijderen();



?>