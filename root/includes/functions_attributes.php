<?php
//
//	file: includes/functions_attributes.php
//	author: abdev
//	author: pastisd
//	begin: 05/04/2008
//	version: 0.3.13 - 12/26/2013
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// ignore
if ( !defined('IN_PHPBB') )
{
	exit;
}

class qte
{
	var $attr = array();
	var $name = array();

	// constructor
	function qte()
	{
		global $db, $cache;

		if ( ($this->attr = $cache->get('_attr')) === false )
		{
			$sql = 'SELECT *
				FROM ' . TOPICS_ATTR_TABLE . '
				ORDER BY left_id ASC';
			$result = $db->sql_query($sql);

			$this->attr = array();
			while ( $row = $db->sql_fetchrow($result) )
			{
				$this->attr[$row['attr_id']] = array(
					'attr_id' => (int) $row['attr_id'],
					'attr_type' => (bool) $row['attr_type'],
					'attr_name' => $row['attr_name'],
					'attr_desc' => $row['attr_desc'],
					'attr_img' => $row['attr_img'],
					'attr_colour' => $row['attr_colour'],
					'attr_date' => $row['attr_date'],
					'attr_user_colour' => (bool) $row['attr_user_colour'],
					'attr_auths' => $row['attr_auths'],
				);
			}
			$db->sql_freeresult();

			$cache->put('_attr', $this->attr);
		}
	}

	function get_users_by_topic_id($topic_list)
	{
		global $db;

		$this->name = array();
		if ( !empty($topic_list) )
		{
			$sql = 'SELECT u.user_id, u.username, u.user_colour
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . TOPICS_TABLE . ' t ON (u.user_id = t.topic_attr_user)
				WHERE ' . $db->sql_in_set('t.topic_id', array_map('intval', $topic_list)) . '
					AND t.topic_attr_user <> ' . ANONYMOUS;
			$result = $db->sql_query($sql);

			while ( $row = $db->sql_fetchrow($result) )
			{
				$this->name[$row['user_id']] = array(
					'user_id' => (int) $row['user_id'],
					'username' => $row['username'],
					'user_colour' => $row['user_colour'],
				);
			}
			$db->sql_freeresult();
		}
	}

	function get_users_by_user_id($user_id)
	{
		global $db;

		$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $db->sql_query($sql);

		$this->name = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$this->name[$row['user_id']] = array(
				'user_id' => (int) $row['user_id'],
				'username' => $row['username'],
				'user_colour' => $row['user_colour'],
			);
		}
		$db->sql_freeresult();
	}

	function attr_select($forum_id = 0, $author_id = 0, $attribute_id = 0, $hide_attr)
	{
		global $db, $user, $auth, $template;
		global $phpbb_root_path, $phpEx;

		// include that file !
		if ( !function_exists('group_memberships') )
		{
			include $phpbb_root_path . 'includes/functions_user.' . $phpEx;
		}

		// load language
		$user->add_lang('mods/attributes');

		// get groups membership !
		$user_membership = group_memberships(false, $user->data['user_id']);

		$show_remove = true;
		$user_groups = array();
		if ( !empty($user_membership) )
		{
			foreach ( $user_membership as $row )
			{
				$row['group_id'] = (int) $row['group_id'];
				$user_groups[$row['group_id']] = $row['group_id'];
			}
		}
		unset($user_membership);

		// get current time once !
		$current_time = time();

		$show_select = false;

		$attributes = array();
		foreach ( $this->attr as $attr )
		{
			if ( empty($attr['attr_auths']) )
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

			foreach ( $attr_auths as $attr_auth )
			{
				$forum_ids = $attr_auth['forums_ids'];
				$group_ids = $attr_auth['groups_ids'];

				if ( is_array($forum_ids) && in_array($forum_id, $forum_ids) )
				{
					if ( is_array($group_ids) && array_intersect($group_ids, $user_groups) || ($attr_auth['author'] && ($author_id == $user->data['user_id'])) )
					{
						// show the selector !
						$show_select = true;

						$groups_removed = array_intersect($user_groups, $hide_attr);
						if ( !empty($hide_attr) && (count($groups_removed) >= count($user_groups)) )
						{
							$show_remove = false;
						}

						if ( !isset($attributes[$attr['attr_id']]) )
						{
							// parse the attribute name
							$attribute_name = str_replace(array('%mod%', '%date%'), array($user->data['username'], $user->format_date($current_time, $attr['attr_date'])), $this->attr_lng_key($attr['attr_name']));

							$attributes[$attr['attr_id']] = array(
								'type' => $attr['attr_type'],
								'name' => $attribute_name,
								'desc' => $this->attr_lng_key($attr['attr_desc']),
								'colour' => $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

								'select' => (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)),

								's_desc' => !empty($attr['attr_desc']),
							);
						}
					}
				}
			}
			unset($attr_auth);
		}
		unset($attr);

		foreach ( $attributes as $attr_id => $attr_row )
		{
			$template->assign_block_vars('row', array(
				'QTE_ID' => $attr_id,
				'QTE_TYPE' => $attr_row['type'],
				'QTE_NAME' => $attr_row['name'],
				'QTE_DESC' => $attr_row['desc'],
				'QTE_COLOUR' => $attr_row['colour'],

				'IS_SELECTED' => $attr_row['select'],

				'S_QTE_DESC' => !empty($attr_row['s_desc']) ? true : false,
			));
		}
		unset($attr_id, $attr_row);

		if ( $show_select )
		{
			$template->assign_vars(array(
				'S_QTE_SELECT' => true,
				'S_QTE_REMOVE' => $show_remove,
				'S_QTE_EMPTY' => (empty($attribute_id) || ($attribute_id == -1) || ($attribute_id == -2)),
				'S_QTE_SELECTED' => ($show_remove && ($attribute_id == -1)),

				'L_QTE_SELECT' => $user->lang['QTE_ATTRIBUTE_' . (!empty($attribute_id) ? ($show_remove ? 'REMOVE' : 'RESTRICT') : 'ADD')],
			));
		}
	}

	function attr_search()
	{
		global $db, $user, $auth, $template;
		global $phpbb_root_path, $phpEx;

		// load language
		$user->add_lang(array('mods/attributes', 'mods/info_acp_attributes'));

		// get current time once !
		$current_time = time();

		$show_select = false;

		$attributes = array();
		foreach ( $this->attr as $attr )
		{
			if ( empty($attr['attr_auths']) )
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

			foreach ( $attr_auths as $attr_auth )
			{
				// show the selector !
				$show_select = true;

				if ( !isset($attributes[$attr['attr_id']]) )
				{
					// parse the attribute name
					$attribute_name = str_replace(array('%mod%', '%date%'), array($user->lang['QTE_KEY_USERNAME'], $user->lang['QTE_KEY_DATE']), $this->attr_lng_key($attr['attr_name']));

					$attributes[$attr['attr_id']] = array(
						'type' => $attr['attr_type'],
						'name' => $attribute_name,
						'desc' => $this->attr_lng_key($attr['attr_desc']),
						'colour' => $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

						's_desc' => !empty($attr['attr_desc']) ? true : false,
					);
				}
			}
			unset($attr_auth);
		}
		unset($attr);

		foreach ( $attributes as $attr_id => $attr_row )
		{
			$template->assign_block_vars('row', array(
				'QTE_ID' => $attr_id,
				'QTE_TYPE' => $attr_row['type'],
				'QTE_NAME' => $attr_row['name'],
				'QTE_DESC' => $attr_row['desc'],
				'QTE_COLOUR' => $attr_row['colour'],

				'S_QTE_DESC' => $attr_row['s_desc'],
			));
		}
		unset($attr_id, $attr_row);

		if ( $show_select )
		{
			$template->assign_var('S_QTE_SELECT', true);
		}
	}

	function attr_sort($forum_id = 0, $attribute_id = 0)
	{
		global $db, $user, $auth, $template;
		global $phpbb_root_path, $phpEx;

		// load language
		$user->add_lang(array('mods/attributes', 'mods/info_acp_attributes'));

		// get current time once !
		$current_time = time();

		$show_select = false;

		$attributes = array();
		foreach ( $this->attr as $attr )
		{
			if ( empty($attr['attr_auths']) )
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

			foreach ( $attr_auths as $attr_auth )
			{
				$forum_ids = $attr_auth['forums_ids'];

				if ( is_array($forum_ids) && in_array($forum_id, $forum_ids) )
				{
					// show the selector !
					$show_select = true;

					if ( !isset($attributes[$attr['attr_id']]) )
					{
						// parse the attribute name
						$attribute_name = str_replace(array('%mod%', '%date%'), array($user->lang['QTE_KEY_USERNAME'], $user->lang['QTE_KEY_DATE']), $this->attr_lng_key($attr['attr_name']));

						$attributes[$attr['attr_id']] = array(
							'type' => $attr['attr_type'],
							'name' => $attribute_name,
							'desc' => $this->attr_lng_key($attr['attr_desc']),
							'colour' => $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

							'select' => (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)) ? true : false,

							's_desc' => !empty($attr['attr_desc']) ? true : false,
						);
					}
				}
			}
			unset($attr_auth);
		}
		unset($attr);

		foreach ( $attributes as $attr_id => $attr_row )
		{
			$template->assign_block_vars('row', array(
				'QTE_ID' => $attr_id,
				'QTE_TYPE' => $attr_row['type'],
				'QTE_NAME' => $attr_row['name'],
				'QTE_DESC' => $attr_row['desc'],
				'QTE_COLOUR' => $attr_row['colour'],

				'IS_SELECTED' => $attr_row['select'],

				'S_QTE_DESC' => $attr_row['s_desc'],
			));
		}
		unset($attr_id, $attr_row);

		if ( $show_select )
		{
			$template->assign_var('S_QTE_SELECT', true);
		}
	}

	function attr_default($forum_id = 0, $attribute_id = 0)
	{
		global $db, $user, $auth, $template;
		global $phpbb_root_path, $phpEx;

		// load language
		$user->add_lang(array('mods/attributes', 'mods/info_acp_attributes'));

		// get current time once !
		$current_time = time();

		$show_select = false;

		$attributes = array();
		foreach ( $this->attr as $attr )
		{
			if ( empty($attr['attr_auths']) )
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

			foreach ( $attr_auths as $attr_auth )
			{
				$forum_ids = $attr_auth['forums_ids'];

				if ( is_array($forum_ids) && in_array($forum_id, $forum_ids) )
				{
					// show the selector !
					$show_select = true;

					if ( !isset($attributes[$attr['attr_id']]) )
					{
						// parse the attribute name
						$attribute_name = str_replace(array('%mod%', '%date%'), array($user->lang['QTE_KEY_USERNAME'], $user->lang['QTE_KEY_DATE']), $this->attr_lng_key($attr['attr_name']));

						$attributes[$attr['attr_id']] = array(
							'type' => $attr['attr_type'],
							'name' => $attribute_name,
							'desc' => $this->attr_lng_key($attr['attr_desc']),
							'colour' => $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

							'select' => (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)) ? true : false,

							's_desc' => !empty($attr['attr_desc']) ? true : false,
						);
					}
				}
			}
			unset($attr_auth);
		}
		unset($attr);

		foreach ( $attributes as $attr_id => $attr_row )
		{
			$template->assign_block_vars('row', array(
				'QTE_ID' => $attr_id,
				'QTE_TYPE' => $attr_row['type'],
				'QTE_NAME' => $attr_row['name'],
				'QTE_DESC' => $attr_row['desc'],
				'QTE_COLOUR' => $attr_row['colour'],

				'IS_SELECTED' => $attr_row['select'],

				'S_QTE_DESC' => $attr_row['s_desc'],
			));
		}
		unset($attr_id, $attr_row);

		if ( $show_select )
		{
			$template->assign_var('S_QTE_SELECT', true);
		}
	}

	function attr_display($attribute_id = 0, $user_id = 0, $timestamp = 0)
	{
		if ( empty($attribute_id) || empty($user_id) || empty($timestamp) )
		{
			return false;
		}

		global $user;

		if ( isset($this->attr[$attribute_id]) )
		{
			$attribute_colour = $this->attr_colour($this->attr[$attribute_id]['attr_name'], $this->attr[$attribute_id]['attr_colour']);

			if ( isset($this->name[$user_id]['user_id']) )
			{
				$attribute_username = get_username_string(($this->attr[$attribute_id]['attr_user_colour'] ? 'no_profile' : 'username'), $this->name[$user_id]['user_id'], $this->name[$user_id]['username'], $this->name[$user_id]['user_colour']);
			}
			else
			{
				$attribute_username = $user->lang['GUEST'];
			}

			$attribute_date = $user->format_date($timestamp, $this->attr[$attribute_id]['attr_date']);

			$attribute_name = str_replace(array('%mod%', '%date%'), array($attribute_username, $attribute_date), $this->attr_lng_key($this->attr[$attribute_id]['attr_name']));

			return !$this->attr[$attribute_id]['attr_type'] ? '<span' . $attribute_colour . '>' . $attribute_name . '</span>' : $this->attr_img_key($this->attr[$attribute_id]['attr_img'], $attribute_name);
		}
	}

	function attr_title($attribute_id = 0, $user_id = 0, $timestamp = 0)
	{
		if ( empty($attribute_id) || empty($user_id) || empty($timestamp) )
		{
			return false;
		}

		global $user;

		if ( isset($this->attr[$attribute_id]) )
		{
			if ( isset($this->name[$user_id]['user_id']) )
			{
				$attribute_username = get_username_string('username', $this->name[$user_id]['user_id'], $this->name[$user_id]['username'], $this->name[$user_id]['user_colour']);
			}
			else
			{
				$attribute_username = $user->lang['GUEST'];
			}

			$attribute_date = $user->format_date($timestamp, $this->attr[$attribute_id]['attr_date']);

			$attribute_name = str_replace(array('%mod%', '%date%'), array($attribute_username, $attribute_date), $this->attr_lng_key($this->attr[$attribute_id]['attr_name']));

			return $attribute_name;
		}
	}

	function attr_apply($attribute_id = 0, $topic_id = 0, $forum_id = 0, $topic_attribute = '')
	{
		global $db, $user;
		global $phpbb_root_path, $phpEx;

		if ( empty($topic_id) || empty($forum_id) || empty($attribute_id) )
		{
			return;
		}

		// time !
		$current_time = time();

		if ( $attribute_id == -1 )
		{
			$fields = array(
				'topic_attr_id' => 0,
				'topic_attr_user' => 0,
				'topic_attr_time' => 0,
			);
		}
		else
		{
			$fields = array(
				'topic_attr_id' => $attribute_id,
				'topic_attr_user' => $user->data['user_id'],
				'topic_attr_time' => $current_time,
			);
		}

		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $fields) . '
			WHERE topic_id = ' . (int) $topic_id;
		$db->sql_query($sql);

		$sql = 'SELECT topic_id
			FROM ' . TOPICS_TABLE . '
			WHERE topic_moved_id = ' . (int) $topic_id;
		$result = $db->sql_query($sql);
		$shadow_topic_id = (int) $db->sql_fetchfield('topic_id');
		$db->sql_freeresult($result);

		if ( !empty($shadow_topic_id) )
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $fields) . '
				WHERE topic_id = ' . $shadow_topic_id;
			$db->sql_query($sql);
		}

		$meta_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$forum_id&amp;t=$topic_id");
		meta_refresh(3, $meta_url);

		// load language
		$user->add_lang(array('posting', 'mods/attributes'));

		$message = $user->lang['QTE_ATTRIBUTE_' . ($attribute_id == -1 ? 'REMOVED' : (empty($topic_attribute) ? 'ADDED' : 'UPDATED'))] . '<br /><br />' . sprintf($user->lang['VIEW_MESSAGE'], '<a href="' . $meta_url . '">', '</a>');
		$message .= '<br /><br />' . sprintf($user->lang['RETURN_FORUM'], '<a href="' . append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $forum_id) . '">', '</a>');

		trigger_error($message);
	}

	function mcp_attr_apply($attribute_id = 0, $topic_ids = array())
	{
		global $auth, $db, $template, $phpEx, $user, $phpbb_root_path;

		// load language
		$user->add_lang(array('mcp', 'mods/attributes'));

		if ( !sizeof($topic_ids) )
		{
			trigger_error('NO_TOPIC_SELECTED');
		}

		if ( !check_ids($topic_ids, TOPICS_TABLE, 'topic_id') )
		{
			return;
		}

		// time !
		$current_time = time();

		$sql = 'SELECT topic_id, forum_id, topic_title, topic_attr_id
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $db->sql_in_set('topic_id', array_map('intval', $topic_ids));
		$result = $db->sql_query($sql);

		// log this action
		while ( $row = $db->sql_fetchrow($result) )
		{
			$message = ($attribute_id == -1) ? 'REMOVED' : (empty($row['topic_attr_id']) ? 'ADDED' : 'UPDATED');
			add_log('mod', $row['forum_id'], $row['topic_id'], 'MCP_ATTRIBUTE_' . $message, $row['topic_title']);
		}
		$db->sql_freeresult($result);

		if ( $attribute_id == -1 )
		{
			$fields = array(
				'topic_attr_id' => 0,
				'topic_attr_user' => 0,
				'topic_attr_time' => 0,
			);
		}
		else
		{
			$fields = array(
				'topic_attr_id' => $attribute_id,
				'topic_attr_user' => $user->data['user_id'],
				'topic_attr_time' => $current_time,
			);
		}

		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $fields) . '
			WHERE ' . $db->sql_in_set('topic_id', array_map('intval', $topic_ids));
		$db->sql_query($sql);

		$sql = 'SELECT topic_id
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $db->sql_in_set('topic_moved_id', array_map('intval', $topic_ids));
		$result = $db->sql_query($sql);

		$shadow_topic_ids = array();
		while ( $row = $db->sql_fetchrow($result) )
		{
			$shadow_topic_ids[] = (int) $row['topic_id'];
		}
		$db->sql_freeresult($result);

		if ( sizeof($shadow_topic_ids) )
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $fields) . '
				WHERE ' . $db->sql_in_set('topic_id', array_map('intval', $shadow_topic_ids));
			$db->sql_query($sql);
		}

		$redirect = request_var('redirect', $user->data['session_page']);

		meta_refresh(3, $redirect);
		trigger_error($user->lang['QTE_TOPIC' . (sizeof($topic_ids) == 1 ? '' : 'S') . '_ATTRIBUTE_' . $message] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect . '">', '</a>'));

		return;
	}

	// borrowed function from "ACP Announcement Centre" mod
	function qte_group_select($group_ids, $exclude_ids = false, $manage_founder = false)
	{
		global $db, $user, $config;

		$exclude_sql = ($exclude_ids !== false && sizeof($exclude_ids)) ? 'WHERE ' . $db->sql_in_set('group_id', array_map('intval', $exclude_ids), true) : '';
		$sql_and = !$config['coppa_enable'] ? ($exclude_sql ? ' AND ' : ' WHERE ') . "group_name <> 'REGISTERED_COPPA'" : '';
		$sql_founder = ($manage_founder !== false) ? (($exclude_sql || $sql_and) ? ' AND ' : ' WHERE ') . 'group_founder_manage = ' . (int) $manage_founder : '';

		$sql = 'SELECT group_id, group_name, group_type
			FROM ' . GROUPS_TABLE . "
			$exclude_sql
			$sql_and
			$sql_founder
			ORDER BY group_type DESC, group_name ASC";
		$result = $db->sql_query($sql);

		$s_group_options = '';
		while ( $row = $db->sql_fetchrow($result) )
		{
			$selected = in_array($row['group_id'], $group_ids) ? ' selected="selected"' : '';
			$s_group_options .= '<option' . (($row['group_type'] == GROUP_SPECIAL) ? ' class="sep"' : '') . ' value="' . $row['group_id'] . '"' . $selected . '>' . (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']) . '</option>';
		}
		$db->sql_freeresult($result);

		return $s_group_options;
	}

	// borrowed from "Categories Hierarchy" : used to check if a language key exists
	function attr_lng_key($key)
	{
		global $user;

		// load language
		$user->add_lang('mods/attributes');

		return isset($user->lang[$key]) ? $user->lang[$key] : $key;
	}

	// borrowed from "Categories Hierarchy" : used to check if a image key exists
	function attr_img_key($key, $alt)
	{
		global $user, $phpbb_root_path;

		return empty($key) ? '' : (isset($user->img_array[$key]) ? $user->img($key, $alt) : '<img src="' . (preg_match('#^(ht|f)tp[s]?\://#i', $key) ? $key : $phpbb_root_path . $key) . '" alt="' . $alt . '" title="' . $alt . '" />');
	}

	// borrowed from "Rank Color System" mod : used to have a different color for each template
	function attr_colour($a_name, $a_colour)
	{
		return empty($a_colour) ? ( empty($a_name) ? '' : ' class="' . strtolower($a_name) . '"' ) : ' style="color:#' . $a_colour . '; font-weight:bold;"';
	}
}
