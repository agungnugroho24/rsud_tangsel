<?php
class KabEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
		
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDProv->DataSource = PropinsiRecord::finder()->findAll($criteria);
			$this->DDProv->DataBind();
			
			$this->ambilData();			
			$this->nama->focus();
		}
	}
	
	protected function getKabupatenRecord()
	{
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KabupatenRecord=KabupatenRecord::finder()->findByPk($ID);		
		if(!($KabupatenRecord instanceof KabupatenRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KabupatenRecord;
	}
	
	public function ambilData()
	{	
		$KabupatenRecord = $this->KabupatenRecord;
		
		$this->nama->Text = $KabupatenRecord->nama;
		$this->DDProv->SelectedValue = $KabupatenRecord->id_propinsi;
		$this->setViewState('namaAwal',$KabupatenRecord->nama);
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
				if($nama == strtolower(trim($this->getViewState('namaAwal'))))
				{
					$data= $this->KabupatenRecord;							
					$data->id_propinsi = $idProp;
					$data->nama = ucwords(trim($this->nama->Text));
					$data->save();	
					
					$this->msg->Text = '    
					<script type="text/javascript">
						alert("Data telah diperbaharui !");
						window.location="index.php?page=Admin.KabAdmin"; 
					</script>';
				}
				else
				{
					$this->Page->CallbackClient->focus($this->nama);
					$this->msg->Text = '    
					<script type="text/javascript">
						alert("Nama yang dimasukan sudah ada. \nSilakan ganti dengan nama yang lain.");
					</script>';
				}
			}
			else
			{
				$data= $this->KabupatenRecord;							
				
				$data->id_propinsi = $idProp;
				$data->nama = ucwords(trim($this->nama->Text));
				$data->save();	
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data telah diperbaharui !");
					window.location="index.php?page=Admin.KabAdmin"; 
				</script>';
			}
		}	
	}
	
	public function clear()
	{	
		$this->nama->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->clear();
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.KabAdmin'));		
	}
}
?>