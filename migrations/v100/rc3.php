<?php


namespace abdev\qte\migrations\v100;

class rc3 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function effectively_installed()
	{
		return isset($this->config['qte_version']) && phpbb_version_compare($this->config['qte_version'], '1.0.0-rc3', '>=');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('qte_version', '1.0.0-rc3')),
			array('config.add', array('qte_force_users', false)),

			array('permission.add', array('a_attr_manage')),
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_attr_manage')),
			array('permission.permission_set', array('ROLE_ADMIN_FORUM', 'a_attr_manage')),
			array('permission.permission_set', array('ROLE_ADMIN_STANDARD', 'a_attr_manage')),
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'topics_attr'			=> array(
					'COLUMNS'		  => array(
						'attr_id'		   => array('UINT', NULL, 'auto_increment'),
						'attr_type'		 => array('BOOL', 0),
						'attr_name'		 => array('VCHAR', ''),
						'left_id'		   => array('UINT', 0),
						'right_id'		  => array('UINT', 0),
						'attr_img'		  => array('VCHAR', ''),
						'attr_date'		 => array('VCHAR:30', ''),
						'attr_colour'	   => array('VCHAR:6', ''),
						'attr_user_colour'  => array('BOOL', 0),
						'allowed_forums'	=> array('TEXT', ''),
						'allowed_groups'	=> array('TEXT', ''),
					),
					'PRIMARY_KEY'		=> 'attr_id',
				),
			),
			'add_columns'	   => array(
				TOPICS_TABLE		   => array(
					'topic_attr_id'     => array('UINT', 0),
					'topic_attr_user'   => array('UINT', 0),
					'topic_attr_time'   => array('TIMESTAMP', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'topics_attr',
			),
			'drop_columns'	   => array(
				TOPICS_TABLE		   => array(
					'topic_attr_id',
					'topic_attr_user',
					'topic_attr_time',
				),
			),
		);
	}
}