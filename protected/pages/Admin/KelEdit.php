<?php
class KelEdit extends SimakConf
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
	
	protected function getKelurahanRecord()
	{
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KelurahanRecord=KelurahanRecord::finder()->findByPk($ID);		
		if(!($KelurahanRecord instanceof KelurahanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KelurahanRecord;
	}
	
	public function ambilData()
	{	
		$KelurahanRecord = $this->KelurahanRecord;
		
		$this->nama->Text = $KelurahanRecord->nama;
		$this->DDProv->SelectedValue = substr($KelurahanRecord->id,0,2);		
		$this->DDProvChanged();
		
		$this->DDKotaKab->SelectedValue = substr($KelurahanRecord->id,2,3);
		$this->DDKotaKabChanged();
		
		$this->DDKec->SelectedValue = substr($KelurahanRecord->id,0,7);
		
		$this->setViewState('namaAwal',$KelurahanRecord->nama);
		$this->setViewState('idAwal',$KelurahanRecord->id);
		$this->setViewState('idPropKabKecAwal',substr($KelurahanRecord->id,0,5));
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
				if($idCari == $this->getViewState('idPropKabKecAwal'))
				{
					$id = $this->getViewState('idAwal');
					if($nama == strtolower(trim($this->getViewState('namaAwal'))))
					{
						$data= $this->KelurahanRecord;							
						$data->id = $id;
						$data->nama = ucwords(trim($this->nama->Text));
						$data->save();	
						
						$this->msg->Text = '    
						<script type="text/javascript">
							alert("Data telah diperbaharui !");
							window.location="index.php?page=admin.KelAdmin"; 
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
					$this->Page->CallbackClient->focus($this->nama);
					$this->msg->Text = '    
					<script type="text/javascript">
						alert("Nama yang dimasukan sudah ada. \nSilakan ganti dengan nama yang lain.");
					</script>';
				}
			}
			else
			{
				$idAwal = $this->getViewState('idAwal');
				$nama = ucwords(trim($this->nama->Text));
				
				if($idCari == $this->getViewState('idPropKabKecAwal'))
				{
					$id = $this->getViewState('idAwal');
					$data= $this->KelurahanRecord;
					$data->nama = $nama;
					$data->save();	
				}
				else
				{
					$id = $this->numUrutKelurahan('tbm_kelurahan',KelurahanRecord::finder(),'3',$idCari);
					$sql = "UPDATE tbm_kelurahan SET id = '$id', nama='$nama' WHERE id='$idAwal' ";
					$this->queryAction($sql,'C');
				}
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data telah diperbaharui !");
					window.location="index.php?page=admin.KelAdmin"; 
				</script>';
				
				$this->msg->Text = $id;
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
		$this->Response->redirect($this->Service->constructUrl('admin.KelAdmin'));		
	}
}
?>