<?php


namespace abdev\qte\migrations\v100;

class rc4 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\abdev\qte\migrations\v100\rc3');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.0.0-rc4', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('qte_version', '1.0.0-rc4')),
		);
	}

	public function update_schema()
	{
		return array(
			'drop_columns'	   => array(
				$this->table_prefix . 'topics_attr'		=> array(
					'allowed_forums',
					'allowed_groups',
				),
			),
			'add_columns'	   => array(
				$this->table_prefix . 'topics_attr'		=> array(
                    'attr_desc'     => array('VCHAR', ''),
                    'attr_auths'    => array('MTEXT', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
            'add_columns'	   => array(
                $this->table_prefix . 'topics_attr'		=> array(
                    'allowed_forums'	=> array('TEXT', ''),
                    'allowed_groups'	=> array('TEXT', ''),
                ),
            ),
			'drop_columns'	   => array(
                $this->table_prefix . 'topics_attr'		=> array(
					'attr_desc',
					'attr_auths',
				),
			),
		);
	}
}