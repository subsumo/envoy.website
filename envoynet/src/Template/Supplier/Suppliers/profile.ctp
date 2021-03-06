<div class="inner-content-wrapper">

    <div id="profile_info">
        <?php echo $this->Form->create('Supplier',array('url'=>array('controller'=>'Suppliers','action'=>'profile','prefix' => 'supplier'))); ?>


        <?php echo $this->Form->input('id',["hidden"=>"hidden", "label"=>false]); ?>

        <div id="Profile_form_holder" class="Base_red_form_txt"> &nbsp;&nbsp;<span class="data_Headers_Bl_txt"><?php echo __('Company Information'); ?></span></div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Company Name'); ?>:</div>
            <?php echo $this->Form->input('company', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2','escape'=>false)); ?>
        </div>

        <div id="Profile_form_holder" class="Base_red_form_txt"> &nbsp;&nbsp;<span class="data_Headers_Bl_txt"><?php echo __('Personal Information'); ?></span></div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('First Name'); ?>:</div>
            <?php echo $this->Form->input('contact_firstname', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Last Name'); ?>:</div>
            <?php echo $this->Form->input('contact_lastname', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div id="Profile_form_holder" class="Base_red_form_txt"> &nbsp;&nbsp;<span class="data_Headers_Bl_txt"><?php echo __('Mailing Information'); ?></span></div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Address'); ?>:</div>
            <?php echo $this->Form->input('address1', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Address2'); ?>:</div>
            <?php echo $this->Form->input('address2', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('City'); ?>:</div>
            <?php echo $this->Form->input('city', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Province'); ?>:</div>
            <?php echo $this->Form->input('province', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Postal Code'); ?>:</div>
            <?php echo $this->Form->input('postal', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Country'); ?>:</div>
            <?php echo $this->Form->input('country', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

        <div id="Profile_form_holder" class="Base_red_form_txt"> &nbsp;&nbsp;<span class="data_Headers_Bl_txt"><?php echo __('Contact Information'); ?></span></div>

        <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Email'); ?>:</div>
            <?php echo $this->Form->input('email', array('label' => false, 'div' => 'blank_form_holder_bg2', 'class' => 'Form_holder_style2')); ?>
        </div>

           <div class="form_content_field_left">
            <div class="User_Name_Form_Holder" class="Base_txt"><?php echo __('Main Travel Agency Phone Number'); ?>:</div>
            <ul id="profile-phone-input">
              <li>
                <?php echo $this->Form->input('digits1', array('div' => false, 'label' => false, 'type' => 'text','minlength'=>'3', 'MAXLENGTH' => '3','class'=>"required number")) ?>
              </li>
              <li>
                <?php echo $this->Form->input('digits2', array('div' => false, 'label' => false, 'type' => 'text','minlength'=>'3', 'MAXLENGTH' => '3','class'=>"required number")) ?>
              </li>
              <li>
                <?php echo $this->Form->input('digits3', array('div' => false, 'label' => false, 'type' => 'text','minlength'=>'4', 'MAXLENGTH' => '4','class'=>"required number")) ?>
              </li>
            </ul>
        </div>

        
    </div>
    <div id="profile_info2">
        <div id="profile_submit_Holder">
            <button type="submit" id="Submit_btn"><?php echo __('Submit'); ?></button>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>