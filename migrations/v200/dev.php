<?php


namespace abdev\qte\migrations\v200;

class dev extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\abdev\qte\migrations\v12x\v125',
			'\phpbb\db\migration\data\v310\gold',
			'\abdev\qte\migrations\v200\convert_old_modules',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '2.0.0-dev')),
		);
	}
}