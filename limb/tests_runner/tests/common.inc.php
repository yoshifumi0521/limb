<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class GeneratedTestClass
{
  protected $class_name;
  protected $body;

  function __construct($body = null)
  {
    $this->class_name = 'GenClass_' . mt_rand(1, 10000);
    $this->body = $body;
  }

  function getClass()
  {
    return $this->class_name;
  }

  function getFileName()
  {
    return $this->class_name . ".class.php";
  }

  function getOutput()
  {
    return $this->class_name . "\n";
  }

  function generate()
  {
    $code = '';
    $code .= "<?php\n";
    $code .= $this->generateClass();
    $code .= "\n?>";
    return $code;
  }

  function generateFailing()
  {
    $code = '';
    $code .= "<?php\n";
    $code .= $this->generateClassFailing();
    $code .= "\n?>";
    return $code;
  }

  function generateClass()
  {
    if(!$this->body)
      $body = "function testSay() {echo \"" . $this->getOutput() . "\";}";
    else
      $body = $this->body;

    $code = "class {$this->class_name} extends UnitTestCase {
             $body
            }";
    return $code;
  }

  function generateClassFailing()
  {
    $code = "class {$this->class_name} extends UnitTestCase {
              function testSay() {\$this->assertTrue(false);echo \"" . $this->getOutput() . "\";}
            }";
    return $code;
  }
}

abstract class lmbTestRunnerBase extends UnitTestCase
{
  function _rmdir($path)
  {
    if(!is_dir($path))
      return;

    $dir = opendir($path);
    while($entry = readdir($dir))
    {
      if(is_file("$path/$entry"))
        unlink("$path/$entry");
      elseif(is_dir("$path/$entry") && $entry != '.' && $entry != '..')
        $this->_rmdir("$path/$entry");
    }
    closedir($dir);
    $res = rmdir($path);
    clearstatcache();
    return $res;
  }

  function _createTestCase($file, $extra = '', $body = '')
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass($body);
    file_put_contents($file, "<?php\n" . $generated->generateClass() . $extra . "\n?>");
    return $generated;
  }

  function _createTestCaseFailing($file, $extra = '', $body = '')
  {
    $dir = dirname($file);
    if(!is_dir($dir))
      mkdir($dir, 0777, true);

    $generated = new GeneratedTestClass($body);
    file_put_contents($file, "<?php\n" . $generated->generateClassFailing() . $extra . "\n?>");
    return $generated;
  }

  function _runNodeAndAssertOutput($node, $expected)
  {
    ob_start();
    $group = $node->createTestCase();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $expected);
  }
}

