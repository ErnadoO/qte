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

class search_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \ernadoo\qte\qte */
	protected $qte;

	/** @var bool */
	private $searc_attr = false;

	/** @var int */
	private $searc_attr_id;

	public function __construct(\phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \ernadoo\qte\qte $qte, \ernadoo\qte\search\fulltext_attribute $qte_search)
	{
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->qte			= $qte;
		$this->qte_search	= $qte_search;
	}

	static public function getSubscribedEvents()
	{
		return array(
			// search
			'core.search_modify_forum_select_list'			=> 'search_select_attributes',
			'core.search_modify_submit_parameters'			=> 'search_alter_show_result',
			'core.search_modify_url_parameters'				=> 'search_add_url_parameter',
			'core.search_modify_tpl_ary'					=> 'search_assign_topic_attributes',

			'core.search_backend_search_after'				=> 'search_modify_keyword_search',

			'core.search_mysql_author_query_before'				=> 'search_author_query_before',
			'core.search_mysql_by_author_modify_search_key'		=> 'search_by_author_modify_search_key',
			'core.search_mysql_keywords_main_query_before'		=> 'search_keywords_main_query_before',

			'core.search_native_author_count_query_before'		=> 'search_author_query_before',
			'core.search_native_by_author_modify_search_key'	=> 'search_by_author_modify_search_key',
			'core.search_native_keywords_count_query_before'	=> 'search_keywords_main_query_before',

			'core.search_postgres_author_count_query_before'	=> 'search_by_author_modify_search_key',
			'core.search_postgres_by_author_modify_search_key'	=> 'search_by_author_modify_search_key',
			'core.search_postgres_keywords_main_query_before'	=> 'search_keywords_main_query_before',
		);
	}

	public function search_assign_topic_attributes($event)
	{
		if (!empty($event['row']['topic_attr_id']))
		{
			$this->qte->get_users_by_topic_id((array)$event['row']['topic_id']);
			$tpl_ary = $event['tpl_ary'];
			$tpl_ary['TOPIC_ATTRIBUTE'] = $this->qte->attr_display($event['row']['topic_attr_id'], $event['row']['topic_attr_user'], $event['row']['topic_attr_time']);
			$event['tpl_ary'] = $tpl_ary;
		}
	}

	public function search_add_url_parameter($event)
	{
		if ($this->searc_attr)
		{
			$event['u_search'] .= '&amp;attr_id=' . $this->searc_attr_id;
		}
	}

	public function search_by_author_modify_search_key($event)
	{
		if ($this->searc_attr)
		{
			$event['firstpost_only'] = true;
		}
	}

	public function search_author_query_before($event)
	{
		if ($this->searc_attr)
		{
			$event['sql_author'] .= ' AND t.topic_attr_id = ' . $this->searc_attr_id;
		}
	}

	public function search_keywords_main_query_before($event)
	{
		if ($this->searc_attr)
		{
			// Fulltext_native
			if (isset($event['sql_where']))
			{
				$event['left_join_topics'] = true;
				$sql_where = $event['sql_where'];
				$sql_where[] = 't.topic_attr_id = ' . (int) $this->searc_attr_id;
				$event['sql_where'] = $sql_where;
			}
			else
			{
				$event['join_topic'] = true;
				$event['sql_match_where'] = ' AND t.topic_attr_id = ' . (int) $this->searc_attr_id;
			}
		}
	}

	public function search_modify_keyword_search($event)
	{
		if ($this->searc_attr)
		{
			$keywords	= utf8_normalize_nfc($this->request->variable('keywords', '', true));
			$author		= $this->request->variable('author', '', true);

			if (!$keywords && !$author)
			{
				$id_ary = $event['id_ary'];
				$start = $event['start'];
				$total_match_count = $event['total_match_count'];
				$total_match_count = $this->qte_search->attribute_search($this->searc_attr_id, $event['show_results'], $event['search_terms'], $event['sort_by_sql'], $event['sort_key'], $event['sort_dir'], $event['sort_days'], $event['ex_fid_ary'], $event['m_approve_posts_fid_sql'], $event['topic_id'], $event['author_id_ary'], $event['sql_author_match'], $id_ary, $start, $event['per_page']);
				$event['total_match_count'] = $total_match_count;
				$event['start'] = $start;
				$event['id_ary'] = $id_ary;
			}
		}
	}

	public function search_alter_show_result($event)
	{
		$topic_attribute = $this->request->variable('attr_id', 0, false, \phpbb\request\request_interface::GET);

		if ($topic_attribute)
		{
			$this->searc_attr = true;
			$this->searc_attr_id = $topic_attribute;
			$event['submit'] = true;
		}
	}

	public function search_select_attributes($event)
	{
		$this->qte->attr_search();
	}
}
