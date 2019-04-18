<?php
class Mixtapes extends MX_Controller 
{

    function __construct() {
        parent::__construct();
    }

    function do_upload($mixtape_id){
        if(!is_numeric($mixtape_id)){
            redirect('site_security/not_allowed');
        }
        die();
        $this->load->library('session');
        $this->load->module('site_security');
        $this->site_security->_make_sure_is_admin();
        $submit=$this->input->post('submit',TRUE);
        if($submit=="Cancel"){
            //cancel the upload 
            redirect('mixtapes/create/'.$mixtape_id);
        }
        $config['upload_path']   = './big_pics/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']      = 0;
        $config['max_width']     = 0;
        $config['max_height']    =   0;
        $this->load->library('upload',$config);
        if(!$this->upload->do_upload('userfile')){
            //upload wasnt successful
            $data['error'] = array('error'=>$this->upload->display_errors("<p style='color:red;'>","</p>"));
            //$this->load->view('upload_form',$error);
            $data['headline']="Upload Error";
            $data['mixtape_id']=$mixtape_id;
            $data['flash']=$this->session->flashdata('item');
            $data['view_file']="upload_image";
            $this->load->module('templates');
            $this->templates->admin($data);
        }else{
            //upload was successful
            $data=array('upload_data'=>$this->upload->data());
            $upload_data=$data['upload_data'];
            $file_name=$upload_data['file_name'];
            $this->_generate_thumbnail($file_name);

            $update_data['small_pic']=$file_name;
            $update_data['big_pic']=$file_name;
            $this->_update($mixtape_id,$update_data);
        
            //$mixtape_id=$this->get_max();//get the ID of the new item
            $flash_msg="The mixtape was successfully added";
            $value="<div class='alert alert-success' role='alert'>".$flash_msg."</div>";
            $this->session->set_flashdata('item',$value);
            redirect('mixtapes/create/'.$mixtape_id);
            /* 

            $data['headline']="Upload Success";
            $data['mixtape_id']=$mixtape_id;
            $data['flash']=$this->session->flashdata('item');
            $data['view_file']="upload_success";
            $this->load->module('templates');
            $this->templates->admin($data);*/
        }

    }

    function upload_image($mixtape_id){
        if(!is_numeric($mixtape_id)){
            redirect('site_security/not_allowed');
        }
        $this->load->library('session');
        $this->load->module('site_security');
        $this->site_security->_make_sure_is_admin();
        $mixtape_id = $this->uri->segment(3);
        $data['headline']="Upload Mixtape Image";
        $data['mixtape_id']=$mixtape_id;
        $data['flash']=$this->session->flashdata('item');
        $data['view_file'] = 'create';
        $this->load->module('templates');
        $this->templates->admin($data);
    }

    function create(){
        $this->load->module('site_security');
        $this->site_security->_make_sure_is_admin();
        $mixtape_id = $this->uri->segment(3);
        $submit = $this->input->post('submit',TRUE);
        if($submit == "Cancel"){
            redirect('mixtapes/manage');
        }elseif($submit == "Submit") {
            //process the form
            $this->load->library('session');
            $this->load->library('form_validation');
            //$this->form_validation->CI =& $this;
            $this->form_validation->set_rules('mixtape_title','Mixtape Title','required');
            $this->form_validation->set_rules('mixtape_link','Mixtape Link','required');
            $this->form_validation->set_rules('mixtape_description','Mixtape Description','required');
            $this->form_validation->set_rules('mixtape_type','Mixtape Type','required');

            if($this->form_validation->run() == TRUE){
                $data=$this->fetch_data_from_post();
                $data['mixtape_url'] = url_title($data['mixtape_title']);
                if(is_numeric($mixtape_id)){
                    //update the mixtape details
                    $this->_update($mixtape_id, $data);
                    $flash_msg="The mixtape details were successfully updated";
                    $value="<div class='alert alert-success' role='alert'>".$flash_msg."</div>";
                    $this->session->set_flashdata('item',$value);
                    redirect('mixtapes/create/'.$mixtape_id);

                }else{
                    //mixtape details OK, move to the mixtape file
                    //prepare the new mixtape file
                    $config['upload_path']   = './mixtapes/';
                    $config['allowed_types'] = 'mp3|mp4|avi|m4v|mkv|flv|vob|m4a|webm';
                    $config['max_size']      = 0;
                    $config['max_width']     = 0;
                    $config['max_height']    = 0;
                    $this->load->library('upload',$config);
                    if(!$this->upload->do_upload('mixtape_file')){
                        //upload wasnt successful
                        $data['error'] = array('error'=>$this->upload->display_errors("<p style='color:red;'>","</p>"));
                        //$this->load->view('upload_form',$error);
                        $data2 = $data['error'];
                        $data['headline']="Upload Error";
                        $data['mixtape_id']=$mixtape_id;
                        $flash_msg="The mixtape file type is not allowed";                        
                        $value="<div class='alert alert-success' role='alert'>".$flash_msg."</div>";
                        $data['flash']=$this->session->flashdata('item');
                        
                    }else{
                        //upload successful
                        $file_data=array('upload_data'=>$this->upload->data());
                        $upload_data=$file_data['upload_data'];
                        $file_name=$upload_data['file_name'];
                        $data['mixtape_file'] = $file_name;

                        //insert new mixtapes details to db
                        $this->_insert($data);
                        $mixtape_id=$this->get_max();//get the ID of the new item
                        $flash_msg="The mixtape was successfully added";
                        $value="<div class='alert alert-success' role='alert'>".$flash_msg."</div>";
                        $this->session->set_flashdata('item',$value);
                        redirect('mixtapes/create/'.$mixtape_id);
                    }
                }
            }
        }
        if((is_numeric($mixtape_id)) && ($submit!="Submit")){
            $data=$this->fetch_data_from_db($mixtape_id);
        }else{
            $data=$this->fetch_data_from_post();
            $data['big_pic']='';
        }
        if(!is_numeric($mixtape_id)){
            $data['headline']= "Add New mixtape";
        }else{
            $data['headline']="Update mixtape Details";
        }
        $data['error'] = isset($data2) ? $data2:'';
        $data['mixtape_id'] = $mixtape_id;
        $data['flash'] = $this->session->flashdata('item');
        $data['view_module'] = "mixtapes";
        $data['view_file'] = "create";
        $this->load->module('templates');
        $this->templates->admin($data);
    }

    function manage(){
        $this->load->module('site_security');
        $this->site_security->_make_sure_is_admin();
        
        $data['view_module'] = "mixtapes";
        $data['view_file'] = "manage";
        $this->load->module('templates');
        $this->templates->admin($data);
    }

    function fetch_data_from_post(){
        $data['mixtape_title']=$this->input->post('mixtape_title',TRUE);
        $data['mixtape_link']=$this->input->post('mixtape_link',TRUE);
        $data['mixtape_type']=$this->input->post('mixtape_type',TRUE);
        $data['mixtape_description']=$this->input->post('mixtape_description',TRUE);
        return $data;
    }
    function fetch_data_from_db($mixtape_id){
        if(!is_numeric($mixtape_id)){
            redirect('site_security/not_allowed');
        }
        $query=$this->get_where($mixtape_id);
        foreach($query->result() as $row){
            //`id`, `mixtape_title`, `mixtape_url`, `mixtape_price`, `mixtape_description`, `big_pic`, `small_pic`, `was_price`
            $data['mixtape_title']=$row->mixtape_title;
            $data['mixtape_url']=$row->mixtape_url;
            $data['mixtape_link']=$row->mixtape_link;
            $data['mixtape_description']=$row->mixtape_description;
            $data['big_pic']=$row->big_pic;
            $data['small_pic']=$row->small_pic;
            $data['mixtape_file']=$row->mixtape_file;
            $data['mixtape_type']=$row->mixtape_type;
        }
        if(!isset($data)){
            $data="";
        }
        return $data;
    }

    function get($order_by){
        $this->load->model('mdl_mixtapes');
        $query = $this->mdl_mixtapes->get($order_by);
        return $query;
    }

    function get_with_limit($limit, $offset, $order_by) {
        if ((!is_numeric($limit)) || (!is_numeric($offset))) {
            die('Non-numeric variable!');
        }

        $this->load->model('mdl_mixtapes');
        $query = $this->mdl_mixtapes->get_with_limit($limit, $offset, $order_by);
        return $query;
    }

    function get_where($id){
        if (!is_numeric($id)) {
            die('Non-numeric variable!');
        }

        $this->load->model('mdl_mixtapes');
        $query = $this->mdl_mixtapes->get_where($id);
        return $query;
    }

    function get_where_custom($col, $value) {
        $this->load->model('mdl_mixtapes');
        $query = $this->mdl_mixtapes->get_where_custom($col, $value);
        return $query;
    }

    function _insert($data){
        $this->load->model('mdl_mixtapes');
        $this->mdl_mixtapes->_insert($data);
    }

    function _update($id, $data){
        if (!is_numeric($id)) {
            die('Non-numeric variable!');
        }

        $this->load->model('mdl_mixtapes');
        $this->mdl_mixtapes->_update($id, $data);
    }

    function _delete($id){
        if (!is_numeric($id)) {
            die('Non-numeric variable!');
        }

        $this->load->model('mdl_mixtapes');
        $this->mdl_mixtapes->_delete($id);
    }

    function count_where($column, $value) {
        $this->load->model('mdl_mixtapes');
        $count = $this->mdl_mixtapes->count_where($column, $value);
        return $count;
    }

    function get_max() {
        $this->load->model('mdl_mixtapes');
        $max_id = $this->mdl_mixtapes->get_max();
        return $max_id;
    }

    function _custom_query($mysql_query) {
        $this->load->model('mdl_mixtapes');
        $query = $this->mdl_mixtapes->_custom_query($mysql_query);
        return $query;
    }

}