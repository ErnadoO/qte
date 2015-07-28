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

class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \abdev\qte\qte */
	protected $qte;

	public function __construct(\phpbb\request\request $request, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \phpbb\user $user, \phpbb\log\log $log, \abdev\qte\qte $qte)
	{
		$this->request = $request;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->log = $log;
		$this->qte = $qte;
	}

	static public function getSubscribedEvents()
	{
		return array(
			// viewforum
			'core.viewforum_modify_topics_data' => 'viewforum_get_user_infos',
			'core.viewforum_modify_topicrow' => 'viewforum_assign_attribute',

			// viewtopic
			'core.viewtopic_assign_template_vars_before' => 'viewtopic_assign_attribute',

			// posting
			'core.posting_modify_template_vars' => array('posting_select_attributes', 'posting_preview_assign_attribute'),
			'core.posting_modify_submit_post_before' => 'posting_submit_data',
			'core.submit_post_modify_sql_data' => 'posting_save_attribute',
		);
	}

	public function viewforum_get_user_infos($event)
	{
		if (sizeof($event['topic_list']))
		{
			$this->qte->get_users_by_topic_id($event['topic_list']);
		}
	}

	public function viewforum_assign_attribute($event)
	{
		if (!empty($event['row']['topic_attr_id']))
		{
			$topic_row = $event['topic_row'];
			$topic_row['TOPIC_ATTRIBUTE'] = $this->qte->attr_display($event['row']['topic_attr_id'], $event['row']['topic_attr_user'], $event['row']['topic_attr_time']);
			$event['topic_row'] = $topic_row;
		}
	}

	public function viewtopic_assign_attribute($event)
	{
		if (!empty($event['topic_data']['topic_attr_id']))
		{
			$this->qte->get_users_by_topic_id(array($event['topic_data']['topic_id']));
			$this->template->assign_var('TOPIC_ATTRIBUTE', $this->qte->attr_display($event['topic_data']['topic_attr_id'], $event['topic_data']['topic_attr_user'], $event['topic_data']['topic_attr_time']));
		}
	}

	public function posting_select_attributes($event)
	{
		if ($event['mode'] == 'post' || ($event['mode'] == 'edit' && $event['post_id'] == $event['post_data']['topic_first_post_id']))
		{
			$topic_attribute = $this->request->variable('attr_id', 0, false, \phpbb\request\request_interface::POST);
			if (!$event['preview'])
			{
				if (!empty($event['post_data']['topic_attr_id']))
				{
					$topic_attribute = $event['post_data']['topic_attr_id'];
				}
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
	}

	/**
	 * @todo Don't commit now (event missing)
	 */
	public function posting_preview_assign_attribute($event)
	{
		$topic_attribute = $this->request->variable('attr_id', 0, false, \phpbb\request\request_interface::POST);
		$current_time = time();

		if (($event['mode'] == 'edit') && !empty($topic_attribute) && ($event['post_id'] == $event['post_data']['topic_first_post_id']))
		{
			$post_data = $event['post_data'];

			if ($event['preview'])
			{
				if ($topic_attribute != $post_data['topic_attr_id'])
				{
					$post_data['topic_attr_id'] = (int) $topic_attribute;
					$post_data['topic_attr_user'] = (int) $this->user->data['user_id'];
					$post_data['topic_attr_time'] = (int) $current_time;
				}
			}

			if ($post_data['topic_attr_id'] != -1)
			{
				$this->qte->get_users_by_topic_id(array($post_data['topic_id']));
				$this->template->assign_var('TOPIC_ATTRIBUTE', $this->qte->attr_display($post_data['topic_attr_id'], $post_data['topic_attr_user'], $post_data['topic_attr_time']));
			}

			$event['post_data'] = $post_data;
		}
	}

	public function posting_submit_data($event)
	{
		$post_data = $event['post_data'];
		$post_data['attr_id'] = $this->request->variable('attr_id', 0, false, \phpbb\request\request_interface::POST);

		if ($post_data['attr_id'] != \abdev\qte\qte::KEEP)
		{
			if (!empty($event['post_data']['topic_attr_id']))
			{
				if (empty($post_data['attr_id']))
				{
					$post_data['attr_id'] = $event['post_data']['topic_attr_id'];
				}
			}
		}
		$event['post_data'] = $post_data;

		$data = $event['data'];
		$data['attr_id'] = (int) (($event['mode'] == 'post') && !empty($event['post_data']['default_attr'])) ? $event['post_data']['default_attr'] : $event['post_data']['attr_id'];
		$event['data'] = $data;
	}

	public function posting_save_attribute($event)
	{
		if (in_array($event['post_mode'], array('post', 'edit_topic', 'edit_first_post')))
		{
			if ($event['data']['attr_id'] != \abdev\qte\qte::KEEP)
			{
				$sql_data = $event['sql_data'];
				if ($event['data']['attr_id'] == \abdev\qte\qte::REMOVE)
				{
					$sql_data[TOPICS_TABLE]['sql'] += array('topic_attr_id' => 0, 'topic_attr_user' => 0, 'topic_attr_time' => 0);
				}
				else
				{
					$sql_data[TOPICS_TABLE]['sql'] += array(
						'topic_attr_id' => $event['data']['attr_id'],
						'topic_attr_user' => (int) $this->user->data['user_id'],
						'topic_attr_time' => time(),
					);
				}
				$event['sql_data'] = $sql_data;

				if (in_array($event['post_mode'], array('edit_topic', 'edit_first_post')))
				{
					$attr_name = $this->qte->get_attr_name_by_id($event['data']['attr_id']);
					$log_data = array(
						'forum_id' => $event['data']['forum_id'],
						'topic_id' => $event['data']['topic_id'],
						$attr_name,
					);

					$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'MCP_ATTRIBUTE_' . ($event['data']['attr_id'] == \abdev\qte\qte::REMOVE ? 'REMOVED' : 'UPDATED'), time(), $log_data);
				}
			}
		}
	}
}
