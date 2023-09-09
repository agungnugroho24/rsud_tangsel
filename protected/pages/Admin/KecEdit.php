<?php
class KecEdit extends SimakConf
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
	}
	
	protected function getKecamatanRecord()
	{
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KecamatanRecord=KecamatanRecord::finder()->findByPk($ID);		
		if(!($KecamatanRecord instanceof KecamatanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KecamatanRecord;
	}
	
	public function ambilData()
	{	
		$KecamatanRecord = $this->KecamatanRecord;
		
		$this->nama->Text = $KecamatanRecord->nama;
		$this->DDProv->SelectedValue = substr($KecamatanRecord->id,0,2);
		$this->DDProvChanged();
		
		$this->DDKotaKab->SelectedValue = substr($KecamatanRecord->id,2,3);
		
		$this->setViewState('namaAwal',$KecamatanRecord->nama);
		$this->setViewState('idAwal',$KecamatanRecord->id);
		$this->setViewState('idPropKabAwal',substr($KecamatanRecord->id,0,5));
	}
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$idUser = $this->User->IsUserNip;
			$idProp = $this->DDProv->SelectedValue;
			$idKab = $this->DDKotaKab->SelectedValue;
			$idCari = $idProp.''.$idKab;
			
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_kecamatan WHERE LOWER(nama) = '$nama' AND SUBSTRING(id,1,5) = '$idCari'";
			
			if(KecamatanRecord::finder()->findBySql($sql))
			{
				if($idCari == $this->getViewState('idPropKabAwal'))
				{
					$id = $this->getViewState('idAwal');
					if($nama == strtolower(trim($this->getViewState('namaAwal'))))
					{
						$data= $this->KecamatanRecord;							
						$data->id = $id;
						$data->nama = ucwords(trim($this->nama->Text));
						$data->save();	
						
						$this->msg->Text = '    
						<script type="text/javascript">
							alert("Data telah diperbaharui !");
							window.location="index.php?page=admin.KecAdmin"; 
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
				
				if($idCari == $this->getViewState('idPropKabAwal'))
				{
					$id = $this->getViewState('idAwal');
					$data= $this->KecamatanRecord;
					$data->nama = $nama;
					$data->save();	
				}
				else
				{
					$id = $this->numUrutKecamatan('tbm_kecamatan',KecamatanRecord::finder(),'2',$idCari);
					$sql = "UPDATE tbm_kecamatan SET id = '$id', nama='$nama' WHERE id='$idAwal' ";
					$this->queryAction($sql,'C');
				}
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data telah diperbaharui !");
					window.location="index.php?page=admin.KecAdmin"; 
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
		$this->Response->redirect($this->Service->constructUrl('admin.KecAdmin'));		
	}
}
?>