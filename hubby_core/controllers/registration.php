<?php
Class registration
{
	private $core;
	private $user_global;
	private $hubby_admin;
	private $load;
	private $file;
	private $notice;
	private $pagination;
	public function __construct()
	{
		$this->core			=	Controller::instance();
		$this->core->load->library('hubby');
		$this->core->load->library('users_global');
		$this->core->load->library('captcha');
		$this->user_global	=&	$this->core->users_global;
		$this->load			=&	$this->core->load;
	}
	// Privates Methods
	private function construct_end()
	{
		$this->hubby_admin			=&	$this->core->hubby_admin;
		$this->data['hubby_admin']	=&	$this->core->hubby_admin;
		$this->load->library('hubby_admin');
		$this->loadOuputFile();
	}
	private function loadLibraries()
	{
		$this->load->library('pagination');
		$this->load->library('file');
		$this->load->library('form_validation');
		$this->input				=&	$this->core->input;
		$this->notice				=&	$this->core->notice;
		$this->file					=&	$this->core->file;
		$this->pagination			=&	$this->core->pagination;
		$this->form_validation		=&	$this->core->form_validation;
		$this->form_validation->set_error_delimiters('<span class="fg-color-redLight">', '</span>');
		$this->data['notice']		=	'';
		$this->data['error']		=	'';
		$this->data['success']		=	'';
	}
	private function loadOuputFile()
	{
		$this->core->file->css_push('app.v2');
		$this->core->file->css_push('css1');
		$this->core->file->css_push('css2');
		$this->core->file->css_push('font');
		$this->core->file->css_push('hubby_global');
	}
	// Public methods
	public function index()
	{
		$this->loadLibraries();				//	Affecting Libraries */
		$this->construct_end();				// 	Fin du constructeur
		
		$this->data['options']		=	$this->core->hubby->getOptions();
		if($this->data['options'][0]['ALLOW_REGISTRATION'] == '0')
		{
			$this->core->url->redirect(array('error','code','registrationNotAllowed'));
		}
		$this->core->form_validation->set_error_delimiters('<span style="color:red">','</span>');
		$this->core->form_validation->set_rules('user_pseudo','Pseudo','trim|required|min_length[5]|max_length[15]');
		$this->core->form_validation->set_rules('user_password','Mot de passe','trim|required|min_length[6]|max_length[15]');
		$this->core->form_validation->set_rules('user_password_confirm','Confirmer le mot de passe','trim|required|min_length[6]|max_length[15]');
		$this->core->form_validation->set_rules('user_mail','Email','trim|valid_email|required');
		$this->core->form_validation->set_rules('user_sex','Selection du sexe','trim|required|min_length[3]|max_length[4]');
		$this->core->form_validation->set_rules('captchaCorrespondance','Code captcha','trim|required|min_length[6]');
		$this->core->form_validation->set_rules('user_captcha',' ','matches[captchaCorrespondance]|trim|required|min_length[6]');
		if($this->core->form_validation->run())
		{
			$query	=	$this->core->users_global->createUser(
				$this->core->input->post('user_pseudo'),
				$this->core->input->post('user_password'),
				$this->core->input->post('user_sex'),
				$this->core->input->post('user_mail'),
				$active	=	'FALSE'
			);
			if($query	==	'adminCreated')
			{
				$this->core->url->redirect(array('login?notice=adminCreated'));
			}
			$this->core->notice->push_notice(notice($query));
		}
		$this->core->session->set_userdata('captcha_code',$this->core->captcha->get());
		$this->data['captcha']	=	$this->core->session->userdata('captcha_code');
		$this->data['pageTitle']	=	'Cr&eacute;er un compte - '.$this->data['options'][0]['SITE_NAME'];
		$this->core->hubby->setTitle($this->data['pageTitle']);
		
		$this->data['body']	=	$this->load->view('registration/createUser',$this->data,true);
		
		$this->load->view('header',$this->data);
		$this->load->view('global_body',$this->data);
	}
	public function superAdmin()
	{
		// Has Admin ?
		($this->user_global->hasAdmin()=== TRUE) ? $this->core->url->redirect(array('login')) : false;
		$this->loadLibraries();				//	Affecting Libraries */
		$this->construct_end();				// 	Fin du constructeur
		
		$this->core->form_validation->set_rules('super_admin_pseudo','Pseudo','trim|required|min_length[5]|max_length[15]');
		$this->core->form_validation->set_rules('super_admin_password','Mot de passe','trim|required|min_length[6]|max_length[15]');
		$this->core->form_validation->set_rules('super_admin_password_confirm','Confirmer le mot de passe','trim|required|min_length[6]|max_length[15]');
		$this->core->form_validation->set_rules('super_admin_mail','Email','trim|valid_email|required');
		$this->core->form_validation->set_rules('super_admin_sex','Selection du sexe','trim|required|min_length[3]|max_length[4]');
		if($this->core->form_validation->run())
		{
			if($this->core->users_global->createSuperAdmin(
				$this->core->input->post('super_admin_pseudo'),
				$this->core->input->post('super_admin_password'),
				$this->core->input->post('super_admin_sex'),
				$this->core->input->post('super_admin_mail')
			))
			{
				$this->core->url->redirect(array('login?notice=adminCreated&ref='.urlencode($this->core->url->site_url(array('admin','index')))));
			}
			$this->core->notice->push_notice(notice('SuperAdminCreationError'));
		}
		$this->data['pageTitle']	=	'Cr&eacute;er un super administrateur - Hubby';
		$this->core->hubby->setTitle($this->data['pageTitle']);
		
		$this->data['body']	=	$this->load->view('registration/createSuperAdmin',$this->data,true);
		
		$this->load->view('header',$this->data);
		$this->load->view('global_body',$this->data);
	}
}