<?php

class ObatPaketBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{				
			$this->nama->focus();	
			$this->tambahBtn->Enabled = false;
			
			$id = $this->Request['ID'];
			$editMode = $this->Request['editMode'];
			$this->setViewState('editMode',$editMode);
			
			if($editMode == '01') //MODE EDIT
			{
				$sql = "SELECT * FROM tbm_obat_paket_kelompok WHERE id = '$id' ";
				$this->nama->Text = ObatPaketKelompokRecord::finder()->findBySql($sql)->nama;
				
				$sql = "SELECT * FROM tbm_obat_paket WHERE id_kelompok_paket = '$id' ";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_obat = $row['id_obat'];
					$jumlah = $row['jumlah'];			
					
					$this->makeTmpTbl($id_obat,$jumlah);
				}	
			}
		}
		
		if ($this->getViewState('nmTable'))
			$this->simpanBtn->Enabled = true;		
		else
			$this->simpanBtn->Enabled = false;	
	}
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
		
        // Sender is the Suggestions repeater		
		if($this->getDummyData($token))
		{
			$sender->DataSource = $this->getDummyData($token);
	        $sender->dataBind();	
		}
		else
		{
			$this->tambahBtn->Enabled = false;	
		}                                                                                                 
    }
	
	public function getDummyData($token) 
	{
		$sql = "SELECT * FROM tbm_obat WHERE nama LIKE '$token%' ORDER BY nama ";	
		$arr = $this->queryAction($sql,'S');
		return $arr;
    }
	
	public function suggestionSelected1($sender,$param) 
	{
        $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
		//$this->kodeObat->Text = '';
		//$this->checkIdObat($id);
		
		if($id != '')
		{
			$this->kodeObat->Text = $id;
			$this->Page->CallbackClient->focus($this->jumlah);
			$this->tambahBtn->Enabled = true;
		}
    }
	
	public function secondCallBack($sender,$param)
   	{
		//$this->secondPanel->render($param->getNewWriter());
	}
	
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	
		
	public function makeTmpTbl($id_obat,$jumlah)
	{			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 id_obat CHAR(5) DEFAULT NULL,
										 jumlah INT(11) DEFAULT '0',									 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		if($this->getViewState('editMode') == '01')
		{
			$sql="SELECT * FROM tbm_obat_paket WHERE id_kelompok_paket = 'id_kelompok_paket' AND id_obat = '$id_obat'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Obat/Alkes sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTable WHERE id_obat = '$id_obat' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTable (id_obat,jumlah) VALUES ('$id_obat','$jumlah')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Obat/Alkes sudah dipilih sebelumnya !</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTable (id_obat,jumlah) VALUES ('$id_obat','$jumlah')";
			$this->queryAction($sql,'C');	
		}
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
		
		$this->UserGrid->DataSource = $arrData;
		$this->UserGrid->dataBind();	
		
		$this->AutoComplete->Text = '';
		$this->kodeObat->Text = '';
		$this->jumlah->Text = '';
		
		$this->Page->CallbackClient->focus($this->AutoComplete);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClicked()
	{
		if($this->Page->IsValid)
		{
			$id_obat = $this->kodeObat->Text;
			$jumlah = intval($this->jumlah->Text);
			
			$this->makeTmpTbl($id_obat,$jumlah);
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreated($sender,$param)
    {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->nmColoumn->Text = ObatRecord::finder()->findByPk($item->DataItem['id_obat'])->nama;
			$item->jmlColoumn->Text = $item->DataItem['jumlah'];
		}	
	}
	
	public function deleteClicked($sender,$param)
    {
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "DELETE FROM $nmTable WHERE id='$ID'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
		$this->UserGrid->DataSource=$arrData;
		$this->UserGrid->dataBind();	
		
		if(count($arrData)==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');
			$this->clearViewState('tmpJml');
		}
		
		$this->Page->CallbackClient->focus($this->AutoComplete);
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Simpan data sekarang ?<br/><br/><br/></p>\',timeout: 1000000,dialog:{
					modal: true,
					buttons: {
						"Ya": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							konfirmasi(\'ya\');
						},
						Tidak: function() {
							jQuery( this ).dialog( "close" );
							konfirmasi(\'tidak\');
						}
					}
				}});');		
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}	
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
						
		if($mode == 'ya')
		{
			if($this->getViewState('editMode') == '01')//mode edit
			{
				$idKelPaket = $this->Request['ID'];	
				$data = ObatPaketKelompokRecord::finder()->findByPk($idKelPaket);	
				$data->nama = trim($this->nama->Text);
				$data->Save();
				
				$idKelPaket = $data->id;
				
				$sql = "SELECT * FROM tbm_obat_paket WHERE id_kelompok_paket = '$idKelPaket' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$sql = "DELETE FROM tbm_obat_paket WHERE id_kelompok_paket = '$idKelPaket' ";
					$this->queryAction($sql,'C');
				}
			}
			else
			{
				$data = new ObatPaketKelompokRecord();
				$data->nama = trim($this->nama->Text);
				$data->Save();
				
				$sql = "SELECT MAX(id) AS id FROM tbm_obat_paket_kelompok ";
				$idKelPaket = ObatPaketKelompokRecord::finder()->findBySql($sql)->id;
			}			
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "SELECT * FROM $nmTable ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_obat_paket
					$data = new ObatPaketRecord();
					$data->id_kelompok_paket = $idKelPaket;
					$data->id_obat = $row['id_obat'];
					$data->jumlah = $row['jumlah'];
					$data->Save();			
				}
			}
			
			if($this->getViewState('nmTable'))
			{		
				$this->queryAction($this->getViewState('nmTable'),'D');				
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}		
			
			if($this->getViewState('editMode') == '01')//mode edit
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Edit Paket Obat Sukses.<br/><br/></p>\',timeout: 1000000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi2(\'tidak\');
							},
						}
					}});');			
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question"><b>'.ucwords(trim($this->nama->Text)).'</b> telah disimpan dalam database. <br/>Apakah akan menambah paket baru lagi ?<br/><br/></p>\',timeout: 1000000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi2(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi2(\'tidak\');
							}
						}
					}});');		
			}		
			
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatPaket'));
		}
		else
		{
			$this->Page->CallbackClient->focus($this->simpanBtn);	
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));	
		}
	}
	
	public function prosesKonfirmasi2($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
						
		if($mode == 'ya')
		{
			$this->nama->Text = '';	
			$this->Page->CallbackClient->focus($this->nama);	
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatPaket'));
		}
	}
	
	public function batalClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');				
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}		
		
		$this->Response->Reload();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');				
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}		
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatPaket'));		
	}

}
?>
