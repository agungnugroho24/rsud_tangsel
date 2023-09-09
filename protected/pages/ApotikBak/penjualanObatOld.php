<?php
class penjualanObat extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		$session=$this->Application->getModule('session');	
		if($session['stCetakPenjualanObat']=='1')
		{
			$session->remove('stCetakPenjualanObat');
			$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObat'));
		}	
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);
		if(!$this->IsPostBack)
		{					
			$this->showSecond->Visible=false;
			$this->showBayar->Visible=false;
			$this->showStok->Visible=false;
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=true;
			$this->DDKlinik->Focus();
			
			$this->DDDokter->Enabled=true;
			
			$this->notrans->Enabled=false;			
			
			$this->DDKateg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDKateg->dataBind();
			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			$this->DDObat->dataBind();
			
			$this->DDObat->Enabled=true;	
			$this->modeByrInap->Enabled=true;
			//$this->tunaiCB->Enabled=false;
		}else{
		
		
		/*
		if($this->getViewState('rwtInapChecked'))
		{
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=true;
			$this->DDObat->Enabled=true;
			$this->embel->Enabled=false;
			$this->tunaiCB->Enabled=true;
			
			if($this->getViewState('idDokter'))
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
				$this->DDDokter->dataBind();
				$this->DDDokter->SelectedValue=$this->getViewState('idDokter');
			}
			else
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
				$this->DDDokter->dataBind();
			}
		}
		else
		{
			if($this->getViewState('klinik'))
			{
				$this->DDDokter->Enabled=true;
			}
			else
			{
				$this->DDDokter->Enabled=false;
				$this->notrans->Enabled=false;
				$this->notrans->Text='';
			}
			
			$this->DDKlinik->Enabled=true;			
		}
		*/
		if($this->getViewState('jnsPasien') == '1')
		{
			$tujuan=$this->getViewState('modeStok');
		}
		else if ($this->getViewState('jnsPasien') == '0')
		{	
			$klinik=$this->DDKlinik->SelectedValue;		
			if ($klinik=='07'){
				$tujuan='5';
			}elseif ($klinik=='17'){
				$tujuan='6';
			}else{
				$tujuan='2';//pasien rawat jalan selain IGD dan OK ambil stok apotik
			}
		}
		else if ($this->getViewState('jnsPasien') == '2')
		{
			$tujuan='2';//Pasien luar ambil stok dari apotik
		}
		$this->clearViewState('tujuan');
		$this->setViewState('tujuan',$tujuan);
		
		$this->test->Text=$tujuan;
		$this->test->Visible=false;//showing tujuan
		
		$tipe = $this->getViewState('tipe');
		$kategori = $this->getViewState('kategori');		
		//$tujuan = $this->getViewState('tujuan');
		//$tujuan = '2';
		$sql=	"SELECT	a.kode AS kode,
						a.nama AS nama 
				FROM 	tbm_obat a, 
						tbt_stok_lain b 
				WHERE a.kode=b.id_obat 
					AND b.jumlah >= 1
					AND b.sumber='01' 
				";					
				
		if($tujuan <> '')			
				$sql .= " AND b.tujuan='$tujuan'  ";
				
		if($kategori <> '')			
				$sql .= " AND a.kategori='$kategori'  ";
				
		if($tipe <> '')			
				$sql .= " AND a.tipe='$tipe'  ";
		
		$sql .= " ORDER BY a.nama ASC "; 		
		//$this->test->text=$sql;
		$arr=$this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;	
			$this->jml->Enabled=true;	
			$this->jml->Text='';	
		}
		else
		{
			//$this->test->text=$sql;
			$this->DDObat->Enabled=false;	
			$this->jml->Enabled=false;	
			$this->jml->Text='';	
		}					
		}	
		//$this->showSql->Text=$sql;	
				
	
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
			
			$this->clearViewState('tmpJml');
			$this->getViewState('sisa');			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';			
			$this->errMsg->Text='';			
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showBayar->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObat',array('goto'=>'1')));
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{			
			$this->notrans->Focus();
		}
    }	
	
	public function modeInputChanged($sender,$param)
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$this->clearViewState('jnsPasien',$jnsPasien);
		$this->clearViewState('tujuan');
		
		$this->setViewState('jnsPasien',$jnsPasien);	
		if($jnsPasien == '0')
		{
			$this->notrans->Enabled = true;
			$this->notrans->focus();
			//$this->DDKlinik->Enabled=true;
			//$this->DDDokter->Enabled=true;
			
			//$this->DDKlinik->Visible=true;
			//$this->DDDokter->Visible=true;
			
			$this->jnsBayarInapCtrl->Visible = false;
			$this->jnsPasRujukCtrl->Visible = false;
			
			$this->showStok->Visible=false;
			
			$this->clearViewState('modeByrInap');
		}
		elseif($jnsPasien == '1')
		{
			$this->notrans->Enabled = true;
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=true;
			
			$this->DDKlinik->Visible=true;
			$this->DDDokter->Visible=true;
			
			$this->showStok->Visible=true;
			$this->modeByrInap->Enabled= true;
			//$this->DDUrut->Enabled=true;
			$this->jnsBayarInapCtrl->Visible = true;
			$this->jnsPasRujukCtrl->Visible = false;
			//$this->clearViewState('cariPoli');
		}
		else
		{
			$this->notrans->Enabled = false;
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=false;
			
			$this->showStok->Visible=false;
			//$this->DDUrut->Enabled=false;
			
			$this->DDKlinik->Visible=true;
			$this->DDDokter->Visible=true;
			
			$this->jnsBayarInapCtrl->Visible = false;
			$this->jnsPasRujukCtrl->Visible = true;
			
			//$this->clearViewState('cariPoli');
			//$this->clearViewState('urutBy');
			//$this->clearViewState('cariDokter');
			$this->clearViewState('modeByrInap');
		}
		
				
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
		$this->DDDokter->dataBind();
		
		$this->dokter->visible=false; $this->dokter->Enabled=false;
		
		$this->nmPas->visible=false;
		$this->nmPas->Enabled=false;
		
		//$this->DDKlinik->enabled=true;
		//$this->DDDokter->visible=true;
		$this->dokter->Text = '';
		$this->nmPas->Text = '';
		$this->notrans->Text = '';
		$this->errMsg->Text = '';
		$this->embel->SelectedIndex = -1;
		
		$this->showFirst->Visible=true;
		$this->showSecond->Visible=false;
	}
	
	public function checkRegister($sender,$param)
    {
        // valid if the username is found in the database
		$tmp = $this->notrans->Text;
		$this->modeByrInap->Enabled=false;
		$tglSkrg = date('Y-m-d');	
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0')//Bila pasien rawat jalan
		{
			if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->notrans->Text,'0','0'))
			{				
				$this->poliCtrl->Visible=true;
				
				$sql = "SELECT 
							tbt_rawat_jalan.cm AS cm,
							tbt_rawat_jalan.no_trans AS no_trans,
							tbd_pasien.nama AS nama
						FROM
							tbt_rawat_jalan
							INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_jalan.cm='$tmp' 
							AND tbt_rawat_jalan.flag='0'
							AND tbt_rawat_jalan.st_alih='0' ";
				$arrData=$this->queryAction($sql,'R');				
				foreach($arrData as $row)
				{
					$this->nama->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);							
					$this->setViewState('no_trans_rwtjln',$row['no_trans']);												
				}
				
				//data u/ DDKlinik
				$sql = "SELECT 
					  tbm_poliklinik.id,
					  tbm_poliklinik.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbt_rawat_jalan.cm = '$tmp' 
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'";
						  
				$this->DDKlinik->Enabled = true;
				$this->DDKlinik->DataSource=$this->queryAction($sql,'S');
				$this->DDKlinik->dataBind();
				$this->DDKlinik->focus();
				
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->modeInput->Enabled= false;
				$this->showSecond->Visible=true;
				$this->DDObat->enabled=true;
				/*
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				
				$this->test3->Visible=false;
				$this->test3->Text=$sql;//showing query
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);							
				$this->setViewState('no_trans_rwtjln',$tmpPasien->alamat);
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;												
				$this->DDObat->Focus();
				*/
				//$this->lock();
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->notrans->Text,'0','1'))
			{
				$this->modeInput->Enabled= true;
				$this->poliCtrl->Visible = false;
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Pasien sudah alih status ke Rawat Inap !';
				$this->notrans->Focus();
			}
			else
			{
				$this->modeInput->Enabled= true;
				$this->poliCtrl->Visible = false;
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Jalan !';
				$this->notrans->Focus();
			}
		}
		elseif($jnsPasien == '1')//Bila pasien rawat inap
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{			
				$this->poliCtrl->Visible=true;
				$this->modeInput->Enabled= false;
				
				$sql = "SELECT 
							tbt_rawat_inap.cm AS cm,
							tbd_pasien.nama AS nama
						FROM
							tbd_pasien 
							INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_inap.cm='$tmp' 
							AND tbt_rawat_inap.status='0' ";
							
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					$this->nama->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);	
				}
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				
				//data u/ DDDokter
				/*$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_inap.cm = '$tmp'
					  AND tbt_rawat_inap.status = 0";*/
				$sql="SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					WHERE
					  tbd_pegawai.kelompok = 1";			  
				$this->DDDokter->DataSource=$this->queryAction($sql,'S');
				//$this->showSql->text=$sql;
				$this->DDDokter->dataBind();
				$this->DDDokter->focus();
				
				/*
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);											
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;												
				$this->DDObat->Focus();
				
				$this->lock();
				*/
			}
			else
			{
				$this->modeInput->Enabled= true;
				$this->poliCtrl->Visible = false;	
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Inap !';
				$this->notrans->Focus();
			}
		}        
    }	
	
	public function showDokter($sender,$param)
	{
		if($this->DDKlinik->SelectedValue!='')
		{
			$tmp = $this->notrans->Text;
				
			$idKlinik = $this->DDKlinik->SelectedValue;
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien=='0')
			{	
				//data u/ DDDokter
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_jalan.cm = '$tmp'
					  AND tbt_rawat_jalan.id_klinik = '$idKlinik'
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'";
			}
			
			$this->DDDokter->DataSource=$this->queryAction($sql,'S');
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled=true;	
			
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
			$this->setViewState('klinik',$klinik);	
			$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
					
			/*
			if ($this->DDKlinik->SelectedValue=='07')
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '08');
				//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			elseif($this->DDKlinik->SelectedValue=='17')
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '03');
				//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			else
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			*/	
		}
		else
		{
			$this->DDDokter->SelectedIndex = -1;
			$this->DDDokter->Enabled = false;
			//$this->batalClicked();
		}		
	}
	
	public function showNotrans($sender,$param)
	{			
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);	
		$this->setViewState('idDokter',$this->DDDokter->SelectedValue);	
		//$this->DDObat->Enabled=true;
		$this->notrans->Enabled=true;
		$this->notrans->focus();	
		
	}
	
	public function checkRegister2($sender,$param)
    {
        // valid if the username is found in the database
		$this->modeByrInap->Enabled=false;
		$tglSkrg = date('Y-m-d');
		
		$this->DDKateg->DataSource=JenisBrgRecord::finder()->findAll();
		$this->DDKateg->dataBind();
		$this->DDObat->enabled=true;	
		
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0')//Bila pasien rawat jalan
		{
			if(PasienRecord::finder()->findByPk($this->notrans->Text))
			{				
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql="SELECT a.cm AS cm, 
							a.no_trans AS alamat, 
							b.nama AS nama
				FROM tbt_rawat_jalan a, 
							tbd_pasien b 
				WHERE a.cm = '$tmp' 
							AND a.tgl_visit = '$dateNow' 
							AND a.cm=b.cm";
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->test3->Visible=false;
				$this->test3->Text=$sql;//showing query
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);							
				$this->setViewState('no_trans_rwtjln',$tmpPasien->alamat);
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;												
				$this->DDObat->Focus();
				
				$this->lock();
			}
			else
			{
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data tidak ada!!';
				$this->notrans->Focus();
			}
		}
		elseif($jnsPasien == '1')//Bila pasien rawat inap
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{			

				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);											
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;												
				$this->DDObat->Focus();
				
				$this->lock();
			}
			else
			{
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data tidak ada!!';
				$this->notrans->Focus();
			}
		}        
    }	
	
	public function embelChanged()
	{
		//$this->test->text=$this->embel->selectedValue;
		$embel = $this->embel->SelectedValue;		
		$this->setViewState('embel',$embel);
		if($this->embel->SelectedValue=='01')
		{
			$this->dokter->Text=''; $this->dokter->Enabled=true;			
			$this->dokter->visible=true;
			$this->dokter->focus();
			$this->DDDokter->visible=false;
			//$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			$this->nmPas->Enabled=true;
			
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
			$this->setViewState('jnsPasLuar','01');
			$this->poliCtrl->Visible=true;
		}elseif($this->embel->SelectedValue=='02')
		{
			$this->dokter->Text=''; $this->dokter->Enabled=false;
			$this->DDDokter->enabled=false;
			//$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			$this->nmPas->Enabled=true;
			$this->nmPas->focus();
			$this->DDKlinik->focus();
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
			$this->setViewState('jnsPasLuar','02');
			$this->poliCtrl->Visible=true;
		}else{
			$this->dokter->Visible=false; $this->dokter->Enabled=false;
			$this->DDDokter->Visible=true;
			//$this->DDKlinik->Enabled=true;
			$this->nmPas->visible=false;
			$this->nmPas->Enabled=false;
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->clearViewState('jnsPasLuar');
			$this->poliCtrl->Visible=false;
		}
		
	}
	
	
	
	public function tunaiCBchanged()
	{
		if($this->tunaiCB->Checked==true)
		{
			$this->setViewState('tunaiChecked','tunaiChecked');
		}
		else
		{
			$this->clearViewState('tunaiChecked');
		}
	}
	
	public function modeByrInapChanged()
	{
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);		
		$this->clearViewState('modeByrInap');
		$this->setViewState('modeByrInap',$modeByrInap);
		
	}	
	
	public function modeStokChanged()
	{
		$modeStok = $this->collectSelectionResult($this->modeStok);		
		$this->clearViewState('modeStok');
		$this->setViewState('modeStok',$modeStok);
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		//$this->DDSumMas->focus();		
	}	
	
	public function chKateg()
	{		
		if($this->DDKateg->SelectedValue=='01')
		{			
			$this->RBtipeObat->focus();
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->SelectedIndex=-1;
			$this->RBtipeObat->Enabled=false;
			//$this->DDSumMas->focus();
		}
		
		$this->DDObat->enabled=true;
		$this->setViewState('kategori',$this->DDKateg->SelectedValue);
		$this->clearViewState('tipe');
	}
	/*
	public function DDSumMasChanged()
	{		
		$sumMas = $this->DDSumMas->SelectedValue;		
		$this->setViewState('sumMas',$sumMas);
		$this->DDObat->Enabled=true;	
	}
	
	public function DDSumSekChanged()
	{
		$sumSek = $this->DDSumSek->SelectedValue;		
		$this->setViewState('sumSek',$sumSek);
		
		$this->DDObat->Enabled=true;
	 
	}*/
	
	public function chObat()
	{
		//$this->test->text=$this->DDObat->SelectedValue;
		$this->jml->Enabled=true;
		$this->jml->focus();
		$this->msgStok->Text='';
		//$this->jml->text=$this->getViewState('tujuan');
	}
	
	public function lock()
    {
		$this->modeInput->enabled=false;
		$this->modeByrInap->enabled=false;
		$this->embel->enabled=false;
		$this->DDKlinik->enabled=false;
		$this->DDDokter->enabled=false;
		$this->DDDokter->enabled=false;
	}
	
	
	public function makeTmpTbl($tujuan)
    {
		$jmlObat = $this->jml->Text;					
		//$tujuan = $tujuan;
		$tujuan=$this->getViewState('tujuan');
		
		//$this->test2->Text=$tujuan;
		//$tmpStok = StokLainRecord::finder()->findById_obat($this->DDObat->SelectedValue)->jumlah;
		$tmpStok = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$this->DDObat->SelectedValue,$tujuan)->jumlah;
		//$this->test->text = $tmpStock;
		
		if($tmpStok >= $jmlObat){			
			$this->showGrid->Visible=true;	
			$this->msgStok->Text='';	
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_obat VARCHAR(5) NOT NULL,									 
											 jml INT(11) NOT NULL,
											 hrg FLOAT(11,2) NOT NULL,
											 hrg_bulat FLOAT(11,2) NOT NULL,
											 total INT(11) NOT NULL,	
											 total_real INT(11) NOT NULL,
											 tujuan CHAR(1) NOT NULL,								 							 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				$jml=$this->jml->Text;
				$tipe = $this->getViewState('tipe');
				$kategori = $this->getViewState('kategori');
				$tujuan = $this->getViewState('tujuan');
				/*
				if($this->getViewState('sumMas') == '2'){	
					$sumber = $this->getViewState('sumMas') . $this->getViewState('sumSek');
				}else{
					$sumber = $this->getViewState('sumMas');
				}*/
				
				$idObat = $this->DDObat->SelectedValue;	
				$jmlObat = $this->jml->Text;
							
				$sql="SELECT a.kode AS kode, 
						 a.nama AS nama, 
						 c.hrg_ppn AS pbf,
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
							AND b.jumlah >= '$jmlObat'";
				//$this->test->Text=$sql;
				$tmpTarif = ObatRecord::finder()->findBySql($sql);	
				//$this->test->text=$sql;			 		
				$id=$tmpTarif->kode;
				$nama=$tmpTarif->nama;				
				$check=HrgObatKhususRecord::finder()->findByPk($idObat)->hrg_jual;
				if ($check)
				{
					$hrg=$check;
				}else{
					$hrg=$tmpTarif->pbf ;				
					$tipe=$tmpTarif->tipe;
					$klinik=$this->getViewState('klinik',$klinik);
					if($klinik=='OK')
					{
						$hrg+=($hrg* 0.22);
					}else{
						if ($tipe=='0')
						{
							$hrg+=($hrg* 0.32);				
						}else{
							$hrg+=($hrg* 0.22);				
						}
					}
				}
				
				$hrg_bulat=$this->bulatkan($hrg);
				$hrgJual_real=round($hrg )* $jml;
				$hrgJual_bulat=round($hrg_bulat) * $jml;
				$total=$this->bulatkan($hrgJual_bulat);
				
				
				//$this->setViewState('total',$total);
				$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,hrg_bulat,total,total_real,tujuan) VALUES ('$id','$nama','$jml','$hrg','$hrg_bulat','$total','$hrgJual_real','$tujuan')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();	
				
				//---------- Tabel Temp u/ tbt_obat_jual_sisa --------
				$sisa=$total-$hrgJual_real;
				$sisaTmpTable = $this->setNameTable('sisaTmpTable');
				$sql="CREATE TABLE $sisaTmpTable (id INT (2) auto_increment, 
											 jumlah FLOAT(11,2) NOT NULL,							 							 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$sql="INSERT INTO $sisaTmpTable (jumlah) VALUES ('$sisa')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				$jml=$this->jml->Text;
				$tipe = $this->getViewState('tipe');
				$kategori = $this->getViewState('kategori');
				$tujuan = $this->getViewState('tujuan');
				/*
				if($this->getViewState('sumMas') == '2'){	
					$sumber = $this->getViewState('sumMas') . $this->getViewState('sumSek');
				}else{
					$sumber = $this->getViewState('sumMas');
				}*/
				
				$idObat = $this->DDObat->SelectedValue;
				$jmlObat = $this->jml->Text;
				//$tujuan = $this->getViewState('tujuan');
				
				
				$sql="SELECT a.kode AS kode, 
						 a.nama AS nama, 
						 c.hrg_ppn AS pbf,
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
							AND b.jumlah >= '$jmlObat'";
										
				//$this->test->Text=$sql;
				$tmpTarif = ObatRecord::finder()->findBySql($sql);					 		
				$id=$tmpTarif->kode;
				$nama=$tmpTarif->nama;
				$check=HrgObatKhususRecord::finder()->findByPk($idObat)->hrg_jual;
				if ($check)
				{
					$hrg=$check;
				}else{
					$hrg=$tmpTarif->pbf ;					
					$tipe=$tmpTarif->tipe;
					$klinik=$this->getViewState('klinik',$klinik);
					if($klinik=='OK')
					{
						$hrg+=($hrg* 0.22);
					}else{
						if ($tipe=='0')
						{
							$hrg+=($hrg* 0.32);				
						}else{
							$hrg+=($hrg* 0.22);				
						}
					}
				}	
				//$hrg=$tmpTarif->pbf ;	
				
				
				$hrg_bulat=$this->bulatkan($hrg);
				$hrgJual_real=round($hrg )* $jml;
				$hrgJual_bulat=round($hrg_bulat) * $jml;
				$total=$this->bulatkan($hrgJual_bulat);
				
				
				//$this->setViewState('total',$total);
				$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,hrg_bulat,total,total_real,tujuan) VALUES ('$id','$nama','$jml','$hrg','$hrg_bulat','$total','$hrgJual_real','$tujuan')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...		
	
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();	
				
				//---------- Tabel Temp u/ tbt_obat_jual_sisa --------
				$sisa=$total-$hrgJual_real;
				$sisaTmpTable = $this->getViewState('sisaTmpTable');
				
				$sql="INSERT INTO $sisaTmpTable (jumlah) VALUES ('$sisa')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...							
			}	
			
			$this->cetakBtn->Enabled = true;
			
			if($this->getViewState('total')){
				$t = (int)$this->getViewState('total') + $total;
				$this->setViewState('total',$t);
			}else{
				$this->setViewState('total',$total);
			}	
						
			$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
			
			$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
			//if($this->getViewState('modeByrInap')){
				if($modeByrInap == 1)
				{
					$this->showBayar->Visible=false;					
				}
				else
				{
					$this->showBayar->Visible=true;
					$this->cetakBtn->Enabled = false;	
				}
			//}
			
			$this->DDObat->Focus();	
			
			if($this->getViewState('jnsPasLuar'))
			{
				$this->dokter->Text = $this->getViewState('nmDokter') ;
				$this->nmPas->Text =$this->getViewState('nmPasien');
			}
		}
		else
		{
			if(!$this->getViewState('nmTable'))
			{
				$this->showGrid->Visible=false;				
			}
			$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			//$this->msgStok->Text=$tmpStok;
		}	
	}
	
				
	public function prosesClicked($sender,$param)
    {
		$jnsPasienLuar = $this->getViewState('jnsPasLuar');
		$this->test->Text = $jnsPasien;
								
		if($jnsPasienLuar=='01') //jika jenis pasien luar = Rujukan
		{	
			$this->setViewState('nmDokter',$this->dokter->Text);
			$this->setViewState('nmPasien',$this->nmPas->Text);			
				
		}
		elseif($jnsPasienLuar=='02') //jika jenis pasien luar = beli sendiri
		{
			$this->setViewState('nmDokter',$this->dokter->Text);
			$this->setViewState('nmPasien',$this->nmPas->Text);				
		}					
		$tujuan = $this->getViewState('tujuan');
		$this->makeTmpTbl($tujuan);
		$this->cetakBtn->Enabled = true;	
	}
	
	public function bayarClicked($sender,$param)
    {
		$this->setViewState('stBayar','1');
		
		if($this->bayar->Text >= $this->getViewState('total'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('total'));
			$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
			$this->setViewState('sisa',$hitung);
			
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->Focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
			$this->bayarBtn->Enabled=false;	
			$this->bayar->Enabled=false;	
			$this->DDKateg->Enabled=false;
			$this->RBtipeObat->Enabled=false;
			$this->DDObat->Enabled=false;
			$this->jml->Enabled=false;
			$this->tambahBtn->Enabled=false;
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->Focus();
			$this->cetakBtn->Enabled=false;
			$this->bayarBtn->Enabled=true;	
			$this->bayar->Enabled=true;
			$this->DDKateg->Enabled=true;
			$this->RBtipeObat->Enabled=true;
			$this->DDObat->Enabled=true;
			$this->jml->Enabled=true;
			$this->tambahBtn->Enabled=true;
		}
	}
	
	 public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT total FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['total'];					
					$t = ($this->getViewState('total') - $n);						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('total',$t);
				}
				
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
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();													
				$this->DDObat->focus();
			//}	
			
		//}	
    }	
	
	public function batalClicked()
    {		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('total');
		$this->clearViewState('modeByrInap');
		$this->clearViewState('nmDokter');
		$this->clearViewState('nmPasien');
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		//$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->notrans->Focus();
		$this->showSecond->Visible=false;
		$this->showBayar->Visible=false;
		
		$this->Response->Reload();
	}
	
	
	
	
	public function checkRM($sender,$param)
    {   		
		$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function cetakClicked($sender,$param)
    {	
		$jmlTagihan=$this->getViewState('total');
		$table=$this->getViewState('nmTable');
		$cm=$this->getViewState('cm');				
		$sumber='01';
		$tujuan = $this->getViewState('tujuan');		
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;	
				
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //Pasien Rawat Jalan
		{		
			$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND sumber = ? AND jumlah >= ?',$row['tujuan'],$row['id_obat'],$sumber,$row['jml']))
				{	
					$ObatJual= new ObatJualRecord();
					$ObatJual->no_trans=$notransTmp;
					$ObatJual->no_trans_rwtjln=$this->getViewState('no_trans_rwtjln');
					$ObatJual->cm=$cm;
					$ObatJual->dokter=$this->getViewState('idDokter');
					//$transRwtJln->sumber=$sumber;
					$ObatJual->sumber='01';
					$ObatJual->tujuan=$row['tujuan'];
					$ObatJual->klinik=$this->DDKlinik->SelectedValue;
					$ObatJual->id_obat=$row['id_obat'];
					$ObatJual->tgl=date('y-m-d');
					$ObatJual->wkt=date('G:i:s');
					$ObatJual->operator=$operator;
					$ObatJual->hrg=$row['hrg'];
					$ObatJual->jumlah=$row['jml'];
					$ObatJual->total=$row['total'];
					$ObatJual->total_real=$row['total_real'];
					$ObatJual->flag='0';
					$ObatJual->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
				}	
			}	
		}
		elseif($jnsPasien == '1') //Pasien Rawat Inap
		{			
			$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
			$notransTmp = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
			$notrans_inap = RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0')->no_trans;
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				if($stok=StokLainRecord::finder()->find('id_obat = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$sumber,$row['jml'],$row['tujuan']))
				{	
					$ObatJualInap= new ObatJualInapRecord();
					$ObatJualInap->no_trans=$notransTmp;
					$ObatJualInap->no_trans_inap=$notrans_inap;					
					$ObatJualInap->cm=$cm;
					$ObatJualInap->dokter=$this->getViewState('idDokter');
					//$ObatJualInap->sumber=$sumber;
					$ObatJualInap->sumber='01';
					$ObatJualInap->tujuan=$row['tujuan'];
					$ObatJualInap->id_obat=$row['id_obat'];
					$ObatJualInap->tgl=date('y-m-d');
					$ObatJualInap->wkt=date('G:i:s');
					$ObatJualInap->operator=$operator;
					$ObatJualInap->hrg=$row['hrg'];
					$ObatJualInap->jumlah=$row['jml'];
					$ObatJualInap->total=$row['total'];
					$ObatJualInap->total_real=$row['total_real'];
					$ObatJualInap->flag='0';
					
					
					if($modeByrInap == '0') //Pembayaran Kredit
					{
						$ObatJualInap->st_bayar='0';
					}elseif($modeByrInap == '1') //Pembayaran Tunai
					{
						$ObatJualInap->st_bayar='1';
						//key '04' adalah konstanta modul untuk Apotik Sentral
						//$notrans_inap=RwtInapRecord::finder()->find('cm = ? ',$this->notrans->Text)->no_trans;
					}		
					
					$ObatJualInap->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
				}	
			}	
			
			
		}
		else //Pasien Rawat Lain
		{	
			$tujuan ='2';//Ambil stok dari apotik langsung!
			$notransTmp = $this->numCounter('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
			$nmPasien = $this->nmPas->Text;
			$nmDokter = $this->dokter->Text;
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				if($stok=StokLainRecord::finder()->find('id_obat = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$sumber,$row['jml'],'2'))
				{
					$ObatJualLain= new ObatJualLainRecord();
					$ObatJualLain->no_trans=$notransTmp;
					$ObatJualLain->cm=$this->getViewState('nmPasien');
					$ObatJualLain->dokter=$this->getViewState('nmDokter');
					//$ObatJualLain->sumber=$sumber;
					$ObatJualLain->sumber='01';
					$ObatJualLain->tujuan='2';
					$ObatJualLain->id_obat=$row['id_obat'];
					$ObatJualLain->tgl=date('y-m-d');
					$ObatJualLain->wkt=date('G:i:s');
					$ObatJualLain->operator=$operator;
					$ObatJualLain->hrg=$row['hrg'];
					$ObatJualLain->jumlah=$row['jml'];
					$ObatJualLain->total=$row['total'];
					$ObatJualLain->total_real=$row['total_real'];
					$ObatJualLain->flag='0';
					$ObatJualLain->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
				}	
			}
		}		
		
		//-------- Insert Harga Sisa Pembulatan ke tbt_obat_jual_sisa -----------------
		$sisaTmpTable = $this->getViewState('sisaTmpTable');
		$sql="SELECT * FROM $sisaTmpTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$ObatJualSisa= new ObatJualSisaRecord();
			$ObatJualSisa->no_trans=$notransTmp;
			$ObatJualSisa->jumlah=$row['jumlah'];
			$ObatJualSisa->tgl=date('y-m-d');
			$ObatJualSisa->Save();	
		}
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		
		//Register cookies u/ status cetak
		$session=$this->Application->getModule('session');		
		$session['stCetakPenjualanObat'] = '1';
		
		if($jnsPasien == '0') //Pasien Rawat Jalan
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBaru',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
		}
		elseif($jnsPasien == '1') //Pasien Rawat Inap
		{
			if($modeByrInap == '0') //Pembayaran Kredit
			{
				$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObatBaruSukses'));
			}
			elseif($modeByrInap == '1') //Pembayaran Tunai
			{
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBaru',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));				
			}	
		}
		else //Pasien Rawat Lain
		{	
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBaru',array('notrans'=>$notransTmp,'cm'=>$this->getViewState('nmPasien'),'dokter'=>$this->getViewState('nmDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
		}
			//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
			//$this->test->text='ada';
				
	}
}
?>
