<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/08 18:23:43 $
  File Versie         : $Revision: 1.6 $

 */
include_once("wwwvars.php");
$AETemplate = new AE_template();

$data = array_merge($_GET, $_POST);/** merge data * */
$action = $data['action'];

$_POST['check_aantal_tt'] = true;

$__funcvar['listurl'] = 'participantenFondsVerloopLiveEdit.php?participanten_id=' . (isset($data['participanten_id']) && is_numeric($data['participanten_id']) ? $data['participanten_id'] : 0);
$__funcvar['location'] = 'participantenFondsVerloopEdit.php';

$participantenFondsVerloop = new ParticipantenFondsVerloop();

$editObject = new editObject($participantenFondsVerloop);

$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
//$editObject->formTemplate = 'participantenFondsVerloopEditTemplate.html';
//$editObject->usetemplate = true;
$editObject->template = $editcontent;

$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editObject->template['style'] .= "<style>
  .maskNumeric6DigitsAllowNegative {
  text-align: right;
  }
</style>";
$editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

  $editObject->template['script_voet'] .= "
    $(document).on('change', '#aantal, #transactietype', function () {
      if ( jQuery.inArray($('#transactietype').val(), ['B', 'A', 'D', 'BK', 'H']) != -1 ) {
        if ( $('#aantal').val() < 0 ) {
          $('#messages').addClass('alert-info');
          $('#messages').html('Aantal dient positief ingevoerd te worden!');
        } else {
          $('#messages').removeClass('alert-info');
          $('#messages').html('');
        }
      } else {
        if ( $('#aantal').val() > 0 ) {
          $('#messages').addClass('alert-info');
          $('#messages').html('Aantal dient negatief ingevoerd te worden!');
        } else {
          $('#messages').removeClass('alert-info');
          $('#messages').html('');
        }
      }
    });

  ";

$editObject->controller($action, $data); //save $data
$_SESSION['nav']->returnUrl = 'participantenFondsVerloopLiveEdit.php?participanten_id='.$participantenFondsVerloop->get('participanten_id');

echo '<div id="messages" class="alert"></div>';
echo $editObject->getOutput();

if ($result = $editObject->result)
{
    header("Location: " . $__funcvar['listurl']);
}
else
{
  echo $_error = $editObject->_error;
}