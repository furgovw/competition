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
		$this->options     = $competition->get_options();

		require 'views/header.php';

		$this->start();

		require 'views/footer.php';
	}

	protected function start()
	{
		if ($this->competition->user_is_admin()
			&&
			(isset($_POST['new-topic']) ||
			 isset($_POST['new-option']) ||
			 isset($_GET['options']) ||
			 isset($_GET['delete']))) {

			$this->routeAdmin();

		} elseif (isset($_POST['vote']) ||
			isset($_POST['voting']) ||
			isset($_GET['resultado']) ||
			isset($_GET['votar'])
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
			$this->competition->save_topics();
		} elseif (isset($_POST['new-option'])) {
			$this->competition->save_options();
			$this->options = $this->competition->get_options();
		}

		if (isset($_GET['options'])) {
			$topics             = $this->competition->topics();
			$categories         = $this->competition->categories();
			$most_viewed_topics = $this->competition->most_viewed_topics();
			require 'views/options.php';
			return;
		} elseif (isset($_GET['delete']) && $_GET["delete"] != "") {
			$this->competition->delete_topic($_GET["delete"]);
		}

		require 'views/home.php';
	}

	protected function routeUser()
	{
		if (isset($_POST['vote']) ||
			isset($_POST['voting']) ||
			isset($_GET['votar'])) {

			$this->routeVotes();

		} elseif (isset($_GET['resultado']) &&
			($this->competition->user_is_admin() ||
			$this->options['ver-resultado'] == "si")) {

			$categoryVotes = $this->competition->get_votes();
			require 'views/results.php';
		}
	}

	protected function routeVotes()
	{
		if (isset($_POST['vote'])) {
			if ($this->competition->save_votes()) {
				require 'views/votes_saved.php';
			} else {
				require 'views/votes_not_saved.php';
			}
		}

		if ((isset($_POST['voting']) &&
			$_POST["voting"]=="si")
			&& ($this->competition->voting_opened())) {

			$this->competition->save_votes();

		} elseif (isset($_GET['votar'])) {

			if ($this->options['voting-opened'] == 'si') {
				$context = $this->competition->get_context();

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


