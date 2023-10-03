<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/04/10 16:01:16 $
    File Versie         : $Revision: 1.5 $

    $Log: vergeetPortefeuille.php,v $
    Revision 1.5  2019/04/10 16:01:16  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");

if(checkAccess()==false)
{
  echo vt("Onvoldoende rechten om deze module te gebruiken");
  exit;
}

if($_GET['lookup']=='clientDetails' || $_GET['lookup']=='clientVerwijderen')
{
  global $USR;
  $AEJson = new AE_Json();
  $db=new DB();
  $records=array();
  $ids = array();
  $clientName='';
  $clientId='';
  //$html=''.$_GET['lookup'];

  $query = "SELECT id,Client,Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuilles.consolidatie=0 AND Portefeuille= '" . $_POST['Portefeuille'] . "'";
  $db->SQL($query);
  $pRecord=$db->lookupRecord();
  $ids['Portefeuilles'][] = $pRecord['id'];
  
  /*
  $query = "SELECT id,Client FROM Clienten WHERE Clienten.consolidatie=0 AND Client= '" . $pRecord['Client'] . "'";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $clientId=$data['id'];
    $ids['Clienten'][] = $clientId;
    $clientName=$data['Client'];
    $records['Clienten']++;
  }
*/
  $query = "SELECT Client, Portefeuille, Vermogensbeheerder, date(startdatum) as startdatum, date(einddatum) as einddatum FROM Portefeuilles WHERE Portefeuilles.consolidatie=0 AND Client= '" . $pRecord['Client'] . "' AND Vermogensbeheerder= '" . $pRecord['Vermogensbeheerder'] . "'";
  $db->SQL($query);
  $db->Query();
  $portefeuilles=array();
  while($data=$db->nextRecord())
  {
    $portefeuilles[$data['Portefeuille']]=$data;
    $records['Portefeuilles']++;
  }

  $idQueries=array('Rekeningmutaties'=>"SELECT Rekeningmutaties.id FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening WHERE Rekeningen.consolidatie=0 AND Rekeningen.Portefeuille= '" . $pRecord['Portefeuille'] . "'",
                   'VoorlopigeRekeningmutaties'=>"SELECT VoorlopigeRekeningmutaties.id FROM VoorlopigeRekeningmutaties JOIN Rekeningen ON VoorlopigeRekeningmutaties.Rekening=Rekeningen.Rekening WHERE Rekeningen.consolidatie=0 AND Rekeningen.Portefeuille= '" . $pRecord['Portefeuille'] . "'");

  foreach($idQueries as $tabel=>$query)
  {
    $db->SQL($query);
    $db->Query();
    //$html.="<br>$query<br>";
    while ($data = $db->nextRecord())
    {
      $ids[$tabel][] = $data['id'];
    }
  }

  $whereParts=array();
  foreach($ids as $tabel=>$idArray)
  {
    if(count($idArray)>0)
    {
      if($tabel=='Clienten'||$tabel=='Portefeuilles')
        $veld='Client';
      else // Rekeningmutaties & VoorlopigeRekeningmutaties
        $veld='Omschrijving';

      if($records['Portefeuilles']==1 || $tabel== 'Rekeningmutaties' || $tabel=='VoorlopigeRekeningmutaties')
        $whereParts[] = "(tabel='$tabel' AND veld='$veld' AND recordId IN('" . implode("','", $idArray) . "'))";
    }
  }
  $trackAndTraceWhere=implode(' OR ',$whereParts);
  $query=" SELECT id FROM trackAndTrace WHERE ".$trackAndTraceWhere;
  $db->SQL($query);
  $db->Query();
  while ($data = $db->nextRecord())
  {
    $ids['trackAndTrace'][] = $data['id'];
  }
  $query=" SELECT id FROM klantMutaties WHERE ".$trackAndTraceWhere;
  $db->SQL($query);
  $db->Query();
  while ($data = $db->nextRecord())
  {
    $ids['klantMutaties'][] = $data['id'];
  }
  //$html.="<br> $query <br>";


  if($_GET['lookup']=='clientDetails')
  {
    if (count($portefeuilles) > 1) // test 03855226
    {
      $table = '<h2>Meerdere portefeuille bij clientcode ' . $pRecord['Client'] . ' aanwezig.</h2><table>';//De clientcode zal niet worden aangepast.
      $table .= "<tr class='list_kopregel' > <td><b>Portefeuille</b></td><td><b>Client</b></td><td><b>Vermogensbeheerder</b></td><td><b>Startdatum</b></td><td><b>Einddatum</b></td></tr>\n";
      $n = 0;
      foreach ($portefeuilles as $portefeuille => $data)
      {
        $n++;
        $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>" . $portefeuille . "</td><td>" . $data['Client'] . "</td><td>" . $data['Vermogensbeheerder'] . "</td><td>" . $data['startdatum'] . "</td><td>" . $data['einddatum'] . "</td> </tr>\n";
      }
      $table .= '</table><br>';
    }
    $html .= $table;

    $records['Rekeningmutaties'] = count($ids['Rekeningmutaties']);
    $records['VoorlopigeRekeningmutaties'] = count($ids['VoorlopigeRekeningmutaties']);
    $records['trackAndTrace'] = count($ids['trackAndTrace']);
    $records['klantMutaties'] = count($ids['klantMutaties']);

    $table = '<table>';
    $table .= "<tr class='list_kopregel' > <td><b>Tabel</b></td><td><b>records</b></td><td><b>info</b></td></tr>\n";
    $n = 0;
    foreach ($records as $tabel => $aantal)
    {
      $n++;
      if ($tabel == 'Portefeuilles' || $tabel == 'Clienten')
      {
        if ($records['Portefeuilles'] > 1)
        {
          $info = $pRecord['Portefeuille'].' zal worden aangepast.';
        }
        else
        {
          $info = $tabel . '.Client '.vt("zal worden aangepast").'.';
        }
      }
      else
      {
        if($tabel=='trackAndTrace'||$tabel=='klantMutaties')
          $info = $tabel . ' '.vt("oude/nieuwe waarde zal anoniem gemaakt worden").'.';
        else
          $info = $tabel . '.Omschrijving '.vt("zal anoniem gemaakt worden").'.';
      }
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>" . $tabel . "</td><td align='right'>" . $aantal . "</td> <td> " . $info . " </td></tr>\n";
    }
    $table .= '</table>';
    $html .= $table;
  }

  if($clientName=='ClId'.$clientId)
    $html .= "<br><h2>".vt("Clientnaam is al aangepast naar")." $clientName.</h2>";
  else
    $html .= "<br>
<form action='vergeetPortefeuille.php'>
<input type='button' value='".vt("Records anoniem maken")."' onclick='javascript:portefeuilleDetailsOphalen(\"clientVerwijderen\");'>
</form>";

  if($_GET['lookup']=='clientVerwijderen')
  {
    $html='';
    $table = '<table>';
    $table .= "<tr class='list_kopregel' > <td><b>Tabel</b></td><td><b>Id</b></td><td><b>OudeWaarde</b></td><td><b>NieuweWaarde</b></td></tr>\n";


    $verwijderTxt = vt('Verwijderd per').' '.date('d-m-Y');
    foreach($ids['Rekeningmutaties'] as $rekMutId)
    {
      $rekmutatieObject = new Rekeningmutaties();
      $rekmutatieObject->getById($rekMutId);
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>Rekeningmutaties</td><td align='right'>" . $rekMutId . "</td> <td> " . $rekmutatieObject->data['fields']['Omschrijving']['value']  . " </td> <td>$verwijderTxt</td></tr>\n";
      $rekmutatieObject->set('Omschrijving',$verwijderTxt);
      $rekmutatieObject->save();

    }

    //if (count($portefeuilles) == 1)
    //{
/*
      $clientObject=new Client();
      $clientObject->getById($clientId);
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>Clienten</td><td align='right'>" . $clientId . "</td> <td> " . $clientObject->data['fields']['Client']['value']  . " </td> <td>ClId$clientId</td></tr>\n";
      $clientObject->set('Client','ClId'.$clientId);
      foreach($clientObject->data['fields'] as $field=>$veldGegegevens)
      {
        if(!in_array($field,array('id','Client','add_date','add_user','change_date','change_user')))
        {
          $clientObject->set($field,'');
        }
      }
      $clientObject->save();
*/
      $portObject=new Portefeuilles();
      $portObject->getById($pRecord['id']);
      $table .= "<tr class='list_dataregel' onmouseover=\"this.className='list_dataregel_hover'\" onmouseout=\"this.className='list_dataregel'\" >  <td>Portefeuilles</td><td align='right'>" . $pRecord['id'] . "</td> <td> " . $portObject->data['fields']['Client']['value']  . " </td> <td>Anoniem</td></tr>\n";//ClId$clientId
      $portObject->set('Client','Anoniem');//'ClId'.$clientId);
      $portObject->save();
   // }

    $queries=array();
    if(count($ids['trackAndTrace'])>0)
    {
      $queries[] = "UPDATE trackAndTrace SET oudeWaarde='ANONIEM',nieuweWaarde='ANONIEM' WHERE id IN ('" . implode("','", $ids['trackAndTrace']) . "')";
    }
    $queries[]="UPDATE trackAndTrace SET oudeWaarde='ANONIEM' WHERE oudeWaarde <> 'ANONIEM' AND nieuweWaarde <>'ANONIEM' AND ".$trackAndTraceWhere;

    if(count($ids['klantMutaties'])>0)
    {
      $queries[] = "UPDATE klantMutaties SET oudeWaarde='ANONIEM',nieuweWaarde='ANONIEM' WHERE id IN ('" . implode("','", $ids['klantMutaties']) . "')";
    }

    foreach($queries as $query)
    {
      $db->SQL($query);
      //$html.="<br>$query<br>\n";
      $db->Query();
    }

    $table .= '</table>';
    $html .= $table;

  }

    echo $AEJson->json_encode(
      array(
        'success' => true,
        'table'   => $html
      )
    );
    exit();

}




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


function portefeuilleDetailsOphalen(lookup)
{
  showLoading(\'Laden\');
  var postData = {  Portefeuille : $(\'#Portefeuille\').val() }
  var formURL = \'vergeetPortefeuille.php\';
  return $.ajax({
    url : formURL + \'?lookup=\'+lookup,
    type: "POST",
    dataType: \'json\',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      if ( data.success == false ) {
        $(\'#PortefeuilleDetails\').html(\'<div class="alert alert-warning">'.vt("Details konden niet worden opgehaald").'</div>\');
        removeLoading();
        return false;
      } else {
        $(\'#PortefeuilleDetails\').html(data.table);
        removeLoading();
        return true;
      }
    }
  });
}



';

echo template($__appvar["templateContentHeader"],$editcontent);


$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Portefeuille');


/** set autocomplete velden **/

$portefeuilleVeld = $autocomplete->addVirtuelField(
  'Portefeuille',
  array(
    'autocomplete' => array(
      'table'        => 'Portefeuilles',
      'order' => 'Portefeuilles.Portefeuille ASC',
      'label'        => array(
        'Portefeuilles.Portefeuille',
        'Portefeuilles.Client'
      ),
      'searchable'   => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
      'field_value'  => array('Portefeuilles.Portefeuille'),
      'extra_fields' => array('*'),
      'value'        => 'Portefeuille',
      'actions'      => array(
        'select' => '
        event.preventDefault(); 
        $("#Portefeuille").val(ui.item.field_value);
        $("#Einddatum").val(ui.item.data.Einddatum);
        $("#Client").val(ui.item.data.Client);
        portefeuilleDetailsOphalen(\'clientDetails\');
      '
      ),
      'conditions'   => array(
        'AND' => array(
          '(Portefeuilles.EindDatum <= now() - interval 5 year)','Portefeuilles.consolidatie=0'
        )
      )
    ),
    'form_size'    => '25',
    'validate'     => '',
    'form_value'   => '',
    'form_class'   => ''
  )
);

$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Portefeuille');



?>
  <br>
  <h2><?=vt("Portefeuille selectie")?></h2>
  <div class="form">
    <input type="hidden" name="action" value="update">
    <div class="formblock">
      <div class="formlinks"><?=vt("Portefeuille")?> </div>
      <div class="formrechts"> <?=$portefeuilleVeld?> </div>
    </div>
    <br><br>
    <hr>
    <br><br>
    <div class="formblock">
      <div class="formlinks"><?=vt("Client")?> </div>
      <div class="formrechts">
        <input  class="" type="text"  size="25" value="" name="Client" id="Client" readonly>
      </div>
    </div>

    <div class="formblock">
      <div class="formlinks"><?=vt("Einddatum")?> </div>
      <div class="formrechts">
        <input  class="" type="text"  size="25" value="" name="Einddatum" id="Einddatum" readonly>
      </div>
    </div>

    <div class="formblock">
      <div class="formlinks"><?=vt("Details")?> </div>
      <div class="formrechts" id="PortefeuilleDetails">

      </div>
    </div>
  </div>

<?



echo template($__appvar["templateRefreshFooter"],$editcontent);


