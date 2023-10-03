<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/06/12 14:10:47 $
    File Versie         : $Revision: 1.57 $

    $Log: orderregelsListV2.php,v $
    Revision 1.57  2020/06/12 14:10:47  rm
    8682
*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/mysqlListClean.php");
include_once("./rapport/rapportRekenClass.php");
include_once("./orderControlleRekenClass.php");

$__debug = false;

$__appvar['rowsPerPage']=10000;
$AETemplate = new AE_template();

$orderObj = new OrdersV2();
$orderRegelsObj = new OrderRegelsV2();
$orderLogs = new orderLogs();
$uitvoeringen = new OrderUitvoeringV2();

//$content = '';
$content['script_voet'] = "
	//$('#dataTable').DataTable();
  //clog('test');
";
//$content = $editcontent;
//$content = '';


if ( isset ($_GET['orderToOpen']) && requestType('ajax') === true ) {
  $AEJson = new AE_Json();
  $ajaxHtml = '';
  $orderLogInfo = '';
  $success = false;
  
  
  $orderRegelData = $orderRegelsObj->parseById((int) $_GET['orderToOpen']);
  $orderData = $orderObj->parseById($orderRegelData['orderid']);
//  $orderLogDatas = $orderLogs->getForOrder($data['order_id']['value']);
  $orderLogDatas = $orderLogs->getForOrder($orderData['id']);


  if ( ! empty ($orderRegelData) && ! empty ($orderData) ) {
    $success = true;
  
    foreach ( $orderLogDatas as $orderLogData ) {
      $orderLogInfo .= date('d-m-Y H:i:s', strtotime($orderLogData['change_date'])) . '/' . $orderLogData['add_user'] . ' - ' . $orderLogData['message'] . "\n";
    }
    
    $db=new DB();
    $query="SELECT * FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid='".$orderRegelData['orderid']."'";
    $db->SQL($query);
    $db->Query();
    $uitvoeringenData=array();
    $totaalUitvoeringen=0;
    while ($dataUit = $db->nextRecord())
    {
      $uitvoeringenData[]=$dataUit;
    }
  
  
      $ajaxHtml .= '
  
        <table class="table table-bordered" style="width: 100%;">
        <tr>
            <td><strong>' . vt('Order') . '</strong></td>
            <td>' . $orderIndenfiticatie. ' (' . date('d-m-Y', strtotime($orderData['add_date'])) . ')</td>
  
            <td style="width:100px;"></td>
            <td><strong>' . vt('Status') . '</strong></td>
            <td>' . $__ORDERvar["orderStatus"][$orderData['orderStatus']] . '</td>
          </tr>
  
          <tr>
            <td><strong>' . vt('Client') . '</strong></td>
            <td>' . $orderRegelData['client'] . '</td>
            <td style="width:100px;"></td>
            <td><strong>Portefeuille</strong></td>
            <td>' . $orderRegelData['portefeuille'] . '</td>
          </tr>
  
          <tr>
            <td><strong>' . vt('ISIN-code') . '</strong></td>
            <td>' . $orderData['ISINCode'] . '</td>
            <td style="width:100px;"></td>
            <td><strong>' . vt('Fonds') . '</strong></td>
            <td>' . $orderData['fonds'] . ' - ' . $orderData['fondsOmschrijving'] . '</td>
          </tr>
  
          </tr>
           <tr>
            <td><strong>' . vt('Beurs') . '</strong></td>
            <td>' . $orderData['beurs'] . '</td>
            <td style="width:100px;"></td>
            <td>
  
            <strong>' . vt('Transactie') . ': </strong>
            </td>
             <td colspan="2">
  
            ' . $__ORDERvar["transactieSoort"][$orderData['transactieSoort']] . '
            ' . vt('van') . ' ' . $orderRegelData['aantal'] . ' ' . vt('stuk(s)') . '
            </td>
          </tr>
  
          <tr>
            <td><strong>' . vt('Koerslimiet') . '</strong></td>
            <td>' . $orderData['koersLimiet'] . '</td>
            <td style="width:100px;"></td>
            <td><strong>' . vt('Soort tijdlimiet') . '</strong></td>
            <td>' . $orderData['tijdsSoort'] . ' - ' . date('d-m-Y', strtotime($orderData['tijdsLimiet'])) . '</td>
          </tr>
  
          <tr>
  
          </tr>
  
          <tr>
            <td  colspan="5"><strong>' . vt('Memo') . '</strong><br />
            ' . $orderData['memo'] . '</td>
          </tr>
          <tr>
            <td  colspan="5">
            <br />
              <strong>' . vt('Order log') . '</strong><br />
              <textarea style="width:100%!important; height: 100px;">' . $orderLogInfo . '</textarea>
              </td>
          </tr>
  
  
          </table>
          <br /><br />
          <strong style="margin-left: 3px;">' . vt('Uitvoeringen') . '</strong>
  
      ';
  
  
  
      if ( ! empty ($uitvoeringenData) ) {
        $ajaxHtml .= '
  
        <table class="table" style="width:100%">
            <tr>
              <td><strong>' . vt('Uitvoeringsaantal') . '</strong></td>
              <td><strong>' . vt('Uitvoeringsprijs') . '</strong></td>
              <td><strong>' . vt('Uitvoeringsdatum') . '</strong></td>
              <td><strong>' . vt('Nettokoers') . '</strong></td>
              <td><strong>' . vt('Opgelopenrente') . '</strong></td>
            </tr>
        ';
  
  
        foreach ( $uitvoeringenData as $uitvoering ) {
          $ajaxHtml .= '
            <tr>
              <td>' . $uitvoering['uitvoeringsAantal'] . '</td>
              <td>' . $uitvoering['uitvoeringsPrijs'] . '</td>
              <td>' . date('d-m-Y', strtotime($uitvoering['uitvoeringsDatum'])) . '</td>
              <td>' . $uitvoering['nettokoers'] . '</td>
              <td>' . $uitvoering['opgelopenrente'] . '</td>
            </tr>
          ';
  
        }
    
        $ajaxHtml .= '</table>';
      } else {
        $ajaxHtml .= vt('Er zijn geen uitvoeringen voor deze order.');
      }
    
    $ajaxHtml .= '<br /><br />';
  }
  echo $AEJson->json_encode(
    array(
      'success' => $success,
      'content' => $ajaxHtml
    )
  );
  exit();
}









$db = new DB();

$toonNota=checkOrderAcces('notaModule');
if($_GET['batchId'] > 0)
{
  $query="
  SELECT OrdersV2.id,OrdersV2.orderSoort,OrdersV2.transactieSoort, portefeuille
  
  FROM OrdersV2
  LEFT JOIN OrderRegelsV2 on OrderRegelsV2.orderid = OrdersV2.id
  WHERE OrdersV2.batchId = '".$_GET['batchId']."'";
  
  
  
  
  $db->SQL($query);
  $db->Query();
  $orderIds=array();
  $combi=true;
  
  
  $thisPortefeuille = null;
//  $orderuitvoerBewaarder = null;
  while($data=$db->nextRecord())
  {
    $thisPortefeuille = $data['portefeuille'];
    if($data['orderSoort']=='C') {//voor combi de orders via de batchid verzamelen anders is filtering op orderId voldoende. (batchid aanroepen zouden alleen voor de combi moeten plaatvinden maar dit is blijkbaar niet het geval, daardoor deze work-around)
      $orderIds[]=$data['id'];
    } else {
      $combi=false;
    }
  }
  
  
  /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
//  $portefeuilleObject = new Portefeuilles ();
//  $portefeuilleData = $portefeuilleObject->parseBySearch(
//    array('Portefeuille' => $thisPortefeuille),
//    array('Vermogensbeheerder', 'Depotbank')
//  );
//
//  $vermogensbeheerderObj = new Vermogensbeheerder ();
//  $vermogensbeheerderData = $vermogensbeheerderObj->parseBySearch(
//    array('vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder']),
//    array('OrderuitvoerBewaarder')
//  );
//  $orderuitvoerBewaarder = $vermogensbeheerderData['OrderuitvoerBewaarder'];
//  debug($orderuitvoerBewaarder);
  
  if(count($orderIds) < 1 && $combi==true)
  {
    echo "Geen orders gevonden voor batch ".$_GET['batchId'].".";
    exit;
  }
}

if( requestType('ajax') )
{
    /** selecteer ajax templates **/
    $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
    $__appvar['templateRefreshFooter'] = 'templates/ajax_voet_list.inc';
    $subHeader= '<a id="orderUitvoeringAdd" href="ordersEditV2.php?action=new&orderid=' . $_GET['orderid'] . '&batchId=' . $_GET['batchId'] . '" ><img src="images//16/record_new.gif" width="16" height="16" border="0" alt="record toevoegen" align="absmiddle">&nbsp;toevoegen</a>';

    $content['style'] = "
      #orderregels {
        border: 1px solid lightgray;
        border-radius: 5px;
        padding: 10px;
        width: 98%;
      }
      
      #orderregels .edit_actionTxt {
        text-shadow: 0 1px white;
        border-bottom: 1px solid #CDCDCD;
        color: #636363;
        font-weight: 600;
        margin: -10px;
        padding: 10px;
        border-radius: 5px 5px 0px 0px;
        background: #eaeaea;
        background-size: 100%;
        background-image: -webkit-linear-gradient(top, #fdfdfd, #eaeaea);
      }

      #orderregels .edit_actionTxt a {
        float: right;
        border-left: 1px solid lightgray;
        margin-left: 15px;
        line-height: 37px;
        padding-right: 10px;
        margin-top: -10px;
        padding-left: 10px;

      }
    ";

    $content['script_voet'] .= "
      $('#orderUitvoeringAdd').on('click', function () {
        event.preventDefault();
        console.log(encodeURI($(this).attr('href')));
        $('#modelContent').load(encodeURI($(this).attr('href')));
      });
      
      $('#orderregels .list_button  a').on('click', function () {
        event.preventDefault();
        $('#modelContent').load(encodeURI($(this).attr('href')));
      });
      
    $('.aantalLiveEdit').on('change', function () {
      console.log($(this).data('orderregelid'));
      

      $.ajax({
        url : 'ordersEditV2.php?orderregelId=' + $(this).data('orderregelid') + '&id=' + $(this).data('id') + '&batchId' + $(this).data('batchid') + '&aantal=' + $(this).val() + '' ,
        type: 'POST',
        dataType: 'json',
        success:function(data, textStatus, jqXHR) {
          

        }
      });


    });

    ";

  }

$content['pageHeader'] = '<div class="formTitle textB">'.$mainHeader . $subHeader.'</div>';

  /** wanneer we een ajax request doen **/
  if( requestType('ajax') ) {
    /** selecteer ajax templates **/
    $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
    $__appvar['templateRefreshFooter'] = 'templates/ajax_voet_list.inc';
    $content['jsincludes'] = '';
//    $content['jsincludes'] = $AETemplate->loadJs('dataTables/jquery.dataTables.min');
    $content['styleinclude'] = $AETemplate->loadCss('workspace');
//    $content['styleinclude'] .= $AETemplate->loadCss('dataTables/jquery.dataTables.min');

    $content['pageHeader'] = '';
  }

  $list = new mysqlListClean();
  $list->editScript = ( isset ($editScript) ? $editScript : array());
  $list->perPage = $__appvar['rowsPerPage'];
 // $list->perPage = '10000';


/** Combinatie orders **/
if(count($orderIds) > 0 && $_GET['batchId'] > 0 )
{
  $list->addColumn("OrderRegelsV2","orderid",array("list_width"=>"100","search"=>false,"order"=>false,'list_order'=>false,"list_invisible"=>true));
  $list->addColumn("OrdersV2","fondsOmschrijving",array("list_width"=>"250","search"=>false,'list_order'=>false));
  $list->addColumn("OrdersV2","ISINCode",array('description'=>'ISIN-code',"list_width"=>"100","search"=>false,'list_order'=>false));
  $list->addColumn("OrderRegelsV2","aantal",array("list_width"=>"100","search"=>false,'list_order'=>false));
  $list->addColumn("OrdersV2","transactieSoort",array('description'=>'Ts', "list_width"=>"100","search"=>false,'list_order'=>false));
  $list->addColumn("OrdersV2","transactieType",array("list_width"=>"100","search"=>false,'list_order'=>false));
  $list->addColumn("OrderRegelsV2","controleStatus",array("list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","kopieOrderId",array("list_invisible"=>true));
  $list->addColumn("OrdersV2","add_date",array("list_invisible"=>true));
  $list->addColumn("","bedrag",array("list_align"=>"right",'description'=>'Geschat bedrag', 'list_visible' => true, "search"=>false, "list_invisible"=>false));

  $list->addColumn("OrdersV2","koersLimiet",array("list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","rekening",array("list_invisible"=>true));
  $list->addColumn("OrdersV2","fonds",array('description'=>'',"search"=>false,"list_invisible"=>true));

  $list->addColumn("OrderRegelsV2","portefeuille",array("search"=>false,"list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","orderregelStatus",array("search"=>false,'list_order'=>false));
  $list->addColumn("OrdersV2","fixOrder",array("search"=>false,'list_order'=>false));
  
//  if ( (int) $orderuitvoerBewaarder === 1 ) {
//    $list->addColumn("OrdersV2","depotbank",array("search"=>false,'list_order'=>false));
//  }
  
  if ( checkOrderAcces ('orderAdviesNotificatie') > 0 ) {
    $list->addColumn("OrderRegelsV2","mailBevestigingVerzonden",array("search"=>false,'list_order'=>false));
  }
 

  $list->setWhere("OrderRegelsV2.orderid = OrdersV2.id AND OrdersV2.batchId='".$_GET['batchId']."'");

  $list->setOrder( (isset($_GET['sort']) ? $_GET['sort'] : null), (isset($_GET['direction']) ? $_GET['direction'] : null));
  $list->setSearch((isset($_GET['selectie']) ? $_GET['selectie'] : null));
  $list->selectPage((isset($_GET['page']) ? $_GET['page'] : null));

  foreach($list->objects as $objectNaam=>$object)
  {
    foreach($list->objects[$objectNaam]->data['fields'] as $fieldname=>$fieldData)
    {
      if(isset($list->objects[$objectNaam]->data['fields'][$fieldname]['list_search']))
      {
        unset($list->objects[$objectNaam]->data['fields'][$fieldname]['list_search']);
      }
    }
  }

  echo template($__appvar["templateContentHeader"],$content);

  echo '<table id="dataTable"  class="table table-boxed table-striped table-hover " >';
  echo $list->printHeader();
  $list->editScript='ordersEditV2.php';
  echo '<tbody>';
  $totaalAantal = 0;

  $rekeningObj = new Rekeningen();
  $aeNumber = new AE_Numbers();
  $totaalBedrag = 0;
  $fixdata = array('fix' => 0, 'nofix' => 0);
  $adviseSend = array('noadvise' => 0, 'notsend' => 0, 'send' => 0, 'notsendFix' => 0, 'sendFix' => 0);
  $orderLocked = 0;
  $orderValidatieFouten = 0;
  
  
  /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
  $portefeuilleObject = new Portefeuilles ();
  $portefeuilleData = $portefeuilleObject->parseBySearch(
    array('Portefeuille' => $thisPortefeuille),
    array('Vermogensbeheerder', 'Depotbank')
  );

  $vermogensbeheerderObj = new Vermogensbeheerder ();
  $vermogensbeheerderData = $vermogensbeheerderObj->parseBySearch(
    array('vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder']),
    array('OrderuitvoerBewaarder')
  );
  
  $orderuitvoerBewaarder = $vermogensbeheerderData['OrderuitvoerBewaarder'];
  
  
  
  while($data = $list->getRow())
  {
  
    $fixTonen = 0;
    /** rmtest */
  
    /** ophalen vermogensbeheerder voor geselecteerde Portefeuilles **/
    $portefeuilleObject = new Portefeuilles ();
    $portefeuilleData = $portefeuilleObject->parseBySearch(
      array('Portefeuille' => $data['OrderRegelsV2.portefeuille']['value']),
      array('Vermogensbeheerder', 'Depotbank')
    );

    if ( $orderuitvoerBewaarder == 1 ) {
    /** ophalen rekeningDepotbank **/
    $rekeningenObj = new Rekeningen ();
    $rekeningData = $rekeningenObj->parseBySearch(
      array('Rekening' => $data['OrderRegelsV2.rekening']['value'], 'Portefeuille' => $data['OrderRegelsV2.portefeuille']['value']),
      array('Depotbank')
    );
    $fixDepotbankObj = new FixDepotbankenPerVermogensbeheerder ();
    $fixDepotbankData = $fixDepotbankObj->parseBySearch(
      array('Vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder'], 'depotbank' => $rekeningData['Depotbank'])
      ,array('fixDefaultAan', 'meervoudigViaFix', 'nominaalViaFix', 'meervNominaalFIX', 'careOrderVerplicht')
    );
    if ( ! empty($fixDepotbankData) ) {
      $fixTonen = 1;
    }

    } else {
      $fixDepotbankenPerVermogensbeheerderObj = new FixDepotbankenPerVermogensbeheerder();
      $fixDepotbankenPerVermogensbeheerderData = $fixDepotbankenPerVermogensbeheerderObj->parseBySearch( array('depotbank'=> $portefeuilleData['Depotbank'],'vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder']));
      
      if (!empty ($fixDepotbankenPerVermogensbeheerderData)) {
        $rekeningNrTonen = $fixDepotbankenPerVermogensbeheerderData['rekeningNrTonen'];
      }
  
      if (isset($fixDepotbankenPerVermogensbeheerderData['depotbank'])) {
        $fixTonen = 1;
      }
  
    }
  
    
    
    
    
    
    
    $adviesRelatie = adviesRelatieCheck($data['OrderRegelsV2.portefeuille']['value']);

    if ( $adviesRelatie === true && checkOrderAcces ('orderAdviesNotificatie') > 0 ) {
      if ( $data['OrderRegelsV2.mailBevestigingVerzonden']['value'] === '0000-00-00 00:00:00' && ( checkOrderAcces ('orderAdviesNotificatie') != 5 && checkOrderAcces ('orderAdviesNotificatie') != 0 ) ) {
        $adviseSend['notsend'] += 1;
        if ( $data['OrdersV2.fixOrder']['value'] == 1 && ($data['OrderRegelsV2.orderregelStatus']['value'] == 0 || $data['OrderRegelsV2.orderregelStatus']['value'] == -1) ) {$adviseSend['notsendFix'] += 1;}
      } else {
        $adviseSend['send'] += 1;
        if ( $data['OrdersV2.fixOrder']['value'] == 1 && ($data['OrderRegelsV2.orderregelStatus']['value'] == 0 || $data['OrderRegelsV2.orderregelStatus']['value'] == -1) ) {$adviseSend['sendFix'] += 1;}
      }

      $data['OrderRegelsV2.mailBevestigingVerzonden']['form_type'] = 'checkbox';
      $data['OrderRegelsV2.mailBevestigingVerzonden']['db_type'] = 'tinyint';
      $data['OrderRegelsV2.mailBevestigingVerzonden']['value'] = ($data['OrderRegelsV2.mailBevestigingVerzonden']['value'] === '0000-00-00 00:00:00' ? "0":1);
    } else {
      $data['OrderRegelsV2.mailBevestigingVerzonden']['form_type'] = 'text';
      $data['OrderRegelsV2.mailBevestigingVerzonden']['value'] = 'N.v.t.';
      $adviseSend['noadvise'] += 1;
    }


    $fixdata['status'][$data['OrderRegelsV2.orderregelStatus']['value']] += 1;
    if ( $data['OrdersV2.fixOrder']['value'] == 1 && ($data['OrderRegelsV2.orderregelStatus']['value'] == 0 || $data['OrderRegelsV2.orderregelStatus']['value'] == -1) ) {$fixdata['fix'] += 1;} else {$fixdata['nofix'] += 1;}

    $rekeningValuta = $rekeningObj->parseBySearch(array('Rekening' => $data['OrderRegelsV2.rekening']['value']), 'Valuta');
    $rekeningValutaKoers = 1;
    if ( $rekeningValuta != 'EUR' ) {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $rekeningValuta . "' AND Valutakoersen.datum <= '" . formdate2db(dbdate2form($data['OrdersV2.add_date']['value'])) . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
      $db->executeQuery($query);
      $rekeningValutaKoersData = $db->NextRecord();
      $rekeningValutaKoers = $rekeningValutaKoersData['Koers'];
    }
    
//debug($data);
    $db = new DB();
    $query = "SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid, Fondskoersen.datum FROM Fondsen  LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds AND Fondskoersen.datum <=  '" . formdate2db(dbdate2form($data['OrdersV2.add_date']['value'])) . "' WHERE Fondsen.Fonds = '" . $data['OrdersV2.fonds']['value'] . "' ORDER BY Fondskoersen.datum DESC LIMIT 1";
    $db->executeQuery($query);
    $fondskoers = $db->nextRecord();
    $fondsValuta = $fondskoers['Valuta'];

    $fondsValutaKoers = 1;
    if ( $fondsValuta != 'EUR' ) {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $fondsValuta . "' AND Valutakoersen.datum <= '" . formdate2db(dbdate2form($data['OrdersV2.add_date']['value'])) . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
      $db->executeQuery($query);
      $fondsValutaKoersData = $db->NextRecord();
      $fondsValutaKoers = $fondsValutaKoersData['Koers'];
    }

    $berekenFondsKoers = $data['OrdersV2.koersLimiet']['value'];
    if ( intval($data['OrdersV2.koersLimiet']['value']) == 0 ) {
      $berekenFondsKoers = $fondskoers['Koers'];
    }

    $indicatieBedrag = $fondskoers['Fondseenheid'] * $fondsValutaKoers * $berekenFondsKoers * $data['OrderRegelsV2.aantal']['value'];
    if (in_array($data['OrdersV2.transactieSoort']['value'], array('A', 'AO', 'AS', 'I'))) {
      $indicatieBedrag = -abs($indicatieBedrag);
    }

    $totaalBedrag += $indicatieBedrag;
    $data['.bedrag']['value'] = number_format($indicatieBedrag, 2, ',', '.');

    $data['OrderRegelsV2.aantal']['list_format'] = null;
    $data['OrderRegelsV2.aantal']['list_numberformat'] = null;
    // value='" . $data['OrderRegelsV2.aantal']['value'] . "'
    $data['OrderRegelsV2.aantal']['list_nobreak'] = true;
    $data['OrderRegelsV2.aantal']['noClick'] = true;

    if ($data["OrderRegelsV2.controleStatus"]["value"] == 1)
      $data["tr_class"] = "list_dataregel_geel";
    elseif ($data["OrderRegelsV2.controleStatus"]["value"] == 2)
      $data["tr_class"] = "list_dataregel_rood";
  
    // Bij een fix order de controle fouten optellen
    if ( $data['OrdersV2.fixOrder']['value'] == 1 ) {
      $orderValidatieFouten += (int) $data["OrderRegelsV2.controleStatus"]["value"];
    }
    
    $list->fullEditScript="ordersEditV2.php?action=edit&orderregelId=".$data['id']['value']."&id=".$data['OrderRegelsV2.orderid']['value']."&batchId=".$_GET['batchId'];
    $data['OrdersV2.transactieType']['value']=$__ORDERvar["transactieType"][$data['OrdersV2.transactieType']['value']];
    $data['OrdersV2.transactieSoort']['value']=$__ORDERvar["transactieSoort"][$data['OrdersV2.transactieSoort']['value']];


    unset($data['OrderRegelsV2.aantal']['list_format']);
    unset($data['OrderRegelsV2.aantal']['list_numberformat']);
    $data['OrderRegelsV2.aantal']['value'] = $aeNumber->viewFormatMaxDecimals($data['OrderRegelsV2.aantal']['value'], 6);
  
  
    /** Controleer of de order  opnieuw is ingelegd */
    if ( (int) $data['OrderRegelsV2.orderregelStatus']['value'] === 7  ) { //'geweigerd'
      if ( ! empty ($data['OrderRegelsV2.kopieOrderId']['value'])  )
      {
        $data["tr_class"] = "list_dataregel_blauw_force";
      }
    }
    if ($data["OrderRegelsV2.controleStatus"]["value"] == 2 || $data["OrderRegelsV2.controleStatus"]["value"] == 1)
      $data["tr_class"] = "list_dataregel_rood_force";
  

    /** Order Lock controleren */
    $db=new DB();
    $query="SELECT user,change_date FROM tableLocks WHERE `table`='OrdersV2' AND `user` <> '".$USR."' AND tableId='".$data['OrderRegelsV2.orderid']['value']."'";
    
    if($db->QRecords($query) > 0) {
      $orderLocked++;
    }
    $isFixOrder = $data['OrdersV2.fixOrder']['value'];
    // Combinatie order fix checkbox aanpassen zodat deze aan en uitgezet kan worden.
    $data['OrdersV2.fixOrder']['form_type'] = 'text';
  
    if ( (int) $data['OrdersV2.fixOrder']['value'] === 1 ) {
      $data['OrdersV2.fixOrder']['value'] = '<i class="fa fa-lg fa-check-square-o" style="color:black;" aria-hidden="true"></i>';
    } else {
      $data['OrdersV2.fixOrder']['value'] = '<i class="fa fa-lg fa-square-o" style="color:black;" aria-hidden="true"></i>';
    }

    
    
    if ( ($data['OrderRegelsV2.orderregelStatus']['value'] == 0 || $data['OrderRegelsV2.orderregelStatus']['value'] == -1) ) {
      if ( $fixTonen === 1 ) {
        $data['OrdersV2.fixOrder']['value'] .= '&nbsp;	&nbsp;	&nbsp;	&nbsp;<span data-canfix="' . $fixTonen . '" data-fix="' . $isFixOrder . '" data-orderid="' . $data['OrderRegelsV2.orderid']['value'] . '" class="switchFix fixNietNodig btn-new btn-xxs btn-default"><i class="fa fa-refresh" aria-hidden="true"></i></span>';
      }
      $data['OrdersV2.fixOrder']['noClick'] = true;
    }
    
  
    
    echo $list->buildRow($data);
  }

  $statusFix = '';
  $statusFixTxt = '';
  foreach ( $fixdata['status'] as $status => $val ) {
    $statusFix .= '<input type="hidden" data-status="' . $__ORDERvar["orderStatus"][$status] . '" data-statusId="' . $status . '" 
    name="ordersstatusCount-' . $status . '" id="totalFixOrders-' . $status . '" value="'.$val.'" /> ';
    $statusFixTxt .= 'Aantal '. $__ORDERvar["orderStatus"][$status] . ' (' . $val . ')<br />';
  }

  $adviesColumn = '';
  if ( $adviesRelatie === true && ( checkOrderAcces ('orderAdviesNotificatie') != 5 && checkOrderAcces ('orderAdviesNotificatie') != 0 ) ) {
    $adviesColumn = '<td>
    
    <input type="hidden" name="totalAdviserders" id="totalAdviserders" value="'.($adviseSend['send'] + $adviseSend['notsend']).'" /> 
      <input type="hidden" name="adviseNotSend" id="adviseNotSend" value="'.$adviseSend['notsend'].'" /> 
      
      <input type="hidden" name="notsendFix" id="notsendFix" value="'.$adviseSend['notsendFix'].'" /> 
      <input type="hidden" name="sendFix" id="sendFix" value="'.$adviseSend['sendFix'].'" /> 
      
      
      '.$adviseSend['send'].'/'.($adviseSend['send'] + $adviseSend['notsend']).'
    
    </td>';
  }
echo '</tbody>
    <tfoot>
    <tr class="list_dataregel">
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td style="padding-right: 6px;" class=" textR">'.number_format($totaalBedrag, 2, ',', '.').'</td>
      <td></td>
      <td>
      <span id="orderStatusCounter" style="display:none">' . $statusFix . '</span>
      <span id="orderStatustxt" style="display:none">' . $statusFixTxt . '</span>
      
      <input type="hidden" name="orderLock" id="orderLock" value="'.$orderLocked.'" />
      <input type="hidden" name="orderVierOgenCheck" id="orderVierOgenCheck" value="'. checkOrderAcces('orderVierOgen') . '" />
      <input type="hidden" name="orderValidatieFouten" id="orderValidatieFouten" value="'. $orderValidatieFouten . '" />
      
      <input type="hidden" name="totalFixOrders" id="totalFixOrders" value="'.$fixdata['fix'].'" /> 
      <input type="hidden" name="totalBatchOrders" id="totalBatchOrders" value="'.($fixdata['fix']+$fixdata['nofix']).'" /> 
      '.$fixdata['fix'].'/'.($fixdata['fix']+$fixdata['nofix']).'
      </td>
      <td></td>
      ' . $adviesColumn . '
    </tr>
  </tfoot>
    </table>';


  logAccess();
  if($__debug)
  	echo getdebuginfo();
  echo template($__appvar["templateRefreshFooter"],$content);

}

/** @todo welke orders zijn dit? **/
/** @todo is dit vanuit crm **/
elseif ($_GET["rel_id"] <> "")
{
  echo $editcontentNieuw['style'];
  echo $editcontent['style'];
  
  $content['pageHeader'] = '';
  $list = new MysqlList();
  $list->idField = "id";
  $editScript = "orderregelsEditV2.php";
  $list->editScript = $editScript;
  $list->perPage = $__appvar['rowsPerPage'];

  $db=new DB();
  $query="SELECT portefeuille FROM CRM_naw WHERE id='".$_GET["rel_id"]."'";
  $db->SQL($query);
  $data=$db->lookupRecord();


  $list->addColumn("OrderRegelsV2","id",array("list_width"=>"10","search"=>false));
  $list->addColumn("","versie",array("list_width"=>"25","search"=>false));
  $list->addColumn("","order_id",array("list_width"=>"25","search"=>false));
  $list->addColumn("OrdersV2","add_date",array("list_width"=>"10","search"=>false,"description"=>"datumInvoer"));
  $list->addColumn("OrdersV2","transactieSoort",array("list_width"=>"5","search"=>false,"description"=>"transactieSoort"));
  $list->addColumn("OrdersV2","fondsOmschrijving",array("list_width"=>"150","search"=>false));
  $list->addColumn("OrderRegelsV2","aantal",array("list_width"=>"5","search"=>true));
  $list->addColumn("OrderRegelsV2","portefeuille",array("list_width"=>"100","search"=>true, "list_invisible"=>true));
  //$list->addColumn("","valuta",array("list_width"=>"70","search"=>true));
  $list->addColumn("OrderRegelsV2","orderregelStatus",array("list_width"=>"10","search"=>false));
  $list->addColumn("","uitvoeringsprijs",array("list_width"=>"10","search"=>false,"list_numberformat"=>4,"list_align"=>"right", "list_invisible"=>true));
  $list->addColumn("OrdersV2","memo",array("list_width"=>"10","search"=>false, "list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","controleStatus",array("list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","orderid",array("list_invisible"=>true,"list_width"=>"10","search"=>false,"description"=>"kenmerk"));


  if ( checkOrderAcces ('orderAdviesNotificatie') > 0 ) {
    $list->addColumn("OrderRegelsV2","mailBevestigingData",array("search"=>false,'list_order'=>false,"description"=>"Bevestigings mail","list_invisible"=>true));
    $list->addColumn("OrderRegelsV2","mailBevestigingVerzonden",array("search"=>false,'list_order'=>false,"description"=>"Bevestigings mail"));
  }


 //OrdersV2.fondsValuta,  //OrderRegels.valuta,
  $list->forceSelect="
SELECT * FROM (
(
 SELECT OrderRegelsV2.id, 2 as versie,  OrdersV2.add_date, OrdersV2.id as order_id, OrdersV2.transactieSoort, OrdersV2.fondsOmschrijving,  OrderRegelsV2.aantal,
 OrderRegelsV2.portefeuille, OrderRegelsV2.orderregelStatus, OrdersV2.memo, OrderRegelsV2.controleStatus ,OrderRegelsV2.orderid,
 OrderRegelsV2.mailBevestigingData, OrderRegelsV2.mailBevestigingVerzonden
 FROM OrderRegelsV2 
 JOIN OrdersV2 ON OrdersV2.id=OrderRegelsV2.orderid 
 WHERE 
 OrderRegelsV2.portefeuille='".$data['portefeuille']."'
 )
UNION
  (
  SELECT OrderRegels.id, 1 as versie,  Orders.add_date, OrderRegels.id as order_id, Orders.transactieSoort, Orders.fondsOmschrijving, OrderRegels.aantal,
 OrderRegels.portefeuille, OrderRegels.status as orderregelStatus, OrderRegels.memo, OrderRegels.controle as controleStatus ,OrderRegels.orderid,OrderRegels.orderid,OrderRegels.orderid
 
 FROM OrderRegels JOIN Orders ON Orders.orderid=OrderRegels.orderid WHERE 1 AND 
 OrderRegels.portefeuille='".$data['portefeuille']."'
  )
) as tmp
";
$list->forceFrom="";
$list->noTables=true;

  if(!isset($_GET['sort']))
  {
    $_GET['sort'][]      = "tmp.add_date";
    $_GET['direction'][] = "DESC";
  }
  $list->setOrder($_GET['sort'],$_GET['direction']);
  $list->setSearch($_GET['selectie']);
  $list->selectPage($_GET['page']);



  $_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],false));
  $_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
  echo template($__appvar["templateContentHeader"],$content);
  ?><table class="list_tabel" cellspacing="0">
  <?=$list->printHeader(true);?>
  <?php
  while($data = $list->getRow())
  {
//    $extraInfo = '';
    $orderLogInfo = '';
    $orderData = $orderObj->parseById($data['order_id']['value']);
    $orderRegelData = $orderRegelsObj->parseById($data['id']['value']);
    $orderLogDatas = $orderLogs->getForOrder($data['order_id']['value']);
//    $uitvoeringen->getUitvoeringen();
    
    
    foreach ( $orderLogDatas as $orderLogData ) {
      $orderLogInfo .= date('d-m-Y H:i:s', strtotime($orderLogData['change_date'])) . '/' . $orderLogData['add_user'] . ' - ' . $orderLogData['message'] . "\n";
    }
  
  
  
    $db=new DB();
    $query="SELECT * FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid='".$data['order_id']['value']."'";
    $db->SQL($query);
    $db->Query();
    $uitvoeringen=array();
    $totaalUitvoeringen=0;
    while ($dataUit = $db->nextRecord())
    {
      $uitvoeringen[]=$dataUit;
      $totaalUitvoeringen+=$dataUit['uitvoeringsAantal'];
    }
    $query="SELECT SUM(OrderRegelsV2.aantal) as aantal FROM OrderRegelsV2 WHERE OrderRegelsV2.orderid='".$airsOrderId."'";
    $db->SQL($query);
    $db->Query();
    $dataOrd = $db->nextRecord();
    $orderegelTotaal=$dataOrd['aantal'];
    
//    debug($orderRegelData, $data['order_id']['value'] . '----' . $data['id']['value']);
//    debug($orderData, $data['order_id']['value'] . '----' . $data['id']['value']);
//    debug($uitvoeringen, $data['order_id']['value'] . '----' . $data['id']['value']);
  
    $orderIndenfiticatie = '';
    $orderRegelIdentificatie = '';
    if ( $orderData['orderSoort'] === 'M' || $orderData['orderSoort'] === 'O' )
    {
      $positie = $orderRegelData['positie'];
      $orderRegelIdentificatie = '-' . sprintf("%03d", $positie);
    }
    $orderIndenfiticatie = $__appvar["bedrijf"] . $orderData['id'] . $orderRegelIdentificatie ;
    
    $mailBevestingData = unserialize($data['mailBevestigingData']['value']);


    if ( $data['mailBevestigingVerzonden']['value'] !== '0000-00-00 00:00:00' ) {
      $mailData .= '
        <div id="viewAdviseMailDialog_' . $data['id']['value'] . '" title="Basic dialog" style="display:none">
          <div class="padded-15">
            <strong>Afzender: </strong>'. $mailBevestingData['senderName'] . '<br />
            <strong>Afzender email: </strong>'. $mailBevestingData['senderEmail'] . '<br />
            <strong>Ontvanger: </strong>'. $mailBevestingData['adviseReceiverName'] . '<br />
            <strong>Ontvanger email: </strong>'. $mailBevestingData['adviseReceiverEmail'] . '<br />
            <strong>Onderwerp: </strong>'. $mailBevestingData['subject'] . '<br />
            <strong>Reden: </strong>'. $mailBevestingData['orderReden'] . '<br />
        
            <br />
            <br />
            <strong>E-mail</strong><br />
            <span>'. $mailBevestingData['body'] . '</span>
            
            <strong>Verzonden op: </strong>'. date('d-m-Y H:i:s', strtotime($data['mailBevestigingVerzonden']['value'])) . '<br />
          </div>
        </div>
      ';
    }

    $adviesVerzonden = '';
    if ( checkOrderAcces ('orderAdviesNotificatie') > 0 ) {
      if ( $data['mailBevestigingVerzonden']['value'] !== '0000-00-00 00:00:00' ) {
        if ( $data['mailBevestigingData']['value'] === 'ignored ' ) {
          $adviesVerzonden = 'Mail genegeerd';
        } else {
          $adviesVerzonden = '<i data-modelid="viewAdviseMailDialog_' . $data['id']['value'] . '" title="Mail bekijken" class="openModel fa fa-envelope-o" aria-hidden="true"></i>';
        }

      }
    }
  
    $data['versie']['value'] = '<span class="openDiag" data-id="' . $data['id']['value'] . '"  id="openDiag_'.$data['id']['value'].'"><i class="fa fa-eye" aria-hidden="true"></i></span>
		 
		 
		 ' . $data['versie']['value'];
    //<div class="dialogBox" id="openDiag_' . $data['id']['value'] . '_box">' . $extraInfo . '</div>

    $data['mailBevestigingVerzonden']['form_type'] = 'text';
    $data['mailBevestigingVerzonden']['value'] = $adviesVerzonden;

    $data['disableEdit']=true;

    $data['orderregelStatus']['value']= $__ORDERvar['laatsteStatus'][$data['orderregelStatus']['value']];
    $data["transactieSoort"]["value"] = $__ORDERvar['transactieSoort'][$data["transactieSoort"]["value"]];
    $query="SELECT uitvoeringsAantal,uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE orderid='".$data['order_id']['value']."' ";
    $db->SQL($query);
    $db->Query();
    $uitvoeringen=array();
    while($uitvoering=$db->nextRecord())
    {
      $uitvoeringen['aantal']+=$uitvoering['uitvoeringsAantal'];
      $uitvoeringen['waarde']+=$uitvoering['uitvoeringsPrijs']*$uitvoering['uitvoeringsAantal'];
    }
    $data['uitvoeringsprijs']['value']=$uitvoeringen['waarde']/$uitvoeringen['aantal'];
	  echo $list->buildRow($data);
  }
?>
</table>
  
  
  
  
  
  <!-- Modal -->
  <div class="modal fade" id="dialogBox" tabindex="-1" role="dialog" aria-labelledby="dialogBox" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="exampleModalLabel">Overzicht</h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
        </div>
      </div>
    </div>
  </div>
  
  
  <script>
    $(function() {
      
      $(".openDiag").click(function(e) {
        $('.modal-body').html('');
        e.preventDefault();
        
        $.ajax({
          url : 'orderregelsListV2.php?orderToOpen=' + $(this).data('id'),
          type: "GET",
          dataType: 'json',
          success:function(data, textStatus, jqXHR) {
            if ( data.success === true ) {
              $('.modal-body').html(data.content);
              $('#dialogBox').modal('show');
            } else {
            
            }
          }
        });
      });
    });
  </script>
  
  
  
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}


  echo $mailData;
  echo '
  <script>
  $( ".openModel" ).on( "click", function() {
      $( "#" + $(this).data("modelid") ).dialog({
      draggable: false,
        modal: true,
        resizable: false,
        width: "auto",
        title: "Order advies",
        minHeight: 150,
      close: function() {
        $(this).dialog( "close" );
      },
      buttons: {
          "Sluiten": function ()
          {

            $(this).dialog(\'destroy\');
          }
        }
    });
    });
    
  </script>
  
  ';


echo template($__appvar["templateRefreshFooter"],$content);
}
else if ($_GET["orderid"] == "" && $_GET['action'] <> 'new')
{

  $list = new MysqlList2();
  $list->editScript = 'orderregelsEditV2.php';
  $list->editScript = 'ordersEditV2.php';
  $list->perPage = 100;

  $list->addFixedField("OrderRegelsV2","orderid",array("list_width"=>"100","search"=>false,'list_invisible'=>true));
  $list->addFixedField("OrderRegelsV2","portefeuille",array("list_width"=>"100","search"=>false));



  $list->categorieVolgorde=array('OrderRegelsV2'=>array("Algemeen"),
                                 'OrdersV2'=>array('Algemeen'),
                                 'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'),
                                 'OrderUitvoeringV2'=>array('Algemeen')  );

  $html = $list->getCustomFields(array('OrderRegelsV2','OrdersV2','Portefeuilles','OrderUitvoeringV2'),"orders");


  $list->ownTables=array('OrderRegelsV2');
  $list->setJoin("LEFT JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
                LEFT JOIN Portefeuilles ON Portefeuilles.Portefeuille = OrderRegelsV2.Portefeuille AND Portefeuilles.consolidatie=0 
                LEFT JOIN OrderUitvoeringV2 ON OrdersV2.id = OrderUitvoeringV2.orderid  ");

  $list->setOrder($_GET['sort'],$_GET['direction']);
  $list->setSearch($_GET['selectie']);
  $list->selectPage($_GET['page']);

  $_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
  $_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $list->perPage ,false));
  $_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

  echo template($__appvar["templateContentHeader"],$editcontent);
  ?>
  <br>
  <?=$list->filterHeader();?>
  <table class="list_tabel" >
    <?=$list->printHeader();?>
    <?
    $_SESSION[submenu] = New Submenu();
    $_SESSION[submenu]->addItem("<br>","");
    $_SESSION[submenu]->addItem($html,"");
    $list->idField='OrderRegelsV2.orderid';
    while($data = $list->getRow())
    {

      $data['OrdersV2.orderStatus']['value']=$data['OrdersV2.orderStatus']['form_options'][$data['OrdersV2.orderStatus']['value']];



      echo $list->buildRow($data);
    }
    logAccess();
    ?>
  </table>

  <?
  if($__debug)
  {
    echo getdebuginfo();
  }
  echo template($__appvar["templateRefreshFooter"],$editcontent);

}
/** Meervoudige orders **/
else
{
  /** haal order op voor berekeningen **/
  $ordersV2Obj = new OrdersV2();
  $orderData = $ordersV2Obj->parseById($_GET['orderid']);

  $list->addColumn("OrderRegelsV2","positie",array("list_width"=>"30","list_align"=>"right","search"=>false,"description"=>"pos","list_order"=>false));
  
  if ( $orderData['orderSoort'] !== 'O' ) {
    $list->addColumn("OrderRegelsV2", "aantal", array("list_width" => "50", "search" => false, "list_order" => false));
  }
  $list->addColumn("OrderRegelsV2","portefeuille",array("list_width"=>"100","search"=>false,"list_order"=>false));
  $list->addColumn("OrderRegelsV2","client",array("list_width"=>"","search"=>false,"list_order"=>false));
  $list->addColumn("OrderRegelsV2","rekening",array("list_width"=>"","search"=>false,"list_order"=>false));
  $list->addColumn("OrderRegelsV2","controleStatus",array("list_invisible"=>true));
  $list->addColumn("OrderRegelsV2","kopieOrderId",array("list_invisible"=>true));

  $list->addColumn("OrderRegelsV2","add_date",array("list_invisible"=>true));
  
  if ( $orderData['orderSoort'] === 'O' ) {
    $list->addColumn("OrderRegelsV2","orderaantal",array("list_align"=>"right",'description'=>'Aantal', 'list_visible' => true, "search"=>false, "list_invisible"=>false));
    $list->addColumn("OrderRegelsV2","bedrag",array("list_align"=>"right",'description'=>'Bedrag', 'list_visible' => true, "search"=>false, "list_invisible"=>false));
    $nominaalBedrag = 0;
    $nominaalAantal = 0;
  } else {
    $list->addColumn("","bedrag",array("list_align"=>"right",'description'=>'Geschat bedrag', 'list_visible' => true, "search"=>false, "list_invisible"=>false));
    if($toonNota==true)
      $list->addColumn("OrderRegelsV2","nettoBedrag",array("list_width"=>"150","search"=>false,'list_order'=>false));
  }
  
  $list->addColumn("OrderRegelsV2","orderReden",array("list_width"=>"","search"=>false,"list_order"=>false, 'description' => 'Orderreden'));
  $list->addColumn("OrderRegelsV2","orderregelStatus",array("search"=>false,'list_order'=>false));
  
//  if ( (int) $orderuitvoerBewaarder === 1 ) {
//    $list->addColumn("OrdersV2","depotbank",array("search"=>false,'list_order'=>false));
//  }
  
  $list->addColumn("","",array("list_width"=>"30%", 'list_visible' => true, "search"=>false, "list_invisible"=>false));



  $list->setWhere("orderid= '".$_GET["orderid"]."'");
//  $_GET['sort'][]      = "OrderRegelsV2.positie";
//  $_GET['direction'][] = "DESC";
//  $list->setOrder($_GET['sort'],$_GET['direction']);


  $list->sortOptions[] = array(
    'veldnaam'  => 'OrderRegelsV2.positie',
    'methode'   => 'DESC'
  );


  $list->setSearch($_GET['selectie']);
  $list->selectPage($_GET['page']);

  foreach($list->objects as $objectNaam=>$object)
  {
    foreach($list->objects[$objectNaam]->data['fields'] as $fieldname=>$fieldData)
    {
      if(isset($list->objects[$objectNaam]->data['fields'][$fieldname]['list_search']))
      {
        unset($list->objects[$objectNaam]->data['fields'][$fieldname]['list_search']);
      }
    }
  }

  $content['pageHeader'] = '';

  echo template($__appvar["templateContentHeader"],$content);
//  if ( requestType('ajax')) {echo '<div class="formContent">';}
  echo '<table id="dataTable" class="table table-boxed table-hover" cellspacing="0" style="width:100%; margin-left:0px;">';
  //echo preg_replace('~(<a href="[^"]*">)([^<]*)(</a>)~', '$2', $list->printHeader());
  echo $list->printHeader();
  $totaalAantal = 0;
  echo '<tbody>';

  $aeNumber = new AE_Numbers();
  $rekeningObj = new Rekeningen();
  $totaalNetto=0;
  while($data = $list->getRow())
  {


    $rekeningValuta = $rekeningObj->parseBySearch(array('Rekening' => $data['OrderRegelsV2.rekening']['value']), 'Valuta');
    $rekeningValutaKoers = 1;
    if ( $rekeningValuta != 'EUR' ) {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $rekeningValuta . "' AND Valutakoersen.datum <= '" . formdate2db(dbdate2form($data['OrderRegelsV2.add_date']['value'])) . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
      $db->executeQuery($query);
      $rekeningValutaKoersData = $db->NextRecord();
      $rekeningValutaKoers = $rekeningValutaKoersData['Koers'];
    }

    $db = new DB();
    $query = "SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid, Fondskoersen.datum FROM Fondsen  LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds AND Fondskoersen.datum <=  '" . formdate2db(dbdate2form($data['OrderRegelsV2.add_date']['value'])) . "' WHERE Fondsen.Fonds = '" . $orderData['fonds'] . "' ORDER BY Fondskoersen.datum DESC LIMIT 1";
    $db->executeQuery($query);
    $fondskoers = $db->nextRecord();
    $fondsValuta = $fondskoers['Valuta'];

    $fondsValutaKoers = 1;
    if ( $fondsValuta != 'EUR' ) {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valutakoersen.Valuta = '" . $fondsValuta . "' AND Valutakoersen.datum <= '" . formdate2db(dbdate2form($data['OrderRegelsV2.add_date']['value'])) . "' ORDER BY Valutakoersen.datum DESC LIMIT 1";
      $db->executeQuery($query);
      $fondsValutaKoersData = $db->NextRecord();
      $fondsValutaKoers = $fondsValutaKoersData['Koers'];
    }

    $berekenFondsKoers = $orderData['koersLimiet'];
    if ( intval($orderData['koersLimiet']) == 0 ) {
      $berekenFondsKoers = $fondskoers['Koers'];
    }
//    debug($fondskoers['Fondseenheid'].' * '.$fondsValutaKoers.' * '.$berekenFondsKoers.' * '.$data['OrderRegelsV2.aantal']['value']);
    $indicatieBedrag = $fondskoers['Fondseenheid'] * $fondsValutaKoers * $berekenFondsKoers * $data['OrderRegelsV2.aantal']['value'];
    if (in_array($orderData['transactieSoort'], array('A', 'AO', 'AS', 'I'))) {
      $indicatieBedrag = -abs($indicatieBedrag);
    }

    $data['.bedrag']['value'] = number_format($indicatieBedrag, 2, ',', '.');
    $totaalBedrag += $indicatieBedrag;

    if($toonNota==true)
    {
      if(substr($orderData['transactieSoort'],0,1)=='A')
        $teken=-1;
      else
        $teken=1;
      $totaalNetto+=$data['OrderRegelsV2.nettoBedrag']['value']*$teken;
      $data['OrderRegelsV2.nettoBedrag']['value'] = number_format($data['OrderRegelsV2.nettoBedrag']['value']*$teken, 2, ',', '.');
    }



    $totaalAantal += $data["OrderRegelsV2.aantal"]["value"];
	  $data['disableEdit'] = $disableEdit;
 //   $data["status"]["value"]   = $__ORDERvar['orderStatus'][$data["status"]["value"]];
    if ($data["OrderRegelsV2.controleStatus"]["value"] == 1)
      $data["tr_class"] = "list_dataregel_geel";
    elseif ($data["OrderRegelsV2.controleStatus"]["value"] == 2)
      $data["tr_class"] = "list_dataregel_rood";
    $list->fullEditScript="ordersEditV2.php?action=edit&orderregelId={id}&id=".$_GET["orderid"]."&batchId=".$_GET['batchId'];
    $list->editScript='ordersEditV2.php';
    $data['vulling'] = '';

    unset($data['OrderRegelsV2.aantal']['list_format']);
    unset($data['OrderRegelsV2.aantal']['list_numberformat']);
    $data['OrderRegelsV2.aantal']['value'] = $aeNumber->viewFormatMaxDecimals($data['OrderRegelsV2.aantal']['value'], 6);
  
    /** Controleer of de order  opnieuw is ingelegd */
    if ( (int) $data['OrderRegelsV2.orderregelStatus']['value'] === 7  ) { //'geweigerd'
      if ( ! empty ($data['OrderRegelsV2.kopieOrderId']['value'])  )
      {
        $data["tr_class"] = "list_dataregel_blauw_force";
      }
    }
  
    if ( $orderData['orderSoort'] === 'O' ) {
      $nominaalBedrag += $data['OrderRegelsV2.bedrag']['value'];
      $nominaalAantal += $data['OrderRegelsV2.orderaantal']['value'];
//      $list->addColumn("OrderRegelsV2","orderaantal",array("list_align"=>"right",'description'=>'Aantal', 'list_visible' => true, "search"=>false, "list_invisible"=>false));
//      $list->addColumn("OrderRegelsV2","bedrag",array("list_align"=>"right",'description'=>'Bedrag', 'list_visible' => true, "search"=>false, "list_invisible"=>false));
//    debug($data);
    }
  
    echo $list->buildRow($data);
  }
  $number= new AE_Numbers();
  
  
//  $nominaalBedrag;
//  $nominaalAantal;
  if ( $orderData['orderSoort'] === 'O' )
  {
    $totaalRegel = '
      </tbody>
      <tfoot>
        <tr class="list_dataregel">
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td style="padding-right: 6px;" class=" textR">' . number_format($nominaalAantal, 2, ',', '.') . '</td>
          <td style="padding-right: 6px;" class=" textR">' . number_format($nominaalBedrag, 2, ',', '.') . '</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </tfoot>
      </table>
    ';
  }
  else
  {
    $totaalRegel = '</tbody>
    <tfoot>
    <tr class="list_dataregel">
      <td></td>
      <td></td>
      <td style="padding-right: 6px;" class=" textR">' . $number->viewFormatMaxDecimals($totaalAantal, 6) . '</td>
      <td></td>
      <td></td>
      <td></td>
      <td style="padding-right: 6px;" class=" textR">' . number_format($totaalBedrag, 2, ',', '.') . '</td>';
    if ($toonNota == true)
    {
      $totaalRegel .= '<td style="padding-right: 6px;" class=" textR">' . number_format($totaalNetto, 2, ',', '.') . '</td>';
    }
    $totaalRegel .= '<td></td>
   <td></td>
   <td></td>
    </tr>
  </tfoot>
    </table>';
  }
  echo $totaalRegel;
  logAccess();
  if($__debug)
  	echo getdebuginfo();
  echo template($__appvar["templateRefreshFooter"],$content);
}

//if ( requestType('ajax')) {echo '</div';}
?>