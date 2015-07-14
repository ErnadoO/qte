<?php


namespace abdev\qte\migrations\v11x;

class v110 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v100\gold');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.1.0', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '1.1.0')),
		);
	}

	public function update_schema()
	{
		return array(
			'update_columns'	   => array(
				$this->table_prefix . 'topics_attr'		=> array(
					array('attr_desc', array('VCHAR:60', '')),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'update_columns'	   => array(
				$this->table_prefix . 'topics_attr'		=> array(
					array('attr_desc', array('VCHAR', '')),
				),
			),
		);
	}
}