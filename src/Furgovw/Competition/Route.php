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

namespace Furgovw\Competition;

class Route
{
	protected $competition;
	protected $options;

	public function __construct(\Furgovw\Competition $competition)
	{
		$this->competition = $competition;
		$this->options     = $competition->getOptions();

		require 'views/header.php';

		$this->start();

		require 'views/footer.php';
	}

	protected function start()
	{
		if ($this->competition->userIsAdmin()
			&&
			(isset($_POST['new-topic']) ||
			 isset($_POST['new-option']) ||
			 isset($_GET['options']) ||
			 isset($_GET['delete']))) {

			$this->routeAdmin();

		} elseif (isset($_POST['vote']) ||
			isset($_POST['voting']) ||
			isset($_GET['results']) ||
			isset($_GET['dovote'])
			) {

			$this->routeUser();

		} elseif (!$_GET) {
			require 'views/home.php';
		} else {
			throw new \Exception("404 error");
		}
	}

	protected function routeAdmin()
	{
		if (isset($_POST['new-topic'])) {
			$this->competition->saveTopics();
		} elseif (isset($_POST['new-option'])) {
			$this->competition->saveOptions();
			$this->options = $this->competition->getOptions();
		}

		if (isset($_GET['options'])) {
			$topics             = $this->competition->topics();
			$categories         = $this->competition->categories();
			$most_viewed_topics = $this->competition->mostViewedTopics();
			require 'views/options.php';
			return;
		} elseif (isset($_GET['delete']) && $_GET["delete"] != "") {
			$this->competition->deleteTopic($_GET["delete"]);
		}

		require 'views/home.php';
	}

	protected function routeUser()
	{
		if (isset($_POST['vote']) ||
			isset($_POST['voting']) ||
			isset($_GET['dovote'])) {

			$this->routeVotes();

		} elseif (isset($_GET['results']) &&
			($this->competition->userIsAdmin() ||
			$this->options['view-results'] == "yes")) {

			$categoryVotes = $this->competition->getVotes();
			require 'views/results.php';
		}
	}

	protected function routeVotes()
	{
		if (isset($_POST['vote'])) {
			if ($this->competition->saveVotes()) {
				require 'views/votes_saved.php';
			} else {
				require 'views/votes_not_saved.php';
			}
		}

		if (isset($_GET['dovote'])) {

			if ($this->competition->votingOpened()) {
				$context = $this->competition->getContext();

				if ($context['user']['is_guest']) {
					require 'views/guest.php';
				} else {
					$categories = $this->competition->categories($this->options['year']);
					$topics     = $this->competition->topics();
					$votes      = $this->competition->votes($context['user']['id']);
					$stats      = $this->competition->stats();
					require 'views/voting_form.php';
				}
			} else {
				require 'views/votings_closed.php';
			}
		}
	}
}


