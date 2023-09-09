<?php

class NamaOperasiBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{				
			$this->nama->focus();	
			
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$this->Repeater->DataSource=$arr;
			$this->Repeater->dataBind();
							
		}
	}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$item->nmKelas->Text = ucwords($item->DataItem['nama']);
        }
    }
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			
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
				//INSERT tbm_rad_tindakan
				$data = new OperasiNamaRecord();		
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
				
				//INSERT tbm_lab_tarif
				$sql = "SELECT id FROM tbm_operasi_nama ORDER BY id DESC";
				$kodeTdk = OperasiNamaRecord::finder()->findBySql($sql)->id;
				
				foreach($this->Repeater->getItems() as $item) {
					switch($item->getItemType()) {
						case "Item":					
							$index = $this->Repeater->DataKeys[$item->getItemIndex()];
							$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
							
							//INSERT tbm_operasi_tarif
							$newLabTarifRecord = new OperasiTarifRecord;
							$newLabTarifRecord->id_operasi = $kodeTdk;
							$newLabTarifRecord->id_kelas = $idKelas;	
							$newLabTarifRecord->js_dok_obgyn = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
							$newLabTarifRecord->save(); 
							
							$this->Repeater->Items[$index]->tarifOperator->Text = '0';
							break;
						case "AlternatingItem":					
							$index = $this->Repeater->DataKeys[$item->getItemIndex()];
							$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
							
							//INSERT tbm_operasi_tarif
							$newLabTarifRecord = new OperasiTarifRecord;
							$newLabTarifRecord->id_operasi = $kodeTdk;
							$newLabTarifRecord->id_kelas = $idKelas;	
							$newLabTarifRecord->js_dok_obgyn = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
							$newLabTarifRecord->save(); 
							
							$this->Repeater->Items[$index]->tarifOperator->Text = '0';
							break;
						default:
							break;
					}
				}
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tindakan Operasi <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
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
				
				$this->nama->Text = '';
				$this->index1->Text = '0';
				$this->index2->Text = '0';
				$this->index3->Text = '0';
				$this->index4->Text = '0';
				$this->index5->Text = '0';
				$this->index6->Text = '0';
				$this->index7->Text = '0';
				$this->index8->Text = '0';
			}	
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));	
		}
	}
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));		
	}
}
?>