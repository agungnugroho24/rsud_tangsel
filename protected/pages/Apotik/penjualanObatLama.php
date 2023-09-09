<?php
class penjualanObatLama extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->showSecond->Visible=false;
			$this->showBayar->Visible=false;
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=true;
			$this->DDKlinik->Focus();
			
			$this->DDDokter->Enabled=false;
			
			$this->notrans->Enabled=false;			
			
			$this->DDKateg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDKateg->dataBind();
			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			$this->DDObat->dataBind();
			
			$this->DDObat->Enabled=true;	
			$this->rwtInapCB->Enabled=true;
			$this->tunaiCB->Enabled=false;
			
		}
		
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
			
		$tipe = $this->getViewState('tipe');
		$kategori = $this->getViewState('kategori');
		//$tujuan = $this->getViewState('tujuan');
		//$tujuan = '2';
		$sql=	"SELECT	a.kode AS kode,
						a.nama AS nama 
				FROM 	tbm_obat a, 
						tbt_stok_lain b 
				WHERE a.kode=b.id_obat 
					AND b.tujuan='2'
					AND b.jumlah >= 1
					AND b.sumber='01' ";					
		
		if($kategori <> '')			
				$sql .= "AND a.kategori='$kategori'  ";
				
		if($tipe <> '')			
				$sql .= "AND a.tipe='$tipe'  ";
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
	
	public function showDokter($sender,$param)
	{
		if ($this->DDKlinik->SelectedValue=='07')
		{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '08');
			//kelompok pegawai '1' adalah untuk dokter
		$this->DDDokter->dataBind();
		}elseif($this->DDKlinik->SelectedValue=='17'){
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '03');
			//kelompok pegawai '1' adalah untuk dokter
		$this->DDDokter->dataBind();
		}else{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
		$this->DDDokter->dataBind();
		}
		
		$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
		$this->setViewState('klinik',$klinik);
		
		$tujuan=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->id;
		if ($tujuan=='07'){
			$tujuan=5;
		}elseif ($tujuan=='17'){
			$tujuan=6;
		}else{
			$tujuan=2;
		}
		$this->setViewState('tujuan',$tujuan);
		//$this->test->text=$tujuan;
		
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
	
	public function rwtInapCBchanged()
	{
		if($this->rwtInapCB->Checked==true)
		{
			$this->setViewState('rwtInapChecked','rwtInapChecked');
		}
		else
		{
			$this->clearViewState('rwtInapChecked');
		}		
	}	
	
	public function embelChanged()
	{
		//$this->test->text=$this->embel->selectedValue;
		$embel = $this->embel->SelectedValue;		
		$this->setViewState('embel',$embel);
		if($this->embel->SelectedValue=='01')
		{
			$this->dokter->visible=true;
			$this->DDDokter->visible=false;
			$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
		}elseif($this->embel->SelectedValue=='02')
		{
			$this->dokter->visible=false;
			$this->DDDokter->enabled=false;
			$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
		}else{
			$this->dokter->Visible=false;
			$this->DDDokter->Visible=true;
			$this->DDKlinik->Enabled=true;
			$this->nmPas->visible=false;
			
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
		}
		
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
		
		$this->setViewState('kategori',$this->DDKateg->SelectedValue);
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
		//$this->jml->text=$this->getViewState('tujuan');
	}
	
	public function showNotrans($sender,$param)
	{				
		$this->setViewState('idDokter',$this->DDDokter->SelectedValue);				
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);	
		$this->setViewState('idDokter',$this->DDDokter->SelectedValue);	
		//$this->DDObat->Enabled=true;
		$this->notrans->Enabled=true;
		$this->notrans->focus();		
	}
	
	public function bulatkan($nilai)
	{		
		$jmlTxt=strlen($nilai);
		$txt01=substr($nilai,0,($jmlTxt-2));
		$txt02=substr($nilai,-2,2);
		
		if($txt02>50)
		{
			$hasil=($txt01+1).'00';		
		}
		elseif($txt02<50)
		{
			$hasil=($txt01).'00';
		}
		else
		{
			$hasil=$nilai;
		}	
		
		return $hasil;	
	}
				
	public function prosesClicked($sender,$param)
    {
		$jmlObat = $this->jml->Text;	
		//$tujuan = $this->getViewState('tujuan');			
		$tujuan='2';
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
											 total INT(11) NOT NULL,								 							 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				$jml=$this->jml->Text;
				$tipe = $this->getViewState('tipe');
				$kategori = $this->getViewState('kategori');
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
				$this->test->Text=$sql;
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
				$total=floor($hrg * $jml);
				$total=$this->bulatkan($total);
				
				//$this->setViewState('total',$total);
				$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,total) VALUES ('$id','$nama','$jml','$hrg','$total')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();						
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				$jml=$this->jml->Text;
				$tipe = $this->getViewState('tipe');
				$kategori = $this->getViewState('kategori');
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
						  tbt_obat_harga c
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
				}	
				//$hrg=$tmpTarif->pbf ;	
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
				
				$total=floor($hrg * $jml);
				$total=$this->bulatkan($total);
				
				//$this->setViewState('total',$total);
				$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,total) VALUES ('$id','$nama','$jml','$hrg','$total')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
	
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();								
			}	
			
			if($this->getViewState('total')){
				$t = (int)$this->getViewState('total') + $total;
				$this->setViewState('total',$t);
			}else{
				$this->setViewState('total',$total);
			}	
						
			$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
			
			$this->showBayar->Visible=true;
			$this->DDObat->Focus();	
		}
		else
		{
			if(!$this->getViewState('nmTable'))
			{
				$this->showGrid->Visible=false;
				$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			}
			//$this->msgStok->Text=$tmpStok;
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
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();													
				$this->DDObat->focus();
			//}	
			
		//}	
    }	
	
	public function batalClicked($sender,$param)
    {		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('total');
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		//$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->notrans->Focus();
		$this->showSecond->Visible=false;
		$this->showBayar->Visible=false;
		
		$this->Response->Reload();
	}
		
	public function checkRegister($sender,$param)
    {
        // valid if the username is found in the database
		$this->rwtInapCB->Enabled=false;
		$tglSkrg = date('Y-m-d');
		
		$this->DDKateg->DataSource=JenisBrgRecord::finder()->findAll();
		$this->DDKateg->dataBind();
		$this->DDObat->enabled=true;	
		if($this->getViewState('rwtInapChecked'))
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
			}
			else
			{
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data tidak ada!!';
				$this->notrans->Focus();
			}
		}
		else
		{
			if(PasienRecord::finder()->findByPk($this->notrans->Text))
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
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function cetakClicked($sender,$param)
    {	
		$embel = $this->getViewState('embel');
		$tujuan = $this->getViewState('tujuan');
		if ($embel=='')
		{			
			if($this->getViewState('rwtInapChecked'))
			{
				if($this->getViewState('tunaiChecked'))
				{
					$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
				}else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
					//key '04' adalah konstanta modul untuk Apotik Sentral
					$notrans_inap=RwtInapRecord::finder()->find('cm = ? ',$this->notrans->Text)->no_trans;
				}				
			}
			else
			{
				$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
				//key '04' adalah konstanta modul untuk Apotik Sentral
			}
			
			$jmlTagihan=$this->getViewState('total');
			$table=$this->getViewState('nmTable');
			$cm=$this->getViewState('cm');		
			$nama=$this->getViewState('nama');
			$dokter=$this->getViewState('dokter');
			$klinik=$this->DDKlinik->SelectedValue;
			$operator=$this->User->IsUserNip;
			$nipTmp=$this->User->IsUserNip;	
			/*	
			if($this->getViewState('sumMas') == '2'){	
				$sumber = $this->getViewState('sumMas') . $this->getViewState('sumSek');
			}else{
				$sumber = $this->getViewState('sumMas');
			}
			*/
			
			$sumber='01';
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND sumber = ? AND jumlah >= ?',$tujuan,$row['id_obat'],$sumber,$row['jml']))
				{	/*					
					if($embel == '01' OR $embel == '02') 
					{
						
					}
					else
					{*/
						if($this->getViewState('rwtInapChecked'))
						{
							if($this->getViewState('tunaiChecked'))
							{
								$transRwtJln= new ObatJualRecord();
								$transRwtJln->klinik=$klinik;	
							}else{
								$transRwtJln= new ObatJualInapRecord();
								$transRwtJln->no_trans_inap=$notrans_inap;
							}
						}
						else
						{
							$transRwtJln= new ObatJualLainRecord();
							$transRwtJln->klinik=$klinik;
						}
					//}
					
					//$transRwtJln= new ObatJualRecord();
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->cm=$cm;
					$transRwtJln->dokter=$this->getViewState('idDokter');
					//$transRwtJln->sumber=$sumber;
					$transRwtJln->sumber='01';
					$transRwtJln->tujuan='2';
					$transRwtJln->id_obat=$row['id_obat'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					$transRwtJln->operator=$operator;
					$transRwtJln->hrg=$row['hrg'];
					$transRwtJln->jumlah=$row['jml'];
					$transRwtJln->total=$row['total'];
					$transRwtJln->flag='0';
					$transRwtJln->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
				}	
			}						
			
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
			//$this->test->text='ada';
		}else
		{
			$notransTmp = $this->numCounter('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
			
			$jmlTagihan=$this->getViewState('total');
			$table=$this->getViewState('nmTable');
			$klinik=$this->DDKlinik->SelectedValue;
			$operator=$this->User->IsUserNip;
			$nipTmp=$this->User->IsUserNip;	
			
			$sumber='01';
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND sumber = ? AND jumlah >= ?',$tujuan,$row['id_obat'],$sumber,$row['jml']))
				{
					if($this->getViewState('rwtInapChecked'))
					{
						$transRwtJln= new ObatJualInapRecord();
						$transRwtJln->no_trans_inap=$notrans_inap;
					}
					else
					{
						$transRwtJln= new ObatJualRecord();
						$transRwtJln->klinik=$klinik;
					}	
					
					//$transRwtJln= new ObatJualLainRecord();
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->cm=$this->nmPas->Text;
					$transRwtJln->dokter=$this->dokter->Text;
					//$transRwtJln->sumber=$sumber;
					$transRwtJln->sumber='01';
					$transRwtJln->tujuan='$tujuan';
					$transRwtJln->id_obat=$row['id_obat'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					$transRwtJln->operator=$operator;
					$transRwtJln->hrg=$row['hrg'];
					$transRwtJln->jumlah=$row['jml'];
					$transRwtJln->total=$row['total'];
					$transRwtJln->flag='0';
					$transRwtJln->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jml'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
				}	
			}				
			
			//$this->test->text='ga ada';
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$this->nmPas->Text,'dokter'=>$this->dokter->Text,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
		}			
	}
}
?>
