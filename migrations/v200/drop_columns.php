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

namespace ernadoo\qte\migrations\v200;

class drop_columns extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\ernadoo\qte\migrations\v200\convert_permissions',
		);
	}

	public function update_schema()
	{
		return array(
			'drop_columns'    => array(
				$this->table_prefix . 'topics_attr'	=> array(
					'attr_auths',
				),
				$this->table_prefix . 'forums'	=> array(
					'hide_attr',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'add_columns'    => array(
				$this->table_prefix . 'topics_attr' => array(
					'attr_auths'    => array('MTEXT', ''),
				),
				$this->table_prefix . 'forums' => array(
					'hide_attr'    => array('TEXT', ''),
				),
			),
		);
	}
}
