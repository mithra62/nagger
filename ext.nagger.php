<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - Nagger
 *
 * @package		mithra62:Nagger
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2012, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @updated		1.0
 * @filesource 	./system/expressionengine/third_party/nagger/
 */
 
 /**
 * Nagger - Extension
 *
 * Extension class
 *
 * @package 	mithra62:Nagger
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/nagger/ext.nagger.php
 */
class Nagger_ext 
{
	/**
	 * The extensions default settings
	 * @var array
	 */
	public $settings = array(
			'alert_message' => '',
			'enable_publish_form' => 'yes',
			'enable_update_member_group' => 'yes',
			'enable_file_publish_form' => 'yes',
			'enable_channel_add' => 'yes',
			'enable_channel_edit' => 'yes'
	);
	
	/**
	 * The extension name
	 * @var string
	 */
	public $name = 'Nagger';
	
	/**
	 * The extension version
	 * @var float
	 */
	public $version = '1.0';
	public $description	= 'Adds confirmation to leave certain form pages.';
	public $settings_exist	= 'y';
	public $docs_url		= '';
	
	/**
	 * The breadcrumb override
	 * @var array
	 */
	protected static $_breadcrumbs = array();	
		
	public function __construct($settings='')
	{
		$this->EE =& get_instance();
		$this->settings = (!$settings ? $this->settings : $settings);
		$this->EE->lang->loadfile('nagger');
	}
	
	public function nagger_js($menu)
	{			
		$C = $this->EE->input->get_post('C');
		$M = $this->EE->input->get_post('M');
		$menu = ($this->EE->extensions->last_call != '' ? $this->EE->extensions->last_call : $menu);
	
		$setup = FALSE;
		$vars = array();
		
		/**
		 * Hopefully, this isn't too convoluted...
		 * Just verifying we're on the right model and controller to be using the alert
		 * Only works on certain forms though because not all have ids and we don't want it on EVERY form
		 * ids were the only way to be sure of this. 
		 */
		switch($C)
		{
			case 'content_publish':
				if($this->settings['enable_publish_form'] != 'no')
				{
					$setup = TRUE;
					$vars['form_id'] = 'publishForm';
				}
			break;
					
			case 'members':
	
				switch($M)
				{
					case 'edit_member_group':
						if($this->settings['enable_update_member_group'] != 'no')
						{						
							$setup = TRUE;
							$vars['form_id'] = 'update_member_group';
						}
					break;
				}
	
				break;
					
			case 'content_files':
	
				switch($M)
				{
					case 'edit_file':
						if($this->settings['enable_file_publish_form'] != 'no')
						{						
							$setup = TRUE;
							$vars['form_id'] = 'publishForm';
						}
					break;
				}
					
			break;
					
			case 'admin_content':
	
				switch($M)
				{
					case 'channel_add':
						if($this->settings['enable_channel_add'] != 'no')
						{						
							$setup = TRUE;
							$vars['form_id'] = 'channel_edit';
						}
					break;
	
					case 'channel_edit':
						if($this->settings['enable_channel_edit'] != 'no')
						{						
							$setup = TRUE;
							$vars['form_id'] = 'channel_prefs';
						}
					break;
	
				}
	
				break;
		}
	
		//if the above is true then we load up the js and compile it to the page head
		if($setup)
		{
			$vars['alert_message'] = $this->settings['alert_message'];
			$this->settings['alert_message'] = lang($this->settings['alert_message']);
			$this->EE->javascript->output($this->EE->load->view('js', $vars, TRUE));
			$this->EE->javascript->compile();
		}
	
		return $menu;
	}
	
	public function settings()
	{
		$settings = array();
		$yes_no = array('no' => 'No', 'yes' => 'Yes');
		$settings['enable_publish_form']   = array('s', $yes_no, $this->settings['enable_publish_form']);
		$settings['enable_update_member_group'] = array('s', $yes_no, $this->settings['enable_update_member_group']);
		$settings['enable_file_publish_form'] = array('s', $yes_no, $this->settings['enable_file_publish_form']);
		$settings['enable_channel_add'] = array('s', $yes_no, $this->settings['enable_channel_add']);
		$settings['enable_channel_edit'] = array('s', $yes_no, $this->settings['enable_channel_edit']);
		$settings['alert_message'] = array('t', array('rows' => '5'), $this->settings['alert_message']);				
		
		return $settings;
	}
	
	public function activate_extension()
	{	
		$this->settings['alert_message'] = lang('default_alert_message');			
		$data = array(
				'class'     => __CLASS__,
				'method'    => 'nagger_js',
				'hook'      => 'cp_menu_array',
				'settings'  => serialize($this->settings),
				'priority'  => 1,
				'version'   => $this->version,
				'enabled'   => 'y'
		);
		
		$this->EE->db->insert('extensions', $data);		
		return TRUE;
	}
	
	public function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update(
	                'extensions',
	                array('version' => $this->version)
	    );
	}
	
	public function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	}
		
}