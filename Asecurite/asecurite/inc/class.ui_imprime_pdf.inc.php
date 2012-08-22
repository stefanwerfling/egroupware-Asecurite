<?php

/**
 * <b>File class.ui_imprime.inc.php</b>
 * asecurite's user interface for print
 * @author N'faly KABA
 * @since   21/08/2012
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_imprime_pdf.inc
 */
require(EGW_INCLUDE_ROOT . '/phpgwapi/inc/fpdf/fpdf.php');
define('FPDF_FONTPATH', EGW_INCLUDE_ROOT . '/phpgwapi/inc/fpdf/font/');

class ui_imprime_pdf extends FPDF {

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0,10, 'Page '.$this->PageNo().'/{nb}', 0,0, 'C');
    }

}
