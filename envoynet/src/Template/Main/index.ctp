<!-- Top Navigation Bar -->
  <header id="top-header">
    <nav >



         <section id="login" class="tabs mobile ">
                  <span>Login As:</span>
                   <div class="tabs__tabItem">
                       <input type="radio" id="tab-one" name="tab-group-one">
                       <label for="tab-one"><?php echo __('Travel Agent'); ?></label>
                       
                <div class="tabs__content">


               


          <?php echo $this->Form->create('LoginData', array('url' => array('controller'=>'main', 'action' => 'login', 'prefix' => 'agent'), 'id' => 'agent-login')); ?>

          <?php echo $this->Form->input('username',array('div'=>false,'label'=>false,'type'=>'text','class'=>'', 'placeholder'=>__("Email"))) ?>

          <?php echo $this->Form->input('password',array('div'=>false,'label'=>false,'type'=>'password', 'placeholder'=>__("Password"))) ?>

          <a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'language','prefix'=>'agent')); ?>"><?php echo __('Forgot Password'); ?></a>




          <button class="button" type="submit"><?php echo __('Login'); ?></button>
          <?php echo $this->Form->end(); ?>

                       </div> 
                   </div>
                    
                    <div class="tabs__tabItem">

                       <input type="radio" id="tab-two" name="tab-group-one">
                       <label for="tab-two"><?php echo __('Supplier'); ?></label>
                     
                       <div class="tabs__content">


                        <?php echo $this->Form->create('Supplier',array('url'=>array('controller'=>'main','action'=>'login','prefix' => 'supplier')));?>

                        <?php echo $this->Form->input('username',array('div'=>false,'label'=>false,'type'=>'text', 'placeholder'=>__("Username"))) ?>

                        <?php echo $this->Form->input('password',array('div'=>false,'label'=>false,'placeholder'=>__("Password"))) ?>

                        <button class="button" type="submit"><?php echo __('Login'); ?></button>

                        <a href="<?php echo $this->Url->build(array('controller' => 'password', 'action' => 'forgot','prefix'=>'supplier')); ?>"><?php echo __('Forgot Password'); ?></a>

                         <?php echo $this->Form->end(); ?>

                       </div> 
                   </div>
                    

                </section>

      <ul class="language">
        <a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'language','prefix'=>'agent')); ?>">Language: <span class="english">EN</span><span class="french">FR</span></a></li>
      </ul>


      <ul class="desktop">
        <li class="gray-text">TORONTO    PICKERING    RICHMOND</li>
        <li class="gray-text"><u><a href="tel:9058310006">(905)831-0006</a></u></li>
        <li class="white-text"><u><a href="mailto:MAIL@ENVOYNETWORKS.CA"></u>MAIL@ENVOYNETWORKS.CA</a></li>
      </ul>
    </nav> 
  </header>

<div id="wrapper">
  <input id="toggle" type="checkbox">

  <div class="content "> 
      
      <header id="main-header" class="section__content " >
      
      <?php echo $this->Html->image('assets/envoy-logo.svg', array( 'id'=>'logo'));?> 
          
          <label for="toggle"> <?php echo $this->Html->image('assets/menu-button.png', array( 'id'=>'menu-button'));?> </label>

      
        <nav class="menu">
          <ul>
            <li><a href="#"><?php echo __('Services'); ?></a>
              <ul>
              <li><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'logistics','prefix'=>'agent')); ?>"><?php echo __('Logistics'); ?></a></li> 
              <li><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'digital','prefix'=>'agent')) ?>"><?php echo __('Digital Support'); ?></a></li> 
              <li><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'marketing','prefix'=>'agent')) ?>"><?php echo __('Marketing Solutions'); ?></a></li> 
              </ul>

            </li>
            <li><a href="#"><?php echo __('About Us'); ?></a>
              <ul>
              <li><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'overview','prefix'=>'agent')); ?>"><?php echo __('Overview'); ?></a></li>
              <li><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'meettheteam','prefix'=>'agent')); ?>"><?php echo __('Meet the Team'); ?></a></li>
              </ul>
            </li>
            <li>  <a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'contactus','prefix'=>'agent')); ?>"><?php echo __('Contact Us'); ?></a> 
   </li>
            </ul>
        </nav>

      </header>

            <section id="masthead" class="section__content ">

                <section class="tabs desktop">
                    
                   <div class="tabs__tabItem">
                       <input type="radio" id="tab-1" name="tab-group-1" checked>
                       <label for="tab-1"><?php echo __('Travel Agent'); ?></label>
                       
                <div class="tabs__content">


          <?php echo $this->Form->create('LoginData', array('url' => array('controller'=>'main', 'action' => 'login', 'prefix' => 'agent'), 'id' => 'agent-login')); ?>

          <?php echo $this->Form->input('username',array('div'=>false,'label'=>false,'type'=>'text','class'=>'', 'placeholder'=>__("Email"))) ?>

          <?php echo $this->Form->input('password',array('div'=>false,'label'=>false,'type'=>'password', 'placeholder'=>__("Password"))) ?>

           <a href="<?php echo $this->Url->build(array('controller' => 'password', 'action' => 'forgot','prefix'=>'agent')); ?>"><?php echo __('Forgot Password'); ?></a>


          <button class="button" type="submit"><?php echo __('Login'); ?></button>
          <?php echo $this->Form->end(); ?>

                       </div> 
                   </div>
                    
                    <div class="tabs__tabItem">

                       <input type="radio" id="tab-3" name="tab-group-1">
                       <label for="tab-3"><?php echo __('Supplier'); ?></label>
                     
                       <div class="tabs__content">


                        <?php echo $this->Form->create('Supplier',array('url'=>array('controller'=>'main','action'=>'login','prefix' => 'supplier')));?>

                        <?php echo $this->Form->input('username',array('div'=>false,'label'=>false,'type'=>'text', 'placeholder'=>__("Username"))) ?>

                        <?php echo $this->Form->input('password',array('div'=>false,'label'=>false,'placeholder'=>__("Password"))) ?>

                        <button class="button" type="submit"><?php echo __('Login'); ?></button>

                       <a href="<?php echo $this->Url->build(array('controller' => 'password', 'action' => 'forgot','prefix'=>'supplier')); ?>"><?php echo __('Forgot Password'); ?></a>

                         <?php echo $this->Form->end(); ?>

                       </div> 
                   </div>
                    
                </section>


              <div class="section__content">
                <h2>
                  <?php echo __('Serving the Travel '); ?><br/>
                  <?php echo __('and Tourism Industry ') ?><br/>
                  <?php echo __('for over 15 years.') ?>
                </h2>
                  <br/>
                  <button class=" button button--green">
                  <a href="<?php echo $this->Url->build(array('controller' => 'agents', 'action' => 'register','prefix'=>'agent')); ?>">
                    <span> <?php echo __('BECOME A PART OF THE NETWORK') ?></span>
                  </a>
                </button>

              </div>

            </section>
          


          <section class="box box__blue overview">
            
             <?php echo $this->Html->image('assets/mastheadBgVans.png');?> 
              
              <div class="section__content ">
                <p>
                  <?php echo __('ENVOY is a distribution and fulfillment company that specializes in providing the travel industry with ') ?><a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'marketing')); ?>"><br/><?php echo __('custom marketing solutions') ?></a>.
                </p>
                <h5><?php echo __('Our business is built on:') ?></h5>

                <ul>
                  <li><?php echo __('Partnerships') ?></li>
                  <li><?php echo __('Intelligence') ?></li>
                  <li><?php echo __('Reach') ?></li></ul>
          
                <button class="button button--white button--full-width "> <a href="<?php echo $this->Url->build(array('controller' => 'pages', 'action' => 'overview')); ?>">
                <?php echo __('THE ENVOY OVERVIEW') ?></a></button>
                <br style="clear:both" />
              </div>
          </section>
          
          <section id="green-circle" class="section__content">
                      
                      <p class=" white-text " id="third-section-paragraph"><?php echo __('With significant investments in equipment, vehicles, infrastructure and technology. Envoy represents its modern position by exceeding the requirements of the Travel & Tourism industry.') ?></p>
          </section>


          <section  class="section__content">
            <h3 class="blue-text about-heading"><?php echo __('About ENVOY') ?></h3>
            <p class="gray-text font-large" class="section__content"><?php echo __('Envoy specializes in providing the Travel & Tourism industry with custom programs to deliver marketing campaigns that facilitate successful transactions. By leveraging it\'s extensive partnerships in Travel & Tourism, unparalleled reach is achieved to consumer & retail outlets.') ?></p>


          </section>

      



 <!--Start copyright Line content /-->

 <section class="" id="partners">
<div class="section__content">
 <h3 class="blue-text"><?php echo __('Our Partners') ?></h3>
 </div>
 <div class="section__images section__content">
    <?php echo $this->Html->image('assets/partners/usa.png');?>
    <?php echo $this->Html->image('assets/partners/g-adventures.png');?>
    <?php echo $this->Html->image('assets/partners/travel-guard.png');?>
    <?php echo $this->Html->image('assets/partners/star-clippers.png');?>
    <?php echo $this->Html->image('assets/partners/resorts-of-ontario.png');?>
    <?php echo $this->Html->image('assets/partners/ensemble.png');?>
    <?php echo $this->Html->image('assets/partners/bahia-principe.png');?>
  </div>
 </section>

  <section class="" id="footer">
  <div class="section__content">
    
      <div id="Travelweek_Holder">
   <a href="http://travelweek.ca" target="_blank"><?php echo $this->Html->image('assets/site_logos/logoTravelWeekGroup.svg',array('alt'=>'Travel Week - A Travel Week Company'));?> </a>
    <p>© Copyright Envoy Networks Inc. 2015</p>
</a>
  </div>
  </section>


<script type="text/javascript" language="javascript">
        $(".iframe-link").fancybox({
				'width'				: '45%',
				'height'			: '70%',
				'autoScale'			: false,
				'transitionIn'		: 'none',
				'transitionOut'		: 'none',
				'type'				: 'iframe'
			});
</script>

<script type="text/javascript" >
$(document).ready(function() {
	$('#LoginDataDigits1, #LoginDataDigits2, #LoginDataDigits3').autotab_magic().autotab_filter('numeric');
})

</script>
