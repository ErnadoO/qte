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

namespace abdev\qte\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

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

	/** @var \abdev\qte\qte */
	protected $qte;

	/** @var string */
	protected $table_prefix;

	public function __construct(\phpbb\request\request $request, \phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\log\log $log, \abdev\qte\qte $qte, $root_path, $php_ext, $table_prefix)
	{
		$this->request = $request;
		$this->cache = $cache;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->log = $log;
		$this->qte = $qte;

		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions' => 'add_permission',
			'core.acp_manage_forums_request_data' => 'acp_manage_forums_request_data_complement',
			'core.acp_manage_forums_initialise_data' => 'acp_manage_forums_initialise_data_complement',
			'core.acp_manage_forums_display_form' => 'acp_manage_forums_display_form_complement',
			'core.acp_manage_forums_validate_data' => 'acp_manage_forums_validate_data_complement',
			'core.acp_manage_forums_update_data_after' => 'acp_manage_forums_update_data_after_complement',

			'core.mcp_topic_review_modify_row' => 'mcp_topic_review_modify_row_complement',

			'core.get_logs_main_query_before' => 'get_logs_main_query_before_complement',

			'core.posting_modify_message_text' => 'posting_modify_message_text_complement',
			'core.posting_modify_submit_post_before' => 'posting_modify_submit_post_before_complement',
			'core.posting_modify_template_vars' => 'posting_modify_template_vars_complement',
			'core.viewforum_modify_topics_data' => 'viewforum_modify_topics_data_complement',
			'core.viewforum_topic_row_after' => 'viewforum_topic_row_after_complement',
			'core.viewtopic_assign_template_vars_before' => 'viewtopic_assign_template_vars_before_complement',
			'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row_complement',
			'core.viewtopic_modify_page_title' => 'viewtopic_modify_page_title_complement',

			'core.submit_post_modify_sql_data' => 'submit_post_modify_sql_data_complement',
			'core.delete_user_after' => 'delete_user_attributes',
		);
	}

	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			// ACP
			'acl_a_attr_manage' => array('lang' => 'ACL_A_ATTR_MANAGE', 'cat' => 'posting'),
		);
		$event['permissions'] = $permissions;
	}

	public function acp_manage_forums_request_data_complement($event)
	{
		if ($event['action'] == 'edit')
		{
			$event['forum_data'] += array(
				'default_attr' => $this->request->variable('default_attr', 0),
				'hide_attr' => $this->request->variable('hide_attr', array(0)),
			);
		}

		$event['forum_data'] += array('force_attr' => $this->request->variable('force_attr', false));
	}

	public function acp_manage_forums_initialise_data_complement($event)
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

	public function acp_manage_forums_display_form_complement($event)
	{
		// init ary
		$tpl_fields = array();

		if ($event['action'] == 'edit')
		{
			$this->qte->attr_default($event['forum_id'], $event['forum_data']['default_attr']);

			$group_ids = unserialize(trim($event['forum_data']['hide_attr']));
			if ($group_ids === false)
			{
				$group_ids = array();
			}

			$tpl_fields += array('S_GROUPS_HIDE_ATTR' => $this->qte->qte_group_select($group_ids));
		}

		$tpl_fields += array('S_FORCE_ATTR' => $event['forum_data']['force_attr'] ? true : false);

		// send to template
		$this->template->assign_vars($tpl_fields);
	}

	public function acp_manage_forums_validate_data_complement($event)
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

	public function acp_manage_forums_update_data_after_complement($event)
	{
		if (!sizeof($event['errors']))
		{
			$from_attr = $this->request->variable('from_attr', 0);
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

	public function mcp_topic_review_modify_row_complement($event)
	{
		if (!empty($event['topic_info']['topic_attr_id']) && !$event['current_row_number'])
		{
			$post_row = $event['post_row'];
			$post_row += array('TOPIC_ATTRIBUTE' => $this->qte->attr_display($event['topic_info']['topic_attr_id'], $event['topic_info']['topic_attr_user'], $event['topic_info']['topic_attr_time']));
			$event['post_row'] = $post_row;
		}
	}

	public function get_logs_main_query_before_complement()
	{
		$this->user->add_lang_ext('abdev/qte', 'info_mcp_attributes');
	}

	public function posting_modify_message_text_complement($event)
	{
		$post_data = $event['post_data'];
		$post_data['attr_id'] = $this->request->variable('attr_id', 0);

		if ($post_data['attr_id'] != -2)
		{
			if (!empty($event['post_data']['topic_attr_id']))
			{
				if (empty($post_data['attr_id']))
				{
					$post_data['attr_id'] = $event['post_data']['topic_attr_id'];
					$event['post_data'] = $post_data;
				}
			}
		}
		$event['post_data'] = $post_data;
	}

	public function posting_modify_submit_post_before_complement($event)
	{
		$attr_id = (($event['mode'] == 'post') && !empty($event['post_data']['default_attr'])) ? $event['post_data']['default_attr'] : $event['post_data']['attr_id'];

		$data = $event['data'];
		$data += array('attr_id' => (int) $attr_id);
		$event['data'] = $data;

	}

	public function posting_modify_template_vars_complement($event)
	{
		$current_time = time();

		if (!empty($event['post_data']['attr_id']) && ($event['post_data']['attr_id'] != -1))
		{
			$this->qte->get_users_by_user_id($this->user->data['user_id']);
			$this->template->assign_vars(array(
				'S_TOPIC_ATTR' => true,
				'TOPIC_ATTRIBUTE' => $this->qte->attr_display($event['post_data']['attr_id'], $this->user->data['user_id'], $current_time),
			));
		}

		if ($event['mode'] == 'post' || ($event['mode'] == 'edit' && $event['post_id'] == $event['post_data']['topic_first_post_id']))
		{
			$topic_attribute = $this->request->variable('attr_id', 0);
			if (!$event['preview'])
			{
				if (!empty($event['post_data']['topic_attr_id']))
				{
					$topic_attribute = $event['post_data']['topic_attr_id'];
				}
			}
			else
			{
				$topic_attribute = $this->request->variable('attr_id', 0);
			}

			$hide_attr = unserialize(trim($event['post_data']['hide_attr']));
			if ($hide_attr === false)
			{
				$hide_attr = array();
			}

			$this->qte->attr_select($event['forum_id'], $this->user->data['user_id'], (int) $topic_attribute, $hide_attr);

			$this->template->assign_vars(array(
				'S_POSTING' => true,
				'IS_AUTHOR' => ($event['post_data']['poster_id'] == $this->user->data['user_id']),
			));
		}

		if (($event['mode'] == 'edit') && isset($topic_attribute) && ($event['post_id'] == $event['post_data']['topic_first_post_id']))
		{
			if ($event['preview'])
			{
				if ($topic_attribute != $event['post_data']['topic_attr_id'])
				{
					$post_data['topic_attr_id'] = (int) $topic_attribute;
					$post_data['topic_attr_user'] = (int) $this->user->data['user_id'];
					$post_data['topic_attr_time'] = (string) $current_time;
				}
			}

			if ($event['post_data']['topic_attr_id'] != -1)
			{
				$this->qte->get_users_by_topic_id(array($event['post_data']['topic_id']));
				$this->template->assign_vars(array(
					'S_TOPIC_ATTR' => true,
					'TOPIC_ATTRIBUTE' => $this->qte->attr_display($event['post_data']['topic_attr_id'], $event['post_data']['topic_attr_user'], $event['post_data']['topic_attr_time']),
				));
			}
		}
	}

	public function viewforum_modify_topics_data_complement($event)
	{
		if (sizeof($event['topic_list']))
		{
			$this->qte->get_users_by_topic_id($event['topic_list']);
		}
	}

	public function viewforum_topic_row_after_complement($event)
	{
		if (!empty($event['row']['topic_attr_id']))
		{
			$this->template->alter_block_array('topicrow', array(
				'TOPIC_ATTRIBUTE' => $this->qte->attr_display($event['row']['topic_attr_id'], $event['row']['topic_attr_user'], $event['row']['topic_attr_time']),
			), true, 'change');
		}
	}

	public function viewtopic_assign_template_vars_before_complement($event)
	{
		// did the user apply an attribute ? so, let's save it !
		$attr_id = $this->request->variable('attr_id', 0);
		if ($attr_id)
		{
			$this->qte->attr_apply($attr_id, $event['topic_id'], $event['forum_id'], $event['topic_data']['topic_attr_id']);
		}

		// show the selector
		$hide_attr = unserialize(trim($event['topic_data']['hide_attr']));
		if ($hide_attr === false)
		{
			$hide_attr = array();
		}

		$this->qte->attr_select($event['forum_id'], $event['topic_data']['topic_poster'], $event['topic_data']['topic_attr_id'], $hide_attr);

		$tpl_ary = array('S_QTE_FORM' => append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f={$event['forum_id']}&amp;t={$event['topic_id']}"));
		if (!empty($event['topic_data']['topic_attr_id']))
		{
			$this->qte->get_users_by_topic_id(array($event['topic_data']['topic_id']));
			$tpl_ary += array(
				'S_TOPIC_ATTR' => true,
				'TOPIC_ATTRIBUTE' => $this->qte->attr_display($event['topic_data']['topic_attr_id'], $event['topic_data']['topic_attr_user'], $event['topic_data']['topic_attr_time']),
			);
		}
		$this->template->assign_vars($tpl_ary);
	}

	public function viewtopic_modify_post_row_complement($event)
	{
		if (!empty($event['topic_data']['topic_attr_id']))
		{
			$this->template->assign_var('TOPIC_ATTRIBUTE', $this->qte->attr_display($event['topic_data']['topic_attr_id'], $event['topic_data']['topic_attr_user'], $event['topic_data']['topic_attr_time']));
		}
	}

	public function viewtopic_modify_page_title_complement($event)
	{
		$attribute_title = $this->qte->attr_title($event['topic_data']['topic_attr_id'], $event['topic_data']['topic_attr_user'], $event['topic_data']['topic_attr_time']);

		$topic_data = $event['topic_data'];
		$topic_data['page_title'] = $attribute_title . ' ' . $event['topic_data']['topic_title'];
		$event['topic_data'] = $topic_data;
	}

	public function submit_post_modify_sql_data_complement($event)
	{
		if (in_array($event['post_mode'], array('post', 'edit_topic', 'edit_first_post')))
		{
			$attr_id = isset($event['data']['attr_id']) ? $event['data']['attr_id'] : -1;

			if ($attr_id != -2)
			{
				$sql_data = $event['sql_data'];
				if ($attr_id == -1)
				{
					$sql_data[TOPICS_TABLE]['sql'] += array('topic_attr_id' => 0, 'topic_attr_user' => 0, 'topic_attr_time' => 0);
				}
				else
				{
					$sql_data[TOPICS_TABLE]['sql'] += array(
						'topic_attr_id' => $attr_id,
						'topic_attr_user' => (int) $this->user->data['user_id'],
						'topic_attr_time' => time(),
					);
				}
				$event['sql_data'] = $sql_data;

				if (in_array($event['post_mode'], array('edit_topic', 'edit_first_post')))
				{
					$attr_name = $this->qte->get_attr_name_by_id($attr_id);
					$log_data = array(
						'forum_id' => $event['data']['forum_id'],
						'topic_id' => $event['data']['topic_id'],
						$attr_name,
					);

					$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'MCP_ATTRIBUTE_' . ($attr_id == -1 ? 'REMOVED' : 'UPDATED'), time(), $log_data);
				}
			}
		}
	}

	public function delete_user_attributes($event)
	{
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET topic_attr_user = ' . ANONYMOUS . '
			WHERE topic_attr_user = ' . (int) $event['user_ids'];
		$this->db->sql_query($sql);
	}
}
