<?php

include_lan(e_PLUGIN . HELPDESK_FOLDER . "/languages/admin/" . e_LANGUAGE . "_helpdesk_admin.php");

class helpdesk_adminArea extends e_admin_dispatcher
{
	protected $modes = array(

		'desk'	=> array(
			'controller' 	=> 'hdu_helpdesk_ui',
			'path' 			=> null,
			'ui' 			=> 'hdu_helpdesk_form_ui',
			'uipath' 		=> null
		),
		'cat'	=> array(
			'controller' 	=> 'hdu_categories_ui',
			'path' 			=> null,
			'ui' 			=> 'hdu_categories_form_ui',
			'uipath' 		=> null
		),

	);


	protected $adminMenu = array(

		'desk/list'			=> array('caption' => LAN_MANAGE, 'perm' => 'P'),
		'desk/create'		=> array('caption' => LAN_CREATE, 'perm' => 'P'),

		'cat/list'			=> array('caption' => LAN_MANAGE, 'perm' => 'P'),
		'cat/create'		=> array('caption' => LAN_CREATE, 'perm' => 'P'),

	);

	protected $adminMenuAliases = array(
		'desk/edit'	=> 'desk/list'
	);

	protected $menuTitle = 'Help Desk';

	/**
	 * Generic Admin Menu Generator
	 * @return string
	 */
	public function renderMenu()
	{

		include_once(e_PLUGIN . HELPDESK_FOLDER . "/admin/admin_menu.php");
	 
	}
}
