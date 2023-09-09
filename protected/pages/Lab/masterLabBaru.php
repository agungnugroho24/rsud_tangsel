<?php
class masterLabBaru extends SimakConf
{
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('5');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
 	public function onPreRender($param)
	{
		parent::onPreRender($param);
			
		if(!$this->IsPostBack || !$this->IsCallBack )
		{
			$sql = "SELECT * FROM tbm_lab_rujukan ORDER BY nama";
			$this->DDLabRujukan->DataSource=LabRujukanRecord::finder()->findAllBySql($sql);
			$this->DDLabRujukan->dataBind();
			$this->DDLabRujukan->Enabled = false;
			$this->valRujukan->Enabled = false;
			
			$this->DDLabKateg->DataSource=LabKategRecord::finder()->findAll();
			$this->DDLabKateg->dataBind();
			
			$this->DDLabKel->DataSource=LabKelRecord::finder()->findAll();
			$this->DDLabKel->dataBind();
			
			/*
			$sql = "SELECT nama, st_paket FROM tbm_lab_tindakan WHERE st_paket > 0 ORDER BY nama ";
			$this->DDLabPaket->DataSource = LabTdkRecord::finder()->findAllBySql($sql);
			$this->DDLabPaket->dataBind();
			
			$this->DDLabPaket->Enabled = false;
			*/
			$this->itemPaketPanel->Display = 'None';
			$this->gridPanel->Display = 'None';
			$this->nama->focus();								
		}		
	}
	
	public function modeRujukanChanged($sender,$param)
    {
		if($this->modeRujukan->Checked === true)
		{
			$this->DDLabRujukan->Enabled = true;
			$this->valRujukan->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDLabRujukan);	
		}
		else
		{
			$this->DDLabRujukan->SelectedValue = 'empty';
			$this->DDLabRujukan->Enabled = false;
			$this->valRujukan->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDLabRujukan);	
		}
	}
	
	public function panelCallback($sender,$param)	
   	{
		$this->itemPaketPanel->render($param->getNewWriter());		
		$this->gridPanel->render($param->getNewWriter());		
	}
	
	public function DDLabKelChanged($sender,$param)
    {
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');				
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}		
		
		$this->itemPaket->Text='';
		
		if($this->DDLabKel->SelectedValue == '2') //kelompok paket
		{
			//$this->DDLabPaket->Enabled = true;	
			//$this->Page->CallbackClient->focus($this->DDLabPaket);	
			$this->normal->Text = '';
			$this->normal2->Text = '';
			
			//$this->normal->Enabled = false;
			//$this->normal2->Enabled = false;
			$this->itemPaketVal->Enabled = true;
			$this->Page->CallbackClient->focus($this->itemPaket);			
			$this->itemPaketPanel->Display = 'Dynamic';
			$this->gridPanel->Display = 'Dynamic';
			$this->tambahBtn->Enabled = true;
		}	
		else
		{
			//$this->DDLabPaket->Enabled = false;	
			//$this->DDLabPaket->SelectedValue = 'empty';	
			
			//$this->normal->Enabled = true;
			//$this->normal2->Enabled = true;
			$this->itemPaketVal->Enabled = false;
			$this->itemPaketPanel->Display = 'None';
			$this->gridPanel->Display = 'None';
			$this->Page->CallbackClient->focus($this->normal);	
			$this->tambahBtn->Enabled = false;
		}		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(LabTdkRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	
	public function tambahClicked()
	{
		if($this->Page->IsValid)
		{
			$item = $this->itemPaket->Text;
			$normal = $this->normal->Text;
			$normal2 = $this->normal2->Text;
			
			$this->makeTmpTbl($item,$normal,$normal2);
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function makeTmpTbl($item,$normal,$normal2)
	{	
		$cekItem = strtolower(trim($item));
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 item VARCHAR(255) DEFAULT NULL,
										 normal VARCHAR(255) DEFAULT NULL,
										 normal2 VARCHAR(255) DEFAULT NULL,						 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_lab_paket WHERE id_paket = '$id_paket' AND LOWER(item) = '$cekItem' ";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Paket yang akan ditambahkan sudah ada.</p>\',timeout: 4000,dialog:{
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
				$sql="SELECT * FROM $nmTable WHERE LOWER(item) = '$cekItem' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTable (item,normal,normal2) VALUES ('$item','$normal','$normal2')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Paket yang akan ditambahkan sudah ada.</p>\',timeout: 4000,dialog:{
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
			$sql="INSERT INTO $nmTable (item,normal,normal2) VALUES ('$item','$normal','$normal2')";
			$this->queryAction($sql,'C');
		}
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
		
		$this->UserGrid->DataSource = $arrData;
		$this->UserGrid->dataBind();	
		
		$this->itemPaket->Text = '';
		$this->normal->Text = '';
		$this->normal2->Text = '';
		
		$this->Page->CallbackClient->focus($this->itemPaket);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreated($sender,$param)
    {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->itemColoumn->Text = $item->DataItem['item'];
			$item->normalColoumn->Text = $item->DataItem['normal'];
			$item->normal2Coloumn->Text = $item->DataItem['normal2'];
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
		}
    }
	
	public function simpanClicked($sender,$param)
	{			
		$nama = strtolower(trim($this->nama->Text));
		$kateg = $this->DDLabKateg->SelectedValue;
		$namaKateg =  $this->ambilTxt($this->DDLabKateg);
		
		$kelompok = $this->DDLabKel->SelectedValue;
		$idLabRujukan = $this->DDLabRujukan->SelectedValue;
		
		if($kateg)
			$sql = "SELECT nama FROM tbm_lab_tindakan WHERE LOWER(nama) = '$nama' AND kategori = '$kateg' AND kelompok = '$kelompok' AND id_lab_rujukan = '$idLabRujukan' ";
		else			
			$sql = "SELECT nama FROM tbm_lab_tindakan WHERE LOWER(nama) = '$nama' AND kelompok = '$kelompok' AND id_lab_rujukan = '$idLabRujukan' ";
			
		$arr = $this->queryAction($sql,'S');
		
		if($arr)
		{
			if($kateg)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan '.ucwords($this->nama->Text).' untuk kategori dan kelompok yang dipilih sudah ada dalam database !</p>\',timeout: 4000,dialog:{
						modal: true
					}});');	
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan '.ucwords($this->nama->Text).' untuk kelompok yang dipilih sudah ada dalam database !</p>\',timeout: 4000,dialog:{
						modal: true
					}});');	
			}		
					
			$this->Page->CallbackClient->focus($this->nama);	
		}
		else
		{
			if($this->IsValid)  // when all validations succeed
			{
				$newLabTdkRecord = new LabTdkRecord;
				
				if($this->DDLabKateg->SelectedValue)
					$newLabTdkRecord->kategori = $this->DDLabKateg->SelectedValue;
					
				$newLabTdkRecord->kelompok = $this->DDLabKel->SelectedValue;
				//$newLabTdkRecord->kode = ucwords($this->ID->Text);		            
				$newLabTdkRecord->nama = ucwords(trim($this->nama->Text));
				$newLabTdkRecord->normal = $this->normal->Text;
				$newLabTdkRecord->normal_perempuan = $this->normal2->Text;					
				
				if($this->modeRujukan->Checked === true)
				{
					$newLabTdkRecord->id_lab_rujukan = $this->DDLabRujukan->SelectedValue;
					$newLabTdkRecord->st_rujukan = '1';
				}
				
				if($this->DDLabKel->SelectedValue == '2') //kelompok paket
				{
					$sql = "SELECT st_paket FROM tbm_lab_tindakan GROUP BY st_paket ORDER BY st_paket DESC";
					$stPaketMax = LabTdkRecord::finder()->findBySql($sql)->st_paket;
					
					$idPaketBaru = $stPaketMax + 1;
					$newLabTdkRecord->st_paket = $idPaketBaru;
					
					
					if($this->getViewState('nmTable'))
					{
						$nmTable = $this->getViewState('nmTable');
						
						$sql = "SELECT * FROM $nmTable ORDER BY id";
						$arr = $this->queryAction($sql,'S');
						$i=1;
						foreach($arr as $row)
						{
							//INSERT tbm_provider_detail_tindakan
							$data = new LabPaketRecord();
							$data->id_paket = $idPaketBaru;
							$data->kode = $i;
							$data->item = $row['item'];
							$data->normal = $row['normal'];
							$data->normal_perempuan = $row['normal2'];
							
							if($this->modeRujukan->Checked === true)
								$data->st_rujukan = '1';
							
							if($this->DDLabKateg->SelectedValue)
								$data->kategori = $this->DDLabKateg->SelectedValue;	
							
							$data->Save();	
							$i++;		
						}
					}
				}
				else
				{
					$newLabTdkRecord->st_paket = '0';
				}
					
				$newLabTdkRecord->save(); 
				
				
				
				//INSERT tbm_lab_tarif
				$sql = "SELECT kode FROM tbm_lab_tindakan ORDER BY kode DESC";
				$kodeTdk = LabTdkRecord::finder()->findBySql($sql)->kode;
					
				$newLabTarifRecord = new LabTarifRecord;
				$newLabTarifRecord->id = $kodeTdk;
				$newLabTarifRecord->tarif = $this->Tarif1->Text;	
				$newLabTarifRecord->tarif1 = $this->Tarif2->Text;	
				$newLabTarifRecord->tarif2 = $this->Tarif3->Text;	
				$newLabTarifRecord->tarif3 = $this->Tarif4->Text;	
				$newLabTarifRecord->save(); 	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tindakan '.ucwords($this->nama->Text).' telah ditambahkan dalam database. <br/><br/> Apakah akan menambah tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->DDLabRujukan->SelectedValue = 'empty';
				$this->DDLabRujukan->Enabled = false;
				
				$this->DDLabKateg->SelectedValue = 'empty';
				$this->DDLabKel->SelectedValue = 'empty';
				$this->modeRujukan->Checked = false;
				$this->normal->Text = '';
				$this->normal2->Text = '';
				
				$this->Tarif1->Text = '0';
				$this->Tarif2->Text = '0';
				$this->Tarif3->Text = '0';
				$this->Tarif4->Text = '0';
				
				if($this->getViewState('nmTable'))
				{		
					$this->queryAction($this->getViewState('nmTable'),'D');				
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();
					$this->clearViewState('nmTable');//Clear the view state				
				}		
				//$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));	
			}	
			else
			{
				$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
			}
		}		
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
		
		if($mode == 'input')
		{	
			$this->Response->redirect($this->Service->constructUrl('Lab.masterLabPaketBaru'));
		}		
		elseif($mode == 'ya')
		{	
			$this->Response->reload();
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));	
		}
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
		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));		
	}
}
?>