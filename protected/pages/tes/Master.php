<?php
	//Prado::using('Application.Custom.TActiveDataGrid');
	//Prado::using('Application.modules.PWCWindow.PWCWindow');
	Prado::using('System.Web.UI.ActiveControls.TActiveLabel');
	Prado::using('System.Web.UI.ActiveControls.TActiveButton');
	Prado::using('System.Web.UI.ActiveControls.TActiveLinkButton');
	Prado::using('System.Web.UI.ActiveControls.TCallback');
	class Master extends TPage
	{
		private $data=array(
								array('id'=>0,'name'=>'Syd','surname'=>'Barrett','born'=>1946),
								array('id'=>1,'name'=>'David','surname'=>'Gilmour','born'=>1946),
								array('id'=>2,'name'=>'Roger','surname'=>'Waters','born'=>1943),
								array('id'=>3,'name'=>'Nick','surname'=>'Mason','born'=>1944),
								array('id'=>4,'name'=>'Richard','surname'=>'Wright','born'=>1943)
							);
		function showDetails($sender,$param)
		{
			if ( $sender->Id==='UpdateCallback' ) //the callback is made using TCallback control (Method 2)
			{
				$id=$param->CallbackParameter->Id;
			}
			else //the callback is triggered by a button from Grid (Method 1)
			{
				//clear the grid so that it doesn't make viewstate huge
				$this->ChoiceGrid->DataSource=null;
				$this->ChoiceGrid->dataBind();
				
				$id=$param->CommandParameter;
			}
			
			//now a query should be run based on $id
			//...
			
			$this->Name->Text=$this->data[$id]['name'];
			$this->Surname->Text=$this->data[$id]['surname'];
			$this->Year->Text=$this->data[$id]['born'];
			$id=$this->Window1->getClientID();
			$this->getPage()->getClientScript()->registerEndScript('closeW1',"Windows.close('$id');");
		}
		function Button1_Clicked()
		{
			//here a query should get the data for databinding
			$this->ChoiceGrid->DataSource=$this->data;
			$this->ChoiceGrid->dataBind();
			$id=$this->Window1->getClientID();
			$this->getPage()->getClientScript()->registerEndScript('openW1',"Windows.showCenter('$id',true);");
		}
		
		function tesClicked()
		{
			$this->tes2->Text = 'rw';
		}
		
		public function tesCallback($sender,$param)	
		{		
			//$this->karyawanPanel->render($param->getNewWriter());	
			$this->tesPanel->render($param->getNewWriter());			
		}
	}
?>