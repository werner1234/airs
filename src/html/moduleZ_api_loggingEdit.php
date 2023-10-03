<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/07 10:11:45 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: moduleZ_api_loggingEdit.php,v $
    Revision 1.1  2018/09/07 10:11:45  cvs
    commit voor robert call 6989

    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$subHeader = "";
$mainHeader    = "moduleZ API logs muteren";



$__funcvar['listurl']  = "moduleZ_api_loggingList.php";
$__funcvar['location'] = "moduleZ_api_loggingEdit.php";

$object = new API_moduleZ_logging();

$outP = new editObject($object);
$outP->__funcvar = $__funcvar;
$outP->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$outP->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$outP->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$outP->usetemplate = true;
$outP->formTemplate = "moduleZ_api_loggingEditTemplate.html";

$outP->controller($action, $data);

$res = (utf8_encode( $object->get("results")));

$res = str_replace("{", "\n\n{", $res);
$res = str_replace("}", "\n}", $res);




$req = (utf8_encode( $object->get("request")));

$req = str_replace("{", "\n\n{", $req);
$req = str_replace("}", "\n}", $req);


$err = (utf8_encode( $object->get("errors")));
$err = str_replace("{", "\n\n{", $err);
$err = str_replace("}", "\n}", $err);



$outP->formVars["request"] = "<pre>$req</pre>";
$outP->formVars["errors"] = "<pre>$err</pre>";
$outP->formVars["results"] = "<pre>$res</pre>";

echo $outP->getOutput();

if ($result = $outP->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $outP->_error;
}


function prettyPrint( $json )
{
  $result = '';
  $level = 0;
  $in_quotes = false;
  $in_escape = false;
  $ends_line_level = NULL;
  $json_length = strlen( $json );

  for( $i = 0; $i < $json_length; $i++ ) {
    $char = $json[$i];
    $new_line_level = NULL;
    $post = "";
    if( $ends_line_level !== NULL ) {
      $new_line_level = $ends_line_level;
      $ends_line_level = NULL;
    }
    if ( $in_escape ) {
      $in_escape = false;
    } else if( $char === '"' ) {
      $in_quotes = !$in_quotes;
    } else if( ! $in_quotes ) {
      switch( $char ) {
        case '}': case ']':
        $level--;
        $ends_line_level = NULL;
        $new_line_level = $level;
        break;

        case '{': case '[':
        $level++;
        case ',':
          $ends_line_level = $level;
          break;

        case ':':
          $post = " ";
          break;

        case " ": case "\t": case "\n": case "\r":
        $char = "";
        $ends_line_level = $new_line_level;
        $new_line_level = NULL;
        break;
      }
    } else if ( $char === '\\' ) {
      $in_escape = true;
    }
    if( $new_line_level !== NULL ) {
      $result .= "\n".str_repeat( "\t", $new_line_level );
    }
    $result .= $char.$post;
  }

  return $result;
}