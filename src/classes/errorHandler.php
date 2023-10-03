<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/14 16:28:23 $
 		File Versie					: $Revision: 1.10 $
 		
 		$Log: errorHandler.php,v $
 		Revision 1.10  2019/08/14 16:28:23  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/07/29 17:15:41  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/07/27 05:52:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/07/27 05:23:08  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/07/26 09:53:43  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2007/06/06 15:08:14  cvs
 		*** empty log message ***
 		
 	
*/
function errorLog($error_nr, $message, $extra = "") {
	global $USR;
	global $__appvars;
	//ini_set("SMTP","smtp.aeict.nl");
	
	$errorMessage = "\nerror-no : ".$error_nr;
	$errorMessage .= "\ntime : ".date("d M Y H:i:s");
	$errorMessage .= "\nserver : ". $_SERVER['SERVER_NAME'];
	$errorMessage .= "\nip : ". $_SERVER['REMOTE_ADDR'];
	$errorMessage .= "\nurl : ". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$errorMessage .= "\nfile : ". $extra['errfile'];
	$errorMessage .= "\nline : ". $extra['errline'];
	$errorMessage .= "\nuser : ". $USR;
	$errorMessage .= "\nbrowser : ". $_SERVER['HTTP_USER_AGENT'];
	$errorMessage .= "\nmessage : ". $extra['errstr'];
	$errorMessage .= "\npost : ". print_r($_POST,true);
	//mail("cvs@aeict.nl","Bug: ".$error_nr." ".$_SERVER['REQUEST_URI']." ".date("d M Y H:i:s"),$errorMessage,"");
}
function errorHandler($errno, $errstr, $errfile, $errline, $errctx) {
	switch($errno)
	{
		case E_WARNING  :
		case E_USER_WARNING  :
	  	break;
		case E_USER_ERROR  :
			//logIt("$errno | $errfile | $errline | $errstr ");//print_r($errctx,true)
			//errorLog($errno, $errstr, array("errstr"=>$errstr,"errfile"=>$errfile,"errline"=>$errline,"errctx"=>$errctx));
		  break;
		break;
		case E_NOTICE :
			break;
		break;
		default :
			break;
		break;
	}
}

function handleFatalPhpError()
{
	if(function_exists('error_get_last'))
	{
		$last_error = error_get_last();
		if ($last_error['type'] === 1 || $last_error['type'] === 64)
		{
			logIt($last_error['type'] . " | " . $last_error['file'] . " | " . $last_error['line'] . " | " . $last_error['message']);
		}
	}
	else
	{
		//global $php_errormsg;
		//if($php_errormsg<>'')
	  	//logIt($php_errormsg);
	}
}

if (function_exists('register_shutdown_function'))
{
	register_shutdown_function('handleFatalPhpError');
}

//set_error_handler("errorHandler");
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR);

//ini_set('display_errors', 1);
?>