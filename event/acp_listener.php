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
		$this->request = $request;
		$this->cache = $cache;
		$this->db = $db;
		$this->user = $user;
		$this->qte = $qte;

		$this->table_prefix = $table_prefix;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions' => 'add_permission',
			'core.delete_user_after' => 'delete_user',
			'core.get_logs_main_query_before' => 'load_log_keys',
			'core.get_logs_modify_entry_data'=> 'translate_attributes',

			// forums admin panel
			'core.acp_manage_forums_request_data' => 'get_attributes_data',
			'core.acp_manage_forums_initialise_data' => 'define_attributes_values',
			'core.acp_manage_forums_display_form' => 'add_attributes_features',
			'core.acp_manage_forums_validate_data' => 'save_attribute_info',
			'core.acp_manage_forums_update_data_after' => 'save_attribute_auths',
		);
	}

	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			// ACP
			'a_attr_manage' => array('lang' => 'ACL_A_ATTR_MANAGE', 'cat' => 'posting'),
		);
		$event['permissions'] = $permissions;
	}

	public function delete_user($event)
	{
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET topic_attr_user = ' . ANONYMOUS . '
			WHERE topic_attr_user = ' . (int) $event['user_ids'];
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
				'hide_attr' => $this->request->variable('hide_attr', array(0), false, \phpbb\request\request_interface::POST),
			);
		}

		$event['forum_data'] += array('force_attr' => $this->request->variable('force_attr', false, false, \phpbb\request\request_interface::POST));
	}

	public function define_attributes_values($event)
	{
		if ($event['action'] == 'edit')
		{
			$event['forum_data'] += array('default_attr' => 0, 'hide_attr' => array());
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

			$group_ids = unserialize(trim($event['forum_data']['hide_attr']));
			if ($group_ids === false)
			{
				$group_ids = array();
			}

			$template_data += array('S_GROUPS_HIDE_ATTR' => $this->qte->qte_group_select($group_ids));
		}

		$template_data += array('S_FORCE_ATTR' => $event['forum_data']['force_attr'] ? true : false);

		// send to template
		$event['template_data'] = $template_data;
		return $event['template_data'];
	}

	public function save_attribute_info($event)
	{
		$forum_data = $event['forum_data'];

		if (!empty($forum_data['hide_attr']))
		{
			$forum_data['hide_attr'] = serialize($event['forum_data']['hide_attr']);
		}
		else
		{
			$forum_data['hide_attr'] = '';
		}

		$event['forum_data'] = $forum_data;
	}

	public function save_attribute_auths($event)
	{
		if (!sizeof($event['errors']))
		{
			$from_attr = $this->request->variable('from_attr', 0, false, \phpbb\request\request_interface::POST);
			if ($from_attr)
			{
				foreach ($this->qte->getAttr() as $attr)
				{
					if ($attr['attr_auths'])
					{
						$attr['attr_auths'] = json_decode($attr['attr_auths'], true);
						if (!empty($attr['attr_auths'][0]['forums_ids']) && in_array($from_attr, $attr['attr_auths'][0]['forums_ids']))
						{
							$attr['attr_auths'][0]['forums_ids'][] = $event['forum_data']['forum_id'];
							$sql_ary = array('attr_auths' => json_encode($attr['attr_auths']));

							$sql = 'UPDATE ' . $this->table_prefix . 'topics_attr
								SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
								WHERE attr_id = ' . (int) $attr['attr_id'];
							$this->db->sql_query($sql);
						}
					}
				}
				$this->cache->destroy('_attr');
			}
		}
	}
}
