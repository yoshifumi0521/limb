<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cli/src/lmbCliResponse.class.php');
lmb_require('limb/fs/src/lmbFsRecursiveIterator.class.php');
lmb_require('limb/i18n/src/translation/lmbSourceDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbFsDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbDictionary.class.php');

Mock :: generate('lmbSourceDictionaryExtractor', 'MockBaseDictionaryParser');
Mock :: generate('lmbFsRecursiveIterator', 'MockFsRecursiveIterator');

class lmbFsDictionaryExtractorTest extends UnitTestCase
{
  function testLoad()
  {
    $it = new MockFsRecursiveIterator();
    $m1 = new MockBaseDictionaryParser();
    $m2 = new MockBaseDictionaryParser();

    $it->setReturnValueAt(0, 'valid', true);
    $it->setReturnValueAt(0, 'current', $it);
    $it->setReturnValueAt(0, 'isFile', false);

    $file_path1 = 'some.php';
    $file_path2 = 'some.html';

    $it->setReturnValueAt(1, 'valid', true);
    $it->setReturnValueAt(1, 'current', $it);
    $it->setReturnValueAt(1, 'isFile', true);
    $it->setReturnValueAt(0, 'getPathName', $file_path1);

    $it->setReturnValueAt(2, 'valid', true);
    $it->setReturnValueAt(2, 'current', $it);
    $it->setReturnValueAt(2, 'isFile', true);
    $it->setReturnValueAt(1, 'getPathName', $file_path2);

    $loader = new lmbFsDictionaryExtractor();
    $loader->registerFileParser('.php', $m1);
    $loader->registerFileParser('.html', $m2);

    $dictionaries = array();

    $response = new lmbCliResponse();
    $m1->expectOnce('extractFromFile', array($file_path1, $dictionaries, $response));
    $m2->expectOnce('extractFromFile', array($file_path2, $dictionaries, $response));

    $loader->traverse($it, $dictionaries, $response);
  }
}


