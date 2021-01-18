<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

$t_language_text_section_content_array = array(
	'about'                           => '<br/><img src="images/qenta-logo.png" alt="Qenta Payment CEE GmbH" /><br /><h3>Qenta Payment CEE GmbH - Ihr Full Service Payment Provider - Alles aus einer Hand</h3><br /><br /> Als unabhängiger Payment-Anbieter begleiten wir Sie in allen Phasen Ihrer Geschäftsentwicklung. Mit maßgeschneiderten Bezahllösungen setzt unser Unternehmen im E-Payment Akzente und ist Österreichs marktführender Payment Service Provider. Persönlich, kompetent und engagiert. <br /><br /> <a href="https://www.qenta-cee.com/" target="_blank">www.qenta-cee.com</a><br /><br />',
	'title'                           => 'Qenta Checkout Seamless',
	'about_support'                   => '<br/><img src="images/qenta-logo.png" alt="Qenta Payment CEE GmbH" /><br /><h3>Qenta Payment CEE GmbH - Ihr Full Service Payment Provider - Alles aus einer Hand</h3><br /><br />',
	'title_support'                   => 'Supportanfrage',
	'title_transferfund'              => 'Auszahlung',
	'about_transferfund'              => '<br/><img src="images/qenta-logo.png" alt="Qenta Payment CEE GmbH" /><br /><h3>Qenta Payment CEE GmbH - Ihr Full Service Payment Provider - Alles aus einer Hand</h3><br /><br />',
	'configure'                       => 'Qenta Checkout Seamless konfigurieren',
	'config_group_basedata'           => 'Basisdaten',
	'config_group_options'            => 'Optionen',
	'config_group_order'              => 'Bestellung',
	'config_group_ccard'              => 'Kreditkarten-Optionen',
	'paymentmethods'                  => 'Zahlungsmittel',
	'config_prod'                     => 'Produktion',
	'configtype'                      => 'Konfiguration',
	'configtype_desc'                 => 'Für die Installation vordefinierte Konfiguration oder "Produktion" für den Livebetrieb auswählen.',
	'config_demo'                     => 'Demo',
	'config_test_no3d'                => 'Test ohne 3-D Secure',
	'config_test_3d'                  => 'Test mit 3-D Secure',
	'param_invalid'                   => 'Der Parameter %s hat einen ungültigen Wert!',
	'param_required'                  => 'Der Parameter %s ist erforderlich!',
	'requestsupport'                  => 'Anfrage an den Qenta-Support stellen',
	'support_request'                 => 'Supportanfrage',
	'support_description'             => 'Problembeschreibung',
	'support_request_send'            => 'Supportanfrage absenden',
	'support_reply_to'                => 'Rückantwort an',
	'support_to'                      => 'Empfänger',
	'support_from'                    => 'Absender',
	'support_subject'                 => 'Supportanfrage via Gambio Online-Shop',
	'support_send_ok'                 => 'Supportanfrage erfolgreich versendet!',
	'support_pluginconfig'            => 'Plugin-Konfiguration:',
	'support_installed_qcs'           => 'Installierte Module:',
	'support_installed_modules'       => 'Fremdmodule:',
	'email_invalid'                   => '%s ist keine gültige E-Mail Adresse',
	'customer_id'                     => 'Customer ID',
	'customer_id_desc'                => 'Ihre Kundennummer bei Qenta Payment CEE GmbH.',
	'shop_id'                         => 'Shop ID',
	'shop_id_desc'                    => 'Kennzeichnung Ihres Shops bei Qenta Payment CEE GmbH.',
	'secret'                          => 'Secret',
	'secret_desc'                     => 'Geheime Zeichenfolge zum Signieren und Prüfen der Daten auf Echtheit.',
	'backendpw'                       => 'Backend-Passwort',
	'backendpw_desc'                  => 'Passwort für Backend-Operationen (Toolkit).',
	'service_url'                     => 'Service URL',
	'service_url_desc'                => 'URL Ihrer Kontakt-/Impressumseite.',
	'saveconfig'                      => 'Konfiguration speichern',
	'testconfig'                      => 'Konfiguration testen',
	'configtest_ok'                   => 'Konfigurationstest ok',
	'field_required'                  => '* erforderlich',
	'active'                          => 'Aktiv',
	'sort_order'                      => 'Anzeigereihenfolge',
	'allowed'                         => 'Erlaubte Zonen',
	'allowed_desc'                    => 'Geben Sie erlaubte Zonen ein (e.g. AT, DE)',
	'send_additional_data'            => 'Detaillierte Kundendaten senden',
	'send_additional_data_desc'       => 'Rechnung- und Lieferadressedaten werden an Qenta Checkout Seamless gesendet.',
	'send_basket'                     => 'Warenkorb-Information senden',
	'send_basket_desc'                => 'Warenkorb-Informationen werden an Qenta Checkout Seamless gesendet.',
	'zone'                            => 'Zahlungszone',
	'zone_desc'                       => 'Wenn eine Zone ausgewählt ist, gilt die Zahlungsmethode nur für diese Zone.',
	'min_amount'                      => 'Minimale Bestellsumme',
	'max_amount'                      => 'Maximale Bestellsumme',
	'datastorage_initerror'           => 'Während der Inititialisierung des Bezahlvorganges ist ein Fehler aufgetreten. Bitte versuchen Sie es noch einmal oder wählen Sie eine andere Zahlungsmethode aus.',
	'docref_title'                    => 'Zur Dokumentation.',
    'button_continue'                 => 'WEITER',
    'button_close'                    => 'SCHLIEßEN',
	'confirm_title'                   => 'Bezahlvorgang',
	'status_init'                     => 'Bezahlung wurde gestartet',
	'status_init_desc'                => '',
	'status_success'                  => 'Bezahlung erfolgreich',
	'status_success_desc'             => '',
	'status_pending'                  => 'Bestätigung ausständig',
	'status_pending_desc'             => '',
	'status_cancel'                   => 'Bezahlung abgebrochen',
	'status_cancel_desc'              => '',
	'status_error'                    => 'Fehler bei der Bezahlung',
	'status_error_desc'               => '',
	'checkout_cancel_title'           => 'Abbruch des Bezahlvorganges',
	'checkout_cancel_content'         => 'Sie haben Ihre Bezahlung abgebrochen!',
	'checkout_failure_title'          => 'Fehler während des Bezahlvorganges',
	'checkout_failure_content'        => 'Ein allgemeiner Fehler ist aufgetreten!',
	'checkout_noconfirm_title'        => 'Fehler während des Bezahlvorganges (Confirm fehlt)',
	'checkout_noconfirm_content'      => 'Während des Bezahlvorganges ist ein Fehler aufgetreten. Bitte versuchen Sie es noch einmal oder wählen Sie eine andere Zahlungsmethode aus.',
	'payment_pending_title'           => 'Zahlungsbestätigung ausstehend',
	'payment_pending_info'            => 'Ihre Zahlung wurde vom Finanzdienstleister noch nicht bestätigt.',
	'fraud_alert'                     => 'Möglicher Betrugsversuch, Warenkorb wurde während des Bezahlvorganges verändert!',
	'shop_name'                       => 'Shop-Name',
	'shop_name_desc'                  => 'Zusatztext auf der Rechnung, max. 9 Zeichen.',
	'send_confirm_email'              => 'Confirmation-E-Mail senden',
	'send_confirm_email_desc'         => 'Wenn der Server-to-Server confirm-Aufruf fehlschlägt, wird ein E-Mail mit den Antwortdaten an Sie geschickt.',
	'auto_deposit'                    => 'Autom. Abbuchen',
	'auto_deposit_desc'               => 'Zahlungen sollen automatisch gebucht werden.',
	'pci3_dss_saq_a_enable'           => 'PCI DSS SAQ A kompatibel',
	'pci3_dss_saq_a_enable_desc'      => 'Verhält sich Qenta Checkout Seamless "PCI DSS SAQ A"-konform.',
	'iframe_css_url'                  => 'Iframe CSS-URL',
	'iframe_css_url_desc'             => 'URL zu einer CSS-Datei, die im iframe inkludiert wird.',
	'creditcard_cardholder'           => 'Karteninhaber',
	'creditcard_cvc'                  => 'Kartenprüfnummer',
	'creditcard_pan'                  => 'Kreditkartennummer',
	'creditcard_expiry'               => 'Ablaufdatum',
	'creditcard_issuedate'            => 'Kartenausgabedatum',
	'creditcard_issuenumber'          => 'Kartenausgabenummer',
	'creditcard_showcardholder'       => 'Karteninhaber anzeigen',
	'creditcard_showcardholder_desc'  => 'Zeigt ein Feld im Kreditkarten-Formular für die Eingabe des Karteninhabers an.',
	'creditcard_showcvc'              => 'Kartenprüfnummer abfragen',
	'creditcard_showcvc_desc'         => 'Zeigt ein Pflichtfeld im Kreditkarten-Formular für die Kartenprüfnummer.',
	'creditcard_showissuedate'        => 'Ausgabedatum anzeigen',
	'creditcard_showissuedate_desc'   => 'Das Kartenausgabedatum steht nicht auf allen Kreditkarten.',
	'creditcard_showissuenumber'      => 'Kartenausgabenummer anzeigen',
	'creditcard_showissuenumber_desc' => 'Die Kartenausgabenummer steht nicht auf allen Kreditkarten.',
	'voucher_voucherid'               => 'Gutscheincode',
	'paybox_payernumber'              => 'paybox-Nummer',
	'sepa_bic'                        => 'BIC',
	'sepa_iban'                       => 'IBAN',
	'sepa_accountowner'               => 'Kontoinhaber',
	'giropay_banknumber'              => 'Bankleitzahl',
	'giropay_accountowner'            => 'Kontoinhaber',
	'giropay_bankaccount'             => 'Kontonummer',
	'financialinstitution'            => 'Finanzinstitut',
    'birthdate'                       => 'Geburtsdatum',
    'birthdate_too_young'             => 'Sie müssen 18 Jahre oder älter sein, um dieses Zahlungsmittel verwenden zu können.',
	'paymentnumber'                   => 'Zahlungsnummer',
	'approveamount'                   => 'Genehmigt',
	'depositamount'                   => 'Abgebucht',
	'orderstate'                      => 'Status',
	'timecreated'                     => 'Erzeugt am',
	'operation'                       => 'Operation',
    'payolution_mid'                  => 'payolution mID',
    'payolution_mid_desc'             => 'payolution-Händler-ID, Nicht base64 kodiert.',
    'payolution_terms_error'          => 'Bitte akzeptieren Sie die Einwilligung.',
    'payolution_terms'                => 'Payolution Nutzungsbedingungen',
    'payolution_terms_desc'           => 'Kunden müssen die Nutzungsbedingungen von payolution während des Bezahlprozesses akzeptieren.',
    'consent_text'                    => 'Mit der Übermittlung jener Daten an payolution, die für die Abwicklung von Zahlungen mit Kauf auf Rechnung und die Identitäts- und Bonitätsprüfung erforderlich sind, bin ich einverstanden. Meine _Einwilligung_ kann ich jederzeit mit Wirkung für die Zukunft widerrufen.',
	'payment_approved'                => 'Genehmigt',
	'payment_deposited'               => 'Abgebucht',
	'payment_closed'                  => 'Abgeschlossen',
	'payment_approvalexpired'         => 'Abgelaufen',
	'credit_refunded'                 => 'Rückerstattet',
	'credit_closed'                   => 'Abgeschlossen',
	'approvereversal'                 => 'Gen. rückgängig',
	'deposit'                         => 'Abbuchen',
	'depositreversal'                 => 'Abbuchen rückgängig',
	'refund'                          => 'Gutschrift',
	'refundreversal'                  => 'Rückgängig',
	'transferfund'                    => 'Auszahlung',
	'credits'                         => 'Gutschriften',
	'credit_add'                      => 'Gutschrift hinzufügen',
	'payments'                        => 'Zahlungen',
	'creditnumber'                    => 'Gutschrift-Nr.',
	'amount'                          => 'Betrag',
	'creditstate'                     => 'Status',
	'amount_invalid'                  => 'Betrag ist ungültig',
	'invoiceinstallment_provider'     => 'Finanzdienstleister',
	'currency'                        => 'Währung',
	'currencies'                      => 'Erlaubte Währungen',
	'currencies_desc'                 => 'Erlaubte Währungen RatePay (e.g. EUR,CHF)',
    'equal_address'                   => 'Rechnungsadresse und Versandadresse müssen übereinstimmen',
	'customergroup'                   => 'Kundengruppe',
	'customergroup_desc'              => 'Zahlungsmittel ist nur für diese Kundengruppe verfügbar',
	'transfer_type'                   => 'Typ',
	'transfer_existingorder'          => 'Bestehende Zahlung',
	'transfer_skrillwallet'           => 'Skrill Digital Wallet',
	'transfer_moneta'                 => 'moneta.ru',
	'transfer_sepact'                 => 'SEPA-CT',
	'transfer_execute'                => 'Auszahlung durchführen',
	'sourceordernumber'               => 'Quell-Auftragsnummer',
	'ordernumber'                     => 'Auftragsnummer',
	'orderdescription'                => 'Auftragsbeschreibung',
	'orderreference'                  => 'Auftragsreferenz',
	'customerstatement'               => 'Abrechnungstext',
	'consumeremail'                   => 'E-mail-Adresse des Konsumenten',
	'consumerwalletid'                => 'Wallet-ID des Konsumenten',
	'bankaccountowner'                => 'Kontoinhaber',
	'bankbic'                         => 'BIC',
	'bankaccountiban'                 => 'IBAN',
	'transferfund_ok'                 => 'Auszahlung erfolgreich gebucht!',
	'delete_failure'                  => 'Bestellungen bei fehlgeschlagener Zahlung l&ouml;schen',
	'delete_failure_desc'             => 'Falls aktiviert, werden die Bestellungen bei fehlgeschlagener Zahlung gel&ouml;scht.',
	'delete_cancel'                   => 'Bestellungen bei abgebrochener Zahlung l&ouml;schen',
	'delete_cancel_desc'              => 'Falls aktiviert, werden die Bestellungen bei abgebrochener Zahlung gel&ouml;scht.',
);

