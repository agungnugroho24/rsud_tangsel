<?php
class AdminRwtJlnBaru extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('11');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->RBjns->Visible=false;
			$this->RBjns->focus();
			$this->RBjns->SelectedValue=1;
			$this->RBjnsChanged($sender,$param);
			
			$this->karcisCtrl->Visible=false;
			
			$this->tdkPanel->Visible=false;
			$this->pdftrPanel->Visible=false;
			
			$this->showSecond->Visible=false;
			$this->showSecondPdftr->Visible=false;
			
			$this->showBayar->Visible=false;
			$this->dtGridCtrl->Visible=false;
			$this->showBayarPdftr->Visible=false;
			
			$this->cetakBtn->Enabled=false;
			$this->cetakBtn->Visible=false;
			
			//$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			//$this->DDKlinik->dataBind();
			
			$this->DDBhp->DataSource=$this->bhpData(2500,20);
			$this->DDBhp->dataBind();
			
			$this->nmTindakan->Text='';
			$this->notrans->focus();
					
		}		
		
		
    }
	
	public function checkRegister($sender,$param)
    {
		$cm=$this->notrans->Text;
		
		if(RwtjlnRecord::finder()->findAll('cm = ? AND flag=0 AND st_alih=0',$cm)) 			
		{
			$this->poliCtrl->Visible = true;
			
			$sql = "SELECT 
					  tbm_poliklinik.id,
					  tbm_poliklinik.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbt_rawat_jalan.cm = '$cm'
					  AND tbt_rawat_jalan.flag = 0
					  AND tbt_rawat_jalan.st_alih = 0";
					  
			$this->DDKlinik->DataSource=$this->queryAction($sql,'S');
			$this->DDKlinik->dataBind();
			$this->DDKlinik->focus();
			
			$this->errMsg->Text='';
		}
		else
		{
			$this->poliCtrl->Visible=false;
			$this->showFirst->Visible=true;
			$this->tdkPanel->Visible=false;
			$this->showSecond->Visible=false;
				
			$this->errMsg->Text='Pasien dengan No. Register '.$cm.' tidak ada !';
			$this->notrans->Text = '';
			$this->notrans->focus();
		}
	}
	
	public function DDKlinikChanged($sender,$param)
	{
		$this->DDDokter->Enabled = true;
		$this->DDDokter->focus();
		$cm=$this->notrans->Text;
		$idKlinik = $this->DDKlinik->SelectedValue;
		
		if($idKlinik == '07'){
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				   tbd_pegawai,	
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  tbd_pegawai.kelompok = 1 AND
				  tbt_rawat_jalan.dokter = tbd_pegawai.id AND
				  tbt_rawat_jalan.id_klinik = '07' ";
		}else{
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				  INNER JOIN tbd_pegawai ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
				  AND (tbt_rawat_jalan.dokter = tbd_pegawai.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  tbd_pegawai.kelompok = 1 AND 
				  tbm_poliklinik.id = '$idKlinik'";
		}
			
		$this->DDDokter->DataSource=$this->queryAction($sql,'S');
		$this->DDDokter->dataBind();
	}
	
	public function DDdokterChanged($sender,$param)
    {
		$tglSkrg = date('Y-m-d');
		$cek=$this->notrans->Text;
		$idKlinik=$this->DDKlinik->SelectedValue;
		
		if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
		{
			
			$this->tdkPanel->Visible=true;
			$this->pdftrPanel->Visible=false;
			$this->cetakBtn->Visible=true;
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT b.nama AS cm,						   
							   b.cm AS cr_masuk, 
							   a.no_trans AS no_trans,
								c.nama AS wkt_visit,
								d.nama AS tgl_visit,
							   a.flag
							   FROM tbt_rawat_jalan a, 
									tbd_pasien b,
									tbd_pegawai c,
									tbm_poliklinik d
							   WHERE a.cm='$tmp'
									 AND a.cm=b.cm
									AND a.dokter=c.id
									AND a.id_klinik=d.id
									 AND a.flag='0'
									 AND a.st_alih='0'";		 
				$tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->cm;			
				$this->dokter->Text= $tmpPasien->wkt_visit;
				$this->klinik->Text= $tmpPasien->tgl_visit;
				$this->setViewState('cm',$tmpPasien->cr_masuk);
				$this->setViewState('nama',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->wkt_visit);
				$this->setViewState('klinik',$tmpPasien->tgl_visit);
				$this->setViewState('transaksi',$tmpPasien->no_trans);			
				$this->setViewState('notrans',$this->notrans->Text);
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				//$this->id_tindakan->Enabled=true;				
				$this->DDTindakan->Enabled=true;
				$sql="SELECT 
						  tbm_nama_tindakan.id,
						  tbm_nama_tindakan.nama
						FROM
						  tbm_tarif_tindakan
						  INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
						WHERE
						  (tbm_tarif_tindakan.biaya1 + tbm_tarif_tindakan.biaya2 + tbm_tarif_tindakan.biaya3 + tbm_tarif_tindakan.biaya4) > 0
						ORDER BY
						  nama ASC";				
				//$this->DDTindakan->DataSource=NamaTindakanRecord::finder()->findAll();
				$this->DDTindakan->DataSource=NamaTindakanRecord::finder()->findAllBySql($sql);
				$this->DDTindakan->dataBind();
				$this->DDBhp->Enabled=true;
				$this->tambahBtn->Enabled=true;
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				$this->DDKlinik->Enabled=false;
				$this->DDDokter->Enabled=false;
				$this->RBjns->Enabled=false;
		}
		else //jika RadioButton Pendaftaran yang dipilih
		{
			$this->tdkPanel->Visible=false;
			$this->pdftrPanel->Visible=true;
			$this->cetakBtn->Visible=true;
			if(PasienRecord::finder()->findAll('cm = ?',$cek)) //jika pasien ditemukan
			{			
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT b.nama AS cm,						   
					   b.cm AS cr_masuk, 
					   a.no_trans AS no_trans,
					   a.flag
					   FROM tbt_rawat_jalan a, 
							tbd_pasien b							
					   WHERE a.cm='$tmp'
						 AND a.cm=b.cm
						 AND a.flag='0'";					 
				$tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
				$this->namaPdftr->Text= $tmpPasien->cm;			
				$this->setViewState('cm',$tmpPasien->cr_masuk);
				$this->setViewState('nama',$tmpPasien->cm);
				$this->setViewState('transaksi',$tmpPasien->no_trans);			
				$this->setViewState('notrans',$this->notrans->Text);
				$this->showSecondPdftr->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				$this->DDKlinik->Enabled=false;
				$this->DDDokter->Enabled=false;
				$this->noKarcisPdftr->Enabled=false;
				$this->RBjns->Enabled=false;
				$this->DDjnsPdftr->focus();
			}
			else //jika pasien tidak ditemukan
			{
				$this->showFirst->Visible=true;
				$this->pdftrPanel->Visible=false;
				$this->showSecondPdftr->Visible=false;
				$this->errMsg->Text='No. Register tidak ada!!';
				$this->notrans->focus();
			}
		}
    }	
	
	public function ChangedDDBhp($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDBhp);
		if ($pilih=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('jmlBhp');
		}			
		else
		{
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('jmlBhp');
			//$this->setViewState('idBulan',$pilih);
			
			$jmlBhp = $pilih;
			$this->setViewState('jmlBhp',$jmlBhp);
		}	
	}
	
	public function prosesClicked()
    {	
		$this->nmTindakan->Text='';
			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_tdk VARCHAR(4) NOT NULL,
										 bhp INT(11) NOT NULL,
										 total INT(11) NOT NULL,
										 jml INT(11) NOT NULL, 									 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			/*
			$klinik=$this->getViewState('id_klinik');
			if ($klinik=='05')
			{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=20000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Umum','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}else{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=50000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Spesialis','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml3=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Rumah Sakit','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml4=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('USG','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}
			*/			
			//$item=$this->id_tindakan->Text;
			$item=$this->DDTindakan->SelectedValue;
			$sql="SELECT b.nama AS nama, 
						 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
						 FROM tbm_tarif_tindakan a, 
							  tbm_nama_tindakan b 
						 WHERE a.id='$item' AND a.id=b.id";
			$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
			$bhp=$this->getViewState('jmlBhp');			
			if($this->citoCheck->Checked==false)
			{
				$total= $tmpTarif->total;
			}
			else
			{
				$total= 2 * $tmpTarif->total;
			}
			
			$nama=$tmpTarif->nama;	
			//$tot=$jml1+$jml2+$jml3+$jml4;
			$jml=$total+$bhp;
			
			$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('$nama','$bhp','$total','$jml','$item')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
			$this->bayar->Enabled=true;
			$this->bayarBtn->Enabled=true;						
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
			//$item=$this->id_tindakan->Text;
			$item=$this->DDTindakan->SelectedValue;
			$sql="SELECT b.nama AS nama, 
						 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
						 FROM tbm_tarif_tindakan a, 
							  tbm_nama_tindakan b 
						 WHERE a.id='$item' AND a.id=b.id";
			$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
			$bhp=$this->getViewState('jmlBhp');
			//$total= $tmpTarif->total;
			
			if($this->citoCheck->Checked==false)
			{
				$total= $tmpTarif->total;
			}
			else
			{
				$total= 2 * $tmpTarif->total;
			}
		
			$nama=$tmpTarif->nama;
			$jml=$total+$bhp;
			
			$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('$nama','$bhp','$total','$jml','$item')";
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
			$this->setViewState('tmpJml',$jml+$tot);
		}	
		
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		$this->DDBhp->SelectedIndex='0';
		$this->showBayar->Visible=true;
$this->dtGridCtrl->Visible=true;
		//$this->id_tindakan->focus();
		$this->DDTindakan->focus();
		$this->citoCheck->Checked=false;
		
		$this->cetakBtn->Enabled=true;
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
				
				$sql="SELECT jml FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['jml'];					
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');	
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();		
				$this->bayar->Text='';											
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				
				if(count($arrData)==0)
				{
					$this->clearViewState('tmpJml');
					$this->cetakBtn->Enabled=false;
					$this->showBayar->Visible=false;
$this->dtGridCtrl->Visible=false;
					//$this->id_tindakan->Enabled=true;
					$this->DDTindakan->Enabled=true;
					$this->DDBhp->Enabled=true;
					$this->sisaByr->Text='';
				}
			//}	
			
		//}	
    }
	
	public function bayarClicked($sender,$param)
    {
		//$this->showSql->text='-'.$this->getViewState('transaksi');	
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text='Rp ' . $hitung;
			$this->setViewState('sisa',$hitung);
			//$this->id_tindakan->Enabled=false;
			$this->DDTindakan->Enabled=false;
			$this->DDBhp->Enabled=false;
			$this->tambahBtn->Enabled=false;
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->focus();
			$this->cetakBtn->Enabled=false;
		}
	}
	
	public function bayarClickedPdftr($sender,$param)
    {
		if($this->bayarBtnPdftr->Text=='Refresh')
		{
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$this->getViewState('transaksi');
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->notrans->Text);
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('id_klinik');
			$this->clearViewState('id_dokter');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
				
			$this->batalClicked();
		}
		else
		{			
			if($this->bayarPdftr->Text >= $this->getViewState('jmlBayarPdftr'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayarPdftr->Text)-TPropertyValue::ensureFloat($this->getViewState('jmlBayarPdftr'));
				$this->sisaByrPdftr->Text='Rp ' . number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->DDjnsPdftr->Enabled=true;			
				$this->errByrPdftr->Text='';		
			
				$this->cetakBtn->Enabled=true;
				
				$this->DDjnsPdftr->Enabled=false;
				$this->bayarPdftr->Enabled=false;
				$this->bayarBtnPdftr->Text='Refresh';
			}
			else
			{
				$this->errByrPdftr->Text='Jumlah pembayaran kurang';	
				$this->bayarPdftr->focus();
			}
		}		
	}

	
	public function showNotrans($sender,$param)
	{					
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		$this->notrans->Enabled=true;
		$this->notrans->focus();		
	}
	
	
	public function RBjnsChanged($sender,$param)
    {
				
		if($this->RBjns->SelectedValue==1)
		{
			$this->noKarcisPdftr->Text='';
			$this->karcisCtrl->Visible=false;
			
			$this->DDKlinik->Enabled=true;
			$this->DDKlinik->focus();
		}
		elseif($this->RBjns->SelectedValue==2)
		{
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=false;
		
			$this->karcisCtrl->Visible=true;
			$this->noKarcisPdftr->Enabled=true;
			$this->noKarcisPdftr->focus();
			
			//$this->DDKlinik->Enabled=false;
		}
		else
		{
			$this->karcisCtrl->Visible=false;
			$this->DDKlinik->Enabled=false;
		}
	}
	
	public function noKarcisPdftrChanged($sender,$param)
    {   
		if(strlen($this->noKarcisPdftr->Text)!=8)
		{
			$this->errMsgNoKarcis->Text='No. karcis harus 8 digit!';	
		}
		else
		{
			if(KasirPendaftaranRecord::finder()->findAll('no_karcis = ?',$this->noKarcisPdftr->Text)) //jika no_karcis ditemukan
			{	
				$this->errMsgNoKarcis->Text='No. karcis sudah ada!!';
				$this->noKarcisPdftr->focus();
			}
			else //jika no_karcis tidak ditemukan
			{
				$this->errMsgNoKarcis->Text='';
				$this->DDKlinik->Enabled=true;
				$this->DDKlinik->focus();
			}
		}
    }
	
	public function checkRM($sender,$param)
    {   
		if($this->DDTindakan->SelectedValue!='')
		{
			//$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
			//$nmTdk=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);
			$this->nmTindakan->Text = '';
			$this->test->Text= $this->DDTindakan->SelectedValue;
			$nmTdk=TarifTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue);
			if(count($nmTdk)<1)
			{
				$this->nmTindakan->Text='Kode tindakan tidak ada!';
				$this->nmTindakan->ForeColor="#FF0000";
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				$this->DDBhp->Enabled=false;
				$this->tambahBtn->Enabled=false;
			}
			else
			{
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->id_tindakan->Text)->nama;
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue)->nama;
				$this->nmTindakan->ForeColor="#000000";
				$this->DDBhp->focus();
				$this->DDBhp->Enabled=true;
				$this->tambahBtn->Enabled=true;
			}
		}
		else
		{
			$this->nmTindakan->Text='';
		}		
		
    }
	
	public function DDjnsPdftrChanged($sender,$param)
    {   		
		if($this->DDjnsPdftr->SelectedValue==1)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='12000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==2)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='6000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==3)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='10000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		else
		{
			$this->showBayarPdftr->Visible=false;
			$this->jmlShowPdftr->Text='';
			$this->clearViewState('jmlBayarPdftr');
		}		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function cetakClicked($sender,$param)
    {		
		$sisaByr=$this->getViewState('sisa');
		$jmlTagihan=$this->getViewState('tmpJml');
		$table=$this->getViewState('nmTable');
		$cm=$this->notrans->Text;
		
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');
		$klinik=$this->getViewState('klinik');
		$id_dokter=$this->DDDokter->SelectedValue;
		$id_klinik=$this->DDKlinik->SelectedValue;
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		$sql = "SELECT 
					no_trans 
				FROM 
					tbt_rawat_jalan 
				WHERE 
					cm = '$cm'
					AND dokter = '$id_dokter'
					AND id_klinik = '$id_klinik' 
					AND flag = 0
					AND st_alih = 0";
		$noTransRwtJln=RwtjlnRecord::finder()->findBySql($sql)->no_trans;
		
		if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
		{
			$jmlBayar=$this->bayar->Text;
			$pjPasien=$this->pjPasien->Text;
			$notrans=$this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$transRwtJln= new KasirRwtJlnRecord();
				$transRwtJln->no_trans=$notrans;
				//$transRwtJln->no_trans_rwtjln=$this->getViewState('transaksi');
				$transRwtJln->no_trans_rwtjln=$noTransRwtJln;
				$transRwtJln->cm=$cm;
				$transRwtJln->klinik=$id_klinik;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->waktu=date('G:i:s');
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->bhp=$row['bhp'];
				$transRwtJln->tarif=$row['total'];
				$transRwtJln->total=$row['jml'];
				$transRwtJln->st_flag='0';
				$transRwtJln->Save();			
			}	
			
			/*
			//Update status tbt_rawat_jalan
			$sql="UPDATE 
					tbt_rawat_jalan 
				SET 
					flag='1' 
				WHERE 
					cm='$cm' 
					AND id_klinik='$id_klinik'
					AND dokter='$id_dokter'
					AND flag='0' 
					AND st_alih='0' ";		
			$this->queryAction($sql,'C');			
			*/
			//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'table'=>$table,'pjPasien'=>$pjPasien)));
			
			$this->batalClicked();	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJlnSukses'));
		}
		else //jika RadioButton Pendaftaran yang dipilih
		{	
			$jmlBayar=$this->bayarPdftr->Text;
			$pjPasien=$this->pjPasienPdftr->Text;
			$notrans=$this->numCounter('tbt_kasir_pendaftaran',KasirPendaftaranRecord::finder(),'09');//key '09' adalah konstanta modul untuk Kasir Pendaftaran
						
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$notrans;
			$newRecord->no_trans_pdftr=$this->getViewState('transaksi');			
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->notrans->Text);
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$this->getViewState('jmlBayarPdftr'),'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'jnsPdftr'=>$this->DDjnsPdftr->SelectedValue,'pjPasien'=>$pjPasien)));
		}
	}
	
	public function batalClicked()
    {		
		$this->bayarBtnPdftr->Text='Bayar';
		$this->RBjns->Enabled=true;		
		$this->DDjnsPdftr->Enabled=true;
		$this->DDjnsPdftr->SelectedIndex=-1;
		$this->bayarPdftr->Enabled=true;
		$this->jmlShow->Text='';
		$this->jmlShowPdftr->Text='';
		$this->sisaByr->Text='';
		$this->sisaByrPdftr->Text='';
		$this->nmTindakan->Text='';
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('jmlBayarPdftr');
		$this->notrans->Text='';
					
		
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->bayarPdftr->Text='';
		$this->DDBhp->SelectedValue='0';
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		$this->noKarcisPdftr->Text='';
		$this->pjPasien->Text='';
		$this->pjPasienPdftr->Text='';	
	
		$this->tdkPanel->Visible=false;
		$this->pdftrPanel->Visible=false;
		$this->showSecond->Visible=false;
		$this->showSecondPdftr->Visible=false;
		$this->showBayar->Visible=false;
		$this->dtGridCtrl->Visible=false;
		$this->showBayarPdftr->Visible=false;
		
		$this->karcisCtrl->Visible=false;
		$this->noKarcisPdftr->Text='';
		
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDKlinik->Enabled=false;
		
		$this->DDDokter->Enabled=false;
		$this->DDDokter->SelectedIndex=-1;
		
		$this->RBjns->SelectedValue=1;
		$this->RBjnsChanged($sender,$param);
		
		$this->poliCtrl->Visible = false;
		$this->notrans->Enabled = true;	
		$this->notrans->focus();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
