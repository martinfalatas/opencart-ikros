<?php
// Heading
$_['heading_title']                         = 'Ikros';
//
//// Text

$_['text_success_settings']                 = 'Úspech: Úspešne ste aktualizovali ikros nastavenia!';
$_['text_none']                             = '---Žiadna---';
$_['text_orders']                           = 'Objednávky a Faktúry';
$_['text_products']                         = 'Produkty';
$_['text_connect']                          = 'Autorizácia';
$_['text_personal']                         = 'Osobné údaje na dokladoch';

$_['text_loading_notifications']            = 'Vyhľadávam aktualizácie programu.';
$_['error_notifications']                   = 'Nemôžem sa pripojiť k servisným službám';
$_['text_retry']                            = 'Refresh';
$_['error_no_news']                         = 'Žiana správa';

$_['text_maintenance']                      = 'Obmezenia Exportov';
$_['text_cron']                             = 'Cron';

$_['text_cron_product']                     = 'Produkty';
$_['text_cron_order']                       = 'Objednávky';
$_['text_cron_invoice']                     = 'Faktúry';
//
$_['text_product_description_none']         = 'Ponechať Prázdne';
$_['text_product_description_desc']         = 'Text Produktu';
$_['text_product_description_opt']          = 'Možnosti Produktu';
$_['text_product_description_model']        = 'Model';

$_['text_company_replacement_company']      = 'Ponechať Firmu';
$_['text_company_replacement_name']         = 'Meno Priezvisko';

$_['entry_product_description']             = 'Popis položky';
$_['help_product_description']              = 'Výber sa zobrazí ako popis položky na objednávke/faktúre.';
$_['entry_company_replacement']             = 'Výmena adresného poľa Firmy';
$_['help_company_replacement']              = 'V prípade ak Vaši zákazníci nemajú firmu alebo IČO, môžete toto pole nahradiť iným údajom.';
//
$_['entry_fill_pivot_document']             = 'Vypnúť odoslanie starých objednávok';
$_['help_fill_pivot_document']              = 'Staré objednávky nebudú odoslané do aplikácie Ikros. Odosielať sa budú len objednávky pridané/upravované po aktivácii nástroja';
$_['entry_clear_pivot_document']            = 'Opätovné odoslanie všetkých objednávok';
$_['help_clear_pivot_document']             = 'Po aktivácii sa všetky objednávky v e-shope budú považovať za neodoslané. Pri najbližšom exporte sa do aplikácii Ikros odošlú všetky objednávky.';

$_['entry_step_document']                   = 'Krokovanie importu dokumentov';
$_['help_step_document']                    = 'Dokumenty sa budú exportovať jednorazovo len v počte uvedenom v poli. Pre export všetkých dokumentov bude potrebné spustiť export viac krát. Ponechajte prázne ak pri exportovaní veľkého množstva dokumentov pri prvom importe nemáte problém.';
$_['entry_order_status']                    = 'Stav objednávky';
$_['help_order_status']                     = 'Odošlú sa len objednávky/faktúry ktorých stav je jedným zo zvolených stavov';


$_['entry_ico_in_shipping']                 = 'Odoslať pole Firma do poštovej adresy';
$_['help_ico_in_shipping']                  = "Ak zvolíte 'Áno', do aplikácie Ikros prenesiete pole pre názov firmy v poštovej adrese. Záreveň sa automaticky zvolí možnosť 'Poštu posielať na inú adresu' a v poli pre odberateľa sa uvedie poštová adresa";

$_['text_yes']                              = 'Áno';
$_['text_no']                               = 'Nie';

$_['entry_import_products']                 = 'Import produktov z Ikrosu';
$_['entry_last_import']                     = 'Uložené produkty staršie než: ';
$_['entry_last_import_default']             = '0000-00-00 00:00';
$_['entry_defaul_stock_id']                 = 'Defaultný stav vypredania';
$_['entry_tax_class']                       = 'Daňová trieda pre dopravu';
$_['entry_coupon_tax_class']                = 'Daňová trieda pre kupóny';
$_['entry_order_start_text']                = 'Úvodný text objednávky';
$_['entry_order_end_text']                  = 'Záverečný text objednávky';
$_['entry_invoices_start_text']             = 'Úvodný text faktúry';
$_['entry_invoices_end_text']               = 'Záverečný text faktúry';
$_['entry_document_discount']               = 'Percento zľavy za celý doklad';
$_['entry_due_date']                        = 'Dátum splatnosti faktúry';
$_['entry_authorization_key']               = 'Autorizačný kľúč';
$_['entry_cron']                            = 'Cron';
$_['entry_entry_key']                       = 'Na karte Nastavenia v sekcii Pripojenie vložte platný autorizačný kľúč';
$_['entry_license_0']                       = "Všeobecná chyba";
$_['entry_license_2']                       = "Licencia je platná";
$_['entry_license_4']                       = "Licencia je platná, no v priebehu niekoľkých dní expiruje";
$_['entry_license_8']                       = "Licencia nie je platná, ale sťahovanie/odosielanie ešte funguje";
$_['entry_license_16']                      = "Licencia nie je platná. Jej platnosť vypršala dávnejšie.";
$_['entry_license_32']                      = "Licencia nie je platná. E-shop bol zmazaný.";
$_['entry_sender_bank_account']             = "Číslo účtu";
$_['entry_sender_bank_iban']                = "IBAN";
$_['entry_sender_bank_swift']               = "SWIFT";
$_['entry_convert_order_number']            = "Formát číslovania dokladov";
$_['entry_custom_format']                   = "Vlastný formát";

$_['entry_cron_key']                        = 'Heslo pre cron';
$_['help_cron_key']                         = 'Zadajte aspoň 5 alfanumerických znakov. Abecedné znaky malým písmom. Cron úlohy môže spúšťať len užívateľ s platným heslom.';
$_['error_cron_key']                        = 'Nesprávny formát hesla. Zadajte aspoň 5 alfanumerických znakov alebo ponechajte prázdne.';


$_['help_tax_class']                        = 'Zadajte prosím daňovú triedu, ktorá je priradená Vašej doprave.<br />Ak používate viac tried, môžete si ich neskôr upraviť v Ikrose';
$_['help_coupon_tax_class']                 = 'Zadajte daňovú triedu pre kupón ak je kupón na objednávke uvedený pred daňou.';
$_['help_defaul_stock_id']                  = 'Stav zobrazenia, keď nie je výrobok na sklade.<br />Aplikuje sa len pre novopridané produkty';
$_['help_document_discount']                = 'Zadajte číslo celé, desatinné, alebo ponechajte prázdne. Oddeľovač desatinných mist je bodka.';
$_['help_due_date']                         = 'Zadajte celé číslo. Dátum splatnosti sa nastaví ako dátum poslednej zmeny objednávky + počet zadaných dní.';
$_['help_authorization_key']                = 'Kľúč získate v aplikácii Ikros v časti Nastavenia - Prepojenie s e-shopom';
$_['help_sender_bank_account']              = 'Vzor: 1234567890';
$_['help_sender_bank_iban']                 = 'Vzor: SK9802000000001234567890';
$_['help_sender_bank_swift']                = 'Vzor: SUBASKBX';
$_['help_convert_order_number']             = "Rok- RR alebo RRRR<br />Mesiac - MM<br />Deň - DD<br />Číslo - C rôzny počet, prázne C budú vyplnené 0";
$_['help_last_import']                      = "Vzor: 2017-12-31 23:59 <br />Importujú sa produkty nové/zmenené od zadaného času";
$_['help_cron']                             = 'Pri spustení cron úloh sa budú prenášať len zvolené položky';



$_['error_document_discount']               = 'Zadajte číslo celé, desatinné, alebo ponechajte prázdne. Oddeľovač desatinných mist je bodka.';
$_['error_document_discount_format']        = 'Chyba: Ikros-Nastavenia -> Nesprávny formát zľavy za doklad.';
$_['error_due_date']                        = 'Zadajte celé číslo. Dátum splatnosti sa nastaví ako dátum zadania objednávky kupujúcim + počet zadaných dní.';
$_['error_due_date_format']                 = 'Chyba: Ikros-Nastavenia -> Nesprávny formát dátumu splatnosti faktúry.';
$_['error_default_ikros_category']          = "Ikros: Musí byť vytvorená defaultná kategória s názvom 'Kros'!";
$_['error_ikros_last_import']               = "Nesprávny formát času. Vzor: YYYY-MM-DD HH:mm (Rok-mesiac-deň hodina:minúta).";
$_['error_sender_bank_account']             = "Nesprávny formát čísla účtu";
$_['error_step_document']                   = "Nesprávny formát. Zadajte len číslice, alebo ponechajte prázne";

$_['warning_settings']                      = 'Chybné nastavenie: ';

// Tabs
$_['tab_import_export']                     = 'Import / Export';

$_['tab_settings']                          = 'Nastavenia';
$_['tab_delete']                            = 'Údržba';
$_['tab_general']                           = 'Hlavné';
$_['tab_tool']                              = 'Nástroje';
$_['tab_initialization']                    = 'Štart aplikácie';


$_['button_export_orders']                  = 'Export objednávok';
$_['button_export_invoices']                = 'Export faktúr';
$_['button_import_products']                = 'Import produktov';
$_['button_active_document']                = 'Aktivovať';
$_['button_settings']                       = 'Uložiť nastavenia';
$_['button_delete_products']                = 'Odstrániť všetky produkty';
$_['button_clean_db']                       = 'Vrátiť do pôvodného stavu';
$_['button_initial']                        = 'ŠTART';



$_['success_ikros_items_empty']             = "Úspech: Žiadne nové položky na stiahnutie.";
$_['success_product_import']                = 'Úspech: Aktualizácia prebehla úspešne. Počet nových produktov: %1  Počet aktualizovaných produktov: %2';
$_['success_clean']                         = 'Ďakujem za udržiavanie čistoty na pracovisku. Pred odchodom nezabudnite odstrániť/znefunkčniť Váš autorizačný kód. Prajem Vám pekný deň.';
$_['success_clean_db']                      = 'Obchod je pripravený na importovanie Vašich produktov.';
$_['success_pivot_create']                  = 'Spúšťací skript skončil úspechom. Rozšírenie je pripravené na použitie.';
$_['success_pivot_exist']                   = 'Spúšťací skript skončil úspechom. Rozšírenie je pripravené na použitie.';
$_['success_pivot_not_exist']               = "Nie je možné importovať produkty. Chýba prevodová tabuľka. Prevodovú tabuľku vytvoríte v sekcii 'Štart aplikácie'";
$_['error_pivot_create']                    = "Spúštací skript skončil neúspechom. V prípade ak nie je dostupná karta 'Štart aplikácie', pravdepodobne bol skript už raz spustený Preverte prosím spojenis s databázou.";
$_['error_pivot_create_partially']          = "Spúštací skript skončil čiastočným neúspechom. Vrátte sa do karty 'Štart aplikácie' a postupujte podľa pokynov.";
$_['success_pivot_doc_fill']                = " Všetky objednávky a faktúry boli označené ako odoslané. Ak si želáte odoslať ich znova, otvorte a uložte požadovanú objednávku a následne exportujte";
$_['success_pivot_doc_clear']               = " Všetky objednávky a faktúry boli označené ako neodoslané.";

//orders
$_['errors_order_general_eror']             = 'Chyba: Ikros / Odosielanie objednávok. Všeobecná chyba';
$_['success_order']                         = 'Úspech: Úspešne ste odoslali objednávky (%1) na spracovanie';
$_['errors_order_license_expired']          = 'Chyba: Ikros / Odosielanie objednávok. Licencia expirovala';
$_['errors_order_unknown']                  = 'Chyba: Ikros / Odosielanie objednávok. Počas odosielania objednávok došlo k neznámej chybe. Neočakávaný návratový kód.';
$_['success_no_order']                      = 'Úspech: Žiadne nové objednávky na odoslanie.';
//invoice
$_['errors_invoice_general_eror']           = 'Chyba: Ikros / Odosielanie faktúr. Všeobecná chyba';
$_['success_invoice']                       = 'Úspech: Úspešne ste odoslali faktúry (%1) na spracovanie';
$_['errors_invoice_license_expired']        = 'Chyba: Ikros / Odosielanie faktúr. Licencia expirovala';
$_['errors_invoice_unknown']                = 'Chyba: Ikros / Odosielanie faktúr. Počas odosielania objednávok došlo k neznámej chybe. Neočakávaný návratový kód.';
$_['success_no_invoice']                    = 'Úspech: Žiadne nové faktúry na odoslanie.';
$_['error_convert_order_number']            = 'Chyba: Nesprávny formát číslovania dokumentov. Formát musí obsahovať minimálne jedno C';
$_['error_sender_bank_iban']                = 'Chyba: Nesprávny formát iban účtu';





?>