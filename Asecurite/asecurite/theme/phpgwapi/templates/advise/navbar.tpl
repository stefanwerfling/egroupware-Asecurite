<!-- BEGIN navbar_header -->

<script language="javascript">
	var currentapp = '{currentapp}';
</script>

<!-- BEGIN app_extra_icons_div -->
<script language="javascript">
	new ypSlideOutMenu("menu1", "down", 45, 5, 188, 300,'right');
</script>

<div id="menu1Container">
	<div id="menu1Content" class="menuOneBox">
		<div id="extraIcons">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<!-- BEGIN app_extra_block -->
				<tr>
					<td class="extraIconsRowIcon"><a href="{url}" {target}><img src="{icon}" alt="{title}" title="{title}" width="16" border="0" /></a></td>
					<td align="left" class="extraIconsRow"><a href="{url}" {target}>{title}</a></td>
				</tr>
			<!-- END app_extra_block -->
				<tr><td colspan="2" class="extraIconsHeader" nowrap="nowrap" align="right">
                                <a href="#" {show_menu_event}="ypSlideOutMenu.hide('menu1')" title="{lang_close}"><img src="{img_root}/orange-ball.png" /> {lang_close}</a>
                        </td></tr>
			</table>
		</div>
	</div>
</div>
<!-- END app_extra_icons_div -->

<div class="appIconSpacer"></div>

<div id="divMain">
	<div id="divUpperTabs">
		<ul>		
<!-- BEGIN upper_tab_block -->
			<li><a href="{url}">{title}</a></li>
<!-- END upper_tab_block -->
		</ul>
	</div>
	
<!-- ************************************************************************
                               SUB HEADER
 **************************************************************************** -->  	
<div id="OrangeHeader" class="mainHeader">
    <!--<a href="{logo_url}" target="_blank"><img src="{logo_file}" border="0" title="{logo_title}" alt="EGroupware"/></a> -->
	<img class="logoRunTools" src="{img_root}/logos/logo.png" border="0" alt="Planification" title="Planification" width="120" height="40" />  
</div>

<!-- *************************************************************************
                             MAIN NAVIGATION
 ****************************************************************************  -->  

<div id="nav" class="divAppIconBlock">
	<ul id="divAppIconBar" class="appIconTbl">
	<!-- BEGIN app_icon_block -->
		<!--  
		<li {style} onclick="location.href='{url}'">
			<img src="{icon}" alt="{title}" title="{title}" border="0" width="16" height="16" />
			<a href="{url}" {target}>{title}</a>
		</li>
		-->
		<li {style} onclick="location.href='{url}'; return false;">
			<img src="{icon}" alt="{title}" title="{title}" border="0" width="16" height="16" />
			<a href="{url}" {target}>{title}</a>
		</li>
	<!-- END app_icon_block -->
	
	<!-- BEGIN app_extra_icons_icon -->
		<li>
			<a title="{lang_show_more_apps}" href="#" {show_menu_event}="ypSlideOutMenu.showMenu('menu1')">
				<img src="{img_root}/show_menu.png" border="0" />
			</a>
		</li>
	<!-- END app_extra_icons_icon -->

	<!--  {current_app_title} -->	

	<!-- BEGIN app_title_block -->
		<!-- <a href="{url}" {target}>{title}</a> -->  
	<!-- END app_title_block -->
				
	</ul>
</div>
<!-- *************************************************************************
                               INFO LINE
 **************************************************************************  -->

<div class="infoline">Vous &ecirc;tes dans&nbsp;:&nbsp;<span>{domain}{current_app_title}</span></div>	

	
<div id="divStatusBar"><table width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
                <td width="33%" align="left" id="user_info">{user_info}</td>
                <td align="center" id="admin_info">{current_users}</td>
                <td width="33%"  align="right" id="quick_add">{quick_add}</td>
        </tr></table></div>
<!-- END navbar_header -->


<!-- BEGIN appbox -->
	</div><!--[if lte IE 6.5]><iframe frameborder="0" framespacing="0" src="{img_root}/dragarea_right.png" class="iframeforselectbox"></iframe><![endif]--></div>
	<div id="divSubContainer">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
		<!-- Sidebox Column -->
{sideboxcolstart}
<!-- END appbox -->


<!-- BEGIN sidebox_hide_header -->
	<script language="javascript">
		new ypSlideOutMenu("menu2", "right", 0, 28, 160, 200)
	</script>

	<!--  <div id="sideboxdragarea" style="position:absolute;left:0px;top:90px; z-index: 52;">  -->
	<div id="sideboxdragarea">
		<a href="#" {show_menu_event}="ypSlideOutMenu.showMenu('menu2')" title="{lang_show_menu}"><img src="{img_root}/orange-ball.png" /> {lang_close}</a>
	</div>

	<div id="menu2Container">
	<!--[if lte IE 6.5]><div class="selectbg" id="dd3"><div class="bdforselection"><![endif]-->
		<div id="menu2Content" style="position: relative; left: 0; text-align: left;">
			<table cellspacing="0" cellpadding="0">
				<tr><td>
					<div class="slideoutBack">
<!-- END sidebox_hide_header -->


<!-- BEGIN sidebox_hide_footer -->
					</div>
				</td><td style="padding-top:10px" valign="top">
					<a href="#" onclick="ypSlideOutMenu.hide('menu2')" ><img src="{img_root}/dragarea_left.png" align="right" /></a>
				</td>
			</tr></table>
		</div>
	</div>
<!-- END sidebox_hide_footer -->


<!-- BEGIN navbar_footer -->
{sideboxcolend}
		<!-- End Sidebox Column -->

		<!-- Applicationbox Column -->
		<td id="tdAppbox" valign="top" {remove_padding}>
		<!-- 	<div id="divAppboxHeader">
                                <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
                                         <tr>
                                                <td class="appBoxHeadLt"></td>
                                                <td class="appBoxHeadMid" nowrap>&nbsp;{current_app_title}&nbsp;</td>
                                                <td class="appBoxHeadMid" nowrap>&nbsp;{current_app_title}/</td>
                                                <td class="appBoxHeadRt"></td>
                                        </tr> 
                                </table>
                        </div>-->
			<div id="divAppbox">
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr><td>
<!-- END navbar_footer -->

<!-- ***************************************************************************
                               SIDE BAR MENU 
 ******************************************************************************* -->

<div class="divSidebox">
	<!-- BEGIN extra_blocks_header -->
	<ul>
    	<li class="divSideboxHeader">{lang_title}</li>
    <!-- END extra_blocks_header -->
    <!-- BEGIN extra_block_row -->
    	<li class="divSideboxEntry">{icon_or_star}<span><a class="textSidebox" href="{item_link}"{target}>{lang_item}</a></span></li>
    <!-- END extra_block_row -->
    <!-- BEGIN extra_block_row_raw -->
    	<li><span><a class="textSidebox" href="{item_link}"{target}>{lang_item}</a></span></li>
    <!-- END extra_block_row_raw -->
	<!-- BEGIN extra_block_row_no_link --> 
    	<li class="divSideboxEntry">{icon_or_star}<span>{lang_item}</span></li>  
	<!-- END extra_block_row_no_link -->
    <!-- BEGIN extra_block_spacer -->
    	<li class="divSideboxEntry">&nbsp;</li> 
    <!-- END extra_block_spacer -->
<!-- BEGIN extra_blocks_footer -->
	</ul>
<!-- END extra_blocks_footer -->        
</div>









<div class="sideboxSpace">
	<table class="menuBotMid" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="menuBotLt"></td>
			<td nowrap>&nbsp;</td>
			<td class="menuBotRt"></td>
		</tr>
	</table>
</div>
<!-- END extra_blocks_footer -->






