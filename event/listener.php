<?php
//
//	file: ext/abdev/qte/event/listener.php
//	author: abdev
//	author: pastisd
//	begin: 08/19/2014
//	version: 0.0.1 - 08/19/2014
//	licence: http://opensource.org/licenses/gpl-license.php GNU Public License
//

// essential
namespace abdev\qte\event;

// ignore
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

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var \abdev\qte\qte */
	protected $qte;

	/** @var string */
	protected $table_prefix;

	public function __construct(\phpbb\request\request $request, \phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\template\template $template, \abdev\qte\qte $qte, $root_path, $php_ext, $table_prefix)
	{
		$this->request = $request;
		$this->cache = $cache;
		$this->db = $db;
		$this->template = $template;
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

			'core.viewtopic_assign_template_vars_before' => 'viewtopic_assign_template_vars_before_complement',
			'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row_complement',
			'core.viewtopic_modify_page_title' => 'viewtopic_modify_page_title_complement',

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
		if ( !empty($event['forum_data']['hide_attr']) )
		{
			$event['forum_data']['hide_attr'] = serialize($event['forum_data']['hide_attr']);
		}
		else
		{
			$event['forum_data']['hide_attr'] = '';
		}
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
		if (!empty($topic_data['topic_attr_id']))
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
		$topic_data['topic_title'] = $attribute_title . ' ' . $event['topic_data']['topic_title'];
		$event['topic_data'] = $topic_data;
	}

	public function delete_user_attributes($event)
	{
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET topic_attr_user = ' . ANONYMOUS . '
			WHERE topic_attr_user = ' . (int) $event['user_ids'];
		$this->db->sql_query($sql);
	}
}
