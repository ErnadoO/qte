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

namespace ernadoo\qte\acp;

class main_module
{
	/** @var string */
	public $u_action;

	/** @var \ernadoo\qte\qte */
	protected $qte;

	/** @var \phpbb\db\migration\tool\permission */
	protected $migrator_tool_permission;

	public function main($id, $mode)
	{
		global $phpbb_container, $db, $user, $phpbb_log, $template, $cache, $request, $table_prefix;

		$this->qte						= $phpbb_container->get('ernadoo.qte');
		$this->migrator_tool_permission	= $phpbb_container->get('migrator.tool.permission');

		$action			= $request->variable('action', '');
		$submit			= $request->is_set_post('submit');
		$attr_id		= $request->variable('id', 0);
		$attr_auth_id	= $request->variable('attr_auth_id', 0);

		$error = array();
		$clear_dest_perms = false;

		$this->tpl_name		= 'acp_attributes';
		$this->page_title	= 'QTE_MANAGE_TITLE';

		$user->add_lang_ext('ernadoo/qte', array('attributes', 'attributes_acp'));

		// Display a warning when a development version is installed or if the database is outdated
		$this->display_version_warning();

		add_form_key('acp_attributes');

		switch ($action)
		{
			case 'edit':
			case 'add':

				$attr_type = $request->variable('attr_type', 0);
				$attr_name = $request->variable('attr_name', '', true);
				$attr_img = $request->variable('attr_img', '');
				$attr_desc = $request->variable('attr_desc', '', true);
				$attr_date = $request->variable('attr_date', '');
				$attr_colour = $request->variable('attr_colour', '');
				$attr_user_colour = $request->variable('attr_user_colour', 0);

				if ($submit)
				{
					if (!check_form_key('acp_attributes'))
					{
						$error[] = $user->lang['FORM_INVALID'];
					}

					if (empty($attr_name))
					{
						$error[] = $user->lang['QTE_NAME_ERROR'];
					}

					if (isset($attr_desc[60]))
					{
						$error[] = $user->lang['QTE_DESC_ERROR'];
					}

					// fully xhtml compatibility : no capital letters
					if (!empty($attr_colour))
					{
						$attr_colour = strtolower($attr_colour);
						if (!preg_match('#^([a-f0-9]){6}#i', $attr_colour))
						{
							$error[] = $user->lang['QTE_COLOUR_ERROR'];
						}
					}

					// we don't need user colour when an image is used as attribute
					if ($attr_type && $attr_user_colour)
					{
						$attr_user_colour = false;
					}

					$attr_name_tmp = $user->lang($attr_name);
					if ($attr_user_colour)
					{
						if (strpos($attr_name_tmp, '%mod%') === false)
						{
							$error[] = $user->lang['QTE_USER_COLOUR_ERROR'];
						}
					}

					if (!empty($attr_date))
					{
						if (strpos($attr_name_tmp, '%date%') === false)
						{
							$error[] = $user->lang['QTE_DATE_ARGUMENT_ERROR'];
						}
					}
					else
					{
						if (strpos($attr_name_tmp, '%date%') !== false)
						{
							$error[] = $user->lang['QTE_DATE_FORMAT_ERROR'];
						}
					}
					unset($attr_name_tmp);

					if (!sizeof($error))
					{
						$sql_ary = array(
							'attr_type' => $attr_type,
							'attr_name' => $attr_name,
							'attr_img' => $attr_img,
							'attr_desc' => $attr_desc,
							'attr_date' => $attr_date,
							'attr_colour' => $attr_colour,
							'attr_user_colour' => $attr_user_colour,
						);

						if ($attr_id)
						{
							$sql = 'UPDATE ' . $table_prefix . 'topics_attr
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE attr_id = ' . (int) $attr_id;
							$db->sql_query($sql);

							$clear_dest_perms = true;
							$message = 'UPDATED';
						}
						else
						{
							$sql = 'SELECT MAX(right_id) AS right_id
								FROM ' . $table_prefix . 'topics_attr';
							$result = $db->sql_query($sql);
							$right_id = (int) $db->sql_fetchfield('right_id');
							$db->sql_freeresult($result);

							$sql_ary['left_id'] = ($right_id + 1);
							$sql_ary['right_id'] = ($right_id + 2);

							$sql = 'INSERT INTO ' . $table_prefix . 'topics_attr ' . $db->sql_build_array('INSERT', $sql_ary);
							$db->sql_query($sql);
							$attr_id = $db->sql_nextid();

							$this->migrator_tool_permission->add('f_qte_attr_'.$attr_id, false);

							$message = 'ADDED';
						}

						if ($attr_auth_id)
						{
							$this->_copy_permission('f_qte_attr_'.$attr_id, 'f_qte_attr_'.$attr_auth_id, $clear_dest_perms);
						}

						$cache->destroy('_attr');

						$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ATTRIBUTE_' . $message, time(), array($attr_name));

						trigger_error($user->lang['QTE_' . $message] . adm_back_link($this->u_action));
					}
				}
				else if ($attr_id)
				{
					$attr = $this->_get_attr_info($attr_id);
				}

				if ($action == 'edit')
				{
					$template->assign_vars(array(
						'L_QTE_ADD_EDIT' => $user->lang['QTE_EDIT'],
						'L_QTE_ADD_EDIT_EXPLAIN' => $user->lang['QTE_EDIT_EXPLAIN'],
					));
				}
				else
				{
					$template->assign_vars(array(
						'L_QTE_ADD_EDIT' => $user->lang['QTE_ADD'],
						'L_QTE_ADD_EDIT_EXPLAIN' => $user->lang['QTE_ADD_EXPLAIN'],
					));
				}

				$this->qte_attr_select($attr_id);

				if (sizeof($error))
				{
					$template->assign_vars(array(
						'S_ERROR' => true,
						'ERROR_MSG' => implode('<br />', $error),
					));
				}

				$attr_type_state = ((isset($attr['attr_type']) && $attr['attr_type']) || (isset($attr_type) && $attr_type));
				$attr_user_colour_state = ((isset($attr['attr_user_colour']) && $attr['attr_user_colour']) || (isset($attr_user_colour) && $attr_user_colour));

				$template->assign_vars(array(
					'S_EDIT' => true,

					'U_ACTION' => $this->u_action . '&amp;action=' . (($action == 'add') ? 'add' : 'edit&amp;id=' . (int) $attr_id),
					'U_BACK' => $this->u_action,
					'U_AJAX' => str_replace('&amp;', '&', $this->u_action),

					'L_QTE_NAME_EXPLAIN' => $user->lang('QTE_NAME_EXPLAIN', $user->data['username']),

					'ATTR_ID' => isset($attr['attr_id']) ? $attr['attr_id'] : $attr_id,
					'ATTR_NAME' => isset($attr['attr_name']) ? $attr['attr_name'] : $attr_name,
					'ATTR_IMG' => isset($attr['attr_img']) ? $attr['attr_img'] : $attr_img,
					'ATTR_DESC' => isset($attr['attr_desc']) ? $attr['attr_desc'] : $attr_desc,
					'ATTR_DATE' => isset($attr['attr_date']) ? $attr['attr_date'] : $attr_date,
					'ATTR_COLOUR' => isset($attr['attr_colour']) ? $attr['attr_colour'] : $attr_colour,

					'S_TEXT' => $attr_type_state ? true : false,
					'S_USER_COLOUR' => $attr_user_colour_state ? true : false,

				));

				return;

			break;

			case 'delete':

				if (!$attr_id)
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if (confirm_box(true))
				{
					$sql = 'SELECT topic_id, topic_attr_id
						FROM ' . TOPICS_TABLE . '
						WHERE topic_attr_id = ' . (int) $attr_id;
					$result = $db->sql_query($sql);

					$topic_id_ary = array();
					while ($row = $db->sql_fetchrow($result))
					{
						$topic_id_ary[] = (int) $row['topic_id'];
					}
					$db->sql_freeresult($result);

					if (sizeof($topic_id_ary))
					{
						$fields = array('topic_attr_id' => 0, 'topic_attr_user' => 0, 'topic_attr_time' => 0);

						$sql = 'UPDATE ' . TOPICS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $fields) . '
							WHERE ' . $db->sql_in_set('topic_id', array_map('intval', $topic_id_ary));
						$db->sql_query($sql);
					}

					$sql = 'SELECT attr_name
						FROM ' . $table_prefix . 'topics_attr
						WHERE attr_id = ' . (int) $attr_id;
					$result = $db->sql_query($sql);
					$attr_name = (string) $db->sql_fetchfield('attr_name');
					$db->sql_freeresult($result);

					$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ATTRIBUTE_REMOVED', time(), array($attr_name));

					$this->migrator_tool_permission->remove('f_qte_attr_'.$attr_id, false);

					$sql = 'DELETE FROM ' . $table_prefix . 'topics_attr
						WHERE attr_id = ' . (int) $attr_id;
					$db->sql_query($sql);

					$cache->destroy('_attr');

					if ($request->is_ajax())
					{
						$json_response = new \phpbb\json_response;
						$json_response->send(array(
							'success' => 'true',
							'MESSAGE_TITLE' => $user->lang['INFORMATION'],
							'MESSAGE_TEXT' => $user->lang['QTE_REMOVED'],
							'REFRESH_DATA' => array(
								'time'	=> 3,
							)
						));
					}
					else
					{
						trigger_error($user->lang['QTE_REMOVED'] . adm_back_link($this->u_action));
					}
				}
				else
				{
					confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'i' => $id,
						'mode' => $mode,
						'attr_id' => $attr_id,
						'action' => 'delete',
					)));
				}

			break;

			case 'move_up':
			case 'move_down':

				if (!$attr_id)
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . $table_prefix . 'topics_attr
					WHERE attr_id = ' . (int) $attr_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if (!$row)
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$move_attr_name = $this->qte_move($row, $action, 1);
				if ($move_attr_name !== false)
				{
					$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ATTRIBUTE_' . strtoupper($action), time(), array($move_attr_name));
				}

				if ($request->is_ajax())
				{
					$json_response = new \phpbb\json_response;
					$json_response->send(array('success' => true));
				}

			break;
		}

		$template->assign_vars(array('U_ACTION' => $this->u_action));

		$sql = 'SELECT topic_attr_id, COUNT(topic_id) AS total_topics
			FROM ' . TOPICS_TABLE . '
			GROUP BY topic_attr_id';
		$result = $db->sql_query($sql);
		$stats = array();
		$total_topics = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$stats[$row['topic_attr_id']] = $row['total_topics'];
			$total_topics += $row['total_topics'];
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT * FROM ' . $table_prefix . 'topics_attr ORDER BY left_id';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$attribute_name = str_replace(array('%mod%', '%date%'), array($user->lang['QTE_KEY_USERNAME'], $user->lang['QTE_KEY_DATE']), $user->lang($row['attr_name']));
			$attribute_count = isset($stats[$row['attr_id']]) ? $stats[$row['attr_id']] : 0;

			$template->assign_block_vars('row', array(
				'S_IMAGE' => $row['attr_type'] ? true : false,
				'S_COLOUR' => $row['attr_colour'] ? true : false,
				'S_DESC' => $row['attr_desc'] ? true : false,
				'S_DATE' => $row['attr_date'] ? true : false,
				'S_USER_COLOUR' => $row['attr_user_colour'] ? true : false,
				'S_CSS' => (!$row['attr_type'] && isset($user->lang[$row['attr_name']]) && empty($row['attr_colour'])) ? true : false,

				'QTE_TXT' => $attribute_name,
				'QTE_DESC' => $user->lang($row['attr_desc']),
				'QTE_IMG' => $this->qte->attr_img_key($row['attr_img'], $attribute_name),
				'QTE_COLOUR' => $row['attr_colour'],
				'QTE_DATE' => $row['attr_date'],
				'QTE_COUNT' => (int) $attribute_count,
				'QTE_PER_CENT' => empty($total_topics) ? 0 : round(intval($attribute_count) * 100 / $total_topics),

				'U_EDIT' => $this->u_action . '&amp;action=edit&amp;id=' . $row['attr_id'],
				'U_MOVE_UP' => $this->u_action . '&amp;action=move_up&amp;id=' . $row['attr_id'],
				'U_MOVE_DOWN' => $this->u_action . '&amp;action=move_down&amp;id=' . $row['attr_id'],
				'U_DELETE' => $this->u_action . '&amp;action=delete&amp;id=' . $row['attr_id'],
			));
		}
		$db->sql_freeresult($result);
	}

	protected function _get_attr_info($attr_id)
	{
		global $db, $table_prefix;

		$sql = 'SELECT * FROM ' . $table_prefix . 'topics_attr WHERE attr_id = ' . (int) $attr_id;
		$result = $db->sql_query($sql);
		$attr = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $attr;
	}

	protected function qte_move($attr_row, $action = 'move_up', $steps = 1)
	{
		global $db, $table_prefix;

		$sql = 'SELECT attr_id, attr_name, left_id, right_id
			FROM ' . $table_prefix . "topics_attr
			WHERE " . (($action == 'move_up') ? "right_id < {$attr_row['right_id']} ORDER BY right_id DESC" : "left_id > {$attr_row['left_id']} ORDER BY left_id ASC");
		$result = $db->sql_query_limit($sql, $steps);

		$target = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if (!sizeof($target))
		{
			return false;
		}

		if ($action == 'move_up')
		{
			$left_id = $target['left_id'];
			$right_id = $attr_row['right_id'];

			$diff_up = $attr_row['left_id'] - $target['left_id'];
			$diff_down = $attr_row['right_id'] + 1 - $attr_row['left_id'];

			$move_up_left = $attr_row['left_id'];
			$move_up_right = $attr_row['right_id'];
		}
		else
		{
			$left_id = $attr_row['left_id'];
			$right_id = $target['right_id'];

			$diff_up = $attr_row['right_id'] + 1 - $attr_row['left_id'];
			$diff_down = $target['right_id'] - $attr_row['right_id'];

			$move_up_left = $attr_row['right_id'] + 1;
			$move_up_right = $target['right_id'];
		}

		$sql = 'UPDATE ' . $table_prefix . "topics_attr
			SET left_id = left_id + CASE
				WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END,
			right_id = right_id + CASE
				WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
				ELSE {$diff_down}
			END
			WHERE left_id BETWEEN {$left_id} AND {$right_id}
				AND right_id BETWEEN {$left_id} AND {$right_id}";
		$db->sql_query($sql);

		return $target['attr_name'];
	}

	protected function qte_attr_select($attr_id)
	{
		global $user, $template;

		$current_time = time();

		foreach ($this->qte->getAttr() as $attr)
		{
			if ($attr['attr_id'] != $attr_id)
			{
				$attribute_name = str_replace(array('%mod%', '%date%'), array($user->data['username'], $user->format_date($current_time, $attr['attr_date'])), $user->lang($attr['attr_name']));

				$template->assign_block_vars('select_row', array(
					'QTE_ID' => $attr['attr_id'],
					'QTE_TYPE' => $attr['attr_type'],
					'QTE_NAME' => $attribute_name,
					'QTE_DESC' => $user->lang($attr['attr_desc']),
					'QTE_COLOUR' => $this->qte->attr_colour($attr['attr_name'], $attr['attr_colour']),
				));
			}
		}
	}

	protected function display_version_warning()
	{
		global $config, $user;

		$version = \ernadoo\qte\ext::VERSION;

		// Check if the database is up-to-date (we don't display warning if we are on a -dev version since versions doesn't matches)
		if ($config['qte_version'] != $version && stripos($version, '-dev') === false)
		{
			trigger_error($user->lang('QTE_MIGRATIONS_OUTDATED', $config['qte_version'], $version), E_USER_ERROR);
		}
	}

	/**
	* Permission Copy
	*
	* Copy a permission (auth) option
	*
	* @param string		$auth_option		The name of the permission (auth) option
	* @param int		$copy_from			If set, contains the id of the permission from which to copy the new one.
	* @param bool		$clear_dest_perms	True if destination permissions should be deleted
	* @return null
	*/
	private function _copy_permission($auth_option, $copy_from, $clear_dest_perms = true)
	{
		global $db, $phpbb_root_path, $phpEx;

		if (!class_exists('auth_admin'))
		{
			include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
		}
		$auth_admin = new \auth_admin();

		$old_id = $auth_admin->acl_options['id'][$copy_from];
		$new_id = $auth_admin->acl_options['id'][$auth_option];

		$tables = array(ACL_GROUPS_TABLE, ACL_ROLES_DATA_TABLE, ACL_USERS_TABLE);

		foreach ($tables as $table)
		{
			// Clear current permissions of destination attributes
			if ($clear_dest_perms)
			{
				$sql = 'DELETE FROM ' . $table . '
					WHERE auth_option_id = ' . $new_id;
				$db->sql_query($sql);
			}

			$sql = 'SELECT *
					FROM ' . $table . '
					WHERE auth_option_id = ' . $old_id;
			$result = $db->sql_query($sql);

			$sql_ary = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$row['auth_option_id'] = $new_id;
				$sql_ary[] = $row;
			}
			$db->sql_freeresult($result);

			if (!empty($sql_ary))
			{
				$db->sql_multi_insert($table, $sql_ary);
			}
		}

		$auth_admin->acl_clear_prefetch();
	}
}
