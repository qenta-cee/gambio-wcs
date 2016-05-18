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

$(document).ready(function () {

	var wirecardCheckoutSeamless = {
		pci3Iframes: {},

		init: function () {
			var self = this;

			if (typeof WirecardCEE_DataStorage !== "undefined") {
				var wdcee = new WirecardCEE_DataStorage();
				if ($('#wcsIframeContainerwcs_ccard').length > 0) {
					this.pci3Iframes.CCARD = wdcee.buildIframeCreditCard('wcsIframeContainerwcs_ccard', '262px', '203px');
					wdcee.onIframeClick(self.handleIframeClick);
				} else if ($('#wcsIframeContainerwcs_ccardmoto').length > 0) {
					this.pci3Iframes.CCARD = wdcee.buildIframeCreditCard('wcsIframeContainerwcs_ccardmoto', '262px', '200px');
					wdcee.onIframeClick(self.handleIframeClick);
				}
			}

			var checkoutPaymentArea = $('#checkout_payment');

			checkoutPaymentArea.find('.continue_button').find('a').on('vclick', function (e) {
				var code = checkoutPaymentArea.find("input[name='payment']:radio:checked").val();
				// not a wirecard payment
				if (!code.match(/^wcs_/))
					return true;

				if (checkoutPaymentArea.find('.errorText').length == 0) {
					checkoutPaymentArea.find('h2').first().after('<div class="errorText"></div>');
				}

				checkoutPaymentArea.find('.errorText').empty();

				var field = code + '_type';
				var paymentTypeField = checkoutPaymentArea.find("input[name='" + field + "']");
				var paymentType = paymentTypeField.val();
				var form = $(this).closest('form');

				var paymentInformation = null;

				$("#checkout_payment ." + code).each(function () {
					var fieldname = $(this).attr('data-wcs-fieldname');
					if (typeof fieldname == 'undefined')
						return true;

					if (paymentInformation === null) {
						paymentInformation = {};
					}
					paymentInformation[fieldname] = $(this).val();
				});

				if (paymentInformation !== null || self.hasIframe(paymentType))
				{
					if (paymentInformation === null)
						paymentInformation = {};

					paymentInformation.paymentType = paymentType;
					var DataStorage = new WirecardCEE_DataStorage();
					var ret = DataStorage.storePaymentInformation(paymentInformation, function (responseObject) {

						if (responseObject.getStatus() == 0) {

							var params = responseObject.getAnonymizedPaymentInformation();
							for (var x in params) {
								var field = document.createElement('input');
								field.type = 'hidden';
								field.name = x;
								field.value = params[x];
								$(paymentTypeField).after(field);
							}

							form.submit();

						} else {
							// suppress errors in iframe mode
							if (wirecardCheckoutSeamless.hasIframe(paymentType)) {
								$('html, body').animate({scrollTop: $("#checkout_payment .payment_item.active").offset().top}, 'slow');
								return;
							}
							var errors = responseObject.getErrors();
							for (var x in errors) {
								if (errors[x].consumerMessage) {
									self.showError(errors[x].consumerMessage, 'APPEND');
								}
								else {
									self.showError(errors[x].message, 'APPEND');
								}
							}
							$('html, body').animate({scrollTop: 0}, 'slow');
						}
					});

					// no postMessage support, issue a read request and check if valid paymentdata is available
					if (ret === null) {
						wirecardCheckoutSeamless.datastorageRead(function (data) {
							if (data.status == 1) {
								$(checkoutForm).submit();
							} else {
								self.showError('', 'CLEAR');
								self.showError(noPaymentdataMessage, 'APPEND');
								$('html, body').animate({scrollTop: 0}, 'slow');
							}
						});
					}

					return false;
				}

				if (paymentInformation === null) {
					return true;
				}

				return false;
			});

		},

		hasIframe: function (paymenttype) {
			return typeof this.pci3Iframes[paymenttype] !== "undefined";
		},

		getIframe: function (paymenttype) {
			return this.pci3Iframes[paymenttype];
		},

		datastorageRead: function (callback) {
			$.ajax({
				type:        "POST",
				url:         wirecardDatastorageReadUrl,
				dataType:    'json',
				contentType: 'application/json'
			}).done(function (ret) {
				callback(ret);
			});
		},

		showError: function (message, type) {

			//if (container.css('display') == "none") {
			//	container.css('display', "block");
			//}
			var container = $('#checkout_payment .errorText');
			switch (type) {
				case 'APPEND':
					$(container).append('<p>' + message + '</p>');
					break;
				case 'CLEAR':
					$(container).empty();
					break;
				case 'NEW':
					$(container).html('<p>' + message + '</p>');
					break;
				default:
					this.showError('Invalid error display-Type' + type, 'APPEND');
			}
		},

		saveOrder: function () {
			$.ajax({
				type:        "GET",
				url:         wirecardSaveOrderUrl,
				dataType:    'json',
				contentType: 'application/json; charset=utf-8',
				context:     this
			}).always(function (response, textStatus, jqXHR) {
				if (jqXHR.status != 200) {
					document.location.href = wirecardCheckoutConfirmUrl;
				}
				else {
					if (response.redirectUrl && response.useIframe == false) {
						document.location.href = response.redirectUrl;
					}
					else if (response.redirectUrl && response.useIframe == true) {
						var iframe = document.createElement('iframe');
						$(iframe).on('load', function () {
							$.loadingIndicator.close();
						});
						iframe.setAttribute("src", response.redirectUrl);
						iframe.setAttribute("id", "paymentIframe");
						iframe.setAttribute("name", "paymentIframe");
						$('#iframeContainer').append(iframe);
					}
					else {
						document.location.href = wirecardCheckoutConfirmUrl;
					}
				}
			});
		},

		iframeBreakout: function (redirectUrl) {
			parent.location.href = redirectUrl;
		},

		handleIframeClick: function (event) {
			$('div.payment_item').removeClass('active');
			$('#' + event.data.parentId).closest( "div.payment_item" ).addClass('active');

			if (event.data.parentId == 'wcsIframeContainerwcs_ccard') {
				$("input[value='wcs_ccard']").prop("checked", true);
			} else {
				$("input[value='wcs_ccardmoto']").prop("checked", true);
			}
		}

	};


	wirecardCheckoutSeamless.init();
});