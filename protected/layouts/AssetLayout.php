<?php
class AssetLayout extends TTemplateControl
{
   public function logoutClicked($sender,$param)
    {
        $this->Application->getModule('auth')->logout();
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
}
?>
