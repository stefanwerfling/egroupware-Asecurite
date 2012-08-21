<?php

/**
 * <b>File hook_preferences.inc.php</b>
 * Asecurite preferences hook
 * @author KABA N'faly
 * @since   28/08/2012
 * @version 2.0
 * @copyright KABANFALY
 * @package egroupware
 * @filesource  hook_preferences.inc.php
 */ {
// Only Modify the $file variables.....

    $file = array(
        'Preferences' => $GLOBALS['phpgw']->link('/preferences/preferences.php', 'appname=' . $appname),
    );

    //Do not modify below this line
    display_section($appname, $file);
}