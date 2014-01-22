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

require_once 'src/Furgovw/Competition.php';
require_once 'src/Furgovw/Competition/Route.php';

$baseUrl  = 'http://www.furgovw.org/concurso/';
$basePath = '/data/www/furgovw/';

if (!file_exists($basePath . 'SSI.php')) {
    throw new Exception("SMF's SSI.php not found");
}

$competition      = new Furgovw\Competition($baseUrl, $basePath);
$competitionRoute = new Furgovw\Competition\Route($competition);

