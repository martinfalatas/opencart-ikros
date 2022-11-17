<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $back; ?>" data-toggle="tooltip" title="<?php echo $button_back; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>



	<div class="container-fluid">
		<?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
        <?php foreach($error_warning as $warning){ ?>
            <br />
		<?php echo $warning; } ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
		<?php } ?>

		<?php if ($success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i><?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ((!$error_warning) && (!$success)) { ?>
        <div id="ikros_notification" class="alert alert-info"><i class="fa fa-info-circle"></i>
            <div id="ikros_loading"><img src="view/image/ikros/loading.gif" /><?php echo $text_loading_notifications; ?></div>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-body">

				<ul class="nav nav-tabs">
                    <?php if($pivot_products && $pivot_orders) { ?>
                    <li><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                    <li><a href="#tab-settings" data-toggle="tab"><?php echo $tab_settings; ?></a></li>
                    <li class="active"><a href="#tab-export" data-toggle="tab"><?php echo $tab_import_export; ?></a></li>
                    <li><a href="#tab-tool" data-toggle="tab"><?php echo $tab_tool; ?></a></li>

					<?php } else { ?>
                    <li class="active"><a href="#tab_initialization" data-toggle="tab"><?php echo $tab_initialization; ?></a></li>
				    <?php } ?>
                </ul>

				<div class="tab-content">

                <?php  if( $pivot_products && $pivot_orders ) { ?>

                    <div class="tab-pane" id="tab-general">
                        <form action="<?php echo $import_products; ?>" method="post" enctype="multipart/form-data" id="import-products" class="form-horizontal">
                            <table class="table table-hover">
                                <?php


                                if(isset($license->license)){ ?>

                                <tr>
                                    <td>
                                        <address>
                                            <strong><?php echo $license->eshopInfo->name; ?></strong><br>
                                            <?php echo $license->eshopInfo->street; ?> <br>
                                            <?php echo $license->eshopInfo->town . " " . $license->eshopInfo->postCode; ?>

                                        </address>
                                    </td><td></td>
                                </tr>

                                <tr>
                                    <td>IČO</td><td> <?php echo $license->eshopInfo->taxId; ?> </td>
                                </tr>
                                <tr>
                                    <td>DIČ</td><td> <?php echo $license->eshopInfo->vatId; ?> </td>
                                </tr>
                                <tr><td></td><td></td></tr>
                                <tr>
                                    <td>Status</td><td> <?php echo $license->license->status; ?> </td>
                                </tr>
                                <tr>
                                    <td>Expirácia</td><td> <?php echo str_replace('T', ' ', $license->license->validUntil); ?> </td>
                                </tr>



                                <?php }
                                 elseif ( isset($license->code) && isset($license->errorType) && isset($license->headerLocation) && isset($license->message) ){
                                ?>
                                <tr>
                                    <td>Error Type</td><td> <?php echo $license->errorType; ?> </td>
                                </tr>
                                <tr>
                                    <td>Code</td><td> <?php echo $license->code; ?> </td>
                                </tr>
                                <tr>
                                    <td>Error Location</td><td> <?php echo $license->headerLocation; ?> </td>
                                </tr>
                                <tr>
                                    <td>Message</td><td> <?php echo $license->message; ?> </td>
                                </tr>
                                <tr>
                                    <td>Solution</td><td> <?php echo $entry_entry_key; ?> </td>
                                </tr>

                               <?php } else {
                                //var_dump($license);
                                ?>

                                <tr>
                                    <td>Message</td><td> <?php echo $license->message; ?> </td>
                                </tr>
                                <tr>
                                    <td>Message Detail</td><td> <?php echo $license->messageDetail; ?> </td>
                                </tr>

                                <?php } ?>


                            </table>
                        </form>
                    </div>


                    <div class="tab-pane" id="tab-settings">
                        <form action="<?php echo $settings; ?>" method="post" enctype="multipart/form-data"
                              id="settings" class="form-horizontal">

                            <fieldset>
                                <legend><?php echo $text_products; ?></legend>




                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-stock-status">
                                        <span data-toggle="tooltip" title="<?php echo $help_defaul_stock_id; ?>"><?php echo $entry_defaul_stock_id; ?></span>
                                    </label>
                                    <div class="col-sm-3">
                                        <select name="ikros_default_stock_status_id" id="input-stock-status" class="form-control">
                                            <?php foreach ($stock_statuses as $stock_status) { ?>
                                            <?php if ($stock_status['stock_status_id'] == $ikros_default_stock_status_id) { ?>
                                            <option value="<?php echo $stock_status['stock_status_id']; ?>" selected="selected"><?php echo $stock_status['name']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $stock_status['stock_status_id']; ?>"><?php echo $stock_status['name']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-date-available">
                                        <span data-toggle="tooltip" title="<?php echo $help_last_import; ?>"><?php echo $entry_last_import; ?></span>
                                    </label>

                                    <div class="col-sm-3">
                                            <input type="text" name="ikros_last_import" id="input-date-available"
                                                   value="<?php echo $ikros_last_import; ?>"
                                                   placeholder="<?php echo $entry_last_import_default; ?>"
                                                   class="form-control"/>
                                        <?php if ($error_ikros_last_import) { ?>
                                        <div class="text-danger"><?php echo $error_ikros_last_import; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-product-description">
                                        <span data-toggle="tooltip" title="<?php echo $help_product_description; ?>"><?php echo $entry_product_description; ?></span>
                                    </label>
                                    <div class="col-sm-3">
                                        <select name="ikros_product_description_id" id="input-product-description" class="form-control">
                                            <?php foreach ($product_description_options as $option) { ?>
                                            <?php if ($option['id'] == $ikros_product_description_id) { ?>
                                            <option value="<?php echo $option['id']; ?>" selected="selected"><?php echo $option['text']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $option['id']; ?>"><?php echo $option['text']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                            </fieldset>



                            <!-- OBJEDNÁVKY a FAKTúRY -->
                            <fieldset>
                                <legend><?php echo $text_orders; ?></legend>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-order-text-start"><?php echo $entry_order_start_text; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_order_start_text"
                                               value="<?php echo $ikros_order_start_text; ?>"
                                               placeholder="<?php echo $entry_order_start_text; ?>"
                                               id="input-order-text-start" class="form-control" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-order-text-end"><?php echo $entry_order_end_text; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_order_end_text"
                                               value="<?php echo $ikros_order_end_text; ?>"
                                               placeholder="<?php echo $entry_order_end_text; ?>"
                                               id="input-order-text-end" class="form-control" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-invoices-text-start"><?php echo $entry_invoices_start_text; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_invoice_start_text"
                                               value="<?php echo $ikros_invoice_start_text; ?>"
                                               placeholder="<?php echo $entry_invoices_start_text; ?>"
                                               id="input-invoices-text-start" class="form-control" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-invoices-text-end"><?php echo $entry_invoices_end_text; ?></label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_invoice_end_text"
                                               value="<?php echo $ikros_invoice_end_text; ?>"
                                               placeholder="<?php echo $entry_invoices_end_text; ?>"
                                               id="input-invoices-text-end" class="form-control" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-company-field-replacement">
                                        <span data-toggle="tooltip" title="<?php echo $help_company_replacement; ?>"><?php echo $entry_company_replacement; ?></span>
                                    </label>
                                    <div class="col-sm-3">
                                        <select name="ikros_company_replacement_id" id="input-company-field-replacement" class="form-control">
                                            <?php foreach ($company_replacement_options as $option) { ?>
                                            <?php if ($option['id'] == $ikros_company_replacement_id) { ?>
                                            <option value="<?php echo $option['id']; ?>" selected="selected"><?php echo $option['text']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $option['id']; ?>"><?php echo $option['text']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">
                                        <span data-toggle="tooltip" title="<?php echo $help_ico_in_shipping; ?>"><?php echo $entry_ico_in_shipping; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <label class="radio-inline">
                                            <?php if ($ikros_ico_in_shipping) { ?>
                                            <input type="radio" name="ikros_ico_in_shipping" value="1" checked="checked" />
                                            <?php echo $text_yes; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="ikros_ico_in_shipping" value="1" />
                                            <?php echo $text_yes; ?>
                                            <?php } ?>
                                        </label>
                                        <label class="radio-inline">
                                            <?php if (!$ikros_ico_in_shipping) { ?>
                                            <input type="radio" name="ikros_ico_in_shipping" value="0" checked="checked" />
                                            <?php echo $text_no; ?>
                                            <?php } else { ?>
                                            <input type="radio" name="ikros_ico_in_shipping" value="0" />
                                            <?php echo $text_no; ?>
                                            <?php } ?>
                                        </label>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-complete-status">
                                        <span data-toggle="tooltip" title="<?php echo $help_order_status; ?>"><?php echo $entry_order_status; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm" style="height: 150px; overflow: auto;">
                                            <?php foreach ($order_statuses as $order_status) { ?>
                                            <div class="checkbox">
                                                <label>
                                                    <?php if (in_array($order_status['order_status_id'], $ikros_order_status_id)) { ?>
                                                    <input type="checkbox" name="ikros_order_status_id[]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
                                                    <?php echo $order_status['name']; ?>
                                                    <?php } else { ?>
                                                    <input type="checkbox" name="ikros_order_status_id[]" value="<?php echo $order_status['order_status_id']; ?>" />
                                                    <?php echo $order_status['name']; ?>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-document-discount">
                                        <span data-toggle="tooltip" title="<?php echo $help_document_discount; ?>"><?php echo $entry_document_discount; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_document_discount"
                                               value="<?php echo $ikros_document_discount; ?>"
                                               placeholder="<?php echo $entry_document_discount; ?>"
                                               id="input-document-discount" class="form-control" />
                                        <?php if ($error_document_discount) { ?>
                                        <div class="text-danger"><?php echo $error_document_discount; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-due-date">
                                        <span data-toggle="tooltip" title="<?php echo $help_due_date; ?>"><?php echo $entry_due_date; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_due_date"
                                               value="<?php echo $ikros_due_date; ?>"
                                               placeholder="<?php echo $entry_due_date; ?>"
                                               id="input-due-date" class="form-control" />
                                        <?php if ($error_due_date) { ?>
                                        <div class="text-danger"><?php echo $error_due_date; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-tax-class"><span data-toggle="tooltip" title="<?php echo $help_tax_class; ?>"><?php echo $entry_tax_class; ?></span></label>
                                    <div class="col-sm-3">
                                        <select name="ikros_shipping_tax_class_id" id="input-tax-class" class="form-control">
                                            <option value="0"><?php echo $text_none; ?></option>
                                            <?php foreach ($tax_classes as $tax_class) { ?>
                                            <?php if ($tax_class['tax_class_id'] == $ikros_shipping_tax_class_id) { ?>
                                            <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-coupon-tax-class"><span data-toggle="tooltip" title="<?php echo $help_coupon_tax_class; ?>"><?php echo $entry_coupon_tax_class; ?></span></label>
                                    <div class="col-sm-3">
                                        <select name="ikros_coupon_tax_class_id" id="input-coupon-tax-class" class="form-control">
                                            <option value="0"><?php echo $text_none; ?></option>
                                            <?php foreach ($tax_classes as $tax_class) { ?>
                                            <?php if ($tax_class['tax_class_id'] == $ikros_coupon_tax_class_id) { ?>
                                            <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div class="form-group">

                                    <label class=" col-sm-2 control-label" for="input-convert-order-number"><span data-toggle="tooltip" title="<?php echo $help_convert_order_number; ?>"><?php echo $entry_convert_order_number; ?></span></label>
                                    <div class="has-feedback col-sm-2">
                                        <select name="ikros_convert_order_number" id="convert-order-number" class="form-control">
                                            <?php foreach ($convert_order_number as $patert) { ?>
                                            <?php if ($patert == $ikros_convert_order_number) { ?>
                                            <option value="<?php echo $patert; ?>" selected="selected"><?php echo $patert; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $patert; ?>"><?php echo $patert; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>



                                    <div class="col-sm-2 col-sm-offset-1">
                                        <label for="inlineCheckbox1" class="checkbox-inline">
                                            <?php if( ( ($ikros_convert_order_format_custom) ) ) { ?>
                                            <input type="checkbox" id="inlineCheckbox1"  name="ikros_convert_order_format_custom" value="1" checked="checked"> <?php echo $entry_custom_format; ?>
                                            <?php } else { ?>
                                            <input type="checkbox" id="inlineCheckbox1"  name="ikros_convert_order_format_custom" value="0"> <?php echo $entry_custom_format; ?>
                                            <?php } ?>
                                        </label>
                                    </div>



                                    <div class="has-feedback col-sm-2">

                                        <input type="text"
                                               name="ikros_convert_order_number"
                                               value="<?php echo $ikros_convert_order_number; ?>"
                                               id="convert-order-number-custom"  class="form-control" />
                                        <?php if ($error_convert_order_number) { ?>
                                        <div class="text-danger"><?php echo $error_convert_order_number; ?></div>
                                        <?php } ?>
                                    </div>

                                </div>


                            </fieldset>

                            <!-- PERSONAL -->
                            <fieldset>
                                <legend><?php echo $text_personal; ?></legend>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-sender-bank-account">
                                        <span data-toggle="tooltip" title="<?php echo $help_sender_bank_account; ?>"><?php echo $entry_sender_bank_account; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_sender_bank_account"
                                               value="<?php echo $ikros_sender_bank_account; ?>"
                                               placeholder="<?php echo $help_sender_bank_account; ?>"
                                               id="input-sender-bank-account" class="form-control" />
                                        <?php if ($error_sender_bank_account) { ?>
                                        <div class="text-danger"><?php echo $error_sender_bank_account; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-sender-bank-iban">
                                        <span data-toggle="tooltip" title="<?php echo $help_sender_bank_iban; ?>"><?php echo $entry_sender_bank_iban; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_sender_bank_iban"
                                               value="<?php echo $ikros_sender_bank_iban; ?>"
                                               placeholder="<?php echo $help_sender_bank_iban; ?>"
                                               id="input-sender-bank-iban" class="form-control" />
                                        <?php if ($error_sender_bank_iban) { ?>
                                        <div class="text-danger"><?php echo $error_sender_bank_iban; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-sender-bank-swift">
                                        <span data-toggle="tooltip" title="<?php echo $help_sender_bank_swift; ?>"><?php echo $entry_sender_bank_swift; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_sender_bank_swift"
                                               value="<?php echo $ikros_sender_bank_swift; ?>"
                                               placeholder="<?php echo $help_sender_bank_swift; ?>"
                                               id="input-sender-bank-swift" class="form-control" />
                                    </div>
                                </div>

                            </fieldset>


                            <!-- AUTH KEY -->
                            <fieldset>
                                <legend><?php echo $text_connect; ?></legend>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-authorization-key">
                                        <span data-toggle="tooltip" title="<?php echo $help_authorization_key; ?>"><?php echo $entry_authorization_key; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_authorization_key"
                                               value="<?php echo $ikros_authorization_key; ?>"
                                               placeholder="<?php echo $entry_authorization_key; ?>"
                                               id="input-authorization-key" class="form-control" />
                                    </div>
                                </div>

                            </fieldset>


                            <!-- CRON -->
                            <fieldset>
                                <legend><?php echo $text_cron; ?></legend>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-complete-status">
                                        <span data-toggle="tooltip" title="<?php echo $help_cron; ?>"><?php echo $entry_cron; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="well well-sm" style="height: 150px; overflow: auto;">

                                            <?php foreach ($cron_statuses as $cron_status){ ?>
                                            <div class="checkbox">
                                                <label>
                                                    <?php if (in_array( $cron_status['cron_status_id'], $ikros_cron_status_id) ){ ?>
                                                    <input type="checkbox" name="ikros_cron_status_id[]" value="<?php echo $cron_status['cron_status_id']; ?>" checked="checked" />
                                                    <?php echo $cron_status['name']; ?>
                                                    <?php } else { ?>
                                                    <input type="checkbox" name="ikros_cron_status_id[]" value="<?php echo $cron_status['cron_status_id']; ?>" />
                                                    <?php echo $cron_status['name']; ?>
                                                    <?php } ?>
                                                </label>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-cron-key">
                                        <span data-toggle="tooltip" title="<?php echo $help_cron_key; ?>"><?php echo $entry_cron_key; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                               name="ikros_cron_key"
                                               value="<?php echo $ikros_cron_key; ?>"
                                               placeholder="<?php echo $entry_cron_key; ?>"
                                               id="input-cron-key" class="form-control" />
                                        <?php if ($error_cron_key) { ?>
                                        <div class="text-danger"><?php echo $error_cron_key; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>


                            </fieldset>


                            <fieldset>
                                <legend><?php echo $text_maintenance; ?></legend>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-step-document">
                                        <span data-toggle="tooltip" title="<?php echo $help_step_document; ?>"><?php echo $entry_step_document; ?></span>
                                    </label>
                                    <div class="col-sm-2">
                                        <input type="text"
                                               name="ikros_step_document"
                                               value="<?php echo $ikros_step_document; ?>"
                                               id="input-step-document"
                                               class="form-control" />
                                        <?php if ($error_step_document) { ?>
                                        <div class="text-danger"><?php echo $error_step_document; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </fieldset>


                            <div class="form-group">
                                <div class="col-sm-2">
                                    <p class="buttons"><a onclick="updateSettings();" class="btn btn-primary"><span><?php echo $button_settings; ?></span></a></p>

                                </div>
                            </div>
                        </form>
                    </div>



                    <div class="tab-pane active" id="tab-export">

                        <form action="<?php echo $import_products; ?>" method="post" enctype="multipart/form-data"
                              id="import-products" class="form-horizontal">
                            <div class="col-sm-4 col-md-3 col-lg-2">
                                <p class="buttons"><a onclick="importProducts();"
                                                      class="btn btn-primary"><span><?php echo $button_import_products; ?></span></a>
                                </p>
                            </div>
                        </form>

                        <form action="<?php echo $export_orders; ?>" method="post" enctype="multipart/form-data"
                              id="export-orders" class="form-horizontal">
                            <div class="col-sm-4 col-md-3 col-lg-2">
                                <p class="buttons"><a onclick="uploadOrders();"
                                                      class="btn btn-primary"><span><?php echo $button_export_orders; ?></span></a>
                                </p>
                            </div>
                        </form>

                        <form action="<?php echo $export_invoice; ?>" method="post" enctype="multipart/form-data"
                              id="export-invoice" class="form-horizontal">
                            <div class="col-sm-4 col-md-3 col-lg-2">
                                <p class="buttons"><a onclick="uploadInvoice();"
                                                      class="btn btn-primary"><span><?php echo $button_export_invoices; ?></span></a>
                                </p>
                            </div>
                        </form>
                    </div>



                    <div class="tab-pane" id="tab-tool">

                        <div class="group">
                            <label class="col-sm-2 control-label" for="fill-pivot-document">
                                <span data-toggle="tooltip" title="<?php echo $help_fill_pivot_document; ?>"><?php echo $entry_fill_pivot_document; ?></span>
                            </label>
                            <div class="col-sm-10">
                                <form action="<?php echo $fill_pivot_doc; ?>" method="post" enctype="multipart/form-data"
                                      id="fill-pivot-document" class="form-horizontal">
                                    <div class="col-sm-4 col-md-3 col-lg-2">
                                        <p class="buttons"><a onclick="fillPivotDocument();"
                                                              class="btn btn-primary"><span><?php echo $button_active_document; ?></span></a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="group">
                            <label class="col-sm-2 control-label" for="clear-pivot-document">
                                <span data-toggle="tooltip" title="<?php echo $help_clear_pivot_document; ?>"><?php echo $entry_clear_pivot_document; ?></span>
                            </label>
                            <div class="col-sm-10">
                                <form action="<?php echo $clear_pivot_doc; ?>" method="post" enctype="multipart/form-data"
                                      id="clear-pivot-document" class="form-horizontal">
                                    <div class="col-sm-4 col-md-3 col-lg-2">
                                        <p class="buttons"><a onclick="clearPivotDocument();"
                                                              class="btn btn-primary"><span><?php echo $button_active_document; ?></span></a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>




                    <?php } else { ?>

                    <div class="tab-pane active" id="tab_initialization">


                        <form action="<?php echo $create_pivot; ?>" method="post" enctype="multipart/form-data"
                              id="initial-pivot" class="form-horizontal">
                            <div class="col-sm-4 col-md-3 col-lg-2">
                                <p class="buttons"><a onclick="initialPivot();"
                                                      class="btn btn-primary"><span><?php echo $button_initial; ?></span></a>
                                </p>
                            </div>
                        </form>
                    </div>

                    <?php } ?>




				</div>
			</div>
		</div>

	</div>


    <script type="text/javascript"><!--

        function getNotifications() {
            $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> <div id="ikros_loading"><img src="view/image/ikros/loading.gif" /><?php echo $text_loading_notifications; ?></div>');
            setTimeout(
                    function(){
                        $.ajax({
                            type: 'GET',
                            url: 'index.php?route=tool/ikros/getNotifications&token=<?php echo $token; ?>',
                            dataType: 'json',
                            success: function(json) {

                                if (json['error']) {

                                    $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> '+json['error']+' <span style="cursor:pointer;font-weight:bold;text-decoration:underline;float:right;" onclick="getNotifications();"><?php echo $text_retry; ?></span>');
                                } else if (json['message']) {
                                    $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> '+json['message']);
                                } else {
                                    $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> '+'<?php echo $error_no_news; ?>');
                                }
                            },
                            failure: function(){
                                $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> '+'<?php echo $error_notifications; ?> <span style="cursor:pointer;font-weight:bold;text-decoration:underline;float:right;" onclick="getNotifications();"><?php echo $text_retry; ?></span>');
                            },
                            error: function() {
                                $('#ikros_notification').html('<i class="fa fa-info-circle"></i><button type="button" class="close" data-dismiss="alert">&times;</button> '+'<?php echo $error_notifications; ?> <span style="cursor:pointer;font-weight:bold;text-decoration:underline;float:right;" onclick="getNotifications();"><?php echo $text_retry; ?></span>');
                            }
                        });
                    },
                    500
            );
        }
        //--></script>


    <script type="text/javascript"><!--

        $(document).ready(function () {
            $('a[data-toggle="tab"]').click(function() {
                $('#ikros_notification').remove();
                $('.alert-success').remove();
            });

            getNotifications();


            $('#inlineCheckbox1').change(function () {
                convertOrder();

            });
            convertOrder();

        });

        function convertOrder(){
            var
                    custom = $('#convert-order-number-custom'),
                    define = $('#convert-order-number'),
                    checkbox = $('#inlineCheckbox1');

            if (checkbox.prop('checked') )  {
                define.prop('disabled', true);
                custom.prop('disabled', false);
                checkbox.val(1);

            }else{
                define.prop('disabled', false);
                custom.prop('disabled', true);
                checkbox.val(0);
            }

        }

        function initialPivot() {
            $('#initial-pivot').submit();
        }

        function uploadOrders() {
            $('#export-orders').submit();
        }

        function importProducts() {
            $('#import-products').submit();
        }

        function updateSettings() {
            $('#settings').submit();
        }

        function uploadInvoice() {
            $('#export-invoice').submit();
        }

        function fillPivotDocument() {
            var result = confirm("Skutočne chcete všetky objednávky/faktúry nastaviť ako odoslané?");
            if (result == true) {
                $('#fill-pivot-document').submit();
            }

        }
        function clearPivotDocument() {

            var result = confirm("Skutočne chcete všetky objednávky/faktúry nastaviť ako neodoslané?");
            if (result == true) {
                $('#clear-pivot-document').submit();
            }
        }




        //--></script>




</div>
<?php echo $footer; ?>
