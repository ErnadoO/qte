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

namespace abdev\qte\migrations\v200;

class convert_old_modules extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\abdev\qte\migrations\v12x\v125',
		);
	}

	public function update_data()
	{
		return array(
			// We remove the old acp module
			array('if', array(
				array('module.exists', array('acp', 'ACP_MESSAGES', 'QTE_MANAGE_TITLE')),
				array('module.remove', array('acp', 'ACP_MESSAGES', 'QTE_MANAGE_TITLE')),
			)),

			array('module.add', array('acp', 'ACP_MESSAGES', array(
				'module_basename'		=> '\abdev\qte\acp\main_module',
				'modes'					=> array('manage'),
			))),
		);
	}
}
