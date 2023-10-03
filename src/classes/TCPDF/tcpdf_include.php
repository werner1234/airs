<?php
//============================================================+
// File name   : tcpdf_include.php
// Begin       : 2008-05-14
// Last Update : 2014-12-10
//
// Description : Search and include the TCPDF library.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Search and include the TCPDF library.
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Include the main class.
 * @author Nicola Asuni
 * @since 2013-05-14
 */

$tcpdfDir = $__appvar['basedir'] . '/classes/TCPDF';

// always load alternative config file for examples
require_once( $tcpdfDir . '/config/tcpdf_config_alt.php');

if ( file_exists($tcpdfDir) ) {
  require_once($tcpdfDir . '/tcpdf.php');
}


//============================================================+
// END OF FILE
//============================================================+
