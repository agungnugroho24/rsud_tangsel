<?php
/**
 * CommentPortlet class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: CommentPortlet.php 1398 2006-09-08 19:31:03Z xue $
 */

Prado::using('Application.Portlets.Portlet');

/**
 * CommentPortlet class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 */
class SidePortletAsset extends Portlet
{
	 public function logoutButtonClicked($sender,$param)
    {
        $this->Application->getModule('auth')->logout();		
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
	
	 public function simakButtonClicked($sender,$param)
    {
        $url=$this->Service->constructUrl('Admin.AdminFormulir');
		$this->Response->redirect($url);
    }
}

?>