<?php
/**
 *
 * @package Quick Title Edition Extension
 * @copyright (c) 2015 ABDev
 * @copyright (c) 2015 PastisD
 * @copyright (c) 2015 Geolim4 <http://geolim4.com>
 * @copyright (c) 2015 Zoddo <zoddo.ino@gmail.com>
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

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
