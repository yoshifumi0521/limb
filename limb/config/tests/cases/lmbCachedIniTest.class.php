<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require(dirname(__FILE__) . '/lmbIniTest.class.php');

class lmbCachedIniTest extends lmbIniTest
{
  var $cache_dir;

  function setUp()
  {
    parent :: setUp();

    $this->cache_dir = LIMB_VAR_DIR . '/ini/';
    lmbFs :: rm($this->cache_dir);
  }

  function _createIni($contents)
  {
    file_put_contents($file = LIMB_VAR_DIR . '/tmp_ini/' . mt_rand() . '.ini', $contents);
    return new lmbCachedIni($file, $this->cache_dir);
  }
}


