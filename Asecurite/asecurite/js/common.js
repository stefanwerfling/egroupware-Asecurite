/**
 * check or uncheck all checkboxes
 */
function push_toggle_all()
{
    var checkedLength = $('input[name*="checkbox"]:checked').length;
    if (checkedLength != 0) {
        $('input[name*="checkbox"]').removeAttr('checked')
    } else {
        $('input[name*="checkbox"]').attr('checked', true);
    }
}

function checkUncheckAll(name)
{
    var checkedLength = $('input[name*="' + name + '"]:checked').length;
    if (checkedLength != 0) {
        $('input[name*="' + name + '"]').removeAttr('checked')
    } else {
        $('input[name*="' + name + '"]').attr('checked', true);
    }
}



function js_btn_book_selected(msg)
{
    var checkedLength = $('input[name*="checkbox"]:checked').length;
    if (checkedLength == 0) {
        alert("Aucune case n'a été cochée");
        return false;
    } else {
        return confirm(msg);
    }
}

/**
 * build the http path to access an element
 * @param path path to modify
 * @return  built path
 */
function buildHttpPath(path) {
    var navLink = document.location.href;
    var _split = navLink.split("/");
    if (_split[3].indexOf('index.php') != -1 || _split[3].indexOf('etemplate') != -1) { // http://domain.com/index.php
        path = _split[0] + '//' + _split[2] + '/' + path;
    } else {// http://localhost/egroupware/index.php
        path = _split[0] + '//' + _split[2] + '/' + _split[3] + '/' + path;
    }
    return path;
}

/**
 * Concat a code to current promo code (rulesdresser)
 */
function concatNewCode() {
    var navLink = document.location.href;
    var _split = navLink.split("?");
    $.ajax({
        type: 'GET',
        url: _split[0] + '?menuaction=rulesdresser.ui_product.getPromoCodeSeperator',
        success: function(separator) {
            var promo_code = $('input[name*="promo_code"]').attr('value');
            var option_search_result = $('select[name*="option_search_result"]').attr('value');
            if (option_search_result !== '') {
                if (promo_code === '') {
                    $('input[name*="promo_code"]').attr('value', option_search_result);
                } else {
                    $('input[name*="promo_code"]').attr('value', promo_code + separator + option_search_result);
                }
            }

        }
    });
}



/** Check the filename which must be upload */
function checkImgPath() {
    var mediaName = $('input[name="exec[img]"]').attr('value');
    if (mediaName != '') {
        //alert( mediaName );
        var pathSep = mediaName.lastIndexOf('/');
        var filename = mediaName;
        if (pathSep != -1) {
            filename = mediaName.substr(pathSep + 1);
        } else {
            pathSep = mediaName.lastIndexOf('\\');
            if (pathSep != -1) {
                filename = mediaName.substr(pathSep + 1);
            }
        }
        if (!filename.match(/^[a-zA-Z_0-9\-.]+$/)) {
            alert("Veuillez n'utiliser que des caractères alpha-numériques pour le nom du fichier à envoyer, merci.");
            return false;
        }
        return true;
    }
}




/**
 * Apply pagination action on displayed medias
 */
function paginateMedia() {
    var start = 0;
    var nb = 6;
    var end = start + nb;
    var length = $('.media').length;
    var list = $('.media');
    var currentStart = 1;
    var currentEnd = end;
    $('.prev, .next').click(function(e) {
        e.preventDefault();
        if ($(this).hasClass('prev')) {
            start -= nb;
        } else {
            start += nb;
        }
        if (start < 0 || start >= length) {
            start = 0;
        }
        end = start + nb;
        currentStart = start + 1;
        currentEnd = length < end ? length : end;
        $('#page').html(currentStart + '-' + currentEnd + '/' + length);

        if (start == 0)
            list.hide().filter(':lt(' + (end) + ')').show();
        else
            list.hide().filter(':lt(' + (end) + '):gt(' + (start - 1) + ')').show();
    });
    $('.prev').click();
}


/**
 *Display window into a dialog box as popin
 *@param  link, link of page to display
 *@param width, window width
 *@param height, window height
 *@param id, get parameter
 *@param dialogId dialog id name
 *@param titleBar, window title 
 */

function displayPopin(link, width, height, id, titleBar, dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }
    $('#' + dialogId).html('<center><span><img src="' + buildHttpPath("asecurite/templates/default/images/loading.gif") + '"></span></center>');
    if (height == 0) {
        height = 'auto';
    }
    if (width == 0) {
        width = 'auto';
    }

    //multi-param as id
    $('#' + dialogId).dialog({
        title: titleBar,
        autoResize: true,
        height: height,
        width: width

    });
    xajax_doXMLHTTP(link, id, dialogId);

}

/**
 * Check required input fields
 */
function checkRequiredField() {
    var ok = true;
    $('.inputRequired input, .inputRequired select').each(function(e) {
        if ($(this).attr('value') === '') {
            var id = 'required' + e;
            $('#' + id).html('');
            $(this).after('<span id="' + id + '" style="color:red">Ce champ ne doit pas être vide!</span>');
            ok = false;
        }
    });
    return ok;
}



/**
 * Check media type onchange
 */
function checkMediaType() {
    var mediaType = $('.editMedia select[name="exec[media_type]"]').attr('value');
    if (mediaType == 'http' || mediaType == 'magic') {
        $('.mediaText').show();
        $('.mediaFile').hide();
    } else {
        $('.mediaText').hide();
        $('.mediaFile').show();
    }
}


/**
 * Submits a media form content
 */
function ajaxSaveMedia(form, ajaxaction) {
    var img = $('#exec\\\[img\\\]').val();
    if (img != '') {
        $(".mediaFile").upload(buildHttpPath('index.php') + '?menuaction=' + 'rulesdresser.ui_media.ajax_upload', function(retour) {
            xajax_doXMLHTTPsync(ajaxaction, xajax.getFormValues(form), retour);
            return true;

        }, 'html');
    } else {
        if (!ajaxaction)
            ajaxaction = form.action.replace(/.+ajaxaction=/, '');
        xajax_doXMLHTTPsync(ajaxaction + './etemplate/process_exec', xajax.getFormValues(form));
    }

}

function addDatePopup(id) {
    $('input[name*="' + id + '"]').after('<img id="exec[' + id + '][str]-trigger" src="' + buildHttpPath('phpgwapi/templates/default/images/datepopup.gif') + '" title="Sélectionner la date" style="cursor:pointer; cursor:hand;">');
}

function setDimension(width, height) {
    if (width != 0 && height != 0) {
        document.write('<input  name="width" value="' + width + '"  type="hidden"  size="40" />');
        document.write('<input  name="height" value="' + height + '" type="hidden"  size="40" />');
    }
}

/**
 * Submits a form by checking required input fields first
 * Form is submitted by using etemplate process_exec function
 */
function ajaxSubmit(form, ajaxaction) {
    if (checkRequiredField()) {
        if (!ajaxaction)
            ajaxaction = form.action.replace(/.+ajaxaction=/, '');
        xajax_doXMLHTTPsync(ajaxaction + './etemplate/process_exec', xajax.getFormValues(form));
    }
}
/**
 * Submits a form by checking required input fields first
 * Form is submitted by no using etemplate process_exec function
 */
function ajaxSubmitSimpleForm(form, ajaxaction) {
    if (checkRequiredField()) {
        if (!ajaxaction)
            ajaxaction = form.action.replace(/.+ajaxaction=/, '');
        xajax_doXMLHTTPsync(ajaxaction, xajax.getFormValues(form));
    }
}

/**
 *Submits a form without checking required input fields
 */
function ajaxSubmitNoCheck(form, ajaxaction) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
    xajax_doXMLHTTPsync(ajaxaction, xajax.getFormValues(form));
}

/**
 *Call an xajax function using one parameter
 */
function ajaxCall(ajaxaction, param) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
   
    xajax_doXMLHTTP(ajaxaction, param);
}
/**
 *Call an xajax function using 2 parameters
 */
function ajaxCall(ajaxaction, param1, param2) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
    xajax_doXMLHTTP(ajaxaction, param1, param2);
}
/**
 *Call an xajax function using 3 parameters
 */
function ajaxCall(ajaxaction, param1, param2, param3) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
    xajax_doXMLHTTP(ajaxaction, param1, param2, param3);
}
/**
 *Call an xajax function using 4 parameters
 */
function ajaxCall(ajaxaction, param1, param2, param3, param4) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
    xajax_doXMLHTTP(ajaxaction, param1, param2, param3, param4);
}
/**
 *Call an xajax function using 5 parameters
 */
//function ajaxCall(ajaxaction, param1, param2, param3, param4, param5) {
//    if (!ajaxaction)
//        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
//    xajax_doXMLHTTP(ajaxaction, param1, param2, param3, param4, param5);
//}

/**
 * delete multiple selected elements
 */
function ajaxMultiDelete(ajaxaction, msg) {
    var checkedLength = $('input[name*="checkbox"]:checked').length;
    if (checkedLength == 0) {
        alert("Aucune case n'a été cochée");
        return false;
    } else {
        if (confirm(msg)) {
            var ids = [];
            i = 0;
            $('input[name*="checkbox"]:checked').each(function(e) {
                ids[i++] = $(this).attr('value');
            });
            ajaxCall(ajaxaction, ids);
            return true;
        }
    }
    return false;
}
/**
 * Close an opened popin
 */
function closePopin(dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }
    $('#' + dialogId).dialog('close');
    return false;
}

function showPopin(dialogId) {
    if (dialogId == '') {
        dialogId = 'dialog';
    }
    $('#' + dialogId).dialog();
    return false;
}


// init javascript for cut and paste
function initCutPaste(appname, classname, suffix) {
    $('body').append('<ul class="contextMenu" id="myMenu"></ul>');
    // re init Show menu when a list item is clicked	
    $(".cutPaste, .popupTrigger").contextMenu({
        menu: 'myMenu'
    }, function(action, el, pos) {
        switch (action) {
            case "cut":
                ajaxCall(appname + '.' + classname + '.ajax_cut' + suffix, $(el).attr('rel'));
                break;
            case "paste":
                ajaxCall(appname + '.' + classname + '.ajax_paste' + suffix, $(el).attr('rel'));
                break;
            case "cancel":
                ajaxCall(appname + '.' + classname + '.ajax_cancel_cut' + suffix, '');
                break;
            default: // for ie
                if (action.indexOf('#cut') != -1) {
                    ajaxCall(appname + '.' + classname + '.ajax_cut' + suffix, $(el).attr('rel'));
                } else if (action.indexOf('#paste') != -1) {
                    ajaxCall(appname + '.' + classname + '.ajax_paste' + suffix, $(el).attr('rel'));
                } else if (action.indexOf('#cancel') != -1) {
                    ajaxCall(appname + '.' + classname + '.ajax_cancel_cut' + suffix, '');
                }
                break;
        }
    });
}
/**
 * Append cut line into contextMenu
 */
function appendCut() {
    $('.contextMenu').html('<li class="cut"><a href="#cut">Couper</a></li>');
}
/**
 * Append paste line into contextMenu
 */
function appendPaste() {
    $('.contextMenu').html('<li class="paste"><a href="#paste">Coller</a></li><li class="cancel"><a href="#cancel">Annuler</a></li>');
}


/**
 *Include a script file 
 */
function include(src)
{
    attributes = {
        charset: "utf-8"
    };
    try {
        attributes = attributes || {};
        attributes.type = "text/javascript";
        attributes.src = src;

        var script = document.createElement("script");
        for (aName in attributes)
            script[aName] = attributes[aName];
        document.getElementsByTagName("head")[0].appendChild(script);
        return true;
    } catch (e) {
        return false;
    }
}


$(function() {
    //disable "ENTER" key
    $('form').bind('keypress', function(e) {
        if (e.keyCode == 13) {
            return false;
        }
        return true;
    });
    $('#dialogContainer').remove();
    $('body').append('<div id="dialogContainer">' +
            '<div id="previewDialog"></div>' +
            '<div id="dialog"></div>' +
            '<div id="dialog2"></div>' +
            '<div id="dialog3"></div>' +
            '<div id="dialog4"></div>' +
            '<div id="push_dialog"></div>' +
            '<div>');
    //adding missing js operations after xajax actions (when etemplate out-put mode = -1 in function exec)
    $('select[name*="exec[nm]"]').each(function() {
        $(this).attr('onchange', 'this.form.submit();');
    });

    $('input[name*=rows]').each(function() {
        var type = $(this).attr('type');
        if (type == 'submit') {
            var id = $(this).attr('id');
            var val = $(this).attr('value');
            $(this).replaceWith('<a href onclick="return submitit(eTemplate,' + id + '); return false;">' + val + '</a>');
        }
    });

}
);
