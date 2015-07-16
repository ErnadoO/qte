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

namespace abdev\qte\migrations\v100;

class rc6 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v100\rc5');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.0.0-rc6', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '1.0.0-rc6')),
			array('config.remove', array('qte_force_users')),
		);
	}

	public function update_schema()
	{
		return array(
			'add_columns'	   => array(
				FORUMS_TABLE		=> array(
					'force_attr'	=> array('BOOL', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	   => array(
				FORUMS_TABLE		=> array(
					'force_attr',
				),
			),
		);
	}
}