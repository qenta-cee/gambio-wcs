/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

gx.compatibility.module(
    'orders_wcs',

    ['xhr'],

    function (data) {

        'use strict';

        // ------------------------------------------------------------------------
        // VARIABLES DEFINITION
        // ------------------------------------------------------------------------

        var
            /**
             * Module Selector
             *
             * @var {object}
             */
            $this = $(this),

            /**
             * Modal Selector
             *
             * @type {object}
             */
            $modal = $('#modal_layer_container'),

            /**
             * Checkboxes Selector
             *
             * @type {object}
             */
            $checkboxes = $('.gx-orders-table tr:not(.dataTableHeadingRow) input'),

            /**
             * Default Options
             *
             * @type {object}
             */
            defaults = {
                detail_page: false,
                comment: ''
            },

            /**
             * Final Options
             *
             * @var {object}
             */
            options = $.extend(true, {}, defaults, data),

            /**
             * Module Object
             *
             * @type {object}
             */
            module = {};

        // ------------------------------------------------------------------------
        // PRIVATE FUNCTIONS
        // ------------------------------------------------------------------------

        var _openAddCreditDialog = function (event) {
            var $form = $('#add_credit_form');

            event.preventDefault();

            $form.dialog({
                'title': jse.core.lang.translate('credit_add', 'wirecard_checkout_seamless'),
                'modal': true,
                'dialogClass': 'gx-container',
                'buttons': _getModalButtons($form),
                'width': 400
            });
        };

        var _getModalButtons = function ($form) {
            var buttons = [
                {
                    'text': jse.core.lang.translate('close', 'buttons'),
                    'class': 'btn',
                    'click': function () {
                        $(this).dialog('close');
                    }
                }
            ];
            buttons.push({
                'text': jse.core.lang.translate('refund', 'wirecard_checkout_seamless'),
                'class': 'btn btn-primary',
                'click': function (event) {
                    var data = {
                        operation: 'refund',
                        payment:   $('#refund_payment').val(),
                        amount:	   $('#refund_amount').val()
                    };

                    $.ajax({
                        type:     "POST",
                        url:      'qenta_checkout_seamless_backend.php',
                        dataType: 'json',
                        context:  null,
                        data:     data
                    }).always(function (jqXHR, textStatus, errorThrown) {
                        location.reload(true);
                    });
                }
            });

            return buttons;
        };

        // ------------------------------------------------------------------------
        // INITIALIZATION
        // ------------------------------------------------------------------------

        module.init = function (done) {
            $this.on('click', _openAddCreditDialog);

            done();
        };

        return module;
    });
