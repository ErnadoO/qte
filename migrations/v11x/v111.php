<?php


namespace abdev\qte\migrations\v11x;

class v111 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v11x\v110');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.1.1', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '1.1.1')),
		);
	}
}