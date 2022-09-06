<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open_multipart(admin_url('leads_reminder/reminder'),array('id'=>'lead_reminder-form')); ?>
<div class="modal fade" id="_reminder_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <h4 class="modal-title" id="myModalLabel">
            <?php echo $title; ?>
         </h4>
      </div>
      <div class="modal-body">
         <div class="row">
            <div class="col-md-12">
            <?php
               $all_leads= get_all_leads();
               echo render_select( 'customer_id[]',$all_leads,array( 'id',array( 'name')), 'Lead(s)',null,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'), 'multiple'=>'multiple'));
               ?>

            <?php $value = (isset($lead) ? $lead->description : ''); ?>
            <?php echo render_textarea('sms','Comments',$value, array('placeholder'=>_l('Add Comments'))); ?>
            </div>
         </div>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
      </div>
   </div>
</div>
<?php echo form_close(); ?>
<script>
   $(function(){

    appValidateForm($('#lead_reminder-form'), {
      customer_id: 'required',
      sms: 'required',
    },lead_reminder_form_handler);

    init_selectpicker();

    });
</script>
