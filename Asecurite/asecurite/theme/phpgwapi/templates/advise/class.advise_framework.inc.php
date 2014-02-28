<?php
/**
 * eGW jerryr template
 * 
 * @link http://www.egroupware.org
 * @author Ralf Becker <RalfBecker-AT-outdoor-training.de> rewrite in 12/2006
 * @author Pim Snel <pim@lingewoud.nl> author of the idots template set
 * @license http://opensource.org/licenses/gpl-license.php GPL - GNU General Public License
 * @package api
 * @subpackage framework
 * @access public
 * @version $Id$
 */

require_once(EGW_SERVER_ROOT.'/phpgwapi/templates/idots/class.idots_framework.inc.php');

/**
 * eGW jerryr template
 */
class advise_framework extends idots_framework
{
	/**
	 * Constructor, calls the contstructor of the extended class
	 *
	 * @param string $template='dop'
	 * @return dop_framework
	 */
	function advise_framework($template='advise')
	{
		$this->idots_framework($template);
	}

	function topmenu(array &$vars,array &$apps)
	{
		$this->tplsav2->menuitems = array();
		$this->tplsav2->menuinfoitems = array();

		if($GLOBALS['egw_info']['user']['apps']['home'] && isset($apps['home']))
		{
			$this->_add_topmenu_item($apps['home']);
		}
		
		if($GLOBALS['egw_info']['user']['apps']['preferences'])
		{
			$this->_add_topmenu_item($apps['preferences']);
		}
		
		if($GLOBALS['egw_info']['user']['apps']['admin'])
		{
			$this->_add_topmenu_item($apps['admin']);
		}
		
		if($GLOBALS['egw_info']['user']['apps']['manual'] && isset($apps['manual']))
		{
			$this->_add_topmenu_item($apps['manual']);
		}
		//$this->_add_topmenu_item('about',lang('About %1',$GLOBALS['egw_info']['apps'][$GLOBALS['egw_info']['flags']['currentapp']]['title']));
		$this->_add_topmenu_item($apps['logout']);

		$this->tplsav2->assign('info_icons',$this->topmenu_icon_arr);

		if($GLOBALS['egw_info']['user']['apps']['notifications'])
		{
			$this->_add_topmenu_info_item($this->_get_notification_bell());
		}
		$this->_add_topmenu_info_item($vars['user_info']);
		$this->_add_topmenu_info_item($vars['current_users']);
		
		// $this->_add_topmenu_info_item ($GLOBALS['egw_info']['user']['account_primary_group']);
		// $this->_add_topmenu_info_item("<pre>".print_r($GLOBALS['egw_info']['user'],true)."</pre>");
		
		//$this->_add_topmenu_info_item($vars['quick_add']);
				
		// add by stephan. Add path to advise theme to use topmenu located in advise vs idot one.
		$this->tplsav2->set_tpl_path(EGW_SERVER_ROOT.SEP.'phpgwapi'.SEP.'templates'.SEP.'advise');
		
		$this->tplsav2->display('topmenu.tpl.php');
	}
	
   /**
	* Return a sidebox menu item
	*
	* @internal PHP5 protected
	* @param string $item_link
	* @param string $item_text
	* @return string
	*/
	function _sidebox_menu_item($item_link='',$item_text='')
	{
		if($item_text === '_NewLine_' || $item_link === '_NewLine_')
		{
			return $this->tpl->parse('out','extra_block_spacer');
		}
		if (strtolower($item_text) == 'grant access' && $GLOBALS['egw_info']['server']['deny_user_grants_access'])
		{
			return;
		}

		$var['icon_or_star']='<img class="sideboxstar" src="'.$GLOBALS['egw_info']['server']['webserver_url'] . '/phpgwapi/templates/'.$this->template.'/images'.'/orange-ball.png" width="7" height="11" alt="ball"/>';
		$var['target'] = '';
		if(is_array($item_link))
		{
			if(isset($item_link['icon']))
			{
				$app = isset($item_link['app']) ? $item_link['app'] : $GLOBALS['egw_info']['flags']['currentapp'];
				$var['icon_or_star'] = $item_link['icon'] ? '<img style="margin:0px 2px 0px 2px; height: 16px;" src="'.common::image($app,$item_link['icon']).'"/>' : False;
			}
			$var['lang_item'] = isset($item_link['no_lang']) && $item_link['no_lang'] ? $item_link['text'] : lang($item_link['text']);
			$var['item_link'] = $item_link['link'];
			if ($item_link['target'])
			{
				if (strpos($item_link['target'], 'target=') !== false)
				{
					$var['target'] = $item_link['target'];
				}
				else
				{
					$var['target'] = ' target="' . $item_link['target'] . '"';
				}
			}
		}
		else
		{
			$var['lang_item'] = lang($item_text);
			$var['item_link'] = $item_link;
		}
		$this->tpl->set_var($var);

		$block = 'extra_block_row';
		if ($var['item_link'] === False)
		{
			$block .= $var['icon_or_star'] === False ? '_raw' : '_no_link';
		}
		return $this->tpl->parse('out',$block);
	}
	
/**
	* Get navbar as array to eg. set as vars for a template (from idots' navbar.inc.php)
	*
	* Reimplemented so set the vars for the navbar itself (uses $this->tpl and the blocks a and b)
	*
	* @internal PHP5 protected
	* @param array $apps navbar apps from _get_navbar_apps
	* @return array
	*/
	function _get_navbar($apps)
	{
		// call directly egw_framework vs parent for the hook
		$var = egw_framework::_get_navbar($apps);

		if($GLOBALS['egw_info']['user']['preferences']['common']['click_or_onmouseover'] == 'onmouseover')
		{
			$var['show_menu_event'] = 'onMouseOver';
		}
		else
		{
			$var['show_menu_event'] = 'onClick';
		}

		if($GLOBALS['egw_info']['user']['userid'] == 'anonymous')
		{
			$config_reg = config::read('registration');

			$this->tpl->set_var(array(
				'url'   => $GLOBALS['egw']->link('/logout.php'),
				'title' => lang('Login'),
			));
			$this->tpl->fp('upper_tabs','upper_tab_block');
			if ($config_reg[enable_registration]=='True' && $config_reg[register_link]=='True')
			{
				$this->tpl->set_var(array(
					'url'   => $GLOBALS['egw']->link('/registration/index.php'),
					'title' => lang('Register'),
				));
			}
		}

		if (!($max_icons=$GLOBALS['egw_info']['user']['preferences']['common']['max_icons']))
		{
			$max_icons = 30;
		}

		if($GLOBALS['egw_info']['user']['preferences']['common']['start_and_logout_icons'] == 'no')
		{
			$tdwidth = 100 / $max_icons;
		}
		else
		{
			$tdwidth = 100 / ($max_icons+1);	// +1 for logout
		}
		$this->tpl->set_var('tdwidth',round($tdwidth));
		
		// Set domain name
		$this->tpl->set_var('domain',"");			
		if($GLOBALS['egw_info']['user']['domain']!=="default") {
			$this->tpl->set_var('domain',$GLOBALS['egw_info']['user']['domain'].'&nbsp;/&nbsp;');	
		}

		$_currentapp=$GLOBALS['egw_info']['flags']['currentapp'];
		
		// not shown in the navbar
		foreach($apps as $app => $app_data)
		{
			if ($app != 'preferences' && $app != 'about' && $app != 'logout' && $app != 'manual' && $app != 'admin' &&
				($app != 'home' || $GLOBALS['egw_info']['user']['preferences']['common']['start_and_logout_icons'] != 'no'))
			{				
				if ($_currentapp==$app) {
					$app_data['style']="class=\"iconCurrent\" ";
				} else { 					
					$app_data['style']="class=\"iconBack\" onmouseover=\"this.className='iconBackHover'\" onmouseout=\"this.className='iconBack'\"";
				}	
				$this->tpl->set_var($app_data);
								
				
				//$var['current']="<pre>".print_r($apps,true)."</pre>";
				
				
				if($i < $max_icons)
				{
					$this->tpl->set_var($app_data);
					if($GLOBALS['egw_info']['user']['preferences']['common']['navbar_format'] != 'text')
					{
						$this->tpl->fp('app_icons','app_icon_block',true);
					}
					if($GLOBALS['egw_info']['user']['preferences']['common']['navbar_format'] != 'icons')
					{
						$this->tpl->fp('app_titles','app_title_block',true);
					}
				}
				else // generate extra icon layer shows icons and/or text
				{
					$this->tpl->fp('app_extra_icons','app_extra_block',true);
				}
				$i++;
			}
		}
		// settings for the extra icons dif
		if ($i <= $max_icons)	// no extra icon div
		{
			$this->tpl->set_var('app_extra_icons_div','');
			$this->tpl->set_var('app_extra_icons_icon','');
		}
		else
		{
			$var['lang_close'] = lang('Close');
			$var['lang_show_more_apps'] = lang('show_more_apps');
		}
		if ($GLOBALS['egw_info']['user']['preferences']['common']['start_and_logout_icons'] != 'no' &&
			$GLOBALS['egw_info']['user']['userid'] != 'anonymous')
		{
			$this->tpl->set_var($apps['logout']);
			if($GLOBALS['egw_info']['user']['preferences']['common']['navbar_format'] != 'text')
			{
				$this->tpl->fp('app_icons','app_icon_block',true);
			}
			if($GLOBALS['egw_info']['user']['preferences']['common']['navbar_format'] != 'icons')
			{
				$this->tpl->fp('app_titles','app_title_block',true);
			}
		}

		if($GLOBALS['egw_info']['user']['preferences']['common']['navbar_format'] == 'icons')
		{
			$var['app_titles'] = '<td colspan="'.$max_icons.'">&nbsp;</td>';
		}
		return $var;
	}
	
	/**
	 * Returns html with user and time
	 * Redefine egw_framework method
	 * @return void
	 * @author Stephan Acquatella 
	 */
	protected static function _user_time_info()
	{
		$now = new egw_time();
		$user_info = '<span class="adviseUser">'.common::display_fullname() .'</span><span class="adviseDate">'. ' - ' . lang($now->format('l')) . ' ' . $now->format(true).'</span>';

		$user_tzs = egw_time::getUserTimezones();
		if (count($user_tzs) > 1)
		{
			$tz = $GLOBALS['egw_info']['user']['preferences']['common']['tz'];
			$user_info .= html::form(html::select('tz',$tz,$user_tzs,true,' onchange="this.form.submit();"'),array(),
				'/index.php','','tz_selection',' style="display: inline;"','GET');
		}
		return $user_info;
	}
	
	/**
	 * Get footer as array to eg. set as vars for a template (from idots' head.inc.php)
	 *
	 * @return array
	 */
	protected function _get_footer()
	{
		$var = Array(
			'img_root'       => $GLOBALS['egw_info']['server']['webserver_url'] . '/phpgwapi/templates/'.$this->template.'/images',
			'version'        => $GLOBALS['egw_info']['server']['versions']['phpgwapi']
		);
		if($GLOBALS['egw_info']['user']['preferences']['common']['show_generation_time'])
		{
			$totaltime = sprintf('%4.2lf',microtime(true) - $GLOBALS['egw_info']['flags']['page_start_time']);

			$var['page_generation_time'] = '<div id="divGenTime"><span>'.lang('Page was generated in %1 seconds',$totaltime);
			if ($GLOBALS['egw_info']['flags']['session_restore_time'])
			{
				$var['page_generation_time'] .= ' '.lang('(session restored in %1 seconds)',
					sprintf('%4.2lf',$GLOBALS['egw_info']['flags']['session_restore_time']));
			}
			$var['page_generation_time'] .= '</span></div>';
		}
		$var['powered_by'] = lang('Powered by').' <a href="http://www.stylite.de/" target="_blank">Stylite\'s</a>'.
			' <a href="'.$GLOBALS['egw_info']['server']['webserver_url'].'/about.php">EGroupware</a>'.
			' Community Version '.$GLOBALS['egw_info']['server']['versions']['phpgwapi'];
		return $var;
	}
	
}
