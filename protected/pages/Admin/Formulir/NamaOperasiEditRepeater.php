<?php
class NamaOperasiEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)        
		{
			$data = $this->OperasiNamaRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;
			$this->index1->Text = $data->index_operator;
			$this->index2->Text = $data->index_anastesi;
			$this->index3->Text = $data->index_ast_anastesi;
			$this->index4->Text = $data->index_ast_instrumen;
			$this->index5->Text = $data->index_paramedis;
			$this->index6->Text = $data->index_resusitasi;
			$this->index7->Text = $data->index_pengembang;
			$this->index8->Text = $data->index_penyulit;
			
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$this->Repeater->DataSource=$arr;
			$this->Repeater->dataBind();
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
		}
	} 	 
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
		$idTindakan = $this->Request['ID'];		
		
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$idKelas = $item->DataItem['id'];
			$item->nmKelas->Text = ucwords($item->DataItem['nama']);
			$item->tarifOperator->Text = OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',array($idTindakan,$idKelas))->js_dok_obgyn;
			
        }
    }
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			
			if($this->getViewState('namaAwal') != $nama)
			{
				$sql = "SELECT nama FROM tbm_operasi_nama WHERE LOWER(nama) = '$nama' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Operasi dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
					$this->Page->CallbackClient->focus($this->nama);		
				}
				else
				{
					$this->prosesSimpan();
				}		
			}
			else
			{
				$this->prosesSimpan();
			}
        }			
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}	
		
	}
	
	protected function prosesSimpan()
	{
		//UPDATE tbm_rad_tindakan
		$data = $this->OperasiNamaRecord;  	
		$data->nama = ucwords($this->nama->Text);				
		$data->index_operator = floatval($this->index1->Text);
		$data->index_anastesi = floatval($this->index2->Text);
		$data->index_ast_anastesi = floatval($this->index3->Text);
		$data->index_ast_instrumen = floatval($this->index4->Text);
		$data->index_paramedis = floatval($this->index5->Text);
		$data->index_resusitasi = floatval($this->index6->Text);
		$data->index_pengembang = floatval($this->index7->Text);
		$data->index_penyulit = floatval($this->index8->Text);
		$data->save();	
		
		$idTindakan = $data->id;
		
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
					
					//UPDATE tbm_operasi_tarif
					$newLabTarifRecord = OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',array($idTindakan,$idKelas));
					$newLabTarifRecord->js_dok_obgyn = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
					$newLabTarifRecord->save(); 
					
					//$this->Repeater->Items[$index]->tarifOperator->Text = '0';
					break;
				case "AlternatingItem":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
					
					//INSERT tbm_operasi_tarif
					//UPDATE tbm_operasi_tarif
					$newLabTarifRecord = OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',array($idTindakan,$idKelas));
					$newLabTarifRecord->js_dok_obgyn = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
					$newLabTarifRecord->save(); 
					
					//$this->Repeater->Items[$index]->tarifOperator->Text = '0';
					break;
				default:
					break;
			}
		}
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tindakan Operasi <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));
		}
	}
	
	/*
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.NamaTindakan';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	*/
	
	protected function getOperasiNamaRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$OperasiNamaRecord=OperasiNamaRecord::finder()->findByPk($ID);		
		if(!($OperasiNamaRecord instanceof OperasiNamaRecord))
			throw new THttpException(500,'id tidak benar.');
		return $OperasiNamaRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));			
	}
}
?>