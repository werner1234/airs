<?php
/*
    AE-ICT CODEX source module versie 1.6, 2 juni 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/08/09 15:03:35 $
    File Versie         : $Revision: 1.3 $

    $Log: orderregelsEditV2.php,v $
    Revision 1.3  2015/08/09 15:03:35  rvv
    *** empty log message ***

    Revision 1.2  2015/08/02 14:25:45  rvv
    *** empty log message ***

    Revision 1.1  2015/06/19 09:14:04  rm
    no message

    Revision 1.38  2014/12/24 09:54:51  cvs
    call 3105

    Revision 1.37  2014/11/30 13:12:00  rvv
    *** empty log message ***

    Revision 1.36  2014/11/08 18:35:29  rvv
    *** empty log message ***

    Revision 1.35  2014/02/08 17:43:33  rvv
    *** empty log message ***

    Revision 1.34  2013/05/26 13:57:17  rvv
    *** empty log message ***

    Revision 1.33  2013/04/20 16:28:49  rvv
    *** empty log message ***

    Revision 1.32  2013/04/07 16:08:24  rvv
    *** empty log message ***

    Revision 1.31  2013/03/30 12:21:17  rvv
    *** empty log message ***

    Revision 1.30  2013/03/27 18:51:59  rvv
    *** empty log message ***

    Revision 1.29  2012/12/22 15:31:52  rvv
    *** empty log message ***

    Revision 1.28  2012/10/07 14:54:38  rvv
    *** empty log message ***

    Revision 1.27  2012/04/18 06:46:50  rvv
    *** empty log message ***

    Revision 1.26  2012/04/11 17:14:52  rvv
    *** empty log message ***

    Revision 1.25  2012/01/28 16:13:06  rvv
    *** empty log message ***

    Revision 1.24  2012/01/25 19:08:27  rvv
    *** empty log message ***

    Revision 1.23  2012/01/22 13:44:07  rvv
    *** empty log message ***

    Revision 1.22  2011/12/31 18:31:33  rvv
    *** empty log message ***

    Revision 1.21  2011/12/21 19:18:08  rvv
    *** empty log message ***

    Revision 1.20  2011/12/04 12:55:26  rvv
    *** empty log message ***

    Revision 1.19  2011/11/19 15:41:14  rvv
    *** empty log message ***

    Revision 1.18  2011/11/12 18:32:28  rvv
    *** empty log message ***

    Revision 1.17  2011/11/03 19:26:01  rvv
    *** empty log message ***

    Revision 1.16  2011/10/30 13:31:24  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("./orderControlleRekenClassV2.php");
include_once("./rapport/rapportRekenClass.php");


$__funcvar[listurl]  = "orderregelsListV2.php";
$__funcvar[location] = "orderregelsEditV2.php";

$data = $_GET;
$action = $data['action'];

$object = new OrderRegelsV2();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editObject->controller($_GET['action'],$data);


/** als request type = ajax return json voor jquery bij update of verwijderen **/
if( requestType('ajax') && ($action == 'update' || $action == 'delete') ) {
  if ($editObject->object->error == false) {
      echo json_encode(array(
        'success' => true, 
        'saved'   => true,
        'message'   => urlencode($editObject->message)
      )); //let ajax know the request ended in success
      exit();
  } else {
    echo json_encode(array(
        'success'               => true,
        'saved'                 => false,
        'message'               => $editObject->_error,
        'errors'                => $object->getErrors()
      )); //let ajax know the request ended in failure
  }
  exit();
}


echo $editObject->getOutput();

if($object->error || $_GET['adding'])
  $adding=true;
if ($result = $editObject->result)
{
  if ($adding)
  {
    header("Location: ".$__funcvar['location']."?action=new&orderid=".$data["orderid"]);
  }
  else
  {
	  header("Location: ".$returnUrl);
  }
}
else
{
	echo $_error = $editObject->_error;
}

?>