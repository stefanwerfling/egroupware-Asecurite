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


/**
 *(for rulesdresser)
 * Check a choosen media dimension contigent to form authorized dimensions
 * that operation his performed from server side
 * @param myaction, method from server side that perform this operation
 * @param mediaId1, media1 id
 * @param mediaId2, media1 id
 */
function fillMediasName(myaction, mediaId1, mediaId2) {
    if (!myaction)
        myaction = form.action.replace(/.+myaction=/, '');
    xajax_doXMLHTTPsync(myaction, mediaId1, mediaId2);
    //validateForm();
}

/**
 * Fill alt field if empty
 */
function fillAltField(altValue) {
    var ALT = $('input[name*="_ALT"]').val();
    if (ALT == '') {
        $('input[name*="_ALT"]').val(altValue);
        $('input[name*="_ALT"]').after("<span id='error'>La valeur de ce champ n'a pas encore \n été enregistrée.</span>");
    }
}
/**
 *(for rulesdresser)
 * Reload media html container to display searched medias
 * @param myaction, ajax method that perform this operation from server
 */
function reloadMediaList(myaction) {
    if (!myaction)
        myaction = form.action.replace(/.+myaction=/, '');
    var formWidth = $('input[name="width"]').attr('value');
    var formHeight = $('input[name="height"]').attr('value');

    var name = $('#exec\\\[_media_name\\\]').attr('value');
    var type = $('#exec\\\[_media_type\\\]').attr('value');
    var tag = $('#exec\\\[_media_tag\\\]').attr('value');
    xajax_doXMLHTTPsync(myaction, name, formWidth, formHeight, type, tag);
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


/** Add a method for using regex to the jquery.validator plugin
 * 
 * To use it :
 *   "montelephone" : {
 *      "required": true,
 *      "regex": /^(\+33\.|0)[0-9]{9}$/
 *   }
 */
function addValidateMethodRegex() {
    jQuery.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                if (regexp.constructor != RegExp)
                    regexp = new RegExp(regexp);
                else if (regexp.global)
                    regexp.lastIndex = 0;
                return this.optional(element) || regexp.test(value);
            }, "Le format est invalide"
            );
}

/**
 * Add a rule for an e-mail validation
 * @param locatedElement
 * @param isRequired
 * http://www.pierrefay.fr/jquery-validate-formulaire-validation-tutoriel-455
 */
function validateEmail(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        email: true,
        maxlength: 255,
        required: isRequired,
        messages: {
            email: "Veuillez saisir un email valide de la forme nom@domaine.tld, merci "
        }
    }
    );
}

/**
 * Add a rules for URL validaton
 * @param locatedElement
 * @param isRequired
 */
function validateURL(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        url: true,
        maxlength: 255,
        required: isRequired,
        messages: {
            url: "Veuillez saisir une URL valide, merci"
        }
    }
    );
}

/**
 * Digits only
 * @param locatedElement
 * @param isRequired
 */
function validateDigits(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        digits: true,
        maxlength: 255,
        required: isRequired,
        messages: {
            digits: "Veuillez ne saisir que des chiffres, merci"
        }
    }
    );
}

/**
 * Decimal number
 * @param locatedElement
 * @param isRequired
 */
function validateNumber(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        number: true,
        maxlength: 255,
        required: isRequired,
        messages: {
            number: "Veuillez entrer un nombre décimal, merci"
        }
    }
    );
}

/**
 * Add a rule for a phone number validation
 * @param locatedElement
 * @param isRequired
 */
function validatePhone(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        minlength: 10,
        required: isRequired,
        "regex": /^(\+33\.|0)[0-9]{9}$/,
        messages: {
            minlength: jQuery.format("Veuillez saisir au moins {0} chiffres, merci ")
        }
    }
    );
}

/**
 * Add a rule for a phone number validation
 * @param locatedElement
 * @param isRequired
 */
function validateRegex(locatedElement, isRequired, theRegex, theErrorMessage) {
    $(locatedElement).rules(
            "add", {
        required: isRequired,
        "regex": theRegex,
        messages: {
            "regex": theErrorMessage
        }
    }
    );
}

/**
 * Add a rule for a phone number validation
 * @param locatedElement
 * @param isRequired
 */
function validateAlphaNum(locatedElement, isRequired) {
    $(locatedElement).rules(
            "add", {
        required: isRequired,
        "regex": /^[A-Za-z0-9\-. _àéêèïôç²#+=!?;,:ù%€@<>|\[\]]*$/,
        messages: {
            "regex": jQuery.format("Veuillez entrer des caractères alpha-numeriques, merci")
        }
    }
    );
}

/**
 * These function must be added on the submit button of a form 
 */
function validateForm() {
    $(document).ready(function() {
        addValidateMethodRegex();
        $('form[name="eTemplate"]').validate();
    });
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
    $('#' + dialogId).html('<center><span><img src="' + buildHttpPath("phpgwapi/templates/default/advise_css/images/loading.gif") + '"></span></center>');
    if (height == 0) {
        height = 'auto';
    }
    if (width == 0) {
        width = 'auto';
    }

    if (link.indexOf('ajax') != -1) {
        //multi-param as id
        $('#' + dialogId).dialog({
            title: titleBar,
            autoResize: true,
            height: height,
            width: width

        });
        if (link.indexOf('ui_rules') != -1) {
            xajax_doXMLHTTP(link, id, dialogId);
        } else {
            xajax_doXMLHTTPsync(link, id, dialogId);
        }

    } else {
        $('#' + dialogId).dialog({
            open: function() {
                $(this).html('<iframe id="TB_iframeContent" src="' + link + '" width="' + width + '" height="' + height + '"></iframe>');
            },
            title: titleBar,
            close: $(this).text(''),
            width: width + 40,
            height: height + 50,
            resize: function() {
                $('#TB_iframeContent').css('height', $(this).height() - 10 + 'px').css('width', $(this).width() - 10 + 'px');
            }
        });
    }
}

/**
 * Check required input fields
 */
function checkRequiredField() {
    var ok = true;
    $('.inputRequired input, .inputRequired select').each(function(e) {
        if ($(this).attr('value') == '') {
            var id = 'required' + e;
            $('#' + id).html('');
            $(this).after('<span id="' + id + '" style="color:red">Le champ ne doit pas être vide!</span>');
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

function checkSaveMedia(save) {
    var domain = $('.editMedia select[name="exec[idrules_dresser_domain]"]').attr('value');
    var mediaType = $('.editMedia select[name="exec[media_type]"]').attr('value');
    if (domain == '') {
        alert("Veuillez choisir un domain s'il vous plait.");
        return false;
    }
    else if (mediaType == '') {
        alert("Veuillez choisir d'abord choisir un type s'il vous plait.");
        return false;
    }
    else {
        mediaType = mediaType.toLowerCase();
        if (mediaType == 'magic' || mediaType == 'http') {
            if ($('.editMedia input[name="exec[link]"]').attr('value') == '') {
                alert("Le champ 'Média' est vide!");
                return false;
            }
            return true;
        } else if (save) {
            if ($('.editMedia input[name="exec[img]"]').attr('value') == '') {
                alert("Veuillez choisir un média SVP.");
                return false;
            }
        }
        var mediaName = $('.editMedia input[name="exec[img]"]').attr('value');
        if (mediaName != '') {
            var isOk = checkImgPath();
            if (!isOk)
                return false;
            var extPos = mediaName.lastIndexOf('.');
            if (extPos != -1) {
                var ext = mediaName.substr(extPos + 1);
                ext = ext.toLowerCase();
                if (mediaType == 'image' && ext != 'jpg' && ext != 'jpeg' && ext != 'png' && ext != 'gif') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'jpg, jpeg, png et gif');
                    return false;
                } else if (mediaType == 'word' && ext != 'doc' && ext != 'docx') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'doc et docx')
                    return false;

                } else if (mediaType == 'excel' && ext != 'xls' && ext != 'xls') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'xls et xlsx')
                    return false;

                } else if (mediaType == 'power point' && ext != 'ppt' && ext != 'pptx') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'ppt et pptx')
                    return false;

                } else if (mediaType == 'zip' && ext != 'zip') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'zip')
                    return false;
                } else if (mediaType == 'pdf' && ext != 'pdf') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'pdf')
                    return false;

                } else if (mediaType == 'postscript' && ext != 'ps') {
                    showCheckSaveMediaErrMsg(mediaName, mediaType, 'ps')
                    return false;
                }
            } else {
                alert("'" + mediaName + "', Type de média inconnu.");
                return false;
            }
        }
    }
    return true;
}

function showCheckSaveMediaErrMsg(mediaName, mediaType, extentions) {
    alert("'" + mediaName + "', Extension non valide pour le type ''" + mediaType + "'.\nExtension(s) autorisée(s) pour le type '" + mediaType + "':\n" + extentions);
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


function setDimension(width, height) {
    if (width != 0 && height != 0) {
        document.write('<input  name="width" value="' + width + '"  type="hidden"  size="40" />');
        document.write('<input  name="height" value="' + height + '" type="hidden"  size="40" />');
    }
}
function checkBoiMandatoryFields() {
    if ($('select[name*="img"]').attr('value') == '') {
        alert('Veuillez choisir une image');
        return false;
    } else if ($('input[name*="alt"]').attr('value') == '') {
        //alert ("Le champ 'Alt' est vide");
        return confirm("Attention, le champ 'Alt' est vide ! Voulez-vous poursuivre l'enregistrement ?");
    } else if ($('input[name*="url1"]').attr('value') == '') {
        //alert ("Le champ 'URL1' est vide");
        return confirm("Attention, le champ 'URL1' est vide ! Voulez-vous poursuivre l'enregistrement ?");
    }
    return true;
}

function checkHpcMandatoryFields() {
    if ($('select[name*="img"]').attr('value') == '') {
        alert('Veuillez choisir une image');
        return false;
    } else if ($('input[name*="alt"]').attr('value') == '') {
        //alert ("Le champ 'Alt' est vide");
        return confirm("Attention, le champ 'Alt' est vide ! Voulez-vous poursuivre l'enregistrement ?");
    } else if ($('input[name*="url1"]').attr('value') == '') {
        //alert ("Le champ 'URL1' est vide");
        return confirm("Attention, le champ 'URL1' est vide ! Voulez-vous poursuivre l'enregistrement ?");
    }
    return true;
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
function ajaxCall(ajaxaction, param1, param2, param3, param4, param5) {
    if (!ajaxaction)
        ajaxaction = form.action.replace(/.+ajaxaction=/, '');
    xajax_doXMLHTTP(ajaxaction, param1, param2, param3, param4, param5);
}

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
