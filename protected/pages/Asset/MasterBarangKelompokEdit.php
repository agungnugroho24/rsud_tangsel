<?php
class MasterBarangKelompokEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('12');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	 public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			$data = $this->BarangKelompokRecord;
			$id_jenis_barang = $data->id_jenis_barang;	
			
			$this->DDJenis->SelectedValue = $id_jenis_barang;	
			$this->DDJenis->focus();
			$this->nama->Text = $data->nama;
			
			$this->setViewState('idAwal',strtolower($data->id));
			$this->setViewState('namaAwal',strtolower($data->nama));
		}
		
	}	
	
	protected function getBarangKelompokRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$BarangKelompokRecord=BarangKelompokRecord::finder()->findByPk($ID);		
		if(!($BarangKelompokRecord instanceof BarangKelompokRecord))
			throw new THttpException(500,'id tidak benar.');
		return $BarangKelompokRecord;
	}	
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$nama = strtolower(trim($this->nama->Text));
			$idJenis = $this->DDJenis->SelectedValue;
			$idAwal = $this->getViewState('idAwal');
			$namaAwal = $this->getViewState('namaAwal');
			
			$sql = "SELECT nama FROM tbm_barang_kelompok WHERE LOWER(nama) = '$nama' AND id_jenis_barang = '$idJenis' AND id <> '$idAwal' ";
			
			if(BarangKelompokRecord::finder()->findBySql($sql))
			{
				$this->Page->CallbackClient->focus($this->nama);
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Nama kelompok barang untuk jenis yang dipilih sudah ada. \nSilakan ganti dengan nama yang lain.");
				</script>';
			}
			else
			{
				$data= BarangKelompokRecord::finder()->findByPk($idAwal);
				$data->id_jenis_barang = $this->DDJenis->SelectedValue;
				$data->nama = trim($this->nama->Text);
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
		$this->DDJenis->SelectedIndex = '';
		$this->Page->CallbackClient->focus($this->DDJenis);
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
			$this->Response->redirect($this->Service->constructUrl('Asset.MasterBarangKelompok'));	
		}
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->clear();
		$this->Page->CallbackClient->focus($this->DDJenis);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterBarangKelompok'));		
	}
}
?>