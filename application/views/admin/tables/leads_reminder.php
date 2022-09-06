 <?php
 defined('BASEPATH') or exit('No direct script access allowed');

 $this->ci->load->model('gdpr_model');
 $lockAfterConvert      = get_option('lead_lock_after_convert_to_customer');
 $has_permission_delete = has_permission('leads', '', 'delete');
 $custom_fields         = get_table_custom_fields('leads');
 $consentLeads          = get_option('gdpr_enable_consent_for_leads');
 $statuses              = $this->ci->leads_model->get_status();
 
 $aColumns = [
     '1',
     db_prefix() . 'leads_reminder.id as id',
     db_prefix() . 'leads_reminder.comment as comment',
     db_prefix() . 'leads_reminder.date as date',
     db_prefix() . 'leads.name as name',
     db_prefix() . 'leads.phonenumber as phone',
     ];


 if (is_gdpr() && $consentLeads == '1') {
     $aColumns[] = '1';
 }

 $sIndexColumn = 'id';
 $sTable       = db_prefix() . 'leads_reminder';
 
 
 $join = [
     'LEFT JOIN ' . db_prefix() . 'leads ON ' . db_prefix() . 'leads.id = ' . db_prefix() . 'leads_reminder.customer_id'
 ];

 $where  = [];

 $additionalColumns = hooks()->apply_filters('leads_table_additional_columns_sql', []);

 $aColumns = hooks()->apply_filters('leads_table_sql_columns', $aColumns);

 // Fix for big queries. Some hosting have max_join_limit
 if (count($custom_fields) > 4) {
     @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
 }

 $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalColumns);

 $output  = $result['output'];
 $rResult = $result['rResult'];
 
 foreach ($rResult as $aRow) {
     $row = [];
 
     $row[] = $aRow['name'];
     
     $row[] = $aRow['phone'];

     $row[] = $aRow['comment'];

     $row[] = $aRow['date'];
 
     $row = hooks()->apply_filters('leads_table_row_data', $row, $aRow);

     $output['aaData'][] = $row;
 }
