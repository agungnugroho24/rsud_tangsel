<?php
class beritaRad extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '8' ORDER BY nama";
			$this->DDDokterRad->DataSource = $this->queryAction($sql,'S');
			$this->DDDokterRad->dataBind();
			
			$this->DDReg->Enabled = false;
			
			$this->showSecond->Display = 'None';
			$this->showTable2->Display = 'None';
			$this->showTable->Display = 'None';
			
			$this->cetakBtn->Enabled=false;
			
			$this->cariCm->Focus();
			
			$jnsPasien = $this->modeInput->SelectedValue;				
			
			if($this->Request['cm'] !='' && $this->Request['noTrans'] != '' && $this->Request['noReg'] != '' && $this->Request['jnsPasien'] != '')
			{
				$this->modeInput->SelectedValue = $this->Request['jnsPasien'];
				$this->cariCm->Text = $this->Request['cm'];
				$this->DDReg->SelectedValue = $this->Request['noReg'];
				
				$this->checkCm($sender,$param);
				$this->checkRegister($sender,$param);
			}
			
			$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Rad.CariPasienRad&parentPage=Rad.beritaRad&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		}
			
		if($this->getViewState('nmTableObat'))
		{
			$nmTableObat = $this->getViewState('nmTableObat');
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik, id_kel_imunisasi, id,  nama ASC ";
			
			$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGridObat->dataBind();	
		}
		else
		{
			$this->UserGridObat->DataSource = '';
			$this->UserGridObat->dataBind();	
		}	
		
		
		if ($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql="SELECT * FROM $nmTable ";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0)
				$this->cetakBtn->Enabled=false;
			else
				$this->cetakBtn->Enabled=true;
			
			//$this->showSql->Text = count($arr);
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
			
			$this->clearViewState('notrans');
			$this->clearViewState('cm');			
			$this->clearViewState('nama');
			$this->clearViewState('nmTable');
			
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';			
			$this->errMsg->Text='';
			
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Rad.beritaRad',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function showFirstLock()
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		$this->DDReg->Enabled = false;
	}
	
	public function showFirstCallback($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
		$this->showTable2->render($param->getNewWriter());
	}
	
	public function showSecondCallback($sender,$param)
    {
		$this->showSecond->render($param->getNewWriter());;
	}
	
	public function DDRegCallBack($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
	}
	
	
	public function modeInputChanged($sender,$param)
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0' || $jnsPasien == '1') //pasien rawat jalan / inap
		{
			$this->cariCm->Enabled = true;	
			$this->DDReg->Enabled = false;
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cariCm->getClientID().'.focus();');	
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //Jika pasien Bebas / Bebas Karyawan
		{
			$this->cariCm->Enabled = false;	
			$this->DDReg->Enabled = true;
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			
			$sql = "SELECT 
					  tbt_rad_penjualan_lain.no_trans,
					  tbt_rad_penjualan_lain.no_trans_pas_luar,
					  tbt_rad_penjualan_lain.no_reg,
					  tbt_rad_penjualan_lain.id_tindakan,
					  tbd_pasien_luar.nama
					FROM
					  tbt_rad_penjualan_lain
					  INNER JOIN tbd_pasien_luar ON (tbt_rad_penjualan_lain.no_trans_pas_luar = tbd_pasien_luar.no_trans)
					WHERE 
						tbt_rad_penjualan_lain.no_reg <> '' AND
						tbt_rad_penjualan_lain.st_cetak_hasil = '0' AND 
						tbt_rad_penjualan_lain.st_ambil = '0' 
					GROUP BY tbt_rad_penjualan_lain.no_reg
					ORDER BY tbt_rad_penjualan_lain.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					$nama = $row['nama'];
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nama);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}	
		}
		
		$jnsPasien = $this->modeInput->SelectedValue;
			
		$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Rad.CariPasienRad&parentPage=Rad.beritaRad&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		
		$this->dataStokObat();
	}
	
	public function checkCm($sender,$param)
    {
		$cm = $this->formatCm($this->cariCm->Text);
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sql = "SELECT 
					  tbt_rad_penjualan.no_trans,
					  tbt_rad_penjualan.no_trans_rwtjln,
					  tbt_rad_penjualan.no_reg,
					  tbt_rad_penjualan.id_tindakan,
					  tbm_poliklinik.nama AS nm_poli
					FROM
					  tbt_rad_penjualan
					  INNER JOIN tbt_rawat_jalan ON (tbt_rad_penjualan.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbm_poliklinik ON (tbt_rad_penjualan.klinik = tbm_poliklinik.id)
					WHERE 
						tbt_rad_penjualan.no_reg <> '' AND
						tbt_rad_penjualan.cm = '$cm' AND 
						tbt_rad_penjualan.st_cetak_hasil = '0' AND 
						tbt_rad_penjualan.st_ambil = '0'  AND
						tbt_rawat_jalan.st_alih = '0' 
					GROUP BY tbt_rad_penjualan.no_reg 
					ORDER BY tbt_rad_penjualan.no_reg ";
						
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noTransJln = $row['no_trans_rwtjln'];
					$noReg = $row['no_reg'];
					$nmKlinik = $row['nm_poli'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nmKlinik);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$sql = "SELECT 
					  tbt_rad_penjualan_inap.no_trans,
					  tbt_rad_penjualan_inap.no_trans_inap,
					  tbt_rad_penjualan_inap.no_reg,
					  tbt_rad_penjualan_inap.id_tindakan
					FROM
					  tbt_rad_penjualan_inap
					  INNER JOIN tbt_rawat_inap ON (tbt_rad_penjualan_inap.no_trans_inap = tbt_rawat_inap.no_trans)
					WHERE 
						tbt_rad_penjualan_inap.no_reg <> '' AND
						tbt_rad_penjualan_inap.cm = '$cm' AND 
						tbt_rad_penjualan_inap.st_cetak_hasil = '0' AND 
						tbt_rad_penjualan_inap.st_ambil = '0' AND
						tbt_rawat_inap.status = '0' 
					GROUP BY tbt_rad_penjualan_inap.no_reg
					ORDER BY tbt_rad_penjualan_inap.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}		
		}
	}
	
	public function cmNoFound($cm)
    {
		$this->showFirstLock();
		
		$jnsPasien = $this->modeInput->SelectedValue;		
		
		if($jnsPasien == '0') 
			$txt = 'Tidak ada transaksi radiologi Rawat Jalan yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '1')
			$txt = 'Tidak ada transaksi radiologi Rawat Inap yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '2')
			$txt = 'Tidak ada transaksi radiologi Pasien Luar yang bisa diproses';	
		
		$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">'.$txt.'</p>\',timeout: 4000,dialog:{
					modal: true
				}});
				document.all.'.$this->cariCm->getClientID().'.focus();');	
	}
	
	public function prosesUploadFoto($sender,$param)
	{
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		//$id = $param->CallbackParameter->Id;
		$noTrans = $sender->CommandParameter;
		$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.'\'); unmaskContent();');
		
		$url = "index.php?page=Rad.uploadFile&noTrans=".$noTrans."&index=".$index."&nmTableTmp=".$nmTableTmp;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'Upload File')");
			
		/*
		$this->getPage()->getClientScript()->registerEndScript
			('','
				var $dialog = jQuery.FrameDialog.create({
						url: "index.php?page=Rad.uploadFile&noTrans='.$noTrans.'&index='.$index.'&nmTableTmp='.$nmTableTmp.'",
						loadingClass: "loading-image",
						title: "Upload File",
						width: 650,
						height: 500,
						autoOpen: false
					});

				$dialog.dialog("open");
				jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
				unmaskContent();
			');
		*/	
		/*
		$this->getPage()->getClientScript()->registerEndScript
			('','
				var $dialog =
					jQuery.FrameDialog.create({
						url: "'.$this->Service->constructUrl('Rad.uploadFile',array('noTrans'=>$noTrans,'index'=>$index)).'",
						loadingClass: "loading-image",
						title: "Upload Foto",
						width: 600,
						height: 400,
						autoOpen: false
					});

				$dialog.dialog("open");
				jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
				unmaskContent();
			');
		*/	
	}
	
	//------------------------------------------- BHP ------------------------------------------------------------	
	public function dataStokObat()
	{
		$modeStok = '15';
		$this->clearViewState('modeStok');
		$this->setViewState('modeStok',$modeStok);
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$this->setViewState('jnsPasien',$jnsPasien);
		$tujuan = '15';
		
		$sql= "SELECT 
				tbm_obat.kode AS kode,
				IF(LENGTH(tbm_obat.nama_generik) > 0,CONCAT(tbm_obat.nama,' (',tbm_obat.nama_generik,')'), tbm_obat.nama) AS nama,
				SUM(tbt_obat_harga.hrg_ppn) AS hrg_ppn
				FROM 
					tbm_obat
					INNER JOIN tbt_stok_lain ON (tbt_stok_lain.id_obat = tbm_obat.kode)
					INNER JOIN tbt_obat_harga ON (tbt_stok_lain.id_harga = tbt_obat_harga.id)
				WHERE 
					tbt_stok_lain.jumlah >= 1
					AND tbt_stok_lain.sumber='01' ";
				
		if($tujuan <> '')			
				$sql .= " AND tbt_stok_lain.tujuan='$tujuan'  ";
		
		$sqlObat = $sql;
		
		$sql .= " GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 		
		
		$arr = $this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;	
			$this->setViewState('sqlObat',$sqlObat);
		}
		else
		{
			$this->DDObat->Enabled=false;	
			$this->jml->Enabled=false;	
			$this->clearViewState('sqlObat');
		}	
	}
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $sender->DataSource=$this->getDummyData($token);
        $sender->dataBind();                                                                                                     
    }
 
    public function suggestionSelected1($sender,$param) {
        $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
		
		if($id)
		{
			$this->kodeObat->Text=$id;
			$this->chObat2();
		}
		else
		{
			$this->kodeObat->Text = '';
		}
		
    }
 
    public function getDummyData($token) 
	{
		if($this->modePaket->SelectedValue == '0')//Paket
		{
			if($this->getViewState('sqlObat'))
			{
				$sql = $this->getViewState('sqlObat');
				$sql .= " AND tbm_obat.nama LIKE '$token%' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
				
				$arr = $this->queryAction($sql,'S');
			}
			else
			{
				$arr = '';
			}
		}
		elseif($this->modePaket->SelectedValue == '1')//Mode Paket
		{
			$sql = "SELECT id AS kode, nama FROM tbm_obat_paket_kelompok WHERE nama LIKE '$token%' GROUP BY kode ORDER BY nama ASC "; 
			$arr = $this->queryAction($sql,'S');
		}
				
		return $arr;
    }
	
	public function tipeRacikChanged()
	{
		$this->jmlBungkus->Text = '';
		$tipeRacik = $this->collectSelectionResult($this->RBtipeRacik);
		$this->setViewState('tipeRacik',$tipeRacik);	
		if($tipeRacik == '0') //Non Racikan
		{
			$this->DDRacik->Enabled = false;
			$this->DDKemasan->Enabled = false;
			$this->jmlBungkus->Enabled = false;
			//$this->jmlBungkus->Enabled = false;
			//$this->Page->CallbackClient->focus($this->RBtipeObat);
			
		}
		elseif($tipeRacik == '1') //racikan
		{
			$this->setViewState('loaderRacik',0);
			$this->DDRacik->Enabled = true;
			$this->DDKemasan->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDRacik);
			
			if (!$this->getViewState('nmTableObat'))
			{
				$data[]=array('id'=>'0','nama'=>'Buat Kelompok Baru');
				
				$this->DDRacik->DataSource = $data;
				$this->DDRacik->dataBind();
				$this->DDRacik->SelectedValue = '0';
				
				$this->jmlBungkus->Enabled = true;
			}
			else
			{
				$nmTableObat = $this->getViewState('nmTableObat');
				$data[]=array('id'=>'0','nama'=>'Buat Kelompok Baru');
				
				$sql = "SELECT id_kel_racik FROM $nmTableObat WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id_kel_racik ";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//sudah ada obat racikan
				{
					foreach($arr as $row)
					{
						$idKelRacik = $row['id_kel_racik'];
						$data[]=array('id'=>$idKelRacik,'nama'=>'Racikan '.$idKelRacik);
					}
					
					$this->jmlBungkus->Enabled = false;
				}
				else //belum ada obat racikan
				{
					$idKelRacik = '0';
					$this->jmlBungkus->Enabled = true;
				}
								
				$this->DDRacik->DataSource = $data;
				$this->DDRacik->dataBind();
				$this->DDRacik->SelectedValue = $idKelRacik;
			}
		}
		
		else //Imunisasi
		{
			$this->DDRacik->Enabled = true;
			$this->DDKemasan->Enabled = true;
			$this->jmlBungkus->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDRacik);
							
			$this->DDRacik->DataSource = ImunisasiRecord::finder()->findAll();
			$this->DDRacik->dataBind();
		}
	}
	
	public function DDRacikChanged($sender,$param)
	{
		if($this->DDRacik->SelectedValue != '')
		{
			if($this->DDRacik->SelectedValue == '0')//bikin racikan baru
			{
				$loaderDDRacik = 
				$this->jmlBungkus->Enabled = true;
				$this->Page->CallbackClient->focus($this->jmlBungkus);
			}
			else
			{
				$this->jmlBungkus->Enabled = false;
				$this->Page->CallbackClient->focus($this->tambahBtn);
			}	
		}
		else
		{
			$this->jmlBungkus->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDRacik);
		}	
		
		$this->jmlBungkus->Text = '';		
	}
	
	public function chTipe()
	{
		//$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		//$this->DDSumMas->focus();		
	}	
	
	public function chKateg()
	{		
		if($this->DDKateg->SelectedValue=='01') //jika kelompok obat yg dipilih
		{			
			$this->Page->CallbackClient->focus($this->RBtipeRacik);
			$this->RBtipeRacik->Enabled=true;
			$this->RBtipeObat->Enabled=true;
		}
		else //jika kelompok alkes atau BHP yg dipilih
		{
			$this->RBtipeRacik->SelectedIndex=-1;
			$this->RBtipeRacik->Enabled=false;
			$this->RBtipeObat->SelectedIndex=-1;
			$this->RBtipeObat->Enabled=false;
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);
		}
		
		//$this->DDObat->enabled=true;
		$this->setViewState('kategori',$this->DDKateg->SelectedValue);
		$this->clearViewState('tipe');
		$this->clearViewState('jenis');
	}
	
	public function chJenis()
	{		
		if($this->DDJenis->SelectedValue!='') //jika kelompok obat yg dipilih
		{			
			$jnsObat = $this->DDJenis->SelectedValue;
			$this->setViewState('jenis',$jnsObat);
			$this->DDObat->enabled=true;	
		}
		else 
		{
			$this->clearViewState('jenis');
			$this->DDObat->enabled=false;
		}
		
	}
	
	public function chObat2()
	{
		if($this->kodeObat->Text != '')
		{
			$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
			$tujuan = '15';
			
			if($this->modePaket->SelectedValue == '0')//Mode NonPaket
				{
				//$this->errMsg->Text=$idObat .' - '.$tujuan;
				//Di Non aktifkan karena Alkes juga bisa masuk dalam Imunisasi
				//if(ObatRecord::finder()->findByPk($idObat)->kategori == '01') //jika kelompok obat yg dipilih
				//{					
					$this->RBtipeRacik->Enabled=true;				
				//}
				//else //jika kelompok alkes atau BHP yg dipilih
				//{
					//$this->RBtipeRacik->SelectedIndex=-1;
					//$this->RBtipeRacik->Enabled=false;
					//$this->DDRacik->Enabled=false;
					//$this->jmlBungkus->Enabled=false;
					//$this->jmlBungkus->Text='';
				//}
				
				//$this->test->text=$this->DDObat->SelectedValue;
				//
				$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idObat' AND tujuan = '$tujuan' GROUP BY id_obat,tujuan  ";
				$jmlStok = StokLainRecord::finder()->findBySql($sql)->jumlah;
				
				if ($this->getViewState('nmTableObat')) 
				{	
					$nmTableObat = $this->getViewState('nmTableObat');
					$sql = "SELECT jml FROM $nmTableObat WHERE id_obat = '$idObat' ";
					$arr = $this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlStok -= $row['jml'];
					}	
				}
				
				$this->jmlStok->Text = $jmlStok;
				
				$this->jml->Enabled=true;
				$this->msgStok->Text='';
				//$this->jml->text=$this->getViewState('tujuan');
				$this->setViewState('idObat',$idObat);
				$this->DDObat->SelectedValue = $idObat ;	
				
				$this->errMsg->Text = '    
					<script type="text/javascript">
						document.all.'.$this->RBtipeRacik->getClientID().'.focus();
					</script>';	
			}
			else //Mode Paket
			{
				$this->RBtipeRacik->Enabled=false;
			}
		}
		else
		{
			$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled=false;
			$this->DDRacik->Enabled=false;
			$this->DDRacik->SelectedValue = 'empty';
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			$this->clearViewState('idObat');
			
			$this->jmlStok->Text = '';
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->AutoComplete->getClientID().'.focus();
				</script>';	
		}
	}
	
	public function modePaketChanged()
	{
		$modePaket = $this->modePaket->SelectedValue;
		$this->CBabaikanHarga->Checked = false;
		
		if($modePaket == '1' && $this->User->IsAdmin)//mode paket
		{
			$this->CBabaikanHarga->Display = 'Dynamic';
			$this->Page->CallbackClient->focus($this->CBabaikanHarga);
		}
		else
		{
			$this->CBabaikanHarga->Display = 'None';
			$this->Page->CallbackClient->focus($this->AutoComplete);
		}
		
	}
	
	public function chObat()
	{
		if($this->DDObat->SelectedValue != '')
		{
			$idObat = $this->DDObat->SelectedValue;
			$tujuan = '15';
			//$this->errMsg->Text=$idObat .' - '.$tujuan;
			//Di Non aktifkan karena Alkes juga bisa masuk dalam Imunisasi
			//if(ObatRecord::finder()->findByPk($idObat)->kategori == '01') //jika kelompok obat yg dipilih
			//{	
				$this->RBtipeRacik->Enabled=true;
				
			//}
			//else //jika kelompok alkes atau BHP yg dipilih
			//{
				//$this->RBtipeRacik->SelectedIndex=-1;
				//$this->RBtipeRacik->Enabled=false;
				//$this->DDRacik->Enabled=false;
				//$this->jmlBungkus->Enabled=false;
				//$this->jmlBungkus->Text='';
			//}
			
			//$this->test->text=$this->DDObat->SelectedValue;
			//

			$this->jml->Enabled=true;
			//$this->msgStok->Text='';
			//$this->jml->text=$this->getViewState('tujuan');
			$this->setViewState('idObat',$idObat);
			$this->DDObat->SelectedValue = $idObat;
			$this->kodeObat->Text = $idObat;	
			$this->chObat2();
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->jml->getClientID().'.focus();
				</script>';	
		}
		else
		{
			$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled=false;
			$this->DDRacik->Enabled=false;
			$this->DDRacik->SelectedIndex = -1;
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			$this->clearViewState('idObat');
		}
	}
	
	public function prosesClicked()
    {
		if($this->IsValid)  // when all validations succeed
        {
			/*if($this->modeInput->SelectedValue == '2') //Jika Pasien Luar
			{
				//$jnsPasienLuar = $this->getViewState('jnsPasLuar');
										
				if($jnsPasienLuar=='01') //jika jenis pasien luar = Rujukan
				{	
					$this->setViewState('nmDokter',$this->dokter->Text);
					$this->setViewState('nmPasien',$this->nmPas->Text);			
						
				}
				elseif($jnsPasienLuar=='02') //jika jenis pasien luar = beli sendiri
				{
					$this->setViewState('nmDokter',$this->dokter->Text);
					$this->setViewState('nmPasien',$this->nmPas->Text);		
					$this->setViewState('modeBayar',$this->modeByrInap->SelectedValue);					
				}					
				
				if($this->modeKryPas->SelectedValue == '0')
				{
					$this->setViewState('modeBayar',$this->modeByrInap->SelectedValue);	
				}
			}*/
			
			//$tujuan = $this->getViewState('tujuan');
			
			if($this->modePaket->SelectedValue == '0')//Mode NonPaket
				$this->cekStok($jmlObat,$idBarang);
			else //mode Paket
			{
				$idKelPaket = $this->kodeObat->Text;
				$jml = $this->jml->Text;	
				$this->setViewState('jmlPaket',$jml);
				
				$sql = "SELECT * FROM tbm_obat_paket WHERE id_kelompok_paket = '$idKelPaket'";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$jmlObat = $row['jumlah'] * $jml;
					$idBarang = $row['id_obat'];
					
					$jnsPasien = $this->collectSelectionResult($this->modeInput);
					if($jnsPasien == '2')//Bila pasien luar
						$tujuan = '15';
					else //Bila pasien rawat inap atau rawat jalan
						$tujuan = '15';
					
					$sqlCek = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idBarang' AND tujuan = '$tujuan' GROUP BY tujuan,id_obat";
					foreach($this->queryAction($sqlCek,'S') as $rowCek)
					{
						$jmlStok = $rowCek['jumlah'];	
						
						if($jmlObat > $jmlStok)
						{
							$dataCekStokPaket[]=array('id_obat'=>$idBarang);	
						}
					}
					
					
					//$this->cekStok($jmlObat,$idBarang);
					/*
					if(count($dataCekStok) > 0)
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Stok obat dalam paket yang dipilih tidak cukup!</p>\',timeout: 3000,dialog:{
							modal: true}});');	
					}
					else
						$this->cekStok($jmlObat,$idBarang);
					*/	
				}	
				$this->setViewState('dataCekStokPaket',$dataCekStokPaket);
				$this->cekStokPaket();
			}
				
			//$this->cetakBtn->Enabled = true; if($this->modeInput->SelectedValue != '1'){$this->tundaBtn->Enabled = true;}	
		}
	}
	
	public function cekStokPaket()
    {
		$dataCekStokPaket = $this->getViewState('dataCekStokPaket');
		if(count($dataCekStokPaket) > 0)
		{
			foreach($dataCekStokPaket as $dataObat)
			{
				$obat .= ObatRecord::finder()->findByPk($dataObat['id_obat'])->nama.', ';
			}
			
			$this->DDObat->SelectedValue = 'empty';
			$this->kodeObat->Text = '';
			$this->AutoComplete->Text = '';
			$this->jmlStok->Text = '';
			$this->Page->CallbackClient->focus($this->DDObat);
			$this->jml->Text = '';
			$this->RBtipeRacik->Enabled = false;
			
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Stok '.$obat.' dalam paket yang dipilih tidak cukup!</p>\',timeout: 4000,dialog:{
				modal: true}});');	
		}
		else
		{
			$idKelPaket = $this->kodeObat->Text;
			$jml = $this->jml->Text;	
			$this->setViewState('jmlPaket',$jml);
			
			$sql = "SELECT * FROM tbm_obat_paket WHERE id_kelompok_paket = '$idKelPaket'";
			$arr = $this->queryAction($sql,'S');
			
			foreach($arr as $row)
			{
				$jmlObat = $row['jumlah'] * $jml;
				$idBarang = $row['id_obat'];
				$this->cekStok($jmlObat,$idBarang);
			}	
		}
			
	}
	
	public function cekStok($jmlObat='',$idBarang='')
    {
		if($this->modePaket->SelectedValue == '0')//Mode NonPaket
		{
			$jmlObat = $this->jml->Text;	
			$idBarang = $this->DDObat->SelectedValue; 
			$idBarang = $this->kodeObat->Text;
		}
			
		$this->setViewState('jmlKekurangan',$jmlObat);				
		//$tujuan = $tujuan;
		//$tujuan=$this->getViewState('tujuan');
		//$tujuan = $this->modeStok->SelectedValue;
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '2')//Bila pasien luar
		{
			$tujuan = '15';
		}
		else //Bila pasien rawat inap atau rawat jalan
		{
			$tujuan = '15';
		}
		
		//$this->test2->Text=$tujuan;
		//$tmpStok = StokLainRecord::finder()->findById_obat($this->DDObat->SelectedValue)->jumlah;
		
		
		$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idBarang' AND tujuan = '$tujuan' GROUP BY id";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tmpStok += $row['jumlah'];
		}	
		
		if ($this->getViewState('nmTableObat')) 
		{	
			$nmTableObat = $this->getViewState('nmTableObat');
			$sql = "SELECT jml FROM $nmTableObat WHERE id_obat = '$idBarang' ";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$tmpStok -= $row['jml'];
			}	
		}
				
		//$tmpStok = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$this->DDObat->SelectedValue,$tujuan)->jumlah;
		//$this->test3->text = $sql;
		
		if($tmpStok >= $jmlObat)
		{
			//cari jumlah minimal 
			$idTujuan = '15';
			$nmFieldMin = 'min_'.$idTujuan;
			$nmFieldTol = 'tol_'.$idTujuan;
			$sql="SELECT $nmFieldMin, $nmFieldTol  FROM tbm_obat WHERE kode ='$idBarang' ";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlMinimal = $row[$nmFieldMin]; 
				$persenTol = $row[$nmFieldTol];
			}
			
			//Periksa jml toleransi minimal
			$jmlStokTol = ($persenTol / 100) * $jmlMinimal;
			$nmBarang = $this->ambilTxt($this->DDObat);
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($tujuan)->nama;
			
			if( ($tmpStok-$jmlObat) < $jmlStokTol)//jika sudah melewati batas toleransi
			{
				$this->msgStok->Text='<br/><br/>Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' mencapai <b>Batas Toleransi Minimal</b>. <br/>Penambahan Obat Gagal';
				
				$this->jml->Text = '';
			}
			else //belum melewati batas toleransi
			{
				//$sql = "SELECT jumlah, id_harga FROM tbt_stok_lain WHERE id_obat='$idBarang' ORDER BY id_harga";
				//$sql = "SELECT jumlah, id_harga, expired FROM tbt_stok_lain WHERE id_obat='$idBarang' AND tujuan = '$tujuan' ORDER BY id_harga DESC"; 
				$sql = "SELECT jumlah, id_harga, expired FROM tbt_stok_lain WHERE id_obat='$idBarang' AND tujuan = '$tujuan' ORDER BY expired ASC"; 
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					if($row['jumlah'] > 0)
					{
						if($row['jumlah'] > $this->getViewState('jmlKekurangan'))
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_harga = $row['id_harga'];
								$expired = $row['expired'];
								$jmlAmbil = $this->getViewState('jmlKekurangan');
								$this->setViewState('jmlKekurangan','0');
								$this->makeTmpTbl($id_harga,$jmlAmbil,$expired,$idBarang);
								//$this->errMsg->Text=$id_harga.'-'.$jmlAmbil;
							}
						}
						else
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_harga = $row['id_harga'];
								$jmlAmbil = $row['jumlah'];
								$expired = $row['expired'];
								$jmlKekurangan = $this->getViewState('jmlKekurangan') - $jmlAmbil;
								$this->setViewState('jmlKekurangan',$jmlKekurangan);	
								$this->makeTmpTbl($id_harga,$jmlAmbil,$expired,$idBarang);
							}
						}
					}
				}
				
				//cek stok krisis => jika lebih kecil dari jml min di tbm_obat, keluarkan peringatan
				if( ($tmpStok-$jmlObat) < $jmlMinimal)
				{
					
					$this->msgStok->Text='Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' sudah mencapai <b>Stok Kritis</b>.';
				}
			}
			
			/*
			if( $this->modeStok->SelectedValue == '2') //minLoket
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_loket;
				//$nmTujuan = 'Apotik';
			}
			elseif( $this->modeStok->SelectedValue == '5') //minIGD
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_igd;
				//$nmTujuan = 'IGD';
			}
			elseif( $this->modeStok->SelectedValue == '6') //minOK
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_ok;
				//$nmTujuan = 'OK';
			}
			*/
			
		}
		else
		{
			if(!$this->getViewState('nmTableObat'))
			{
				$this->showGridObat->Visible=false;				
			}
			
			$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Stok obat yang ada tidak cukup!</p>\',timeout: 3000,dialog:{
							modal: true}});');	
							
			//$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			
			$this->DDObat->SelectedValue = 'empty';
			$this->kodeObat->Text = '';
			$this->AutoComplete->Text = '';
			$this->jmlStok->Text = '';
			$this->Page->CallbackClient->focus($this->DDObat);
			$this->jml->Text = '';
			//$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled = false;
			//$this->jmlBungkus->Text = '';
			//$this->jmlBungkus->Enabled = false;
			//$this->msgStok->Text=$tmpStok;
		}
	}
	
	public function makeTmpTbl($id_harga,$jmlAmbil,$expired,$idObat='')
	{
		$this->showGridObat->Visible=true;	
		//$this->msgStok->Text='';
		$kelompokPasien = $this->getViewState('kelompokPasien');
		$stAsuransi = $this->getViewState('stAsuransi');
		$tipeRacik = $this->RBtipeRacik->SelectedValue; //$this->getViewState('tipeRacik');
		$jnsPasien = $this->getViewState('jnsPasien');
		
		$resepHrg=$this->getViewState('resepHrg');
		$racikHrg=$this->getViewState('racikHrg');
		$bungkusRacikHrg=$this->getViewState('bungkusRacikHrg');
		
		$hrgTmp = 0;//Make initial value for $hrg
		$jml= $jmlAmbil;
		
		
		/*if($this->getViewState('modeKryPas') == '')
		{
			$modeKryPas = $this->collectSelectionResult($this->modeKryPas);
			$this->setViewState('modeKryPas',$modeKryPas);
			
		}*/	
			
			if($this->modePaket->SelectedValue == '0')//Mode NonPaket
			{
				$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
				$stPaket = '0';
				$idKelPaket = '';
				$jmlPaket = '0';
			}
			elseif($this->modePaket->SelectedValue == '1')//Mode Paket
			{
				$stPaket = '1';
				$idKelPaket = $this->kodeObat->Text;
				$jmlPaket = $this->getViewState('jmlPaket');
			}
			
			
			$tipe =  ObatRecord::finder()->findByPk($idObat)->tipe;
			$kategori = ObatRecord::finder()->findByPk($idObat)->kategori;
			$idKelompok = ObatRecord::finder()->findByPk($idObat)->kel_margin;
			$persenProvider = 0;
			
			if($jnsPasien != 4) //bukan penjulan internal
			{
				if($kelompokPasien == '02'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->findByPk($idKelompok)->persentase_asuransi / 100;
				}
				elseif($kelompokPasien == '07'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->findByPk($idKelompok)->persentase_jamper / 100;
				}
				else
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->findByPk($idKelompok)->persentase / 100;
				}
			}
			elseif($jnsPasien == '4') //penjulan internal
			{
				$persenMargin = ObatKelompokMarginRecord::finder()->findByPk($idKelompok)->persentase_unit_internal / 100;
			}
			
			$persenMargin = 0;
			
			//$tujuan = $this->getViewState('tujuan');
			//$tujuan = $this->modeStok->SelectedValue;			
			
			//jika obat dibeli dari APOTIK LUAR => set persenMargin = 15%
			if(HrgObatRecord::finder()->findByPk($id_harga)->st_pembelian == '1')
			{
				//$persenMargin = 15 / 100;
			}
			
			$r_item = JasaResepRacikanRecord::finder()->findByPk('1')->jasa_resep; 
			
			if($jnsPasien == '2')//Bila pasien luar
			{
				$tujuan = '15';
				
				if($this->embel->SelectedValue == '02')//beli sendiri
					$r_item = 0;
			}
			else //Bila pasien rawat inap atau rawat jalan
			{
				$tujuan = '15';
				
				if($jnsPasien == '1' || $jnsPasien == '3' || $jnsPasien == '4') //Pasien Rawat Inap / Unit Internal
					$r_item = 0;
			}
			
			/*
			if($this->getViewState('sumMas') == '2'){	
				$sumber = $this->getViewState('sumMas') . $this->getViewState('sumSek');
			}else{
				$sumber = $this->getViewState('sumMas');
			}*/
			
			//$this->errMsg->Text = $kelompokPasien .' - '. $jnsPasien.' - '.$tipeRacik;//showing status kelompok pasien
			
			$jmlObat = $jmlAmbil;
			
			//jika dalam tbt_obat_harga terdapat lebih dari satu harga untuk item yg sama => pilih item yg id nya paling besar
			$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$idObat'";
			$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
						
			$sql="SELECT a.kode AS kode, 
					 a.nama AS nama, 
					 c.hrg_netto AS derivat,
					 c.hrg_ppn AS pbf,
					 c.hrg_netto_disc AS jenis,
					 c.hrg_ppn_disc AS produsen,
					 c.id AS gol,
					 a.kel_obat AS tipe
				  FROM tbm_obat a, 
					  tbt_stok_lain b, 
					  tbt_obat_harga c, 
					  tbm_satuan_obat d

				  WHERE a.kode=b.id_obat 
						AND a.kode=c.kode 
						AND a.kode='$idObat'
						AND b.sumber=c.sumber 
						AND b.sumber='01' 
						AND b.tujuan='$tujuan'								
						AND b.jumlah >= '$jmlObat'
						AND c.id = '$id_harga' GROUP BY a.kode";
						
			//$this->errMsg->Text=$sql;
			$tmpTarif = ObatRecord::finder()->findBySql($sql);	
			//$this->test->text=$sql;			 		
			$id=$tmpTarif->kode;
			$nama=$tmpTarif->nama;				
			/*$hrgNett = $tmpTarif->derivat;
			$hrgPpn = $tmpTarif->pbf;
			$hrgNettDisc = $tmpTarif->jenis;
			$hrgPpnDisc = $tmpTarif->produsen;*/
			
			//$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
			$hrgNett = HrgObatRecord::finder()->findByPk($idMax)->hrg_netto;
			$hrgPpn = HrgObatRecord::finder()->findByPk($idMax)->hrg_ppn;
			$hrgNettDisc = HrgObatRecord::finder()->findByPk($idMax)->hrg_netto_disc;
			$hrgPpnDisc = HrgObatRecord::finder()->findByPk($idMax)->hrg_ppn_disc;
			
			//$check=HrgObatKhususRecord::finder()->findByPk($idObat)->hrg_jual;				
			$sql = "SELECT id, hrg_jual, tgl FROM tbm_obat_hrg_khusus WHERE id_obat = '$idObat' ";
			$arr = $this->queryAction($sql,'S');
			
			if (count($arr) > 0)
			{
				$hrg1 = HrgObatKhususRecord::finder()->findBySql($sql)->hrg_jual;
				//$idHarga = HrgObatKhususRecord::finder()->findBySql($sql)->id;
				$this->setViewState('hrg1',$hrg1);				
				$idHarga=$id_harga ;
				//$stKhusus='1' ;
				$this->setViewState('stKhusus','1');
				//$hrg=$tmpTarif->pbf;
				$hrg=$hrgPpn;
			}else{
				$idHarga=$id_harga ;
				//$hrg=$tmpTarif->pbf ;		
				$hrg=$hrgPpn ;		
				$tipe=$tmpTarif->tipe;
				//$stKhusus='0';
				$this->setViewState('stKhusus','0');
			}
			
			if($this->CBabaikanHarga->Checked === true)
			{	
				$hrg = 0;	
				$r_item = 0;
			}
			
		if (!$this->getViewState('nmTableObat'))
		{			
			$nmTableObat = $this->setNameTable('nmTableObat');
			$sql="CREATE TABLE $nmTableObat (id INT (2) auto_increment, 
										 nama VARCHAR(100) NOT NULL,
										 id_obat VARCHAR(5) NOT NULL,									 
										 jml INT(11) NOT NULL,
										 hrg_nett FLOAT(11,2) NOT NULL,
										 hrg_ppn FLOAT(11,2) NOT NULL,
										 hrg_nett_disc FLOAT(11,2) NOT NULL,
										 hrg_ppn_disc FLOAT(11,2) NOT NULL,
										 hrg FLOAT(11,2) NOT NULL,
										 hrg_bulat FLOAT(11,2) NOT NULL,
										 total FLOAT(11,2) NOT NULL,	
										 total_real FLOAT(11,2) NOT NULL,
										 id_kel_racik INT(11) NOT NULL,
										 r_item FLOAT(11,2) DEFAULT '0',
										 r_racik FLOAT(11,2) DEFAULT '0',
										 bungkus_racik FLOAT(11,2) DEFAULT '0',
										 id_kemasan CHAR(2) DEFAULT NULL,
										 tujuan CHAR(2) NOT NULL,								 							 
										 id_harga VARCHAR(20) NOT NULL,
										 st_obat_khusus CHAR(1) DEFAULT '0',
										 st_racik CHAR(1) DEFAULT '0',
										 st_imunisasi CHAR(1) DEFAULT '0',								 							 										 										 id_kel_imunisasi INT(11) NOT NULL,
										 st_bayar CHAR(1) DEFAULT '0',
										 expired DATE DEFAULT '0000-00-00',
										 id_bhp INTEGER(11) DEFAULT NULL,
										 nama_bhp VARCHAR(50) DEFAULT NULL,
										 bhp FLOAT DEFAULT '0',
										 st_paket CHAR(1) DEFAULT '0',
										 id_kel_paket INT(11) DEFAULT NULL,
										 jml_paket INT(11) DEFAULT '0',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...	
		}
		else//Tabel sudah eksis!!
		{
			$nmTableObat = $this->getViewState('nmTableObat');							
		}	
		
		//$this->showSql->Text=$this->getViewState('nmTable');	
		
								
		//Do some calculation for drug
		if(($kelompokPasien == '04' || $kelompokPasien == '05' || $kelompokPasien == '06') && $stAsuransi == '1') //kelompok pasien adalah karyawan atau keluarga inti atau keluarga lain yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya)
			{	
				if($kelompokPasien == '04')//karyawan yg bersangkutan lgsg
				{
					$r_item = 0;
					$r_racik = 0;
				}
				else if($kelompokPasien == '05')//Keluarga Inti suami, istri anak
				{
					$r_item = 0;
					$r_racik = 0;
				}
				else if($kelompokPasien == '06')//Diluar keluarga inti
				{
					//$r_item = 1000;
					$r_item = 0;
					$r_racik = 0;
				}	
				
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
					{
						$hrgTmp += $hrg;
					}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%							
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
							$hrgTmp += $hrgKhusus;
						}	
					}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
							$hrgTmp += $hrgKhusus;
						}
					}
				}
				else if($jnsPasien == '2')//Obat OTC - Jual Bebas 	
				{			
					if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
					{
						$hrgTmp += $hrg;
					}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
							$hrgTmp += $hrgKhusus;
						}	
					}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
							$hrgTmp += $hrgKhusus;
						}
					}
				}
				else if($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					if($kelas == '1' || $kelas == '2' || $kelas == '3') //kelas VIP atau kelas IA atau IB
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.4); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.4) - (($hrg * 1.4)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.4); //ini aturan main real-nya kurangi 5%
								$hrgTmp += ($hrg * 1.4) - (($hrg * 1.4)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}
					}
					else if($kelas == '4' || $kelas == '5') //kelas II atau kelas III
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}						
					}					
				}				
				else if($jnsPasien == '3')//One Day Service
				{
					$noTrans = $this->getViewState('notransinap');
					$hrgObatPaket = $this->getViewState('hrgObatPaket');
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$tmp'
								   AND no_trans_inap = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAktual = $row['jumlah'];
					}		
					
					if($jmlObatAktual <= $hrgObatPaket) //jika jml obat aktual belum melebihi plafon jml obat paket
					{ 
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.2); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.2) - (($hrg * 1.2)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.2); //ini aturan main real-nya kurangi 5% 
								$hrgTmp += ($hrg * 1.2) - (($hrg * 1.2)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10% jadi pengali menjadi 0.2
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5% jadi pengali menjadi 0.2
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}	
					}	
				}			
				
				if($tipeRacik == '2') //JIka Paket Imunisasi
				{					
					if($this->getViewState('st_imunisasi') == '1'){ 
						if($this->getViewState('id_kel_imunisasi') == $this->DDRacik->SelectedValue)  {
							$hrg=0;
						}
						else
						{
							$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+500; //ditambah r_item Rp.500 karena karyawan
							if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
							{
								$hrg -= $hrg;
							}else if($kelompokPasien == '05')//Pasien adalah keluarga inti Karyawan itu sendiri (Harga Jual - 10%)
							{
								$hrg -= $hrg * 0.1;
							}else if ($kelompokPasien == '06')//Pasien adalah Keluarga lain Karyawan itu sendiri (harga jual - 5%)
							{
								$hrg -= $hrg * 0.05;
							}else if ($this->getViewState('modeKryPas') == '3')
							{
								$hrg;
							}		
						}								
					}
					else
					{
						$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000						
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrg -= $hrg;
						}else if($kelompokPasien == '05')//Pasien adalah keluarga inti Karyawan itu sendiri (Harga Jual - 10%)
						{
							$hrg -= $hrg * 0.1;
						}else if ($kelompokPasien == '06')//Pasien adalah Keluarga lain Karyawan itu sendiri (harga jual - 5%)
						{
							$hrg -= $hrg * 0.05;
						}else if ($this->getViewState('modeKryPas') == '3')
						{
							$hrg;
						}
					}				
					$hrgTmp = $hrg;
					$st_imunisasi = 1;							
					$id_kel_imunisasi = $this->DDRacik->SelectedValue;
					$this->setViewState('st_imunisasi','1');
					$this->setViewState('id_kel_imunisasi',$this->DDRacik->SelectedValue);
				}
				else if($tipeRacik == '1')//Jika Racikan
				{
					$idKemasan = $this->DDKemasan->SelectedValue;
					
					$r_racik = JasaResepRacikanRecord::finder()->findByPk('1')->jasa_racikan; 
					$bungkus = KemasanRecord::finder()->findByPk($idKemasan)->hrg; 
			
					$idKelRacik = $this->DDRacik->SelectedValue;
					if($idKelRacik == '0')//kelompok baru
					{
						$sql = "SELECT MAX(id_kel_racik) AS id_kel_racik FROM $nmTableObat WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id ";
						$arr = $this->queryAction($sql,'S');
						if(count($arr) > 0) //sudah ada kel racik
						{
							foreach($arr as $row)
							{
								$idKelRacik = $row['id_kel_racik'] + 1;
							}	
							//$r_item = $r_racik;//r_obat racikan untuk karyawan 1000
							$bungkus_racik = $bungkus * $this->jmlBungkus->Text;						
						}else{
							$idKelRacik = 1;	
							//$r_item = $r_racik;//r_obat racikan untuk karyawan 1000
							$bungkus_racik = $bungkus * $this->jmlBungkus->Text;													
						}	
						
					}
					else
					{
						//Bila ada opsi delete terhadap obat racikan yg hrgnya sdh termasuk 
						//kombinasi /R dan bungkus_racik
						if($this->getViewState('resepHrg') > 0)
						{
							$r_item = $resepHrg;
						}
						else
						{
							$r_item;
						}		
						
						if($this->getViewState('racikHrg') > 0)
						{
							$bungkus_racik = $racikHrg;
						}
						else
						{
							$bungkus_racik = 0;
						}						
						
					}
									
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;					
					$this->clearViewState('resepHrg');
					$this->clearViewState('racikHrg');
					$this->setViewState('st_racikan','1');
					$this->setViewState('id_kel_racikan',$this->DDRacik->SelectedValue);
				}
				else
				{
					$idKelRacik = 0;
					/* Perhitungan /R aktifkan modul ini ini bila OTC tanpa /R*/

					if(($this->getViewState('jnsPasien') == '2') && ($this->getViewState('embel')== '02'))//Obat pasienluar tanpa resep dokter
					{
						$r_item;//r_obat bukan racikan untuk karyawan Rp 0,-
					}else{
						$r_item;//r_obat bukan racikan untuk karyawan Rp 500,-	
					}				
					//*/	
					//$r_item = 500;//Disable nilai /R ini bila modul OTC diatas diaktifkan
					$st_racik = 0;
					$bungkus_racik = 0;					
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;				
				}				
					
			//--BILA PASIEN ADALAH BUKAN KARYAWAN--//	
			
			}
			else
			{// Bila kelompok pasien adalah bukan karyawan!!
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($this->getViewState('stKhusus') == '0')
					{	
						//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH ADA PERSENTASE OBAT YANG DIKENAKAN DALAM TRANSAKSI APOTIK YANG DICOVER ASURANSI
						//JIKA YA => TAMBAHAKAN PERSENTASE TSB KE DALAM PERHITUNGAN OBAT
						if(($kelompokPasien == '02' || $kelompokPasien == '07' )  && $stAsuransi == '1') //kelompok pasien asuransi/jamper yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
						{
							$idKlinik = $this->DDKlinik->SelectedValue;							
							$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->perus_asuransi;
							
							$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider='$idPerusAsuransi' AND id_poli='$idKlinik' ";
							$persenProvider = ProviderDetailObatRecord::finder()->findBySql($sql)->margin / 100;
						}
						
						$hrgTmp += $hrg + ($hrg * $persenMargin) + ($hrg * $persenProvider);
						//$this->showSql->Text = $hrgTmp;
					}
					else if($this->getViewState('stKhusus')=='1')
					{
						//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH ADA PERSENTASE OBAT YANG DIKENAKAN DALAM TRANSAKSI APOTIK YANG DICOVER ASURANSI
						//JIKA YA => TAMBAHAKAN PERSENTASE TSB KE DALAM PERHITUNGAN OBAT
						if(($kelompokPasien == '02' || $kelompokPasien == '07' )  && $stAsuransi == '1') //kelompok pasien asuransi/jamper yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
						{
							$idKlinik = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->id_klinik;	
							$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->perus_asuransi;
							
							$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider='$idPerusAsuransi' AND id_poli='$idKlinik' ";
							$persenProvider = ProviderDetailObatRecord::finder()->findBySql($sql)->margin / 100;
						}
						
						$hrgTmp += $this->getViewState('hrg1') + ($this->getViewState('hrg1') * $persenProvider);
					}
				}
				else if($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					/*
					if($kelas == '2') //kelas VIP 
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg*0.35);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '3') //kelas IA
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.15);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '1') //kelas IB
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.1);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '4') //kelas II
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.05);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '5') //kelas III
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0);
						}else  if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}						
					}
					*/
					if ($this->getViewState('stKhusus') == '0')
					{
						$hrgTmp += $hrg + ($hrg * $persenMargin);
					}elseif($this->getViewState('stKhusus')=='1')
					{
						$hrgTmp += $this->getViewState('hrg1');
					}	
				}
				else if($jnsPasien == '2')//Pasien Luar/ODC
				{
					if ($this->getViewState('stKhusus') == '0')
					{
						$hrgTmp += $hrg+($hrg * $persenMargin);
					}else if($this->getViewState('stKhusus') == '1'){
						$hrgTmp = $this->getViewState('hrg1');
					}
				}
				else if($jnsPasien == '3')//One Day Service
				{
					$noTrans = $this->getViewState('notransinap');
					$hrgObatPaket = $this->getViewState('hrgObatPaket');
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$tmp'
								   AND no_trans_inap = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAktual = $row['jumlah'];
					}		
					
					if($jmlObatAktual <= $hrgObatPaket) //jika jml obat aktual belum melebihi plafon jml obat paket
					{ 
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.2);
						}else if($this->getViewState('stKhusus') == '1'){
							$hrgTmp = $this->getViewState('hrg1');
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.3);
						}else if ($this->getViewState('stKhusus') == '1'){
							$hrgTmp = $this->getViewState('hrg1');
						}	
					}	
				}
				elseif($jnsPasien == '4')//Unit Internal
				{
					if ($this->getViewState('stKhusus') == '0')
					{
						//$hrgTmp += $hrg + ($hrg * 0.3);
						$hrgTmp += $hrg + ($hrg * $persenMargin);
					}else if($this->getViewState('stKhusus')=='1'){
						$hrgTmp += $this->getViewState('hrg1');
					}
				}
				
				if($tipeRacik == '2') //JIka Paket Imunisasi
				{					
					if($this->getViewState('st_imunisasi') == '1'){ 
						if($this->getViewState('id_kel_imunisasi') == $this->DDRacik->SelectedValue)  {
							$hrg=0;
						}
						else
						{
							$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000
						}								
					}
					else
					{
						$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000						
					}				
					$hrgTmp = $hrg;
					$st_imunisasi = 1;							
					$id_kel_imunisasi = $this->DDRacik->SelectedValue;
					$this->setViewState('st_imunisasi','1');
					$this->setViewState('id_kel_imunisasi',$this->DDRacik->SelectedValue);
				}
				else if($tipeRacik == '1')//Jika Racikan
				{
					$idKemasan = $this->DDKemasan->SelectedValue;
					
					$r_racik = JasaResepRacikanRecord::finder()->findByPk('1')->jasa_racikan; 
					$bungkus = KemasanRecord::finder()->findByPk($idKemasan)->hrg; 
			
					$idKelRacik = $this->DDRacik->SelectedValue;
					if($idKelRacik == '0')//kelompok baru
					{
						$sql = "SELECT MAX(id_kel_racik) AS id_kel_racik FROM $nmTableObat WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id ";
						$arr = $this->queryAction($sql,'S');
						if(count($arr) > 0) //sudah ada kel racik
						{
							foreach($arr as $row)
							{
								$idKelRacik = $row['id_kel_racik'] + 1;
							}
						}
						else
						{
							$idKelRacik = 1;	
							//$r_item = 3000;
							//$r_item = 0;
						}	
						
						$bungkus_racik = $bungkus * $this->jmlBungkus->Text;						
						
					}
					else
					{
						//Bila ada opsi delete terhadap obat racikan yg hrgnya sdh termasuk 
						//kombinasi /R dan bungkus_racik
						if($resepHrg > 0)
						{
							$r_racik = $racikHrg;
						}	
						
						if($bungkusRacikHrg > 0)
						{
							$bungkus_racik = $bungkusRacikHrg;
						}
						else
						{
							$bungkus_racik = 0;
						}
						
						//jika r_racik sudah dimasukan sebelumnya dalam satu kelompok racikan => nilai r_racik tidak dimasukan lagi	
						$sql = "SELECT SUM(r_racik) AS r_racik FROM $nmTableObat WHERE id_kel_racik = '$idKelRacik' GROUP BY id_kel_racik ";
						$arr = $this->queryAction($sql,'S');
						
						foreach($arr as $row)
						{
							$jmlR_Racik = $row['r_racik'];
						}
						
						if($jmlR_Racik > 0)
							$r_racik = 0;
						
					}
									
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;	
					$this->clearViewState('resepHrg');
					$this->clearViewState('racikHrg');				
					$this->setViewState('st_racikan','1');
					$this->setViewState('id_kel_racikan',$this->DDRacik->SelectedValue);
				}
				else
				{
					$idKelRacik = 0;
					
					/* Perhitungan /R aktifkan modul ini ini bila OTC tanpa /R
					if(($this->getViewState('jnsPasien') == '2') && ($this->getViewState('embel')== '02'))//Obat pasienluar tanpa resep dokter
						$r_item = 0;
					else
						$r_item = 0;
					*/
						
					//*/	
					//$r_item = 1500;//Disable nilai /R ini bila modul OTC diatas diaktifkan
					$st_racik = 0;
					$bungkus_racik = 0;					
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;				
				}				
					
			}// End of tipe pasien bukan Karyawan	
		
		//jika r_item sudah dimasukan sebelumnya => nilai r_item tidak dimasukan lagi	
		$sql = "SELECT SUM(r_item) AS r_item FROM $nmTableObat ";
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$jmlR = $row['r_item'];
		}
		
		if($jmlR > 0)
			$r_item = 0;
						
		$sqlCekObat = "SELECT * FROM $nmTableObat WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
		$arrCekObat = $this->queryAction($sqlCekObat,'S');
		
		$jmlAwal = 0;
		$totSementaraAwal = 0;
		$rAwal = 0;
		$bungkusAwal = 0;
		if($arrCekObat)
		{
			foreach($arrCekObat as $rowCekObat)
			{
				$jmlAwal += $rowCekObat['jml'];
				$r_item += $rowCekObat['r_item'];
				$r_racik += $rowCekObat['r_racik'];
				$bungkus_racik += $rowCekObat['bungkus_racik'];
				
				//if($this->getViewState('stAsuransi') == '1')
					//$totSementaraAwal = $rowCekObat['total_real'];
				//else
					$totSementaraAwal = $this->bulatkan($rowCekObat['total_real']);
			}
			
			$jml = $jml + $jmlAwal;
			
			//if($this->getViewState('stAsuransi') == '1')
				//$hrg_bulat = $hrgTmp;
			//else
				$hrg_bulat = $this->bulatkan($hrgTmp);
					
			//$hrg_bulat = ($hrgTmp);
			$hrgJual_real = $hrgTmp * $jml;
			
			//if($this->getViewState('stAsuransi') == '1')
				//$hrgJual_bulat = $hrg_bulat * $jml;
			//else
				$hrgJual_bulat = $this->bulatkan($hrg_bulat * $jml);
				
					
			//$hrgJual_bulat = ($hrg_bulat * $jml);
			$total = $hrgJual_real;//$hrgJual_bulat;
			
			//if($this->getViewState('stAsuransi') == '1')
			//	$total = $total + $r_item + $r_racik + $bungkus_racik;
			//else
				$total = $this->bulatkan($total + $r_item + $r_racik + $bungkus_racik);
				
			$hrgJual_real = $hrgJual_real + $r_item + $r_racik + $bungkus_racik;						
			if($this->getViewState('totSementara'))  
			{
				$totSementara = $this->getViewState('totSementara') - $totSementaraAwal;
			}else{
				$totSementara=0;
			}			
			
			$totSementara += $total;
		}
		else
		{				
			//if($this->getViewState('stAsuransi') == '1')
			//	$hrg_bulat = $hrgTmp;
			//else
				$hrg_bulat = $this->bulatkan($hrgTmp);
					
			//$hrg_bulat = ($hrgTmp);
			$hrgJual_real = $hrgTmp * $jml;
			
			//if($this->getViewState('stAsuransi') == '1')
			//	$hrgJual_bulat = $hrg_bulat * $jml;
			//else
				$hrgJual_bulat = $this->bulatkan($hrg_bulat * $jml);
				
			//$hrgJual_bulat = ($hrg_bulat * $jml);
			$total = $hrgJual_real;//$hrgJual_bulat;
			
			//if($this->getViewState('stAsuransi') == '1')
			//	$total = $total + $r_item + $r_racik + $bungkus_racik;
			//else
				$total = $this->bulatkan($total + $r_item + $r_racik + $bungkus_racik);
				
			$hrgJual_real = $hrgJual_real + $r_item + $r_racik + $bungkus_racik;						
			if($this->getViewState('totSementara'))
			{
				$totSementara=$this->getViewState('totSementara');
			}else{
				$totSementara=0;
			}			
			
			$totSementara += $total;
		}
		
		//-----------jika ada BHP --------------
		/*if($this->DDBhp->SelectedValue != '')
		{
			$idBhp = $this->DDBhp->SelectedValue;
			$namaBhp = $this->ambilTxt($this->DDBhp);
			$hargaBhp = ObatBhpRecord::finder()->findByPk($idBhp)->bhp;
		}
		else
		{
			$idBhp = '';
			$namaBhp = '';
			$hargaBhp = '0';
		}*/
		
		//$totSementara += $hargaBhp;
		
		if($jnsPasien == '0')//Rawat Jalan 
		{
			//CEK APAKAH ADA PLAFON OBAT UNTUK PASIEN ASURANSI/JAMPER
			if($this->getViewState('plafonObat'))
			{
				//PENAMBAHAN OBAT TIDAK BISA DILAKUKAN JIKA TOTAL PENJUALAN MELEBIHI PLAFON
				if($totSementara > $this->getViewState('plafonObat'))
				{
					$sql = "SELECT * FROM $nmTableObat ";
					$arr = $this->queryAction($sql,'S');
					
					if(count($arr) > 0)
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Penambahan transaksi obat tidak bisa dilakukan karena total transaksi telah melebihi batas plafon obat yang telah ditentukan untuk Pasien Asuransi/Perusahaan !<br/><br/>Plafon Obat : '.number_format($this->getViewState('plafonObat'),'2',',','.').'<br/>Total Transaksi Apotik : '.number_format($totSementara,'2',',','.').'</p>\',timeout: 1000000,dialog:{
							modal: true,
							buttons: {
								"Batalkan Transaksi": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasi(\'ya\');
								},
								"Batalkan Penambahan": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasi(\'tidak\');
								},
							}
						}});');	
					}
					else
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Penambahan transaksi obat tidak bisa dilakukan karena total transaksi telah melebihi batas plafon obat yang telah ditentukan untuk Pasien Asuransi/Perusahaan !<br/><br/>Plafon Obat : '.$this->getViewState('plafonObat').'<br/>Total Transaksi Apotik : '.$totSementara.'</p>\',timeout: 1000000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasi(\'ya\');
								},
							}
						}});');	
						
						$this->queryAction($nmTableObat,'D');//Droped the table		
						$this->clearViewState('nmTableObat');//Clear the view state		
					}
						
					$this->AutoComplete->Text = '';
					$this->kodeObat->Text = '';
					$this->jml->Text = '';
					$this->jmlStok->Text = '';
					$this->DDObat->SelectedValue = 'empty';
					//$this->DDBhp->SelectedValue = 'empty';
					$this->RBtipeRacik->SelectedValue = '0';
					$this->RBtipeRacik->Enabled = false;
					$this->DDRacik->Enabled = false;
					$this->DDKemasan->Enabled = false;
					$this->DDRacik->SelectedValue = 'empty';
					$this->DDKemasan->SelectedValue = 'empty';
					
					$this->Page->CallbackClient->focus($this->DDObat);
					
					if($this->getViewState('jnsPasLuar'))
					{
						$this->dokter->Text = $this->getViewState('nmDokter') ;
						$this->nama->Text =$this->getViewState('nmPasien');
					}
				}
				else
				{
					$this->setViewState('totSementara',$totSementara);
					//$this->totSementara->Text = $this->getViewState('totSementara');
					$stBayar=$this->getViewState('modeBayar');	
					//$this->setViewState('total',$total);
					//$this->errMsg->Text=$id.'-'.$nama.'-'.$jml.'-'.$hrg.'-'.$hrg_bulat.'-'.$total.'-'.$hrgJual_real.'-'.$tujuan.'-'.$idHarga.'-'.$stKhusus.'-'.$tipeRacik.'-'.$idKelRacik.'-'.$r_item.'-'.$bungkus_racik.'-'.$st_imunisasi.'-'.$id_kel_imunisasi;
					
					$sqlCekObat = "SELECT * FROM $nmTableObat WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
					$arrCekObat = $this->queryAction($sqlCekObat,'S');
					
					if($arrCekObat)
					{
						$sql = "UPDATE $nmTableObat SET
								jml = '$jml',
								hrg = '$hrgTmp',
								hrg_bulat = '$hrg_bulat',
								total = '$total',
								total_real = '$hrgJual_real'
								WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
					}
					else
					{
						$sql="INSERT INTO $nmTableObat
								(id_obat,
								nama,
								jml,
								hrg_nett,
								hrg_ppn,
								hrg_nett_disc,
								hrg_ppn_disc,
								hrg,
								hrg_bulat,
								total,
								total_real,
								tujuan,
								id_harga,
								expired,
								st_obat_khusus,
								st_racik,
								id_kel_racik,
								r_item,
								r_racik,
								bungkus_racik,
								id_kemasan,
								st_imunisasi,
								id_kel_imunisasi,
								st_bayar,
								id_bhp,
								nama_bhp,
								bhp,
								st_paket,
								id_kel_paket,
								jml_paket) 
							VALUES 
								('$id',
								'$nama',
								'$jml',
								'$hrgNett',
								'$hrgPpn',
								'$hrgNettDisc',
								'$hrgPpnDisc',
								'$hrgTmp',
								'$hrg_bulat',
								'$total',
								'$hrgJual_real',
								'$tujuan',
								'$idHarga',
								'$expired',
								'$stKhusus',
								'$tipeRacik',
								'$idKelRacik',
								'$r_item',
								'$r_racik',
								'$bungkus_racik',
								'$idKemasan',
								'$st_imunisasi',
								'$id_kel_imunisasi',
								'$stBayar',
								'$idBhp',
								'$namaBhp',
								'$hargaBhp',
								'$stPaket',
								'$idKelPaket',
								'$jmlPaket')";
					}
								
					//$this->errMsg->Text=$sql;
					$this->queryAction($sql,'C');//Insert new row in tabel bro...			
					
					/*	
					$sql="SELECT 
							id,
							nama,
							hrg_bulat,
							SUM(jml) AS jml,
							SUM(total) AS total,
							st_racik,
							id_kel_racik,
							st_imunisasi,
							id_kel_imunisasi 
						FROM 
							$nmTable 
						GROUP BY 
							id_obat, st_racik, id_kel_racik, '$st_imunisasi', '$id_kel_imunisasi'
						ORDER BY 
							id_kel_racik,id_kel_imunisasi, id";
					*/
					
					$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik,id_kel_imunisasi, id";
						
					$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
					$this->UserGridObat->dataBind();
					
					
					/*$sql="SELECT id,nama_bhp,bhp FROM $nmTableObat WHERE id_bhp <> '' ORDER BY id";
					$arr = $this->queryAction($sql,'S');
					
					//jika ada BHP
					if (count($arr) > 0)
					{	
						$this->BhpGrid->DataSource = $arr ;//Insert new row in tabel bro...
						$this->BhpGrid->dataBind();
					}*/
					
					//$this->cetakBtn->Enabled = true; if($this->modeInput->SelectedValue != '1'){$this->tundaBtn->Enabled = true;}
					
					$this->jmlBungkus->Text = '';
					$this->jmlBungkus->Enabled = false;		
					
					if($this->getViewState('total')){
						$t = (int)$this->getViewState('total') + $total;
						$this->setViewState('total',$t);
					}else{
						$this->setViewState('total',$total);
					}	
					
					if($this->getViewState('hrg1'))
					{
						$this->clearViewState('hrg1');
					}
								
					//$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
					
					//$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
					//if($this->getViewState('modeByrInap')){
						/*if($modeByrInap == 1)
						{
							$this->showBayar->Display = 'Hidden';					
						}
						else
						{
							$this->showBayar->Enabled=true;
							$this->cetakBtn->Enabled = false; $this->tundaBtn->Enabled = false;	
						}*/
					//}
					
					
					
					$this->AutoComplete->Text = '';
					$this->kodeObat->Text = '';
					$this->jml->Text = '';
					$this->jmlStok->Text = '';
					$this->DDObat->SelectedValue = 'empty';
					//$this->DDBhp->SelectedValue = 'empty';
					$this->RBtipeRacik->SelectedValue = '0';
					$this->RBtipeRacik->Enabled = false;
					$this->DDRacik->Enabled = false;
					$this->DDKemasan->Enabled = false;
					$this->DDRacik->SelectedValue = 'empty';
					$this->DDKemasan->SelectedValue = 'empty';
					
					$this->Page->CallbackClient->focus($this->DDObat);
					
					if($this->getViewState('jnsPasLuar'))
					{
						$this->dokter->Text = $this->getViewState('nmDokter') ;
						$this->nama->Text =$this->getViewState('nmPasien');
					}
				}
			}
			else
			{
				$this->setViewState('totSementara',$totSementara);
				//$this->totSementara->Text = $this->getViewState('totSementara');
				$stBayar=$this->getViewState('modeBayar');	
				//$this->setViewState('total',$total);
				//$this->errMsg->Text=$id.'-'.$nama.'-'.$jml.'-'.$hrg.'-'.$hrg_bulat.'-'.$total.'-'.$hrgJual_real.'-'.$tujuan.'-'.$idHarga.'-'.$stKhusus.'-'.$tipeRacik.'-'.$idKelRacik.'-'.$r_item.'-'.$bungkus_racik.'-'.$st_imunisasi.'-'.$id_kel_imunisasi;
				
				$sqlCekObat = "SELECT * FROM $nmTableObat WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
				$arrCekObat = $this->queryAction($sqlCekObat,'S');
				
				if($arrCekObat)
				{
					$sql = "UPDATE $nmTableObat SET
							jml = '$jml',
							hrg = '$hrgTmp',
							hrg_bulat = '$hrg_bulat',
							total = '$total',
							total_real = '$hrgJual_real'
							WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
				}
				else
				{
					$sql="INSERT INTO $nmTableObat
							(id_obat,
							nama,
							jml,
							hrg_nett,
							hrg_ppn,
							hrg_nett_disc,
							hrg_ppn_disc,
							hrg,
							hrg_bulat,
							total,
							total_real,
							tujuan,
							id_harga,
							expired,
							st_obat_khusus,
							st_racik,
							id_kel_racik,
							r_item,
							r_racik,
							bungkus_racik,
							id_kemasan,
							st_imunisasi,
							id_kel_imunisasi,
							st_bayar,
							id_bhp,
							nama_bhp,
							bhp,
							st_paket,
							id_kel_paket,
							jml_paket) 
						VALUES 
							('$id',
							'$nama',
							'$jml',
							'$hrgNett',
							'$hrgPpn',
							'$hrgNettDisc',
							'$hrgPpnDisc',
							'$hrgTmp',
							'$hrg_bulat',
							'$total',
							'$hrgJual_real',
							'$tujuan',
							'$idHarga',
							'$expired',
							'$stKhusus',
							'$tipeRacik',
							'$idKelRacik',
							'$r_item',
							'$r_racik',
							'$bungkus_racik',
							'$idKemasan',
							'$st_imunisasi',
							'$id_kel_imunisasi',
							'$stBayar',
							'$idBhp',
							'$namaBhp',
							'$hargaBhp',
							'$stPaket',
							'$idKelPaket',
							'$jmlPaket')";
				}
							
				//$this->errMsg->Text=$sql;
				$this->queryAction($sql,'C');//Insert new row in tabel bro...			
				
				/*	
				$sql="SELECT 
						id,
						nama,
						hrg_bulat,
						SUM(jml) AS jml,
						SUM(total) AS total,
						st_racik,
						id_kel_racik,
						st_imunisasi,
						id_kel_imunisasi 
					FROM 
						$nmTable 
					GROUP BY 
						id_obat, st_racik, id_kel_racik, '$st_imunisasi', '$id_kel_imunisasi'
					ORDER BY 
						id_kel_racik,id_kel_imunisasi, id";
				*/
				
				$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik,id_kel_imunisasi, id";
					
				$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGridObat->dataBind();
				
				
				/*$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				//jika ada BHP
				if (count($arr) > 0)
				{	
					$this->BhpGrid->DataSource = $arr ;//Insert new row in tabel bro...
					$this->BhpGrid->dataBind();
				}*/
				
				//$this->cetakBtn->Enabled = true; if($this->modeInput->SelectedValue != '1'){$this->tundaBtn->Enabled = true;}
				
				$this->jmlBungkus->Text = '';
				$this->jmlBungkus->Enabled = false;		
				
				if($this->getViewState('total')){
					$t = (int)$this->getViewState('total') + $total;
					$this->setViewState('total',$t);
				}else{
					$this->setViewState('total',$total);
				}	
				
				if($this->getViewState('hrg1'))
				{
					$this->clearViewState('hrg1');
				}
							
				//$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
				
				/*$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
				//if($this->getViewState('modeByrInap')){
					if($modeByrInap == 1)
					{
						$this->showBayar->Display = 'Hidden';					
					}
					else
					{
						$this->showBayar->Enabled=true;
						$this->cetakBtn->Enabled = false; $this->tundaBtn->Enabled = false;	
					}*/
				//}
				
				
				
				$this->AutoComplete->Text = '';
				$this->kodeObat->Text = '';
				$this->jml->Text = '';
				$this->jmlStok->Text = '';
				$this->DDObat->SelectedValue = 'empty';
				//$this->DDBhp->SelectedValue = 'empty';
				$this->RBtipeRacik->SelectedValue = '0';
				$this->RBtipeRacik->Enabled = false;
				$this->DDRacik->Enabled = false;
				$this->DDKemasan->Enabled = false;
				$this->DDRacik->SelectedValue = 'empty';
				$this->DDKemasan->SelectedValue = 'empty';
				
				$this->Page->CallbackClient->focus($this->DDObat);
				
				if($this->getViewState('jnsPasLuar'))
				{
					$this->dokter->Text = $this->getViewState('nmDokter') ;
					$this->nama->Text =$this->getViewState('nmPasien');
				}
			}
		}
		else
		{
			$this->setViewState('totSementara',$totSementara);
			//$this->totSementara->Text = $this->getViewState('totSementara');
			$stBayar=$this->getViewState('modeBayar');	
			//$this->setViewState('total',$total);
			//$this->errMsg->Text=$id.'-'.$nama.'-'.$jml.'-'.$hrg.'-'.$hrg_bulat.'-'.$total.'-'.$hrgJual_real.'-'.$tujuan.'-'.$idHarga.'-'.$stKhusus.'-'.$tipeRacik.'-'.$idKelRacik.'-'.$r_item.'-'.$bungkus_racik.'-'.$st_imunisasi.'-'.$id_kel_imunisasi;
			
			$sqlCekObat = "SELECT * FROM $nmTableObat WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
			$arrCekObat = $this->queryAction($sqlCekObat,'S');
			
			if($arrCekObat)
			{
				$sql = "UPDATE $nmTableObat SET
						jml = '$jml',
						hrg = '$hrgTmp',
						hrg_bulat = '$hrg_bulat',
						total = '$total',
						total_real = '$hrgJual_real'
						WHERE id_obat = '$id' AND tujuan = '$tujuan' AND id_harga = '$idHarga' AND st_obat_khusus = '$stKhusus' AND st_racik = '$tipeRacik' AND id_kel_racik = '$idKelRacik' AND st_imunisasi = '$st_imunisasi' AND id_kel_imunisasi = '$id_kel_imunisasi' AND expired = '$expired' AND st_paket = '$stPaket' AND id_kel_paket = '$idKelPaket'  ";
			}
			else
			{
				$sql="INSERT INTO $nmTableObat
						(id_obat,
						nama,
						jml,
						hrg_nett,
						hrg_ppn,
						hrg_nett_disc,
						hrg_ppn_disc,
						hrg,
						hrg_bulat,
						total,
						total_real,
						tujuan,
						id_harga,
						expired,
						st_obat_khusus,
						st_racik,
						id_kel_racik,
						r_item,
						r_racik,
						bungkus_racik,
						id_kemasan,
						st_imunisasi,
						id_kel_imunisasi,
						st_bayar,
						id_bhp,
						nama_bhp,
						bhp,
						st_paket,
						id_kel_paket,
						jml_paket) 
					VALUES 
						('$id',
						'$nama',
						'$jml',
						'$hrgNett',
						'$hrgPpn',
						'$hrgNettDisc',
						'$hrgPpnDisc',
						'$hrgTmp',
						'$hrg_bulat',
						'$total',
						'$hrgJual_real',
						'$tujuan',
						'$idHarga',
						'$expired',
						'$stKhusus',
						'$tipeRacik',
						'$idKelRacik',
						'$r_item',
						'$r_racik',
						'$bungkus_racik',
						'$idKemasan',
						'$st_imunisasi',
						'$id_kel_imunisasi',
						'$stBayar',
						'$idBhp',
						'$namaBhp',
						'$hargaBhp',
						'$stPaket',
						'$idKelPaket',
						'$jmlPaket')";
			}
						
			//$this->errMsg->Text=$sql;
			$this->queryAction($sql,'C');//Insert new row in tabel bro...			
			
			/*	
			$sql="SELECT 
					id,
					nama,
					hrg_bulat,
					SUM(jml) AS jml,
					SUM(total) AS total,
					st_racik,
					id_kel_racik,
					st_imunisasi,
					id_kel_imunisasi 
				FROM 
					$nmTable 
				GROUP BY 
					id_obat, st_racik, id_kel_racik, '$st_imunisasi', '$id_kel_imunisasi'
				ORDER BY 
					id_kel_racik,id_kel_imunisasi, id";
			*/
			
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik,id_kel_imunisasi, id";
				
			$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGridObat->dataBind();
			
			
			/*$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";
			$arr = $this->queryAction($sql,'S');
			
			//jika ada BHP
			if (count($arr) > 0)
			{	
				$this->BhpGrid->DataSource = $arr ;//Insert new row in tabel bro...
				$this->BhpGrid->dataBind();
			}
			
			$this->cetakBtn->Enabled = true; if($this->modeInput->SelectedValue != '1'){$this->tundaBtn->Enabled = true;}*/
			
			$this->jmlBungkus->Text = '';
			$this->jmlBungkus->Enabled = false;		
			
			if($this->getViewState('total')){
				$t = (int)$this->getViewState('total') + $total;
				$this->setViewState('total',$t);
			}else{
				$this->setViewState('total',$total);
			}	
			
			if($this->getViewState('hrg1'))
			{
				$this->clearViewState('hrg1');
			}
						
			//$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
			
			/*$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
			//if($this->getViewState('modeByrInap')){
				if($modeByrInap == 1)
				{
					$this->showBayar->Display = 'Hidden';					
				}
				else
				{
					$this->showBayar->Enabled=true;
					$this->cetakBtn->Enabled = false; $this->tundaBtn->Enabled = false;	
				}
			//}*/
			
			$this->AutoComplete->Text = '';
			$this->kodeObat->Text = '';
			$this->jml->Text = '';
			$this->jmlStok->Text = '';
			$this->DDObat->SelectedValue = 'empty';
			//$this->DDBhp->SelectedValue = 'empty';
			$this->RBtipeRacik->SelectedValue = '0';
			$this->RBtipeRacik->Enabled = false;
			$this->DDRacik->Enabled = false;
			$this->DDKemasan->Enabled = false;
			$this->DDRacik->SelectedValue = 'empty';
			$this->DDKemasan->SelectedValue = 'empty';
			
			$this->Page->CallbackClient->focus($this->DDObat);
			
			if($this->getViewState('jnsPasLuar'))
			{
				$this->dokter->Text = $this->getViewState('nmDokter') ;
				$this->nama->Text =$this->getViewState('nmPasien');
			}
			
			$this->clearViewState('jmlPaket');
		}
	}
	
	public function itemCreatedObat($sender,$param)
  {
    $item=$param->Item;
		
		if($item->ItemType==='EditItem')
		{
			 $item->jmlColumn->jmlEdit->Columns = 5;
			 $item->jmlColumn->jmlEdit->Text = $item->DataItem['jml'];
		}       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{		
			$item->jmlColumn->Text = $item->DataItem['jml'];
		 	
			/*if($this->getViewState('kelDiagnosa') != '0')//FFS
			{
				$this->UserGridObat->Columns[4]->Visible = true; 
				
				if($item->DataItem['st_ffs'] == '1')//FFS
					$item->fssColumn->st_ffs->Checked = true;
				else
					$item->fssColumn->st_ffs->Checked = false;
			}
			else
			{
				$this->UserGridObat->Columns[4]->Visible = false; 
				$item->fssColumn->st_ffs->Checked = false;
			}*/
		}
  }
	
	public function pagerCreatedObat($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function editItemObat($sender,$param)
	{
		$item=$param->Item;
		$this->UserGridObat->EditItemIndex = $param->Item->ItemIndex;
	}
	
	public function cancelItemObat($sender,$param)
	{        
		$item=$param->Item;
		$this->UserGridObat->EditItemIndex=-1;  
	}
	
	public function saveItemObat($sender,$param)
  {
		if($this->Page->IsValid) 
		{ 
			$item=$param->Item;
			$ID = $this->UserGridObat->DataKeys[$item->ItemIndex];	
			
			if(trim($item->jmlColumn->jmlEdit->Text) != '' && intval($item->jmlColumn->jmlEdit->Text))
			{
				$nmTableObat = $this->getViewState('nmTableObat');
				$sql="SELECT id_obat FROM $nmTableObat WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'S');		
				foreach($arrData as $row)
				{
					$idObat = $row['id_obat'];
				}
				
				$jumlah = intval($item->jmlColumn->jmlEdit->Text);
				$this->setViewState('editStok','1');		
				$this->setViewState('idObatEdit',$idObat);
				$this->deleteClickedObat($sender,$param);
				$this->cekStokEdit($jumlah,$idObat);
				
				$this->cetakBtn->Enabled = true; 
				$this->tundaBtn->Enabled = true;
				
				$this->UserGridObat->EditItemIndex=-1; 
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jumlah belum di isi !<br/><br/></p>\',timeout: 3000,dialog:{
					modal: true
				}});');	
			}
		}
  }
	public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$this->clearViewState('st_imunisasi');
				$this->clearViewState('id_kel_imunisasi');
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT id_kel_racik,total, st_imunisasi, st_racik, r_item, r_racik, bungkus_racik FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['total'];					
					$t = ($this->getViewState('totSementara') - $n);						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);					
					$stRacik = $row['st_racik'];
					$stImunisasi = $row['st_imunisasi'];
					$resepHrg=$row['r_item'];
					$racikHrg=$row['r_racik'];
					$bungkusRacikHrg=$row['bungkus_racik'];
					
					/*
					if($stRacik == '1' && $resepHrg > 0 && $racikHrg > 0){						
						$this->setViewState('resepHrg',$resepHrg);
						$this->setViewState('racikHrg',$racikHrg);
					}	
					*/
					
					if($stRacik == '0' && $stImunisasi == '0' && $resepHrg > 0)//Jika yg didelete itu adalah bukan kelompok obat racikan dan mengandung nilai r_item
					{					
						$sql ="SELECT * FROM $nmTable WHERE st_racik='0' AND st_imunisasi = '0'";
						$arr = $this->queryAction($sql,'S');				
						
						$i = 1;
						if(count($arr) > 1) 
						{
							foreach($arr as $row)
							{
								if($i == '1')
								{
									//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
									$sql2 = "SELECT MAX(id) AS id FROM $nmTable WHERE st_racik='0' AND st_imunisasi = '0' AND id<>'$ID' GROUP BY id";
									$arr2 = $this->queryAction($sql2,'S');						
									foreach($arr2 as $row2)
									{
										$idObat = $row2['id'];
									}
									
									$sql2 = "SELECT total, total_real FROM $nmTable WHERE id='$idObat' ";
									$arr2 = $this->queryAction($sql2,'S');						
									foreach($arr2 as $row2)
									{
										$totAsal = $row2['total'];
										$tot = $totAsal + $resepHrg;
										$totReal = $row2['total_real'] + $resepHrg;
										
										$sqlUpdate = "UPDATE $nmTable 
														SET r_item='$resepHrg',
															total='$tot',
															total_real='$totReal'
														WHERE id = '$idObat' ";
										$this->queryAction($sqlUpdate,'C');
									}
								}
								
								$i++;
							}
							
							//update total transaksi sementara
							$t = ($this->getViewState('totSementara') + $resepHrg);						
							$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
							$this->setViewState('totSementara',$t);	
						}
					}
					elseif($stRacik == '1' && $racikHrg > 0)//Jika yg didelete itu adalah kelompok obat racikan dan mengandung nilai r_racik
					{						
						$idKelRacik = $row['id_kel_racik'];
						
						$sqlRacik="SELECT id_kel_racik FROM $nmTable WHERE id_kel_racik='$idKelRacik'";
						$arrRacik = $this->queryAction($sqlRacik,'S');				
						
						$i = 1;
						if(count($arrRacik) > 1) //Jika dalam kelompok racikan tsb terdapat lebih dari 1 obat racikan
						{
							foreach($arrRacik as $rowRacik)
							{
								if($i == '1')
								{
									//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
									$sqlRacik2 = "SELECT MAX(id) AS id FROM $nmTable WHERE id_kel_racik='$idKelRacik' AND id<>'$ID' GROUP BY id";
									$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
									foreach($arrRacik2 as $rowRacik2)
									{
										$idObatRacik = $rowRacik2['id'];
									}
									
									$sqlRacik2 = "SELECT total, total_real FROM $nmTable WHERE id='$idObatRacik' ";
									$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
									foreach($arrRacik2 as $rowRacik2)
									{
										$totRacikAsal = $rowRacik2['total'];
										$totRacik = $totRacikAsal + $resepHrg + $racikHrg + $bungkusRacikHrg;
										$totReal = $rowRacik2['total_real'] + $resepHrg + $racikHrg + $bungkusRacikHrg;
										
										$sqlUpdateRacik = "UPDATE $nmTable 
														SET r_item='$resepHrg',
															r_racik='$racikHrg',
															bungkus_racik='$bungkusRacikHrg',
															total='$totRacik',
															total_real='$totReal'
														WHERE id = '$idObatRacik' ";
										$this->queryAction($sqlUpdateRacik,'C');
									}
								}
								
								$i++;
							}
							
							//update total transaksi sementara
							$t = ($this->getViewState('totSementara') + $resepHrg + $racikHrg + $bungkusRacikHrg);						
							$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
							$this->setViewState('totSementara',$t);	
						}
						else
						{
							//jika obat racikan yg di delete mengandung nilai r_item
							if($resepHrg > 0)
							{
								$sql ="SELECT * FROM $nmTable ";
								$arr = $this->queryAction($sql,'S');				
								
								$i = 1;
								if(count($arr) > 1) 
								{
									foreach($arr as $row)
									{
										if($i == '1')
										{
											//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
											$sql2 = "SELECT MAX(id) AS id FROM $nmTable WHERE id<>'$ID' ";
											$arr2 = $this->queryAction($sql2,'S');						
											foreach($arr2 as $row2)
											{
												$idObat = $row2['id'];
											}
											
											$sql2 = "SELECT total, total_real FROM $nmTable WHERE id='$idObat' ";
											$arr2 = $this->queryAction($sql2,'S');						
											foreach($arr2 as $row2)
											{
												$totAsal = $row2['total'];
												$tot = $totAsal + $resepHrg;
												$totReal = $row2['total_real'] + $resepHrg;
												
												$sqlUpdate = "UPDATE $nmTable 
																SET r_item='$resepHrg',
																	total='$tot',
																	total_real='$totReal'
																WHERE id = '$idObat' ";
												$this->queryAction($sqlUpdate,'C');
											}
										}
										
										$i++;
									}
									
									//update total transaksi sementara
									$t = ($this->getViewState('totSementara') + $resepHrg);						
									$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
									$this->setViewState('totSementara',$t);	
								}
							}	
						}
					}
					
					$sql = "DELETE FROM $nmTable WHERE id='$ID'";						
					$this->queryAction($sql,'C');
				}
							
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				
				$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";				
				$arrBhp=$this->queryAction($sql,'S');
					
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false; $this->tundaBtn->Enabled = false;
					
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
					$this->clearViewState('nmTable');//Clear the view state				
					
					$t = '0';						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);					
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();
				
				$this->BhpGrid->DataSource = $arrBhp;//Insert new row in tabel bro...
				$this->BhpGrid->dataBind();	
								
				//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);	
				$this->msgStok->Text='';				
				$this->totSementara->Text=$this->getViewState('totSementara');				
			//}	
			
		//}	
    }
	
	public function deleteClickedObat($sender,$param)
	{
		$this->clearViewState('st_imunisasi');
		$this->clearViewState('id_kel_imunisasi');
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridObat->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableObat = $this->getViewState('nmTableObat');
		
		$sql="SELECT id_kel_racik,total, st_imunisasi, st_racik, r_item, r_racik, bungkus_racik FROM $nmTableObat WHERE id='$ID'";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$n=$row['total'];					
			$t = ($this->getViewState('totSementara') - $n);						
			//$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
			$this->setViewState('totSementara',$t);					
			$stRacik = $row['st_racik'];
			$stImunisasi = $row['st_imunisasi'];
			$resepHrg=$row['r_item'];
			$racikHrg=$row['r_racik'];
			$bungkusRacikHrg=$row['bungkus_racik'];
			
			if($stRacik == '0' && $stImunisasi == '0' && $resepHrg > 0)//Jika yg didelete itu adalah bukan kelompok obat racikan dan mengandung nilai r_item
			{					
				$sql ="SELECT * FROM $nmTableObat WHERE st_racik='0' AND st_imunisasi = '0'";
				$arr = $this->queryAction($sql,'S');				
				
				$i = 1;
				if(count($arr) > 1) 
				{
					foreach($arr as $row)
					{
						if($i == '1')
						{
							//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
							$sql2 = "SELECT MAX(id) AS id FROM $nmTableObat WHERE st_racik='0' AND st_imunisasi = '0' AND id<>'$ID' GROUP BY id";
							$arr2 = $this->queryAction($sql2,'S');						
							foreach($arr2 as $row2)
							{
								$idObat = $row2['id'];
							}
							
							$sql2 = "SELECT total, total_real FROM $nmTableObat WHERE id='$idObat' ";
							$arr2 = $this->queryAction($sql2,'S');						
							foreach($arr2 as $row2)
							{
								$totAsal = $row2['total'];
								$tot = $totAsal + $resepHrg;
								$totReal = $row2['total_real'] + $resepHrg;
								
								$sqlUpdate = "UPDATE $nmTableObat 
												SET r_item='$resepHrg',
													total='$tot',
													total_real='$totReal'
												WHERE id = '$idObat' ";
								$this->queryAction($sqlUpdate,'C');
							}
						}
						
						$i++;
					}
					
					//update total transaksi sementara
					$t = ($this->getViewState('totSementara') + $resepHrg);						
					//$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);	
				}
			}
			elseif($stRacik == '1' && $racikHrg > 0)//Jika yg didelete itu adalah kelompok obat racikan dan mengandung nilai r_racik
			{						
				$idKelRacik = $row['id_kel_racik'];
				
				$sqlRacik="SELECT id_kel_racik FROM $nmTableObat WHERE id_kel_racik='$idKelRacik'";
				$arrRacik = $this->queryAction($sqlRacik,'S');				
				
				$i = 1;
				if(count($arrRacik) > 1) //Jika dalam kelompok racikan tsb terdapat lebih dari 1 obat racikan
				{
					foreach($arrRacik as $rowRacik)
					{
						if($i == '1')
						{
							//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
							$sqlRacik2 = "SELECT MAX(id) AS id FROM $nmTableObat WHERE id_kel_racik='$idKelRacik' AND id<>'$ID' GROUP BY id";
							$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
							foreach($arrRacik2 as $rowRacik2)
							{
								$idObatRacik = $rowRacik2['id'];
							}
							
							$sqlRacik2 = "SELECT total, total_real FROM $nmTableObat WHERE id='$idObatRacik' ";
							$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
							foreach($arrRacik2 as $rowRacik2)
							{
								$totRacikAsal = $rowRacik2['total'];
								$totRacik = $totRacikAsal + $resepHrg + $racikHrg + $bungkusRacikHrg;
								$totReal = $rowRacik2['total_real'] + $resepHrg + $racikHrg + $bungkusRacikHrg;
								
								$sqlUpdateRacik = "UPDATE $nmTableObat 
												SET r_item='$resepHrg',
													r_racik='$racikHrg',
													bungkus_racik='$bungkusRacikHrg',
													total='$totRacik',
													total_real='$totReal'
												WHERE id = '$idObatRacik' ";
								$this->queryAction($sqlUpdateRacik,'C');
							}
						}
						
						$i++;
					}
					
					//update total transaksi sementara
					$t = ($this->getViewState('totSementara') + $resepHrg + $racikHrg + $bungkusRacikHrg);						
					//$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);	
				}
				else
				{
					//jika obat racikan yg di delete mengandung nilai r_item
					if($resepHrg > 0)
					{
						$sql ="SELECT * FROM $nmTableObat ";
						$arr = $this->queryAction($sql,'S');				
						
						$i = 1;
						if(count($arr) > 1) 
						{
							foreach($arr as $row)
							{
								if($i == '1')
								{
									//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
									$sql2 = "SELECT MAX(id) AS id FROM $nmTableObat WHERE id<>'$ID' ";
									$arr2 = $this->queryAction($sql2,'S');						
									foreach($arr2 as $row2)
									{
										$idObat = $row2['id'];
									}
									
									$sql2 = "SELECT total, total_real FROM $nmTableObat WHERE id='$idObat' ";
									$arr2 = $this->queryAction($sql2,'S');						
									foreach($arr2 as $row2)
									{
										$totAsal = $row2['total'];
										$tot = $totAsal + $resepHrg;
										$totReal = $row2['total_real'] + $resepHrg;
										
										$sqlUpdate = "UPDATE $nmTableObat 
														SET r_item='$resepHrg',
															total='$tot',
															total_real='$totReal'
														WHERE id = '$idObat' ";
										$this->queryAction($sqlUpdate,'C');
									}
								}
								
								$i++;
							}
							
							//update total transaksi sementara
							$t = ($this->getViewState('totSementara') + $resepHrg);						
							//$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
							$this->setViewState('totSementara',$t);	
						}
					}	
				}
			}
			
			$sql = "DELETE FROM $nmTableObat WHERE id='$ID'";						
			$this->queryAction($sql,'C');
		}
		
		$sql="SELECT * FROM $nmTableObat ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
			
		$jmlData=0;
		foreach($arrData as $row)
		{
			$jmlData++;
		}
		
		if($jmlData==0)
		{
			$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table		
			$this->clearViewState('nmTableObat');//Clear the view state				
			
			$t = '0';						
			//$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
			$this->setViewState('totSementara',$t);					
		}
		
		$this->Page->CallbackClient->focus($this->DDObat);	
		$this->msgStok->Text='';				
		//$this->totSementara->Text = $this->bulatkan($this->getViewState('totSementara'));	
	}	
	
	public function prosesModal($sender,$param)
	{
		$index = $param->CallbackParameter->Id;
		$nmTableTmp = $param->CallbackParameter->nmTableTmp;
		
		//$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		if($nmTableTmp)
		{
			$sql="SELECT * FROM $nmTableTmp ";
			$arr = $this->queryAction($sql,'S');
			
			$this->Repeater->Items[$index]->Repeater2->DataSource = $arr;
			$this->Repeater->Items[$index]->Repeater2->dataBind();			
			
			$this->Repeater->Items[$index]->nmTableTmp->Text = $nmTableTmp;
			$this->Repeater->Items[$index]->jmlFile->setText(count($arr).' File');
			//$this->Repeater->Items[$index]->jmlFile->setText($index.' '.count($arr).' File '.$sql);
			
			//$this->Repeater->Items[$index]->Repeater2->render($param->getNewWriter());
			$this->showTable2->render($param->getNewWriter());
		}
		
		//$this->Repeater->Items[$index]->Repeater2->render($param->getNewWriter());
		//$this->checkRegister();
		
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$idTdk = $item->DataItem['id_tindakan'];
			
			$item->nmTindakan->Text = strtoupper(RadTdkRecord::finder()->findByPk($idTdk)->nama);
			$item->HtmlArea->Text = $item->DataItem['catatan'];
			
			
			//$nmField = $item->DataItem['nm_field'];
			//$item->nmSyarat->Text = $nmSyarat;
			//$sql="SELECT * FROM tbd_pegawai WHERE kelompok = '1' ";
			//$arr = $this->queryAction($sql,'S');
			
			//$item->Repeater2->DataSource = $arr;
			//$item->Repeater2->dataBind();
			
			$nmTableTmp = $item->nmTableTmp->Text ;
			
			if($nmTableTmp)
			{
				$sql="SELECT * FROM $nmTableTmp  ";
				$arr = $this->queryAction($sql,'S');
				
				$item->Repeater2->DataSource = $arr;
				$item->Repeater2->dataBind();		
				
				$item->nmTableTmp->Text = $nmTableTmp;
				$item->jmlFile->setText(count($arr).' File');	
			}	
			else
			{
				$item->jmlFile->setText('0 File');	
			}	
        }
    }
	
	public function repeaterDataBound2($sender,$param)
    {
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			$item->idTableTmp->Value = $item->DataItem['id'];
			$item->nmFileTmp->Text = $item->DataItem['nama'];
        }
    }
	
	public function delFileUploadClicked($sender,$param)
    {
		$item = $sender->Parent->Parent->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
		
		$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
		
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$item2 = $sender->getNamingContainer(); 
					$index2 = $this->Repeater->Items[$index]->Repeater2->DataKeys[$item2->getItemIndex()];
					$id = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				case "AlternatingItem":					
					$item2 = $sender->getNamingContainer(); 
					$index2 = $this->Repeater->Items[$index]->Repeater2->DataKeys[$item2->getItemIndex()];
					$id = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				default:
					break;
			}
		}
		
		$sql = "DELETE FROM $nmTableTmp WHERE id = $id ";
		$this->queryAction($sql,'C');
		
		//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->Text;
		$urlTmp ='protected/pages/Rad/foto/tmp//'.$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->Text;
		if(file_exists($urlTmp))
		{
			unlink($urlTmp);
		}
		
		$sql="SELECT * FROM $nmTableTmp  ";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) > 0)
		{
			$this->Repeater->Items[$index]->Repeater2->DataSource = $arr;
			$this->Repeater->Items[$index]->Repeater2->dataBind();	
			
			$this->Repeater->Items[$index]->jmlFile->setText(count($arr).' File');	
		}
		else
		{
			$this->queryAction($nmTableTmp,'D');
			$this->Repeater->Items[$index]->nmTableTmp->Text = '';
								
			$this->Repeater->Items[$index]->Repeater2->DataSource = '';
			$this->Repeater->Items[$index]->Repeater2->dataBind();	
			
			$this->Repeater->Items[$index]->jmlFile->setText('0 File');	
		}
			
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
		//$value = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
		//$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->setText($value);
	}
	
	public function Repeater2Callback($sender,$param)
    {
	
	}
	
	public function uploadBtnClicked($sender,$param)
    {
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		$jml = $this->Repeater->Items[$index]->jmlFoto->Text;
		
		if(intval($jml) > 0)
		{
			$noTrans = $sender->CommandParameter;
			$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$jml.'\');');
		}
		else
		{
			$this->Repeater->Items[$index]->jmlFoto->setText('');
			$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah Foto Yang Akan Diunggah tidak sesuai !\'); document.all.'.$this->Repeater->Items[$index]->jmlFoto->getClientID().'.focus();');
			//$this->Repeater->Items[$index]->jmlFoto->Focus();
		}
	}
	
	public function uploadDocBtnClicked($sender,$param)
    {
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		$jml = $this->Repeater->Items[$index]->jmlDok->Text;
		
		if(intval($jml) > 0)
		{
			$noTrans = $sender->CommandParameter;
			$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$jml.'\');');
		}
		else
		{
			$this->Repeater->Items[$index]->jmlDok->setText('');
			$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah Dokumen Yang Akan Diunggah tidak sesuai !\'); document.all.'.$this->Repeater->Items[$index]->jmlDok->getClientID().'.focus();');
			//$this->Repeater->Items[$index]->jmlFoto->Focus();
		}
	}
	
	
	public function checkRegister()
    {
		$tmp = $this->DDReg->SelectedValue;
		$jnsPasien = $this->modeInput->SelectedValue;
				
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			if(RadJualRecord::finder()->find('no_reg = ?', $tmp))
			{								
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_rad_penjualan a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg = '$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = RadJualRecord::finder()->findBySql($sql);
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			if(RadJualInapRecord::finder()->find('no_reg = ?', $tmp))
			{						
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_rad_penjualan_inap a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg='$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = RadJualInapRecord::finder()->findBySql($sql);
			}	
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			if(RadJualLainRecord::finder()->find('no_reg = ?', $tmp))
			{					
				$sql ="SELECT 
						  tbt_rad_penjualan_lain.nama AS id,
						  tbt_rad_penjualan_lain.no_trans,
						  tbt_rad_penjualan_lain.id_tindakan
						FROM
						  tbt_rad_penjualan_lain 
						WHERE tbt_rad_penjualan_lain.no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
						
				$tmpPasien = RadJualLainRecord::finder()->findBySql($sql);
			}
		}
		
		if($tmpPasien)
		{	
			$this->setViewState('notrans',$this->notrans->Text);						
			$this->setViewState('nama',$tmpPasien->id);				
			
			if ($jnsPasien=='0' || $jnsPasien=='1')
			{
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->dokter);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->dokter->Text= $tmpPasien->dokter;
			}
			else
			{
				$this->cm->Text= '-';
				$this->dokter->Text= '-';
			}
						
			
			$this->nama->Text = $tmpPasien->id;			
									
			$this->showFirst->Enabled = false;
			$this->showSecond->Display = 'Dynamic';
			$this->showTable2->Display = 'Dynamic';
			$this->showTable->Display = 'Dynamic';			
			
			$this->errMsg->Text='';		
			
			$noTrans=$this->getViewState('notrans');
			$cm = $this->formatCm($this->cariCm->Text);
			
			if ($jnsPasien=='0')
			{
				$sql="SELECT * FROM tbt_rad_penjualan WHERE no_reg = '$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			elseif ($jnsPasien=='1')
			{
				$sql="SELECT * FROM tbt_rad_penjualan_inap WHERE no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			elseif ($jnsPasien=='2')
			{
				$sql="SELECT * FROM tbt_rad_penjualan_lain WHERE no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			
			$arr = $this->queryAction($sql,'R');
			
			$this->Repeater->DataSource=$arr;
			$this->Repeater->dataBind();
			$this->dataStokObat();
			$this->cetakBtn->Enabled=true;
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->cetakBtn->Enabled=false;
			$this->notrans->Focus();
		}
    }	
		
	public function checkRegister2($sender,$param)
    {
		$tmp = $this->DDReg->SelectedValue;
		$jnsPasien = $this->modeInput->SelectedValue;
				
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			if(RadJualRecord::finder()->find('no_reg = ?', $tmp))
			{								
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_rad_penjualan a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg = '$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = RadJualRecord::finder()->findBySql($sql);
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			if(RadJualInapRecord::finder()->find('no_reg = ?', $tmp))
			{						
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_rad_penjualan_inap a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg='$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = RadJualInapRecord::finder()->findBySql($sql);
			}	
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			if(RadJualLainRecord::finder()->find('no_reg = ?', $tmp))
			{					
				$sql ="SELECT 
						  tbt_rad_penjualan_lain.nama AS id,
						  tbt_rad_penjualan_lain.no_trans,
						  tbt_rad_penjualan_lain.id_tindakan
						FROM
						  tbt_rad_penjualan_lain 
						WHERE tbt_rad_penjualan_lain.no_reg='$tmp' ";
						
				$tmpPasien = RadJualLainRecord::finder()->findBySql($sql);
			}
		}
		
		if($tmpPasien)
		{	
			$this->setViewState('notrans',$this->notrans->Text);						
			$this->setViewState('nama',$tmpPasien->id);				
			
			if ($jnsPasien=='0' || $jnsPasien=='1')
			{
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->dokter);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->dokter->Text= $tmpPasien->dokter;
			}
			else
			{
				$this->cm->Text= '-';
				$this->dokter->Text= '-';
			}
						
			
			$this->nama->Text = $tmpPasien->id;			
									
			$this->showFirst->Enabled = false;
			$this->showSecond->Display = 'Dynamic';
			$this->showTable->Display = 'Dynamic';			
			
			$this->errMsg->Text='';					
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_tdk VARCHAR(4) NOT NULL,									 
										 nilai TEXT DEFAULT '',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');						
			}			
			
			$noTrans=$this->getViewState('notrans');
			$cm = $this->formatCm($this->cariCm->Text);
			
			if ($jnsPasien=='0')
			{
				$sql="SELECT * FROM tbt_rad_penjualan WHERE no_reg = '$tmp' ";
			}
			elseif ($jnsPasien=='1')
			{
				$sql="SELECT * FROM tbt_rad_penjualan_inap WHERE no_reg='$tmp'";
			}
			elseif ($jnsPasien=='2')
			{
				$sql="SELECT * FROM tbt_rad_penjualan_lain WHERE no_reg='$tmp' ";
			}
			//RadJualRecord::finder()->findBySql($sql);
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$idTdk = $row['id_tindakan'];
				
				$sql="SELECT nama FROM tbm_rad_tindakan WHERE kode='$idTdk' ";
				
				$tmpTdk = RadTdkRecord::finder()->findBySql($sql);					 				
				$nama = $tmpTdk->nama;
				$nilai= '';				
				
				$sql="INSERT INTO $nmTable (nama,id_tdk,nilai) VALUES ('$nama','$idTdk','$nilai')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql="DELETE FROM $nmTable WHERE id_tdk='RUJ'";
			$arr=$this->queryAction($sql,'C');
			$sql="DELETE FROM $nmTable WHERE id_tdk='PDT'";
			$arr=$this->queryAction($sql,'C');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;				
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();	
		}
		else
		{
			$this->showFirst->Enabled = true;
			$this->showSecond->Display = 'None';
			$this->showTable2->Display = 'None';
			$this->showTable->Display = 'None';	
			
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
		}
    }	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		}
		else
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}
			
		
		$page=$this->queryAction($sql,'S');		 
		
		return $page;
		
	}
	
	/*
	protected function getDataRows($offset,$rows)
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
        $data=$this->queryAction($sql,'R');
        $page=array();	
		
        for($i=0;$i<$rows;++$i)
        {
            if($offset+$i<$this->getRowCount())
                $page[$i]=$data[$offset+$i];
        }
		
		
		
        return $page;
    }
	
	
	protected function getRowCount()
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$jmlRows=0;
		foreach($arr as $row)
		{
			$jmlRows=$jmlRows+1;
		}
        return $jmlRows;
    }
	*/
	
	public function changePage($sender,$param)
	{				
		$limit=$this->UserGrid->PageSize;		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		$nmTable = $this->getViewState('nmTable');		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
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
				
		//$this->DataGrid->DataKeys[$param->Item->ItemIndex]
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->nilai->TextBox->Columns=15;
		   $item->normal->TextBox->Columns=15;
		   $item->normal2->TextBox->Columns=15;
        }       
    }
	
	public function useNumericPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
	
	public function useNextPrevPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
	
	public function changePageSize($sender,$param)
	{
		
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}	
	
	
	public function sortGrid($sender,$param)
	{		
		$item = $param->SortExpression;
		
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
	}
	
	public function toggleColumnVisibility($sender,$param)
	{			
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
		$this->UserGrid->dataBind();
	}	
	
	public function editItem($sender,$param)
    {
        
		if ($this->User->IsAdmin)
		{
		
			$limit=$this->UserGrid->PageSize;		
			$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
			
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();	
		}	
    }
	
	public function cancelItem($sender,$param)
    {
       	$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$this->UserGrid->EditItemIndex=-1;
		
       	$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
    }
	
	public function saveItem($sender,$param)
    {
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$nmTable = $this->getViewState('nmTable');
		
        $item = $param->Item;		
		$id = $this->UserGrid->DataKeys[$item->ItemIndex];
		$nilai = ucwords(trim($item->nilai->TextBox->Text));
		$normal = $item->normal->TextBox->Text;
		$normal2 = $item->normal2->TextBox->Text;
		
		$sql="UPDATE $nmTable SET nilai='$nilai',normal='$normal',normal_perempuan='$normal2' WHERE id='$id'";
		$this->queryAction($sql,'C');
      
        $this->UserGrid->EditItemIndex=-1;
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
    }
	
	public function batalClicked($sender,$param)
    {		
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
					
					if($nmTableTmp != '')
					{
						$sql = "SELECT * FROM $nmTableTmp ";
						$arr = $this->queryAction($sql,'S');
						
						foreach($arr as $row)
						{
							//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
							$urlTmp ='protected/pages/Rad/foto/tmp//'.$row['nama'];
								
							if(file_exists($urlTmp))
							{
								unlink($urlTmp);
							}
						}
						
						$this->queryAction($nmTableTmp,'D');
					}
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				case "AlternatingItem":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
					
					if($nmTableTmp != '')
					{
						$sql = "SELECT * FROM $nmTableTmp ";
						$arr = $this->queryAction($sql,'S');
						
						foreach($arr as $row)
						{
							//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
							$urlTmp ='protected/pages/Rad/foto/tmp//'.$row['nama'];
								
							if(file_exists($urlTmp))
							{
								unlink($urlTmp);
							}
						}
						
						$this->queryAction($nmTableTmp,'D');
					}
					
					break;
				default:
					break;
			}
		}
		
		$this->Response->Reload();				
		$this->Response->redirect($this->Service->constructUrl('Rad.beritaRad'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	public function cetakClicked($sender,$param)
  {
			
				if($this->getViewState('nmTableObat'))
				{	
					$this->prosesCetakClicked();
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Belum ada data BHP yang di input! <br/><br/></p>\',timeout: 4000,dialog:{
							modal: true
						}});');
				}
			
		
		
	}
	
	public function prosesCetakClicked($sender,$param)
    {	
		if($this->DDDokterRad->SelectedValue != '')
		{
			$jnsPasien = $this->modeInput->SelectedValue;
			$noReg = $this->DDReg->SelectedValue;	
			$dokterRad = $this->DDDokterRad->SelectedValue;	
			
			$cm = $this->formatCm($this->cariCm->Text);
			$nama = $this->nama->Text;
			$dokter = $this->dokter->Text;
			
			$operator = $this->User->IsUserNip;
			$nipTmp = $this->User->IsUserNip;
			$tgl = date('Y-m-d');
			$wkt = date('G:i:s');
			
			$tableObat = $this->getViewState('nmTableObat');
					$sumber = '01';
					
					//obat penunjang
					if($this->getViewState('nmTableObat'))
					{	
						$tujuan = '15';
						
						$sql="SELECT * FROM $tableObat ORDER BY id";
						$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
						foreach($arrData as $row)
						{
							if($jnsPasien == '0' ) //Pasien Rawat Jalan
							{	
								$ObatJualPenunjang= new ObatJualPenunjangRecord();
								
								$jmlTotal = $row['total'];
								
								if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND expired = ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],$row['expired']))
								{	
									$sqlRwtJln = "SELECT no_trans_rwtjln,dokter,klinik FROM tbt_rad_penjualan WHERE no_reg = '$noReg' GROUP BY no_reg ";
									
									//$ObatJualPenunjang->no_trans = $notransTmp;
									$ObatJualPenunjang->jns_trans = 'Rad';
									$ObatJualPenunjang->no_trans_rwtjln = RadJualRecord::finder()->findBySql($sqlRwtJln)->no_trans_rwtjln;						
									$ObatJualPenunjang->cm = $cm;
									$ObatJualPenunjang->no_reg = $noReg;
									$ObatJualPenunjang->dokter=RadJualRecord::finder()->findBySql($sqlRwtJln)->dokter;
									$ObatJualPenunjang->sumber='01';
									$ObatJualPenunjang->tujuan=$row['tujuan'];
									$ObatJualPenunjang->klinik = RadJualRecord::finder()->findBySql($sqlRwtJln)->klinik;
									$ObatJualPenunjang->id_obat=$row['id_obat'];
									$ObatJualPenunjang->id_harga=$row['id_harga'];
									$ObatJualPenunjang->tgl=date('y-m-d');
									$ObatJualPenunjang->wkt=date('G:i:s');
									$ObatJualPenunjang->operator=$operator;
									$ObatJualPenunjang->hrg_nett=$row['hrg_nett'];
									$ObatJualPenunjang->hrg_ppn=$row['hrg_ppn'];
									$ObatJualPenunjang->hrg_nett_disc=$row['hrg_nett_disc'];
									$ObatJualPenunjang->hrg_ppn_disc=$row['hrg_ppn_disc'];
									$ObatJualPenunjang->hrg=$row['hrg'];
									$ObatJualPenunjang->jumlah=$row['jml'];
									$ObatJualPenunjang->total=$jmlTotal;
									$ObatJualPenunjang->total_real=$row['total_real'];
									$ObatJualPenunjang->flag='0';
									$ObatJualPenunjang->st_obat_khusus=$row['st_obat_khusus'];
									$ObatJualPenunjang->st_racik=$row['st_racik'];
									$ObatJualPenunjang->id_kel_racik=$row['id_kel_racik'];
									$ObatJualPenunjang->r_item=$row['r_item'];
									$ObatJualPenunjang->r_racik=$row['r_racik'];
									$ObatJualPenunjang->bungkus_racik=$row['bungkus_racik'];
									$ObatJualPenunjang->id_kemasan=$row['id_kemasan'];
									$ObatJualPenunjang->st_imunisasi=$row['st_imunisasi'];
									$ObatJualPenunjang->id_kel_imunisasi=$row['id_kel_imunisasi'];
									$ObatJualPenunjang->id_bhp=$row['id_bhp'];
									$ObatJualPenunjang->bhp=$row['bhp'];
									$ObatJualPenunjang->expired=$row['expired'];
									$ObatJualPenunjang->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
									$ObatJualPenunjang->st_paket=$row['st_paket'];
									$ObatJualPenunjang->id_kel_paket=$row['id_kel_paket'];
									$ObatJualPenunjang->jml_paket=$row['jml_paket'];
									$ObatJualPenunjang->st_costsharing=$row['st_costsharing'];
									$ObatJualPenunjang->st_ffs=$row['st_ffs'];
									$ObatJualPenunjang->ket=trim($this->ket->Text);
									$ObatJualPenunjang->Save();			
									
									$stokAkhir=$stok->jumlah-$row['jml'];
									$stok->jumlah=$stokAkhir;
									$stok->Save();
									
								}	
							}
							elseif($jnsPasien == '2' ) //Pasien Rawat Lain
							{	
								$ObatJualPenunjangLain= new ObatJualPenunjangLainRecord();
								
								$jmlTotal = $row['total'];
								
								if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ? AND expired = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],$tujuan,$row['expired']))
								{
									$sqlRwtJln = "SELECT no_trans_pas_luar FROM tbt_rad_penjualan_lain WHERE no_reg = '$noReg' GROUP BY no_reg ";
									
									//$ObatJualPenunjangLain->no_trans = $notransTmp;
									$ObatJualPenunjangLain->jns_trans = 'Rad';
									$ObatJualPenunjangLain->no_trans_pas_luar = RadJualLainRecord::finder()->findBySql($sqlRwtJln)->no_trans_pas_luar;			
									$ObatJualPenunjangLain->no_reg = $noReg;
									$ObatJualPenunjangLain->dokter=$this->getViewState('dokter');
									$ObatJualPenunjangLain->sumber='01';
									$ObatJualPenunjangLain->tujuan=$row['tujuan'];
									$ObatJualPenunjangLain->id_obat=$row['id_obat'];
									$ObatJualPenunjangLain->id_harga=$row['id_harga'];
									$ObatJualPenunjangLain->tgl=date('y-m-d');
									$ObatJualPenunjangLain->wkt=date('G:i:s');
									$ObatJualPenunjangLain->operator=$operator;
									$ObatJualPenunjangLain->hrg_nett=$row['hrg_nett'];
									$ObatJualPenunjangLain->hrg_ppn=$row['hrg_ppn'];
									$ObatJualPenunjangLain->hrg_nett_disc=$row['hrg_nett_disc'];
									$ObatJualPenunjangLain->hrg_ppn_disc=$row['hrg_ppn_disc'];
									$ObatJualPenunjangLain->hrg=$row['hrg'];
									$ObatJualPenunjangLain->jumlah=$row['jml'];
									$ObatJualPenunjangLain->total=$jmlTotal;
									$ObatJualPenunjangLain->total_real=$row['total_real'];
									$ObatJualPenunjangLain->flag='0';
									$ObatJualPenunjangLain->st_obat_khusus=$row['st_obat_khusus'];
									$ObatJualPenunjangLain->st_racik=$row['st_racik'];
									$ObatJualPenunjangLain->id_kel_racik=$row['id_kel_racik'];
									$ObatJualPenunjangLain->r_item=$row['r_item'];
									$ObatJualPenunjangLain->r_racik=$row['r_racik'];
									$ObatJualPenunjangLain->bungkus_racik=$row['bungkus_racik'];
									$ObatJualPenunjangLain->id_kemasan=$row['id_kemasan'];
									$ObatJualPenunjangLain->st_imunisasi=$row['st_imunisasi'];
									$ObatJualPenunjangLain->id_kel_imunisasi=$row['id_kel_imunisasi'];
									$ObatJualPenunjangLain->id_bhp=$row['id_bhp'];
									$ObatJualPenunjangLain->bhp=$row['bhp'];
									$ObatJualPenunjangLain->expired=$row['expired'];
									$ObatJualPenunjangLain->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
									$ObatJualPenunjangLain->st_paket=$row['st_paket'];
									$ObatJualPenunjangLain->id_kel_paket=$row['id_kel_paket'];
									$ObatJualPenunjangLain->jml_paket=$row['jml_paket'];
									$ObatJualPenunjangLain->ket=trim($this->ket->Text);
									$ObatJualPenunjangLain->Save();			
									
									$stokAkhir=$stok->jumlah-$row['jml'];
									$stok->jumlah=$stokAkhir;
									$stok->Save();
									
								}	
							}
						}
					}
					
					if($this->getViewState('nmTableObat'))
					{		
						$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table						
						$this->UserGridObat->DataSource='';
						$this->UserGridObat->dataBind();
						$this->clearViewState('nmTableObat');//Clear the view state				
					}
					
			foreach($this->Repeater->getItems() as $item) {
				switch($item->getItemType()) {
					case "Item":					
						$index = $this->Repeater->DataKeys[$item->getItemIndex()];
						$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
						$noTrans = $this->Repeater->Items[$index]->noTrans->Text;
						$catatan = $this->Repeater->Items[$index]->HtmlArea->Text;
						
						//UPDATE tbt_rad_hasil	
						if ($jnsPasien=='0')
						{
							$sql="UPDATE tbt_rad_penjualan SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='1')
						{
							$sql="UPDATE tbt_rad_penjualan_inap SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='2')
						{
							$cm = '';
							$sql="UPDATE tbt_rad_penjualan_lain SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
											
						$this->queryAction($sql,'C');
						
						//jika ada file yang di upload
						if($nmTableTmp != '')
						{
							$sql = "SELECT * FROM $nmTableTmp ";
							$arr = $this->queryAction($sql,'S');
							
							foreach($arr as $row)
							{
								$tipeFile = $row['tipe_file'];
								
								$data = new RadFotoHasilRecord();
								$data->no_trans = $noTrans;
								$data->tgl = $tgl;
								$data->wkt = $wkt;
								$data->operator = $operator;
								$data->nama_file = $row['nama'];
								$data->tipe_file = $row['tipe'];
								$data->tipe_pasien = $jnsPasien;
								$data->Save();			
								
								//$url ='protected\pages\Rad\foto\\'.$row['nama'];
								//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
								$url ='protected/pages/Rad/foto//'.$row['nama'];
								$urlTmp ='protected/pages/Rad/foto/tmp//'.$row['nama'];
									
								if(file_exists($urlTmp))
								{
									copy($urlTmp,$url);
									unlink($urlTmp);
								}
							}
							
							$this->queryAction($nmTableTmp,'D');
						}
						
						//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
						break;
					case "AlternatingItem":					
						$index = $this->Repeater->DataKeys[$item->getItemIndex()];
						$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
						$noTrans = $this->Repeater->Items[$index]->noTrans->Text;
						$catatan = $this->Repeater->Items[$index]->HtmlArea->Text;
						
						//UPDATE tbt_rad_hasil	
						if ($jnsPasien=='0')
						{
							$sql="UPDATE tbt_rad_penjualan SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='1')
						{
							$sql="UPDATE tbt_rad_penjualan_inap SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='2')
						{
							$cm = '';
							$sql="UPDATE tbt_rad_penjualan_lain SET catatan = '$catatan', dokter_rad = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
											
						$this->queryAction($sql,'C');
						
						//jika ada file yang di upload
						if($nmTableTmp != '')
						{
							$sql = "SELECT * FROM $nmTableTmp ";
							$arr = $this->queryAction($sql,'S');
							
							foreach($arr as $row)
							{
								$tipeFile = $row['tipe_file'];
								
								$data = new RadFotoHasilRecord();
								$data->no_trans = $noTrans;
								$data->tgl = $tgl;
								$data->wkt = $wkt;
								$data->operator = $operator;
								$data->nama_file = $row['nama'];
								$data->tipe_file = $row['tipe'];
								$data->tipe_pasien = $jnsPasien;
								$data->Save();			
								
								//$url ='protected\pages\Rad\foto\\'.$row['nama'];
								//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
								$url ='protected/pages/Rad/foto//'.$row['nama'];
								$urlTmp ='protected/pages/Rad/foto/tmp//'.$row['nama'];
									
								if(file_exists($urlTmp))
								{
									copy($urlTmp,$url);
									unlink($urlTmp);
								}
							}
							
							$this->queryAction($nmTableTmp,'D');
						}
						
						break;
					default:
						break;
				}
			}
			
			if ($jnsPasien=='2')
			{
				$sql="UPDATE tbt_rad_penjualan_lain SET st_cetak_hasil = '1' WHERE no_reg = '$noReg' AND id_tindakan='PDT'";
				$this->queryAction($sql,'C');
			}
				
			$this->Response->redirect($this->Service->constructUrl('Rad.cetakLapHasilRad',array('jnsPasien'=>$jnsPasien,'noReg'=>$noReg,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'dokterRad'=>$this->ambilTxt($this->DDDokterRad),'table'=>$table)));
			
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Dokter Radioterapi belum di pilih !<br/><br/></p>\',timeout: 4000,dialog:{
					modal: true
				}});
				document.all.'.$this->DDDokterRad->getClientID().'.focus();');
		}		
	}
}
?>
