<?php
namespace Lib\TCPDF;
//============================================================+
// File name   : TCPdiParserException.php
// Version     : 1.0
// Begin       : 2013-09-25
// Last Update : 2013-09-25
// Author      : Paul Nicholls - https://github.com/pauln
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
//
// Based on    : TCPDF_PARSER.php
// Version     : 1.0.003
// Begin       : 2011-05-23
// Last Update : 2013-03-17
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2011-2013 Nicola Asuni - Tecnick.com LTD
//
// This file is for use with the TCPDF software library.
//
// tcpdi_parser is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// tcpdi_parser is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the License
// along with tcpdi_parser. If not, see
// <http://www.tecnick.com/pagefiles/tcpdf/LICENSE.TXT>.
//
// See LICENSE file for more information.
// -------------------------------------------------------------------
//
// Description : This is a PHP class for parsing PDF documents.
//
//============================================================+

/**
 * @file
 * This is a PHP class for parsing PDF documents.<br>
 * @author Paul Nicholls
 * @author Nicola Asuni
 * @version 1.0
 */

/**
 * Exception for handling issues on pdf parsing.
 */
class TCPdiParserException extends \Exception {};

if (!defined ('PDF_TYPE_NULL'))
    define ('PDF_TYPE_NULL', 0);
if (!defined ('PDF_TYPE_NUMERIC'))
    define ('PDF_TYPE_NUMERIC', 1);
if (!defined ('PDF_TYPE_TOKEN'))
    define ('PDF_TYPE_TOKEN', 2);
if (!defined ('PDF_TYPE_HEX'))
    define ('PDF_TYPE_HEX', 3);
if (!defined ('PDF_TYPE_STRING'))
    define ('PDF_TYPE_STRING', 4);
if (!defined ('PDF_TYPE_DICTIONARY'))
    define ('PDF_TYPE_DICTIONARY', 5);
if (!defined ('PDF_TYPE_ARRAY'))
    define ('PDF_TYPE_ARRAY', 6);
if (!defined ('PDF_TYPE_OBJDEC'))
    define ('PDF_TYPE_OBJDEC', 7);
if (!defined ('PDF_TYPE_OBJREF'))
    define ('PDF_TYPE_OBJREF', 8);
if (!defined ('PDF_TYPE_OBJECT'))
    define ('PDF_TYPE_OBJECT', 9);
if (!defined ('PDF_TYPE_STREAM'))
    define ('PDF_TYPE_STREAM', 10);
if (!defined ('PDF_TYPE_BOOLEAN'))
    define ('PDF_TYPE_BOOLEAN', 11);
if (!defined ('PDF_TYPE_REAL'))
    define ('PDF_TYPE_REAL', 12);
if (!defined ('PDF_PARSER_ERROR_HANDLER_PHP_DIE'))
    define('PDF_PARSER_ERROR_HANDLER_PHP_DIE', 'php_die');
if (!defined ('PDF_PARSER_ERROR_HANDLER_EXCEPTION'))
    define('PDF_PARSER_ERROR_HANDLER_EXCEPTION', 'exception'); // END OF TCPDF_PARSER CLASS

//============================================================+
// END OF FILE
//============================================================+
