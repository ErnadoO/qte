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

class mcp_listener implements EventSubscriberInterface
{
	/** @var \phpbb\request\request */
	protected $request;

	/** @var \ernadoo\qte\qte */
	protected $qte;

	/**
	* Constructor
	*
	* @param \phpbb\request\request					$request			Request object
	* @param \ernadoo\qte\qte						$qte				QTE object
	*/
	public function __construct(\phpbb\request\request $request, \ernadoo\qte\qte $qte)
	{
		$this->request	= $request;
		$this->qte		= $qte;
	}

	static public function getSubscribedEvents()
	{
		return array(
			// MCP
			'core.mcp_view_forum_modify_topicrow'	=> 'assign_topic_attributes_mcp',
			'core.mcp_forum_view_before'			=> 'mcp_select_assign_attributes',
		);
	}

	public function assign_topic_attributes_mcp($event)
	{
		if (!empty($event['row']['topic_attr_id']))
		{
			$this->qte->get_users_by_user_id($event['row']['topic_attr_user']);
			$topic_row = $event['topic_row'];
			$topic_row['MCP_TOPIC_ATTRIBUTE'] = $this->qte->attr_display($event['row']['topic_attr_id'], $event['row']['topic_attr_user'], $event['row']['topic_attr_time']);
			$event['topic_row'] = $topic_row;
		}
	}

	public function mcp_select_assign_attributes($event)
	{
		$attr_id	= (int) $this->request->variable('attr_id', 0);
		$forum_id	= (int) $event['forum_info']['forum_id'];

		if ($attr_id)
		{
			$this->qte->mcp_attr_apply($attr_id, $forum_id, $event['topic_id_list']);
		}

		$this->qte->attr_select($forum_id);
	}
}
