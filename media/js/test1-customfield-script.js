(function($) {

    if (!Joomla) {
        throw new Error('core.js was not properly initialized');
    }

    $(document).ready(function() {

        var elements = document.querySelectorAll('.select-link');
        var ParamsFromCustomField = Joomla.getOptions('com_test1');
        var CustFieldExtName = ParamsFromCustomField['CustFieldExtName'];
        var CustFieldExtId = ParamsFromCustomField['CustFieldExtId'];

        for (var i = 0, l = elements.length; l > i; i += 1) {
            if (elements[i].getAttribute('data-function') == 'btn-' +  CustFieldExtId+'Func')
            {
                elements[i].addEventListener('click', function (event) {
                    event.preventDefault();
                    CustomFieldFunc();
                });
            }
        }
    });

    /*---------------------------------------------------------------------------*/
    function CustomFieldFunc() {
        var ParamsFromCustomField = Joomla.getOptions('com_test1');
        var CustFieldExtName = ParamsFromCustomField['CustFieldExtName'];
        var CustFieldExtId = ParamsFromCustomField['CustFieldExtId'];

        var FuncLangText = '<p>'+Joomla.Text._('COM_TEST1_AJAX_TEXT')+'</p>';
        jQuery('#input-text-' + CustFieldExtId).val('NewValue');

    }

})(jQuery);







