<?php
	Prado::using('System.Web.UI.ActiveControls.TActiveLinkButton');
	class Slave extends TPage
	{
		private $data=array(
								array('id'=>0,'name'=>'Syd','surname'=>'Barrett','born'=>1946),
								array('id'=>1,'name'=>'David','surname'=>'Gilmour','born'=>1946),
								array('id'=>2,'name'=>'Roger','surname'=>'Waters','born'=>1943),
								array('id'=>3,'name'=>'Nick','surname'=>'Mason','born'=>1944),
								array('id'=>4,'name'=>'Richard','surname'=>'Wright','born'=>1943)
							);
		function OnLoad()
		{
			$this->ChoiceGrid->DataSource=$this->data;
			$this->ChoiceGrid->dataBind();
		}
	}
	
	//tes tes
?>