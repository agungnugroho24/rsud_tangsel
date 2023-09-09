<?php
class MasterAsetSupplierEdit extends SimakConf
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
			$data = $this->AsetSupplierRecord;
			//$id_jenis_barang = $data->id_jenis_barang;	
			
			//$this->DDJenis->SelectedValue = $id_jenis_barang;	
			$this->nama->focus();
			$this->nama->Text = $data->nama;
			$this->alamat->Text = $data->alamat;
			$this->tlp->Text = $data->tlp;
			
			$this->setViewState('idAwal',strtolower($data->id));
			$this->setViewState('namaAwal',strtolower($data->nama));
		}
		
	}	
	
	protected function getAsetSupplierRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AsetSupplierRecord=AsetSupplierRecord::finder()->findByPk($ID);		
		if(!($AsetSupplierRecord instanceof AsetSupplierRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AsetSupplierRecord;
	}	
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$nama = strtolower(trim($this->nama->Text));
			$idJenis = $this->DDJenis->SelectedValue;
			$idAwal = $this->getViewState('idAwal');
			$namaAwal = $this->getViewState('namaAwal');
			
			$sql = "SELECT nama FROM tbm_aset_supplier WHERE LOWER(nama) = '$nama' AND id <> '$idAwal' ";
			
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
				$data= AsetSupplierRecord::finder()->findByPk($idAwal);
				//$data->id_jenis_barang = $this->DDJenis->SelectedValue;
				$data->nama = trim($this->nama->Text);
				$data->alamat = trim($this->alamat->Text);
				$data->tlp = trim($this->tlp->Text);
				$data->save();	
				
				$this->clear();
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Data <b>'.$namaAwal.'</b> telah diedit.</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'tidak\');
							}
						}
					}});');	
			}
		}			
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
		$this->alamat->Text = '';
		$this->tlp->Text = '';
		//$this->DDJenis->SelectedIndex = '';
		$this->Page->CallbackClient->focus($this->nama);
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			//$this->Page->CallbackClient->focus($this->DDKamar);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->Response->Reload();	
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Asset.MasterAsetSupplier'));	
		}
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