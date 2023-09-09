<?php
class bayarRadRev1 extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function ambilDataTdkRad()
	{
		$sql = "SELECT 
				  tbm_rad_tindakan.kode AS kode,
				  tbm_rad_tindakan.nama AS nama
				FROM
				  tbm_rad_tindakan
				  INNER JOIN tbm_rad_tarif ON (tbm_rad_tindakan.kode = tbm_rad_tarif.id)
				WHERE
				  tbm_rad_tarif.tarif <> '0'";
		
		$this->DDTdkRad->DataSource=LabTdkRecord::finder()->findAllBySql($sql);
		$this->DDTdkRad->dataBind();
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
			$this->DDKlinik->Enabled=false;
			$this->DDKlinik->Focus();
			$this->DDDokter->Enabled=false;
			//$this->notrans->Enabled=false;			
			$this->ambilDataTdkRad();
			//$this->DDTdkRad->DataSource=RadTdkRecord::finder()->findAll();
			//$this->DDTdkRad->dataBind();
			$this->DDTdkRad->Enabled=false;	
		}
    }
	
	public function modeInputChanged($sender, $param)
	{
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0')
		{
			$this->notrans->Enabled = true;
			$this->notrans->focus();
			
			$this->DDKlinik->Enabled=true;
			$this->DDDokter->Enabled=false ;
			
			$this->DDKlinik->Visible=true;
			$this->DDDokter->Visible=true;
			
			$this->jnsBayarInapCtrl->Visible = false;
			$this->jnsPasRujukCtrl->Visible = false;
			
			$this->clearViewState('modeByrInap');
		}
		elseif($jnsPasien == '1')
		{
			$this->notrans->Enabled = true;
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=true;
			
			$this->DDKlinik->Visible=true;
			$this->DDDokter->Visible=true;
			
			//$this->DDUrut->Enabled=true;
			$this->jnsBayarInapCtrl->Visible = true;
			$this->jnsPasRujukCtrl->Visible = false;
			//$this->clearViewState('cariPoli');
		}
		else
		{
			//$this->notrans->Enabled = false;
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=false;
			//$this->DDUrut->Enabled=false;
			//$this->DDKlinik->Visible=true;
			//$this->DDDokter->Visible=true;
			
			$this->jnsBayarInapCtrl->Visible = false;
			$this->jnsPasRujukCtrl->Visible = true;
			//$this->clearViewState('cariPoli');
			//$this->clearViewState('urutBy');
			//$this->clearViewState('cariDokter');
			$this->clearViewState('modeByrInap');
			$this->notrans->Enabled = false;
		}
		
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
		$this->DDDokter->dataBind();
		
		$this->nmDok->visible=false; $this->nmDok->Enabled=false;
		$this->nmPas->visible=false;
		$this->nmPas->Enabled=false;
		
		$this->dokRujukCtrl->Visible=false;	
		$this->nmDok->Text = '';
		$this->nmPas->Text = '';
		$this->notrans->Text = '';
		$this->errMsg->Text = '';
		$this->embel->SelectedIndex = -1;
		
		$this->showFirst->Visible=true;
		$this->showSecond->Visible=false;
		$this->poliCtrl->Visible=false;	
		
		$this->modeByrInapChanged();
	}
	
	public function modeByrInapChanged()
	{
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);		
		$this->clearViewState('modeByrInap');
		$this->setViewState('modeByrInap',$modeByrInap);		
	}		
	
	public function embelChanged()
	{
		//$this->test->text=$this->embel->selectedValue;
		$embel = $this->embel->SelectedValue;		
		$this->setViewState('embel',$embel);
		if($this->embel->SelectedValue=='01')
		{
			$this->dokRujukCtrl->Visible=true;	
			$this->nmDok->Text=''; $this->nmDok->Enabled=true;			
			$this->nmDok->visible=true;
			$this->DDKlinik->focus();
			$this->DDDokter->visible=false;
			//$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			$this->nmPas->Enabled=true;
			
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
			//$this->setViewState('jnsPasLuar','01');
		}elseif($this->embel->SelectedValue=='02')
		{
			$this->dokRujukCtrl->Visible=true;	
			$this->nmDok->Text=''; $this->nmDok->Enabled=false;
			$this->DDDokter->enabled=false;
			//$this->DDKlinik->enabled=false;
			$this->nmPas->visible=true;
			$this->nmPas->Enabled=true;
			$this->DDKlinik->focus();
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=true;
			//$this->setViewState('jnsPasLuar','02');
		}else{
			$this->dokRujukCtrl->Visible=false;	
			$this->nmDok->Visible=false; $this->nmDok->Enabled=false;
			$this->DDDokter->Visible=true;
			//$this->DDKlinik->Enabled=true;
			$this->nmPas->visible=false;
			$this->nmPas->Enabled=false;
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			//$this->clearViewState('jnsPasLuar');
		}
		
	}	
	
	public function checkRegister($sender,$param)
    {
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$tmp = $this->notrans->Text;
		$this->DDFilm->SelectedIndex=-1;
		$this->ambilDataTdkRad();
		if($jnsPasien=='0')
		{	
			if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->notrans->Text,'0','0'))
			{	
				$this->poliCtrl->Visible = true;
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
					$this->showSecond->Visible=true;
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';	
				}
				
				/*
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);							
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				*/
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
						  
				$this->DDKlinik->DataSource=$this->queryAction($sql,'S');
				$this->DDKlinik->dataBind();
				$this->DDKlinik->focus();
				$this->modeInput->Enabled= false;
			}else
			{
				$this->modeInput->Enabled= true;
				$this->poliCtrl->Visible = false;
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Jalan !!';
				$this->notrans->Focus();
			}
			
		}elseif($jnsPasien=='1')
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{
				$this->modeInput->Enabled= false;
				$this->poliCtrl->Visible = true;
					 
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
					$this->showSecond->Visible=true;
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';			
					$this->DDTdkLab->Enabled=true;						
					$this->DDRadKel->Focus();					
				}
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				
				//data u/ DDDokter
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_inap.cm = '$tmp'
					  AND tbt_rawat_inap.status = 0";
						  
				$this->DDDokter->DataSource=$this->queryAction($sql,'S');
				$this->DDDokter->dataBind();
				$this->DDDokter->focus();
				/*
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm ='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);						
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ?',$this->notrans->Text);
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				*/
			}else
			{
				$this->modeInput->Enabled= true;
				$this->poliCtrl->Visible = false;
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Inap !!';
				$this->notrans->Focus();
			}
		}
    }
	
	public function showDokter($sender,$param)
	{
		if(!$this->DDKlinik->SelectedValue=='')
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
				
			//$this->rwtInapCB->Enabled=false;
			/*
			if($this->DDKlinik->SelectedValue=='07')
			{
				
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');
				$this->DDDokter->dataBind();
				$this->DDDokter->Enabled=true;
			}
			else
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);
				$this->DDDokter->dataBind();
				$this->DDDokter->Enabled=true;				
			}
			*/
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
			$this->setViewState('klinik',$klinik);
			$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
		}else{
			$this->batalClicked();
		}
	}
	
	public function showNotrans($sender,$param)
	{		
		//$this->rwtInapCB->Enabled=false;
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		$this->notrans->Enabled=true;
		$this->notrans->focus();		
	}
	
	public function DDFilmChanged($sender,$param)
	{
		$this->setViewState('film',$this->DDFilm->SelectedValue);
		$this->DDTdkRad->Enabled=true;			
		
		$this->ambilDataTdkRad();
		//$this->DDTdkRad->DataSource=RadTdkRecord::finder()->findAll();
		//$this->DDTdkRad->dataBind();
		$this->DDTdkRad->focus();
	}	
		
	public function prosesClicked()
    {		
		$film=$this->getViewState('film');
		switch ($film)
		{
			case '0':
				$nmFilm='18 x 24';
				break;
			case '1':
				$nmFilm='24 x 30';
				break;
			case '2':
				$nmFilm='30 x 40';
				break;
			case '3':
				$nmFilm='35 x 35';
				break;
		}
		
		if (!$this->getViewState('nmTable'))
		{
		$nmTable = $this->setNameTable('nmTable');
		$sql="CREATE TABLE $nmTable (id INT (2) auto_increment,
				 id_film VARCHAR(1) NOT NULL,
				 nm_film VARCHAR(8) NOT NULL,
				 nama VARCHAR(30) NOT NULL,
				 id_tdk VARCHAR(4) NOT NULL,									 
				 total INT(11) NOT NULL,									 							 
				 PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien=='2')
		{			
			$jml1=2000;
			$jml2=2500;
			$sql="INSERT INTO $nmTable (id_film,nm_film,nama,total,id_tdk) VALUES ('-','-','RUJUKAN',$jml1,'RUJ')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			$sql="INSERT INTO $nmTable (id_film,nm_film,nama,total,id_tdk) VALUES ('-','-','PENDAFTARAN',$jml2,'PDT')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
		}
		
		$item=$this->DDTdkRad->SelectedValue;
		
		$sql="SELECT b.nama AS id, 
					 a.tarif AS tarif 
			  		 FROM tbm_rad_tarif a, 
			         	  tbm_rad_tindakan b 
			  		 WHERE a.id='$item' AND a.id=b.kode";
		
		$tmpTarif = RadTarifRecord::finder()->findBySql($sql);				 		
		$total= $tmpTarif->tarif;
		$nama=$tmpTarif->id;	
		$jml=$total+$jml1+$jml2;
		
		$sql="INSERT INTO $nmTable (id_film,nm_film,nama,total,id_tdk) VALUES ('$film','$nmFilm','$nama','$total','$item')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();					
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
			$item=$this->DDTdkRad->SelectedValue;
			$sql="SELECT b.nama AS id, 
					 a.tarif AS tarif 
			  		 FROM tbm_rad_tarif a, 
			         	  tbm_rad_tindakan b 
			  		 WHERE a.id='$item' AND a.id=b.kode";
			$tmpTarif = RadTarifRecord::finder()->findBySql($sql);	
				 		
			$total= $tmpTarif->tarif;
			$nama=$tmpTarif->id;	
			$jml=$total;
			
			$sql="INSERT INTO $nmTable (id_film,nm_film,nama,total,id_tdk) VALUES ('$film','$nmFilm','$nama','$total','$item')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...			

			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->UserGrid->DataSource=$arrData;
            $this->UserGrid->dataBind();										
		}	

		if($this->getViewState('tmpJml')){
			$t = (int)$this->getViewState('tmpJml') + $jml;
			$this->setViewState('tmpJml',$t);
		}else{
			$this->setViewState('tmpJml',$jml);
		}	
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');		
		$this->showBayar->Visible=true;
		
		$this->DDFilm->SelectedIndex = -1;
		$this->ambilDataTdkRad();
		//$this->DDTdkRad->DataSource=RadTdkRecord::finder()->findAll();
		//$this->DDTdkRad->dataBind();
		$this->DDTdkRad->Enabled=false;
		$this->cetakBtn->Enabled = true;
		$this->DDFilm->focus();
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
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
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
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();													
				$this->DDFilm->focus();
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false;
				}
			//}
		//}	
    }	
		
	
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function clearState()
    {
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
		}	
		
		$this->clearViewState('nmTable');//Clear the view state	
					
		$this->UserGrid->DataSource='';
		$this->UserGrid->dataBind();
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('sisa');			
		$this->clearViewState('nmTable');
		$this->clearViewState('cm');
		$this->clearViewState('notrans');
		$this->clearViewState('nama');
		$this->clearViewState('dokter');
		$this->clearViewState('klinik');
		$this->clearViewState('film');
	}
	
	public function cetakClicked($sender,$param)
    {	
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$byrInap=$this->getViewState('modeByrInap');
		
		$jmlTagihan=$this->getViewState('tmpJml');
		$table=$this->getViewState('nmTable');
		$cm=$this->getViewState('cm');
		$id_dokter=$this->getViewState('id_dokter');
		$klinik=$this->getViewState('id_klinik');
		$notransinap=$this->getViewState('notransinap');
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;		
		$sql="SELECT * FROM $table ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...		
		
		if($jnsPasien == '0')
		{
			$notransTmp = $this->numCounter('tbt_rad_penjualan',RadJualRecord::finder(),'07');
			$noRwtJln=$this->getViewState('no_trans_rwtjln');
			
			foreach($arrData as $row)
			{
				$transRwtJln= new RadJualRecord();
				$transRwtJln->no_trans=$notransTmp;
				$transRwtJln->no_trans_rwtjln=$noRwtJln;
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->wkt=date('G:i:s');
				$transRwtJln->film_size=$row['id_film'];
				$transRwtJln->operator=$operator;
				$transRwtJln->klinik=$klinik;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->harga=$row['total'];
				$transRwtJln->flag='0';
				$transRwtJln->Save();
					
			}
		}elseif($jnsPasien == '1')
		{
			$notransTmp1 = $this->numCounter('tbt_rad_penjualan_inap',RadJualInapRecord::finder(),'12');
			foreach($arrData as $row)
			{
				$transRwtJln= new RadJualInapRecord();
				$transRwtJln->no_trans=$notransTmp1;
				$transRwtJln->no_trans_inap=$notransinap;
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->wkt=date('G:i:s');
				$transRwtJln->film_size=$row['id_film'];
				$transRwtJln->operator=$operator;
				//$transRwtJln->klinik=$klinik;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->harga=$row['total'];
				$transRwtJln->flag='0';
				
				if($byrInap=='0')
				{					
					$transRwtJln->st_bayar='0';
				}else{				
					$transRwtJln->st_bayar='1';
				}				
				$transRwtJln->Save();		
			}
		}else{
			$notransTmp = $this->numCounter('tbt_rad_penjualan_lain',LabJualLainRecord::finder(),'15');
			foreach($arrData as $row)
			{
				$transRwtJln= new RadJualLainRecord();
				$transRwtJln->no_trans=$notransTmp;
				$transRwtJln->cm=$this->nmPas->text;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->wkt=date('G:i:s');
				$transRwtJln->film_size=$row['id_film'];
				$transRwtJln->operator=$operator;
				$transRwtJln->dokter=$this->nmDok->text;
				$transRwtJln->harga=$row['total'];
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}	
		}
		$this->clearState();	
		//$this->Response->redirect($this->Service->constructUrl('Lab.bayarLabSukses'));
		$this->MultiView->ActiveViewIndex='1';
	}
	
	public function batalClicked()
    {	
		$this->clearState();
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->clearState();
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	public function viewChanged($sender,$param)
	{
		if($this->MultiView->ActiveViewIndex===1)
        {
            $this->kembaliBtn->Focus();
        }
	}
	public function kembaliClicked()
	{	
		$this->clearState();
		$this->Response->Reload();
	}	
}
?>
