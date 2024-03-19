<?php
//============================================================+
// File name   : etldevtcpdf_barcodes_1d_include.php
// Begin       : 2013-05-19
// Last Update : 2013-05-19
//
// Description : Search and include the ETLDEVTCPDF Barcode 1D class.
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
 * Search and include the ETLDEVTCPDF Barcode 1D class.
 * @package com.tecnick.etldevtcpdf
 * @abstract ETLDEVTCPDF - Include the main class.
 * @author Nicola Asuni
 * @since 2013-05-19
 */

// Include the ETLDEVTCPDF 1D barcode class (search the class on the following directories).
$etldevtcpdf_barcodes_1d_include_dirs = array(realpath('../../etldevtcpdf_barcodes_1d.php'), '/usr/share/php/etldevtcpdf/etldevtcpdf_barcodes_1d.php', '/usr/share/etldevtcpdf/etldevtcpdf_barcodes_1d.php', '/usr/share/php-etldevtcpdf/etldevtcpdf_barcodes_1d.php', '/var/www/etldevtcpdf/etldevtcpdf_barcodes_1d.php', '/var/www/html/etldevtcpdf/etldevtcpdf_barcodes_1d.php', '/usr/local/apache2/htdocs/etldevtcpdf/etldevtcpdf_barcodes_1d.php');
foreach ($etldevtcpdf_barcodes_1d_include_dirs as $etldevtcpdf_barcodes_1d_include_path) {
	if (@file_exists($etldevtcpdf_barcodes_1d_include_path)) {
		require_once($etldevtcpdf_barcodes_1d_include_path);
		break;
	}
}

//============================================================+
// END OF FILE
//============================================================+
