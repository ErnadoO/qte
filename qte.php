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

namespace ernadoo\qte;

class qte
{
	const KEEP = -2;
	const REMOVE = -1;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $table_prefix;

	/** @var array */
	private $_attr = array();

	/** @var array */
	private $_name = array();

	/**
	* Constructor
	*
	* @param \phpbb\request\request					$request			Request object
	* @param \phpbb\cache\driver\driver_interface	$cache				Cache object
	* @param \phpbb\config\config					$config				Config object
	* @param \phpbb\db\driver\driver_interface 		$db					Database object
	* @param \phpbb\template\template				$template			Template object
	* @param \phpbb\user							$user				User object
	* @param \phpbb\log\log							$log				Log object
	* @param string									$root_path			phpBB root path
	* @param string									$php_ext   			phpEx
	* @param string									$table_prefix   	Prefix tables
	*/
	public function __construct(\phpbb\request\request $request, \phpbb\cache\driver\driver_interface $cache, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\log\log $log, $root_path, $php_ext, $table_prefix)
	{
		$this->request		= $request;
		$this->cache		= $cache;
		$this->config		= $config;
		$this->db			= $db;
		$this->template		= $template;
		$this->user			= $user;
		$this->log			= $log;

		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;
		$this->table_prefix = $table_prefix;

		$this->_get_attributes();
		$this->user->add_lang_ext('ernadoo/qte', 'attributes');
	}

	/**
	* Get topic attributes username
	*
	* @param	array	$topic_list	Topic ids
	*
	* @return	null
	*/
	public function get_users_by_topic_id($topic_list)
	{
		if (!empty($topic_list))
		{
			$sql = 'SELECT u.user_id, u.username, u.user_colour
				FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . TOPICS_TABLE . ' t ON (u.user_id = t.topic_attr_user)
				WHERE ' . $this->db->sql_in_set('t.topic_id', array_map('intval', $topic_list)) . '
					AND t.topic_attr_user <> ' . ANONYMOUS;
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->_name[$row['user_id']] = array(
					'user_id' => (int) $row['user_id'],
					'username' => $row['username'],
					'user_colour' => $row['user_colour'],
				);
			}
			$this->db->sql_freeresult();
		}
	}

	/**
	* Get attribute name
	*
	* @param	int		$attr_id	The attribute id
	*
	* @return	string
	*/
	public function get_attr_name_by_id($attr_id)
	{
		return $this->_attr[$attr_id]['attr_name'];
	}

	/**
	* Get attribute author
	*
	* @param	int		$user_id	User id
	*
	* @return	string
	*/
	public function get_users_by_user_id($user_id)
	{
		if (!isset($this->_name[$user_id]))
		{
			$sql = 'SELECT user_id, username, user_colour
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
			$result = $this->db->sql_query($sql);

			$this->_name = array();
			while ( $row = $this->db->sql_fetchrow($result) )
			{
				$this->_name[$row['user_id']] = array(
					'user_id'		=> (int) $row['user_id'],
					'username'		=> $row['username'],
					'user_colour'	=> $row['user_colour'],
				);
			}
			$this->db->sql_freeresult();
		}
	}

	/**
	* Generate a list of attributes based on permissions
	*
	* @param	int		$forum_id		Forum id
	* @param	int		$author_id		Topic author id
	* @param	int		$attribute_id	Current attribute id
	* @param	array	$hide_attr		Groups which can't delete attribute in this forum
	* @param	string	$viewtopic_url	Topic's url
	*
	* @return	null
	*/
	public function attr_select($forum_id = 0, $author_id = 0, $attribute_id = 0, $hide_attr = array(), $viewtopic_url = '')
	{
		// get current time once !
		$current_time = time();

		$show_select = false;
		$user_groups = array();
		$show_remove = $this->_check_auth_remove_attr($user_groups, $hide_attr);

		foreach ($this->_attr as $attr)
		{
			if (empty($attr['attr_auths']))
			{
				$attr_auths = array(array(
					'forums_ids'	=> array(),
					'groups_ids'	=> array(),
					'author'		=> false,
				));
			}
			else
			{
				$attr_auths = json_decode($attr['attr_auths'], true);
			}

			foreach ($attr_auths as $attr_auth)
			{
				if (!$this->_check_auth_attribute($attr_auth, $forum_id, $user_groups, $author_id))
				{
					continue;
				}

				// show the selector !
				$show_select = true;

				// parse the attribute name
				$attribute_name = str_replace(array('%mod%', '%date%'), array($this->user->data['username'], $this->user->format_date($current_time, $attr['attr_date'])), $this->user->lang($attr['attr_name']));

				$this->template->assign_block_vars('attributes', array(
					'QTE_ID'		=> $attr['attr_id'],
					'QTE_TYPE'		=> $attr['attr_type'],
					'QTE_NAME'		=> $attribute_name,
					'QTE_DESC'		=> $this->user->lang($attr['attr_desc']),
					'QTE_COLOUR'	=> $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

					'IS_SELECTED'	=> (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)),

					'S_QTE_DESC'	=> !empty($attr['attr_desc']) ? true : false,
					'U_QTE_URL'		=> !empty($viewtopic_url) ? append_sid($viewtopic_url, array('attr_id' => $attr['attr_id'])) : false,
				));
			}
		}

		if ($show_select)
		{
			$this->template->assign_vars(array(
				'S_QTE_SELECT'		=> true,
				'S_QTE_REMOVE'		=> $show_remove,
				'S_QTE_EMPTY'		=> (empty($attribute_id)),
				'S_QTE_SELECTED'	=> ($show_remove && ($attribute_id == -1)),
				'S_QTE_KEEP'		=> !empty($attribute_id) && ($attribute_id == self::KEEP),

				'L_QTE_SELECT'		=> $this->user->lang['QTE_ATTRIBUTE_' . (!empty($attribute_id) ? ($show_remove ? 'REMOVE' : 'RESTRICT') : 'ADD')],
				'U_QTE_URL'			=> !empty($viewtopic_url) ? append_sid($viewtopic_url, array('attr_id' => -1)) : false,
			));
		}
	}

	/**
	* Generate a list of all attributes for search page
	*
	* @return	null
	*/
	public function attr_search()
	{
		$show_select = false;

		foreach ($this->_attr as $attr)
		{
			// show the selector !
			$show_select = true;

			// parse the attribute name
			$attribute_name = str_replace(array('%mod%', '%date%'), array($this->user->lang['QTE_KEY_USERNAME'], $this->user->lang['QTE_KEY_DATE']), $this->user->lang($attr['attr_name']));

			$this->template->assign_block_vars('attributes', array(
				'QTE_ID'		=> $attr['attr_id'],
				'QTE_TYPE'		=> $attr['attr_type'],
				'QTE_NAME'		=> $attribute_name,
				'QTE_DESC'		=> $this->user->lang($attr['attr_desc']),
				'QTE_COLOUR'	=> $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

				'S_QTE_DESC'	=> !empty($attr['attr_desc']) ? true : false,
			));
		}

		if ($show_select)
		{
			$this->template->assign_var('S_QTE_SELECT', true);
		}
	}

	/**
	* Generate a list of attributes for viewforum page
	*
	* @param	int		$forum_id		Forum id
	* @param	int		$attribute_id	Current attribute id
	*
	* @return	null
	*/
	public function attr_sort($forum_id = 0, $attribute_id = 0)
	{
		$show_select = false;

		foreach ($this->_attr as $attr)
		{
			if (empty($attr['attr_auths']))
			{
				$attr_auths = array(array(
					'forums_ids'	=> array(),
					'groups_ids'	=> array(),
					'author'		=> false,
				));
			}
			else
			{
				$attr_auths = json_decode($attr['attr_auths'], true);
			}

			foreach ($attr_auths as $attr_auth)
			{
				$forum_ids = $attr_auth['forums_ids'];

				if (is_array($forum_ids) && in_array($forum_id, $forum_ids))
				{
					// show the selector !
					$show_select = true;

					// parse the attribute name
					$attribute_name = str_replace(array('%mod%', '%date%'), array($this->user->lang['QTE_KEY_USERNAME'], $this->user->lang['QTE_KEY_DATE']), $this->user->lang($attr['attr_name']));

					$this->template->assign_block_vars('attributes', array(
						'QTE_ID'		=> $attr['attr_id'],
						'QTE_TYPE'		=> $attr['attr_type'],
						'QTE_NAME'		=> $attribute_name,
						'QTE_DESC'		=> $this->user->lang($attr['attr_desc']),
						'QTE_COLOUR'	=> $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

						'IS_SELECTED'	=> (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)) ? true : false,

						'S_QTE_DESC'	=> !empty($attr['attr_desc']) ? true : false,
					));
				}
			}
		}

		if ($show_select)
		{
			$this->template->assign_var('S_QTE_SELECT', true);
		}
	}

	/**
	* Generate a default attribute list for a forum
	*
	* @param	int		$forum_id		Forum id
	* @param	int		$attribute_id	Current attribute id
	*
	* @return	null
	*/
	public function attr_default($forum_id = 0, $attribute_id = 0)
	{
		$show_select = false;

		foreach ($this->_attr as $attr)
		{
			if (empty($attr['attr_auths']))
			{
				$attr_auths = array(array(
					'forums_ids'	=> array(),
					'groups_ids'	=> array(),
					'author'		=> false,
				));
			}
			else
			{
				$attr_auths = json_decode($attr['attr_auths'], true);
			}

			foreach ($attr_auths as $attr_auth)
			{
				$forum_ids = $attr_auth['forums_ids'];

				if (is_array($forum_ids) && in_array($forum_id, $forum_ids))
				{
					// show the selector !
					$show_select = true;

					// parse the attribute name
					$attribute_name = str_replace(array('%mod%', '%date%'), array($this->user->lang['QTE_KEY_USERNAME'], $this->user->lang['QTE_KEY_DATE']), $this->user->lang($attr['attr_name']));

					$this->template->assign_block_vars('attributes', array(
						'QTE_ID'		=> $attr['attr_id'],
						'QTE_TYPE'		=> $attr['attr_type'],
						'QTE_NAME'		=> $attribute_name,
						'QTE_DESC'		=> $this->user->lang($attr['attr_desc']),
						'QTE_COLOUR'	=> $this->attr_colour($attr['attr_name'], $attr['attr_colour']),

						'IS_SELECTED'	=> (!empty($attribute_id) && ($attr['attr_id'] == $attribute_id)),

						'S_QTE_DESC'	=> !empty($attr['attr_desc']) ? true : false,
					));
				}
			}
		}

		if ($show_select)
		{
			$this->template->assign_var('S_QTE_SELECT', true);
		}
	}

	/**
	* Generate attribute for topic title
	*
	* @param	int		$attribute_id	Current attribute id
	* @param	int		$user_id		Current attribute user id
	* @param	int		$timestamp		Attribute timestamp
	*
	* @return	string					Attribute html code
	*/
	public function attr_display($attribute_id = 0, $user_id = 0, $timestamp = 0)
	{
		if (empty($attribute_id) || empty($user_id) || empty($timestamp))
		{
			return false;
		}

		if (isset($this->_attr[$attribute_id]))
		{
			$attribute_colour = $this->attr_colour($this->_attr[$attribute_id]['attr_name'], $this->_attr[$attribute_id]['attr_colour']);

			if (isset($this->_name[$user_id]['user_id']))
			{
				$attribute_username = get_username_string(($this->_attr[$attribute_id]['attr_user_colour'] ? 'no_profile' : 'username'), $this->_name[$user_id]['user_id'], $this->_name[$user_id]['username'], $this->_name[$user_id]['user_colour']);
			}
			else
			{
				$attribute_username = $this->user->lang['GUEST'];
			}

			$attribute_date = $this->user->format_date($timestamp, $this->_attr[$attribute_id]['attr_date']);

			$attribute_name = str_replace(array('%mod%', '%date%'), array($attribute_username, $attribute_date), $this->user->lang($this->_attr[$attribute_id]['attr_name']));

			return !$this->_attr[$attribute_id]['attr_type'] ? '<span' . $attribute_colour . '>' . $attribute_name . '</span>' : $this->attr_img_key($this->_attr[$attribute_id]['attr_img'], $attribute_name);
		}
	}

	/**
	* Generate attribute for page title
	*
	* @param	int		$attribute_id	Current attribute id
	* @param	int		$user_id		Current attribute user id
	* @param	int		$timestamp		Attribute timestamp
	*
	* @return	string					attribute html code
	*/
	public function attr_title($attribute_id = 0, $user_id = 0, $timestamp = 0)
	{
		if (empty($attribute_id) || empty($user_id) || empty($timestamp))
		{
			return false;
		}

		if (isset($this->_attr[$attribute_id]))
		{
			if (isset($this->_name[$user_id]['user_id']))
			{
				$attribute_username = get_username_string('username', $this->_name[$user_id]['user_id'], $this->_name[$user_id]['username'], $this->_name[$user_id]['user_colour']);
			}
			else
			{
				$attribute_username = $this->user->lang['GUEST'];
			}

			$attribute_date = $this->user->format_date($timestamp, $this->_attr[$attribute_id]['attr_date']);

			return str_replace(array('%mod%', '%date%'), array($attribute_username, $attribute_date), $this->user->lang($this->_attr[$attribute_id]['attr_name']));
		}
	}


	/**
	* Change topic attribute
	*
	* @param	int		$attribute_id		New attribute id
	* @param	int		$topic_id			The id of the topic
	* @param	int		$forum_id			The id of the forum
	* @param	int		$topic_attribute	Current attribute id
	* @param	array	$hide_attr			Groups which can't delete attribute in this forum
	*
	* @return	null
	*/
	public function attr_apply($attribute_id = 0, $topic_id = 0, $forum_id = 0, $topic_attribute = 0, $hide_attr = array())
	{
		if (empty($topic_id) || empty($forum_id) || empty($attribute_id))
		{
			return;
		}

		// time !
		$current_time = time();
		$user_groups = array();

		if ($attribute_id == self::REMOVE && !$this->_check_auth_remove_attr($user_groups, $hide_attr))
		{
			return;
		}
		else if ($attribute_id == self::REMOVE)
		{
			$fields = array(
				'topic_attr_id'		=> 0,
				'topic_attr_user'	=> 0,
				'topic_attr_time'	=> 0,
			);
		}
		else
		{
			$fields = array(
				'topic_attr_id'		=> $attribute_id,
				'topic_attr_user'	=> $this->user->data['user_id'],
				'topic_attr_time'	=> $current_time,
			);
		}

		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $fields) . '
			WHERE topic_id = ' . (int) $topic_id;
		$this->db->sql_query($sql);

		$sql = 'SELECT topic_id
			FROM ' . TOPICS_TABLE . '
			WHERE topic_moved_id = ' . (int) $topic_id;
		$result = $this->db->sql_query($sql);
		$shadow_topic_id = (int) $this->db->sql_fetchfield('topic_id');
		$this->db->sql_freeresult($result);

		if (!empty($shadow_topic_id))
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $this->db->sql_build_array('UPDATE', $fields) . '
				WHERE topic_id = ' . $shadow_topic_id;
			$this->db->sql_query($sql);
		}

		$meta_url = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, array('f' => $forum_id, 't' => $topic_id));
		meta_refresh(3, $meta_url);

		// load language
		$this->user->add_lang('posting');

		$message = $this->user->lang['QTE_ATTRIBUTE_' . ($attribute_id == -1 ? 'REMOVED' : (empty($topic_attribute) ? 'ADDED' : 'UPDATED'))] . '<br /><br />' . sprintf($this->user->lang['VIEW_MESSAGE'], '<a href="' . $meta_url . '">', '</a>');
		$message .= '<br /><br />' . sprintf($this->user->lang['RETURN_FORUM'], '<a href="' . append_sid($this->root_path . 'viewforum.' . $this->php_ext, array('f' => $forum_id)) . '">', '</a>');

		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'success' => true,

				'MESSAGE_TITLE'	=> $this->user->lang['INFORMATION'],
				'MESSAGE_TEXT'	=> $message,
				'NEW_ATTRIBUTE'	=> $this->attr_display($attribute_id, $this->user->data['user_id'], $current_time),
			));
		}

		trigger_error($message);
	}

	/**
	* Change topic attribute in mcp
	*
	* @param	int		$attribute_id		New attribute id
	* @param	array	$topic_ids			Topics ids
	*
	* @return	null
	*/
	public function mcp_attr_apply($attribute_id = 0, $topic_ids = array())
	{
		if (!sizeof($topic_ids))
		{
			trigger_error('NO_TOPIC_SELECTED');
		}

		if (!phpbb_check_ids($topic_ids, TOPICS_TABLE, 'topic_id'))
		{
			return;
		}

		// time !
		$current_time = time();

		$sql = 'SELECT topic_id, forum_id, topic_title, topic_attr_id
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $this->db->sql_in_set('topic_id', array_map('intval', $topic_ids));
		$result = $this->db->sql_query($sql);

		// log this action
		while ($row = $this->db->sql_fetchrow($result))
		{
			$message = ($attribute_id == -1) ? 'REMOVED' : (empty($row['topic_attr_id']) ? 'ADDED' : 'UPDATED');
			$additional_data = array(
				'forum_id'	=> $row['forum_id'],
				'topic_id'	=> $row['topic_id'],
				$row['topic_title'],
			);
			$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'MCP_ATTRIBUTE_' . $message, $current_time, $additional_data);
		}
		$this->db->sql_freeresult($result);

		if ($attribute_id == -1)
		{
			$fields = array(
				'topic_attr_id'		=> 0,
				'topic_attr_user'	=> 0,
				'topic_attr_time'	=> 0,
			);
		}
		else
		{
			$fields = array(
				'topic_attr_id'		=> $attribute_id,
				'topic_attr_user'	=> $this->user->data['user_id'],
				'topic_attr_time'	=> $current_time,
			);
		}

		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $fields) . '
			WHERE ' . $this->db->sql_in_set('topic_id', array_map('intval', $topic_ids));
		$this->db->sql_query($sql);

		$sql = 'SELECT topic_id
			FROM ' . TOPICS_TABLE . '
			WHERE ' . $this->db->sql_in_set('topic_moved_id', array_map('intval', $topic_ids));
		$result = $this->db->sql_query($sql);

		$shadow_topic_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$shadow_topic_ids[] = (int) $row['topic_id'];
		}
		$this->db->sql_freeresult($result);

		if (sizeof($shadow_topic_ids))
		{
			$sql = 'UPDATE ' . TOPICS_TABLE . '
				SET ' . $this->db->sql_build_array('UPDATE', $fields) . '
				WHERE ' . $this->db->sql_in_set('topic_id', array_map('intval', $shadow_topic_ids));
			$this->db->sql_query($sql);
		}

		$redirect = $this->request->variable('redirect', $this->user->data['session_page']);

		meta_refresh(3, $redirect);
		trigger_error($this->user->lang['QTE_TOPIC' . (sizeof($topic_ids) == 1 ? '' : 'S') . '_ATTRIBUTE_' . (isset($message) ? $message : 'ADDED')] . '<br /><br />' . sprintf($this->user->lang['RETURN_PAGE'], '<a href="' . $redirect . '">', '</a>'));

		return;
	}

	/**
	* Getter...
	*
	* @return	array
	*/
	public function getAttr()
	{
		return $this->_attr;
	}

	/**
	* Generate list of groups
	*
	* @param int		$group_ids		The default groups id to mark as selected
	* @param array|bool	$exclude_ids	The group ids to exclude from the list, false (default) if you whish to exclude no id
	* @param bool		$manage_founder If set to false (default) all groups are returned, if 0 only those groups returned not being managed by founders only, if 1 only those groups returned managed by founders only.
	*
	* @return string The list of options.
	*/
	public function qte_group_select($group_ids, $exclude_ids = array(), $manage_founder = false)
	{
		$exclude_sql = ($exclude_ids !== false && sizeof($exclude_ids)) ? 'WHERE ' . $this->db->sql_in_set('group_id', array_map('intval', $exclude_ids), true) : '';
		$sql_and = !$this->config['coppa_enable'] ? ($exclude_sql ? ' AND ' : ' WHERE ') . "group_name <> 'REGISTERED_COPPA'" : '';
		$sql_founder = ($manage_founder !== false) ? (($exclude_sql || $sql_and) ? ' AND ' : ' WHERE ') . 'group_founder_manage = ' . (int) $manage_founder : '';

		$sql = 'SELECT group_id, group_name, group_type
			FROM ' . GROUPS_TABLE . "
			$exclude_sql
			$sql_and
			$sql_founder
			ORDER BY group_type DESC, group_name ASC";
		$result = $this->db->sql_query($sql);

		$s_group_options = '';
		while ($row = $this->db->sql_fetchrow($result))
		{
			$selected = in_array($row['group_id'], $group_ids) ? ' selected="selected"' : '';
			$s_group_options .= '<option' . (($row['group_type'] == GROUP_SPECIAL) ? ' class="sep"' : '') . ' value="' . $row['group_id'] . '"' . $selected . '>' . (($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang['G_' . $row['group_name']] : $row['group_name']) . '</option>';
		}
		$this->db->sql_freeresult($result);

		return $s_group_options;
	}

	// borrowed from "Categories Hierarchy" : used to check if a image key exists
	public function attr_img_key($key, $alt)
	{
		return empty($key) ? '' : (preg_match('#^[a-z0-9_-]+$#i', $key) ? $this->user->img($key, $alt) : '<img src="' . (preg_match('#^(ht|f)tp[s]?\://#i', $key) ? $key : $this->root_path . $key) . '" alt="' . $alt . '" title="' . $alt . '" />');
	}

	/**
	* Build class and style attribute
	*
	* @param	string	$a_name			Attribute name
	* @param	string	$a_colour		Attribute color
	* @return	string					html code
	*/
	public function attr_colour($a_name, $a_colour)
	{
		if ($a_name != $this->user->lang($a_name))
		{
			$a_class_name = preg_replace('#[^a-z0-9 _-]#', '', strtolower($a_name));
		}

		return ' class="qte-attr ' . (isset($a_class_name) ?  $a_class_name : '') . '"' . (!empty($a_colour) ? ' style="color:#' . $a_colour . '; font-weight:bold;"' : '');
	}

	/**
	* Check if user can apply an attribute
	*
	* @param	array	$attr_auth		Forum auth
	* @param	int		$forum_id		Forum id
	* @param	array	$user_groups	User's groups
	* @param	int		$author_id		Topic author id
	* @return	bool
	*/
	private function _check_auth_attribute($attr_auth, $forum_id, $user_groups, $author_id)
	{
		$forum_ids = $attr_auth['forums_ids'];
		$group_ids = $attr_auth['groups_ids'];

		if (is_array($forum_ids) && in_array($forum_id, $forum_ids))
		{
			if (is_array($group_ids) && array_intersect($group_ids, $user_groups) || ($attr_auth['author'] && ($author_id == $this->user->data['user_id']) && ($this->user->data['user_id'] != ANONYMOUS)))
			{
				return true;
			}
		}

		return false;
	}

	/**
	* Check if user can delete an attribute
	*
	* @param	array	$user_groups	User's groups
	* @param	array	$hide_attr		Groups which can't delete attribute in a forum
	* @return	bool
	*/
	private function _check_auth_remove_attr(&$user_groups, $hide_attr)
	{
		// include that file !
		if (!function_exists('group_memberships'))
		{
			include $this->root_path . 'includes/functions_user.' . $this->php_ext;
		}

		// get groups membership !
		$user_membership = group_memberships(false, $this->user->data['user_id']);

		$user_groups = array();
		if (!empty($user_membership))
		{
			foreach ($user_membership as $row)
			{
				$user_groups[$row['group_id']] = (int) $row['group_id'];
			}
		}

		$groups_removed = array_intersect($user_groups, $hide_attr);
		return (empty($hide_attr) || (count($groups_removed) < count($user_groups)));
	}

	/**
	* Get attributes from database
	*
	* @return	null
	*/
	private function _get_attributes()
	{
		if (($this->_attr = $this->cache->get('_attr')) === false)
		{
			$sql = 'SELECT *
				FROM ' . $this->table_prefix . 'topics_attr
				ORDER BY left_id ASC';
			$result = $this->db->sql_query($sql);

			$this->_attr = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->_attr[$row['attr_id']] = array(
					'attr_id'			=> (int) $row['attr_id'],
					'attr_type'			=> (bool) $row['attr_type'],
					'attr_name'			=> $row['attr_name'],
					'attr_desc'			=> $row['attr_desc'],
					'attr_img'			=> $row['attr_img'],
					'attr_colour'		=> $row['attr_colour'],
					'attr_date'			=> $row['attr_date'],
					'attr_user_colour'	=> (bool) $row['attr_user_colour'],
					'attr_auths'		=> $row['attr_auths'],
				);
			}
			$this->db->sql_freeresult();

			$this->cache->put('_attr', $this->_attr);
		}
	}
}
