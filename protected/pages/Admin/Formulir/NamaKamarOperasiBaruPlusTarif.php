<?php

class NamaKamarOperasiBaru extends SimakConf
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
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
			
			$this->nama->focus();	
			
			/*
			$sql="SELECT * FROM tbm_kamar_kelas ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$this->Repeater->DataSource=$arr;
			$this->Repeater->dataBind();
			*/				
		}
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$item->nmKelas->Text = ucwords($item->DataItem['nama']);
        }
    }
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(KamarOperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_operasi_kamar_nama WHERE LOWER(nama) = '$nama' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Kamar Tindakan Operasi dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_operasi_kamar_nama
				$data = new KamarOperasiNamaRecord();
				$data->id = $this->ID->Text;			
				$data->nama = $this->nama->Text;
				$data->save();				
				
				
				//INSERT tbm_operasi_kamar_tarif
				$sql = "SELECT id FROM tbm_operasi_kamar_nama ORDER BY id DESC";
				$idKamar = KamarOperasiNamaRecord::finder()->findBySql($sql)->id;
				
				$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$idKelas = $row['id'];
					$persentaseKenaikan = $row['persentase'];
					$tarifStandar = floatval($this->tarifOperator->Text);
					
					//INSERT tbm_operasi_kamar_tarif
					$dataTarif = new KamarOperasiTarifRecord;
					$dataTarif->id_kamar_operasi = $idKamar;
					$dataTarif->id_kelas = $idKelas;	
					$dataTarif->id_kategori_operasi = $this->DDKateg->SelectedValue;
					$dataTarif->tarif = $tarifStandar + ($tarifStandar * $persentaseKenaikan / 100);
					$dataTarif->save(); 	
				}
				
				/*
				foreach($this->Repeater->getItems() as $item) {
					switch($item->getItemType()) {
						case "Item":					
							$index = $this->Repeater->DataKeys[$item->getItemIndex()];
							$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
							
							//INSERT tbm_operasi_tarif
							$dataTarif = new KamarOperasiTarifRecord;
							$dataTarif->id_kamar_operasi = $idKamar;
							$dataTarif->id_kelas = $idKelas;	
							$dataTarif->tarif = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
							$dataTarif->save(); 
							
							$this->Repeater->Items[$index]->tarifOperator->Text = '0';
							break;
						case "AlternatingItem":					
							$index = $this->Repeater->DataKeys[$item->getItemIndex()];
							$idKelas = $this->Repeater->Items[$index]->idKelas->Text;
							
							//INSERT tbm_operasi_tarif
							$dataTarif = new KamarOperasiTarifRecord;
							$dataTarif->id_kamar_operasi = $idKamar;
							$dataTarif->id_kelas = $idKelas;	
							$dataTarif->tarif = floatval($this->Repeater->Items[$index]->tarifOperator->Text);
							$dataTarif->save(); 
							
							$this->Repeater->Items[$index]->tarifOperator->Text = '0';
							break;
						default:
							break;
					}
				}
				*/
				
				
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Kamar Tindakan Operasi <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah kamar tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamarOperasi'));	
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
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamarOperasi'));		
	}
}
?>