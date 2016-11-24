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

namespace ernadoo\qte\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \ernadoo\qte\qte */
	protected $qte;

	/** @var string */
	protected $table_prefix;

	public function __construct(\phpbb\request\request $request, \phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \ernadoo\qte\qte $qte, $table_prefix)
	{
		$this->request	= $request;
		$this->cache	= $cache;
		$this->db		= $db;
		$this->user		= $user;
		$this->qte		= $qte;

		$this->table_prefix = $table_prefix;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'					=> 'add_permission',
			'core.delete_user_after'			=> 'delete_user',
			'core.get_logs_main_query_before'	=> 'load_log_keys',
			'core.get_logs_modify_entry_data'	=> 'translate_attributes',

			// forums admin panel
			'core.acp_manage_forums_request_data'		=> 'get_attributes_data',
			'core.acp_manage_forums_initialise_data'	=> 'define_attributes_values',
			'core.acp_manage_forums_display_form'		=> 'add_attributes_features',
			'core.acp_manage_forums_update_data_after'	=> 'save_attribute_auths',
		);
	}

	public function add_permission($event)
	{
		$categories				= $event['categories'];
		$categories				= array_merge($categories, array('qte' => 'ACL_CAT_QTE'));
		$event['categories']	= $categories;

		$permissions = $event['permissions'];

		$permissions += array(
			'a_attr_manage'		=> array('lang' => 'ACL_A_ATTR_MANAGE', 'cat' => 'posting'),
			'm_qte_attr_del'	=> array('lang' => 'ACL_M_ATTR_DEL', 'cat' => 'qte'),
			'm_qte_attr_edit'	=> array('lang' => 'ACL_M_ATTR_EDIT', 'cat' => 'qte'),
		);

		foreach ($this->qte->getAttr() as $attr)
		{
			$permissions += array(
				'f_qte_attr_'.$attr['attr_id'] => array('lang' => $this->user->lang('QTE_CAN_USE_ATTR', $attr['attr_name']), 'cat' => 'qte'),
			);
		}
		$event['permissions'] = $permissions;
	}

	public function delete_user($event)
	{
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET topic_attr_user = ' . ANONYMOUS . '
			WHERE ' . $this->db->sql_in_set('topic_attr_user', $event['user_ids']);
		$this->db->sql_query($sql);
	}

	public function load_log_keys()
	{
		$this->user->add_lang_ext('ernadoo/qte', array('attributes', 'logs_attributes'));
	}

	public function translate_attributes($event)
	{
		$row = $event['row'];

		if (strpos($row['log_operation'], 'LOG_ATTRIBUTE_') === 0 || strpos($row['log_operation'], 'MCP_ATTRIBUTE_') === 0)
		{
			$log_data = unserialize($row['log_data']);

			if (!empty($log_data) && is_array($log_data))
			{
				foreach ($log_data as &$arg)
				{
					$arg = str_replace(array('%mod%', '%date%'), array($this->user->lang['QTE_KEY_USERNAME'], $this->user->lang['QTE_KEY_DATE']), $this->user->lang($arg));
				}
			}

			$row['log_data'] = serialize($log_data);
			$event['row'] = $row;
		}
	}

	public function get_attributes_data($event)
	{
		if ($event['action'] == 'edit')
		{
			$event['forum_data'] += array(
				'default_attr' => $this->request->variable('default_attr', 0, false, \phpbb\request\request_interface::POST),
			);
		}

		$event['forum_data'] += array('force_attr' => $this->request->variable('force_attr', false, false, \phpbb\request\request_interface::POST));
	}

	public function define_attributes_values($event)
	{
		if ($event['action'] == 'edit')
		{
			$event['forum_data'] += array('default_attr' => 0);
		}

		if ($event['update'])
		{
			$event['forum_data'] += array('force_attr' => false);
		}
	}

	public function add_attributes_features($event)
	{
		$this->user->add_lang_ext('ernadoo/qte', 'attributes_acp');

		// init ary
		$template_data = $event['template_data'];

		if ($event['action'] == 'edit')
		{
			$this->qte->attr_default($event['forum_id'], $event['forum_data']['default_attr']);
		}

		$template_data += array('S_FORCE_ATTR' => $event['forum_data']['force_attr'] ? true : false);

		// send to template
		$event['template_data'] = $template_data;
		return $event['template_data'];
	}

	public function save_attribute_auths($event)
	{
		if (!sizeof($event['errors']))
		{
			$from_attr = $this->request->variable('from_attr', 0, false, \phpbb\request\request_interface::POST);

			if ($from_attr && $from_attr != $event['forum_data']['forum_id'])
			{
				$this->_copy_attribute_permissions($from_attr, $event['forum_data']['forum_id'], $event['is_new_forum'] ? false : true);
			}
		}
	}

	/**
	* Copies attributes permissions from one forum to others
	*
	* @param int	$src_forum_id		The source forum we want to copy permissions from
	* @param array	$dest_forum_ids		The destination forum(s) we want to copy to
	* @param bool	$clear_dest_perms	True if destination permissions should be deleted
	*
	* @return bool						False on error
	*/
	private function _copy_attribute_permissions($src_forum_id, $dest_forum_ids, $clear_dest_perms)
	{
		// Only one forum id specified
		if (!is_array($dest_forum_ids))
		{
			$dest_forum_ids = array($dest_forum_ids);
		}

		// Make sure forum ids are integers
		$src_forum_id = (int) $src_forum_id;
		$dest_forum_ids = array_map('intval', $dest_forum_ids);

		// No source forum or no destination forums specified
		if (empty($src_forum_id) || empty($dest_forum_ids))
		{
			return false;
		}

		// Check if source forum exists
		$sql = 'SELECT forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . $src_forum_id;
		$result = $this->db->sql_query($sql);
		$src_forum_name = $this->db->sql_fetchfield('forum_name');
		$this->db->sql_freeresult($result);

		// Source forum doesn't exist
		if (empty($src_forum_name))
		{
			return false;
		}

		// Check if destination forums exists
		$sql = 'SELECT forum_id, forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE ' . $this->db->sql_in_set('forum_id', $dest_forum_ids);
		$result = $this->db->sql_query($sql);

		$dest_forum_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$dest_forum_ids[]	= (int) $row['forum_id'];
		}
		$this->db->sql_freeresult($result);

		// No destination forum exists
		if (empty($dest_forum_ids))
		{
			return false;
		}

		// Get informations about acl options
		$sql = 'SELECT auth_option_id FROM ' . ACL_OPTIONS_TABLE . '
			WHERE auth_option ' . $this->db->sql_like_expression($this->db->get_any_char() . '_qte_attr_' . $this->db->get_any_char());
		$result = $this->db->sql_query($sql);

		$acl_options_ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$acl_options_ids[]	= (int) $row['auth_option_id'];
		}

		// From the mysql documentation:
		// Prior to MySQL 4.0.14, the target table of the INSERT statement cannot appear
		// in the FROM clause of the SELECT part of the query. This limitation is lifted in 4.0.14.
		// Due to this we stay on the safe side if we do the insertion "the manual way"

		// Rowsets we're going to insert
		$users_sql_ary = $groups_sql_ary = array();

		// Query acl users table for source forum data
		$sql = 'SELECT user_id, auth_option_id, auth_role_id, auth_setting
			FROM ' . ACL_USERS_TABLE . '
			WHERE '. $this->db->sql_in_set('auth_option_id', $acl_options_ids) . '
				AND forum_id = ' . $src_forum_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$row = array(
				'user_id'			=> (int) $row['user_id'],
				'auth_option_id'	=> (int) $row['auth_option_id'],
				'auth_role_id'		=> (int) $row['auth_role_id'],
				'auth_setting'		=> (int) $row['auth_setting'],
			);

			foreach ($dest_forum_ids as $dest_forum_id)
			{
				$users_sql_ary[] = $row + array('forum_id' => $dest_forum_id);
			}
		}
		$this->db->sql_freeresult($result);

		// Query acl groups table for source forum data
		$sql = 'SELECT group_id, auth_option_id, auth_role_id, auth_setting
			FROM ' . ACL_GROUPS_TABLE . '
			WHERE '. $this->db->sql_in_set('auth_option_id', $acl_options_ids) . '
				AND forum_id = ' . $src_forum_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$row = array(
				'group_id'			=> (int) $row['group_id'],
				'auth_option_id'	=> (int) $row['auth_option_id'],
				'auth_role_id'		=> (int) $row['auth_role_id'],
				'auth_setting'		=> (int) $row['auth_setting'],
			);

			foreach ($dest_forum_ids as $dest_forum_id)
			{
				$groups_sql_ary[] = $row + array('forum_id' => $dest_forum_id);
			}
		}
		$this->db->sql_freeresult($result);

		$this->db->sql_transaction('begin');

		if ($clear_dest_perms)
		{
			// Clear current permissions of destination forums
			$sql = 'DELETE FROM ' . ACL_USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('auth_option_id', $acl_options_ids) . '
					AND ' . $this->db->sql_in_set('forum_id', $dest_forum_ids);
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . ACL_GROUPS_TABLE . '
				WHERE ' . $this->db->sql_in_set('auth_option_id', $acl_options_ids) . '
					AND ' . $this->db->sql_in_set('forum_id', $dest_forum_ids);
			$this->db->sql_query($sql);
		}
		$this->db->sql_multi_insert(ACL_USERS_TABLE, $users_sql_ary);
		$this->db->sql_multi_insert(ACL_GROUPS_TABLE, $groups_sql_ary);

		$this->db->sql_transaction('commit');

		return true;
	}
}
