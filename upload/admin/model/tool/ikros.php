<?php
class ModelToolIkros extends Model {

    private $last_upload_product = '2012-01-01T12:00:00';


    /*******************************************************************************************************************
     * @return mixed
     * Požiadavka na aktualizované údaje v ikrose
     ******************************************************************************************************************/
    private function getProductInteo(){

        $url = "https://eshops.inteo.sk/api/v1/products/";
        $url .= isset($this->last_upload_product) ? "?changesFrom=".$this->last_upload_product : "";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->config->get('ikros_authorization_key')
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * @param $order
     * @return mixed
     * Požiadavka na spracovanie objednávok pre ikrosu
     ******************************************************************************************************************/
    private function postOrdersInteo($order){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://eshops.inteo.sk/api/v1/incomingorders/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $order );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->config->get('ikros_authorization_key')
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * @param $order
     * Požiadavka na spracovanie faktúr pre ikros
     ******************************************************************************************************************/
    private function postInvoicesInteo($order){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://eshops.inteo.sk/api/v1/invoices/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $order  );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->config->get('ikros_authorization_key')
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * @return mixed
     * Požiadavka na licenciu
     ******************************************************************************************************************/
    private function getLicence()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://eshops.inteo.sk/api/v1/eshops/license");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->config->get('ikros_authorization_key')
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Metóda vyčistí z pivotu už nepotrebné záznamy (vymazané objednávky) a zároveň pripraví pre update položky,
     * ktoré ešte neboli importované (tým sa zbavím podmienky where not null)
     ******************************************************************************************************************/
    private function clearDocumentPivotTable(){
        //odstranim zaznamy neexistujúcich objednávok
        $sql = " SELECT `".DB_PREFIX."ikros_document_pivot`.`order_id`
                FROM `".DB_PREFIX."ikros_document_pivot`
                LEFT JOIN `".DB_PREFIX."order` USING(order_id)
                WHERE `".DB_PREFIX."order`.`order_id` is null";
        $query = $this->db->query($sql);


        if($query->num_rows){
            $sql = "DELETE FROM `".DB_PREFIX."ikros_document_pivot` WHERE `order_id` in('0' ";
            foreach($query->rows as $row){
                $sql .=", '".$row['order_id']."'";
            }
            $sql .=")";

            $this->db->query($sql);
        }

        //pripravim zaznamy pre objednávky, ktoré ešte neboli odosielane
        $sql = "SELECT `order_id`
                FROM `" . DB_PREFIX . "order`
                WHERE `order_id` NOT IN (SELECT `order_id` FROM `" . DB_PREFIX . "ikros_document_pivot`)";
        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach ($query->rows as $row) {
                $this->simpleQuery("INSERT INTO `" . DB_PREFIX . "ikros_document_pivot` (`order_id`, `order_modified`, `invoice_modified`)
                                    VALUES ('$row[order_id]', '2000-01-01 00:00:00', '2000-01-01 00:00:00')");
            }
        }
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Metóda prekopíruje objednávky do pivot tabuľky a tak simuluje ich odoslanie
     * Použitie pri preplnených databázach, kedy je prekročený limit odosielaný dát (server db)
     ******************************************************************************************************************/
    public function fillPivotDocument(){
        $this->db->query("TRUNCATE `".DB_PREFIX."ikros_document_pivot`");

        $sql = "SELECT `order_id`, `date_modified`
                FROM `" . DB_PREFIX . "order`";

        $query = $this->db->query($sql);
        if($query->num_rows) {
            foreach ($query->rows as $row) {
                $this->simpleQuery("INSERT INTO `" . DB_PREFIX . "ikros_document_pivot` (`order_id`, `order_modified`, `invoice_modified`)
                                    VALUES ('$row[order_id]', '$row[date_modified]', '$row[date_modified]')");
            }
        }

        $this->session->data['success'] = $this->language->get( 'success_pivot_doc_fill' );
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * Metóda vyprázni pivot tabuľku dokumentov. Všetky objednávky sa pri najbližšom importe znova odošlú
     ******************************************************************************************************************/
    public function clearPivotDocument(){
        $this->db->query("TRUNCATE `".DB_PREFIX."ikros_document_pivot`");

        $this->session->data['success'] = $this->language->get( 'success_pivot_doc_clear' );
    }
    //------------------------------------------------------------------------------------------------------------------




    /*******************************************************************************************************************
     * @param $type
     * @return mixed
     * Vráti všetky objednávky
     * ktoré
     ******************************************************************************************************************/
    private function getOrders($type){

        $this->clearDocumentPivotTable();

        $allowed_statuses = $this->config->has('ikros_order_status_id') ? implode(",", $this->config->get('ikros_order_status_id')) : '0';
        $limit = $this->config->get('ikros_step_document');


        $sql = "SELECT `" . DB_PREFIX . "order`.`order_id`,
                `" . DB_PREFIX . "order`.`invoice_no`,
                `" . DB_PREFIX . "order`.`invoice_prefix`,
                `" . DB_PREFIX . "order`.`store_id`,
                `" . DB_PREFIX . "order`.`store_name`,
                `" . DB_PREFIX . "order`.`store_url`,
                `" . DB_PREFIX . "order`.`customer_id`,
                `" . DB_PREFIX . "order`.`customer_group_id`,
                `" . DB_PREFIX . "order`.`firstname`,
                `" . DB_PREFIX . "order`.`lastname`,
                `" . DB_PREFIX . "order`.`email`,
                `" . DB_PREFIX . "order`.`telephone`,
                `" . DB_PREFIX . "order`.`fax`,
                `" . DB_PREFIX . "order`.`payment_firstname`,
                `" . DB_PREFIX . "order`.`payment_lastname`,
                `" . DB_PREFIX . "order`.`payment_company`,
                `" . DB_PREFIX . "order`.`payment_address_1`,
                `" . DB_PREFIX . "order`.`payment_address_2`,
                `" . DB_PREFIX . "order`.`payment_city`,
                `" . DB_PREFIX . "order`.`payment_postcode`,
                `" . DB_PREFIX . "order`.`payment_country`,
                `" . DB_PREFIX . "order`.`payment_country_id`,
                `" . DB_PREFIX . "order`.`payment_zone`,
                `" . DB_PREFIX . "order`.`payment_zone_id`,
                `" . DB_PREFIX . "order`.`payment_address_format`,
                `" . DB_PREFIX . "order`.`payment_method`,
                `" . DB_PREFIX . "order`.`payment_code`,
                `" . DB_PREFIX . "order`.`shipping_firstname`,
                `" . DB_PREFIX . "order`.`shipping_lastname`,
                `" . DB_PREFIX . "order`.`shipping_company`,
                `" . DB_PREFIX . "order`.`shipping_address_1`,
                `" . DB_PREFIX . "order`.`shipping_address_2`,
                `" . DB_PREFIX . "order`.`shipping_city`,
                `" . DB_PREFIX . "order`.`shipping_postcode`,
                `" . DB_PREFIX . "order`.`shipping_country`,
                `" . DB_PREFIX . "order`.`shipping_country_id`,
                `" . DB_PREFIX . "order`.`shipping_zone`,
                `" . DB_PREFIX . "order`.`shipping_zone_id`,
                `" . DB_PREFIX . "order`.`shipping_address_format`,
                `" . DB_PREFIX . "order`.`shipping_method`,
                `" . DB_PREFIX . "order`.`shipping_code`,
                `" . DB_PREFIX . "order`.`comment`,
                `" . DB_PREFIX . "order`.`total`,
                `" . DB_PREFIX . "order`.`order_status_id`,
                `" . DB_PREFIX . "order`.`affiliate_id`,
                `" . DB_PREFIX . "order`.`commission`,
                `" . DB_PREFIX . "order`.`language_id`,
                `" . DB_PREFIX . "order`.`currency_id`,
                `" . DB_PREFIX . "order`.`currency_code`,
                `" . DB_PREFIX . "order`.`currency_value`,
                `" . DB_PREFIX . "order`.`ip`,
                `" . DB_PREFIX . "order`.`forwarded_ip`,
                `" . DB_PREFIX . "order`.`user_agent`,
                `" . DB_PREFIX . "order`.`accept_language`,
                `" . DB_PREFIX . "order`.`date_added`,
                `" . DB_PREFIX . "order`.`date_modified`
            FROM " . DB_PREFIX . "order
            LEFT JOIN " . DB_PREFIX . "ikros_document_pivot
            ON (" . DB_PREFIX . "order.order_id = " . DB_PREFIX . "ikros_document_pivot.order_id)
            WHERE " . DB_PREFIX . "order.order_status_id IN (" . $allowed_statuses . ")
            AND (" . DB_PREFIX . "order.date_modified != " . DB_PREFIX . "ikros_document_pivot." . $type . "_modified)
            OR " . DB_PREFIX . "ikros_document_pivot." . $type . "_modified IS NULL ";
        if($limit){
            $sql .= " LIMIT " . $limit;
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * @param $order_id
     * @return array
     * Order total - súhrn cien v danej objednávke
     ******************************************************************************************************************/
    private function getOrderTotal($order_id){
        $sql        = "SELECT `code`, `title`, `value`, `sort_order`
                      FROM `".DB_PREFIX."order_total`
                      WHERE `order_id` = $order_id";

        $query      = $this->db->query($sql);
        $total      = array();
        foreach($query->rows as $result){
            $total[$result['code']] =  $result;
        }
        return $total;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Položky objednavky, len tovar v košíku
     * @param $order_id
     * @return array
     ******************************************************************************************************************/
    private function getOrderItems($order_id){

        $sql = "SELECT
        `".DB_PREFIX."order_product`.`product_id`,
        `".DB_PREFIX."order_product`.`order_product_id`,
        `".DB_PREFIX."order_product`.`quantity`,
        `".DB_PREFIX."order_product`.`total`,
        `".DB_PREFIX."order_product`.`price`,
        `".DB_PREFIX."order_product`.`tax`,
        `".DB_PREFIX."order_product`.`name`,
        `".DB_PREFIX."order_product`.`model`,

        `".DB_PREFIX."ikros_products_pivot`.`product_id_ikros`,
        `".DB_PREFIX."ikros_products_pivot`.`numbering_sequence_code`,
        `".DB_PREFIX."ikros_products_pivot`.`type_id`

        FROM `".DB_PREFIX."order_product` LEFT JOIN `oc_ikros_products_pivot` using(`product_id`)
        WHERE  `order_id` = $order_id";

        $query = $this->db->query($sql);
        $items = array();
        foreach($query->rows as $result){
            $items[] =  $result;
        }

        return $items;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_id
     * @param $order_product_id
     * @return mixed
     * Možnosti pre predukt
     */
    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT *FROM " . DB_PREFIX . "order_option
        WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        return $query->rows;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_id
     * @param $order_product_id
     * @return string
     * Ak produkt má možnosti, usporiada ich do zoznamu (použitie v fa popis položky)
     ******************************************************************************************************************/
    private function getOptionsAsText($order_id, $order_product_id){

        $option_data = array();
        $text = '';

        $options = $this->getOrderOptions($order_id, $order_product_id);

        foreach ($options as $option) {
            if ($option['type'] != 'file') {
                $value = $option['value'];
            } else {
                $value = substr($option['value'], 0, strrpos($option['value'], '.'));
            }

            $option_data[] = array(
                'name'  => $option['name'],
                'value' => (strlen($value) > 20 ? substr($value, 0, 20) . '..' : $value)
            );
        }

        if(!empty($option_data)){
            foreach ($option_data as $option) {
                $text .= $option['name'] . ": " . $option['value'] . PHP_EOL;

            }
        }

        return $text;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_id
     * @param $exchangeRate
     * @return array
     * metóda zabezpečí naplnenie vlastnosti items pre formát ikrosu - tovar
     ******************************************************************************************************************/
    private function completeItems( $order_id, $exchangeRate ){
        $items              = array();
        $exchangeRate       = floatval( $exchangeRate );
        $order_products     = $this -> getOrderItems( $order_id );


        foreach ($order_products as $product){
            if(!empty($product['product_id_ikros'])){
                $decode_product_id_ikros = array($product['product_id_ikros'],$product['numbering_sequence_code'],$product['type_id']);
            }else{
                $decode_product_id_ikros = array($product['product_id'], '', '1');
            }

            //popis polozky
            $popis_polozky = $this->config->get('ikros_product_description_id');
            if($popis_polozky == 1){
                $description = $product['model'];
                $description = empty($description) ? '' : $description;
            }elseif($popis_polozky == 2){
                $description = $this->getOptionsAsText($order_id, $product['order_product_id']);
            }else{
                $description = '';
            }
            //popis polozky koniec


            $hasDiscount = false;
            $discountName           = "";
            $discountPercent        = 0;
            $discountValue          = 0;
            $discountValueWithVat   = 0;

            $measureType            = 'ks';

            $vat                    = floatval( ($product['tax'] * 100) / $product['price'] ); //prepočet z order_total

            $unitPrice              = floatval( $product['price'] ) * $exchangeRate;
            $unitPriceWithVat       = $unitPrice + ( ( $unitPrice / 100 ) * $vat );

            $totalPrice             = round( $unitPrice * $product['quantity'], 2 );
            $totalPriceWithVat      = round( $unitPriceWithVat * $product['quantity'], 2 );

            //zaokruhlenie
            $unitPrice              = round( $unitPrice, 2 );
            $unitPriceWithVat       = round( $unitPriceWithVat, 2 );

            $productCode            = $decode_product_id_ikros[0];
            $typeId                 = $decode_product_id_ikros[2];
            $warehouseCode          = null;
            $foreignName            = null;
            $customText             = null;
            $ean                    = null;
            $jkpov                  = null;
            $plu                    = null;
            $numberingSequenceCode  = $decode_product_id_ikros[1];
            $specialAttribute       = null;

            $items[] = $this->fillItem(
                html_entity_decode( $product['name'] ),
                $description, $product['quantity'], $measureType, $totalPrice,
                $totalPriceWithVat, $unitPrice, $unitPriceWithVat, $vat,
                $hasDiscount, $discountName, $discountPercent, $discountValue, $discountValueWithVat,
                $productCode, $typeId, $warehouseCode, $foreignName, $customText,
                $ean, $jkpov, $plu, $numberingSequenceCode, $specialAttribute );

        }

        return $items;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_total
     * @param $exchangeRate
     * @return array
     * metóda zabezpečí naplnenie vlastnosti items pre formát ikrosu - doprava
     ******************************************************************************************************************/
    private function  completeTotalItems($order_total, $exchangeRate, $order_total_item)
    {
        if($order_total_item == 'shipping'){
            $vat = floatval($this->getTax('ikros_shipping_tax_class_id'));
        }elseif($order_total_item == 'coupon'){
            $vat = floatval($this->getTax('ikros_coupon_tax_class_id'));
        }else{
            $vat = 0;
        }


        $shipping                   = $order_total[$order_total_item];
        $totalPrice                 = floatval($shipping['value']) * $exchangeRate;
        $totalPriceWithVat          = $totalPrice + (($totalPrice / 100) * $vat);

        $totalPrice                 = round($totalPrice, 2); //zaokruhlenie
        $totalPriceWithVat          = round($totalPriceWithVat, 2); //zaokruhlenie

        return $this->fillItem($shipping['title'], "", 1, 'ks', $totalPrice,
            $totalPriceWithVat, $totalPrice, $totalPriceWithVat, $vat,
            false, "", null, null, null,
            null, 0, null, null, null,
            null, null, null, null, null);
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return mixed
     * pre poštu užívateľ zadáva daň v administrácii
     ******************************************************************************************************************/
    private function getTax($tax_order_total_item){
        if(!$this->config->has($tax_order_total_item) ) return 0;
        if($this->config->get($tax_order_total_item) == 0 ) return 0;

        $tax_id = $this->config->get($tax_order_total_item);
        $sql = " SELECT
                    `".DB_PREFIX."tax_rule`.`tax_class_id`,
                    `".DB_PREFIX."tax_rate`.`rate`
                FROM `".DB_PREFIX."tax_rule`
                LEFT JOIN `".DB_PREFIX."tax_rate` using(`tax_rate_id`)
                WHERE `".DB_PREFIX."tax_rule`.`tax_class_id` = $tax_id";
        $query = $this->db->query($sql);
        return $query->row['rate'];
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_id
     * @param $date_modified
     * @param $typ
     *
     ******************************************************************************************************************/
    private function updateDocumentPivot($data_for_update, $typ){
        if(isset($data_for_update)){
            foreach ($data_for_update as $order_id => $date_modified){

                $sql = "UPDATE `".DB_PREFIX."ikros_document_pivot`
                SET `".$typ."_modified`='$date_modified'
                WHERE `order_id`='$order_id'";

                $this->db->query($sql);
            }
        }
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $name
     * @param $description
     * @param $count
     * @param $measureType
     * @param $totalPrice
     * @param $totalPriceWithVat
     * @param $unitPrice
     * @param $unitPriceWithVat
     * @param $vat
     * @param $hasDiscount
     * @param $discountName
     * @param $discountPercent
     * @param $discountValue
     * @param $discountValueWithVat
     * @param $productCode
     * @param $typeId
     * @param $warehouseCode
     * @param $foreignName
     * @param $customText
     * @param $ean
     * @param $jkpov
     * @param $plu
     * @param $numberingSequenceCode
     * @param $specialAttribute
     * @return array
     ******************************************************************************************************************/
    private function fillItem(
        $name,
        $description,
        $count,
        $measureType,
        $totalPrice,
        $totalPriceWithVat,
        $unitPrice,
        $unitPriceWithVat,
        $vat,
        $hasDiscount,
        $discountName,
        $discountPercent,
        $discountValue,
        $discountValueWithVat,
        $productCode,
        $typeId,
        $warehouseCode,
        $foreignName,
        $customText,
        $ean,
        $jkpov,
        $plu,
        $numberingSequenceCode,
        $specialAttribute
    ) {
        return
            array(
                "name"                  => $name,
                "description"           => $description,
                "count"                 => $count,
                "measureType"           => $measureType,
                "totalPrice"            => $totalPrice,
                "totalPriceWithVat"     => $totalPriceWithVat,
                "unitPrice"             => $unitPrice,
                "unitPriceWithVat"      => $unitPriceWithVat,
                "vat"                   => $vat,
                "hasDiscount"           => $hasDiscount,
                "discountName"          => $discountName,
                "discountPercent"       => $discountPercent,
                "discountValue"         => $discountValue,
                "discountValueWithVat"  => $discountValueWithVat,
                "productCode"           => $productCode,
                "typeId"                => $typeId,
                "warehouseCode"         => $warehouseCode,
                "foreignName"           => $foreignName,
                "customText"            => $customText,
                "ean"                   => $ean,
                "jkpov"                 => $jkpov,
                "plu"                   => $plu,
                "numberingSequenceCode" => $numberingSequenceCode,
                "specialAttribute"      => $specialAttribute
            );
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * @param $order_id
     * @param $date
     * @return string
     * Metoda na základe čísla a dátumu objednávky vygeneruje documentNumber podľa zvoleného vzoru
     ******************************************************************************************************************/
    private function encodeDocumentNumber($order_id, $date){

        $format = $this->config->get('ikros_convert_order_number');

        $date = new DateTime($date);

        $format_char    = '';
        $box            = array();
        $index          = -1;

        foreach (str_split($format) as $char) {
            if ($format_char == $char) {
                $box[$index] = $box[$index] . $char;
                continue;
            }
            $index++;
            $box[$index] = $char;
            $format_char = $char;
        }

        foreach ( $box as $key=>$tag ){
            switch ($tag){
                case 'RR':
                    $box[$key] = $date->format('y');
                    break;
                case 'RRRR':
                    $box[$key] = $date->format('Y');
                    break;
                case 'MM':
                    $box[$key] = $date->format('m');
                    break;
                case 'DD':
                    $box[$key] = $date->format('d');
                    break;
                default:
                    if(preg_match('/C/', $tag)){
                        $c_count = substr_count($tag, 'C');
                        $box[$key] = str_pad($order_id, $c_count, "0", STR_PAD_LEFT);
                    }
                    break;
            }
        }
        return implode('', $box);
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order
     * @return bool
     * Určí parameter pre clientHasDifferentPostalAddress -> rozdielna poštová a fakturačná adresa
     ******************************************************************************************************************/
    private function clientHasDifferentPostalAddress( $order ){
        if( trim( $order['payment_company'] ) != trim( $order['shipping_company'] ) ){
            return true;
        }
        if( trim( $order['payment_firstname'] ) != trim( $order['shipping_firstname'] ) ){
            return true;
        }
        if( trim( $order['payment_lastname'] ) != trim( $order['shipping_lastname'] ) ){
            return true;
        }
        if( trim( $order['payment_address_1'] ) != trim( $order['shipping_address_1'] ) ){
            return true;
        }
        if( trim( $order['payment_postcode'] ) != trim( $order['shipping_postcode'] ) ){
            return true;
        }
        if( trim( $order['payment_city'] ) != trim( $order['shipping_city'] ) ){
            return true;
        }
        if( trim( $order['payment_country'] ) != trim( $order['shipping_country'] ) ){
            return true;
        }
        return false;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order
     * @param $order_total
     * @param $exchangeRate
     * @param $type  F => faktura, O=>objednavka
     * @return array
     ******************************************************************************************************************/
    private function completePersonalInformations($order, $order_total, $exchangeRate, $type){

        $openingText        = ($type=='F' ? $this->config->get( 'ikros_invoice_start_text' ) : $this->config->get( 'ikros_order_start_text' ) );
        $closingText        = ($type=='F' ? $this->config->get( 'ikros_invoice_end_text' )   : $this->config->get( 'ikros_order_end_text' ) );
        $order_discount     = $this->orderDiscount($order_total); //zľava za doklad

        $totalPrice         = round( floatval( $order_total['sub_total']['value'] ) * $exchangeRate, 2 ) ;
        $totalPriceWithVat  = round( floatval( $order_total['total']['value'] ) * $exchangeRate, 2 ) ;

        $createDate         = $order['date_added'];

        $dueDate            = new DateTime( $order['date_added'] );
        $dueDate->modify( '+'.$this->config->get( 'ikros_due_date' ).' day' );
        $dueDate            = $dueDate->format( 'Y-m-d\TH:i:s' );

        $senderBankAccount  = $this->config->get( 'ikros_sender_bank_account' );
        $senderBankIban     = $this->config->get( 'ikros_sender_bank_iban' );
        $senderBankSwift    = $this->config->get( 'ikros_sender_bank_swift' );

        $documentNumber = $this->encodeDocumentNumber($order['order_id'], $order['date_added']);

        //nahrada pola company ak je potrebne
        $client_name = $order['payment_company'];
        $client_postal_name = $order['shipping_company'];
        if($this->config->get('ikros_company_replacement_id') == 1){
            $client_name = $order['payment_firstname'] . ' ' .  $order['payment_lastname'];
            $client_postal_name = $order['shipping_firstname'] . ' ' .  $order['shipping_lastname'];
        }
        //nahrada pola company ak je potrebne koniec

        //posle do postovej adresy firmu a hneď zvoli posielat postu na inu adresu
        //ale ponecha prazdne a posielat postu na inu adresu sa nevoli
        if(!$this->config->get('ikros_ico_in_shipping')){
            $client_postal_name = null;
        }
        //end



        //spoločné pre objednávku aj faktúru
        $personal_informations = array(
            "documentNumber"                    => $documentNumber,
            "totalPrice"                        => $totalPrice ,
            "totalPriceWithVat"                 => $totalPriceWithVat,
            "createDate"                        => str_replace( ' ', 'T', $createDate),
            "completionDate"                    => null,

            "clientName"                        => $client_name,
            "clientContactName"                 => $order['payment_firstname'],
            "clientContactSurname"              => $order['payment_lastname'],
            "clientStreet"                      => $order['payment_address_1'],
            "clientPostCode"                    => $order['payment_postcode'],
            "clientTown"                        => $order['payment_city'],
            "clientCountry"                     => $order['payment_country'],
            "clientPhone"                       => $order['telephone'],
            "clientEmail"                       => $order['email'],
            "clientRegistrationId"              => "",
            "clientTaxId"                       => "",
            "clientVatId"                       => "",
            "clientInternalId"                  => $order['customer_id'],
            "variableSymbol"                    => $documentNumber,
            "openingText"                       => $openingText,
            "closingText"                       => $closingText,

            "senderName"                        => null,
            "senderRegistrationId"              => null,
            "senderRegistrationCourt"           => null,
            "senderVatId"                       => null,
            "senderTaxId"                       => null,
            "senderStreet"                      => null,
            "senderPostCode"                    => null,
            "senderTown"                        => null,
            "senderRegion"                      => null,
            "senderCountry"                     => null,
            "senderBankAccount"                 => $senderBankAccount,
            "senderBankIban"                    => $senderBankIban,
            "senderBankSwift"                   => $senderBankSwift,
            "paymentType"                       => $order['payment_method'],
            "deliveryType"                      => $order['shipping_method'],
            "senderContactName"                 => null,
            "senderPhone"                       => null,
            "senderEmail"                       => null,
            "senderWeb"                         => $order['store_name'],


            "clientPostalName"                  => $client_postal_name, // ak je zadane, hneď vyplní posielať poštu na inú adresu
            "clientPostalContactName"           => $order['shipping_firstname'],
            "clientPostalContactSurname"        => $order['shipping_lastname'],
            "clientPostalPhone "                => null,
            "clientPostalStreet"                => $order['shipping_address_1'],
            "clientPostalPostCode"              => $order['shipping_postcode'],
            "clientPostalTown"                  => $order['shipping_city'],
            "clientPostalCountry"               => $order['shipping_country'],
            "clientHasDifferentPostalAddress"   => $this -> clientHasDifferentPostalAddress( $order ),
            "currency"                          => $order['currency_code'],
            "exchangeRate"                      => round( $order['currency_value'], 2 ),
            "senderIsVatPayer"                  => true,
            "discountPercent"                   => $order_discount['discountPercent'],
            "discountValue"                     => $order_discount['discountValue'],
            "discountValueWithVat"              => $order_discount['discountValueWithVat'],
            "priceDecimalPlaces"                => null,
            "clientNote"                        => $order['comment'],
        );

        // faktúra má niekoľko položiek naviac
        if ($type == 'F') {
            $personal_informations["numberingSequence"]     = "OF";
            $personal_informations["dueDate"]               = $dueDate;
            $personal_informations["deposit"]               = 0;
            $personal_informations["depositText"]           = "Deposit Text";
            $personal_informations["depositDate"]           = "2015-04-28T08:23:13";
            $personal_informations["orderNumber"]           = $documentNumber;
            $personal_informations["isVatAccordingPayment"] = true;
        }

        return $personal_informations;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @param $order_total
     * @return array
     * celková zľava za doklad
     ******************************************************************************************************************/
    private function orderDiscount($order_total){
        $percent = $this->config->get('ikros_order_discount');
        if ( $percent > 0 ){
            $discountPercent =  $percent;
            $discountValue = ($order_total['sub_total']['value'] / 100) * $percent;
            $discountValueWithVat =  ($order_total['total']['value'] / 100) * $percent;
            return array("discountPercent"=> $discountPercent,
                         "discountValue"=> $discountValue,
                         "discountValueWithVat"=> $discountValueWithVat);
        }
        return array(
            "discountPercent"      => null,
            "discountValue"        => null,
            "discountValueWithVat" => null
        );
    }
    //------------------------------------------------------------------------------------------------------------------












    /*******************************************************************************************************************
     * RIADIACA METÓDA PRE SKOMPLETIZOVANIE FAKTÚR A ICH ODOSLANIE DO IKROSU
     ******************************************************************************************************************/
    public function exportInvoices(){

        //krok x: dostupné objednávky v oc
        //parameter pre sql
        $orders = $this->getOrders('invoice');
        $update_document_pivot = array();
        $x = 0;
        foreach($orders as $order){
            $update_document_pivot[$order['order_id']] = $order['date_modified'];
            $exchangeRate = $order['currency_value'];
            $order_total = $this->getOrderTotal($order['order_id']);


            //tovar
            $items = $this->completeItems($order['order_id'], $exchangeRate);

            //doprava
            if(array_key_exists('shipping', $order_total)) {
                $items[] = $this->completeTotalItems($order_total, $exchangeRate, 'shipping');
            }

            //kupony adding
            if(array_key_exists('coupon', $order_total)) {
                $items[] = $this->completeTotalItems($order_total, $exchangeRate, 'coupon');
            }

            $personal_informations[$x] = $this->completePersonalInformations($order, $order_total, $exchangeRate, $type='F');
            $personal_informations[$x]['items']= $items;

            $x++;

        }

        if(isset($personal_informations)){
            $orderIkros = json_encode( $personal_informations );


            $response = $this-> postInvoicesInteo($orderIkros);
            $response = json_decode($response);
            $result = $response->result;
            switch ($result) {
                case 0:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_invoice_general_eror');
                    break;
                case 1:
                    $this->session->data['success'] = str_replace('%1', $x, $this->language->get('success_invoice') );
                    $this->updateDocumentPivot( $update_document_pivot, 'invoice' );
                    break;
                case 3:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_invoice_license_expired');
                    break;
                default:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_invoice_unknown');
                    break;
            }
        }else{
            $this->session->data['success'] = $this->language->get('success_no_invoice');
        }
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * RIADIACA METÓDA PRE SKOMPLETIZOVANIE OBJEDNÁVOK A ICH ODOSLANIE DO IKROSU
     ******************************************************************************************************************/
    public function exportOrders() {

        //parameter pre sql
        $orders = $this->getOrders('order');

        $update_document_pivot = array();
        $x = 0;
        foreach($orders as $order){
            $update_document_pivot[$order['order_id']] = $order['date_modified'];
            $exchangeRate = $order['currency_value'];
            $order_total = $this->getOrderTotal($order['order_id']);

            //tovar
            $items = $this->completeItems($order['order_id'], $exchangeRate);

            //doprava
            if(array_key_exists('shipping', $order_total)) {
                $items[] = $this->completeTotalItems($order_total, $exchangeRate, 'shipping');
            }

            //kupony adding
            if(array_key_exists('coupon', $order_total)) {
                $items[] = $this->completeTotalItems($order_total, $exchangeRate, 'coupon');
            }

            $personal_informations[$x] = $this->completePersonalInformations($order, $order_total, $exchangeRate, $type='O');
            $personal_informations[$x]['items']= $items;

            $x++;
        }


        if(isset($personal_informations)){
            $orderIkros = json_encode( $personal_informations );

            $response = $this-> postOrdersInteo($orderIkros);

            $response = json_decode($response);

            $result = $response->result;
            switch ($result) {
                case 0:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_order_general_eror');
                    break;
                case 1:
                    $this->session->data['success'] = str_replace('%1', $x, $this->language->get('success_order') );
                    $this->updateDocumentPivot( $update_document_pivot, 'order' );
                    break;
                case 3:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_order_license_expired');
                    break;
                default:
                    $this->session->data['ikros_error'][] = $this->language->get('errors_order_unknown');
                    break;
            }
        }else{
            $this->session->data['success'] = $this->language->get('success_no_order');
        }
    }
    //------------------------------------------------------------------------------------------------------------------











    /*******************************************************************************************************************
     * Vráti id defaultnej kategórie Kros
     * @return int
     ******************************************************************************************************************/
    public function getIdIkrosCategory(){
        $sql = "SELECT  `".DB_PREFIX."category_description`.`category_id`
                FROM    `".DB_PREFIX."category_description`
                WHERE   `".DB_PREFIX."category_description`.`name` ='Kros' AND
		                `".DB_PREFIX."category_description`.`language_id` = ".$this->getDefaultLanguageId();
        $result = $this->db->query( $sql );
        if ($result->rows) {
            return $result->row['category_id'];
        }
        return null;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Vrati pole nastavení skladu vychodieho jazyka
     * Stock si nastaví užívateľ, ak nie, nastaví sa prvý v poradí
     * @return array
     ******************************************************************************************************************/
    public function getStocks() {
        $sql = "SELECT * FROM `".DB_PREFIX."stock_status`
                WHERE `language_id` = ".$this->getDefaultLanguageId();
        $result = $this->db->query( $sql );
        return $result;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return int
     ******************************************************************************************************************/
    protected function getDefaultLanguageId() {
        $code = $this->config->get('config_language');
        $sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = '$code'";
        $result = $this->db->query( $sql );
        $language_id = 1; //default
        if ($result->rows) {
            foreach ($result->rows as $row) {
                $language_id = $row['language_id'];
                break;
            }
        }
        return $language_id;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Vrati pole všetkých jazykov
     * @return mixed
     ******************************************************************************************************************/
    protected function getLanguages() {
        $query = $this->db->query( "SELECT * FROM `".DB_PREFIX."language` WHERE `status`=1 ORDER BY `code`" );
        return $query->rows;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * V prípade ak užívateľ odstránil určitý produkt, je potrebné ho odstrániť aj z pivotu
     ******************************************************************************************************************/
    private function clearPivotTable(){
        $sql = " SELECT `".DB_PREFIX."ikros_products_pivot`.`product_id`
                FROM `".DB_PREFIX."ikros_products_pivot`
                LEFT JOIN `".DB_PREFIX."product` USING(product_id)
                WHERE `".DB_PREFIX."product`.`product_id` is null";
        $query = $this->db->query($sql);

        if($query->num_rows){
            $sql = "DELETE FROM `".DB_PREFIX."ikros_products_pivot` WHERE `product_id` in('0' ";
            foreach($query->rows as $row){
                $sql .=", '".$row['product_id']."'";
            }
            $sql .=")";
            $this->db->query($sql);
        }
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * Vráti pivot tabuľku id pre OC a id pre Ikros
     * @return array
     ******************************************************************************************************************/
    private function getAvailablePivotKeys(){
        $this->clearPivotTable();

        $sql = "SELECT `product_id`, `product_id_ikros`
                FROM `".DB_PREFIX."ikros_products_pivot`";
        $result = $this->db->query( $sql );
        $pivot_table = array();
        foreach ($result->rows as $row) {
            $pivot_table[$row['product_id']] = $row['product_id_ikros'];
        }
        return $pivot_table;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * return tax_class_id alebo 0
     * vrati id dane zananej sadzby alebo nulu ak sa id pre sadzbu nenajde
     * @param $vat
     * @return int
     ******************************************************************************************************************/
    protected function transformTax($vat){
        $sql = "SELECT `tax_class_id`, `rate` FROM `".DB_PREFIX."tax_class`
                    LEFT JOIN `".DB_PREFIX."tax_rule` USING (`tax_class_id`)
                    LEFT JOIN `".DB_PREFIX."tax_rate` USING (`tax_rate_id`)";
        $query = $this->db->query($sql);

        foreach($query->rows as $result){
            if($result['rate'] == $vat){
                return $result['tax_class_id'];
            }
        }
        return 0;   //defaultná tax_class_id
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * pomocná/servisná funkcia zgrupujúca sql dotazy
     ******************************************************************************************************************/
    private function simpleQuery($sql){
        //echo $sql . "<br>";
        $this->db->query($sql);
    }
    //------------------------------------------------------------------------------------------------------------------


    /*******************************************************************************************************************
     * HLAVNÁ METÓDA PRE IMPORTOVANIE/AKTUALIZÁCIU PRODUKTOV
     ******************************************************************************************************************/
    public function download(){

        if( !$this->pivotProductsExist() ){
            $this->session->data['ikros_error'][] = $this->language->get('success_pivot_not_exist');
            return ;
        }

        //krok1: skontroluj existenciu defaultnej kategorie
        $default_category = $this->getIdIkrosCategory();
        if( !isset($default_category)){
            $msg =  $this->language->get( 'error_default_ikros_category' ) ;
            $this->log->write( $msg );

            $this->session->data['ikros_error'][] = $this->language->get( 'error_default_ikros_category' ) ;
            return ;
        }


        //krok:2 od kedy sa ma nacitat
        if($this->config->has('ikros_last_import')){
            $this->last_upload_product = str_replace(' ', 'T', $this->config->get('ikros_last_import')).':00';
        }else{
            $this->last_upload_product = '2012-01-01T12:00:00';
        }


        //krok3: stiahni nové produkty
        $productInteo = json_decode( $this->getProductInteo() );


        //krok4: ak nie je nič nové, ukonči kvôli uspore vykonu
        if(empty($productInteo->products)){
            $this->session->data['success'] = $this->language->get( 'success_ikros_items_empty' );
            return;
        }


        //krok 5: inicializacia parametrov pre všetky položky
        $languages = $this->getLanguages();
        $default_store = 0;
        $default_layout = 0;
        $default_zero_value = 0;
        $default_one_value = 1;

        $default_weight_class_id = $this->config->get( 'config_weight_class_id' );
        $default_length_class_id = $this->config->get( 'config_length_class_id' );

        if($this->config->has( 'ikros_default_stock_status_id' )){
            $default_stock_status_id = $this->config->get( 'ikros_default_stock_status_id' );
        }else{
            $default_stock_status_id = $this->getStocks()->rows[0]['stock_status_id'];
        }

        $pivot_keys = $this->getAvailablePivotKeys(); //zoznam produktov z pivotu

        // upozornenia pre užívateľa
        $message_data = array(
            'new_product'=>0,
            'update_product'=>0
        );



        //--------------------
        // filter duplicit v ikrose
        $produkty = array();

        foreach ($productInteo->products as $product) {
            if(!array_key_exists($product->stockCardNumber, $produkty)){
                $produkty[$product->stockCardNumber] = $product;
            }else{
                //ak prechádzaný produkt (s rovnakým číslom) je starší než už už uložený v produktoch, musí sa nahradiť práve prechádzaným
                if( new DateTime($produkty[$product->stockCardNumber]->timestamp) < new DateTime($product->timestamp)){
                    $produkty[$product->stockCardNumber] = $product;
                }
            }
        }
        //-----------------------------

        //cyklus pre aktualizovanie položiek z ikrosu
        foreach ($productInteo->products as $product) {

            $good = array(); //produktove pole

            $stockCardNumber = $product->stockCardNumber;

            // insert alebo update podľa hodnoty $product_id_in_pivot = id prodktu v oc
            $product_id_in_pivot = null;
            if ( in_array( $stockCardNumber , $pivot_keys) ){
                $product_id_in_pivot_array = array_keys($pivot_keys, $stockCardNumber);
                $product_id_in_pivot = $product_id_in_pivot_array[0];
            }


            //zistim ktore položky sa majú prenášať
            //nastavenie v ikrose, určité polia majú buď hodotu, alebo null
            //pri prvom korektnom prenose majú hodnoty nezávisle na nastavení - teda prvý import stále s hodnotami
            $items_for_update = array();
            foreach (array('name', 'description', 'count', 'price') as $tag){
                if($product->$tag){
                    $items_for_update[$tag] = $product->$tag;
                }
            }


            //************** prerobiť, pole netreba, ukladaj rovno premenné  ******************//
            $good['model']              = "Ikros";
            $good['sku']                = "";
            $good['upc']                = "";
            $good['ean']                = $product->ean;
            $good['jan']                = "";
            $good['isbn']               = "";
            $good['mpn']                = "";
            $good['location']           = "";
            $good['quantity']           = $product->count;
            $good['stock_status_id']    = $default_stock_status_id;   //language,  form control
            $good['manufacturer_id']    = $default_zero_value;
            $good['shipping']           = $default_one_value;
            $good['price']              = $product->price;
            $good['points']             = $default_zero_value;
            $good['tax_class_id']       = $this->transformTax($product->vat);
            $good['date_available']     = substr( $product->timestamp, 0, 10 );
            $good['weight']             = $default_zero_value;
            $good['weight_class_id']    = $default_weight_class_id;
            $good['length']             = $default_zero_value;
            $good['width']              = $default_zero_value;
            $good['height']             = $default_zero_value;
            $good['length_class_id']    = $default_length_class_id;
            $good['subtract']           = $default_one_value;
            $good['minimum']            = $default_one_value;
            $good['sort_order']         = $default_zero_value;
            $good['status']             = $default_one_value;
            $good['viewed']             = $default_zero_value;
            $good['date_added']         = str_replace( 'T', ' ', $product->timestamp) ;

            $good['name']               = $product->name;
            $good['description']        = $product->description;


            /*************
             * oc_product
             ************/
            if(!$product_id_in_pivot){
                $sql = "INSERT INTO `".DB_PREFIX."product` (";

                $sql .="`model`,`sku`,`upc`,`ean`, `jan`, `isbn`, `mpn`,
                `location`, `quantity`,`stock_status_id`, `manufacturer_id`, `shipping`, `price`, `points`,
                `tax_class_id`, `date_available`, `weight`, `weight_class_id`,
                `length`, `width`, `height`, `length_class_id`,
                `subtract`,`minimum`, `sort_order`, `status`,
                `viewed`, `date_added`, `date_modified`) VALUES(";

                $sql .="'$good[model]', '$good[sku]', '$good[upc]', '$good[ean]', '$good[jan]', '$good[isbn]', '$good[mpn]', '$good[location]',";
                $sql .= isset($items_for_update['count']) ? $good['quantity']."," : "'0',";
                $sql .="$good[stock_status_id], $good[manufacturer_id], $good[shipping], ";
                $sql .= isset($items_for_update['price']) ? $good['price']."," : "'0',";
                $sql .= "$good[points], $good[tax_class_id], '$good[date_available]', $good[weight], $good[weight_class_id],
                $good[length], $good[width], $good[height], $good[length_class_id], $good[subtract], $good[minimum],
                $good[sort_order], $good[status], $good[viewed], '$good[date_added]', '$good[date_added]' )";

                $message_data['new_product'] += 1;
            }
            else {

                $sql = "UPDATE `".DB_PREFIX."product` SET ";
                $sql .= isset($items_for_update['count']) ? "`quantity`='".$good['quantity']."'," : "";
                $sql .= isset($items_for_update['price']) ? "`price` = '".$good['price']."'," : "";
                $sql .= isset($items_for_update['price']) ? "`tax_class_id` = '".$good['tax_class_id']."'," : "";
                $sql .= "`date_modified` = '".$good['date_added']."'
                WHERE product_id='".(int)$product_id_in_pivot."'";

                $message_data['update_product'] += 1;
            }
            $this->simpleQuery($sql);


            $good['product_id'] = $this->db->getLastId();


            /*************
             * oc_product_description
             ************/
            $name                   = isset($items_for_update['name']) ? $this->db->escape($items_for_update['name']) : '';
            $description            = isset($items_for_update['description']) ? $this->db->escape($items_for_update['description']) : '';
            $meta_description       =  '';
            $tag                    = '';
            $meta_keyword           =  '';

            if(!$product_id_in_pivot) {
                foreach ($languages as $language) {
                    $language_id = $language['language_id'];

                    $sql = "INSERT INTO `" . DB_PREFIX . "product_description` (`product_id`, `language_id`, `name`, `description`, ";
                    $sql .= "`tag`, `meta_description`, `meta_keyword`) VALUES ";
                    $sql .= "( $good[product_id], $language_id, '$name', '$description', ";

                    $sql .= "'$tag', '$meta_description', '$meta_keyword' );";
                    $this->simpleQuery($sql);
                }
            }


            else{
                if( isset( $items_for_update['name'] ) || isset( $items_for_update['description'] ) ){
                    $sql = "UPDATE `" . DB_PREFIX . "product_description` SET ";
                    $sql .= isset($items_for_update['name']) ? "`name` = '".$name."'," : "";
                    $sql .= isset($items_for_update['description']) ? "`description` = '".$description."'," : "";
                    $sql = rtrim($sql, ',');
                    $sql .= " WHERE `product_id`='".(int)$product_id_in_pivot."'";
                    $this->simpleQuery($sql);
                }
            }


            /*************
             * oc_product_to_store len pre nove produkty
             ************/
            if(!$product_id_in_pivot) {
                $sql = "INSERT INTO `" . DB_PREFIX . "product_to_store` (`product_id`,`store_id`)
                        VALUES ($good[product_id], $default_store);";
                $this->simpleQuery($sql);
            }

            /*************
             * oc_product_to_layout len pre nove produkty
             ************/
            if(!$product_id_in_pivot) {
                $sql = "INSERT INTO `".DB_PREFIX."product_to_layout`(`product_id`, `store_id`, `layout_id`) VALUES";
                $sql .= "($good[product_id], $default_store, $default_layout)";
                $this->simpleQuery($sql);

            }

            /*************
             * oc_ikros_products_pivot pre nove produkty vytvori prepojenia, pri existujúcich prepisuje typId a SequinceCode - ochrana proti duplicitam v ikrose
             ************/
            if(!$product_id_in_pivot) {
                $sql = "INSERT INTO `".DB_PREFIX."ikros_products_pivot`(`product_id`,`product_id_ikros`, `numbering_sequence_code`, `type_id`)
                VALUES ($good[product_id], '$product->stockCardNumber', '$product->numberingSequenceCode', $product->typeId )";
                $this->simpleQuery($sql);
            }else{
                $sql = "UPDATE `".DB_PREFIX."ikros_products_pivot` SET `numbering_sequence_code`='$product->numberingSequenceCode', `type_id`='$product->typeId'
                        WHERE `product_id_ikros`='$product->stockCardNumber'";
                $this->simpleQuery($sql);
            }

            //product_to_category
            if(!$product_id_in_pivot) {
                $sql = "INSERT INTO `".DB_PREFIX."product_to_category`(`product_id`, `category_id`) VALUES ($good[product_id], $default_category)";
                $this->simpleQuery($sql);
            }else{
                //check if exist, write only if not exist
                $sql = "SELECT `product_id`, `category_id` FROM `".DB_PREFIX."product_to_category`
                        WHERE   `product_id` = '".$product_id_in_pivot."' AND
                                `category_id` = '".$default_category."'";
                $query = $this->db->query($sql);
                if(!$query->num_rows){
                    $sql = "INSERT INTO `".DB_PREFIX."product_to_category`(`product_id`, `category_id`) VALUES ($product_id_in_pivot, $default_category)";
                    $this->simpleQuery($sql);
                }
            }
        }


        //krok x: aktualizuj čas poslednej aktualizácie
        $sql = "UPDATE `".DB_PREFIX."setting` SET `value`='".date('Y-m-d H:i')."' WHERE `key`='ikros_last_import'";
        $this->simpleQuery($sql);


        //krok x: info pre užívateľa v admin rozhraní
        $this->session->data['success'] = str_replace(
            array('%1', '%2'),
            array($message_data['new_product'], $message_data['update_product']),
            $this->language->get('success_product_import') );

    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return string
     * Notifikácia nová verzia - dôležité bezpečnostné upozornenia
     ******************************************************************************************************************/
    public function getNotifications() {
        $language_code = $this->config->get( 'config_admin_language' );

        $result = $this->curl_get_contents("http://www.openquiz.eu/index.php?route=message/information&tool=ikros&version=1.2&server=".HTTP_SERVER."&ocver=".VERSION);

        if (stripos($result,'<html') !== false) {

            return '';
        }
        return $result;
//        $json = "Vaše rozšírenie Ikros/Opencart ver. 1.0. je aktuálne. Žiadne nové aktualizácie nie sú k dispozícii.";
//        return $json;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return string
     * Notifikácia nová verzia - dôležité bezpečnostné upozornenia
     ******************************************************************************************************************/
    protected function curl_get_contents($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
    //------------------------------------------------------------------------------------------------------------------




    /*******************************************************************************************************************
     * @return bool|mixed|null
     * upraví status licencie podľa dodaného statusu pre zobrazenie na hlavnej stránke administrácie
     ******************************************************************************************************************/
    public function parseLicence(){
        $license = $this->getLicence();
        $license = json_decode($license);

        if (isset($license->license->status)){
            $license->license->status = $this->language->get( 'entry_license_'.$license->license->status );
        }
        return $license;
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return bool
     * Kontorla existencie pivot tabuľky
     */
    public function pivotProductsExist(){
        $query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."ikros_products_pivot'" );
        return  ($query->num_rows > 0);
    }
    //------------------------------------------------------------------------------------------------------------------



    /*******************************************************************************************************************
     * @return bool
     * Kontorla existencie pivot tabuľky
     */
    public function pivotOrdersExist(){
        $query = $this->db->query( "SHOW TABLES LIKE '".DB_PREFIX."ikros_document_pivot'" );
        return  ($query->num_rows > 0);
    }
    //------------------------------------------------------------------------------------------------------------------

    /*******************************************************************************************************************
     * @return mixed
     * Štart aplikácie - vytvorenie pivot tabuľky
     ******************************************************************************************************************/
    public function createPivot(){

        if($this->pivotProductsExist() && $this->pivotOrdersExist()){
            $this->session->data['success'] = $this->language->get( 'success_pivot_exist' );
            return;
        }

        $sql = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ikros_products_pivot` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `product_id` int(11) NOT NULL,
                    `product_id_ikros` varchar(20) NOT NULL,
                    `numbering_sequence_code` varchar(20) DEFAULT NULL,
                    `type_id` tinyint(2) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`,`product_id_ikros`,`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $query_products = $this->db->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS `".DB_PREFIX."ikros_document_pivot` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `order_id` int(11) NOT NULL,
                   `order_modified` datetime NOT NULL,
                   `invoice_modified` datetime NOT NULL,
                PRIMARY KEY (`id`,`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $query_orders = $this->db->query($sql);

        if($query_products && $query_orders){
            $this->session->data['success'] = $this->language->get( 'success_pivot_create' );
        }
        elseif($query_products || $query_orders){
            $this->session->data['ikros_error'][] = $this->language->get( 'error_pivot_create_partially' );
        }
        else {
            $this->session->data['ikros_error'][] = $this->language->get('error_pivot_create');
        }
        return;
    }
    //------------------------------------------------------------------------------------------------------------------






}

?>
