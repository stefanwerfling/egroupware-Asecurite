<?php

/**
 * <b>File class.bo_fwkpopin.inc.php</b>
 * popin and popup managment for eGroupware applications
 * @author N'faly KABA
 * @since   02/04/2013
 * @version 1.0
 * @copyright France Telecom
 * @subpackage popin
 * @filesource  class.bo_fwkpopin.inc.php
 */
class bo_fwkpopin {

    /** @var int max popin width */
    private static $maxpopinwidth = 800;

    /** @var int max popin heihgt */
    private static $maxpopinheight = 500;

    /**
     * Constructor
     * Static Class, can't be instancied
     */
    private function __construct() {
        
    }

    static function display_type() {
        return 'popin';//$GLOBALS['egw_info']['user']['preferences']['common']['display_type'];
    }

    /**
     * write a js function to display a popup
     * @param string $link popup's window link
     * @param int $width popup's window width
     * @param int $height popup's window height
     * @return type string js
     */
    public static function open_popup($link, $width, $height) {
        return 'egw_openWindowCentered2(\'' . $link . '\', \'_blank\', ' . $width . ', ' . $height . ', \'yes\'); return false;';
    }

    /**
     * write a js function to display a popin
     * @param string $link popup's window link
     * @param int $width popup's window width
     * @param int $height popup's window height
     * @param string $title_bar popin title bar
     * @return type string js
     */
    public static function open_popin($link, $width, $height, $id, $title_bar = '', $dialog_id = '') {
        return "displayPopin('$link', $width, $height, $id, '$title_bar', '$dialog_id'); return false;";
    }

    /**
     * close a popup opened windows and redirect (refresh) the popup parent opener
     * @param string $link link to the opener location
     * @return void
     */
    public static function close_popup($link) {
        $js = "opener.location.href='" . $link . "';";
        $js .= 'window.close();';
        echo '<html><body onload="' . $js . '"></body></html>';
        $GLOBALS['egw']->common->egw_exit();
    }

    /**
     * close a popin opened windows and redirect (refresh) the popup parent opener
     * @param string $link link to the parent location
     * @return void
     */
    public static function close_popin($link) {
        $js = "parent.location.href='" . $link . "';";
        $GLOBALS['egw']->js->set_onload($js);
    }

    /**
     * Perform close operation 
     * @param string $link link to the opener location
     * @return void
     */
    public static function close($link) {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            self::close_popup($link);
        } else {
            self::close_popin($link);
        }
    }

    /**
     * draw a button in html in order to display new window following the chosen window display type
     * @param string $link link to window to display
     * @param string $name button name
     * @param int $width window width
     * @param int $height window height
     * @param string $title_bar popin title bar
     * @return string html
     */
    public static function draw_button($link, $name, $width, $height, $id, $title_bar = '', $dialog_id = '') {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            return '<button id="popup"  onclick="' . self::open_popup($link, $width, $height) . '" name="popup">' . lang($name) . '</button>';
        } else {
            if ($width > self::$maxpopinwidth)
                $width = self::$maxpopinwidth;
            if ($height > self::$maxpopinheight)
                $height = self::$maxpopinheight;
            
            return '<span id="popin" name="popin" onclick="' . self::open_popin($link, $width, $height, $id, $title_bar, $dialog_id) . '"><button>' . lang($name) . '</button></span>';
        }
    }

    /**
     * draw an icon (image) in html in order to display new window following the chosen window display type
     * @param string $link link to window to display
     * @param string $src icon source name
     * @param int $width window width
     * @param int $height window height
     * @param string $extra extra parameters ex: 'id="toto" name="tata"'
     * @param string $title_bar popin title bar
     * @return string html
     */
    public static function draw_icon_button($link, $src, $width, $height, $id, $extra = '', $title_bar = '', $dialog_id = '') {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            return '<img onclick="' . self::open_popup($link, $width, $height) . '" ' . $extra . ' src="' . $src . '"/>';
        } else {
            if ($width > self::$maxpopinwidth)
                $width = self::$maxpopinwidth;
            if ($height > self::$maxpopinheight)
                $height = self::$maxpopinheight;

            return '<span onclick="' . self::open_popin($link, $width, $height, $id, $title_bar, $dialog_id) . '" ' . $extra . '>' . '<img ' . $extra . ' src="' . $src . '"/>' . '</span>';
        }
    }

    /**
     * draw openable link in html in order to display new window following the chosen window display type
     * @param string $link link to window to display
     * @param string $text icon source name
     * @param int $width window width
     * @param int $height window height
     * @param string $extra extra parameters ex: 'id="toto" name="tata"'
     * @param string $title_bar popin title bar
     * @return string
     */
    public static function draw_openable_link($link, $text, $width, $height, $id, $extra = '', $title_bar = '', $dialog_id = '') {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            return '<span onclick="' . self::open_popup($link, $width, $height) . '" ' . $extra . '>' . $text . '</span>';
        } else {
            if ($width > self::$maxpopinwidth)
                $width = self::$maxpopinwidth;
            if ($height > self::$maxpopinheight)
                $height = self::$maxpopinheight;

            return '<span onclick="' . self::open_popin($link, $width, $height, $id, $title_bar, $dialog_id) . '" ' . $extra . '>' . $text . '</span>';
        }
    }

    /**
     * draw a button to perform close operation for both popin and popup
     * @param string $link redirect link on close
     * @param string $name button name
     * @param string $dialog_id id of dialog to close
     * @return string
     */
    public static function draw_close_button($link = '', $name = 'close', $dialog_id = '') {
        $display_type = self::display_type();
        if ($link !== '') {
            return '<button onclick="' . self::redirect($display_type, $link) . 'return false;">' . lang($name) . '</button>';
        }
        if ($display_type == 'popup') {
            return '<button onclick="window.close();return false;">' . lang($name) . '</button>';
        }
        return '<button onclick="self.parent.closePopin(\'' . $dialog_id . '\');return false;">' . lang($name) . '</button>';
    }

    /**
     * write js redirect script contigent display type
     * @param string $link redirect link
     * @return string 
     */
    public static function redirect($link) {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            return "opener.location.href='" . $link . "'; window.close();";
        } else {
            return "parent.location.href='" . $link . "';";
        }
    }

    /**
     * Refresh a window's opener without closing it
     * @param array $param link queries ex: array ('q' => 'value')
     * @return boolean
     */
    public static function refresh_opener($link, $extra_script = '') {
        $display_type = self::display_type();
        if ($display_type == 'popup') {
            $js = "opener.location.href='$link ';$extra_script";
            echo '<html><body onload="' . $js . '"></body></html>';
            return true;
        }
        return true;
    }

    public static function add_close_script($dialog_id = '') {
        return 'self.parent.tb_remove();self.parent.closePopin(\'' . $dialog_id . '\');';
    }

    /**
     * Draw a json editor
     * @param string $json json to display
     * @param string $dest_id id of input which will contains json (after editing)
     * @param int $with window width
     * @param int $height window height
     * @return string
     */
    public static function draw_json_editor($dest_id, $dialog_id = '', $with = 750, $height = 400) {
        return '<iframe src="/phpgwapi/js/advise_js/JSONeditor/JSONeditor.php?dest_id=' . $dest_id . '&dialog=' . $dialog_id . '" width="' . $with . '" height="' . $height . '"></iframe>';
    }

    /**
     * Draw an html editor
     * @param string $action action for onsubmit
     * @param string $content content of the textarea
     * @param string $prev_button input button with onclick action to go back to precedent panel, example:
     * <input type="button" value="< Retour" name="retour" onclick="self.parent.closePopin('dialog2');self.parent.showPopin('');"/>
     * @param string $skin (value = kama, office2003, v2)
     * @return string
     */
    public static function draw_html_editor($action, $content = '', $prev_button = '', $skin = 'kama') {
        $html = '<script type="text/javascript" src="' . self::build_file_path('/phpgwapi/js/advise_js/ckeditor/ckeditor.js') . '"></script>
        <script src="' . self::build_file_path('/phpgwapi/js/advise_js/jquery.js') . '" type="text/javascript"></script>
        <script src="' . self::build_file_path('/phpgwapi/js/advise_js/ckeditor/init.js') . '" type="text/javascript"></script>';
        if (!empty($prev_button)) {
            $html .= '<div class="navPanel">' . $prev_button . '</div>';
        }
        $html .= '<form action="' . $action . '" method="post">
                <textarea class="ckeditor" cols="80" id="htmlContent" name="htmlContent" rows="10">' . $content . '</textarea>
                    <script type="text/javascript">
                    //<![CDATA[
                        CKEDITOR.replace( "htmlContent",
                                {
                                        skin : "' . $skin . '",
                                        extraPlugins : "tableresize"
                                });
                    //]]>
                </script>
            </form>';
        return $html;
    }

    /**
     * build a file with filemtime (in order to don't reload the file if not modified)
     * @param string $file to build
     * @return string
     */
    public static function build_file_path($file) {
        if (file_exists(EGW_SERVER_ROOT . $file)) {
            return $GLOBALS['egw_info']['server']['webserver_url'] . $file . '?' . filemtime(EGW_SERVER_ROOT . $file);
        }
        return $file;
    }

}
