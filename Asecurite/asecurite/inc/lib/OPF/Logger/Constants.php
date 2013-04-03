<?php
/**
 * Define log priorities
 * 
 * cannot use standard PHP LOG_xxx because its values
 * changes from unix to windows
 * 
 * @author  Sandro Lex
 * @version $Revision-Id: sandro.lex@orange-ftgroup.com-20110419124533-o2w4iw46v5ry30g7 $
 * @since   19/07/2010
 * @copyright France Telecom
 * @package OPF
 * @subpackage Logger
 * @filesource OPF/Logger/Constants.php
 */

defined('OPF_NOLOG') or define('OPF_NOLOG', -1);
defined('OPF_EMERG') or define('OPF_EMERG', 0);
defined('OPF_ALERT') or define('OPF_ALERT', 1);
defined('OPF_CRIT') or define('OPF_CRIT', 2);
defined('OPF_ERR') or define('OPF_ERR', 3);
defined('OPF_WARNING') or define('OPF_WARNING', 4);
defined('OPF_NOTICE') or define('OPF_NOTICE', 5);
defined('OPF_INFO') or define('OPF_INFO', 6);
defined('OPF_DEBUG') or define('OPF_DEBUG', 7);

