<?php
/*
 * Competition voting system created for furgovw.org by Javier Montes: @montesjmm / kalimocho@gmail.com
 * http://www.furgovw.org/concurso/
 * Created at October 2013
 */

require_once("Competition.php");

$headers  = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>' . "\n";
$headers .= '<script type="text/javascript" src="' . Competition::BASE_URL . 'js/main.js"></script>' . "\n";
$headers .= '<link rel="stylesheet" type="text/css" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">' . "\n";
$headers .= '<link rel="stylesheet" type="text/css" href="' . Competition::BASE_URL . 'competition.css">' . "\n";

$context['html_headers'] = $headers;

// Loads SMF's SSI
$ssi_layers = array('html', 'body'); // Shows header+menu
require_once('../SSI.php');

if (!empty($db_options['persist']))
    $connection = @mysql_pconnect($db_server, $db_user, $db_passwd);
else
    $connection = @mysql_connect($db_server, $db_user, $db_passwd);

// Something's wrong, show an error if its fatal (which we assume it is)
if (!$connection) {
    if (!empty($db_options['non_fatal'])) {
        return null;
    } else {
        db_fatal_error();
    }
}

// Select the database, unless told not to
mysql_select_db($db_name, $connection);

global $context, $smcFunc, $user_info;

$competition = new Competition($context, $smcFunc, $user_info);
$options     = $competition->get_options();

require 'views/header.php';

if ($competition->is_admin())
{
    if (isset($_POST['new-topic'])) {
        $competition->save_topics();
    } elseif (isset($_POST['new-option'])) {
        $competition->save_options();
        $options = $competition->get_options();
    }

    if (isset($_GET['options'])) {
        $topics             = $competition->topics();
        $categories         = $competition->categories();
        $most_viewed_topics = $competition->most_viewed_topics();
        require 'views/options.php';
        return;
    } elseif (isset($_GET['delete']) && $_GET["delete"] != "") {
        $competition->delete_topic($_GET["delete"]);
        return;
    }
}

if (isset($_POST['vote'])) {
    if ($competition->save_votes()) {
        echo '<div class="alert alert-success">Votos grabados</div>';
    } else {
        echo '<div class="alert alert-danger">Error al grabar los votos</div>';
    }
}

if ((isset($_POST['voting']) && $_POST["voting"]=="si") && ($competition->voting_opened())) {

    $competition->save_votes();

} elseif (isset($_GET['resultado']) && ($competition->is_admin() || $options['ver-resultado'] == "si")) {
    $categoryVotes = $competition->get_votes();

    require 'views/results.php';

} elseif (isset($_GET['votar'])) {

    if ($options['voting-opened'] == 'si') {
        if ($context['user']['is_guest']) {
            echo '<h1>Debes conectarte para poder votar</h1>';
        } else {
            $categories = $competition->categories($options['year']);
            $topics     = $competition->topics();
            $votes      = $competition->votes($context['user']['id']);
            $stats      = $competition->stats();
            require 'views/voting_form.php';
        }
    } else {
        echo '<h1>Las votaciones están cerradas</h1>';
    }

} else {

    require 'views/home.php';

}

require 'views/footer.php';























