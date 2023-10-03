<?php


include_once("wwwvars.php");
include_once("../classes/editObject.php");

include_once('rapport/rapportVertaal.php');
include_once("../classes/AE_cls_fpdf.php");
include_once("../config/applicatie_functies.php");
include_once("rapport/PDFRapport.php");
session_start();
$AEDatum = new AE_datum();
$AENumbers = new AE_Numbers();
$AEValidate = new AE_Validate();
$AETemplate = new AE_template();
$customTemplate = new AE_CustomTemplate('factuurLos');

define('EURO', chr(128));
$data = array_merge($_POST, $_GET);
$_SESSION['NAV']='';
$_SESSION['submenu'] = '';
$_SESSION['shortcut'] = '';
define('FPDF_FONTPATH', $__appvar['basedir'] . '/html/font/');
$verzenden = false;

$content['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$content['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

$descriptionEmpty = false;
$categoryEmpty = false;


if ( isset ($data['sub_edos']) ) {
  if ( ! isset($data['description']) || empty ($data['description']) ) {
    $descriptionEmpty = true;
  }
  if ( ! isset($data['categorie']) || empty ($data['categorie']) ) {
    $categoryEmpty = true;
  }
}
$DB = new DB();

if ( isset ($data['items']) && (int) $data['items'] > 0 && $categoryEmpty === false && $descriptionEmpty === false)
{


  if ( isset ($data['sub_save']) ) {
    $_SESSION['factuurLos'][$data['deb_id']] = $data;
  } elseif ( isset ($data['sub_pdf'])  || isset ($data['sub_edos']) || isset ($data['sub_email'])  ) {


  if (isset ($data['sendMails']) && (int)$data['sendMails'] === 1)
  {
    echo '
    <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
    <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  ';

    $verzenden = true;
  }




  $pdf = new PDFRapport('P', 'mm');
  $pdf->Rapportagedatum = date('d-m-Y');
  $pdf->rapport_type = 'FACTUUR';

  loadLayoutSettings($pdf,'',array(),$data['deb_id']);
  
  $pdf->layout = new AE_cls_RapportTemplate($pdf);
  $pdfLayoutSet = false;

  $rapportDir = $__appvar['rapportdir'] . DIRECTORY_SEPARATOR . 'factuurLos' . DIRECTORY_SEPARATOR;

  $layoutFile = 'factuurLos_L' . $pdf->portefeuilledata['Layout'] . '.php';
  if (file_exists($rapportDir . $layoutFile))
  {
    $includeFile = $layoutFile;
  }
  else
  {
    $includeFile = 'factuurLos_Ldefault.php';
  }

  if (file_exists($rapportDir . $includeFile))
  {

    $query = "SELECT
CRM_naw.id,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendAanhef,
Portefeuilles.BetalingsinfoMee,
Portefeuilles.Depotbank,
CRM_naw.Portefeuille,
CRM_naw.CRMGebrNaam,
CRM_naw.wachtwoord
FROM CRM_naw LEFT JOIN Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.id = '" . $data['deb_id'] . "'  ";
    $DB->SQL($query);
    $crmData = $DB->lookupRecord();


    $query="SELECT * FROM Vermogensbeheerders
              Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
    $DB->SQL($query);
    $DB->Query();
    $pdfData['Vermogensbeheerders']=$DB->lookupRecord();
    $pdf->portefeuille = $crmData['Portefeuille'];
    include_once($rapportDir . $includeFile);
  }
  else
  {
    exit($includeFile . 'Geen rapport gevonden');
  }
  }

  if ( ! isset ($data['sub_save']) )
  {
    exit();
  }
} else {

  $checkData = $data;
  unset($checkData['deb_id']);
  if ( isset ($_SESSION['factuurLos'][$data['deb_id']]) && empty($checkData) ) {
    $data = $_SESSION['factuurLos'][$data['deb_id']];
  }

  $DB = new DB();
  $query = "SELECT
CRM_naw.id,
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
Portefeuilles.BetalingsinfoMee,
CRM_naw.Portefeuille,
CRM_naw.CRMGebrNaam,
CRM_naw.wachtwoord
FROM CRM_naw LEFT JOIN Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.id = '" . $data['deb_id'] . "'  ";
  $DB->SQL($query);
  $crmData = $DB->lookupRecord();

  $query="SELECT * FROM Vermogensbeheerders
              Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  $DB->SQL($query);
  $DB->Query();
  $pdfData['Vermogensbeheerders']=$DB->lookupRecord();
}

$docCategorieenQuery = 'SELECT omschrijving,omschrijving FROM (CRM_selectievelden) WHERE module = "docCategrien"';
$DB->SQL($docCategorieenQuery);
$DB->Query();
$docCategorieenSelect = '<option value=""> --- </option>';
while($docCategorieen = $DB->nextRecord()) {
  $docCategorieenSelect .= '<option value="' . $docCategorieen['omschrijving'] . '">' . $docCategorieen['omschrijving'] . '</option>';
}
$content['pageHeader'] = '<br /><div class="formTitle textB"><strong>Factuur</strong></div>';
echo $editcontent['style'];


echo template($__appvar["templateContentHeader"],$content);

?>
<style>
  th {
    text-align: left;
    font-size: 14px;
  }
</style>


<?php

echo '

<div class="formblock">
    <div class="formlinks"><label for="factuurnr">' . vt('Factuur templates') . '</label></div>
    <div class="formrechts">
      <select name="template" id="templateSelect">' . $customTemplate->getTemplateSelect() . '</select>
    </div>
  </div>

';


?>


<form  method="post">


  <div class="formblock">
    <div class="formlinks"><label for="factuurnr"><?= vt('Portefeuille'); ?></label></div>
    <div class="formrechts">
      <?=$crmData['Portefeuille'];?>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks"><label for="factuurnr"><?= vt('Naam'); ?></label></div>
    <div class="formrechts">
      <?=$crmData['naam'];?> <?=$crmData['naam1'];?>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="factuurnr"><?= vt('Factuurnummer'); ?></label></div>
    <div class="formrechts">
      <input type="text" name="factuurnr" value="<?=(isset($data['factuurnr'])?$data['factuurnr']:'')?>">
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="factuurdatum"><?= vt('Factuurdatum'); ?></label></div>
    <div class="formrechts">
      <input type="text" name="factuurdatum" class="AIRSdatepicker" value="<?=(isset($data['factuurdatum'])?$data['factuurdatum']:'')?>">
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="bodyHtml"><?= vt('Onderwerp'); ?></label></div>
    <div class="formrechts">
      <input type="text" style="width: 450px;" name="factuuronderwerp" id="factuuronderwerp" value="<?=(isset($data['factuuronderwerp'])?$data['factuuronderwerp']:'')?>">
    </div>
  </div>
  
  <div class="formblock">
    <div class="formlinks"><label for="fileName"><?= vt('Bestandsnaam'); ?></label></div>
    <div class="formrechts">
      <input type="text" name="fileName" value="<?=(isset($data['fileName'])?$data['fileName']:'')?>">
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="bodyHtml"><?= vt('Tekstblok 1'); ?></label></div>
    <div class="formrechts">
      <textarea rows="10" cols="80" name="tekstblok1" id="tekstblok1"><?=(isset($data['tekstblok1'])?$data['tekstblok1']:'')?></textarea>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks"><label for="bodyHtml"><?= vt('Tekstblok 2'); ?></label></div>
    <div class="formrechts">
      <textarea rows="10" cols="80" name="tekstblok2" id="tekstblok2"><?=(isset($data['tekstblok2'])?$data['tekstblok2']:'')?></textarea>
    </div>
  </div>



<table style="margin-top: 30px;
    display: inline-table;" class="dd" width="60%" id="data">
  <thead>
    <tr>
      <th><?= vt('Omschrijving'); ?></th>
      <th><?= vt('Bedrag'); ?></th>
      <th><?= vt('BTW perc'); ?></th>
      <th><?= vt('BTW Bedrag'); ?></th>
      <th><?= vt('Totaal'); ?></th>
    </tr>
  </thead>

  <tr>
    <td><input class="ond1" id="ond1" type="text" style="width: 400px;" name="factuur[1][ond]" value="<?=(isset($data['factuur'][1]['ond'])?$data['factuur'][1]['ond']:'')?>"></td>
    <td><input type="text" style="    width: 100px;" class="bedrag1 maskValuta2digits" name="factuur[1][bedrag]" value="<?=(isset($data['factuur'][1]['ond'])?$data['factuur'][1]['bedrag']:'')?>"></td>
    <td><select name="factuur[1][btw]" class="btw1" id="btw1">
        <option value="">-</option>
        <option value="0" <?=(isset($data['factuur'][1]['btw']) && $data['factuur'][1]['btw'] == 0?'selected="selected"':'')?>>0%</option>
        <option value="6" <?=(isset($data['factuur'][1]['btw']) && $data['factuur'][1]['btw'] == 6?'selected="selected"':'')?>>6%</option>
        <option value="9" <?=(isset($data['factuur'][1]['btw']) && $data['factuur'][1]['btw'] == 9?'selected="selected"':'')?>>9%</option>
        <option value="21" <?=(isset($data['factuur'][1]['btw']) && $data['factuur'][1]['btw'] == 21?'selected="selected"':'')?>>21%</option>
      </select><div style="width:100px"></div></td>
    <td><input type="text" style="width: 100px;" class="btwtot1 maskValuta2digits" name="factuur[1][btwtot]" disabled></td>
    <td><input type="text" style="width: 100px;" class="totaal1 maskValuta2digits" name="factuur[1][totaal]" disabled></td>
  </tr>

  <tr>
    <td><input class="ond2" id="ond2" type="text" style="width: 400px;" name="factuur[2][ond]" value="<?=(isset($data['factuur'][2]['ond'])?$data['factuur'][2]['ond']:'')?>"></td>
    <td><input type="text" style="width: 100px;" class="bedrag2 maskValuta2digits" name="factuur[2][bedrag]" value="<?=(isset($data['factuur'][2]['ond'])?$data['factuur'][2]['bedrag']:'')?>"></td>
    <td><select name="factuur[2][btw]"  class="btw2"  id="btw1">
        <option value="">-</option>
        <option value="0" <?=(isset($data['factuur'][2]['btw']) && $data['factuur'][2]['btw'] == 0?'selected="selected"':'')?>>0%</option>
        <option value="6" <?=(isset($data['factuur'][2]['btw']) && $data['factuur'][2]['btw'] == 6?'selected="selected"':'')?>>6%</option>
        <option value="9" <?=(isset($data['factuur'][2]['btw']) && $data['factuur'][2]['btw'] == 9?'selected="selected"':'')?>>9%</option>
        <option value="21" <?=(isset($data['factuur'][2]['btw']) && $data['factuur'][2]['btw'] == 21?'selected="selected"':'')?>>21%</option>
      </select></td>
    <td><input type="text" style="width: 100px;" class="btwtot2 maskValuta2digits" name="factuur[2][btwtot]" disabled></td>
    <td><input type="text" style="width: 100px;" class="totaal2 maskValuta2digits" name="factuur[2][totaal]" disabled></td>
  </tr>

  <tr>
    <td><input class="ond3" id="ond3" type="text" style="width: 400px;" name="factuur[3][ond]" value="<?=(isset($data['factuur'][3]['ond'])?$data['factuur'][3]['ond']:'')?>"></td>
    <td><input type="text"  style="    width: 100px;" class="bedrag3 maskValuta2digits" name="factuur[3][bedrag]" value="<?=(isset($data['factuur'][3]['ond'])?$data['factuur'][3]['bedrag']:'')?>"></td>
    <td><select name="factuur[3][btw]"  class="btw3" id="btw1">
        <option value="">-</option>
        <option value="0" <?=(isset($data['factuur'][3]['btw']) && $data['factuur'][3]['btw'] == 0?'selected="selected"':'')?>>0%</option>
        <option value="6" <?=(isset($data['factuur'][3]['btw']) && $data['factuur'][3]['btw'] == 6?'selected="selected"':'')?>>6%</option>
        <option value="9" <?=(isset($data['factuur'][3]['btw']) && $data['factuur'][3]['btw'] == 9?'selected="selected"':'')?>>9%</option>
        <option value="21" <?=(isset($data['factuur'][3]['btw']) && $data['factuur'][3]['btw'] == 21?'selected="selected"':'')?>>21%</option>
      </select></td>
    <td><input type="text" style="width: 100px;" class="btwtot3 maskValuta2digits" name="factuur[3][btwtot]" disabled></td>
    <td><input type="text" style="width: 100px;" class="totaal3 maskValuta2digits" name="factuur[3][totaal]" disabled></td>
  </tr>


</table>

  <style>
    .totTable td {
      font-size: 16px;
    }
  </style>
<br />
<br />
<br />
<table class="totTable" style="margin-left: 615px;
    width: 203px;">

  <tr class="btw6TotDisplay">
    <td><strong>Btw 6%</strong></td>
    <td  style="text-align: right"  class="btw6"></td>
  </tr>
  
  <tr class="btw9TotDisplay">
    <td><strong>Btw 9%</strong></td>
    <td  style="text-align: right"  class="btw9"></td>
  </tr>

  <tr class="btw21TotDisplay">
    <td><strong>Btw 21%</strong></td>
    <td style="text-align: right" class="btw21"></td>
  </tr>

  <tr>
    <td><strong>Totaal</strong></td>
    <td style="text-align: right" class="totIncl"></td>
  </tr>

<!--  <tr>-->
<!--    <td>Totaal</td>-->
<!--    <td class="totbedr"></td>-->
<!--  </tr>-->
</table>


  <input type="hidden" id="items" name="items" value="1" />
  <input type="hidden" id="test" name="test" value="" />
  <input type="hidden" id="deb_id" name="deb_id" value="<?=$data['deb_id'];?>" />

  <br />
  <br />
  <br /> <br />
  <div class="formMessage" ></div>
  <br />
  <fieldset style="height:100px;width:400px; display: inline;    float: left;">
    <legend><strong>&nbsp</strong></legend>
    
    <div style="height: 20px;"></div>
    <input type="hidden" name="blanc" value="0" >
  
    <input type="radio" name="pdfType" value="mail" checked> Layout e-mail
    <input type="radio" name="pdfType" value="paper"> Layout Briefpapier <br>
    
    
    <div style="height: 15px;"></div>
    <input class="submitBtn" name="sub_pdf" type="submit" formtarget="_blank" value="Pdf"/>
    <input class="submitBtn" name="sub_save" type="submit"  value="Tijdelijk opslaan"/>
    <input class="submitBtn" name="sub_email" type="submit"  value="Naar eMail wachtrij"/>
    </fieldset>

  <fieldset style="height:100px;width:500px; display: inline; float:right;">
    <legend><strong>eDossier</strong></legend>
    <div class="formblock">
      <div class="formlinks"><label for="description">Omschrijving</label></div>
      <div class="formrechts">
        <input type="text" style="width: 150px;" name="description" value="<?=(isset($data['description'])?$data['description']:'')?>">
      </div>
      <?=($descriptionEmpty===true?'<div style="    float: right;
    margin-right: 100px;
    padding: 4px 15px;    margin-bottom: 0px;" class="alert alert-error">Mag niet leeg zijn</div>':'')?>
    </div>
    
    <div class="formblock">
      <div class="formlinks"><label for="bodyHtml">Categorie</label></div>
      <div class="formrechts">
        <select type="text" style="width: 150px;" name="categorie"><?=$docCategorieenSelect;?></select>
      </div><?=($categoryEmpty===true?'<div style="    float: right;
    margin-right: 100px;
    padding: 4px 15px;    margin-bottom: 0px;" class="alert alert-error ">Mag niet leeg zijn</div>':'')?>
    </div>

    <input class="submitBtn" name="sub_edos" type="submit"  value="naar eDossier"/>


  </fieldset>

  <br />
  <br />
  <br />
  
  
  


</form>
<script type='text/javascript'>
  
  
  <?=$customTemplate->getTemplateSelectAjax();?>
  
  
  function totalChange () {
    $btw0 = 0;
    $btw6 = 0;
    $btw9 = 0;
    $btw21 = 0;

    $bedrag1 = 0;
    $bedrag2 = 0;
    $bedrag9 = 0;
    $bedrag3 = 0;

    if ( $('.bedrag1').val() != '' &&  $('.btw1').val() != '') {
      if ( $('.btw1').val() == 0 ) {
        $btw0 = $btw0 + parseFloat($('.bedrag1').val() + 0);
      } else if ( $('.btw1').val() == 6 ) {
        $btw6 = $btw6 + ( ($('.bedrag1').val() / 100 ) * 6 );
      } else if ( $('.btw1').val() == 9 ) {
        $btw9 = $btw9 + ( ($('.bedrag1').val() / 100 ) * 9 );
      } else if ($('.btw1').val() == 21 ) {
        $btw21 = $btw21 + ( ($('.bedrag1').val() / 100 ) * 21 );
      }
  
      $( ".ond1" ).addClass( "label-danger" );
      
      $bedrag1 = $('.bedrag1').val();
    }

    if ( $('.bedrag2').val() != '' &&  $('.btw2').val() != '') {
      if ( $('.btw2').val() == 0 ) {
        $btw0 = $btw0 + parseFloat($('.bedrag2').val() + 0);
      } else if ( $('.btw2').val() == 6 ) {
        $btw6 = $btw6 + ( ($('.bedrag2').val() / 100 ) * 6 );
      } else if ( $('.btw2').val() == 9 ) {
        $btw9 = $btw9 + ( ($('.bedrag2').val() / 100 ) * 9 );
      } else if ($('.btw2').val() == 21 ) {
        $btw21 = $btw21 + ( ($('.bedrag2').val() / 100 ) * 21 );
      }
      
      $bedrag2 = $('.bedrag2').val();
    }

    if ( $('.bedrag3').val() != '' &&  $('.btw3').val() != '') {
      if ( $('.btw3').val() == 0 ) {
        $btw0 = $btw0 + parseFloat($('.bedrag3').val() + 0);
      } else if ( $('.btw3').val() == 6 ) {
        $btw6 = $btw6 + ( ($('.bedrag3').val() / 100 ) * 6 );
      } else if ( $('.btw3').val() == 9 ) {
        $btw9 = $btw9 + ( ($('.bedrag3').val() / 100 ) * 9 );
      } else if ($('.btw3').val() == 21 ) {
        $btw21 = $btw21 + ( ($('.bedrag3').val() / 100 ) * 21 );
      }
      
      $bedrag3 = $('.bedrag3').val();
    }


    if ( $btw0 > 0 ) {
      $('.btw0').html($btw0.toFixed(2));
      $('.btw0TotDisplay').show();
    } else {
      $('.btw0').html(0);
      $('.btw0TotDisplay').hide();
    }
    
    if ( $btw6 > 0 ) {
      $('.btw6').html($btw6.toFixed(2));
      $('.btw6TotDisplay').show();
    } else {
      $('.btw6').html(0);
      $('.btw6TotDisplay').hide();
    }
  
    if ( $btw9 > 0 ) {
      $('.btw9').html($btw9.toFixed(2));
      $('.btw9TotDisplay').show();
    } else {
      $('.btw9').html(0);
      $('.btw9TotDisplay').hide();
    }
    
    if ( $btw21 > 0 ) {
      $('.btw21').html($btw21.toFixed(2));
      $('.btw21TotDisplay').show();
    } else {
      $('.btw21').html(0);
      $('.btw21TotDisplay').hide();
    }

    $totaal = parseFloat($bedrag1) + parseFloat($bedrag2) + parseFloat($bedrag3) + parseFloat($btw6) + parseFloat($btw9) + parseFloat($btw21);

    $('.totIncl').html($totaal.toFixed(2));

  }

  function bedrag($veld) {

    $bedragVeld = '.bedrag' + $veld;
    $btwTotVeld = '.btwtot' + $veld;
    $btwVeld = '.btw' + $veld;
    $totaalVeld = '.totaal' + $veld;

    if ( $($bedragVeld).val() == '0.00' ) {
      $($bedragVeld).val('');
    }

    if ( $($bedragVeld).val() != '' &&  $($btwVeld).val() != '') {
      $totaal = 0;
      if ( $($btwVeld).val() == 0 ) {
        $totaal = parseFloat($($bedragVeld).val() + 0);
      } else if ( $($btwVeld).val() == 6 ) {
        $totaal = $($bedragVeld).val() * 1.06;
      } else if ( $($btwVeld).val() == 9 ) {
        $totaal = $($bedragVeld).val() * 1.09;
      } else if ($($btwVeld).val() == 21 ) {
        $totaal = $($bedragVeld).val() * 1.21;
      }

      $($totaalVeld).val( $totaal.toFixed(2));

      if ( $($btwVeld).val() == 0 ) {
        $btwtot = 0;
      } else {
        $btwtot = $totaal - $($bedragVeld).val();
      }

      $($btwTotVeld).val( $btwtot.toFixed(2));
      totalChange ();
    } else {
      $($btwTotVeld).val('');
      $($totaalVeld).val('');
      totalChange ();
    }
  }

  function bedrag2() {
    if ( $('.bedrag2').val() == '0.00' ) {
      $('.bedrag2').val('');
    }

    if ( $('.bedrag2').val() != '' &&  $('.btw2').val() != '') {
      $totaal2 = 0;
      if ( $('.bbtw2tw1').val() == 0 ) {
        $totaal2 = parseFloat($('.bedrag2').val() + 0);
      } if ( $('.btw2').val() == 6 ) {
        $totaal2 = $('.bedrag2').val() * 1.06;
      } else if ($('.btw2').val() == 21 ) {
        $totaal2 = $('.bedrag2').val() * 1.21;
      }
      $('.totaal2').val( $totaal2.toFixed(2));

      if ( $('.btw2').val() == 0 ) {
        $btwtot2 = 0;
      } else {
        $btwtot2 = $totaal2 - $('.bedrag2').val();
      }


      $('.btwtot2').val( $btwtot2.toFixed(2));
      totalChange ();
    } else {
      $('.btwtot2').val('');
      $('.totaal2').val('');
      totalChange ();
    }
  }

  function bedrag3() {
    if ( $('.bedrag3').val() != '' &&  $('.btw3').val() != '') {
      $totaal3 = 0;
      if ( $('.btw3').val() == 6 ) {
        $totaal3 = $('.bedrag3').val() * 1.06;
      } else if ($('.btw3').val() == 21 ) {
        $totaal3 = $('.bedrag3').val() * 1.21;
      }
      $('.totaal3').val( $totaal3.toFixed(2));

      if ( $('.btw3').val() == 0 ) {
        $btwtot3 = 0;
      } else {
        $btwtot3 = $totaal3 - $('.bedrag3').val();
      }


      $('.btwtot3').val( $btwtot3.toFixed(2));
      totalChange ();
    } else {
      $('.btwtot3').val('');
      $('.totaal3').val('');
      totalChange ();
    }
  }


  $(document).ready(function() {

    bedrag(1);
    bedrag(2);
    bedrag(3);
    bedrag(9);
//    bedrag2();
//    bedrag3();
    totalChange ();

    $(document).on('change', '.bedrag1, .btw1', function ()
    {
      bedrag(1);
    });

    $(document).on('change', '.bedrag2, .btw2', function ()
    {
      bedrag(2);
    });
  
    $(document).on('change', '.bedrag9, .btw9', function ()
    {
      bedrag(9);
    });

    $(document).on('change', '.bedrag3, .btw3', function ()
    {
      bedrag(3);
    });

    var currentItem = 1;
    $('#addnew').click(function(){
      currentItem++;
      $('#items').val(currentItem);
      var strToAdd = '<tr><td><input type="text" name="factuur['+currentItem+'][ond]"></td><td><input type="text" name="factuur['+currentItem+'][bedrag]"></td><td><select name="factuur['+currentItem+'][btw]" id="factuur['+currentItem+'][btw]"><option value="6">6%</option><option value="21">21%</option></select></td></tr>';
      $('#data').append(strToAdd);

    });
    
    
    $(document).on('click', '.submitBtn', function (event) {
      $('.formMessage').html(' ')
      var isEmpty = 0;
      
      if ( $('.totaal1').val() !== "" && $('.ond1').val() == "" ) {
        isEmpty = 1;
      }
  
      if ( $('.totaal2').val() !== "" && $('.ond2').val() == "" ) {
        isEmpty = 1;
      }
  
      if ( $('.totaal3').val() !== "" && $('.ond3').val() == "" ) {
        isEmpty = 1;
      }
      
      if ( isEmpty === 1 ) {
        event.preventDefault();
        console.log('Let op, er is geen omschrijving ingevuld');
        $('.formMessage').html('<div class="alert alert-danger alert-error" role="alert">Let op, er is geen omschrijving ingevuld</div>');
      }
    });
  });

</script>


<?php
echo template($__appvar["templateRefreshFooter"],$content);