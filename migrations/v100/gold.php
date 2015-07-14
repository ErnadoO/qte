<?php


namespace abdev\qte\migrations\v100;

class gold extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v100\rc6');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.0.0', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('qte_version', '1.0.0')),
		);
	}
}