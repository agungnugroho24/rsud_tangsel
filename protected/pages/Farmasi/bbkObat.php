<?php
class bbkObat extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->secondPanel->Display='None';
			$this->cetakBtn->Enabled=false;
			$this->showGrid->Display = 'None';	
			
			$this->expCtrl->Display='None';
			$this->valTglExp->Enabled = false;
				
			//$this->jmlStokCtrl->Display='Dynamic';
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->DDTujuan->dataBind();			
			$sqlNmObat="SELECT tbm_obat.kode,tbm_obat.nama 
					FROM tbt_stok_lain 
					INNER JOIN tbm_obat ON (tbt_stok_lain.id_obat = tbm_obat.kode)
					WHERE tbt_stok_lain.sumber = '01' AND tbt_stok_lain.tujuan = '2' AND tbt_stok_lain.jumlah > '0'
					ORDER BY tbm_obat.nama";
			$this->DDnmObat->DataSource=$this->queryAction($sqlNmObat,'S');
			$this->DDnmObat->dataBind();
			$this->DDnmObat->Enabled=true;
			$this->nama->Enabled=true;
			
			$sql = "SElECT nip, real_name FROM tbd_user ORDER BY real_name";
			$this->DDPetugas->DataSource = UserRecord::finder()->findAllBySql($sql);
			$this->DDPetugas->dataBind();
			
			$idPetugas = $this->User->IsUserNip;
			$this->DDPetugas->SelectedValue = $idPetugas;
			
			$sql = "SElECT nip, real_name FROM tbd_user WHERE nip <> '$idPetugas' ORDER BY real_name";
			$this->DDPenerima->DataSource = UserRecord::finder()->findAllBySql($sql);
			$this->DDPenerima->dataBind();
			
			$this->DDPenerima->Enabled = true;
			
			$this->DDTujuan->Focus();
		}
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT id,kode,sumber,tujuan, SUM(jumlah) AS jumlah FROM $nmTable GROUP BY kode ORDER BY id ";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();			
		}
		
		
		$purge=$this->Request['purge'];
		$nmTable=$this->Request['nmTable'];
		
		if($purge =='1'	)
		{			
			if(!empty($nmTable))
			{		
				$this->queryAction($nmTable,'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
						
			$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat',array('goto'=>'1')));
		}	
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->DDTujuan->Focus();
		}
	}
	
	
	protected function modeDistribusiChanged()
	{
		if($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
		{
			$this->DDTujuan->SelectedValue = '2';
			$this->DDTujuan->Enabled = false;
			$this->DDPetugas->Focus();
		}
		else
		{
			$this->DDTujuan->Enabled = true;
			$this->DDTujuan->Focus();
		}
		
	}
	
	
	protected function DDPetugasChanged()
	{
		if($this->DDPetugas->SelectedValue != 'empty')
		{
			$idPetugas = $this->DDPetugas->SelectedValue;
			
			$sql = "SElECT nip, real_name FROM tbd_user WHERE nip <> '$idPetugas' ORDER BY real_name";
			$this->DDPenerima->DataSource = UserRecord::finder()->findAllBySql($sql);
			$this->DDPenerima->dataBind();
			
			$this->DDPenerima->Enabled=true;
			$this->DDPenerima->Focus();
		}
		else
		{
			$this->DDPenerima->SelectedValue != 'empty';
			$this->DDPenerima->Enabled=false;
			
			$this->DDPenerima->Enabled=false;
			$this->DDPetugas->Focus();
		}
		
	}
	
	public function prosesCallBack($sender,$param)
   	{
		//$this->form2->render($param->getNewWriter());
	}
	
	protected function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			//$this->BTproses->Enabled=false;
			$this->DDTujuan->Enabled=false;
			$this->nmPetugas->Enabled=false;
			$this->nmPenerima->Enabled=false;
			
			$this->DDPetugas->Enabled=false;
			$this->DDPenerima->Enabled=false;
			
			$this->ket->Enabled=false;
			$this->BTproses->Enabled=false;
			
			$this->secondPanel->Display='Dynamic';	 	
			$this->DDnmObat->Enabled=false;
			$this->nama->Enabled=false;
			$tmp=$this->DDTujuan->SelectedValue;/*
			$this->DDSumMaster->DataSource=DesFarmasiRecord::finder()->find('id <> ?',$tmp);
			$this->DDSumMaster->dataBind();*/
			$sql="SELECT id, nama FROM tbm_destinasi_farmasi WHERE id <> '$tmp' ";
			$this->DDSumMaster->DataSource=$this->queryAction($sql,'S');
			$this->DDSumMaster->dataBind();
			
			if($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
			{
				$this->expCtrl->Enabled = true;
				$this->expCtrl->Display = 'Dynamic';
				$this->valTglExp->Enabled = true;
				
				$this->expCtrl->getAttributes()->Add('Style', 'display: inline;');
			}
			else
			{
				$this->expCtrl->Enabled = false;
				$this->expCtrl->Display = 'None';
				$this->valTglExp->Enabled = false;
			}
			
			$this->Page->CallbackClient->focus($this->DDSumMaster);
		}
		
	}
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $sender->DataSource=$this->getDummyData($token);
        $sender->dataBind();                                                                                                     
    }
	
	public function getDummyData($token) 
	{
		if($this->getViewState('sqlObat'))
		{
			$sql = $this->getViewState('sqlObat');
			$sql .= " AND tbm_obat.nama LIKE '$token%' GROUP BY tbt_stok_lain.id_obat
					ORDER BY
					  tbm_obat.nama "; 
			
			$arr = $this->queryAction($sql,'S');
		}
		else
		{
			$arr = '';
		}
		
		return $arr;
    }
	
	public function suggestionSelected1($sender,$param) 
	{
        $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
		
		if($id)
			$this->DDnmObat->SelectedValue = $id;	
		else
			$this->DDnmObat->SelectedValue = 'empty';
		
		$this->DDnmObatChanged();
    }
	
	public function secondCallBack($sender,$param)
   	{
		$this->secondPanel->render($param->getNewWriter());
	}
	
	public function DDSumMasterChanged()
	{
		if($this->DDSumMaster->SelectedValue != '')
		{
			$tmp=$this->DDSumMaster->SelectedValue;
			$this->DDnmObat->Enabled=true;
			$this->nama->Enabled=true;
			
			$sql = "SELECT
					  tbm_obat.kode,
					  tbm_obat.nama,
					  SUM(tbt_stok_lain.jumlah) AS jumlah
					FROM
					  tbt_stok_lain
					  INNER JOIN tbm_obat ON (tbt_stok_lain.id_obat = tbm_obat.kode)
					WHERE
					  tbt_stok_lain.tujuan = '$tmp' 
					  AND jumlah > 0 ";
			
			$sqlObat = $sql;
					  
			$sql .= " GROUP BY tbt_stok_lain.id_obat
					ORDER BY
					  tbm_obat.nama";
			
			if(count($this->queryAction($sql,'S')) > 0) //jika data obat ada untuk sumber obat tersedia
			{
				$this->DDnmObat->DataSource=$this->queryAction($sql,'S');
				$this->DDnmObat->dataBind();
				$this->errMsg->Text = "";
				$this->setViewState('sqlObat',$sqlObat);
				$this->Page->CallbackClient->focus($this->nama);
			}
			else
			{	
				$this->DDnmObat->SelectedIndex = -1;
				$this->DDnmObat->Enabled = false;
				$this->nama->Enabled=false;
				$this->errMsg->Text = "Maaf, belum ada obat untuk sumber obat yg dipilih";
				$this->clearViewState('sqlObat');
				$this->Page->CallbackClient->focus($this->DDSumMaster);
			}	
		}
		else
		{
			$this->DDnmObat->SelectedIndex = -1;
			$this->DDnmObat->Enabled = false;
			$this->nama->Enabled=false;
			$this->errMsg->Text = "";
			$this->Page->CallbackClient->focus($this->DDSumMaster);
		}
		
		$this->showSql->text=$sql;
		//$this->jmlStokCtrl->Display='Dynamic';
	}
	
	protected function DDnmObatChanged()
	{
		if($this->DDnmObat->SelectedValue=='')
		{
			$this->jmlAmbil->Enabled=false;
			//$this->jmlStokCtrl->Display='Dynamic';
		}
		else
		{
			//$sumber = $this->getViewState('sumber');
			//$idObat=$this->DDnmObat->SelectedValue;
			$this->jmlAmbil->Text='';
			$this->jmlAmbil->Enabled=true;
			
			$idTujuanDistribusi = $this->DDTujuan->SelectedValue;
			$idTujuan = $this->DDSumMaster->SelectedValue;
			$idObat = $this->DDnmObat->SelectedValue;
			
			if($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
			{
				if($this->tglExp->Text != '')
				{
					$exp = $this->convertDate($this->tglExp->Text,'2');
				}
				else
				{
					$exp = '';
				}
						
				$sql = "SELECT
					  SUM(tbt_stok_lain.jumlah) AS jumlah
					FROM
					  tbt_stok_lain
					WHERE
					  tbt_stok_lain.tujuan = '$idTujuan'
					  AND tbt_stok_lain.id_obat = '$idObat' 
					  AND tbt_stok_lain.expired = '$exp' 
					GROUP BY tbt_stok_lain.expired";
			}
			else
			{
				$this->Page->CallbackClient->focus($this->jmlAmbil);				
				
				$sql = "SELECT
					  SUM(tbt_stok_lain.jumlah) AS jumlah
					FROM
					  tbt_stok_lain
					WHERE
					  tbt_stok_lain.tujuan = '$idTujuan'
					  AND tbt_stok_lain.id_obat = '$idObat' 
					GROUP BY tbt_stok_lain.id_obat";
			}
			
			
			//$this->jmlStokCtrl->Display='Dynamic';
			
			if(StokLainRecord::finder()->findBySql($sql))
			{
				$jmlStok = StokLainRecord::finder()->findBySql($sql)->jumlah;
				$this->jmlStok->Text=$jmlStok;
				
				$this->Page->CallbackClient->focus($this->jmlAmbil);	
				$this->BTambil->Enabled=true;
				
				$idDestinasi = DesFarmasiRecord::finder()->findByPk($idTujuanDistribusi)->id;
				$fieldMax = 'max_'.$idDestinasi;
				
				$maxLoket = ObatRecord::finder()->findByPk($idObat)->$fieldMax;
				
				if($maxLoket > 0)
				{					
					$sql = "SELECT
					  SUM(tbt_stok_lain.jumlah) AS jumlah
					FROM
					  tbt_stok_lain
					WHERE
					  tbt_stok_lain.tujuan = '$idTujuanDistribusi'
					  AND tbt_stok_lain.id_obat = '$idObat' 
					GROUP BY tbt_stok_lain.id_obat";
					
					$jmlStokLain = StokLainRecord::finder()->findBySql($sql)->jumlah;
					$this->jmlAmbil->Text = $maxLoket - $jmlStokLain;
					$this->setViewState('maxLoket',$maxLoket - $jmlStokLain);
				}
				else
				{
					$this->clearViewState('maxLoket');
				}
			}
			else
			{		
				if($this->tglExp->Text != '')
				{
					$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>'.$this->ambilTxt($this->DDnmObat).' dengan tanggal expired : <br/>'.$this->tglExp->Text.' tidak ditemukan !</p>\',timeout: 4000,dialog:{modal: true}});');
				}
						
				$this->tglExp->Text = '';
				$this->Page->CallbackClient->focus($this->tglExp);	
				$this->jmlStok->Text='0';
				////$this->jmlStokCtrl->Display='Dynamic';
				$this->BTambil->Enabled=false;
				
				
			}	
			//$this->jmlAmbil->Text=$this->DDnmObat->SelectedValue.$sumber;
		}		
	}
	
	public function checkJml($sender,$param)
    {    
		$sumber = '01';
		$tmp=$this->DDSumMaster->SelectedValue;
		$idObat=$this->DDnmObat->SelectedValue;
		$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tujuan='$tmp' ";
		$jmlGudang=StokLainRecord::finder()->findBySql($sql);   
		$param->IsValid=($this->jmlAmbil->Text <= $jmlGudang->getColumnValue('jumlah'));
    }
	
	public function prosesAmbilClicked()
    {
		$idTujuan = $this->DDSumMaster->SelectedValue;
		$idObat = $this->DDnmObat->SelectedValue;
		
		$jmlTotalStok = $this->jmlStok->Text;	
		$jmlAmbil = $this->jmlAmbil->Text;
		$this->setViewState('jmlKekurangan',$jmlAmbil);
		
		if($this->getViewState('maxLoket'))
		{
			$maxLoket = $this->getViewState('maxLoket');
		}
		else
		{
			$maxLoket = $jmlAmbil+1;
		}
		
		if($jmlTotalStok >= $jmlAmbil) //jika jumlah total stok mencukupi
		{
			if($this->modeDistribusi->SelectedValue == '0') //mode distribusi normal
			{
				if($jmlAmbil > $maxLoket)
				{
					$this->getPage()->getClientScript()->registerEndScript('', 'alert(\'Jumlah yang diambil melebihi jumlah max yang telah ditentukan di '.$this->ambilTxt($this->DDTujuan).' !\')');  
					$this->Page->CallbackClient->focus($this->jmlAmbil);
				}
				else
				{
					$sql = "SELECT 
								id,jumlah, id_harga,expired 
							FROM 
								tbt_stok_lain 
							WHERE 
								id_obat='$idObat'
								AND tujuan ='$idTujuan'
							ORDER BY id_harga DESC";
							
					$arr = $this->queryAction($sql,'S');
					
					foreach($arr as $row)
					{
						if($row['jumlah'] <> 0)
						{
							if($row['jumlah'] > $this->getViewState('jmlKekurangan'))
							{
								if($this->getViewState('jmlKekurangan') > 0)
								{
									$id_tbt_stok_lain = $row['id'];
									$id_harga = $row['id_harga'];
									$jmlAmbil = $this->getViewState('jmlKekurangan');
									$expired = $row['expired'];
									$this->setViewState('jmlKekurangan','0');
									$this->makeTmpTbl($id_tbt_stok_lain,$id_harga,$jmlAmbil,$expired);
								}
							}
							else
							{
								if($this->getViewState('jmlKekurangan') > 0)
								{
									$id_tbt_stok_lain = $row['id'];
									$id_harga = $row['id_harga'];
									$jmlAmbil = $row['jumlah'];
									$expired = $row['expired'];
									$jmlKekurangan = $this->getViewState('jmlKekurangan') - $jmlAmbil;
									$this->setViewState('jmlKekurangan',$jmlKekurangan);	
									$this->makeTmpTbl($id_tbt_stok_lain,$id_harga,$jmlAmbil,$expired);
								}
							}
						}
					}
				}
			}
			elseif($this->modeDistribusi->SelectedValue == '1') //mode distribusi rusak
			{
				$exp = $this->convertDate($this->tglExp->Text,'2');
				
				$sql = "SELECT 
							id,jumlah, id_harga,expired
						FROM 
							tbt_stok_lain 
						WHERE 
							id_obat='$idObat'
							AND tujuan ='$idTujuan'
							AND expired ='$exp'
						ORDER BY id_harga DESC";
						
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					if($row['jumlah'] <> 0)
					{
						if($row['jumlah'] > $this->getViewState('jmlKekurangan'))
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_tbt_stok_lain = $row['id'];
								$id_harga = $row['id_harga'];
								$jmlAmbil = $this->getViewState('jmlKekurangan');
								$expired = $row['expired'];
								$this->setViewState('jmlKekurangan','0');
								$this->makeTmpTbl($id_tbt_stok_lain,$id_harga,$jmlAmbil,$expired);
							}
						}
						else
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_tbt_stok_lain = $row['id'];
								$id_harga = $row['id_harga'];
								$jmlAmbil = $row['jumlah'];
								$expired = $row['expired'];
								$jmlKekurangan = $this->getViewState('jmlKekurangan') - $jmlAmbil;
								$this->setViewState('jmlKekurangan',$jmlKekurangan);	
								$this->makeTmpTbl($id_tbt_stok_lain,$id_harga,$jmlAmbil,$expired);
							}
						}
					}
				}
			}
			
			$this->DDnmObat->SelectedValue = 'empty';	
			$this->nama->Text = '';	
			$this->Page->CallbackClient->focus($this->nama);	
				
			//$this->jmlStokCtrl->Display='Dynamic';
		}
		else
		{
			if(!$this->getViewState('nmTable'))
			{
				$this->showGrid->Display = 'None';				
			}
			
			$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p> Jumlah yang diambil melebihi jumlah stok yang ada !</p>\',timeout: 4000,dialog:{modal: true}});');
						
			$this->jmlAmbil->Text = '';
			$this->Page->CallbackClient->focus($this->jmlAmbil);	
				
			//$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			//$this->msgStok->Text=$tmpStok;
		}	
	}
	
	public function ambilClicked()
    {
		$idTujuan = $this->DDSumMaster->SelectedValue;
		$idObat = $this->DDnmObat->SelectedValue;		
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT SUM(jumlah) AS jumlah FROM $nmTable WHERE kode = '$idObat' GROUP BY kode ORDER BY id ";
			if($this->queryAction($sql,'S'))
			{
				$this->getPage()->getClientScript()->registerEndScript('', 'alert(\'Item barang sudah ditambahkan sebelumnya !\')');  	
				$this->DDnmObat->SelectedValue = 'empty';	
				$this->nama->Text = '';	
				$this->jmlStok->Text = '';	
				$this->jmlAmbil->Text = '';	
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
				$this->prosesAmbilClicked();
		}
		else
		{
			$this->prosesAmbilClicked();
		}	
	}
	
	protected function makeTmpTbl($id_tbt_stok_lain,$id_harga,$jmlAmbil,$expired)
	{
		if($this->IsValid)  // when all validations succeed
        {
			$this->showGrid->Display = 'Dynamic';				
			$this->msgStok->Text='';	
		
			$this->cetakBtn->Enabled=true;
			$idObat=$this->DDnmObat->SelectedValue;
			$tujuan=TPropertyValue::ensureString($this->DDTujuan->SelectedValue);
			$sumber=TPropertyValue::ensureString($this->DDSumMaster->SelectedValue);
			
			//$jumlah=$this->jmlAmbil->Text;
			$this->test->text=$idObat;
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 									 
											 kode VARCHAR(5) NOT NULL,
											 sumber VARCHAR(30) NOT NULL,
											 tujuan VARCHAR(30) NOT NULL,				
											 jumlah INT(11) NOT NULL,
											 id_harga VARCHAR(20) NOT NULL,
											 id_tbt_stok_lain VARCHAR(20) NOT NULL,	
											 expired DATE DEFAULT '0000-00-00',			
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...					
				
				$sql="INSERT INTO 
						$nmTable(kode,sumber,tujuan,jumlah,id_harga,id_tbt_stok_lain,expired) 
					VALUES ('$idObat','$sumber','$tujuan','$jmlAmbil','$id_harga','$id_tbt_stok_lain','$expired')";
					
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT id,kode,sumber,tujuan,expired , SUM(jumlah) AS jumlah FROM $nmTable GROUP BY kode ORDER BY id ";
				
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();				
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql="INSERT INTO 
						$nmTable(kode,sumber,tujuan,jumlah,id_harga,id_tbt_stok_lain,expired) 
					VALUES ('$idObat','$sumber','$tujuan','$jmlAmbil','$id_harga','$id_tbt_stok_lain','$expired')";
					
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT id,kode,sumber,tujuan,expired, SUM(jumlah) AS jumlah FROM $nmTable GROUP BY kode ORDER BY id ";
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();						
			}
			 	
			$this->DDnmObat->Enabled=true;
			$this->nama->Enabled=true;
			
			$this->jmlAmbil->Text='';
			$this->jmlAmbil->Enabled=false;
			
			$this->tglExp->Text = '';
			
			$this->show->Text = $this->getViewState('nmTable');
		}	
		
	}
	
	public function deleteButtonClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			$nmTable = $this->getViewState('nmTable');
				
			$sql = "DELETE FROM $nmTable WHERE id='$ID'";
			$this->queryAction($sql,'C');								
						
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$jmlData=0;
			foreach($arrData as $row)
			{
				$jmlData++;
			}
				
			if($jmlData==0)
			{
				$this->cetakBtn->Enabled = false;
				
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
				$this->clearViewState('nmTable');//Clear the view state	
			}
				
			$this->UserGrid->DataSource = $arrData;
			$this->UserGrid->dataBind();	
			
			$this->DDnmObat->SelectedValue = 'empty';	
			$this->nama->Text = '';	
			$this->Page->CallbackClient->focus($this->nama);						
		//}	
    }	
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function changePagerPosition($sender,$param)
	{		
		$position='TopAndBottom';		
		$this->UserGrid->PagerStyle->Position=$position;
		$this->UserGrid->PagerStyle->Visible=true;		
	}
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	public function blnRomawi($input)
    {		
		switch ($input) 
		{
			case "01":
				$bln='I';
				break;
			case "02":
				$bln='II';
				break;	
			case "03":
				$bln='II';
				break;	
			case "04":
				$bln='IV';
				break;	
			case "05":
				$bln='V';
				break;	
			case "06":
				$bln='IV';
				break;		
			case "07":
				$bln='IIV';
				break;		
			case "08":
				$bln='IIIV';
				break;		
			case "09":
				$bln='IX';
				break;	
			case "10":
				$bln='X';
				break;	
			case "11":
				$bln='XI';
				break;	
			case "12":
				$bln='XII';
				break;	
		}
		return $bln;
	}
	
	public function numCounter($activeTable)
    {			
		if($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
			$nmTableBbk = 'tbt_bbk_barang_rusak';
		else
			$nmTableBbk = 'tbt_bbk_barang';	
		
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT no_bbk FROM $nmTableBbk order by no_bbk desc";
		$no = $find->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$thn=date("Y");
			$bln=$this->blnRomawi(date("m"));			
			$urut='000001';
			$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
		}else{
			$thn=date("Y");
			$bln=$this->blnRomawi(date("m"));
			$urut=intval(substr($no->getColumnValue('no_bbk'),0,6));
			if ($urut==999999)
			{
				$urut=1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
			}else{
				$urut=$urut+1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
			}
		}
		return $nobbk;
	}	
	
	public function cetakClicked($sender,$param)
	{	
		$tujuan=TPropertyValue::ensureString($this->DDTujuan->SelectedValue);	
		$sumMas=TPropertyValue::ensureString($this->DDSumMaster->SelectedValue);
		$nmTable=$this->getViewState('nmTable');
		
		if($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
			$nobbk=$this->numCounter(BbkObatRusakRecord::finder());
		else
			$nobbk=$this->numCounter(BbkObatRecord::finder());
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{	
			$id_tbt_stok_lain=$row['id_tbt_stok_lain'];	
			$idObat=$row['kode'];
			$sumber=$row['sumber'];
			$id_harga=$row['id_harga'];
			$jumlah=$row['jumlah'];
			$expired=$row['expired'];
			
			
			if($this->modeDistribusi->SelectedValue == '0') //mode distribusi barang normal
			{
				//$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tujuan='$tujuan' AND id_harga='$id_harga'";
				
				$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND tujuan='$tujuan' AND id_harga='$id_harga' AND expired='$expired' ";
				
				$data=StokLainRecord::finder()->findBySql($sql);
				if($data===NULL)
				{
					//data baru untuk tbt_stok_lain
					$dataStokLain=new StokLainRecord;
					$dataStokLain->id_obat=$row['kode'];
					//$dataStokLain->sumber=$row['sumber'];
					$dataStokLain->sumber='01'; //sumber 01 adalah sumber pendanaan dari APBD
					$dataStokLain->tujuan=$tujuan;
					$dataStokLain->jumlah=$row['jumlah'];
					$dataStokLain->id_harga=$row['id_harga'];
					$dataStokLain->expired=$row['expired'];
							
					//save ke tbt_stok_lain
					$dataStokLain->save();
				}
				else
				{
					//update jumlah di tbt_stok_lain
					$data->jumlah = $jumlah + $data->getColumnValue('jumlah');
					$data->save();
				}
				
				//data baru untuk tbt_bbk_barang
				$dataBbkObat=new BbkObatRecord;
				//$dataBbkObat->no_bbk=$this->numCounter('tbt_bbk_barang',BbkObatRecord::finder(),'50');
				$dataBbkObat->no_bbk=$nobbk;
				$dataBbkObat->kode_obat=$idObat;
				$dataBbkObat->jumlah=$row['jumlah'];
				$dataBbkObat->tgl=date("Y-m-d");
				
				$dataBbkObat->id_petugas=$this->DDPetugas->SelectedValue;
				$dataBbkObat->id_penerima=$this->DDPenerima->SelectedValue;
				
				$dataBbkObat->petugas=$this->ambilTxt($this->DDPetugas);
				$dataBbkObat->penerima=$this->ambilTxt($this->DDPenerima);
				
				$dataBbkObat->tujuan=$tujuan;
				$dataBbkObat->asal_dari=$sumber;
				//$dataBbkObat->sumber=$row['sumber']; //sumber tidak di update karena RS. SWASTA
				$dataBbkObat->sumber='01'; 
				$dataBbkObat->keterangan=$this->ket->Text;
				
				//save ke tbt_bbk_barang
				$dataBbkObat->save();
			}
			elseif($this->modeDistribusi->SelectedValue == '1') //mode distribusi barang rusak
			{
				
				//INSERT tbt_bbk_barang_rusak
				$dataBbkObat=new BbkObatRusakRecord;
				$dataBbkObat->no_bbk=$nobbk;
				$dataBbkObat->kode_obat=$idObat;
				$dataBbkObat->jumlah=$row['jumlah'];
				$dataBbkObat->tgl=date("Y-m-d");
				$dataBbkObat->id_petugas=$this->DDPetugas->SelectedValue;
				$dataBbkObat->id_penerima=$this->DDPenerima->SelectedValue;
				$dataBbkObat->petugas=$this->ambilTxt($this->DDPetugas);
				$dataBbkObat->penerima=$this->ambilTxt($this->DDPenerima);
				$dataBbkObat->tujuan=$tujuan;
				$dataBbkObat->asal_dari=$sumber;
				//$dataBbkObat->sumber=$row['sumber']; //sumber tidak di update karena RS. SWASTA
				$dataBbkObat->sumber='01'; 
				$dataBbkObat->keterangan=$this->ket->Text;
				
				$dataBbkObat->id_harga = $id_harga;
				$dataBbkObat->hrg_ppn = HrgObatRecord::finder()->findByPk($id_harga)->hrg_ppn;
				$dataBbkObat->expired = $expired;
				
				$dataBbkObat->save();
			}
						
			
			//Mengurangi stok barang di stok yang diambil
			//$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tujuan='$sumMas' AND id_harga='$id_harga'";
			
			$sql="SELECT * FROM tbt_stok_lain WHERE id = '$id_tbt_stok_lain' AND  id_obat='$idObat' AND tujuan='$sumMas' AND id_harga='$id_harga' AND expired='$expired' ";
			
			$data=StokLainRecord::finder()->findBySql($sql);
			$data->jumlah=$data->getColumnValue('jumlah') - $jumlah;
			//update jumlah di tbt_stok_gudang
			$data->save();
		}				
		
		//$this->clearViewState('nmTable');//Clear the view state
		//$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat'));
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakBbkObat',array('nobbk'=>$nobbk,'petugas'=>$this->ambilTxt($this->DDPetugas),'tujuan'=>$tujuan,'penerima'=>$this->ambilTxt($this->DDPenerima),'table'=>$this->getViewState('nmTable'))));
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
		
		//$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);
		
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
