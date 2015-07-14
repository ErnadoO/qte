<?php
//
//	file: includes/acp/acp_attributes.php
//	author: abdev
//	author: pastisd
//	begin: 05/03/2008
//	version: 0.3.3 - 12/26/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if ( !defined('IN_PHPBB') )
{
	exit;
}

class acp_attributes
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $template, $cache, $qte;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$action = request_var('action', '');
		$submit = isset($_POST['submit']) ? true : false;
		$attr_id = request_var('id', 0);
		$attr_auth_id = request_var('attr_auth_id', 0);

		$error = array();

		$this->tpl_name = 'acp_attributes';
		$this->page_title = 'QTE_MANAGE_TITLE';

		add_form_key('acp_attributes');

		switch ( $action )
		{
			case 'edit':
			case 'add':

				$attr_type = request_var('attr_type', 0);
				$attr_name = utf8_normalize_nfc(request_var('attr_name', '', true));
				$attr_img = request_var('attr_img', '');
				$attr_desc = utf8_normalize_nfc(request_var('attr_desc', '', true));
				$attr_date = request_var('attr_date', '');
				$attr_colour = request_var('attr_colour', '');
				$attr_user_colour = request_var('attr_user_colour', 0);

				// is it too complex for u ? pastisd has no limit :)
				$attr_auths = array(array('forums_ids' => array(), 'groups_ids' => array(), 'author' => false));
				if ( isset($_POST['attr_auths']) )
				{
					$attr_auths = array();
					foreach ( $_POST['attr_auths'] as $attr_auth )
					{
						$attr_auths[] = array(
							'forums_ids' => isset($attr_auth['forums_ids']) ? $attr_auth['forums_ids'] : array(),
							'groups_ids' => isset($attr_auth['groups_ids']) ? $attr_auth['groups_ids'] : array(),
							'author' => isset($attr_auth['author']) ? true : false,
						);
					}
					unset($attr_auth);
				}

				if ( $submit )
				{
					if ( !check_form_key('acp_attributes') )
					{
						$error[] = $user->lang['FORM_INVALID'];
					}

					if ( empty($attr_name) )
					{
						$error[] = $user->lang['QTE_NAME_ERROR'];
					}

					if ( isset($attr_desc[60]) )
					{
						$error[] = $user->lang['QTE_DESC_ERROR'];
					}

					// fully xhtml compatibility : no capital letters
					if ( !empty($attr_colour) )
					{
						$attr_colour = strtolower($attr_colour);
						if ( !preg_match('#^([a-f0-9]){6}#i', $attr_colour) )
						{
							$error[] = $user->lang['QTE_COLOUR_ERROR'];
						}
					}

					// we don't need user colour when an image is used as attribute
					if ( $attr_type && $attr_user_colour )
					{
						$attr_user_colour = false;
					}

					$attr_name_tmp = $qte->attr_lng_key($attr_name);
					if ( $attr_user_colour )
					{
						if ( strpos($attr_name_tmp, '%mod%') === false )
						{
							$error[] = $user->lang['QTE_USER_COLOUR_ERROR'];
						}
					}

					if ( !empty($attr_date) )
					{
						if ( strpos($attr_name_tmp, '%date%') === false )
						{
							$error[] = $user->lang['QTE_DATE_ARGUMENT_ERROR'];
						}
					}
					else
					{
						if ( strpos($attr_name_tmp, '%date%') !== false )
						{
							$error[] = $user->lang['QTE_DATE_FORMAT_ERROR'];
						}
					}
					unset($attr_name_tmp);

					if ( !sizeof($error) )
					{
						$sql_ary = array(
							'attr_type' => $attr_type,
							'attr_name' => $attr_name,
							'attr_img' => $attr_img,
							'attr_desc' => $attr_desc,
							'attr_date' => $attr_date,
							'attr_colour' => $attr_colour,
							'attr_user_colour' => $attr_user_colour,
							'attr_auths' => sizeof($attr_auths) ? json_encode($attr_auths) : '',
						);

						if ( $attr_id )
						{
							$sql = 'UPDATE ' . TOPICS_ATTR_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE attr_id = ' . (int) $attr_id;
							$db->sql_query($sql);

							$message = 'UPDATED';
						}
						else
						{
							$sql = 'SELECT MAX(right_id) AS right_id
								FROM ' . TOPICS_ATTR_TABLE;
							$result = $db->sql_query($sql);
							$right_id = (int) $db->sql_fetchfield('right_id');
							$db->sql_freeresult($result);

							$sql_ary['left_id'] = ($right_id + 1);
							$sql_ary['right_id'] = ($right_id + 2);

							$sql = 'INSERT INTO ' . TOPICS_ATTR_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
							$db->sql_query($sql);

							$message = 'ADDED';
						}

						$cache->destroy('_attr');

						add_log('admin', 'LOG_ATTRIBUTE_' . $message, $attr_name);

						trigger_error($user->lang['QTE_' . $message] . adm_back_link($this->u_action));
					}
				}
				else if ( $attr_id )
				{
					$set_permissions = $this->set_auths($attr_id);
					$attr = $set_permissions['attr'];
					$attr_auths = $set_permissions['attr_auths'];
				}

				if ( $action == 'edit' )
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

					$attr_auths = array(array(
						'forums_ids' => array(),
						'groups_ids' => array(),
						'author' => false,
					));
				}

				$this->qte_attr_select($attr_id);
				$this->add_auths($attr_auths);

				if ( sizeof($error) )
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

					'L_QTE_NAME_EXPLAIN' => sprintf($user->lang['QTE_NAME_EXPLAIN'], $user->data['username']),

					'ATTR_ID' => isset($attr['attr_id']) ? $attr['attr_id'] : $attr_id,
					'ATTR_NAME' => isset($attr['attr_name']) ? $attr['attr_name'] : $attr_name,
					'ATTR_IMG' => isset($attr['attr_img']) ? $attr['attr_img'] : $attr_img,
					'ATTR_DESC' => isset($attr['attr_desc']) ? $attr['attr_desc'] : $attr_desc,
					'ATTR_DATE' => isset($attr['attr_date']) ? $attr['attr_date'] : $attr_date,
					'ATTR_COLOUR' => isset($attr['attr_colour']) ? $attr['attr_colour'] : $attr_colour,

					'S_TEXT' => $attr_type_state ? true : false,
					'S_USER_COLOUR' => $attr_user_colour_state ? true : false,

					'ICON_ATTR_AUTH_ADD' => '<img src="' . $phpbb_admin_path . 'images/qte_auth_add.gif" alt="' . $user->lang['QTE_AUTH_ADD'] . '" title="' . $user->lang['QTE_AUTH_ADD'] . '" />',
					'ICON_ATTR_AUTH_REMOVE' => '<img src="' . $phpbb_admin_path . 'images/qte_auth_remove.gif" alt="' . $user->lang['QTE_AUTH_REMOVE'] . '" title="' . $user->lang['QTE_AUTH_REMOVE'] . '" />',
				));

				return;

			break;

			case 'set_permissions':

				$set_permissions = $this->set_auths($attr_auth_id);
				$this->add_auths($set_permissions['attr_auths']);

				$template->assign_vars(array(
					'ICON_ATTR_AUTH_ADD' => '<img src="' . $phpbb_admin_path . 'images/qte_auth_add.gif" alt="' . $user->lang['QTE_AUTH_ADD'] . '" title="' . $user->lang['QTE_AUTH_ADD'] . '" />',
					'ICON_ATTR_AUTH_REMOVE' => '<img src="' . $phpbb_admin_path . 'images/qte_auth_remove.gif" alt="' . $user->lang['QTE_AUTH_REMOVE'] . '" title="' . $user->lang['QTE_AUTH_REMOVE'] . '" />',
				));
				$this->tpl_name = 'acp_attributes_auths';

			break;

			case 'delete':

				if ( !$attr_id )
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				if ( confirm_box(true) )
				{
					$sql = 'SELECT topic_id, topic_attr_id
						FROM ' . TOPICS_TABLE . '
						WHERE topic_attr_id = ' . (int) $attr_id;
					$result = $db->sql_query($sql);

					$topic_id_ary = array();
					while ( $row = $db->sql_fetchrow($result) )
					{
						$topic_id_ary[] = (int) $row['topic_id'];
					}
					$db->sql_freeresult($result);

					if ( sizeof($topic_id_ary) )
					{
						$fields = array('topic_attr_id' => 0, 'topic_attr_user' => 0, 'topic_attr_time' => 0);

						$sql = 'UPDATE ' . TOPICS_TABLE . '
							SET ' . $db->sql_build_array('UPDATE', $fields) . '
							WHERE ' . $db->sql_in_set('topic_id', array_map('intval', $topic_id_ary));
						$db->sql_query($sql);
					}

					$sql = 'SELECT attr_name
						FROM ' . TOPICS_ATTR_TABLE . '
						WHERE attr_id = ' . (int) $attr_id;
					$result = $db->sql_query($sql);
					$attr_name = (string) $db->sql_fetchfield('attr_name');
					$db->sql_freeresult($result);

					add_log('admin', 'LOG_ATTRIBUTE_REMOVED', $attr_name);

					$sql = 'DELETE FROM ' . TOPICS_ATTR_TABLE . '
						WHERE attr_id = ' . (int) $attr_id;
					$db->sql_query($sql);

					$cache->destroy('_attr');

					trigger_error($user->lang['QTE_REMOVED'] . adm_back_link($this->u_action));
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

				if ( !$attr_id )
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT *
					FROM ' . TOPICS_ATTR_TABLE . '
					WHERE attr_id = ' . (int) $attr_id;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if ( !$row )
				{
					trigger_error($user->lang['QTE_MUST_SELECT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$move_attr_name = $this->qte_move($row, $action, 1);
				if ( $move_attr_name !== false )
				{
					add_log('admin', 'LOG_ATTRIBUTE_' . strtoupper($action), $row['attr_name'], $move_attr_name);
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
		while ( $row = $db->sql_fetchrow($result) )
		{
			$stats[$row['topic_attr_id']] = $row['total_topics'];
			$total_topics += $row['total_topics'];
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT * FROM ' . TOPICS_ATTR_TABLE . ' ORDER BY left_id';
		$result = $db->sql_query($sql);

		while ( $row = $db->sql_fetchrow($result) )
		{
			$attribute_name = str_replace(array('%mod%', '%date%'), array($user->lang['QTE_KEY_USERNAME'], $user->lang['QTE_KEY_DATE']), $qte->attr_lng_key($row['attr_name']));
			$attribute_count = isset($stats[$row['attr_id']]) ? $stats[$row['attr_id']] : 0;

			$template->assign_block_vars('row', array(
				'S_IMAGE' => $row['attr_type'] ? true : false,
				'S_COLOUR' => $row['attr_colour'] ? true : false,
				'S_DESC' => $row['attr_desc'] ? true : false,
				'S_DATE' => $row['attr_date'] ? true : false,
				'S_USER_COLOUR' => $row['attr_user_colour'] ? true : false,
				'S_CSS' => (!$row['attr_type'] && isset($user->lang[$row['attr_name']]) && empty($row['attr_colour'])) ? true : false,

				'QTE_TXT' => $attribute_name,
				'QTE_DESC' => $qte->attr_lng_key($row['attr_desc']),
				'QTE_IMG' => $qte->attr_img_key($row['attr_img'], $attribute_name),
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

	function set_auths($attr_id)
	{
		global $db;

		$sql = 'SELECT * FROM ' . TOPICS_ATTR_TABLE . ' WHERE attr_id = ' . (int) $attr_id;
		$result = $db->sql_query($sql);
		$attr = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ( !$attr['attr_auths'] )
		{
			$attr_auths = array(array(
				'forums_ids' => array(),
				'groups_ids' => array(),
				'author' => false,
			));
		}
		else
		{
			$attr_auths = json_decode($attr['attr_auths'], true);
		}

		return array('attr' => $attr, 'attr_auths' => $attr_auths);
 	}

	function add_auths($attr_auths)
	{
		global $template, $qte;

		$offset = 0;
		foreach ( $attr_auths as $attr_auth )
		{
			$template->assign_block_vars('auths_row', array(
				'OFFSET' => $offset,

				'S_FORUM_ID_OPTIONS' => $this->qte_forum_select($attr_auth['forums_ids']),
				'S_GROUP_ID_OPTIONS' => $qte->qte_group_select($attr_auth['groups_ids'], false, false),

				'S_AUTHOR' => $attr_auth['author'],
			));
			$offset++;
		}
		unset($attr_auth);
	}

	function qte_move($attr_row, $action = 'move_up', $steps = 1)
	{
		global $db;

		$sql = 'SELECT attr_id, attr_name, left_id, right_id
			FROM ' . TOPICS_ATTR_TABLE . "
			WHERE " . (($action == 'move_up') ? "right_id < {$attr_row['right_id']} ORDER BY right_id DESC" : "left_id > {$attr_row['left_id']} ORDER BY left_id ASC");
		$result = $db->sql_query_limit($sql, $steps);

		$target = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$target = $row;
		}
		$db->sql_freeresult($result);

		if ( !sizeof($target) )
		{
			return false;
		}

		if ( $action == 'move_up' )
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

		$sql = 'UPDATE ' . TOPICS_ATTR_TABLE . "
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

	// borrowed from "includes/acp/acp_attachments.php" file
	function qte_forum_select($forum_ids)
	{
		global $db, $auth;

		$s_forum_id_options = '';

		$sql = 'SELECT forum_id, forum_name, parent_id, forum_type, left_id, right_id
			FROM ' . FORUMS_TABLE . '
			ORDER BY left_id ASC';
		$result = $db->sql_query($sql, 600);

		$right = $cat_right = $padding_inc = 0;
		$padding = $forum_list = $holding = '';
		$padding_store = array('0' => '');

		while ( $row = $db->sql_fetchrow($result) )
		{
			if ( ($row['forum_type'] == FORUM_CAT) && ($row['left_id'] + 1 == $row['right_id']))
			{
				continue;
			}

			if ( !$auth->acl_get('f_list', $row['forum_id']) )
			{
				continue;
			}

			if ( $row['left_id'] < $right )
			{
				$padding .= '&nbsp; &nbsp;';
				$padding_store[$row['parent_id']] = $padding;
			}
			else if ( $row['left_id'] > $right + 1 )
			{
				$padding = empty($padding_store[$row['parent_id']]) ? '' : $padding_store[$row['parent_id']];
			}

			$right = $row['right_id'];

			$selected = in_array($row['forum_id'], $forum_ids) ? ' selected="selected"' : '';

			if ( $row['left_id'] > $cat_right )
			{
				$s_forum_id_options .= $holding;
				$holding = '';
			}

			if ( $row['right_id'] - $row['left_id'] > 1 )
			{
				$cat_right = max($cat_right, $row['right_id']);

				$holding .= '<option value="' . $row['forum_id'] . '"' . (($row['forum_type'] == FORUM_POST) ? ' class="sep"' : ' disabled="disabled"') . $selected . '>' . $padding . $row['forum_name'] . '</option>';
			}
			else
			{
				$s_forum_id_options .= $holding . '<option value="' . $row['forum_id'] . '"' . (($row['forum_type'] == FORUM_POST) ? ' class="sep"' : ' disabled="disabled"') . $selected . '>' . $padding . $row['forum_name'] . '</option>';
				$holding = '';
			}
		}

		if ( $holding )
		{
			$s_forum_id_options .= $holding;
		}

		$db->sql_freeresult($result);
		unset($padding_store);

		return $s_forum_id_options;
	}

	function qte_attr_select($attr_id)
	{
		global $db, $user, $auth, $template, $qte;
		global $phpbb_root_path, $phpEx;

		$user->add_lang('mods/attributes');

		$current_time = time();

		foreach ( $qte->attr as $attr )
		{
			if ( $attr['attr_id'] != $attr_id )
			{
				$attribute_name = str_replace(array('%mod%', '%date%'), array($user->data['username'], $user->format_date($current_time, $attr['attr_date'])), $qte->attr_lng_key($attr['attr_name']));

				$template->assign_block_vars('select_row', array(
					'QTE_ID' => $attr['attr_id'],
					'QTE_TYPE' => $attr['attr_type'],
					'QTE_NAME' => $attribute_name,
					'QTE_DESC' => $qte->attr_lng_key($attr['attr_desc']),
					'QTE_COLOUR' => $qte->attr_colour($attr['attr_name'], $attr['attr_colour']),
				));
			}
		}
		unset($attr, $attribute_name);
	}
}
