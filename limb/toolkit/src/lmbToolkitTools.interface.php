<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Interface for defining toolkit tools that extends lmbToolkit
 * @see lmbToolkit
 * @package toolkit
 * @version $Id: lmbToolkitTools.interface.php 6221 2007-08-07 07:24:35Z pachanga $
 */
interface lmbToolkitTools
{
  /**
  * @return array Array of method names with reference to itself, something like array('getUser' => $this, 'getTree' => $this)
  */
  function getToolsSignatures();
}

