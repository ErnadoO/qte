<?php


namespace abdev\qte\migrations\v12x;

class v125 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v12x\v122');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.2.5', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '1.2.5')),
		);
	}
}