<?php

namespace App\Controller\Agent;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Form\Form;
use Cake\Network\Http\Client;
use Cake\Routing\Router;


class AgentsController extends \App\Controller\AgentsController {

	public function beforeFilter( Event $event ) {
		parent::beforeFilter($event);
		$this->Auth->allow(array('contact', 'index','login','logout', 'forgot', 'login'));
		$this->Auth->allowedActions = (array('contact', 'index','login','logout', 'forgot', 'register','sso_register'));
	}

	public function profile() {
		$this->set('title_for_layout', __('My Profile'));
		$id = $this->Auth->user('id');
		if (!$id && empty($this->request->data)) {
			$this->Flash->set(__('Invalid agent'));
			$this->redirect(array('action' => 'index'));
		}
		$this->request->session()->write('UserId', $id);

		if (!empty($this->request->data)) {
			$phonenumber = $this->request->data['digits1'] . "-" . $this->request->data['digits2'] . "-" . $this->request->data['digits3'];
			$this->request->data['phonenumber'] = $phonenumber;
			$agentProfile = $this->Agents->newEntity($this->request->data);
			$agentProfile->id = $id;
			if ($this->Agents->save($agentProfile)) {
				$this->Flash->set(__('Profile data has been saved'));
				$this->redirect($this->referer());
			} else {
				$this->Flash->set(__('Profile data could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Agents->findById($id)->first()->toArray();
			$ph = explode('-', $this->request->data['phonenumber']);
			$this->request->data['digits1'] = $ph[0];
			$this->request->data['digits2'] = $ph[1];
			$this->request->data['digits3'] = $ph[2];
		}
		$provinces = array('AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT','------', 'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY');
		$provinces = array_combine($provinces,$provinces);
		$this->set(compact('provinces'));
	}

  public function register() {

    $this->set('title_for_layout', __('Agent Registration'));
	
    if (!empty($this->request->data)) {

      $phonenumber = $this->request->data['digits1'] . "-" . $this->request->data['digits2'] . "-" . $this->request->data['digits3'];

      $this->request->data['phonenumber'] = $phonenumber;

      $agent = $this->Agents->newEntity($this->request->data);

      if ($this->Agents->save($agent)) {
        $this->Flash->set(__('Your registration is pending approval.'));

        $from = Configure::read('hippo.warehouse_email');
        $subject = "Registration confirmation";
        $message = Configure::read('hippo.msg_agent_reg');

        if (!empty($this->request->data['email'])) {
          $this->_sendEmail($this->request->data['email'], $from, $subject, $message);
        }


        $from = Configure::read('hippo.system_email');
        $subject = "New agent registration";
        $agentData = "";
        $messageBegin = "<html>
        <head>
        </head>
        <body>
        <h1>New agent registered</h1>
        ";
        $messageEnd = "
        </body>
        </html>";
        foreach ($this->request->data['Agent'] as $key => $value) {
          $agentData .="<p>" . $key . ": " . $value . "</p>";
        }
        $message = $messageBegin . $agentData . $messageEnd;

        $this->_sendEmail(Configure::read('hippo.warehouse_email'), $from, $subject, $message);


        $this->redirect($this->Auth->loginRedirect);
      } else {
        $this->Flash->set(__('Registration failed. Please, try again.'));
      }
    }
    $provinces = array('AB', 'BC', 'MB', 'NB', 'NL', 'NT', 'NS', 'NU', 'ON', 'PE', 'QC', 'SK', 'YT','------', 'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY');
    $provinces = array_combine($provinces,$provinces);
    $this->set(compact('provinces'));
  }

  public function sso_profile() {

    $this->set('title_for_layout', __('My Profile'));
    $http = new Client();

  	$_reg_error = '';
	$_reg_saved = false;
	
	$broker_url =  Router::url('/', true);
	$broker_url =  str_replace("http://","",$broker_url);
	$broker_url =  str_replace("https://","",$broker_url);
	$broker_url =  str_replace("/","",$broker_url);
	
	// update profile form
	if( isset( $_REQUEST['sso_session_check'] ) && $_REQUEST['sso_session_check'] ){

		$http = new Client();
		
		$params = [
			'action'=>'sso_form_session',
			'broker_url' => $broker_url,
			'session' => $_REQUEST['sso_session_check']
		] ;
		
		$response = $http->post( 'http://' . SSO_PARENT . '/wp-admin/admin-ajax.php', $params );

		$items = get_object_vars( json_decode( $response->body() ) );
		
		if( isset( $items['_error'] ) && !is_array($items['_error']) && $items['_error'] ){
			$_reg_error = $items['_error'];
		}elseif( isset( $items['_error'][0] ) && $items['_error'][0] ){
			$_reg_error = $items['_error'][0];
		}

		if( isset( $items['_post'] ) ){

			foreach( $items['_post'] as $key => $value ){
				if( !is_array( $value ) ){
					$value = str_replace("\\\'","'",$value);
					$value = str_replace("\\","",$value);
					$this->request->data[ $key ] = $value;
				}
			}

		}

		if( isset( $items['_saved'] ) ){ $_reg_saved = true; }

	}
	
	$user = $this->Agents->findById( $this->Auth->user('id') )->first();

	$user_email = $user['email'];

    $params = [
            'action'=>'user_profile',
            'broker_url' => $broker_url,
            'broker_key' => Configure::read('hippo.sso_broker_key'),
            'email' => $user_email
        ] ;
    $response = $http->post('http://' . SSO_PARENT . '/wp-admin/admin-ajax.php', $params );
    $user_info = get_object_vars(json_decode($response->body()));
	
	$provinceAbbreviations = [
		'alberta'=>'AB',
		'british columbia' => 'BC',
		'manitoba' => 'MB',
		'new brunswick' => 'NB',
		'newfoundland' => 'NL',
		'north west territories' => 'NT',
		'nova scotia' => 'NS',
		'nunavut' => 'NU',
		'ontario' => 'ON',
		'prince edward island' => 'PE',
		'quebec' => 'QC',
		'saskatchewan' => 'SK',
		'yukon territory' => 'YT',
		'alabama' => 'AL',
		'alaska' => 'AK',
		'arizona'=>'AZ',
		'arkansas'=>'AR',
		'california'=>'CA',
		'colorado'=>'CO',
		'connecticut'=>'CT',
		'delaware'=>'DE',
		'district of columbia'=>'DC',
		'florida'=>'FL',
		'georgia'=>'GA',
		'hawaii'=>'HI',
		'idaho'=>'ID',
		'illinois'=>'IL',
		'indiana'=>'IN',
		'iowa'=>'IA',
		'kansas'=>'KS',
		'kentucky'=>'KY',
		'louisiana'=>'LA',
		'maine'=>'ME',
		'maryland'=>'MD',
		'massachusetts'=>'MA',
		'michigan'=>'MI',
		'minnesota'=>'MN',
		'mississippi'=>'MS',
		'missouri'=>'MO',
		'montana'=>'MT',
		'nebraska'=>'NE',
		'nevada'=>'NV',
		'new hampshire'=>'NH',
		'new jersey'=>'NJ',
		'new mexico'=>'NM',
		'new york'=>'NY',
		'north carolina'=>'NC',
		'north dakota'=>'ND',
		'ohio'=>'OH',
		'oklahoma'=>'OK',
		'oregon'=>'OR',
		'pennsylvania'=>'PA',
		'rhode island'=>'RI','south carolina'=>'SC',
		'south dakota'=>'SD',
		'tennessee'=>'TN',
		'texas'=>'TX',
		'utah'=>'UT',
		'vermont'=>'VT',
		'virginia'=>'VA',
		'washington'=>'WA',
		'west virginia'=>'WV',
		'wisconsin'=>'WI',
		'wyoming'=>'WY'
	];
	
	foreach( $user_info as $key => $value ){
		$value = str_replace("\\\'","'",$value);
		$value = str_replace("\\","",$value);
		$user_info[$key] = $value;
	}

	
	if( $_reg_saved ){
		
		if (!isset($user_info['city'])) { $user_info['city'] = ''; }
		if (!isset($user_info['company'])) { $user_info['company'] = ''; }
		if (!isset($user_info['postal_code'])) { $user_info['postal_code'] = ''; }
		if (!isset($user_info['phone_no'])) { $user_info['phone_no'] = ''; }
		if (!isset($user_info['province'])) {
			$user_info['province'] = '';
			$user_prov = '';
		} elseif (strlen($user_info['province']) > 4) {
			$user_prov = $provinceAbbreviations[strtolower($user_info['province'])];
		} else {
			$user_prov = $user_info['province'];
		}

		$user->username = $user_info['username'];
		$user->email = $user_info['email'];
		$user->firstname = $user_info['first_name'];
		$user->lastname = $user_info['last_name'];
		
		if ($ssoSession) { $user->password = $ssoSession->password; }
		$user->company = $user_info['company'];
		$user->phonenumber = $user_info['phone_no'];
		$user->province = $user_prov;
		$user->postalcode = $user_info['postal_code'];
		$user->city = $user_info['city'];
		$user->address = $user_info['street'] . ' ' . $user_info['streetName'];
		$user->address2 = $user_info['unitApt'] . ' ' . $user_info['unitType'];
		$user=$this->Agents->save($user)->toArray();
		$sessionUser = $this->Auth->user();
		foreach(array_keys($user) as $key) {
			$sessionUser[$key] = $user[$key];
		}
		$this->Auth->setUser($sessionUser);
				
	}

	$attr = [
		'lc_role_agency' => '',
		'contact_sml' => '',
		'contact_lrg' => true,
		'company_job' => true,
		'pro_information' => true,
		'tw_verfiy_request' => '',
		'tw_magazine' => '',
		'register_fallback' => '',
		'_error' => $_reg_error,
		'_saved' => $_reg_saved
	];

    $broker_key = Configure::read('hippo.sso_broker_key');

	$param = [
		'action'=> 'user_profile',
		'email' => $user_email,
		'broker_key' => $broker_key,
		'broker_url' => $broker_url
	];
	
	$response = $http->post( 'http://' . SSO_PARENT . '/wp-admin/admin-ajax.php', $param );

	$form = $this->sso_form_profile( json_decode($response->body()), $attr );

	$this->set('form', $form);
	
}
  
  
  
  public function sso_register() {

    $this->set('title_for_layout', __('Agent Registration'));
    $http = new Client();
    $broker_url = trim(str_replace(array('http://','https://'),'',Router::url('/', true)),'/');

    /*
    lc_role_agency < LC only shows role and agency
    contact_sml < shows province, phone, website
    contact_lrg < shows all contact info
    company_job < shows company and job_function
    pro_information < shows all profession info
    tw_verfiy_request < shows ca_verify
    tw_magazine << subscribe info
    register_fallback < only when used with profile. Falls back to reg form if logged out.
    */

    $attr = [
      'lc_role_agency' => '',
      'contact_sml' => '',
      'contact_lrg' => true,
      'company_job' => true,
      'pro_information' => true,
      'tw_verfiy_request' => '',
      'tw_magazine' => '',
      'register_fallback' => ''
    ];

    $broker_key = Configure::read('hippo.sso_broker_key');
    
    $form = $this->sso_form_register( $attr );
    
    $this->set('form', $form);
  }
  protected function sso_form_profile( $data = array(), $attr ) {

    $broker_key = Configure::read('hippo.sso_broker_key'); 
    $redirect = Router::url('/', true) . $this->request->url;
    $formAction = 'http://' . SSO_PARENT;
    ob_start();
    $userinfo = $data;

	
    $data  = (array) $data;

	
	foreach( $data as $key => $value ){
		$value = str_replace("\\\'","'",$value);
		$value = str_replace("\\","",$value);
		$data[$key] = $value;
	}
	
    //below is the same form code as in the wp-sso plugin.
    //as long as the form action along with the sso_redirect and sso_redirect hiddens are stay the same everything else can be change. 

	if( $attr['_error'] ){ ?><div class="sso_alert sso_error"><?php echo $attr['_error']; ?></div><?php }
	
	if( $attr['_saved'] ){ ?><div class="sso_alert sso_success"><strong>Your profile has been updated.</div><?php }
		
    ?>
	<style type="text/css" media="screen">
	.sso_alert {
		border: 1px solid transparent;
	    border-radius: 4px;
	    margin-top: 20px;
	    padding: 15px;
		font-size:16px;
		line-height: 28px;
	}
	.sso_alert div {
		margin: 0px 0;
	}
	.sso_error {
		background-color: #f2dede;
	    border-color: #a94442;
	    color: #a94442;
	}
	.sso_error a { color: #a94442; text-decoration:underline; }
	.sso_error a:hover { color: #a94442; text-decoration:underline; }
	.sso_success {
		background-color: #dff0d8;
	    border-color: #3c763d;
	    color: #3c763d;
	}
	.sso_success a { color: #3c763d; }
	.sso_success a:hover { color: #3c763d; }
	</style>
	
	
      <form class="ssoForm" action="<?php echo $formAction; ?>" method="post" accept-charset="utf-8">
        

        <input type="hidden" name="sso_redirect" value="<?php echo $redirect ?>" id="sso_redirect">
        <input type="hidden" name="sso_key" value="<?php echo $broker_key; ?>" id="sso_key">

        

        <input type="hidden" name="existing_username" value="<?php echo $userinfo->username; ?>" id="existing_username">
        <input type="hidden" name="existing_email" value="<?php echo $userinfo->email; ?>" id="existing_email">

        <input type="hidden" name="sso_action" value="profile" id="sso_action">
        <input type="hidden" name="username" value="<?php echo $data['username']; ?>" id="username">
        
        
        <?php if( $attr['tw_magazine'] ){ $travelweek_sub_type = $data['travelweek_sub_type']; $subscription_dt = $data['subscription_dt']; ?>
        <?php if( $subscription_dt == '0000-00-00 00:00:00' ){ ?><input type="hidden" name="subscription_dt" value="<?php echo date("Y-m-d H:i:s"); ?>" id="subscription_dt"><?php } ?>
        <input type="hidden" name="renewal_dt" value="<?php echo date("Y-m-d H:i:s",strtotime("+ 2 years")); ?>" id="renewal_dt"> 
        <fieldset>
          <legend>Travelweek Subscription Type</legend>
          <div class="formGroup">
            <div class="fieldHalf">
              <label for=""><?php echo __('Please indicate in which format you would like to receive Travelweek'); ?>:</label>
              <?php
              if( $travelweek_sub_type == '' ){ $travelweek_sub_type = 'Print_Digital'; }
              $subscription = array(
                'Print' => 'Travelweek Print',
                'Digital' => 'Travelweek Digital',
                'Print_Digital' => 'Travelweek Print and Digital',
                'None' => 'No subscription at this moment',
                );
              ?>
              <?php foreach( $subscription as $key => $value ){ if( $travelweek_sub_type == $key ){ $checked = ' checked '; }else{ $checked = ''; } ?>
              <div><input type="radio" value="<?php echo $key; ?>" name="travelweek_sub_type" <?php echo $checked; ?> id="travelweek_sub_type_<?php echo $key; ?>"> <?php echo $value; ?></div>
              <?php } ?>
            </div>
            <div style="clear:both;"></div>
          </div>
        </fieldset>
        <?php } ?>
        
        <fieldset>
          <legend><?php echo __('Personal Information'); ?></legend>
          <div class="formGroup">
            <div class="fieldHalf">
              <label for=""><?php echo __('First Name'); ?><span class="sso_required_icon">*</span></label>
              <input type="text" name="first_name" value="<?php echo $data['first_name']; ?>" id="first_name">
            </div>
            <div class="fieldHalf">
              <label for=""><?php echo __('Last Name'); ?><span class="sso_required_icon">*</span></label>
              <input type="text" name="last_name" value="<?php echo $data['last_name']; ?>" id="last_name">
            </div>
            <div style="clear:both;"></div>
          </div>
        </fieldset>
        
        <fieldset>
          <div class="formGroup">
            <div class="fieldHalf">
              <label for=""><?php echo __('Username'); ?> <span class="sso_required_icon">*</span></label>
              <input type="text" name="username" value="<?php echo $data['username']; ?>" id="username">
            </div>
            <div class="fieldHalf">
            <div class="disclaimer"><br><?php echo __('Username can only consist of letters, numbers, spaces, hyphens and underscores.'); ?></div></div>
            <div style="clear:both;"></div>
          </div>
          
        </fieldset>
        
        <fieldset>
          <legend><?php echo __('Preferred Email'); ?></legend>
          <div class="formGroup">
            <div class="fieldHalf">
              <label for=""><?php echo __('Email'); ?> <span class="sso_required_icon">*</span></label>
              <input type="email" name="email" value="<?php echo $data['email']; ?>" id="email">
            </div>
            <div class="fieldHalf">
              <label for=""><?php echo __('Confirm Email'); ?> <span class="sso_required_icon">*</span></label>
              <input type="email" name="email_confirm" value="<?php echo $data['email']; ?>" id="email_confirm">
            </div>
            <div style="clear:both;"></div>
          </div>
        </fieldset>
        
        <fieldset>
          <legend><?php echo __('Password'); ?></legend>
          <div class="formGroup">
            <div class="fieldHalf">
              <label for=""><?php echo __('Password'); ?></label>
              <input autocomplete="off" type="password" name="password" value="" id="password">
            </div>
            <div class="fieldHalf">
              <label for=""><?php echo __('Confirm Password'); ?></label>
              <input autocomplete="off" type="password" name="password_confirm" value="" id="password_confirm">
            </div>
            <div style="clear:both;"></div>
            <div class="disclaimer"><?php echo __('Hint: the password should be at least eight (8) characters long.  To make it stronger, use upper and lower case letters, numbers and symbols like'); ?>&nbsp ! " ? $ % ^ & ).</div>
          </div>
        </fieldset>
        
        <?php $this->sso_form_specific( $data, 'profile', $attr ); ?>

        <p><input type="submit" class="button dark" value="<?php echo __('Update Profile'); ?> &rarr;"></p>
        
      </form>
  
    <?php
    
    $content = ob_get_contents();
    ob_end_clean();
    
    return $content; 
  }


  protected function sso_form_register( $attr ) {
	  
  	$_reg_error = '';
	$_reg_saved = false;

	$broker_url =  Router::url('/', true);
	$broker_url =  str_replace("http://","",$site_url);
	$broker_url =  str_replace("https://","",$site_url);
	$broker_url =  str_replace("/","",$site_url);
	
  	if( isset( $_REQUEST['sso_session_check'] ) && $_REQUEST['sso_session_check'] ){
		
  		$http = new Client();
  		        $params = [
  		                'action'=>'sso_form_session',
  		                'broker_url' => $broker_url,
  		                'session' => $_REQUEST['sso_session_check']
  		            ] ;
  		        $response = $http->post(
  		            'http://' . SSO_PARENT . '/wp-admin/admin-ajax.php',
  		            $params
  		        );
		
  		$items = get_object_vars( json_decode( $response->body() ) );
		
		if( isset( $items['_error'] ) && !is_array($items['_error']) && $items['_error'] ){ 
			$_reg_error = $items['_error']; 
		}elseif( isset( $items['_error'][0] ) && $items['_error'][0] ){ 
			$_reg_error = $items['_error'][0]; 
		}

  		if( isset( $items['_post'] ) ){

  				foreach( $items['_post'] as $key => $value ){
  					if( !is_array( $value ) ){
						$this->request->data[ $key ] = $value;
  					}
					
  				}
			
			
  		}
		
		if( isset( $items['_saved'] ) ){
			$_reg_saved = true;
		}
  	}
	
    $broker_key = Configure::read('hippo.sso_broker_key'); 
    $redirect = Router::url('/', true) . $this->request->url;
    $formAction = 'http://' . SSO_PARENT;
    ob_start();

    //below is the same form code as in the wp-sso plugin.
    //as long as the form action along with the sso_redirect and sso_redirect hiddens are stay the same everything else can be change. 
    ?>

		<style type="text/css" media="screen">
		.sso_alert {
			border: 1px solid transparent;
		    border-radius: 4px;
		    margin-top: 20px;
		    padding: 15px;
			font-size:16px;
			line-height: 28px;
		}
		.sso_alert div {
			margin: 0px 0;
		}
		.sso_error {
			background-color: #f2dede;
		    border-color: #a94442;
		    color: #a94442;
		}
		.sso_error a { color: #a94442; text-decoration:underline; }
		.sso_error a:hover { color: #a94442; text-decoration:underline; }
		.sso_success {
			background-color: #dff0d8;
		    border-color: #3c763d;
		    color: #3c763d;
		}
		.sso_success a { color: #3c763d; }
		.sso_success a:hover { color: #3c763d; }
		</style>
		
	<?php if( $_reg_error ){ ?>
		<div class="sso_alert sso_error"><?php echo $_reg_error; ?></div>

	<?php } ?>

	<?php if( $_reg_saved ){ ?>
	<div class="sso_alert sso_success"><strong>Thank you for registering.</strong><br>Your account has been created and you can now log in.<br><a href="<?php echo Router::url('/', true); ?>">Login to your account</a></div>
	<?php }else{ ?>
		
    <form class="ssoForm" action="<?php echo $formAction; ?>" method="post" accept-charset="utf-8">
      
      <input type="hidden" name="sso_redirect" value="<?php echo $redirect; ?>" id="sso_redirect">
      <input type="hidden" name="sso_key" value="<?php echo $broker_key; ?>" id="sso_key">
      <input type="hidden" name="sso_action" value="register" id="sso_action">
      <input type="hidden" name="sso_source" value="<?php echo $redirect; ?>" id="sso_source">
      
      <?php if( $attr['tw_magazine'] ){  $travelweek_sub_type = sso_form_post('travelweek_sub_type'); ?>  
      <input type="hidden" name="subscription_dt" value="<?php echo date("Y-m-d H:i:s"); ?>" id="subscription_dt">
      <input type="hidden" name="renewal_dt" value="<?php echo date("Y-m-d H:i:s",strtotime("+ 2 years")); ?>" id="renewal_dt">
      <br />
      <fieldset>
        <legend>Travelweek Subscription Type</legend>
        <div class="formGroup">
          <div class="fieldHalf">
            <label for="">Please indicate in which format you would like to receive Travelweek:</label>
            <?php
            if( $travelweek_sub_type == '' ){ $travelweek_sub_type = 'Print_Digital'; }
            $subscription = array(
              'Print' => 'Travelweek Print',
              'Digital' => 'Travelweek Digital',
              'Print_Digital' => 'Travelweek Print and Digital',
              'None' => 'No subscription at this moment',
              );
            ?>
            <?php foreach( $subscription as $key => $value ){ if( $travelweek_sub_type == $key ){ $checked = ' checked '; }else{ $checked = ''; } ?>
            <div><input type="radio" value="<?php echo $key; ?>" name="travelweek_sub_type" <?php echo $checked; ?> id="travelweek_sub_type_<?php echo $key; ?>"> <?php echo $value; ?></div>
            <?php } ?>
          </div>
          <div style="clear:both;"></div>
        </div>
      </fieldset>
      <?php } ?>
      <br />
      <br />
      <fieldset>
        <legend><?php echo __('Personal Information'); ?></legend>
        <div class="formGroup">
          <div class="fieldHalf">
            <label for=""><?php echo __('First Name'); ?> <span class="sso_required_icon">*</span></label>
            <input type="text" name="first_name" value="<?php echo $this->request->data['first_name']; ?>" id="first_name">
          </div>
          <div class="fieldHalf">
            <label for=""><?php echo __('Last Name'); ?> <span class="sso_required_icon">*</span></label>
            <input type="text" name="last_name" value="<?php echo $this->request->data['last_name']; ?>" id="last_name">
          </div>
          <div style="clear:both;"></div>
        </div>
      </fieldset>
      
      <fieldset>
        <legend><?php echo __('Preferred Email'); ?></legend>
        <div class="formGroup">
          <div class="fieldHalf">
            <label for=""><?php echo __('Email'); ?> <span class="sso_required_icon">*</span></label>
            <input type="email" name="email" value="<?php echo $this->request->data['email']; ?>" id="email">
          </div>
          <div class="fieldHalf">
            <label for=""><?php echo __('Confirm Email'); ?> <span class="sso_required_icon">*</span></label>
            <input type="email" name="email_confirm" value="<?php echo $this->request->data['email_confirm']; ?>" id="email_confirm">
          </div>
          <div style="clear:both;"></div>
        </div>
      </fieldset>
      
      <fieldset>
        <legend><?php echo __('Preferred Username and Password'); ?></legend>
        <div class="formGroup">
          <div class="fieldHalf">
            <label for=""><?php echo __('Username'); ?> <span class="sso_required_icon">*</span></label>
            
            <input type="text" name="username" value="<?php echo $this->request->data['username']; ?>" id="username">
          </div>
          <div class="fieldHalf disclaimer"><br/><?php echo __('Username can only consist of letters, numbers, spaces, hyphens and underscores.'); ?></div>
          <div style="clear:both;"></div>
          
        </div>
        <div class="formGroup">
          <div class="fieldHalf">
            <label for=""><?php echo __('Password'); ?> <span class="sso_required_icon">*</span></label>
            <input autocomplete="off" type="password" name="password" value="" id="password">
          </div>
          <div class="fieldHalf">
            <label for=""><?php echo __('Confirm Password'); ?> <span class="sso_required_icon">*</span></label>
            <input autocomplete="off" type="password" name="password_confirm" value="" id="password_confirm">
          </div>
          <div style="clear:both;"></div>
          <div class="disclaimer"><?php echo __('Hint: the password should be at least eight (8) characters long.  To make it stronger, use upper and lower case letters, numbers and symbols like'); ?>&nbsp ! " ? $ % ^ & ).</div>  
        </div>
      </fieldset>

      <?php $this->sso_form_specific( array(), 'register', $attr ); ?>
          <?php $serverAddress = $_SERVER['REMOTE_ADDR'];
/*	  
        if(($serverAddress == '99.232.36.54' || $serverAddress == '216.191.207.70' || $serverAddress == '66.249.83.179' || $serverAddress == '104.251.98.226')){   //($serverAddress == '99.232.36.54' || $serverAddress == '216.191.207.70' || $serverAddress == '66.249.83.179')?>
      <fieldset>
        <legend>Stay In the Know > Subscribe to our Digital Products</legend>
        
            <?php 

          // !!!! NOTE ::: Antonio ... use $subscription_db-> instead of $wpdb to access anything in the tweek_subscription database

          //display:none;
                          
                          ///subscription/processSubscription.php
                          
          //                $unSubNumTop = 0;
          //                $rootDirectoryFunc = get_template_directory();
                          
                  
                  
          //        $subTypes = $wpdb->get_results("SELECT subID,subName,description_en from subTypes
          //        WHERE subID NOT LIKE 165436") or die("error: ".mysql_error());
          //        foreach( $subTypes as $subType ){
          //          if( isset( $_POST[ $subType->Field ] ) ){
          //            print_r($subTypes);//$user_data[ $subType->Field ] = $_POST[ $subType->Field ];
          //          }
          //        }

          //$subscription_db = new wpdb( 'tweek_wheelsUp', 'WuPo_966', 'tweek_subscription', 'localhost' );
            
          $results = [];

          $unSubBoxesTop = "";
          $UnSubNumTop = 0;

          foreach($results as $result){ 
 
            
            $subs = $result['subID'];
            $nonSubName = $result['subName'];
            $description_en = stripcslashes(utf8_encode($result['description_en']));
            
            $nonShow = array("33333","164735");            
            //                  $diff_subs = array_diff($arrSubs,array("164617","164735"));//"33333",
                        
            if(in_array($subs,$nonShow))  {
              
            } else {
              //$nonSubID = $key;
              if ($subs == '164613'){
                $addAttr = ""; //checked='chedcked' required='required' readonly
              } else {
                $addAttr = "";
              }
              $unSubBoxesTop = $unSubBoxesTop . "
                <span class='hoverImgHome' id='$subs'>
                  <input style='float:left;margin-right:5px;margin-top:4px;' name='subscribeBoxes[]' id='sub$subs' type='checkbox' value='$subs' $addAttr/>
                  <strong><label for='sub$subs'><span style='font-size:12px;'>$nonSubName</span> - <span style='font-size:10px;font-family: SourceSansPro-Regular, Helvetica, \"Trebuchet MS\", Arial, sans-serif;'>$description_en</span></label></strong></span>
                </span><br style='clear:both' />";
            }
          }
          if($unSubBoxesTop) {
          echo $unSubBoxesTop;
        }

        }


              ?>
          </fieldset>
          
    <?php
*/
    $sso_policy_page = Router::url(['controller'=>'Pages','action'=>'privacy','prefix' => false]);
    if( $sso_policy_page ){
      $sso_policy_page = '<a href="'.$sso_policy_page.'" target="_blank">Privacy Policy</a>';
    }else{
      $sso_policy_page = 'Privacy Policy';
    }

    ?>
    <fieldset>
      <legend><?php echo __('Verify Human, agree to Terms of Use, and Submit Registration Form'); ?></legend>
      <?php $number1 = rand(1,10); $number2 = rand(1,10); $answer = MD5($number1+$number2); ?>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="registration_question"><?php echo __('Confirm that you are human'); ?></label>
          <div class="disclaimer"><?php echo __('Please enter the sum of'); ?> <?php echo $number1 ?> + <?php echo $number2; ?> <?php echo __('in the field below.'); ?></div>
          <input type="hidden" name="reg_nonce" value="<?php echo $answer; ?>" id="reg_nonce">
          <input type="text" name="registration_question" value="" id="registration_question">
        </div>
        <div class="fieldHalf">
          <label><input type="checkbox" value="true" id="terms_agree" name="terms_agree" style="display:inline-block;width:auto;"> <?php echo __('I agree to the terms as detailed in the'); ?> <?php echo $sso_policy_page; ?></label>
        </div>
        <div style="clear:both;"></div>
      </div>
      
    </fieldset>
    
    <p><input type="submit" class="button dark" value="<?php echo __('Register'); ?>"></p>
    
    </form>
  	
	<?php } ?>
	
    <?php
    
    $content = ob_get_contents();
    ob_end_clean();
    
    return $content;
      

  }
  protected function sso_form_specific( $data = array(), $action = 'profile', $attr ){
    function sso_form_post( $key, $default = '', $echo = true ){
      if( isset( $_POST[ $key ] ) ){ 
        if( $echo ){ echo $_POST[ $key ]; }else{ return $_POST[ $key ]; } 
      }else{ 
        if( $echo ){ echo $default; }else{ return $default; }  
      }
      
    }
    /*
    `company` varchar(250) DEFAULT NULL,
    `job_function` varchar(250) DEFAULT NULL,
    `travel_group` varchar(250) DEFAULT NULL,
    `business_type` varchar(250) DEFAULT NULL,
    
    `contact_info_type` varchar(50) DEFAULT NULL,
    `street` varchar(250) DEFAULT NULL,
    `streetName` varchar(250) DEFAULT NULL,
    `unitApt` varchar(250) DEFAULT NULL,
    `province` varchar(30) NOT NULL,
    `city` varchar(50) DEFAULT NULL,
    `postal_code` varchar(10) DEFAULT NULL,
    `phone_no` varchar(30) DEFAULT NULL,
    `fax` varchar(30) DEFAULT NULL,
    `website` varchar(250) DEFAULT NULL,
    
    `ca_verify` varchar(10) DEFAULT NULL,
    
    `travelweek_sub_type` varchar(250) DEFAULT NULL,
    `agent` varchar(255) DEFAULT NULL,
    `source` varchar(250) DEFAULT NULL,
    `unitType` varchar(255) DEFAULT NULL,
    `status` varchar(30) DEFAULT 'Active',
    `guid` varchar(255) DEFAULT NULL,
    `reminder` varchar(3) DEFAULT 'Yes',
    `subscription_dt` datetime DEFAULT NULL,
    `renewal_dt` datetime DEFAULT NULL,
    `promoCode` varchar(255) DEFAULT NULL,
    `loginFrom` varchar(255) DEFAULT NULL,
    `login` varchar(255) NOT NULL DEFAULT 'out',
    `features` varchar(255) DEFAULT NULL,
    */
    
    if( $data ){
      foreach( $data as $key => $value ){ $_POST[ $key ] = $value; }
    }
    
    $travel_group_values = array(
      'Advantage_Travel_TCOMM'=>'Advantage Travel TCOMM',
      'Algonquin_Travel'=>'Algonquin Travel (or Algonquin/Voyages Funtastique)',
      'American_Express'=>'American Express',
	  'Atrium_Group'=>'Atrium Group',
	  'BCD_Travel'=>'BCD Travel',
      'BTI_Travel'=>'BTI Travel',
      'CAA'=>'CAA',
      'Carlson_Wagonlit'=>'Carlson Wagonlit',
	  'Club_Voyages'=>'Club Voyages',
      'Cruiseshipcenter'=>'Cruiseshipcenter',
      'Cruise_Holidays'=>'Cruise Holidays',
      'Ensemble_Travel'=>'Ensemble Travel',
      'Flight_Center'=>'Flight Center',
	  'HRG'=>'HRG',
	  'K_Walsh'=>'K Walsh',
      'Maritime_Travel'=>'Maritime Travel',
	  'Merit_Travel'=>'Merit Travel',
      'My_Travel'=>'My Travel',
	  'Nexion'=>'Nexion',
      'Sears_Travel'=>'Sears Travel',
	  'The_Travel_Agent_Next_Door'=>'The Travel Agent Next Door',
      'Thomas_Cook_Marlin'=>'Thomas Cook/Marlin',
      'TPI_Travel'=>'TPI Travel',
	  'Transat'=>'Transat',
      'Travel_Choice_Amex'=>'Travel Choice/Amex',
	  'Travel_Counsellors'=>'Travel Counsellors',
	  'Travelonly'=>'TravelOnly',
	  'Travelsavers'=>'Travelsavers',
	  'Tripcentral_ca'=>'Tripcentral.ca',
      'Uniglobe'=>'Uniglobe',
      'Vacation_com'=>'Vacation.com',
	  'Virtuoso'=>'Virtuoso',
	  'Vision_Travel'=>'Vision Travel',
      'Independent'=>'Independent',
      'Travelsavers'=>'Travelsavers',
      'Other'=>'Other'
    );
    
    $job_function_values = array(
      'Owner' => 'Owner',
      'Manager' => 'Manager',
      'Leisure Counselor' => 'Leisure Counselor',
      'Corporate Counselor' => 'Corporate Counselor',
      'Administrator' => 'Administrator',
      'Other' => 'Other'
    );
    
    $business_type_values = array(
    'Association'=>'Association, federation, club, society, group tour organizer',
    'Carrier_Transportation'=>'Carrier/Transportation - Air, Cruise line, Rail, Motor coach, Car, etc.',
    'Hotel_Resort'=>'Hotel, Resort, Motel and/or Representative',
    'Library_Newspaper'=>'Library, newspaper, public relations firms',
    'Student_Travel_Counselor'=>'Student Travel counselor',
    'Tour_Operator'=>'Tour Operator/Wholesaler',
    'Tourist_Board'=>'Tourist Board or Office, Chamber Of Commerce, Embassy',
	'Travel_Agent'=>'Travel Agent',
    'Commercial_Firm'=>'Travel department in commercial firm',
    'Other'=>'Other'
    );
    
    $province_values = array(
    'Alberta'=>'Alberta',
    'British Columbia'=>'British Columbia',
    'Manitoba'=>'Manitoba',
    'New Brunswick'=>'New Brunswick',
    'Newfoundland'=>'Newfoundland',
    'Northwest Territories'=>'Northwest Territories',
    'Nova Scotia'=>'Nova Scotia',
    'Nunavut'=>'Nunavut',
    'Ontario'=>'Ontario',
    'Prince Edward Island'=>'Prince Edward Island',
    'Quebec'=>'Quebec',
    'Saskatchewan'=>'Saskatchewan',
    'Yukon Territory'=>'Yukon Territory',
	'Alabama'=>'Alabama',
	'Alaska'=>'Alaska',
'Arizona'=>'Arizona',
'Arkansas'=>'Arkansas',
'California'=>'California',
'Colorado'=>'Colorado',
'Connecticut'=>'Connecticut',
'Delaware'=>'Delaware',
'District of Columbia'=>'District of Columbia',
'Florida'=>'Florida',
'Georgia'=>'Georgia',
'Hawaii'=>'Hawaii',
'Idaho'=>'Idaho',
'Illinois'=>'Illinois',
'Indiana'=>'Indiana',
'Iowa'=>'Iowa',
'Kansas'=>'Kansas',
'Kentucky'=>'Kentucky',
'Louisiana'=>'Louisiana',
'Maine'=>'Maine',
'Maryland'=>'Maryland',
'Massachusetts'=>'Massachusetts',
'Michigan'=>'Michigan',
'Minnesota'=>'Minnesota',
'Mississippi'=>'Mississippi',
'Missouri'=>'Missouri',
'Montana'=>'Montana',
'Nebraska'=>'Nebraska',
'Nevada'=>'Nevada',
'New Hampshire'=>'New Hampshire',
'New Jersey'=>'New Jersey',
'New Mexico'=>'New Mexico',
'New York'=>'New York',
'North Carolina'=>'North Carolina',
'North Dakota'=>'North Dakota',
'Ohio'=>'Ohio',
'Oklahoma'=>'Oklahoma',
'Oregon'=>'Oregon',
'Pennsylvania'=>'Pennsylvania',
'Rhode Island'=>'Rhode Island',
'South Carolina'=>'South Carolina',
'South Dakota'=>'South Dakota',
'Tennessee'=>'Tennessee',
'Texas'=>'Texas',
'Utah'=>'Utah',
'Vermont'=>'Vermont',
'Virginia'=>'Virginia',
'Washington'=>'Washington',
'West Virginia'=>'West Virginia',
'Wisconsin'=>'Wisconsin',
'Wyoming'=>'Wyoming'
    );
  
    // Antonio  
    $lc_role_values = array(
      'Travel Agent'=>'Travel Agent',
      'Travel Professional'=>'Travel Professional',
      'Student'=>'Student',
      'Other'=>'Other'
    );
    // /Antonio
    
    $lc_agency_values = array(
      'Yes'=>'Yes',
      'No'=>'No',
    );
    
    ?>
    
    <?php if( $attr['lc_role_agency'] ){ ?>
    <fieldset>
      <legend><?php echo __('Professional Information'); ?></legend>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="phone_no"><?php echo __('Role'); ?></label>
          <select name="job_function" id="job_function">
                    <option value="" disabled="disabled"> -- Select -- </option>
            <?php $lc_role = sso_form_post('lc_role','',false); ?>
            <?php foreach( $lc_role_values as $key => $value  ){ if( $key == $lc_role ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="fieldHalf">
          <label for="website">Agency Home-based</label>
          <select name="lc_agency" id="lc_agency">
          <option value="" disabled="disabled"> -- Select -- </option>
          <?php $lc_agency = sso_form_post('lc_agency','',false); ?>
          <?php foreach( $lc_agency_values as $key => $value  ){ if( $key == $lc_agency ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
          <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
          <?php } ?>
          </select>
        </div>
        <div style="clear:both;"></div>
      </div>  
    </fieldset>
    <?php } ?>
    
    <?php if( $attr['company_job'] || $attr['pro_information'] ){ ?>
    <fieldset>
      <legend><?php echo __('Professional Information'); ?></legend>
      <?php if( $attr['company_job'] || $attr['pro_information'] ){ ?>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="company"><?php echo __('Company'); ?></label>
          <input type="text" name="company" value="<?php sso_form_post('company'); ?>" id="company">
        </div>
        <div class="fieldHalf">
          <label for="job_function"><?php echo __('Job Function'); ?></label>
          <select name="job_function" id="job_function">
                    <option value="" disabled="disabled"> -- Select -- </option>
            <?php $job_function = sso_form_post('job_function','',false); ?>
            <?php foreach( $job_function_values as $key => $value  ){ if( $key == $job_function ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div style="clear:both;"></div>
      </div>
      <?php } ?>
      <?php if( $attr['pro_information'] ){ ?>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="travel_group"><?php echo __('Travel Group'); ?></label>
          <select name="travel_group" id="travel_group">
            <option value="" disabled="disabled"> -- Select -- </option>
            <?php $travel_group = sso_form_post('travel_group','',false); ?>
            <?php foreach( $travel_group_values as $key => $value  ){ if( $key == $travel_group ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="fieldHalf">
          <label for="business_type"><?php echo __('Business Type')?></label>
          <select name="business_type" id="business_type">
            <option value="" disabled="disabled"> -- Select -- </option>
            <?php $business_type = sso_form_post('business_type','',false); ?>
            <?php foreach( $business_type_values as $key => $value  ){ if( $key == $business_type ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div style="clear:both;"></div>
      </div>
      <?php } ?>
    </fieldset>
    <?php } ?>
    
    <?php if( $attr['contact_lrg'] || $attr['contact_sml'] ){ ?>
    <fieldset>
      <legend><?php echo __('Contact Information'); ?></legend>
      <?php if( $attr['contact_lrg'] ){ ?>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="contact_info_type"><?php echo __('This is my'); ?></label>
          <?php $contact_info_type = sso_form_post('contact_info_type','',false); ?>
          <select name="contact_info_type" id="contact_info_type">
                <option value="business" <?php if ( $contact_info_type == 'business') echo 'selected="selected"'; ?> >Business Contact Information</option> 
                <option value="residence" <?php if ( $contact_info_type == 'residence') echo 'selected="selected"'; ?> >Residence Contact Information</option>
            </select>
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldSingle">
          <label for="street"><?php echo __('Street'); ?></label>
          <input type="text" style="width:12%;display:inline-block;" name="street" value="<?php sso_form_post('street'); ?>" id="street">
          <input type="text" style="width:83%;margin-left:2%;display:inline-block;" name="streetName" value="<?php sso_form_post('streetName'); ?>" id="streetName">
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="unitApt"><?php echo __('Unit/Apt/Suite'); ?></label>
          <input type="text" name="unitApt" value="<?php sso_form_post('unitApt'); ?>" id="unitApt">
        </div>
        <div class="fieldHalf">
          <label for="city"><?php echo __('City'); ?></label>
          <input type="text" name="city" value="<?php sso_form_post('city'); ?>" id="city">
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="province"><?php echo __('Province/State'); ?></label>
          <select name="province" id="province">
                    <?php $province = sso_form_post('province','Ontario',false); ?>
            <?php foreach( $province_values as $key => $value  ){ if( $key == $province ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="fieldHalf">
          <label for="postal_code"><?php echo __('Postal Code'); ?></label>
          <input type="text" name="postal_code" value="<?php sso_form_post('postal_code'); ?>" id="postal_code">
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="phone_no"><?php echo __('Phone'); ?></label>
          <input type="text" name="phone_no" value="<?php sso_form_post('phone_no'); ?>" id="phone_no">
        </div>
        <div class="fieldHalf">
          <label for="fax">FAX</label>
          <input type="text" name="fax" value="<?php sso_form_post('fax'); ?>" id="fax">
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="website"><?php echo __('Website (www.domain.com)'); ?></label>
          <input type="text" name="website" value="<?php sso_form_post('website'); ?>" id="website">
        </div>
        <div style="clear:both;"></div>
      </div>
      <?php } ?>
      
      <?php if( $attr['contact_sml'] ){ ?>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="province">Province/State</label>
          <select name="province" id="province">
                    <?php $province = sso_form_post('province','Ontario',false); ?>
            <?php foreach( $province_values as $key => $value  ){ if( $key == $province ){ $selected = ' selected="selected" '; }else{ $selected = ''; } ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
          </select>
        </div>
        <div style="clear:both;"></div>
      </div>
      <div class="formGroup">
        <div class="fieldHalf">
          <label for="phone_no">Phone</label>
          <input type="text" name="phone_no" value="<?php sso_form_post('phone_no'); ?>" id="phone_no">
        </div>
        <div class="fieldHalf">
          <label for="website">Website (www.domain.com)</label>
          <input type="text" name="website" value="<?php sso_form_post('website'); ?>" id="website">
        </div>
        <div style="clear:both;"></div>
      </div>
      <?php } ?>
      
    </fieldset>
    <?php } ?>
    
    <?php if( $attr['tw_verfiy_request'] ){ ?>
    <fieldset>
      <legend>Verify Personal Request <span class="sso_required_icon">*</span></legend>
      <div class="formGroup">
        <div class="fieldSingle">
          <label for="website">Please answer the following question as required by the Circulation Auditors to verify your personal request for this subscription.</label>
          <div class="disclaimer">* What is the first letter of the high school you graduated from?:</div>
          <input type="text" name="ca_verify" value="<?php sso_form_post('ca_verify'); ?>" id="ca_verify">
        </div>
        <div style="clear:both;"></div>
      </div>
    </fieldset>
    <?php } ?>
    
    <?php
  }
}

?>