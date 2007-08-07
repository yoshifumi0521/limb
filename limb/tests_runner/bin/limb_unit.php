<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package tests_runner
 * @version $Id: limb_unit.php 6221 2007-08-07 07:24:35Z pachanga $
 */
require_once(dirname(__FILE__) . '/../src/lmbTestShellUI.class.php');

set_time_limit(0);
error_reporting(E_ALL);

$ui = new lmbTestShellUI();
$ui->run();


