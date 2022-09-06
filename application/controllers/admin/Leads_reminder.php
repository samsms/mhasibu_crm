<?php
require 'vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Leads_reminder extends AdminController
{
    public function __construct()
    {
         parent::__construct();
        $this->load->model('leads_model');
        $this->load->library('app_sms');
        $this->load->model('projects_model');
        $this->load->model('tasks_model');

        
    }

    /* List all leads */
    public function index($id = '')
    {
        close_setup_menu();

        if (!is_staff_member()) {
            access_denied('Leads');
        }

        $data['switch_kanban'] = true;

        if ($this->session->userdata('leads_kanban_view') == 'true') {
            $data['switch_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }
        $data['summary']  = get_leads_summary();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['title']    = _l('leads');
        // in case accesed the url leads/index/ directly with id - used in search
        $data['leadid'] = $id;
        $this->load->view('admin/leads_reminder/manage_reminders', $data);
    }

    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }
        $this->app->get_table_data('leads_reminder');
    }
    
    public function option_name($id, $option)
    {
        return 'sms_' . $id . '_' . $option;
    }

    /* Add or update lead */
    public function reminder($id = '')
    {
        if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            ajax_access_denied();
        }
        $data = [];
        

        if ($this->input->post()) {
            $message = $this->input->post('sms');
            $ids = $this->input->post('customer_id');

            if($ids == null){
                echo json_encode([
                   'success' => false,
                    'message' => 'Leads required',
                ]);
                die; 
            }else{
            
                $data_save = [];
                $gateway = $this->app_sms->get_active_gateway();
        

            if ($gateway !== false) {
            
             $className = 'sms_' . $gateway['id'];
             $CI = &get_instance();
             
             $username = $this->app->get_option($this->option_name('africastalking', 'username'));
             $apiKey = $this->app->get_option($this->option_name('africastalking', 'apiKey'));
             $senderId = $this->app->get_option($this->option_name('africastalking', 'senderId'));
        
             foreach($ids as $id){   
                    $data_save[] = array(
                        'comment' => strip_tags($message),
                        'customer_id' => $id,
                    );              
                   $cust_data = $this->leads_model->getLeadById($id);
                       
                   $phone = '+254'.ltrim($cust_data->phonenumber, "0");
                   $AT       = new AfricasTalking($username, $apiKey);
       
                   $sms      = $AT->sms();
                   
                   $result   = $sms->send([
                       'from'   =>  $senderId,
                       'to'      =>  $phone,
                       'message' => strip_tags($message)
                   ]);
                   
                  
                   
               }

               $this->leads_model->addLeadReminder($data_save);
               
               echo json_encode([
                    'success' => true,
                    'message' => 'Reminder sent successfully',
                ]);
                die;
            
            
            }else{
            echo json_encode([
                    'success' => false,
                    'message' => 'No Active SMS Gateway',
                ]);
            }
            die;

            }  
            
        }

        $data['title'] = 'Send Lead Reminder';
        $this->load->view('admin/leads_reminder/reminder', $data);
    }

   
   
 }  