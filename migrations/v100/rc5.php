<?php


namespace abdev\qte\migrations\v100;

class rc5 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v100\rc4');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.0.0-rc5', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('qte_version', '1.0.0-rc5')),
		);
	}
}