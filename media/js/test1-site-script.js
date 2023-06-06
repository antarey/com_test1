
(function($) {
    'use strict';
    if (!Joomla) {
        throw new Error('core.js was not properly initialized');
    }

    $(document).ready(function() {
        var elements = document.querySelectorAll('.select-link');
        for (var i = 0, l = elements.length; l > i; i += 1) {
            elements[i].addEventListener('click', function (event) {
                event.preventDefault();
                var functionName = event.target.getAttribute('data-function');
                if (functionName == 'jAjaxBtnFunc')SiteScriptAjaxFunc();
                if (functionName == 'jClearAjaxFunc')$("#DivAjaxResult").html('');
            });
        }
    });

    /*---------------------------------------------------------------------------*/
    function SiteScriptAjaxFunc() {

        var SomeAjaxParam = 1;
        var ParamsFromPhp = Joomla.getOptions('com_test1');
        var ParamFromPhp = ParamsFromPhp['ParamFromPhp'];
        var token = Joomla.getOptions("csrf.token", "");

        var NewHtml = '';
        var AjaxLang = '<p>'+Joomla.Text._('COM_TEST1_AJAX_TEXT')+'</p>';
        NewHtml += AjaxLang;
        if (ParamFromPhp == 1)
        {
            NewHtml += '<div>The result of processing the parameters that are passed from PHP to Ajax</div>';
        }

        $.ajax({
            data: { [token]: "1", task: "AjaxFunkOnServer", tmpl: "component",format: "raw",SomeAjaxParam: SomeAjaxParam},
            success: function(responce) {
                NewHtml += responce;
                $("#DivAjaxResult").html(NewHtml);
            },
            error: function(responce) {
                Joomla.renderMessages({
                    error: [Joomla.Text._('ERROR')+':<br><b>'+responce.statusText+'<b><br>'+responce.responseText]
                });
                //console.log(UnicodeStrToText(responce.responseText));
                console.log(responce.responseText);

            },
        });
    }
    /*------------------- Конвертація \u... в текст -----------------------------*/
    function UnicodeStrToText(text) {
        return text.replace(/\\u[\dA-F]{4}/gi,
            function (match) {
                return String.fromCharCode(parseInt(match.replace(/\\u/g, ''), 16));
            });
    }
    /*---------------------------------------------------------------------------*/




})(jQuery);






