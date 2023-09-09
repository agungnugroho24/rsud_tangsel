<?php
class KelBaru extends SimakConf
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
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDProv->DataSource = PropinsiRecord::finder()->findAll($criteria);
			$this->DDProv->DataBind();
			
			//$this->nama->focus();
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
				
		}
	}
	
	public function DDProvChanged()
	{
		$this->DDKotaKab->SelectedValue = 'empty';
		
		if($this->DDProv->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			
			$sql = "SELECT id,nama FROM tbm_kabupaten WHERE id_propinsi = '$idProv' ORDER BY nama ";
			
			$this->DDKotaKab->DataSource=KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKotaKab->dataBind();
			$this->DDKotaKab->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKotaKab);
		}
		else
		{	
			$this->DDKotaKab->DataSource='';
			$this->DDKotaKab->dataBind();
			$this->DDKotaKab->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDProv);
		}
		
		$this->DDKotaKabChanged();
	}
	
	public function DDKotaKabChanged()
	{
		$this->DDKec->SelectedValue = 'empty';
		
		if($this->DDKotaKab->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			$idKotaKab = $this->DDKotaKab->SelectedValue;
			$idFilter = $idProv.''.$idKotaKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKec);
		}
		else
		{
			$this->DDKec->DataSource='';
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDKotaKab);
		}
	}
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$idUser = $this->User->IsUserNip;
			$idProp = $this->DDProv->SelectedValue;
			$idKab = $this->DDKotaKab->SelectedValue;
			$idKec = $this->DDKec->SelectedValue;
			$idCari = $idKec;
			
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_kelurahan WHERE LOWER(nama) = '$nama' AND SUBSTRING(id,1,7) = '$idCari'";
			
			if(KelurahanRecord::finder()->findBySql($sql))
			{
				$this->Page->CallbackClient->focus($this->nama);
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Nama yang dimasukan sudah ada. \nSilakan ganti dengan nama yang lain.");
				</script>';
			}
			else
			{
				$data= new KelurahanRecord();							
				$data->id = $this->numUrutKelurahan('tbm_kelurahan',KelurahanRecord::finder(),'3',$idCari);
				$data->nama = ucwords(trim($this->nama->Text));
				$data->save();	
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data kelurahan baru telah dimasukan dalam database!");
				</script>';
				
				$this->clear();
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
		$this->clear();
		$this->Page->CallbackClient->focus($this->nama);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.KelAdmin'));		
	}
}
?>