<?php

class ProviderBaru extends SimakConf
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
			//$this->nama->focus();	
			
			$id = $this->Request['id'];
			$stDetail = $this->Request['stDetail'];
			$this->setViewState('stDetail',$stDetail);
			
			$sql = "SELECT * FROM tbm_perusahaan_asuransi ORDER BY nama ";
			$this->DDPerus->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDPerus->dataBind();
			$this->DDPerus->SelectedValue = $id;
			$this->DDPerus->Enabled = false;
			
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoli->dataBind();
			
			$this->DDPoli2->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoli2->dataBind();
			
			$sql = "SELECT * FROM tbm_nama_tindakan ORDER BY nama ";
			$this->DDTindakan->DataSource = NamaTindakanRecord::finder()->findAllBySql($sql);
			$this->DDTindakan->dataBind();
			$this->DDTindakan->Enabled = false;			
			$this->DDDokter->Enabled = false;
			
			if($stDetail == '1') //MODE EDIT
			{
				/*$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$id' ";
				$this->plafonObat->Text = ProviderDetailObatRecord::finder()->findBySql($sql)->plafon;
				$this->marginObat->Text = ProviderDetailObatRecord::finder()->findBySql($sql)->margin;
				*/
				
				$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_poli = $row['id_poli'];
					$plafon = $row['plafon'];
					$margin = $row['margin'];	
					
					$this->makeTmpTblObat($id_poli,$plafon,$margin);
				}	
				
				
				$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_poli = $row['id_poli'];
					$id_tindakan = $row['id_tindakan'];
					$id_dokter = $row['dokter'];
					$tarif = $row['tarif'];			
					
					$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif);
				}	
			}
		}
	}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	
	
	public function DDPolichanged($sender,$param)
    {
	   $idKlinik = $this->DDPoli->SelectedValue;
       if($idKlinik != '')
	   {
	   		$sql = "SELECT * FROM tbm_nama_tindakan WHERE id_klinik = '$idKlinik' ORDER BY nama ";
			$this->DDTindakan->DataSource = NamaTindakanRecord::finder()->findAllBySql($sql);
			$this->DDTindakan->dataBind();
			$this->DDTindakan->Enabled = true;
			
			if($idKlinik == '07'){
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1 AND
					  tbm_poliklinik.id = '08' ";
			}
			else
			{
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai	
					  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1 AND 
					  tbm_poliklinik.id = '$idKlinik'";
			}
			
			$arr = $this->queryAction($sql,'S');	
			$this->DDDokter->DataSource = $arr;
			$this->DDDokter->dataBind();
			
			$this->DDDokter->Enabled = true;
	   }
	   else
	   {
	   		$this->DDTindakan->SelectedValue = 'empty';
	   		$this->DDTindakan->Enabled = false;
			
			$this->DDDokter->SelectedValue = 'empty';
			$this->DDDokter->Enabled = false;
	   }	
    }	   
	
	public function tambahObatClicked()
	{
		if($this->Page->IsValid)
		{
			$id_poli = $this->DDPoli2->SelectedValue;
			$plafon = floatval($this->plafonObat->Text);
			$margin = floatval($this->marginObat->Text);
			
			$this->makeTmpTblObat($id_poli,$plafon,$margin);
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function makeTmpTblObat($id_poli,$plafon,$margin)
	{			
		if (!$this->getViewState('nmTableObat'))
		{
			$nmTableObat = $this->setNameTable('nmTableObat');
			$sql="CREATE TABLE $nmTableObat (id INT (2) auto_increment, 
										 id_poli CHAR(2) DEFAULT NULL,
										 plafon FLOAT DEFAULT '0',
										 margin FLOAT DEFAULT '0',									 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableObat = $this->getViewState('nmTableObat');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_obat WHERE id_provider = 'id_provider' AND id_poli = '$id_poli' ";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Plafon Obat untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
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
				$sql="SELECT * FROM $nmTableObat WHERE id_poli = '$id_poli' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableObat (id_poli,plafon,margin) VALUES ('$id_poli','$plafon','$margin')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Plafon Obat untuk Asuransi Perusaahan sudah ada sebelumnya.</p>\',timeout: 4000,dialog:{
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
			$sql="INSERT INTO $nmTableObat (id_poli,plafon,margin) VALUES ('$id_poli','$plafon','$margin')";
			$this->queryAction($sql,'C');
		}
		
		$sql="SELECT * FROM $nmTableObat ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
		
		$this->UserGridObat->DataSource = $arrData;
		$this->UserGridObat->dataBind();	
		
		$this->DDPoli2->SelectedValue = 'empty';
		$this->plafonObat->Text = '0';
		$this->marginObat->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPoli2);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif)
	{			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 id_poli CHAR(2) DEFAULT NULL,
										 id_tindakan CHAR(4) DEFAULT NULL,
										 id_dokter CHAR(4) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',									 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_poli = '$id_poli' AND id_tindakan = '$id_tindakan' AND dokter = '$id_dokter'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
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
				$sql="SELECT * FROM $nmTable WHERE id_poli = '$id_poli' AND id_tindakan = '$id_tindakan' AND id_dokter = '$id_dokter'";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTable (id_poli,id_tindakan,id_dokter,tarif) VALUES ('$id_poli','$id_tindakan','$id_dokter','$tarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
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
			$sql="INSERT INTO $nmTable (id_poli,id_tindakan,id_dokter,tarif) VALUES ('$id_poli','$id_tindakan','$id_dokter','$tarif')";
			$this->queryAction($sql,'C');
		}
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
		
		$this->UserGrid->DataSource = $arrData;
		$this->UserGrid->dataBind();	
		
		$this->DDPoli->SelectedValue = 'empty';
		$this->DDTindakan->SelectedValue = 'empty';
		$this->DDDokter->SelectedValue = 'empty';
		$this->tarifOperator->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPoli);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClicked()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_dokter = $this->DDDokter->SelectedValue;
			$id_poli = $this->DDPoli->SelectedValue;
			$id_tindakan = $this->DDTindakan->SelectedValue;
			$tarif = floatval($this->tarifOperator->Text);
			
			$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif);
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreated($sender,$param)
    {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->tindakanColoumn->Text = NamaTindakanRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			$item->poliColoumn->Text = PoliklinikRecord::finder()->findByPk($item->DataItem['id_poli'])->nama;
			$item->dokterColoumn->Text = PegawaiRecord::finder()->findByPk($item->DataItem['id_dokter'])->nama;
			$item->tarifColoumn->Text = $item->DataItem['tarif'];
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
    }
	
	public function itemCreatedObat($sender,$param)
    {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->poli2Coloumn->Text = PoliklinikRecord::finder()->findByPk($item->DataItem['id_poli'])->nama;
			$item->plafonColoumn->Text = number_format($item->DataItem['plafon'],'2',',','.');
			$item->marginColoumn->Text = $item->DataItem['margin'].'%';
		}	
	}
	
	public function deleteClickedObat($sender,$param)
    {
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridObat->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableObat = $this->getViewState('nmTableObat');
		
		$sql = "DELETE FROM $nmTableObat WHERE id='$ID'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT * FROM $nmTableObat ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
		$this->UserGridObat->DataSource=$arrData;
		$this->UserGridObat->dataBind();	
		
		if(count($arrData)==0)
		{
			$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table	
			$this->clearViewState('nmTableObat');
			$this->clearViewState('tmpJml');
		}
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
		$idProv = $this->DDPerus->SelectedValue;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			//UPDATE st_detail di tbt_perusahaan_asuransi
			$data = PerusahaanRecord::finder()->findByPk($idProv);
			$data->st_detail = '1';
			$data->Save();			
			
			$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_tindakan WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "SELECT * FROM $nmTable ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_tindakan
					$data = new ProviderDetailTindakanRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_poli = $row['id_poli'];
					$data->id_tindakan = $row['id_tindakan'];
					$data->dokter = $row['id_dokter'];
					$data->tarif = $row['tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_obat
			$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableObat'))
			{
				$nmTableObat = $this->getViewState('nmTableObat');
				
				$sql = "SELECT * FROM $nmTableObat ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_obat
					$data = new ProviderDetailObatRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_poli = $row['id_poli'];
					$data->plafon = $row['plafon'];
					$data->margin = $row['margin'];
					$data->Save();			
				}
			}
			
			/*
			//INSERT tbm_provider_detail_obat
			$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$data = ProviderDetailObatRecord::finder()->findBySql($sql);
			}
			else
			{
				$data = new ProviderDetailObatRecord();
				$data->id_provider = $idProv;
			}
				
			$data->plafon = floatval($this->plafonObat->Text);
			$data->margin = floatval($this->marginObat->Text);
			$data->Save();	
			*/
			
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));	
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
		
		if($this->getViewState('nmTableObat'))
		{		
			$this->queryAction($this->getViewState('nmTableObat'),'D');				
			$this->UserGridObat->DataSource='';
            $this->UserGridObat->dataBind();
			$this->clearViewState('nmTableObat');//Clear the view state				
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
		
		if($this->getViewState('nmTableObat'))
		{		
			$this->queryAction($this->getViewState('nmTableObat'),'D');				
			$this->UserGridObat->DataSource='';
            $this->UserGridObat->dataBind();
			$this->clearViewState('nmTableObat');//Clear the view state				
		}	
		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));		
	}

}
?>
