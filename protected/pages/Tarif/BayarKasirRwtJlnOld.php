<?php
class BayarKasirRwtJln extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
			
		$session=$this->Application->getModule('session');	
		if($session['stCetakKasirRwtJln']=='1')
		{
			$session->remove('stCetakKasirRwtJln');
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtJln'));
		}	
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			//$this->notrans->Focus();
			$this->citoCheck->Focus();
			$this->citoCheck->Checked=false;
			$this->noTransCtrl->Visible=false;
			$this->showSecond->Visible=false;
			//$this->showBayar->Visible=false;
			$this->cetakBtn->Enabled=false;
			$this->notrans->Focus();
		}		
    }
	
	public function checkRegister($sender,$param)
    {
		$cm = $this->notrans->Text;
		$activeRec = KasirRwtJlnRecord::finder();
		$nmTbl = 'tbt_kasir_rwtjln';
		
		// valid if the username is not found in the database
		if($activeRec->find('cm = ? AND st_flag = ?', array($cm ,'0')))
		{			
			$sql="SELECT 
					no_trans,
					operator
				FROM 
					$nmTbl
				WHERE 
					cm='$cm' 
					AND st_flag='0'
				GROUP BY no_trans ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$data .= $row['no_trans'] .',';	
				$petugasPoli = $row['operator'];	
			}			
			
			$v = strlen($data) - 1;
			$var=substr($data,0,$v);				
			$temp = explode(',',$var);
			
			$this->DDtrans->DataSource=$temp;
			$this->DDtrans->dataBind();
			
			$this->setViewState('petugasPoli',$petugasPoli);
			
			$this->noTransCtrl->Visible=true;
			$this->citoCheck->Enabled=false;
			$this->errMsg->Text='';
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->noTransCtrl->Visible=false;
			$this->showSecond->Visible=false;
			$this->errMsg->Text='Pasien dengan No. Register '.$cm.' tidak ada !';
			$this->notrans->Text = '';
			$this->notrans->focus();				
			$this->citoCheck->Enabled=true;
		}
    }	
	
	public function DDtransChanged()
	{
		if($this->DDtrans->SelectedValue!='')
		{
			$this->checkNoTrans();
			$this->bayar->focus();
		}
		else
		{
			$this->showSecond->Visible=false;
			$this->errMsg->Text='';	
		}
	}
	
	public function checkNoTrans()
    {
		$tmp = $this->ambilTxt($this->DDtrans);	
		
		$sql ="SELECT b.nama 				
				 FROM tbt_kasir_rwtjln a, 
					  tbd_pegawai b 
				 WHERE a.no_trans='$tmp' 
				 	AND a.dokter=b.id
				 ";
		
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{			
			$dokter=$row['nama'];	
		}
		
		$this->setViewState('dokter',$dokter);
			
		$activeRec = KasirRwtJlnRecord::finder();
		$nmTbl = 'tbt_kasir_rwtjln';
		$cm = $this->notrans->Text;
		$noTransRwtJln = $activeRec->find('no_trans = ?', $tmp)->no_trans_rwtjln;
		$this->setViewState('noTransRwtJln',$noTransRwtJln);
		
		//pengecekan status alih di tbt_rawat_jalan
		if(RwtjlnRecord::finder()->find('no_trans = ? AND st_alih = ?', array($noTransRwtJln ,'1'))) //jika sudah alih status ke rwt inap
		{
			$this->errMsg->Text='Tagihan masuk rekap rawat inap !';
			$this->showSecond->Visible=false;
		}
		else
		{
			$sql ="SELECT 
						SUM(total) AS total
					 FROM 
					 	view_biaya_total_rwtjln 
					 WHERE 
					 	no_trans='$noTransRwtJln'
						AND flag = '0' ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['total'];
				}
				else
				{
					$jmlHarga += 2 * $row['total'];
				}			
			}
			
			$jmlHargaAsli = $jmlHarga;
			$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			//$tmpPasien = $activeRec->findBySql($sql);
			$nmPas = PasienRecord::finder()->findByPk($cm)->nama;
			
			$this->setViewState('nama',$nmPas);
			
			$this->nama->Text= $nmPas;	
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';	
		}
    }	
	
	/*
	public function checkNoTrans()
    {
		$tmp = $this->ambilTxt($this->DDtrans);		
		$activeRec = KasirRwtJlnRecord::finder();
		$nmTbl = 'tbt_kasir_rwtjln';
		
		$noTransRwtJln = $activeRec->find('no_trans = ?', $tmp)->no_trans_rwtjln;
		
		//pengecekan status alih di tbt_rawat_jalan
		if(RwtjlnRecord::finder()->find('no_trans = ? AND st_alih = ?', array($noTransRwtJln ,'1'))) //jika sudah alih status ke rwt inap
		{
			$this->errMsg->Text='Tagihan masuk rekap rawat inap !';
		}
		else
		{
			$sql ="SELECT 
						b.nama AS id_tindakan,
						b.cm,
						a.cm,
						a.no_trans,
						a.total
					 FROM $nmTbl a, 
						  tbd_pasien b 
					 WHERE a.no_trans='$tmp' AND a.cm=b.cm
					 ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['total'];
				}
				else
				{
					$jmlHarga += 2 * $row['total'];
				}			
			}
			
			$jmlHargaAsli = $jmlHarga;
			$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			$tmpPasien = $activeRec->findBySql($sql);
			
			$this->setViewState('nama',$tmpPasien->id_tindakan);	
			$this->setViewState('notrans',$this->notrans->Text);
			$this->setViewState('cm',$tmpPasien->cm);
			
			$this->nama->Text= $tmpPasien->id_tindakan;	
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';	
		}
    }	
	*/
	
	public function bayarClicked($sender,$param)
    {
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
			$this->setViewState('sisa',$hitung);
			
			
			$this->citoCheck->Enabled=false;
			
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->Focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
			$this->bayarBtn->Enabled=false;	
			$this->bayar->Enabled=false;	
			$this->DDtrans->Enabled=false;
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->Focus();
			$this->cetakBtn->Enabled=false;
			$this->bayarBtn->Enabled=true;	
			$this->bayar->Enabled=true;
			$this->DDtrans->Enabled=true;
		}
	}
	
	public function detailClicked($sender,$param)
    {
		$this->detailPanel->Visible = true ;
		$this->detailNoTrans->Text=$this->getViewState('noTransRwtJln');	
	}
	
	
	public function batalClicked()
    {	
		$this->clearViewState('tmpJml');
		$this->clearViewState('sisaBulat');		
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text='';
		
		$this->citoCheck->Enabled=true;
		$this->DDtrans->Enabled=true;
		$this->bayarBtn->Enabled=true;	
		$this->bayar->Enabled=true;
			
		$this->citoCheck->Checked=false;
		$this->notrans->Focus();
		$this->noTransCtrl->Visible=false;
		$this->showSecond->Visible=false;
		$this->detailPanel->Visible = false ;
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {	
		
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml');
		//$notrans=$this->ambilTxt($this->DDtrans);
		$notrans = $this->getViewState('noTransRwtJln');
		$cm=$this->notrans->Text;
		$dokter=$this->getViewState('dokter');
		$nama=$this->nama->Text;
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		//update status flag di masing-masing tabel 
		$sql = "SELECT
					jns_trans
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$notrans'
					AND flag = '0'";
	
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{			
			if($row['jns_trans']=='tindakan rawat jalan')
			{
				//Update tbt_kasir_rwtjln
				$nmTable = 'tbt_kasir_rwtjln ';
				$field = 'st_flag ';
			}
			elseif($row['jns_trans']=='laboratorium')
			{
				//Update tbt_lab_penjualan
				$nmTable = 'tbt_lab_penjualan ';
				$field = 'flag ';
			}
			elseif($row['jns_trans']=='radiologi')
			{
				//Update tbt_rad_penjualan	
				$nmTable = 'tbt_rad_penjualan ';
				$field = 'flag ';
			}
			
			$sql = "UPDATE 
						$nmTable
					SET 
						$field=1 
					WHERE 
					no_trans_rwtjln = '$notrans'
					AND $field = 0";	
			$this->queryAction($sql,'C');
		}
		
		//Update status tbt_arawat_jalan
		$sql="UPDATE tbt_rawat_jalan SET flag='1' WHERE no_trans='$notrans' AND flag='0' ";		
		$this->queryAction($sql,'C');
		
		/*	
		//Update status tbt_kasir_jalan
		$sql="UPDATE tbt_kasir_rwtjln SET st_flag='1' WHERE no_trans='$notrans' AND cm='$cm' AND st_flag='0' ";		
		$this->queryAction($sql,'C');
		*/
		
		//-------- Insert Harga Sisa Pembulatan ke tbt_obat_jual_sisa Jika ada Pembulatan-----------------
		$sisaBulat = $this->getViewState('sisaBulat');		
		if($sisaBulat > 0)
		{
			$ObatJualSisa= new ObatJualSisaRecord();
			$ObatJualSisa->no_trans=$notrans;
			$ObatJualSisa->jumlah=$sisaBulat;
			$ObatJualSisa->tgl=date('y-m-d');
			$ObatJualSisa->Save();			
		}	
		
		
		//Register cookies u/ status cetak
		$session=$this->Application->getModule('session');		
		$session['stCetakKasirRwtJln'] = '1';
		
		//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtLabJual',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr)));
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('notrans'=>$notrans,'cm'=>$cm,'dokter'=>$dokter,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'sisaByr'=>$sisaByr,'jmlBayar'=>$jmlBayar,'petugasPoli'=>$this->getViewState('petugasPoli'))));
		
	}
}
?>
