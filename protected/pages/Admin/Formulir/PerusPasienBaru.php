<?php
class PerusPasienBaru extends SimakConf
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
			
			$this->DDKel->DataSource = KelompokRecord::finder()->findAll($criteria);
			$this->DDKel->DataBind();
			
			//$this->nama->focus();
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
			$idKel = $this->DDKel->SelectedValue;
			$nama = strtolower(trim($this->nama->Text));
			$st = $this->st->SelectedValue;
			
			$sql = "SELECT nama FROM tbm_perusahaan_asuransi WHERE LOWER(nama) = '$nama' AND id_kel = '$idKel' AND st='$st'";
			
			if(PerusahaanRecord::finder()->findBySql($sql))
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Perusahaan Asuransi untuk kelompok dan status perawatan yg dipilih sudah ada dalam database.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
					
				$this->Page->CallbackClient->focus($this->nama);
			}
			else
			{
				$data= new PerusahaanRecord();							
				$data->id = $this->numUrut('tbm_perusahaan_asuransi',PerusahaanRecord::finder(),'2');		
				$data->id_kel = $idKel;
				$data->nama = ucwords(trim($this->nama->Text));
				$data->st = $st;
				$data->save();	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Perusahaan Asuransi <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah data lagi ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'tidak\');
							}
						}
					}});');	
				
				$this->clear();
			}
		}			
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));	
		}
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
		$this->DDKel->SelectedValue = 'empty';
		$this->st->SelectedValue = '0';
	}
	
    public function batalClicked($sender,$param)
	{		
		//$this->msg->Text = '';
		$this->clear();
		$this->Page->CallbackClient->focus($this->nama);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.Formulir.PerusPasien'));		
	}
}
?>
