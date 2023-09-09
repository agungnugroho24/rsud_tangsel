<?php
class KasirApotik extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			//$this->notrans->Focus();
			$this->citoCheck->Focus();
			$this->citoCheck->Checked=false;
			$this->showSecond->Visible=false;
			//$this->showBayar->Visible=false;
			$this->cetakBtn->Enabled=false;
			$this->bayarBtn->Enabled=true;
			
		}		
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{			
			if($this->getViewState('nmTable'))
			{		
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->clearViewState('tmpJml');
			$this->clearViewState('sisaBulat');
			$this->getViewState('sisa');
			$this->bayar->Text;			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			//$this->clearViewState('dokter');
			//$this->clearViewState('klinik');
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';
			$this->errByr->Text='';
			$this->errMsg->Text='';
			$this->bayar->Text='';
			//$this->bhp->Text='';
			//$this->id_tindakan->Text='';
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			//$this->showBayar->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Tarif.KasirApotik',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	
	/*
	public function prosesClicked()
    {		
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
		$item=$this->id_tindakan->Text;
		$sql="SELECT b.nama AS nama, 
					 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
			  		 FROM tbm_tarif_tindakan a, 
			         	  tbm_nama_tindakan b 
			  		 WHERE a.id='$item' AND a.id=b.id";
		$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
		$bhp=$this->bhp->Text;
		$total= $tmpTarif->total;
		$nama=$tmpTarif->nama;	
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
			$item=$this->id_tindakan->Text;
			$sql="SELECT b.nama AS nama, 
						 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
						 FROM tbm_tarif_tindakan a, 
							  tbm_nama_tindakan b 
						 WHERE a.id='$item' AND a.id=b.id";
			$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
			$bhp=$this->bhp->Text;
			$total= $tmpTarif->total;
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
			$this->setViewState('tmpJml',$jml);
		}	
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
		$this->id_tindakan->Text='';
		$this->bhp->Text='';
		//$this->showBayar->Visible=true;
		$this->id_tindakan->Focus();	
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
				$this->id_tindakan->focus();
			//}	
			
		//}	
    }
	*/
	public function bayarClicked($sender,$param)
    {
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
			$this->setViewState('sisa',$hitung);
			//$this->id_tindakan->Enabled=false;
			//$this->bhp->Enabled=false;
			//$this->tambahBtn->Enabled=false;
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->Focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
			$this->bayarBtn->Enabled=false;
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->Focus();
			$this->cetakBtn->Enabled=false;
			$this->bayarBtn->Enabled=true;
		}
	}
	
	public function batalClicked()
    {		
		/*
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		*/
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('sisaBulat');
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text= '';
		
		$this->citoCheck->Checked=false;
		//$this->bhp->Text='';
		//$this->id_tindakan->Text='';
		$this->notrans->Focus();
		$this->showSecond->Visible=false;
		$this->bayarBtn->Enabled=true;
		//$this->showBayar->Visible=false;
	}
		
	public function checkRegister($sender,$param)
    {
		//$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$jnsPasien = substr($this->notrans->Text,6,2);
		
		if($jnsPasien == '04')
		{
			$activeRec = ObatJualRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual';
		}
		elseif($jnsPasien == '10')
		{
			$activeRec = ObatJualInapRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_inap';	
		}
		elseif($jnsPasien == '13')
		{
			$activeRec = ObatJualLainRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_lain';
		}
		
		if($jnsPasien == '04' || $jnsPasien == '10')
		{
			// valid if the username is not found in the database
		   if($activeRec->find('no_trans = ? AND flag = ?',$this->notrans->Text,'0'))
			{			
				$tmp = $this->notrans->Text;
				
				/*$sql = "SELECT 
						  tbd_pasien.nama as id_tindakan,
						  tbt_lab_penjualan.cm,
						  tbt_lab_penjualan.no_trans,
						  SUM(tbt_lab_penjualan.harga) AS tot_harga
						FROM
						  tbd_pasien
						  INNER JOIN tbt_lab_penjualan ON (tbd_pasien.cm = tbt_lab_penjualan.cm)
						GROUP BY
						  tbd_pasien.nama as id_tindakan,
						  tbt_lab_penjualan.cm,
						  tbt_lab_penjualan.no_trans
						WHERE tbt_lab_penjualan.no_trans='$tmp'";  
						
						(a.tarif1 + a.tarif2 + a.tarif3) AS tarif1 
						*/
				$sql ="SELECT 
							b.nama AS id_obat,
							b.cm,
							a.cm,
							a.no_trans,
							a.total
						 FROM $tbtObatTbl a, 
							  tbd_pasien b 
						 WHERE a.no_trans='$tmp' AND a.cm=b.cm
						 ";
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					//$jmlHarga += $row['total'];				
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
				
				$this->setViewState('nama',$tmpPasien->id_obat);	
				$this->setViewState('notrans',$this->notrans->Text);
				$this->setViewState('tmpJml',$jmlHargaBulat);
				$this->setViewState('sisaBulat',$sisaBulat);
				$this->setViewState('cm',$tmpPasien->cm);
				//$this->setViewState('tmpFlag',$tmpPasien->flag);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->nama->Text= $tmpPasien->id_obat;			
				$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
				//$this->dokter->Text= $tmpPasien->dokter;					 			
				//$this->setViewState('cm',$tmpPasien->cr_masuk);
				//$this->setViewState('dokter',$tmpPasien->dokter);		
				
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				//$this->id_tindakan->Enabled=true;
				//$this->bhp->Enabled=true;
				//$this->tambahBtn->Enabled=true;
				//$this->id_tindakan->Focus();
			}
			else
			{
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='No. Register tidak ada!!';
				$this->notrans->Focus();
			}
		}
		elseif($jnsPasien == '13')
		{
			if($activeRec->find('no_trans = ? AND flag = ?',$this->notrans->Text,'0'))
			{			
				$tmp = $this->notrans->Text;
				$sql ="SELECT 
							a.cm,
							a.no_trans,
							a.total
						 FROM $tbtObatTbl a
						 WHERE a.no_trans='$tmp' 
						 ";
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					//$jmlHarga += $row['total'];				
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
				
				$this->setViewState('nama',$tmpPasien->cm);	
				$this->setViewState('notrans',$this->notrans->Text);
				$this->setViewState('tmpJml',$jmlHargaBulat);
				$this->setViewState('sisaBulat',$sisaBulat);
				//$this->setViewState('cm',$tmpPasien->cm);
				//$this->setViewState('tmpFlag',$tmpPasien->flag);
				
				//$this->cm->Text= $tmpPasien->cm;
				$this->cm->Text='-';
				$this->nama->Text= $tmpPasien->cm;			
				$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
				//$this->dokter->Text= $tmpPasien->dokter;					 			
				//$this->setViewState('cm',$tmpPasien->cr_masuk);
				//$this->setViewState('dokter',$tmpPasien->dokter);		
				
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';			
				//$this->id_tindakan->Enabled=true;
				//$this->bhp->Enabled=true;
				//$this->tambahBtn->Enabled=true;
				//$this->id_tindakan->Focus();
			}
			else
			{
				$this->showFirst->Visible=true;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='No. Register tidak ada!!';
				$this->notrans->Focus();
			}
		}
			
    }	
	
	/*
	public function checkRM($sender,$param)
    {   		
		$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	*/
	public function keluarClicked($sender,$param)
	{		
		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {	
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml');
		$cm=$this->getViewState('cm');
		$notrans=$this->getViewState('notrans');
		$nama=$this->nama->Text;
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;
		
		$jnsPasien = substr($this->notrans->Text,6,2);
		
		if($jnsPasien == '04')
		{
			$activeRec = ObatJualRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual';
		}
		elseif($jnsPasien == '10')
		{
			$activeRec = ObatJualInapRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_inap';	
		}
		elseif($jnsPasien == '13')
		{
			$activeRec = ObatJualLainRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_lain';
		}
		
		//$sql="SELECT flag FROM tbt_lab_penjualan WHERE no_trans='$notrans' AND cm='$cm' ";
		//$data=LabJualRecord::finder()->find('no_trans = ? AND cm = ? ',$notrans, $cm);
		//$data->flag="1";
		//$data->save();
		if($jnsPasien == '04' || $jnsPasien == '10')
		{
			$sql="UPDATE $tbtObatTbl SET flag='1' WHERE no_trans='$notrans' AND cm='$cm' ";
		}
		
		elseif($jnsPasien == '13')
		{
			$sql="UPDATE $tbtObatTbl SET flag='1' WHERE no_trans='$notrans'";
		}
		$this->queryAction($sql,'C');
		
		
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
		
		$this->batalClicked();
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtApotikJual',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'jnsPasien'=>$jnsPasien)));
		
	}
}
?>
