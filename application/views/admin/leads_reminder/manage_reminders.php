<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="_buttons">
                     <a href="#" onclick="new_leads_reminder(); return false;" class="btn mright5 btn-info pull-left display-block">
                     <?php echo _l('new reminder'); ?>
                     </a>
                     <div class="row">
                        <div class="col-md-4 col-xs-12 pull-right leads-search">
                           <?php echo form_hidden('sort_type'); ?>
                           <?php echo form_hidden('sort',(get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                     </div>
                  <hr class="hr-panel-heading" />
                  <div class="tab-content">
                  
                        <div class="col-md-12">
                           <a href="#" data-toggle="modal" data-table=".table-leads" data-target="#leads_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
                           <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                 <div class="modal-content">
                                    <div class="modal-header">
                                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                       <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                       <?php if(has_permission('leads','','delete')){ ?>
                                       <div class="checkbox checkbox-danger">
                                          <input type="checkbox" name="mass_delete" id="mass_delete">
                                          <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                       </div>
                                       <hr class="mass_delete_separator" />
                                       <?php } ?>
                                       <div id="bulk_change">
                                       <div class="form-group">
                                              <div class="checkbox checkbox-primary checkbox-inline">
                                                <input type="checkbox" name="leads_bulk_mark_lost" id="leads_bulk_mark_lost" value="1">
                                                <label for="leads_bulk_mark_lost">
                                                <?php echo _l('lead_mark_as_lost'); ?>
                                                </label>
                                             </div>
                                         </div>
                                          <?php echo render_select('move_to_status_leads_bulk',$statuses,array('id','name'),'ticket_single_change_status'); ?>
                                          <?php
                                             echo render_select('move_to_source_leads_bulk',$sources,array('id','name'),'lead_source');
                                             echo render_datetime_input('leads_bulk_last_contact','leads_dt_last_contact');
                                             if(has_permission('leads','','edit')){
                                               echo render_select('assign_to_leads_bulk',$staff,array('staffid',array('firstname','lastname')),'leads_dt_assigned');
                                             }
                                             ?>
                                          <div class="form-group">
                                             <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                             <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
                                          </div>
                                          <hr />
                                          <div class="form-group no-mbot">
                                             <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="leads_bulk_visibility" id="leads_bulk_public" value="public">
                                                <label for="leads_bulk_public">
                                                <?php echo _l('lead_public'); ?>
                                                </label>
                                             </div>
                                             <div class="radio radio-primary radio-inline">
                                                <input type="radio" name="leads_bulk_visibility" id="leads_bulk_private" value="private">
                                                <label for="leads_bulk_private">
                                                <?php echo _l('private'); ?>
                                                </label>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="modal-footer">
                                       <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                       <a href="#" class="btn btn-info" onclick="leads_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                    </div>
                                 </div>
                                 <!-- /.modal-content -->
                              </div>
                              <!-- /.modal-dialog -->
                           </div>
                           <!-- /.modal -->
                           <?php

                              $table_data = array();
                              $_table_data = array(
                                 array(
                                    'name'=>_l('leads_dt_name'),
                                    'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-leed')
                                  ),

                                array(
                                 'name'=>_l('Sms'),
                                 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sms')
                               ),

                               array(
                                'name'=>_l('Date'),
                                'th_attrs'=>array('class'=>'date-created toggleable','id'=>'th-date')
                              )
                              
                              );
                              

                              foreach($_table_data as $_t){
                               array_push($table_data,$_t);
                              }
                              $custom_fields = get_custom_fields('leads',array('show_on_table'=>1));
                              
                              foreach($custom_fields as $field){
                              array_push($table_data,$field['name']);
                              }
                              
                              $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
                              
                              render_datatable($table_data,'leads_reminder',
                              array('customizable-table'),
                              array(
                                 'id'=>'table-lead_reminders',
                                 'data-last-order-identifier'=>'leads_reminders',
                                 'data-default-order'=>get_table_last_order('leads_reminders'),
                                 )); ?>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script id="hidden-columns-table-leads" type="text/json">
   <?php echo get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<?php include_once(APPPATH.'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
<script>
   var openLeadID = '<?php echo $leadid; ?>';
   $(function(){
      leads_kanban();
      $('#leads_bulk_mark_lost').on('change', function(){
          $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
          $('#move_to_status_leads_bulk').selectpicker('refresh')
       });
      $('#move_to_status_leads_bulk').on('change', function(){
        if($(this).selectpicker('val') != '') {
         $('#leads_bulk_mark_lost').prop('disabled', true);
         $('#leads_bulk_mark_lost').prop('checked', false);
      } else {
         $('#leads_bulk_mark_lost').prop('disabled', false);
      }
   });
   });
</script>
</body>
</html>
