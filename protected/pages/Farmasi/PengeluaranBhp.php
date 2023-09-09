<?php
class PengeluaranBhp extends SimakConf
{   
	public function onInit($param)
	{		
		parent::onInit($param);
		//$this->prosesPageAllow();
	}
	
	public function onPreInit ($param)
	{
		parent::onPreInit($param);
		
		/*if($this->Request['layout'] == 'modal') 
			$this->setMasterClass('Application.layouts.DialogLayout');
		else
			$this->setMasterClass('Application.layouts.MainLayout');*/
	}
	
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->modeStok->DataSource = DesFarmasiRecord::finder()->FindAll();
			$this->modeStok->dataBind();
			
			$depo = $this->request['depo'];
			if($depo == 'Rad')
			{
				$this->modeStok->SelectedValue = 15;
				$this->modeStok->enabled = false;
			}
			else
			{
				$this->modeStok->SelectedValue = '';
				$this->modeStok->enabled = true;
			}
			//$this->dataStokObat();
			$this->obatPanel->Enabled=false;
			$this->cetakBtn->Enabled=false;
			
			$this->DDKemasan->DataSource=KemasanRecord::finder()->findAll($criteria);
			$this->DDKemasan->dataBind();
			
			/*if($this->Request['layout'] == 'modal') 
			{
				$this->jdMain->Visible = false;
				//$this->batalBtn->Visible = false;
			}*/			
			//$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Lab.CariPasienLab&parentPage=Farmasi.PengeluaranBhp&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		}		
		
		if($this->getViewState('nmTableObat'))
		{
			$nmTableObat = $this->getViewState('nmTableObat');
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik, id_kel_imunisasi, id,  nama ASC ";
			$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGridObat->dataBind();	
			$this->cetakBtn->Enabled = true; 
			$this->showGridObat->Display = 'Dynamic';
		}
		else
		{
			$this->UserGridObat->DataSource = '';
			$this->UserGridObat->dataBind();	
			$this->cetakBtn->Enabled = false; 
			$this->showGridObat->Display = 'None';
		}
  }
	
	public function obatPanelCallBack($sender,$param)
  {
		$this->obatPanel->render($param->getNewWriter());
	}
	
	public function modeStokChanged($sender,$param)
  {
		$this->DDObat->SelectedValue = 'empty';
		$this->kodeObat->Text = '';
		$this->AutoComplete->Text = '';
		$this->jmlStok->Text = '';
		$this->jml->Text = '';
		
		if($this->modeStok->SelectedValue != '')
		{
			$this->dataStokObat();
			$this->obatPanel->Enabled=true;
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->AutoComplete->getClientID().'").focus();');	
		}
		else
		{
			if($this->getViewState('nmTableObat'))
			{		
				$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table						
				$this->UserGridObat->DataSource='';
				$this->UserGridObat->dataBind();
				$this->clearViewState('nmTableObat');//Clear the view state				
			}
		
			$this->obatPanel->Enabled=false;
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->modeStok->getClientID().'").focus();');	
		}
	}
	
	public function batalClicked($sender,$param)
  {	
		if($this->getViewState('nmTableObat'))
		{		
			$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table						
			$this->UserGridObat->DataSource='';
      $this->UserGridObat->dataBind();
			$this->clearViewState('nmTableObat');//Clear the view state				
		}
				
		//if($this->Request['layout'] != 'modal')
			$this->Response->redirect($this->Service->constructUrl('Farmasi.PengeluaranBhp'));		
		//else
			//$this->getPage()->getClientScript()->registerEndScript('', 'window.parent.modalCallback(); jQuery.FrameDialog.closeDialog();');		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	public function konfirmasiAmbil()
	{
		if($this->Page->IsValid)  // when all validations succeed
    {
			$this->getPage()->getClientScript()->registerEndScript('', '
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Apakah yakin data sudah benar ?<br/><br/></p>\',timeout: 1600000,dialog:{
					modal: true,
					buttons: {
						"Ya": function() {
							jQuery( this ).dialog( "close" );
							prosesKonfirmasiCetak(\'ya\');
						},
						"Tidak": function() {
							jQuery( this ).dialog( "close" );
						}
					}
				}});');		
		}
	}
	
	public function cetakClicked()
  {	
		$operator = $this->User->IsUserNip;
		$nipTmp = $this->User->IsUserNip;
		
		$tableObat = $this->getViewState('nmTableObat');
		$sumber = '01';
		
		if($this->getViewState('nmTableObat'))
		{	
			$tujuan = $this->modeStok->SelectedValue;
			
			$sql="SELECT * FROM $tableObat ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$noReg = $this->numRegister('tbt_obat_jual_poli',ObatJualPoliRecord::finder(),'73');
			
			foreach($arrData as $row)
			{
				$ObatJualPenunjang= new ObatJualPoliRecord();
							
				$jmlTotal = $row['total'];
				
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND expired = ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],$row['expired']))
				{	
					$ObatJualPenunjang->no_reg = $noReg;
					$ObatJualPenunjang->sumber='01';
					$ObatJualPenunjang->tujuan=$row['tujuan'];
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
					$ObatJualPenunjang->ket=trim($this->ket->Text);
					$ObatJualPenunjang->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
					
					/*$newStokInOut= new StokInOutRecord();
					$newStokInOut->tgl=date('Y-m-d');
					$newStokInOut->wkt=date('G:i:s');
					$newStokInOut->id_obat=$row['id_obat'];			
					$newStokInOut->tujuan=$row['tujuan'];
					$newStokInOut->jml_keluar=$row['jml'];						
					$newStokInOut->no_trans_asal=$noReg;
					$newStokInOut->st_trans='13';//pengeluaran BHP
					$newStokInOut->ket='Pengeluaran BHP di '.DesFarmasiRecord::finder()->findByPk($row['tujuan'])->nama;
					$newStokInOut->Save();*/
				}	
			}
				
			$this->queryAction($this->getViewState('nmTableObat'),'D');		
			$this->UserGridObat->DataSource='';
			$this->UserGridObat->dataBind();
			$this->clearViewState('nmTableObat');	
			
			$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakNotaBhp',array('noReg'=>$noReg)));
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Belum ada BHP yang ditambahkan !<br/><br/></p>\',timeout: 4000,dialog:{
					modal: true
				}});');	
		}
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function cariBtnClicked($sender,$param) 
	{
		$session=new THttpSession;
		$session->open();
		$dataCetak = $session['dataCetakHasilLab'];
		$session->remove('dataCetakHasilLab');
		
		if($dataCetak != '')
		{
			$this->Response->redirect($this->Service->constructUrl('Lab.cetakLapHasilLabRtf',$dataCetak));			
		}
		else
		{
			$url = "index.php?page=Pendaftaran.CariPasienPenunjang&modeTdkPenunjang=2";
			$this->getPage()->getClientScript()->registerEndScript('',"tesFrame('$url',jQuery(window).width()-100,jQuery(window).height()-50,'Pencarian Pasien')");		
		}
	}
	
	//------------------------------------------- BHP ------------------------------------------------------------	
	public function dataStokObat()
	{
		$modeStok = $this->modeStok->SelectedValue;
		$this->clearViewState('modeStok');
		$this->setViewState('modeStok',$modeStok);
		$tujuan = $this->modeStok->SelectedValue;
		
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
		
		//$this->showSql->Text = $sql;
	}
	
	public function suggestNames($sender,$param) 
	{
		// Get the token
		$token=$param->getToken();
		// Sender is the Suggestions repeater
		$sender->DataSource=$this->getDummyData($token);
		$sender->dataBind();                                                                                                     
	}
 
  public function suggestionSelected1($sender,$param)
	{
    $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
		//$this->setViewState('stCostSharing','0');
		
		if($id)
		{
			$this->kodeObat->Text=$id;
			
			$kelompokPasien = $this->getViewState('kelompokPasien');
			$stAsuransi = $this->getViewState('stAsuransi');
		
			if($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
			{
				$noTransJln = $this->getViewState('no_trans_rwtjln');
				//$nipJpk = RwtjlnRecord::finder()->findByPk($noTransJln)->nip_jkp;
				//$jpk_level_pegawai = JpkPegawaiRecord::finder()->findByPk($nipJpk)->jpk_level_pegawai;
				//$st_level_jpk = ObatRecord::finder()->findByPk($id)->st_level_jpk;
				
				if($st_level_jpk == '2')//obat tidak berlaku untuk semua level
				{
					if($this->cekObatLevelJpk($id,$jpk_level_pegawai) == 'False')
					{							
						$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
							jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">Obat Alkes yang dipilih tidak bisa digunakan untuk pasien JPK <b>'.JpkLevelPegawaiRecord::finder()->findByPk($jpk_level_pegawai)->nama.' </b>!. <br/><br/> </p>\',timeout: 6000000,dialog:{
							modal: true,
							buttons: {
								"Cost Sharing": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasiLevelJpk(\'ya\');
								},
								"Batalkan Penambahan": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasiLevelJpk(\'tidak\');
								},
							}
						}});');	
					}
					else
					{
						$this->chObat2();
					}
				}
				else
				{
					$this->chObat2();
				}
			}
			else
			{
				$this->chObat2();	
			}
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
		//$this->showSql->Text = $sql;
		return $arr;
  }
		
	public function chObat2()
	{
		if($this->kodeObat->Text != '')
		{
			$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
			$tujuan = $this->modeStok->SelectedValue;
			
		
			if($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
			{
				//$transInterval = ObatRecord::finder()->findByPk($idObat)->trans_interval;
				//if($transInterval > 0) //jika item obat memiliki jangka waktu transaksi selanjutnya yg dibolehkan
				//{
					//cek tanggal transaksi terakhir pasien jpk	
					$sql = "SELECT tgl FROM tbt_obat_jual WHERE id_obat = '$idObat' AND cm = '$cm' ORDER BY id DESC ";
					$tglTransAkhir = ObatJualRecord::finder()->findBySql($sql)->tgl;
					$bedaHari = $this->dateDifference(date('Y-m-d'), $tglTransAkhir);
					
					//if($bedaHari['days_total'] <= $transInterval)//belum melewati batas hari yg ditentukan => munculkan warning
					if($bedaHari['days_total'] <= 30)//batas hari transaksi untuk obat yg sama 30hari => munculkan warning
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
							jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">Pasien JPK sudah menerima obat pada tanggal <b>'.$this->convertDate($tglTransAkhir,'1').'</b> <br/>apakah akan melanjutkan transaksi?</p>\',timeout: 6000000,dialog:{
							modal: true,
							buttons: {
								"Ya": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasiLimitTransJpk(\'ya\');
								},
								"Tidak": function() {
									jQuery( this ).dialog( "close" );
									maskContent();
									konfirmasiLimitTransJpk(\'tidak\');
								},
							}
						}});');	
					}
					else
						$this->chObat3();	
				//}
				//else
				//	$this->chObat3();	
			}
			else
				$this->chObat3();
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
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->AutoComplete->getClientID().'").focus();');	
		}
	}
	
	public function chObat3()
	{
		$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
		$tujuan = $this->modeStok->SelectedValue;
		
		if($this->modePaket->SelectedValue == '0')//Mode NonPaket
		{
			$this->RBtipeRacik->Enabled=true;			
			$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idObat' AND tujuan = '$tujuan' GROUP BY id_obat,tujuan    ";
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
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->jml->getClientID().'").focus();');	
		}
		else //Mode Paket
		{
			$this->RBtipeRacik->Enabled=false;
		}
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
				$this->Page->CallbackClient->focus($this->AutoComplete);
			}	
		}
		else
		{
			$this->jmlBungkus->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDRacik);
		}	
		
		$this->jmlBungkus->Text = '';		
	}
	
	public function prosesClicked()
  {
		if($this->IsValid)  // when all validations succeed
    {
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
					
					$this->cekStok($jmlObat,$idBarang);
				}	
			}
		}
	}
	
	public function cekStok($jmlObat='',$idBarang='')
  {
		//$satelit = $this->Application->Parameters['satelit'];
		if($this->modePaket->SelectedValue == '0')//Mode NonPaket
		{
			$jmlObat = $this->jml->Text;	
			$idBarang = $this->DDObat->SelectedValue; $idBarang = $this->kodeObat->Text;
		}
			
		$this->setViewState('jmlKekurangan',$jmlObat);
		$tujuan = $this->modeStok->SelectedValue;
		
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
			$idTujuan = $this->modeStok->SelectedValue;
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
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($this->modeStok->SelectedValue)->nama;
			
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
								$this->makeTmpTblObat($id_harga,$jmlAmbil,$expired,$idBarang);
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
								$this->makeTmpTblObat($id_harga,$jmlAmbil,$expired,$idBarang);
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
		}
		else
		{
			if(!$this->getViewState('nmTableObat'))
			{
				$this->showGridObat->Visible=false;				
			}
			
			$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p class="msg_forbidden">Stok obat yang ada tidak cukup!</p>\',timeout: 3000,dialog:{
							modal: true}});');	
							
			//$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			
			$this->DDObat->SelectedValue = 'empty';
			$this->kodeObat->Text = '';
			$this->AutoComplete->Text = '';
			$this->jmlStok->Text = '';
			$this->Page->CallbackClient->focus($this->AutoComplete);
			$this->jml->Text = '';
			//$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled = false;
			//$this->jmlBungkus->Text = '';
			//$this->jmlBungkus->Enabled = false;
			//$this->msgStok->Text=$tmpStok;
		}
	}
	
	public function cekStokEdit($jmlObat,$idBarang)
  {
		//$satelit = $this->Application->Parameters['satelit'];
			
		$this->setViewState('jmlKekurangan',$jmlObat);	
		$tujuan = $this->modeStok->SelectedValue;
		
		$sql = "SELECT SUM(if(jumlah>0,(jumlah - IF(jumlah_ambil_pending>=0,jumlah_ambil_pending,0)),jumlah)) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idBarang' AND tujuan = '$tujuan' GROUP BY id";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tmpStok += $row['jumlah'];
		}	
		
		if($tmpStok >= $jmlObat)
		{
			//cari jumlah minimal 
			$idTujuan = $this->modeStok->SelectedValue;
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
			$nmBarang = ObatRecord::finder()->findByPk($idBarang)->nama;
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($this->modeStok->SelectedValue)->nama;
			
			if( ($tmpStok-$jmlObat) < $jmlStokTol)//jika sudah melewati batas toleransi
			{
				$this->msgStok->Text='<br/><br/>Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' mencapai <b>Batas Toleransi Minimal</b>. <br/>Pengeditan Obat Gagal';
				//$this->jml->Text = '';
			}
			else //belum melewati batas toleransi
			{
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
								$this->makeTmpTblObat($id_harga,$jmlAmbil,$expired,$idBarang);
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
								$this->makeTmpTblObat($id_harga,$jmlAmbil,$expired,$idBarang);
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
			
		}
		else
		{
			if(!$this->getViewState('nmTableObat'))
			{
				$this->showGridObat->Visible=false;				
			}
			
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p class="msg_forbidden">Stok obat yang ada tidak cukup!</p>\',timeout: 3000,dialog:{
							modal: true}});');
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
				//$this->tundaBtn->Enabled = true;
				
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
		
		$this->Page->CallbackClient->focus($this->AutoComplete);	
		$this->msgStok->Text='';				
		//$this->totSementara->Text = $this->bulatkan($this->getViewState('totSementara'));	
	}		
	
	public function makeTmpTblObat($id_harga,$jmlAmbil,$expired,$idObat='')
	{
		$this->showGridObat->Visible=true;	
		$this->msgStok->Text='';
		//$stCostSharing = $this->getViewState('stCostSharing');
		//$st_ffs = '0';
		//$stMultiTransJpk = '0';
			
		$kelompokPasien = $this->getViewState('kelompokPasien');
		$stAsuransi = $this->getViewState('stAsuransi');
		$tipeRacik = $this->RBtipeRacik->SelectedValue; //$this->getViewState('tipeRacik');
		$jnsPasien = $this->getViewState('jnsPasien');
		
		$resepHrg=$this->getViewState('resepHrg');
		$racikHrg=$this->getViewState('racikHrg');
		$bungkusRacikHrg=$this->getViewState('bungkusRacikHrg');
		
		$hrgTmp = 0;//Make initial value for $hrg
		$jml= $jmlAmbil;
			
			if($this->modePaket->SelectedValue == '0')//Mode NonPaket
			{
				if($this->getViewState('idObatEdit'))
					$idObat = $this->getViewState('idObatEdit');
				else
				{
					$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
				}
				
				$this->clearViewState('idObatEdit');
				
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
			$namaMargin = ObatKelompokMarginRecord::finder()->findByPk($idKelompok)->nama;
			//$satelit = $this->Application->Parameters['satelit'];
			$persenProvider = 0;
			
			//$st_level_jpk = ObatRecord::finder()->findByPk($idObat)->st_level_jpk;
			
			if($jnsPasien != 4) //bukan penjulan internal
			{
				if($kelompokPasien == '02'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$noTransJln = $this->getViewState('no_trans_rwtjln');
					$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJln)->perus_asuransi;							
					$sqlMargin = "SELECT * FROM tbm_provider_detail_obat_cover WHERE id_provider='$idPerusAsuransi' AND id_obat='$idObat' ";
					if(ProviderDetailObatCoverRecord::finder()->findBySql($sqlMargin))
						$persenMargin = ProviderDetailObatCoverRecord::finder()->findBySql($sqlMargin)->margin / 100;
					else
						$persenMargin = ObatKelompokMarginRecord::finder()->find('nama=? ',array($namaMargin))->persentase_asuransi / 100;
				}
				elseif($kelompokPasien == '07'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->find('nama=? ',array($namaMargin))->persentase_jamper / 100;
				}
				elseif($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->find('nama=? ',array($namaMargin))->persentase_jpk / 100;
				}
				else
				{
					$persenMargin = ObatKelompokMarginRecord::finder()->find('nama=? ',array($namaMargin))->persentase / 100;
				}
			}
			elseif($jnsPasien == '4') //penjulan internal
			{
				$persenMargin = ObatKelompokMarginRecord::finder()->find('nama=? ',array($namaMargin))->persentase_unit_internal / 100;
			}
			
			$r_item = JasaResepRacikanRecord::finder()->findByPk('1')->jasa_resep; 
			$tujuan = $this->modeStok->SelectedValue;
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
					 c.disc AS persentase_dokter,
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
			$id=$tmpTarif->kode;
			$nama = mysql_escape_string($tmpTarif->nama);						
			
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
				
				if($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
					$hrg=$hrgNettDisc ;			
				else
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
										 tujuan CHAR (4) NOT NULL,								 							 
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
		
		if($jnsPasien == '0')//Rawat Jalan 
		{
			if ($this->getViewState('stKhusus') == '0')
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
				
				if($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$hrgTmp += $hrg;
				}
				else
				{
					//$hrgTmp += $hrg + ($hrg * $persenMargin) + ($hrg * $persenProvider); tanpa ppn
					$hrgTmp += ($hrg + ($hrg * $persenMargin) + ($hrg * $persenProvider)) * 1.1;
					//$this->showSql->Text = $hrgTmp;
				}
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
				
				if($kelompokPasien == '08'  && $stAsuransi == '1') //kelompok pasien JPK yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
				{
					$hrgTmp += $this->getViewState('hrg1');
				}
				else
				{
					$hrgTmp += ($this->getViewState('hrg1') + ($this->getViewState('hrg1') * $persenProvider)) * 1.1;
				}
				
			}
		}
		elseif($jnsPasien == '2')//Pasien Luar/ODC
		{
			if ($this->getViewState('stKhusus') == '0')
			{
				$hrgTmp += ($hrg+($hrg * $persenMargin)) * 1.1;
			}else if($this->getViewState('stKhusus') == '1'){
				$hrgTmp = ($this->getViewState('hrg1')) * 1.1;
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
			$hrgTmp = ($hrg) * 1.1;
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
			$st_racik = 0;
			$bungkus_racik = 0;					
			$st_imunisasi = 0;
			$id_kel_imunisasi = 0;				
		}						
		
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
				$totSementaraAwal = $rowCekObat['total_real'];
			}
			
			if($this->getViewState('editStok') == '1')
				$jml = $jml;
			else
				$jml = $jml + $jmlAwal;
			
			$this->clearViewState('editStok');
			$hrg_bulat = $hrgTmp;
			$hrgJual_real = $hrgTmp * $jml;
			$hrgJual_bulat = $hrg_bulat * $jml;
			$total = $hrgJual_real;//$hrgJual_bulat;
			$total = $total + $r_item + $r_racik + $bungkus_racik;
				
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
			$hrg_bulat = $hrgTmp;
			$hrgJual_real = $hrgTmp * $jml;
			$hrgJual_bulat = $hrg_bulat * $jml;
			$total = $hrgJual_real;//$hrgJual_bulat;
			$total = $total + $r_item + $r_racik + $bungkus_racik;
				
			$hrgJual_real = $hrgJual_real + $r_item + $r_racik + $bungkus_racik;						
			if($this->getViewState('totSementara'))
				$totSementara=$this->getViewState('totSementara');
			else
				$totSementara=0;
			
			$totSementara += $total;
		}
		
		
		if($jnsPasien == '0')//Rawat Jalan 
		{
			$this->setViewState('totSementara',$totSementara);
			//$this->totSementara->Text = $this->bulatkan($this->getViewState('totSementara'));
			$stBayar=$this->getViewState('modeBayar');
			
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
			
			$this->queryAction($sql,'C');
			
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik,id_kel_imunisasi, id";
				
			$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGridObat->dataBind();
			
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
			
			$this->AutoComplete->Text = '';
			//$this->kodeObat->Text = '';
			$this->jml->Text = '';
			$this->jmlStok->Text = '';
			//$this->DDObat->SelectedValue = 'empty';
			//$this->DDBhp->SelectedValue = 'empty';
			$this->RBtipeRacik->SelectedValue = '0';
			$this->RBtipeRacik->Enabled = false;
			$this->DDRacik->Enabled = false;
			$this->DDKemasan->Enabled = false;
			$this->DDRacik->SelectedValue = 'empty';
			$this->DDKemasan->SelectedValue = 'empty';
			//$this->clearViewState('stMultiTransJpk');
			
			$this->Page->CallbackClient->focus($this->AutoComplete);
		}
		else
		{
			$this->setViewState('totSementara',$totSementara);
			//$this->totSementara->Text = $this->bulatkan($this->getViewState('totSementara'));
			$stBayar=$this->getViewState('modeBayar');	
			
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
			
			$this->queryAction($sql,'C');
			
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTableObat ORDER BY id_kel_racik,id_kel_imunisasi, id";
				
			$this->UserGridObat->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGridObat->dataBind();
			
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
			
			$this->AutoComplete->Text = '';
			//$this->kodeObat->Text = '';
			$this->jml->Text = '';
			$this->jmlStok->Text = '';
			//$this->DDObat->SelectedValue = 'empty';
			//$this->DDBhp->SelectedValue = 'empty';
			$this->RBtipeRacik->SelectedValue = '0';
			$this->RBtipeRacik->Enabled = false;
			$this->DDRacik->Enabled = false;
			$this->DDKemasan->Enabled = false;
			$this->DDRacik->SelectedValue = 'empty';
			$this->DDKemasan->SelectedValue = 'empty';
			
			$this->Page->CallbackClient->focus($this->AutoComplete);
			
			$this->clearViewState('jmlPaket');
			//$this->clearViewState('stMultiTransJpk');
		}
	}
	
}
?>
