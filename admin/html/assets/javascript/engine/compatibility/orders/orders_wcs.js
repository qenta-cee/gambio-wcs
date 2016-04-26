/**
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between Wirecard
 * Central Eastern Europe (subsequently referred to as WDCEE) and it's
 * contractual partners (subsequently referred to as customer or customers)
 * which are related to the use of plugins provided by WDCEE.
 *
 * The Plugin is provided by WDCEE free of charge for it's customers and
 * must be used for the purpose of WDCEE's payment platform integration
 * only. It explicitly is not part of the general contract between WDCEE
 * and it's customer. The plugin has successfully been tested under
 * specific circumstances which are defined as the shopsystem's standard
 * configuration (vendor's delivery state). The Customer is responsible for
 * testing the plugin's functionality before putting it into production
 * enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's
 * full functionality neither does WDCEE assume liability for any
 * disadvantage related to the use of this plugin. By installing the plugin
 * into the shopsystem the customer agrees to the terms of use. Please do
 * not use this plugin if you do not agree to the terms of use!
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
                        url:      'wirecard_checkout_seamless_backend.php',
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