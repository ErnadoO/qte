<?php
//
//	file: includes/acp/info/acp_attributes.php
//	author: abdev
//	begin: 02/28/2008
//	version: 0.0.3 - 12/02/2010
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if ( !defined('IN_PHPBB') )
{
	exit;
}

class acp_attributes_info
{
	function module()
	{
		return array(
			'filename' => 'acp_attributes',
			'title' => 'QTE_MANAGE_TITLE',
			'version' => '1.0.0',
			'modes' => array(
				'manage' => array('title' => 'QTE_MANAGE_TITLE', 'auth' => 'acl_a_attr_manage', 'cat' => array('ACP_MESSAGES')),
			),
		);
	}

	// nested methods, but they will not be used ...
	function install()
	{
	}

	function uninstall()
	{
	}
}
