<?php
class ControllerToolIkros extends Controller {
	private $error = array();
	private $ssl = 'SSL';

	public function __construct( $registry ) {
		parent::__construct( $registry );
		$this->ssl = (defined('VERSION') && version_compare(VERSION,'2.2.0.0','>=')) ? true : 'SSL';
	}


	public function index() {
		$this->load->language('tool/ikros');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('tool/ikros');

		$this->getForm();
	}


	public function products() {
		$this->load->language( 'tool/ikros' );
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model( 'tool/ikros' );

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->download();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
		}

		$this->getForm();
	}


    public function orders() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->exportOrders();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
        }

        $this->getForm();
    }


    /*******************************************************************************************************************
     * Faktura
     ******************************************************************************************************************/
    public function invoices() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->exportInvoices();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Nastavenia
     ******************************************************************************************************************/
	public function settings() {
		$this->load->language('tool/ikros');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('tool/ikros');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validateSettingsForm())) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('ikros', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_settings');
			$this->response->redirect($this->url->link('tool/ikros', 'token=' . $this->session->data['token'], $this->ssl));
		}

		$this->getForm();
	}
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Maintenance do pivot tabuľky dokumentov prekopíruje objednávky s dátumom poslednej modifikácie
     * Objednávky sa pri najbližšom importe nebudú odosielať
     ******************************************************************************************************************/
    public function fillpivotdocument() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->fillPivotDocument();
            $this->response->redirect($this->url->link('tool/ikros', 'token=' . $this->session->data['token'], $this->ssl));
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * Maintenance: Pivot tabuľka dokumentov sa vyprázdni. Všetky dokumenty sa pri najbližšom exporte odošlú
     ******************************************************************************************************************/
    public function clearpivotdocument() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->clearPivotDocument();
            $this->response->redirect($this->url->link('tool/ikros', 'token=' . $this->session->data['token'], $this->ssl));
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * Servisná funkcia obnova produktov
     ******************************************************************************************************************/
    public function clean_db() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->restore();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Servisná funkcia mazanie produktov
     ******************************************************************************************************************/
    public function evakuate() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->checkPermission()) {
            $this->model_tool_ikros->evakuate();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Servisná funkcie vytvorenie pivot tabuľky
     ******************************************************************************************************************/
    public function pivot() {
        $this->load->language( 'tool/ikros' );
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model( 'tool/ikros' );

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_tool_ikros->createPivot();
            $this->response->redirect( $this->url->link( 'tool/ikros', 'token='.$this->request->get['token'], $this->ssl) );
        }

        $this->getForm();
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Vizuál administrácie
     ******************************************************************************************************************/
	protected function getForm() {

        $this->load->model('tool/ikros');

		$data = array();
		$data['heading_title'] = $this->language->get('heading_title');


        //default stock
        $data['text_products'] = $this->language->get( 'text_products' );   //fieldset
        $data['help_defaul_stock_id'] = $this->language->get( 'help_defaul_stock_id' );
        $data['entry_defaul_stock_id'] = $this->language->get( 'entry_defaul_stock_id' );
        //posledný import
        $data['entry_last_import'] = $this->language->get('entry_last_import');
        $data['entry_last_import_default'] = $this->language->get('entry_last_import_default');
        $data['help_last_import'] = $this->language->get('help_last_import');
        //popis položky
        $data['entry_product_description'] = $this->language->get('entry_product_description');
        $data['help_product_description'] = $this->language->get('help_product_description');
        //objednávka
        $data['text_orders'] = $this->language->get( 'text_orders' );   //fieldset
        $data['entry_order_start_text'] = $this->language->get( 'entry_order_start_text' ); //uvodny text objednávky
        $data['entry_order_end_text'] = $this->language->get( 'entry_order_end_text' ); //záverečný text objednávky
        //faktúra
        $data['entry_invoices_start_text'] = $this->language->get( 'entry_invoices_start_text' ); //uvodny text faktury
        $data['entry_invoices_end_text'] = $this->language->get( 'entry_invoices_end_text' );   //záverečný text faktury
        //meno ako adresna polozka firma/ico
        $data['entry_company_replacement'] = $this->language->get( 'entry_company_replacement' );
        $data['help_company_replacement'] = $this->language->get( 'help_company_replacement' );
        //poslat pole firma do shipping adresy
        $data['entry_ico_in_shipping'] = $this->language->get( 'entry_ico_in_shipping' );
        $data['help_ico_in_shipping'] = $this->language->get( 'help_ico_in_shipping' );
        //stav objednvky
        $data['entry_order_status'] = $this->language->get( 'entry_order_status' );
        $data['help_order_status'] = $this->language->get( 'help_order_status' );
        //percento zľavy za doklad
        $data['entry_document_discount'] = $this->language->get( 'entry_document_discount' );
        $data['help_document_discount'] = $this->language->get( 'help_document_discount' );
        //dátum splatnosti faktúry
        $data['entry_due_date'] = $this->language->get( 'entry_due_date' );
        $data['help_due_date'] = $this->language->get( 'help_due_date' );
        //daňová trieda pre dopravu
        $data['entry_tax_class'] = $this->language->get( 'entry_tax_class' );
        $data['help_tax_class'] = $this->language->get( 'help_tax_class' );
        $data['text_none'] = $this->language->get( 'text_none' );   //ziadna daň
        //daňová trieda pre kupon
        $data['entry_coupon_tax_class'] = $this->language->get( 'entry_coupon_tax_class' );
        $data['help_coupon_tax_class'] = $this->language->get( 'help_coupon_tax_class' );
        //PERSONAL
        $data['text_personal'] = $this->language->get( 'text_personal' );   //fieldset
        //číslo účtu
        $data['entry_sender_bank_account'] = $this->language->get( 'entry_sender_bank_account' );
        $data['help_sender_bank_account'] = $this->language->get( 'help_sender_bank_account' );
        $data['error_sender_bank_account'] = $this->language->get( 'error_sender_bank_account' );
        //iban
        $data['entry_sender_bank_iban'] = $this->language->get( 'entry_sender_bank_iban' );
        $data['help_sender_bank_iban'] = $this->language->get( 'help_sender_bank_iban' );
        //swift
        $data['entry_sender_bank_swift'] = $this->language->get( 'entry_sender_bank_swift' );
        $data['help_sender_bank_swift'] = $this->language->get( 'help_sender_bank_swift' );
        //formát čísla dokumentu
        $data['help_convert_order_number'] = $this->language->get( 'help_convert_order_number' );
        $data['entry_convert_order_number'] = $this->language->get( 'entry_convert_order_number' );
        $data['entry_custom_format'] = $this->language->get( 'entry_custom_format' ); //checkbox
        //auth
        $data['text_connect'] = $this->language->get( 'text_connect' );
        $data['entry_authorization_key'] = $this->language->get( 'entry_authorization_key' );
        $data['help_authorization_key'] = $this->language->get( 'help_authorization_key' );
        //servis
       // $data['entry_start_app'] = $this->language->get( 'entry_start_app' );
        //neplatna licencia
        $data['entry_entry_key'] = $this->language->get( 'entry_entry_key' );

        //cron
        $data['text_cron'] = $this->language->get( 'text_cron' );
        $data['entry_cron'] = $this->language->get( 'entry_cron' );
        $data['help_cron'] = $this->language->get( 'help_cron' );
        //cron heslo
        $data['entry_cron_key'] = $this->language->get( 'entry_cron_key' );
        $data['help_cron_key'] = $this->language->get( 'help_cron_key' );

        $data['entry_step_document'] = $this->language->get( 'entry_step_document' );
        $data['help_step_document'] = $this->language->get( 'help_step_document' );

        $data['entry_fill_pivot_document'] = $this->language->get( 'entry_fill_pivot_document' );
        $data['help_fill_pivot_document'] = $this->language->get( 'help_fill_pivot_document' );
        $data['entry_clear_pivot_document'] = $this->language->get( 'entry_clear_pivot_document' );
        $data['help_clear_pivot_document'] = $this->language->get( 'help_clear_pivot_document' );


        $data['text_maintenance'] = $this->language->get( 'text_maintenance' );



        //buttons
        $data['button_export_orders'] = $this->language->get( 'button_export_orders' );
        $data['button_export_invoices'] = $this->language->get( 'button_export_invoices' );
        $data['button_import_products'] = $this->language->get( 'button_import_products' );
        $data['button_settings'] = $this->language->get( 'button_settings' );
        $data['button_initial'] = $this->language->get( 'button_initial' );
        $data['button_delete_products'] = $this->language->get( 'button_delete_products' );
        $data['button_clean_db'] = $this->language->get( 'button_clean_db' );
        $data['button_active_document'] = $this->language->get( 'button_active_document' );


        //tab
        $data['tab_general'] = $this->language->get( 'tab_general' );
        $data['tab_settings'] = $this->language->get( 'tab_settings' );
        $data['tab_import_export'] = $this->language->get( 'tab_import_export' );
        $data['tab_delete'] = $this->language->get( 'tab_delete' );
        $data['tab_initialization'] = $this->language->get( 'tab_initialization' );
        $data['tab_tool'] = $this->language->get( 'tab_tool' );


        //error
		$data['text_loading_notifications'] = $this->language->get( 'text_loading_notifications' );
		$data['error_notifications'] = $this->language->get( 'error_notifications' );
		$data['text_retry'] = $this->language->get( 'text_retry' );
		$data['error_no_news'] = $this->language->get( 'error_no_news' );
		$data['entry_import_products'] = $this->language->get( 'entry_import_products' );
		$data['error_default_ikros_category'] = $this->language->get( 'error_default_ikros_category' );
        $data['entry_import_price'] = $this->language->get( 'entry_import_price' );
        $data['entry_import_quantity'] = $this->language->get( 'entry_import_quantity' );
        $data['entry_import_description'] = $this->language->get( 'entry_import_description' );
        $data['entry_import_vat'] = $this->language->get( 'entry_import_vat' );
        //notifikacia
        $data['error_no_news'] = $this->language->get( 'error_no_news' );
        $data['error_notification'] = $this->language->get( 'error_notification' );


        //stavy licencie
        $data['entry_license_0'] = $this->language->get( 'entry_license_0' );
        $data['entry_license_2'] = $this->language->get( 'entry_license_2' );
        $data['entry_license_4'] = $this->language->get( 'entry_license_4' );
        $data['entry_license_8'] = $this->language->get( 'entry_license_8' );
        $data['entry_license_16'] = $this->language->get( 'entry_license_16' );
        $data['entry_license_32'] = $this->language->get( 'entry_license_32' );

        $data['text_yes'] = $this->language->get( 'text_yes' );
        $data['text_no'] = $this->language->get( 'text_no' );




		if (!empty($this->session->data['ikros_error'])) {
			$this->error['warning'] = $this->session->data['ikros_error'];
		}

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];

		} else {
			$data['error_warning'] = '';
		}

		unset($this->session->data['ikros_error']);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}





        //**************************************************************************


        //skladové statusy
        $this->load->model('localisation/stock_status');
        $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
        if (isset($this->request->post['ikros_default_stock_status_id'])) {
            $data['ikros_default_stock_status_id'] = $this->request->post['ikros_default_stock_status_id'];
        } elseif ($this->config->has('ikros_default_stock_status_id')) {
            $data['ikros_default_stock_status_id'] = $this->config->get('ikros_default_stock_status_id');
        } else {
            $data['ikros_default_stock_status_id'] =  $data['stock_statuses'][0]['stock_status_id'];
        }

        //OBJEDNÁVKOVÉ STATUSY
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['ikros_order_status_id'])) {
            $data['ikros_order_status_id'] = $this->request->post['ikros_order_status_id'];
        } elseif($this->config->has('ikros_order_status_id')) {
            $data['ikros_order_status_id'] = $this->config->get('ikros_order_status_id');
        }else{
            $data['ikros_order_status_id'] = array();
        }

        //POSLEDNÝ IMPORT PRODUKTOV
        if (isset($this->request->post['ikros_last_import'])) {
            $data['ikros_last_import'] = $this->request->post['ikros_last_import'];
        } elseif ($this->config->has('ikros_last_import')) {
            $data['ikros_last_import'] = $this->config->get('ikros_last_import');
        } else {
            $data['ikros_last_import'] = date('Y-m-d H:i');
        }

        //POPIS POLOZKY
        $data['product_description_options'] = array(
            array('id'=> 0, 'text'=>$this->language->get('text_product_description_none')),
            //array('id'=> 1, 'text'=>$this->language->get('text_product_description_desc')),
            array('id'=> 1, 'text'=>$this->language->get('text_product_description_model')),
            array('id'=> 2, 'text'=>$this->language->get('text_product_description_opt')),
        );

        if (isset($this->request->post['ikros_product_description_id'])) {
            $data['ikros_product_description_id'] = $this->request->post['ikros_product_description_id'];
        } elseif ($this->config->has('ikros_product_description_id')) {
            $data['ikros_product_description_id'] = $this->config->get('ikros_product_description_id');
        } else {
            $data['ikros_product_description_id'] =  $data['product_description_options'][0]['id'];
        }

        //ÚVODNÝ TEXT OBJEDNÁVKY
        if (isset($this->request->post['ikros_order_start_text'])) {
            $data['ikros_order_start_text'] = $this->request->post['ikros_order_start_text'];
        } elseif($this->config->has('ikros_order_start_text')){
            $data['ikros_order_start_text'] = $this->config->get('ikros_order_start_text');
        } else{
            $data['ikros_order_start_text'] = "";
        }

        //ZÁVEREČNÝ TEXT OBJEDNÁVKY
        if (isset($this->request->post['ikros_order_end_text'])) {
            $data['ikros_order_end_text'] = $this->request->post['ikros_order_end_text'];
        } elseif($this->config->has('ikros_order_end_text')){
            $data['ikros_order_end_text'] = $this->config->get('ikros_order_end_text');
        } else{
            $data['ikros_order_end_text'] = "";
        }

        //ÚVODNÝ TEXT FAKTÚRY
        if (isset($this->request->post['ikros_invoice_start_text'])) {
            $data['ikros_invoice_start_text'] = $this->request->post['ikros_invoice_start_text'];
        } elseif($this->config->has('ikros_invoice_start_text')){
            $data['ikros_invoice_start_text'] = $this->config->get('ikros_invoice_start_text');
        } else{
            $data['ikros_invoice_start_text'] = "";
        }

        //ZÁVEREČNÝ TEXT FAKTÚRY
        if (isset($this->request->post['ikros_invoice_end_text'])) {
            $data['ikros_invoice_end_text'] = $this->request->post['ikros_invoice_end_text'];
        } elseif($this->config->has('ikros_invoice_end_text')){
            $data['ikros_invoice_end_text'] = $this->config->get('ikros_invoice_end_text');
        } else{
            $data['ikros_invoice_end_text'] = "";
        }

        //NAHRADA POLA FIRMA
        $data['company_replacement_options'] = array(
            array('id'=> 0, 'text'=>$this->language->get('text_company_replacement_company')),
            array('id'=> 1, 'text'=>$this->language->get('text_company_replacement_name'))
        );

        if (isset($this->request->post['ikros_company_replacement_id'])) {
            $data['ikros_company_replacement_id'] = $this->request->post['ikros_company_replacement_id'];
        } elseif ($this->config->has('ikros_company_replacement_id')) {
            $data['ikros_company_replacement_id'] = $this->config->get('ikros_company_replacement_id');
        } else {
            $data['ikros_company_replacement_id'] =  $data['company_replacement_options'][0]['id'];
        }


        //FIRMA V POŠTOVEJ ADRESE
        if (isset($this->request->post['ikros_ico_in_shipping'])) {
            $data['ikros_ico_in_shipping'] = $this->request->post['ikros_ico_in_shipping'];
        } else {
            $data['ikros_ico_in_shipping'] = $this->config->get('ikros_ico_in_shipping');
        }

        //PERCENTO ZĽAVY ZA DOKLAD
        if (isset($this->request->post['ikros_document_discount'])) {
            $data['ikros_document_discount'] = $this->request->post['ikros_document_discount'];
        } elseif($this->config->has('ikros_document_discount')){
            $data['ikros_document_discount'] = $this->config->get('ikros_document_discount');
        } else{
            $data['ikros_document_discount'] = 0;
        }

        //DÁTUM SPLATNOSTI FAKTÚRY
        if (isset($this->request->post['ikros_due_date'])) {
            $data['ikros_due_date'] = $this->request->post['ikros_due_date'];
        } elseif($this->config->has('ikros_due_date')){
            $data['ikros_due_date'] = $this->config->get('ikros_due_date');
        } else{
            $data['ikros_due_date'] = 0;
        }

        // DAŇOVÁ TRIEDA PRE DOPRAVU
        $this->load->model('localisation/tax_class');
        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        if (isset($this->request->post['ikros_shipping_tax_class_id'])) {
            $data['ikros_shipping_tax_class_id'] = $this->request->post['ikros_shipping_tax_class_id'];
        } elseif ($this->config->has('ikros_shipping_tax_class_id')) {
            $data['ikros_shipping_tax_class_id'] = $this->config->get('ikros_shipping_tax_class_id');
        }  else {
            $data['ikros_shipping_tax_class_id'] = 0;
        }

        // DAŇOVÁ TRIEDA PRE KUPONY
        if (isset($this->request->post['ikros_coupon_tax_class_id'])) {
            $data['ikros_coupon_tax_class_id'] = $this->request->post['ikros_coupon_tax_class_id'];
        } elseif ($this->config->has('ikros_coupon_tax_class_id')) {
            $data['ikros_coupon_tax_class_id'] = $this->config->get('ikros_coupon_tax_class_id');
        }  else {
            $data['ikros_coupon_tax_class_id'] = 0;
        }

        //ČÍSLO ÚČTU
        if (isset($this->request->post['ikros_sender_bank_account'])) {
            $data['ikros_sender_bank_account'] = $this->request->post['ikros_sender_bank_account'];
        } elseif($this->config->has('ikros_sender_bank_account')){
            $data['ikros_sender_bank_account'] = $this->config->get('ikros_sender_bank_account');
        } else{
            $data['ikros_sender_bank_account'] = "";
        }

        //IBAN
        if (isset($this->request->post['ikros_sender_bank_iban'])) {
            $data['ikros_sender_bank_iban'] = $this->request->post['ikros_sender_bank_iban'];
        } elseif($this->config->has('ikros_sender_bank_iban')){
            $data['ikros_sender_bank_iban'] = $this->config->get('ikros_sender_bank_iban');
        } else{
            $data['ikros_sender_bank_iban'] = "";
        }

        //SWIFT
        if (isset($this->request->post['ikros_sender_bank_swift'])) {
            $data['ikros_sender_bank_swift'] = $this->request->post['ikros_sender_bank_swift'];
        } elseif($this->config->has('ikros_sender_bank_swift')){
            $data['ikros_sender_bank_swift'] = $this->config->get('ikros_sender_bank_swift');
        } else{
            $data['ikros_sender_bank_swift'] = "";
        }

        //FORMAT DOKLADOV
        $data['convert_order_number'] = array(
            'RRRRCCCC','CCCRRRR','CCC/RR','RRMMCCCC','RRMMDDCC'
        );
        if (isset($this->request->post['ikros_convert_order_number'])) {
            $data['ikros_convert_order_number'] = $this->request->post['ikros_convert_order_number'];
        } elseif ($this->config->has('ikros_convert_order_number')) {
            $data['ikros_convert_order_number'] = $this->config->get('ikros_convert_order_number');
        }  else {
            $data['ikros_convert_order_number'] = 0;
        }

        //FORMAT DOKLADOV checkbox
        if (isset($this->request->post['ikros_convert_order_format_custom'])) {
            $data['ikros_convert_order_format_custom'] = $this->request->post['ikros_convert_order_format_custom'];
        } elseif ($this->config->has('ikros_convert_order_format_custom')) {
            $data['ikros_convert_order_format_custom'] = $this->config->get('ikros_convert_order_format_custom');
        }  else {
            $data['ikros_convert_order_format_custom'] = 0;
        }

        //AUTORIZAČNÝ KĽÚČ
        if (isset($this->request->post['ikros_authorization_key'])) {
            $data['ikros_authorization_key'] = $this->request->post['ikros_authorization_key'];
        } else {
            $data['ikros_authorization_key'] = $this->config->get('ikros_authorization_key');
        }

        //CRON
        $data['cron_statuses'] = array(
            array('cron_status_id'=> 1, 'name'=>$this->language->get('text_cron_product')),
            array('cron_status_id'=> 2, 'name'=>$this->language->get('text_cron_order')),
            array('cron_status_id'=> 3, 'name'=>$this->language->get('text_cron_invoice')),
        );

        if (isset($this->request->post['ikros_cron_status_id'])) {
            $data['ikros_cron_status_id'] = $this->request->post['ikros_cron_status_id'];
        } elseif($this->config->has('ikros_cron_status_id')) {
            $data['ikros_cron_status_id'] = $this->config->get('ikros_cron_status_id');
        }else{
            $data['ikros_cron_status_id'] = array();
        }

        //CRON PASS
        if (isset($this->request->post['ikros_cron_key'])) {
            $data['ikros_cron_key'] = $this->request->post['ikros_cron_key'];
        } else {
            $data['ikros_cron_key'] = $this->config->get('ikros_cron_key');
        }

        //KROKOVANIE IMPORTOV
        if (isset($this->request->post['ikros_step_document'])) {
            $data['ikros_step_document'] = $this->request->post['ikros_step_document'];
        } else {
            $data['ikros_step_document'] = $this->config->get('ikros_step_document');
        }


        //************************************************************************************************




        //posledný import produktov
        if (isset($this->error['ikros_last_import'])) {
            $data['error_ikros_last_import'] = $this->error['ikros_last_import'];
        } else {
            $data['error_ikros_last_import'] = '';
        }

        //formá čísla dokumentu
        if (isset($this->error['ikros_order_format'])) {
            $data['error_convert_order_number'] = $this->error['ikros_order_format'];
        } else {
            $data['error_convert_order_number'] = '';
        }

        $errors = array('step_document', 'document_discount', 'due_date', 'sender_bank_account', 'sender_bank_iban', 'cron_key');
        foreach($errors as $error){
            if (isset($this->error[$error])) {
                $data['error_'.$error] = $this->error[$error];
            } else {
                $data['error_'.$error] = '';
            }
        }




		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->ssl)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tool/ikros', 'token=' . $this->session->data['token'], $this->ssl)
		);

		$data['back'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->ssl);
		$data['button_back'] = $this->language->get( 'button_back' );


		$data['export_orders']      = $this->url->link('tool/ikros/orders',     'token=' . $this->session->data['token'], $this->ssl);
		$data['export_invoice']     = $this->url->link('tool/ikros/invoices',   'token=' . $this->session->data['token'], $this->ssl);
        $data['import_products']    = $this->url->link('tool/ikros/products',   'token=' . $this->session->data['token'], $this->ssl);
        $data['fill_pivot_doc']     = $this->url->link('tool/ikros/fillpivotdocument','token=' . $this->session->data['token'], $this->ssl);
        $data['clear_pivot_doc']    = $this->url->link('tool/ikros/clearpivotdocument','token=' . $this->session->data['token'], $this->ssl);
        $data['create_pivot']       = $this->url->link('tool/ikros/pivot',      'token=' . $this->session->data['token'], $this->ssl);
		$data['settings']           = $this->url->link('tool/ikros/settings',   'token=' . $this->session->data['token'], $this->ssl);
		$data['restore']            = $this->url->link('tool/ikros/clean_db',   'token=' . $this->session->data['token'], $this->ssl);
        $data['empty_db']           = $this->url->link('tool/ikros/evakuate',   'token=' . $this->session->data['token'], $this->ssl);


        $data['license'] = $this->model_tool_ikros->parseLicence();
        $data['pivot_products'] =  $this->model_tool_ikros->pivotProductsExist();    //existencia pivotu / zobrazenie karty start aplikacie
        $data['pivot_orders'] =  $this->model_tool_ikros->pivotOrdersExist();    //existencia pivotu / zobrazenie karty start aplikacie
		$data['token'] = $this->session->data['token'];


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view( ((version_compare(VERSION, '2.2.0.0') >= 0) ? 'tool/ikros' : 'tool/ikros.tpl'), $data));
	}
    //------------------------------------------------------------------------------------------------------------------





    /*******************************************************************************************************************
     * @param $date
     * @param string $format
     * @return bool
     * Kontrola formátu dátumu
     ******************************************************************************************************************/
    private function validateDate($date, $format = 'Y-m-d H:i')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return bool
     * Kontrola nastavení
     ******************************************************************************************************************/
	protected function validateSettingsForm() {
		if (!$this->user->hasPermission('access', 'tool/ikros')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        //posledný import
        if(!$this->validateDate($this->request->post['ikros_last_import'])){
            $this->error['ikros_last_import'] = $this->language->get('error_ikros_last_import');
            $this->session->data['ikros_error'][] = $this->language->get( 'warning_settings' ) . $this->language->get( 'error_ikros_last_import' );
        }


        //validate comma
        if( strstr($this->request->post['ikros_document_discount'], ",")) {
            $this->request->post['ikros_document_discount'] = str_replace(",", ".", $this->request->post['ikros_document_discount']) ;
        }
        if ( ( utf8_strlen($this->request->post['ikros_document_discount']) > 0) && !is_numeric($this->request->post['ikros_document_discount']) )  {
            $this->error['document_discount'] = $this->language->get('error_document_discount');
            $this->session->data['ikros_error'][] = $this->language->get('error_document_discount_format');
            //return false;
        }
        if( utf8_strlen($this->request->post['ikros_document_discount']) == 0 ) {
            $this->request->post['ikros_document_discount'] = 0 ;
        }

        if ( ($this->request->post['ikros_authorization_key']) )  {
            $this->request->post['ikros_authorization_key'] = $this->db->escape($this->request->post['ikros_authorization_key']);
        }

        if (  !preg_match('/^\d+$/', $this->request->post['ikros_due_date']) )  {
            $this->error['due_date'] = $this->language->get('error_due_date');
            $this->session->data['ikros_error'][] = $this->language->get('error_due_date_format');
        }

        if ( ( !utf8_strlen($this->request->post['ikros_convert_order_number']) > 0)  || !preg_match('/C/', $this->request->post['ikros_convert_order_number'])  )  {
            $this->error['ikros_order_format'] = $this->language->get('error_convert_order_number');
            $this->session->data['ikros_error'][] = $this->language->get('error_convert_order_number');
        }

        //ak, tak len čísla
        if ( ( utf8_strlen($this->request->post['ikros_sender_bank_account']) > 0) && !preg_match( '/^(\d)+$/', $this->request->post['ikros_sender_bank_account']) )  {
            $this->error['sender_bank_account'] = $this->language->get('error_sender_bank_account');
            $this->session->data['ikros_error'][] = $this->language->get('error_sender_bank_account');
        }

        //ak, tak musí začínať dvoma písmenami - nasledujú čísla
        if ( ( utf8_strlen($this->request->post['ikros_sender_bank_iban']) > 0) && !preg_match(('/^([A-Z]{2})(\d+)$/'), $this->request->post['ikros_sender_bank_iban']) )  {
            $this->error['sender_bank_iban'] = $this->language->get('error_sender_bank_iban');
            $this->session->data['ikros_error'][] = $this->language->get('error_sender_bank_iban');
        }

        //ak, tak len čísla
        if ( ( utf8_strlen($this->request->post['ikros_step_document']) > 0) && !preg_match(('/^(\d+)$/'), $this->request->post['ikros_step_document']) )  {
            $this->error['step_document'] = $this->language->get('error_step_document');
            $this->session->data['ikros_error'][] = $this->language->get('error_step_document');
        }

        //len konvert na veľke
        if ( utf8_strlen($this->request->post['ikros_sender_bank_swift']) > 0 )  {
            $this->request->post['ikros_sender_bank_swift'] = strtoupper($this->request->post['ikros_sender_bank_swift']);
        }

        //cron heslo - nepovinné, ale ak je zadané, tak aspoň 5 znakov písmená malým písmom
        $this->request->post['ikros_cron_key'] = strtolower($this->request->post['ikros_cron_key']);
        if ( ( utf8_strlen($this->request->post['ikros_cron_key']) > 0 ) &&  ( utf8_strlen($this->request->post['ikros_cron_key']) < 5  ) )  {
            $this->error['cron_key'] = $this->language->get('error_cron_key');
            $this->session->data['ikros_error'][] = $this->language->get('error_cron_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
	}
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return bool
     * kontrola prístupu pre produkt order invoice
     ******************************************************************************************************************/
    protected function checkPermission() {
        if (!$this->user->hasPermission('access', 'tool/ikros')) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Notifikácia
     ******************************************************************************************************************/
    public function getNotifications() {
        sleep(1); // give the data some "feel" that its not in our system
        $this->load->model('tool/ikros');
        $this->load->language( 'tool/ikros' );
        $response = $this->model_tool_ikros->getNotifications();
        $json = array();
        if ($response===false) {
            $json['message'] = '';
            $json['error'] = $this->language->get( 'error_notifications' );
        } else {
            $json['message'] = $response;
            $json['error'] = '';
        }

        $this->response->setOutput(json_encode($json));
    }
    //------------------------------------------------------------------------------------------------------------------


}
?>