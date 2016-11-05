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

namespace ernadoo\qte\migrations\v200;

use phpbb\db\migration\container_aware_migration;

class convert_permissions extends container_aware_migration
{
	static public function depends_on()
	{
		return array(
			'\ernadoo\qte\migrations\v200\alpha1',
		);
	}

	public function update_data()
	{
		return array(
			array('permission.add', array('m_qte_attr_del', false)),
			array('permission.add', array('m_qte_attr_edit', false)),

			array('custom', array(array(&$this, 'convert_permissions'))),
		);
	}

	public function revert_data()
	{
		return array(

			array('custom', array(array(&$this, 'remove_permissions'))),
		);
	}

	public function convert_permissions()
	{
		$attr_permissions_array = $groups_array = array();

		$migrator_tool_permission = $this->container->get('migrator.tool.permission');

		$sql = 'SELECT * FROM ' . $this->table_prefix . 'topics_attr';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$auth_option = 'f_qte_attr_'.$row['attr_id'];
			$migrator_tool_permission->add($auth_option, false);

			$attr_permissions_array[$auth_option] = json_decode($row['attr_auths'], true);
		}

		if (!class_exists('auth_admin'))
		{
			include($this->phpbb_root_path . 'includes/acp/auth.' . $this->php_ext);
		}
		$auth_admin = new \auth_admin();

		foreach ($attr_permissions_array as $auth_option => $attr_permissions)
		{
			foreach ($attr_permissions as $attr_permission)
			{
				if (sizeof($attr_permission['forums_ids']) && sizeof($attr_permission['groups_ids']))
				{
					$auth_option_arry = array();
					$auth_option_arry[$auth_option] = ACL_YES;
					$auth_admin->acl_set('group', $attr_permission['forums_ids'], $attr_permission['groups_ids'], $auth_option_arry);
				}
			}
		}

		$sql = 'SELECT group_id FROM ' . GROUPS_TABLE .
			(!$this->config['coppa_enable'] ? " WHERE group_name <> 'REGISTERED_COPPA'" : '');
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$groups_array[] = $row['group_id'];
		}

		$sql = 'SELECT forum_id, hide_attr FROM ' . FORUMS_TABLE . '
			WHERE hide_attr <> ""';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$result_diff = array_diff($groups_array, unserialize($row['hide_attr']));

			if (sizeof($result_diff))
			{
				$auth_option_arry = array();
				$auth_option_arry['m_qte_attr_del'] = ACL_YES;
				$auth_admin->acl_set('group', $row['forum_id'], $result_diff, $auth_option_arry);
			}
		}
	}

	public function remove_permissions()
	{
		$migrator_tool_permission = $this->container->get('migrator.tool.permission');

		$sql = 'SELECT auth_option FROM ' . ACL_OPTIONS_TABLE . '
			WHERE auth_option ' . $this->db->sql_like_expression('f_qte_attr_' . $this->db->get_any_char());
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$migrator_tool_permission->remove($row['auth_option'], false);
		}
		$this->db->sql_freeresult($result);
	}
}
