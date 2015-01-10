<?php
/*
 * This file is part of the Competition voting system created for furgovw.org
 * by Javier Montes: @montesjmm / kalimocho@gmail.com / montesjmm.com
 *
 * http://www.furgovw.org/concurso/
 *
 * Started at October 2013
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Furgovw;

class Competition
{
	protected $options;
	protected $smcFunc;
	protected $context;
	protected $user_info;
	protected $baseUrl;
	protected $basePath;

	public function __construct($baseUrl, $basePath)
	{
		$this->baseUrl  = $baseUrl;
		$this->basePath = $basePath;
	}

	public function setupSmf()
	{
		$headers  = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>' . "\n";
		$headers .= '<script type="text/javascript" src="' . $this->baseUrl . 'js/main.js"></script>' . "\n";
		$headers .= '<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">' . "\n";
		$headers .= '<link rel="stylesheet" type="text/css" href="' . $this->baseUrl . 'css/competition.css">' . "\n";

		global $context;
		$context['html_headers'] = $headers;

		$ssi_layers = array('html', 'body'); // Shows smf's header+menu

		require_once($this->basePath . 'SSI.php');

		if (!empty($db_options['persist']))
			$connection = @mysql_pconnect($db_server, $db_user, $db_passwd);
		else
			$connection = @mysql_connect($db_server, $db_user, $db_passwd);

		if (!$connection) {
			if (!empty($db_options['non_fatal'])) {
				return null;
			} else {
				db_fatal_error();
			}
		}

		mysql_select_db($db_name, $connection);
		$result = $smcFunc['db_query']('', "SET NAMES UTF8");
	}

	public function setupSmfGlobals()
	{
		global $smcFunc, $user_info, $context;

		$this->smcFunc   = $smcFunc;
		$this->context   = $context;
		$this->user_info = $user_info;
	}

	function getOptions($optionSlug = false)
	{
		return ($optionSlug ? $this->options[$optionSlug] : $this->options);
	}

	function getContext()
	{
		return $this->context;
	}

	function getBaseUrl()
	{
		return $this->baseUrl;
	}

	function getBasePath()
	{
		return $this->basePath;
	}

	function year()
	{
		return $this->options['year'];
	}

	function userIsAdmin()
	{
		//Checks if user is in any of some user groups
		if (
			(in_array("1", $this->user_info['groups'])) ||
			(in_array("9", $this->user_info['groups'])) ||
			(in_array("2", $this->user_info['groups'])) ||
			$this->context['user']['id'] == '3140' ||
			$this->context['user']['id'] == '9994' ||
			$this->context['user']['id'] == '5862' ||
			$this->context['user']['id'] == '3078'
			) {
			return true;
		} else {
			return false;
		}
	}

	function loadOptions()
	{
		$smcFunc = $this->smcFunc;

		$result = $smcFunc['db_query']('', "
			SELECT `value` FROM fcompetition_options
			WHERE `option` = 'options'
			");
		$row = $smcFunc['db_fetch_assoc']($result);
		$this->options = unserialize($row["value"]);
	}

	function saveOptions()
	{
		if (isset($_POST['new-option']) && $_POST['new-option'] != '') {
			$this->options[$_POST['new-option']] = $_POST['new-option-value'];
		}

		foreach ($this->options as $index => $value) {
			if (isset($_POST[$index]) && $_POST[$index] != '') {
				$this->options[$index] = $_POST[$index];
			}
		}

		$smcFunc = $this->smcFunc;

		$result = $smcFunc['db_query']('', "
			UPDATE fcompetition_options SET value = '" . serialize($this->options) . "' WHERE option = 'options'
			"
		);
	}

	function votingOpened()
	{
		if ($this->options['voting-opened'] == 'yes')
			return true;
		else
			return false;
	}

	function deleteTopic($id)
	{
		if (!is_numeric($id)) {
			return false;
		}

		$smcFunc = $this->smcFunc;

		$sql = "DELETE FROM fcompetition_topics WHERE id='$id'";

		return $smcFunc['db_query']('', $sql);
	}

	function topics($year = false)
	{
		$smcFunc = $this->smcFunc;
		if (!is_numeric($year)) {
			$year = $this->options['year'];
		}

		$result = $smcFunc['db_query']('', "
			SELECT h.*,
			m.subject as title,
			m.id_member as author_id,
			t.id_topic as id_topic,
			e.member_name as moderator,
			c.name as category,
			h.category_id
			FROM fcompetition_topics h
			LEFT JOIN smf_topics t
			ON h.topic_id = t.id_topic
			LEFT JOIN smf_messages m
			ON t.id_first_msg = m.id_msg
			LEFT JOIN smf_members e
			ON h.moderator_member_id = e.id_member
			LEFT JOIN fcompetition_categories c
			ON h.category_id = c.id
			WHERE
			h.year = '".$year."'
			ORDER BY RAND()
			");

		$topics = array();
		while ($row = $smcFunc['db_fetch_assoc']($result)) {

			$author = false;
			$result2 = $smcFunc['db_query']('', "SELECT * FROM smf_members WHERE id_member = ".$row['author_id']);
			$author  = $smcFunc['db_fetch_assoc']($result2);

			$rowObj = new \stdClass();
			$rowObj->title = $row['title'];
			$rowObj->author_id = $row['author_id'];
			if ($author) {
				$rowObj->author = $author['member_name'];
			}
			$rowObj->url = 'http://www.furgovw.org/index.php?topic='.$row['id_topic'];
			$rowObj->moderator = $row['moderator'];
			$rowObj->category = $row['category'];
			$rowObj->id = $row['id'];
			$rowObj->date = date('d/m/Y H:i', strtotime($row['date']));
			$rowObj->category_id = $row['category_id'];
			$rowObj->topic_id = $row['topic_id'];

			$topics[] = $rowObj;
		}

		return $topics;
	}

	function categories($year=false)
	{
		$smcFunc = $this->smcFunc;

		$sql = "SELECT c.*
				FROM fcompetition_categories c ";
		if (is_numeric($year)) {
			$sql .= 'LEFT JOIN fcompetition_topics h
				ON c.id = h.category_id
				WHERE h.year = "' . $year . '"
				GROUP BY c.id';
		}

		$result = $smcFunc['db_query']('', $sql);

		$categories = array();
		while ($row = $smcFunc['db_fetch_assoc']($result)) {
			$rowObj = new \stdClass();
			$rowObj->id     = $row['id'];
			$rowObj->name   = $row['name'];
			$rowObj->boards = $row['boards'];

			$categories[] = $rowObj;
		}

		return $categories;
	}

	function saveTopics()
	{
		if (!isset($_POST['new-topic']) || !isset($_POST['category'])) {
			return false;
		}

		$topics = $_POST['new-topic'];
		for ($cont=0;$cont<=9;$cont++) {

			$topic = false;
			if (is_numeric($_POST['category']) && $_POST['new-topic'][$cont] != '') {
				preg_match('%topic\=(\d+)%i', $_POST['new-topic'][$cont], $matches);
				if (isset($matches[1])) {
					$topic = $matches[1];
				}
			}

			if (is_numeric($topic)) {
				$smcFunc = $this->smcFunc;

				$smcFunc['db_query']('', "
					INSERT INTO fcompetition_topics
					SET
					topic_id            = '".$topic."',
					moderator_member_id = '".$this->context['user']['id']."',
					category_id         = '".$_POST['category']."',
					year                = '".$this->options['year']."',
					date                = NOW()
					");
			}
		}

		return true;
	}

	function mostViewedTopics()
	{
		$smcFunc    = $this->smcFunc;
		$categories = $this->categories();

		foreach ($categories as $category) {
			$most_viewed_topics[$category->name] = array();

			$boards = $this->getBoardsAndBoardsChilds($category->boards);

			$result = $smcFunc['db_query']('', "
				SELECT m.*, u.member_name, t.num_views
				FROM smf_topics t
				LEFT JOIN smf_messages m
				ON t.id_first_msg = m.id_msg
				LEFT JOIN smf_members u
				ON m.id_member = u.id_member
				WHERE m.poster_time >= ".strtotime($this->options['from'])."
				AND m.poster_time <= ".strtotime($this->options['to'])."
				AND m.id_board IN (".implode(',', $boards).")
				ORDER BY t.num_views DESC
				LIMIT 150
				");


			while ($row = $smcFunc['db_fetch_assoc']($result)) {
				$rowObj = new \stdClass();
				$rowObj->id_msg      = $row['id_msg'];
				$rowObj->id_topic    = $row['id_topic'];
				$rowObj->id_board    = $row['id_board'];
				$rowObj->date        = date('d/m/Y', $row['poster_time']);
				$rowObj->id_member   = $row['id_member'];
				$rowObj->member      = $row['member_name'];
				$rowObj->subject     = $row['subject'];
				$rowObj->poster_name = $row['poster_name'];
				$rowObj->num_views   = number_format($row['num_views'], 0, ',', '.');
				$rowObj->url         = 'http://www.furgovw.org/index.php?topic='.$row['id_topic'];

				$most_viewed_topics[$category->name][] = $rowObj;
			}
		}

		return $most_viewed_topics;
	}

	function getBoardsAndBoardsChilds($boards)
	{
		if (!is_array($boards) && is_numeric($boards)) {
			$boards = array($boards);
		} else {
			$boards            = explode(',', $boards);
		}
		if (!is_array($boards)) {
			return false;
		}

		$boards_and_childs = $boards;

		foreach ($boards as $board) {
			$boards_and_childs = array_merge($boards_and_childs, $this->getBoardChilds($board));
		}
		$boards_and_childs = array_unique($boards_and_childs);

		return $boards_and_childs;
	}

	function getBoardChilds($board)
	{
		$childs = array();

		$smcFunc = $this->smcFunc;
		$result = $smcFunc['db_query']('', "
			SELECT id_board, child_level
			FROM smf_boards
			WHERE id_parent = ".$board."
			");
		while ($row = $smcFunc['db_fetch_assoc']($result)) {
			$childs[] = $row['id_board'];
			if ($row['child_level'] < 4) {
				$more_childs = $this->getBoardChilds($row['id_board']);
				if ($more_childs) {
					$childs = array_merge($childs, $more_childs);
				}
			}
		}

		return $childs;
	}

	function votes($member_id)
	{
		if (!is_numeric($member_id)) {
			return false;
		}

		$votes = array();
		$smcFunc = $this->smcFunc;
		$result = $smcFunc['db_query']('', "
			SELECT *
			FROM fcompetition_votes
			WHERE member_id = ".$member_id."
			AND year = ".$this->options['year']."
			");
		while ($row = $smcFunc['db_fetch_assoc']($result)) {
			$votes[$row['topic_id']] = true;
		}

		return $votes;
	}

	function saveVotes()
	{
		if (!isset($this->context['user']['id']) || $this->context['user']['id'] < 1) {
			die('ERROR: Debes estar conectado para participar');
		}

		$smcFunc    = $this->smcFunc;
		$categories = $this->categories($this->options['year']);

		foreach ($categories as $category) {
			if (isset($_POST['category'.$category->id]) && is_numeric(($_POST['category'.$category->id]))) {
				$result = $smcFunc['db_query']('', "
					SELECT v.*
					FROM fcompetition_votes v
					LEFT JOIN fcompetition_topics h
					ON v.topic_id = h.topic_id
					WHERE v.member_id = ".$this->context['user']['id']."
					AND v.year = ".$this->options['year']."
					AND h.category_id = ".$category->id."
					");
				$row = $smcFunc['db_fetch_assoc']($result);

				if ($row) {
					$smcFunc['db_query']('', "
						UPDATE fcompetition_votes
						SET topic_id = ".$_POST['category'.$category->id]."
						WHERE id = ".$row['id']."
						");
				} else {
					$smcFunc['db_query']('', "
						INSERT INTO fcompetition_votes
						SET member_id = ".$this->context['user']['id'].",
						year = ".$this->options['year'].",
						topic_id = ".$_POST['category'.$category->id].",
						date = NOW()
						");
				}
			}
		}

		return true;
	}


	function getVotes($year = false, $onlyWinners = false)
	{
		$smcFunc    = $this->smcFunc;
		$categories = $this->categories();
		$votes      = array();
		$yearSql    = '';

		if ((!$year || !is_numeric($year)) &&
			$year != 'all') {

			$year = $this->options['year'];
			$yearSql = " AND fcompetition_votes.year = '".$year."' ";
		}

		foreach ($categories as $category) {

			$votes[$category->name] = array();

			$sql = "
				SELECT fcompetition_votes.topic_id,
					 COUNT(fcompetition_votes.topic_id) as votes,
					 m.subject,
					 m.poster_name,
					 m.poster_time,
					 m.id_member,
					 u.member_name
				 FROM fcompetition_votes
				 LEFT JOIN fcompetition_topics
				 ON fcompetition_votes.topic_id = fcompetition_topics.topic_id
				 LEFT JOIN smf_topics t
				 ON fcompetition_votes.topic_id = t.id_topic
				 LEFT JOIN smf_messages m
				 ON t.id_first_msg = m.id_msg
				 LEFT JOIN smf_members u
				 ON m.id_member = u.id_member
				 WHERE fcompetition_topics.category_id = " . $category->id . "
				 " . $yearSql . "
				 GROUP BY fcompetition_votes.topic_id
				 ORDER BY votes DESC, m.poster_time ASC";

			if ($onlyWinners) {
				$sql .= " LIMIT 3";
			}

			$result = $smcFunc['db_query']('', $sql);

			while ($row = $smcFunc['db_fetch_assoc']($result)) {
				$rowObj = new \stdClass();
				$rowObj->id_topic    = $row['topic_id'];
				$rowObj->votes       = $row['votes'];
				$rowObj->date        = date('d/m/Y', $row['poster_time']);
				$rowObj->id_member   = $row['id_member'];
				$rowObj->member      = $row['member_name'];
				$rowObj->subject     = $row['subject'];
				$rowObj->poster_name = $row['poster_name'];
				$rowObj->url         = 'http://www.furgovw.org/index.php?topic='.$row['topic_id'];

				$votes[$category->name][] = $rowObj;
			}
		}

		return $votes;
	}

	public function isUserWinner($memberId, $year = false)
	{
		$winnersCategories = $this->getVotes('all', true);

		foreach ($winnersCategories as $winners) {
			foreach ($winners as $winner) {
				if ($winner->id_member == $memberId) {
					return true;
				}
			}
		}

		return false;
	}

	public function yearTopicWinner($topicId)
	{
		for ($year=2013; $year<=date('Y'); $year++) {
			$winnersCategories = $this->getVotes($year, true);

			foreach ($winnersCategories as $winners) {
				foreach ($winners as $winner) {
					if ($winner->id_topic == $topicId) {
						return $year;
					}
				}
			}
		}

		return false;
	}

	public function stats()
	{
		$stats = new \stdClass();

		$smcFunc = $this->smcFunc;
		$result = $smcFunc['db_query']('', "
			SELECT count(distinct member_id) as totalMembers
			FROM fcompetition_votes
			WHERE year = ".$this->options['year']."
			");
		$row = $smcFunc['db_fetch_assoc']($result);

		if ($row) {
			$stats->totalMembers = $row['totalMembers'];
		}

		return $stats;
	}

    public function userAddTopic()
    {
        $smcFunc = $this->smcFunc;

        if (isset($_GET['add-topic']) &&
            is_numeric($_GET['add-topic']) &&
            !$this->context['user']['is_guest']) {

            $idBoard = false;

            $result = $smcFunc['db_query']('', "
                SELECT id_board
                FROM smf_topics
                WHERE id_topic = ".(int)$_GET['add-topic']."
                ");
            $row = $smcFunc['db_fetch_assoc']($result);
            if ($row) {
                $idBoard = $row['id_board'];
            }

            if ($idBoard) {

                $result = $smcFunc['db_query']('', "
                     SELECT * FROM fcompetition_categories
                     ");
                $thisBoardIsInCompetition = false;

                if ($smcFunc['db_num_rows']($result) > 0) {
                    while ($row = $smcFunc['db_fetch_assoc']($result)) {
                        $boards = explode(',', $row['boards']);
                        foreach ($boards as $board) {
                            if ($idBoard == $board || in_array($idBoard, $this->getBoardChilds($board))) {
                                $thisBoardIsInCompetition = true;
                                $categoryId = $row['id'];
                            }
                        }
                    }
                }

                if ($thisBoardIsInCompetition && $categoryId) {
                    $smcFunc['db_query']('', "
                        DELETE
                        FROM fcompetition_proposed_topics
                        WHERE category_id = ".$categoryId."
                        AND member_id = ".$this->context['user']['id']."
                        AND year = ".date('Y')."
                        ");

                    $smcFunc['db_query']('', "
                        INSERT INTO fcompetition_proposed_topics
                        SET topic_id = ".(int)$_GET['add-topic'].",
                        member_id = ".$this->context['user']['id'].",
                        category_id = ".$categoryId.",
                        year = ".date('Y').",
                        date = NOW()
                        ");

                    echo '<h1>Tema añadido, gracias por participar.</h1>';
                    echo '<script>setTimeout(function(){ window.location.href="'.$_GET['back'].'"; }, 3000)</script>';
                }
            }
        }
    }

    public function proposedTopicsStats()
    {
        $smcFunc = $this->smcFunc;
        $stats = array();

        $result = $smcFunc['db_query']('', "
            SELECT count(distinct(topic_id)) as total
            FROM fcompetition_proposed_topics
            ");
        $row = $smcFunc['db_fetch_assoc']($result);

        $stats['totalProposedTopics'] = $row['total'];

        $result = $smcFunc['db_query']('', "
            SELECT p.*, m.subject, c.name as category_name
            FROM fcompetition_proposed_topics p
            LEFT JOIN fcompetition_categories c
            ON p.category_id = c.id
            LEFT JOIN smf_topics t
            ON p.topic_id = t.id_topic
            LEFT JOIN smf_messages m
            ON t.id_first_msg = m.id_msg
            WHERE p.year = '".date('Y')."'
            GROUP BY topic_id
            ORDER BY category_id ASC, p.id ASC
            ");
        while ($row = $smcFunc['db_fetch_assoc']($result)) {
            $stats['proposedTopics'][] = $row;
        }


        $stats['yourProposedTopics']  = array();

        if (!$this->context['user']['is_guest']) {
            $result = $smcFunc['db_query']('', "
                SELECT p.*, m.subject, c.name as category_name
                FROM fcompetition_proposed_topics p
                LEFT JOIN fcompetition_categories c
                ON p.category_id = c.id
                LEFT JOIN smf_topics t
                ON p.topic_id = t.id_topic
                LEFT JOIN smf_messages m
                ON t.id_first_msg = m.id_msg
                WHERE p.member_id = ".$this->context['user']['id']."
                AND p.year = '".date('Y')."'
                ORDER BY p.id ASC
                ");
            while ($row = $smcFunc['db_fetch_assoc']($result)) {
                $stats['yourProposedTopics'][] = $row;
            }
        }

        return $stats;
    }

}


