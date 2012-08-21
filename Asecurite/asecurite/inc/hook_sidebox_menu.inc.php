<?php

/* * ************************************************************************\
 * eGroupWare - Knowledge Base                                              *
 * http://www.egroupware.org                                                *
 * -----------------------------------------------                          *
 *  This program is free software; you can redistribute it and/or modify it *
 *  under the terms of the GNU General Public License as published by the   *
 *  Free Software Foundation; either version 2 of the License, or (at your  *
 *  option) any later version.                                              *
  \************************************************************************* */

/* $Id: hook_sidebox_menu.inc.php 18864 2005-07-23 09:44:46Z milosch $ */ {
    $menu_title = $GLOBALS['egw_info']['apps'][$appname]['title'] . ' ' . lang('Menu');
    $file = Array(
        'Planning global' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_planning_global.index&current=true'),
        'Gestion des villes' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_ville.index'),
        'Gestion des sites' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_site.index'),
        'Gestion des agents' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_agent.index'),
        'Gestion des agents' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_agent.index'),
        'Gestion des jours fériés' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_ferie.index'),
        'Echange de planning' => $GLOBALS['egw']->link('/index.php', 'menuaction=asecurite.ui_planning_global.change_planning&current=true'),
    );

    display_sidebox($appname, $menu_title, $file);

    if ($location != 'preferences') {
        $file = Array(
            lang('Preferences') => egw::link('/index.php', 'menuaction=preferences.uisettings.index&appname=' . $appname)
        );
        if ($location == 'admin') {
            display_section($appname, $file);
        } else {
            display_sidebox($appname, lang('Admin'), $file);
        }
    }
}
?>