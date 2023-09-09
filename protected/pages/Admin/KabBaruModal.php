<?php
class KabBaruModal extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		/*
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
		*/	
	 }
	 
	 public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDProv->DataSource = PropinsiRecord::finder()->findAll($criteria);
			$this->DDProv->DataBind();
			
			if($this->Request['idProv'] != '')
			{
				$this->DDProv->SelectedValue = $this->Request['idProv'];
				$this->nama->focus();
			}
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
				
		}
	}
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$idUser = $this->User->IsUserNip;
			$idProp = $this->DDProv->SelectedValue;
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_kabupaten WHERE LOWER(nama) = '$nama' AND id_propinsi = '$idProp'";
			
			if(KabupatenRecord::finder()->findBySql($sql))
			{
				$this->Page->CallbackClient->focus($this->nama);
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Nama yang dimasukan sudah ada. \nSilakan ganti dengan nama yang lain.");
				</script>';
				
				
			}
			else
			{
				$idKabBaru = $this->numUrut('tbm_kabupaten',KabupatenRecord::finder(),'3');
				
				$data= new KabupatenRecord();							
				$data->id = $idKabBaru;		
				$data->id_propinsi = $idProp;
				$data->nama = ucwords(trim($this->nama->Text));
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','window.parent.maskContent(); window.parent.modalKabCallback('.$idKabBaru.'); jQuery.FrameDialog.closeDialog();');
				
				//$this->clear();
			}
		}			
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		//$this->clear();
		//$this->Page->CallbackClient->focus($this->nama);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
		
		//$this->getPage()->getClientScript()->registerEndScript
		//('','window.parent.unmaskContent(); window.parent.modalCallback('.$index.'); jQuery.FrameDialog.closeDialog();');
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalKabCallback('.$idKabBaru.'); jQuery.FrameDialog.closeDialog();');
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.KabAdmin'));		
	}
}
?>