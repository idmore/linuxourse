<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once('application/controllers/base.php');//load base class
class discussion extends base { //class for public

	public function __construct()
	{
		parent::__construct();
		//only for member
		$this->load->model('m_discussion');
		$this->load->library('user_agent');
	}
	public function index(){
		echo 'empty';
	}
	// index page
	public function all(){
		//pagination setup
		$this->load->library('pagination');		
		$config['total_rows'] = $this->db->count_all('discussion');
		$config['per_page'] = 15; 
		$config['uri_segment'] = 3;
		$config['num_link'] = 5;
		$config['use_page_number'] = TRUE;
		$uri = $this->uri->segment(3);		
		//end of pagination setup
		$data['title'] = 'Discussion';
		$data['script'] = '<script>$(document).ready(function(){$("#discusion,#orderAll").addClass("activemenu")});</script>';
		if(!empty($_GET['type'])){//filter discussion by type
			$config['base_url'] = site_url('discussion/all?type='.$this->input->get('type',TRUE));
			$config['page_query_string'] = TRUE;
			$this->pagination->initialize($config);
			if(!$uri) {
				$uri = 0;
			}
			if($config['total_rows'] < 15) {
				$data['page'] = 1;
			} else {
				$data['page'] = $this->pagination->create_links();
			}			
			$data['view'] = $this->m_discussion->show_discussion_by_type($config['per_page'],$uri,$_GET['type']);
		} else{ //show all discussion			
			$config['base_url'] = site_url('discussion/all?type='.$this->input->get('type',TRUE));
			$config['page_query_string'] = TRUE;
			$this->pagination->initialize($config);
			if(!$uri) {
				$uri = 0;
			}
			if($config['total_rows'] < 15) {
				$data['page'] = 1;
			} else {
				$data['page'] = $this->pagination->create_links();
			}
			$data['view'] = $this->m_discussion->show_discussion($config['per_page'],$uri);
		}		
		$this->baseView('discussion/discussion',$data);
	}
	//show discussion order by
	public function orderby(){
		// pagination setup
		$this->load->library('pagination');		
		$config['total_rows'] = $this->db->count_all('discussion');
		$config['per_page'] = 15; 
		$config['uri_segment'] = 4;
		$config['num_link'] = 5;
		$config['use_page_number'] = TRUE;
		$uri = $this->uri->segment(4);
		$config['base_url'] = site_url('discussion/orderby/'.$this->uri->segment(3));
		$this->pagination->initialize($config);
		if(!$uri) {
			$uri = 0;
		}
		if($config['total_rows'] < 15) {
			$data['page'] = 1;
		} else {
			$data['page'] = $this->pagination->create_links();
		}
		// end of pagination setup
		//by views or top comment
		switch ($this->uri->segment(3)) {
			case 'views': //order by views
			$data['title'] = 'Order By Views';
			$data['script'] = '<script>$(document).ready(function(){$("#discusion,#orderViews").addClass("activemenu")});</script>';
			$data['view'] = $this->m_discussion->showDiscussionByViews($config['per_page'],$uri);
			break;			
			default:
			echo 'menu error';
			break;
		}		
		$this->baseView('discussion/discussion',$data);
	}
	//open discussion
	public function open(){
		if(!empty($_POST)){
			//add comment
			if(empty($this->session->userdata['student_login'])){//if not login
				redirect(site_url('p/login'));
			}
			$enc_id_discuss = $this->uri->segment(3);
			$id_discuss = str_replace('', '=', $enc_id_discuss);
			$id_discuss = base64_decode(base64_decode($id_discuss));
			$comment = $_POST['input_comment'];
			$id_user = $this->session->userdata['student_login']['id_user'];
			$now = date('Y-m-d h:i:s');
			$data = array(
				'id_discussion'=>$id_discuss,
				'id_user'=>$id_user,
				'commentdate'=>$now,
				'updatedate'=>$now,
				'comment'=>$comment
				);
			//print_r($data);
			//is captcha is matching
			if($_POST['input_captcha'] == $this->session->userdata('mycaptcha')){
				$this->db->insert('discussion_comment',$data);
				redirect($this->agent->referrer());
			}else{
				echo 'captcha not matched';
			}
		}
		$this->load->helper('captcha');
		$id_discuss = $this->uri->segment(3);
		$id_discuss = base64_decode(base64_decode($id_discuss));
		$id_discuss = str_replace('', '=', $id_discuss);
		$tgl = date('d');
		$minutes = date('m');
		$second = date('s');
		$key = $tgl * $minutes * $second ;
		$vals = array(
			'word' => $key,
			'img_path'   => './assets/img/captcha',
			'img_url'    => base_url('assets/img/captcha'),
			'img_width'  => '200',
			'img_height' => 30,
			'border' => 0,
			'expiration' => 7200
			);
	  		// create captcha image
		$cap = create_captcha($vals);
			// store the captcha word in a session
		$this->session->set_userdata('mycaptcha', $cap['word']);
		$data = array(
			'script'=>'<script>$(document).ready(function(){$("#discusion").addClass("activemenu")});</script>',
			'image'=>$cap['image'],
			'comments'=>$this->m_discussion->showCommentByIdDiscusion($id_discuss,10,0),
			'view'=>$this->m_discussion->showDiscussionById($id_discuss));
		$data['title'] = $data['view']['title'];
		$this->baseView('discussion/open.php',$data);
	}
	//create new ask
	public function creatediscuss(){
		$this->load->helper('captcha');
		if(empty($this->session->userdata['student_login'])){//if not login
			redirect(site_url('p/login'));
		}
		if(!empty($_POST)){ //is form submission
			$iduser = $this->session->userdata['student_login']['id_user'];
			$title = $_POST['input_title'];
			$content = $_POST['input_content'];
			$type = $_POST['input_type'];
			$postdate = '';
		}else{ //not form submission			
			// captcha
			$tgl = date('d');
			$minutes = date('m');
			$second = date('s');
			$key = $tgl * $minutes * $second ;
			$vals = array(
				'word' => $key,
				'img_path'   => './assets/img/captcha',
				'img_url'    => base_url('assets/img/captcha'),
				'img_width'  => '200',
				'img_height' => 30,
				'border' => 0,
				'expiration' => 7200
				);
	  		// create captcha image
			$cap = create_captcha($vals);
			// store the captcha word in a session
			$this->session->set_userdata('mycaptcha', $cap['word']);
			// end of captcha config
			$data = array(
				'title'=>'Create New Ask',
				'image'=>$cap['image'],
				'script'=>'<script>$(document).ready(function(){$("#discusion").addClass("activemenu")});</script>',
				);
			$this->baseView('discussion/newdiscuss',$data);
		}		
	}

	/*
	* ALl ABOUT COMMENT
	*/


	/*
	* CREATE OR UPDATE
	*/

	//create new topic
	public function addtopic(){
		$title = $_POST['input_title'];
		$content = $_POST['input_content'];
		$type = $_POST['input_type'];
		$captcha = $_POST['input_captcha'];
		$this->load->library('form_validation');
		$this->form_validation->set_rules('input_title', 'title', 'required|min_length[5]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('input_content', 'content', 'required|min_length[5]|max_length[500]|xss_clean');
		if($this->form_validation->run() && $captcha == $this->session->userdata('mycaptcha')){//is data valid
			$data = array(
				'title'=>$title,
				'content'=>strip_tags($content),
				'postdate'=>date('Y-m-d h:i:s'),
				'updatedate'=>date('Y-m-d h:i:s'),
				'id_user'=>$this->session->userdata['student_login']['id_user'],
				'type'=>$type,
				'views'=>0
				);
			if($this->db->insert('discussion',$data)){
				//get lattest id_discussion by id_user
				$this->db->where('id_user',$this->session->userdata['student_login']['id_user']);
				$this->db->order_by('updatedate','desc');
				$query = $this->db->get('discussion');
				$query = $query->row_array();
				$id_discussion = base64_encode(base64_encode($query['id_discuss']));
				$id_discussion = str_replace('=', '', $id_discussion);
				redirect(site_url('discussion/open/'.$id_discussion));//redirect to open mode
			}else{
				echo 'error add topic';
			}
		}else{//not valid
			// captcha
			$this->load->helper('captcha');
			$tgl = date('d');
			$minutes = date('m');
			$second = date('s');
			$key = $tgl * $minutes * $second ;
			$vals = array(
				'word' => $key,
				'img_path'   => './assets/img/captcha',
				'img_url'    => base_url('assets/img/captcha'),
				'img_width'  => '200',
				'img_height' => 30,
				'border' => 0,
				'expiration' => 7200
				);
	  		// create captcha image
			$cap = create_captcha($vals);
			// store the captcha word in a session
			$this->session->set_userdata('mycaptcha', $cap['word']);
			// end of captcha config
			$data = array(
				'title'=>'Create New Ask',
				'image'=>$cap['image'],
				'type'=>$type,
				'captcha'=>$captcha,
				'isedit'=>false,
				'script'=>'<script>$(document).ready(function(){$("#discusion").addClass("activemenu")});</script>',
				);
			$this->baseView('discussion/editdiscuss',$data);
		}
	}
	//edit my discuss
	public function edit(){
		$this->load->library('form_validation');
		if(!empty($_POST)){//submit data

		}
		//captcha
		$this->load->helper('captcha');
		$tgl = date('d');
		$minutes = date('m');
		$second = date('s');
		$key = $tgl * $minutes * $second ;
		$vals = array(
			'word' => $key,
			'img_path'   => './assets/img/captcha',
			'img_url'    => base_url('assets/img/captcha'),
			'img_width'  => '200',
			'img_height' => 30,
			'border' => 0,
			'expiration' => 7200
			);
	  		// create captcha image
		$cap = create_captcha($vals);
			// store the captcha word in a session
		$this->session->set_userdata('mycaptcha', $cap['word']);
		//end of captcha
		if(empty($this->session->userdata['student_login'])){//if not login
			redirect(site_url('p/login'));
		}
		$referrer =  $this->agent->referrer();
		preg_match('#/discussion/open/(.*)#',$referrer,$getid);
		$enc_id_discuss = $getid[1];
		$id_discuss = str_replace('', '=', $enc_id_discuss);
		$id_discuss = base64_decode(base64_decode($id_discuss));
		//is this topic create by now login user
		if($this->m_discussion->checkOwner($id_discuss,$this->session->userdata['student_login']['id_user'])){
			//get discussion by id
			$data = array(
				'title'=>'Edit Topic',
				'image'=>$cap['image'],
				'view'=>$this->m_discussion->showDiscussionById($id_discuss),
				'isedit'=>true,
				'enc_id_discuss'=>$enc_id_discuss,
				);
			$data['type']=$data['view']['type'];
			$this->baseView('discussion/editdiscuss',$data);
		}else{
			echo 'you re no topic owner';
		}
	}

	/*
	* Action after login
	*/

	//show all topics added by me
	public function mytopics(){
		//if not login redirect to discussion
		if(empty($this->session->userdata['student_login']['id_user'])){
			redirect(site_url('discussion/all'));
		}
		//pagination setup
		$this->load->library('pagination');
		$config = array(
			'total_rows'=> $this->m_discussion->countMyTopics(),
			'per_page'=>15,
			'uri_segment'=>3,
			'num_link'=>5,
			'use_page_number'=>TRUE,
			);
		$uri = $this->uri->segment(3);
		$config['base_url'] = site_url('discussion/mytopics');
		$this->pagination->initialize($config);
		if(!$uri) {
			$uri = 0;
		}
		if($config['total_rows'] < 15) {
			$data['page'] = 1;
		} else {
			$data['page'] = $this->pagination->create_links();
		}
		//end of pagination setup
		$data['title']='My Topics';
		$data['view']=$this->m_discussion->myTopics($config['per_page'],$uri);
		$data['script']='<script>$(document).ready(function(){$("#discusion,#orderTopics").addClass("activemenu")});</script>';
		$this->baseView('discussion/mytopics',$data);
	}
	//show all answer added by me
	public function myanswers(){
		//if not login redirect to discussion
		if(empty($this->session->userdata['student_login']['id_user'])){
			redirect(site_url('discussion/all'));
		}
		//pagination setup
		$this->load->library('pagination');
		$config = array(
			'total_rows'=> $this->m_discussion->countMyAnswer(),
			'per_page'=>15,
			'uri_segment'=>3,
			'num_link'=>5,
			'use_page_number'=>TRUE,
			);
		$uri = $this->uri->segment(3);
		$config['base_url'] = site_url('discussion/myanswer');
		$this->pagination->initialize($config);
		if(!$uri) {
			$uri = 0;
		}
		if($config['total_rows'] < 15) {
			$data['page'] = 1;
		} else {
			$data['page'] = $this->pagination->create_links();
		}
		//end of pagination setup
		$data['title']='My Topics';
		$data['view']=$this->m_discussion->myAnswers($config['per_page'],$uri);
		$data['script']='<script>$(document).ready(function(){$("#discusion,#orderAnswers").addClass("activemenu")});</script>';
		$this->baseView('discussion/myanswers',$data);
	}
}