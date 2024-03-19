<?php
//============================================================+
// File name   : etldevtcpdf_include.php
// Begin       : 2008-05-14
// Last Update : 2014-12-10
//
// Description : Search and include the ETLDEVTCPDF library.
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
 * Search and include the ETLDEVTCPDF library.
 * @package com.tecnick.etldevtcpdf
 * @abstract ETLDEVTCPDF - Include the main class.
 * @author Nicola Asuni
 * @since 2013-05-14
 */

// always load alternative config file for examples
require_once('config/etldevtcpdf_config_alt.php');

// Include the main ETLDEVTCPDF library (search the library on the following directories).
$etldevtcpdf_include_dirs = array(
	realpath('../etldevtcpdf.php'),
	'/usr/share/php/etldevtcpdf/etldevtcpdf.php',
	'/usr/share/etldevtcpdf/etldevtcpdf.php',
	'/usr/share/php-etldevtcpdf/etldevtcpdf.php',
	'/var/www/etldevtcpdf/etldevtcpdf.php',
	'/var/www/html/etldevtcpdf/etldevtcpdf.php',
	'/usr/local/apache2/htdocs/etldevtcpdf/etldevtcpdf.php'
);
foreach ($etldevtcpdf_include_dirs as $etldevtcpdf_include_path) {
	if (@file_exists($etldevtcpdf_include_path)) {
		require_once($etldevtcpdf_include_path);
		break;
	}
}

//============================================================+
// END OF FILE
//============================================================+
