<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a onclick="location = '<?php echo $cancel; ?>';" class="btn btn-default"
                   data-toggle="tooltip"><?php echo $button_cancel; ?></a>
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
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit;?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ezysplits"
                      class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-merchant-id"><span data-toggle="tooltip"
                                                                                            title="<?php echo $help_merchant_id; ?>"><?php echo $entry_merchant_app_id; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="ezysplits_merchant_app_id"
                                   value="<?php echo $ezysplits_merchant_app_id; ?>"
                                   placeholder="<?php echo $entry_merchant_app_id; ?>" id="input-merchant-id"
                                   class="form-control"/>
                            <?php if($error_merchant_id) { ?>
                            <div class="text-danger"><?php echo $error_merchant_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-secret-key"><?php echo $entry_merchant_secret_key; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="ezysplits_merchant_secret_key" value="<?php echo $ezysplits_merchant_secret_key; ?>"
                                   placeholder="<?php echo $entry_merchant_secret_key; ?>" id="input-secret-key"
                                   class="form-control"/>
                            <?php if($error_secret_key) { ?>
                            <div class="text-danger"><?php echo $error_secret_key; ?> </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-order-status"><span data-toggle="tooltip"
                                                                                             title="<?php echo $help_order_status; ?>"><?php echo $entry_order_status; ?></span></label>
                        <div class="col-sm-10">
                            <select name="ezysplits_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $ezysplits_order_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"
                                        selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="ezysplits_status" id="input-status" class="form-control">
                                <?php if ($ezysplits_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="ezysplits_sort_order" value="<?php echo $ezysplits_sort_order; ?> "
                                   placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order"
                                   class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-pay-mode"><?php echo $entry_payment_mode; ?></label>
                        <div class="col-sm-10">
                            <select name="ezysplits_payment_mode" id="input-pay-mode" class="form-control">
                                <?php if($ezysplits_payment_mode == 'sandbox') { ?>
                                <option value="sandbox" selected="selected"><?php echo $text_sandbox; ?></option>
                                <option value="live"><?php echo $text_live; ?></option>
                                <?php } else { ?>
                                <option value="sandbox"><?php echo $text_sandbox; ?></option>
                                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>