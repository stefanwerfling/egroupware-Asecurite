<?php

/**
 * <b>File class.ui_pdf.inc.php</b>
 * asecurite's user interface for print
 * @author N'faly KABA
 * @since   21/08/2012
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @subpackage asecurite/inc/
 * @filesource  class.ui_pdf.inc
 */
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/lib/phpToPDF.php');
include_once(EGW_INCLUDE_ROOT . '/asecurite/inc/class.bo_asecurite.inc.php');

class ui_pdf extends phpToPDF
{

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
        $pref = bo_asecurite::getPreference();
        $this->Ln(4);
        $this->Cell(0, 10, $pref['address'], 0, 0, 'C');
    }

    function Output($name = '')
    {
        // Output PDF to some destination
        if ($this->state < 3)
            $this->Close();

        // Send to standard output
        $this->_checkoutput();
        if (PHP_SAPI != 'cli')
        {
            // We send to a browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
        }
        echo $this->buffer;


        return '';
    }

}
