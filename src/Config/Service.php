<?php
global $OOO_CONVERT_VALIDEXTENSIONS;

if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/modules/OpenOfficeConverter/")) define("OOOCONV_ROOT", $_SERVER['DOCUMENT_ROOT'] . "/modules/OpenOfficeConverter/");
else define("OOOCONV_ROOT", $_SERVER['DOCUMENT_ROOT'] . "/OpenOfficeConverter/");

define("OOO_CONVERT_DEBUG", false);
define("OOO_CONVERT_PROXY_HOST", "localhost");
define("OOO_CONVERT_PROXY_PORT", 8888);
// define("OOO_CONVERT_REMOTEENDPOINT", "http://74.52.87.98:8080/OpenOfficeConverter/Api/Server.php"); // Remote Server
define("OOO_CONVERT_REMOTEENDPOINT", "https://pdf.prodigentia.org/OpenOfficeConverter/src/Api/Server.php"); // Localhost
define("OOO_CONVERT_ACCESSKEY", "0DF11384E261E5DE27FB4FF8D0DC37B5");
define("OOO_CONVERT_SECRETKEY", "a'D&is?y^T3b^hXWjZe3OIdB-h;[fWhBiOjsrt%(HJ)u1\S*N9-lbXU&SGOo2<[");

// ares
define("OOO_CONVERT_INDIR", OOOCONV_ROOT . "tmp/in");
define("OOO_CONVERT_OUTDIR", OOOCONV_ROOT . "tmp/out");
define("OOO_CONVERT_EXECPATH", "/opt/openoffice4/program/python");
define("OOO_CONVERT_JODPATH", OOOCONV_ROOT . "src/JOD/DocumentConverter.py");

// $OOO_CONVERT_VALIDEXTENSIONS=array('pdf', 'html', 'odt', 'doc', 'rtf', 'txt', 'ods', 'xls', 'odp', 'ppt', 'sxw');
$OOO_CONVERT_VALIDEXTENSIONS = array('pdf', 'odt', 'doc', 'ods');

define("OOO_CONVERT_OOOHOME", "/tmp/ooohome");

define("OOO_CONVERT_JRE_HOME", "/usr/bin/java");

define("OOO_CONVERT_OOO_HOME", "/opt/openoffice4");
define("OOO_CONVERT_SOFFICE_PATH", OOO_CONVERT_OOO_HOME . "/program/soffice");
