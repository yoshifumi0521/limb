<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag route_url
 * @suppress_attributes params route extra skip_controller
 * @package web_app
 * @version $Id: route_url.tag.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class lmbRouteURLTag extends WactRuntimeComponentHTMLTag
{
  function getRenderedTag()
  {
    return 'a';
  }

  function generateBeforeOpenTag($code)
  {
    $href = '$' . $code->getTempVariable();

    $route = '$' . $code->getTempVariable();
    $code->writePhp($route. ' = "";');
    if(isset($this->attributeNodes['route']))
      $code->writePhp($route. ' = "'. $this->attributeNodes['route']->getValue() . '";');

    $params = '$' . $code->getTempVariable();
    $code->writePhp($params . ' = array();');

    if(isset($this->attributeNodes['params']))
    {
      $code->writePhp($params . ' = lmbArrayHelper :: explode(",", ":",');
      $this->attributeNodes['params']->generateExpression($code);
      $code->writePhp(');');
    }

    if($this->getBoolAttribute('skip_controller')) $skip_controller = 'true';
    else $skip_controller = 'false';

    $this->removeAttribute('skip_controller');
    $code->writePhp($href . '= lmbToolkit :: instance()->getRoutesUrl(' . $params . ', ' . $route .', ' . $skip_controller .');');

    $this->removeAttribute('route');
    $this->removeAttribute('params');

    if(isset($this->attributeNodes['extra']))
    {
      $params = '$' . $code->getTempVariable();
      $code->writePhp($params . ' = array();');

      $code->writePhp($href . ' .= ');
      $this->attributeNodes['extra']->generateExpression($code);
      $code->writePhp(';');

      $this->removeAttribute('extra');
    }

    $code->writePhp($this->getComponentRefCode() .
                    '->setAttribute("href", ' . $href . ');');

    parent :: generateBeforeOpenTag($code);
  }
}


