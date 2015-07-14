<?php
//
//	file: abdev/qte/acp/main_info.php
//	author: abdev
//	begin: 02/28/2008
//	version: 0.0.5 - 08/18/2014
//	licence: GNU General Public License, version 2 (GPL-2.0)
//

// essential
namespace abdev\qte\acp;

class main_info
{
	public function module()
	{
		return array(
			'filename' => '\abdev\qte\acp\main_module',
			'title' => 'QTE_MANAGE_TITLE',
			'modes' => array(
				'manage' => array('title' => 'QTE_MANAGE_TITLE', 'auth' => 'ext_abdev/qte && acl_a_attr_manage', 'cat' => array('ACP_MESSAGES')),
			),
		);
	}
}
