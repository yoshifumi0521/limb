<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * The opposite of the CoreDefaultTag
 * @tag core:OPTIONAL
 * @req_attributes for
 * @convert_to_expression for
 * @package wact
 * @version $Id: optional.tag.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class WactCoreOptionalTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $tempvar = $code->getTempVariable();
    $code->writePHP('$' . $tempvar . ' = ');
    $this->attributeNodes['for']->generateExpression($code);
    $code->writePHP(';');

    $code->writePHP('if (is_scalar($' . $tempvar .' )) $' . $tempvar . '= trim($' . $tempvar . ');');
    $code->writePHP('if (!empty($' . $tempvar . ')){');

    parent :: generateTagContent($code);

    $code->writePHP('}');
  }
}

