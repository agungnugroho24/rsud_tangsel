<?php
class MasterAsetSupplierBaru extends SimakConf
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
			$this->nama->focus();
		}
		
	}	
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$nama = strtolower(trim($this->nama->Text));
			$idJenis = $this->DDJenis->SelectedValue;
			
			$sql = "SELECT nama FROM tbm_aset_supplier WHERE LOWER(nama) = '$nama' ";
			
			if(AsetSupplierRecord::finder()->findBySql($sql))
			{
				$this->Page->CallbackClient->focus($this->nama);
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Nama supplier aset sudah ada. \nSilakan ganti dengan nama yang lain.");
				</script>';
			}
			else
			{
				$data= new AsetSupplierRecord();	
				//$data->id_jenis_barang = $this->DDJenis->SelectedValue;
				$data->nama = trim($this->nama->Text);
				$data->alamat = trim($this->alamat->Text);
				$data->tlp = trim($this->tlp->Text);
				$data->save();	
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data supplier aset baru telah dimasukan dalam database!");
				</script>';
				
				$this->clear();
			}
		}			
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
		$this->alamat->Text = '';
		$this->tlp->Text = '';
		$this->DDJenis->SelectedIndex = '';
		$this->Page->CallbackClient->focus($this->nama);
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
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterAsetSupplier'));		
	}
}
?>