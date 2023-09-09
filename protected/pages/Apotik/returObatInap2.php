<?php
class returObatInap2 extends SimakConf
{   
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Apotik
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if($this->IsPostBack)
		{	
			//$this->modeActive();
			//$this->noCmPanel->Visible = true;
			//$this->Page->CallbackClient->focus($this->noCM);
			//$this->notrans->Enabled=true;	
			
			//$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			//$this->DDObat->dataBind();
			
			//$this->DDObat->Enabled=true;
		}
		else
		{
			$this->setViewState('modeInput','modeTrans');
		}
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{	
			$this->clearViewState('modeInput');			
			$this->Response->redirect($this->Service->constructUrl('Apotik.returObatInap'));
			$this->Response->Reload();
		}	
    }
	
	public function modeActive()
	{
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$this->queryAction($nmTable ,'D');
		}
		
		if($this->collectSelectionResult($this->modeInput) == '1')
		{				
			$this->noCmPanel->Visible = true;
			$this->noCM->Text = '';
			$this->noCM->focus();
			$this->noTransPanel->Visible = false;
			$this->tglCtrl->Visible = true;
			$this->modeInputTxt->Text = "No. CM";
			
			$this->clearViewState('modeInput');
			$this->setViewState('modeInput','modeCm');
		}
		else
		{
			$this->noTransPanel->Visible = true;
			$this->notrans->Text = '';
			$this->notrans->focus();
			$this->tglCtrl->Visible = false;
			$this->noCmPanel->Visible = false;
			$this->modeInputTxt->Text = "No. Transaksi";
			
			$this->clearViewState('modeInput');
			$this->setViewState('modeInput','modeTrans');
		}	
		$this->errMsgCtrl->Visible = false;
		//$this->modeTransDeactive();
		//$this->modeCmDeactive();
	}
	
	public function modeActiveTrans()
	{		
		if($this->modeInputTrans->SelectedValue == '1')//Sudah Bayar
		{								
			$this->clearViewState('modeTrans');
			$this->setViewState('modeTrans','1');
		}
		else
		{		
			$this->clearViewState('modeTrans');
			$this->setViewState('modeTrans','2');
		}	
		$this->errMsgCtrl->Visible = false;
		//$this->modeTransDeactive();
		//$this->modeCmDeactive();
	}
	
	public function cariClicked()
	{
		if($this->IsValid)  // when all validations succeed
        { 
			if($this->modeInput->SelectedValue == '1')
			{	
				$this->modeCmActive();
				//$this->checkInput();
				$cm = $this->noCM->Text;
				$tgl = $this->ConvertDate( $this->tglBeli->Text,2);					
						
				$sql ="SELECT 
						  tbt_obat_jual_inap.no_trans,
						  tbt_obat_jual_inap.no_reg
						FROM
						  tbt_obat_jual_inap
						WHERE ";
				if($this->modeInputTrans->SelectedValue == '1')
					$sql .= "tbt_obat_jual_inap.flag = 1 AND  ";
					
				if($this->modeInputTrans->SelectedValue == '2')
					$sql .= "tbt_obat_jual_inap.flag = 0 AND ";	
				 
				$sql .=  " tbt_obat_jual_inap.cm = '$cm'
						  AND tbt_obat_jual_inap.tgl = '$tgl' GROUP BY tbt_obat_jual_inap.no_reg  ";
				
				$arr=$this->queryAction($sql,'S');
				$jmlData=0;
				foreach($arr as $row)
				{
					$data .= $row['no_reg'] .',';	
					$jmlData++;
				}
				
				if($jmlData>0)
				{
					$v = strlen($data) - 1;
					$var=substr($data,0,$v);				
					$temp = explode(',',$var);
					
					$this->DDtrans->DataSource=$temp;
					$this->DDtrans->dataBind();
					$this->errMsgCtrl->Visible = false;
				}
				else
				{	
					//$this->errMsgCtrl->Visible = true;
					$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Data tidak ditemukan atau Pembayaran belum diselesaikan.</p>\',timeout: 4000,dialog:{modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							}
						}
					}});');	
					
					$this->noCM->Text = '';
					$this->tglBeli->Text = '';	
					$this->noCM->focus();
					$this->modeCmDeactive();
				}
				
			}
			elseif($this->modeInput->SelectedValue == '0')
			{
				$this->modeTransActive();
				$this->checkInput();
			}	
		}
	}
	
	public function modeCmActive()
	{
		$this->DDtransCtrl->Visible = true;
		$this->modeTransDeactive();
	}
	
	public function modeCmDeactive()
	{
		$this->DDtransCtrl->Visible = false;
		$this->noCmTxt2->Text = '';
		$this->nmPasien2->Text = '';
		$this->nmDokter->Text = '';
		$this->tgl->Text = '';
		$this->alasan->Text = '';
	}
	
	public function modeTransActive()
	{
		$this->hasilCariCtrl->Visible = true;
		$this->modeCmDeactive();
				
	}
	
	public function modeTransDeactive()
	{
		$this->hasilCariCtrl->Visible = false;
		//$this->noCmTxt1->Text = '';
		//$this->nmPasien1->Text = '';
	}
	
	public function DDtransChanged()
	{
		if($this->DDtrans->SelectedValue!='')
		{
			$this->checkInput();
		}
	}
	
	public function lock()
	{
		$this->modeInput->Enabled = false;	
		$this->modeInputTrans->Enabled = false;	
		
		$this->noCM->Enabled = false;	
		$this->notrans->Enabled = false;	
		$this->tglBeli->Enabled = false;	
		$this->cariBtn->Enabled = false;	
		$this->DDtrans->Enabled = false;	
	}
	
	public function unlock()
	{
		$this->modeInput->Enabled = true;
		$this->modeInputTrans->Enabled = true;	
			
		$this->noCM->Enabled = true;	
		$this->notrans->Enabled = true;	
		$this->tglBeli->Enabled = true;	
		$this->cariBtn->Enabled = true;	
		//$this->DDtrans->Enabled = true;	
	}
	
	public function checkInput()
    {	
		$this->modeActiveTrans();
		//cek mode input
		if($this->modeInput->SelectedValue == '1')
		{	
			 // valid if the username is not found in the database
			if(ObatJualInapRecord::finder()->find('cm = ?', $this->noCM->Text))
			{			
				$no_trans = $this->ambilTxt($this->DDtrans);
			}
			else
			{
				$this->noCM->Focus();
			}
		}
		elseif($this->modeInput->SelectedValue == '0')
		{
			 // valid if the username is not found in the database
			if(ObatJualInapRecord::finder()->find('no_reg = ?', $this->notrans->Text))
			{			
				$no_trans = $this->notrans->Text;
			}	
			else
			{
				$this->notrans->Focus();
			}
		}	
			
		$sql ="SELECT 
				  tbt_obat_jual_inap.no_trans,
				  tbt_obat_jual_inap.no_trans_inap,
				  tbt_obat_jual_inap.no_reg,
				  tbt_obat_jual_inap.cm,
				  tbt_obat_jual_inap.dokter,
				  tbt_obat_jual_inap.tgl,
				  tbt_obat_jual_inap.id_obat,
				  tbt_obat_jual_inap.id_harga,
				  tbt_obat_jual_inap.expired,
				  tbt_obat_jual_inap.jumlah,
				  tbt_obat_jual_inap.hrg,
				  tbt_obat_jual_inap.total,
				  tbt_obat_jual_inap.flag
				FROM
				  tbt_obat_jual_inap
				WHERE ";
		if($this->modeInputTrans->SelectedValue == '1')
			$sql .= " tbt_obat_jual_inap.flag = 1 ";		
		elseif($this->modeInputTrans->SelectedValue == '2')
			$sql .= " tbt_obat_jual_inap.flag = 0 ";
		
		$sql .= "  AND tbt_obat_jual_inap.no_reg = '$no_trans'
				   AND tbt_obat_jual_inap.st_racik = '0'
				   AND tbt_obat_jual_inap.st_imunisasi = '0' ";
		
		
		
		if($this->queryAction($sql,'S')) //jika pengecekan no_trans di tbt_obat_jual_inap suskes
		{
			$this->hasilCariCtrl->Visible = true;
			$this->errMsgCtrl->Visible = false;
			$this->lock();
		
			/*
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$jmlHarga += $row['harga'];				
			}
			*/
					
			$tmpPasien = ObatJualInapRecord::finder()->findBySql($sql);
			$this->nmDokter->Text= PegawaiRecord::finder()->findByPk($tmpPasien->dokter)->nama;	
			$this->noCmTxt2->Text= $tmpPasien->cm;
			$this->nmPasien2->Text= PasienRecord::finder()->findByPk($tmpPasien->cm)->nama;
			$this->tgl->Text= $this->convertDate($tmpPasien->tgl,'3');						
			
			//$this->showSecond->Visible=true;
			//$this->showTable->Visible=true;
			//$this->notrans->Enabled=false;
			//$this->errMsg->Text='';		
			//$this->cetakBtn->Enabled=true;
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										  no_trans varchar(14) NOT NULL,
										  no_trans_inap varchar(14) NOT NULL,
										  no_reg varchar(14) NOT NULL,
										  cm varchar(10) NOT NULL,
										  dokter varchar(5) NOT NULL,
										  klinik varchar(2) DEFAULT NULL,
										  tgl date NOT NULL,
										  wkt time NOT NULL,
										  sumber varchar(4) NOT NULL,
										  tujuan char(2) NOT NULL,
										  id_obat varchar(5) NOT NULL,
										  id_harga int(11) NOT NULL,
										  expired date default '0000-00-00',
										  jumlah int(11) NOT NULL,
										  hrg FLOAT(11,2) NOT NULL,
										  hrg_bulat FLOAT(11,2) NOT NULL,
										  total FLOAT(11,2) NOT NULL,
										  jml_retur int(11) DEFAULT NULL,
										  alasan varchar(255) NOT NULL,									 							 										 						PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				if($this->getViewState('modeInput')=='modeCm')
				{	
					$noTrans = $this->ambilTxt($this->DDtrans);
					
				}
				elseif($this->getViewState('modeInput')=='modeTrans')
				{
					$noTrans = $this->notrans->Text;
					
				}					
				
				$cm=$tmpPasien->cm;
				$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_reg='$noTrans' AND cm='$cm' AND tbt_obat_jual_inap.st_racik = 0 AND tbt_obat_jual_inap.st_imunisasi = 0 ";
				   
				if($this->getViewState('modeTrans') == '1')
					$sql .= " AND tbt_obat_jual_inap.flag = 1 ";
				
				if($this->getViewState('modeTrans') == '2')
					$sql .= " AND tbt_obat_jual_inap.flag = 0 ";					
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$no_trans=$row['no_trans'];
					$no_trans_inap=$row['no_trans_inap'];
					$no_reg=$row['no_reg'];
					$cm=$row['cm'];
					$dokter=$row['dokter'];
					$klinik='';
					$tgl=$row['tgl'];
					$wkt=$row['wkt'];
					$sumber=$row['sumber'];
					$tujuan=$row['tujuan'];
					$id_obat=$row['id_obat'];
					$id_harga=$row['id_harga'];
					$expired=$row['expired'];
					$jumlah=$row['jumlah'];
					$hrg=$row['hrg'];
					
					$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInap)->st_asuransi;
					if($stAsuransi == '1')
						$hrg_bulat = $row['hrg'];
					else
						$hrg_bulat = $this->bulatkan($row['hrg']);
					
					$total=$row['total'];					
					$jml_retur='0';					
				
					$sql="INSERT INTO $nmTable (no_trans,no_trans_inap,no_reg,cm,dokter,klinik,tgl,wkt,sumber,tujuan,id_obat,id_harga,expired,jumlah,hrg,hrg_bulat,total,jml_retur) VALUES ('$no_trans','$no_trans_inap','$no_reg','$cm','$dokter','$klinik','$tgl','$wkt','$sumber','$tujuan','$id_obat','$id_harga','$expired','$jumlah','$hrg','$hrg_bulat','$total','$jml_retur')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
					
				}
				
				$sql="SELECT * FROM $nmTable ORDER BY no_trans";
				
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
				if($this->getViewState('modeInput')=='modeCm')
				{	
					$noTrans = $this->ambilTxt($this->DDtrans);
					
				}
				elseif($this->getViewState('modeInput')=='modeTrans')
				{
					$noTrans = $this->notrans->Text;
					
				}			
				
				$cm=$tmpPasien->cm;
				$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_reg='$noTrans' AND cm='$cm' AND tbt_obat_jual_inap.st_racik = 0 AND tbt_obat_jual_inap.st_imunisasi = 0 ";
				if($this->getViewState('modeTrans') == '1')
					$sql .= " AND tbt_obat_jual_inap.flag = 1 ";
				
				if($this->getViewState('modeTrans') == '2')
					$sql .= " AND tbt_obat_jual_inap.flag = 0 ";		
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$no_trans=$row['no_trans'];
					$no_trans_inap=$row['no_trans_inap'];
					$no_reg=$row['no_reg'];
					$cm=$row['cm'];
					$dokter=$row['dokter'];
					$klinik='';
					$tgl=$row['tgl'];
					$wkt=$row['wkt'];
					$sumber=$row['sumber'];
					$tujuan=$row['tujuan'];
					$id_obat=$row['id_obat'];
					$id_harga=$row['id_harga'];
					$expired=$row['expired'];
					$jumlah=$row['jumlah'];
					$hrg=$row['hrg'];
					
					$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInap)->st_asuransi;
					if($stAsuransi == '1')
						$hrg_bulat = $row['hrg'];
					else
						$hrg_bulat = $this->bulatkan($row['hrg']);
						
					$total=$row['total'];					
					$jml_retur='0';					
				
					$sql="INSERT INTO $nmTable (no_trans,no_trans_inap,no_reg,cm,dokter,klinik,tgl,wkt,sumber,tujuan,id_obat,id_harga,expired,jumlah,hrg,hrg_bulat,total,jml_retur) VALUES ('$no_trans','$no_trans_inap','$no_reg','$cm','$dokter','$klinik','$tgl','$wkt','$sumber','$tujuan','$id_obat','$id_harga','$expired','$jumlah','$hrg','$hrg_bulat','$total','$jml_retur')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
					
				}
				
				$sql="SELECT * FROM $nmTable ORDER BY no_trans";
				
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
		}
		else //jika pengecekan no_trans di tbt_obat_jual_inap gagal => no_trans tidak ada atau pembayaran belum dilakukan
		{
			$this->hasilCariCtrl->Visible = false;
			//$this->errMsgCtrl->Visible = true;
			
			$this->getPage()->getClientScript()->registerEndScript
				('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Data tidak ditemukan atau Pembayaran belum diselesaikan.</p>\',timeout: 4000,dialog:{modal: true,
				buttons: {
				"OK": function() {
					jQuery( this ).dialog( "close" );
				}
			}
			}});');	
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
	
	
	public function itemCreated($sender,$param)
    {
		
		$item=$param->Item;
				
		//$this->DataGrid->DataKeys[$param->Item->ItemIndex]
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jmlRetur->TextBox->Columns=15;
        }       
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
		
        $item=$param->Item;		
		$id=$this->UserGrid->DataKeys[$item->ItemIndex];		
				
		$sql="SELECT cm,no_trans,id_obat FROM $nmTable WHERE id='$id' ";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			//$jmlAwal=$row['jumlah'];
			//$hrg=$row['hrg'];
			$cm=$row['cm'];
			$notrans=$row['no_trans'];
			$idObat=$row['id_obat'];
		}
		
		//if($this->getViewState('modeInput')=='modeCm')
		//{	
		//	$tgl = $this->ConvertDate( $this->tglBeli->Text,2);			
		//	$sql ="SELECT jumlah,hrg FROM tbt_obat_jual_inap WHERE flag = 1 AND cm = '$cm' AND tgl = '$tgl' AND id_obat='$idObat' ";
			
		//}
		//elseif($this->getViewState('modeInput')=='modeTrans')
		//{
			$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_trans='$notrans' AND cm='$cm' AND id_obat='$idObat'";
		//}	
		
		
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$no_trans_inap = $row['no_trans_inap'];
			$jmlAwal = $row['jumlah'];
			$hrg = $row['hrg'];
			$r_item = $row['r_item'];
			$r_racik = $row['r_racik'];
			$bungkus_racik = $row['bungkus_racik'];
		}
		
		$jmlRetur = abs($item->jmlRetur->TextBox->Text);
		$alasan = $this->alasan->Text;
		
		if($jmlRetur <= $jmlAwal)
		{
			$jml = $jmlAwal-$jmlRetur;
			
			$stAsuransi = RwtInapRecord::finder()->findByPk($no_trans_inap)->st_asuransi;
			if($stAsuransi == '1')
				$total = $hrg * $jml;
			else
				$total = $this->bulatkan($hrg * $jml);
				
			$total = $total + $r_item + $r_racik + $bungkus_racik;	
			
			
			$sql = "UPDATE $nmTable SET total='$total', jumlah='$jml', jml_retur='$jmlRetur', alasan='$alasan' WHERE id='$id'";
			$this->queryAction($sql,'C');
		}		
		
      
        $this->UserGrid->EditItemIndex=-1;
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
			
			$totRetur += $row['jml_retur'] ;
		}
		
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
		
		$this->alasan->Text='';
		
		//periksa tabel temp, jika ada data maka tombol cetak aktif
		 if($totRetur == 0)
		 {
		 	$this->cetakBtn->Enabled=false;
		 }
		 else
		 {
		 	$this->cetakBtn->Enabled=true;
		 }
    }
	
	/*		
	public function chObat($sender,$param)
	{
		$nofak=$this->notrans->text;
		$nocm=$this->noCM->text;
		$obat=$this->DDObat->SelectedValue;
		$sql="SELECT 
				  tbt_obat_jual_inap.jumlah AS jumlah
				FROM
				  tbt_obat_jual_inap
				WHERE
				  tbt_obat_jual_inap.no_trans = '$nofak' AND
				  tbt_obat_jual_inap.cm = '$nocm' AND
				  tbt_obat_jual_inap.id_obat = '$obat' ";		
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{			
			$this->jml->Text=$row['jumlah'];	
		}	
	}
	*/
		
	public function batalClicked()
    {	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->Reload();
	}
		
	public function checkRegister($sender,$param)
    {			
		$nofak=$this->notrans->text;
		$nocm=$this->noCM->text;
		$sql="SELECT 
				  tbm_obat.kode AS kode,
				  tbm_obat.nama AS nama,
				  tbt_obat_jual_inap.jumlah,
				  tbt_obat_jual_inap.cm,
				  tbt_obat_jual_inap.no_trans
				FROM
				  tbt_obat_jual_inap
				  INNER JOIN tbm_obat ON (tbt_obat_jual_inap.id_obat = tbm_obat.kode)
				WHERE 
				  tbt_obat_jual_inap.no_reg = '$nofak' AND
				  tbt_obat_jual_inap.cm = '$nocm' ";
		$arr=$this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;
		}
    }
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function hapusClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		if($this->getViewState('modeInput')=='modeCm')
		{	
			$noTrans = $this->ambilTxt($this->DDtrans);				
		}
		elseif($this->getViewState('modeInput')=='modeTrans')
		{
			$noTrans = $this->notrans->Text;			
		}
				
		$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_reg = '$noTrans'";
				if($this->getViewState('modeTrans') == '1')
					$sql .= " AND tbt_obat_jual_inap.flag = 1 ";
				
				if($this->getViewState('modeTrans') == '2')
					$sql .= " AND tbt_obat_jual_inap.flag = 0 ";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			//$jmlAwal=$row['jumlah'];
			//$hrg=$row['hrg'];
			
			$cm=$row['cm'];
			$notrans=$row['no_trans']; //notrans tbt_obat_jual_inap
			$notrans_rwtinap=$row['no_trans_inap'];//notrans tbt_rawat_inap
			$dokter=$row['dokter'];
			$klinik='';
			$sumber=$row['sumber'];
			$tujuan=$row['tujuan'];
			$idObat=$row['id_obat'];
			$jumlah=$row['jumlah'];	
			$id_harga=$row['id_harga'];	
			$expired=$row['expired'];	
			
			$sqlStok="SELECT * FROM tbt_stok_lain WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";
			$arrStok=$this->queryAction($sqlStok,'S');
			foreach($arrStok as $rowStok)
			{
				$jumlah = $jumlah + $rowStok['jumlah'];
				//update tbt_stok_lain		
				$sql="UPDATE tbt_stok_lain SET jumlah = '$jumlah' WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired' ";	
				
				$update=$this->queryAction($sql,'C');
			}		
		}
		
		$sql = "DELETE FROM tbt_obat_jual_inap WHERE no_reg = '$noTrans'";		
		$this->queryAction($sql,'C');
		
		$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Transaksi penjualan obat rawat inap dengan no. resep '.$noTrans.' Telah Dihapus dari database.</p>\',timeout: 200000,dialog:{
			modal: true,
			buttons: {
						"OK": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							reloadpage();
						}
					}
		}});');	
					
		//$this->Response->Reload();
	}
	
	public function reloadpage($sender,$param)
    {
		$this->Response->Reload();
	}
	
	public function cetakClicked($sender,$param)
    {
		$nmTable = $this->getViewState('nmTable');
		$operator=$this->User->IsUserNip;
		$noTransRetur = $this->numCounter('tbt_obat_jual_inap_retur',ObatJualInapReturRecord::finder(),'20');
		
		$sql="SELECT * FROM $nmTable WHERE jml_retur<>'0'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			//$jmlAwal=$row['jumlah'];
			//$hrg=$row['hrg'];
			
			$cm=$row['cm'];
			$notrans=$row['no_trans']; //notrans tbt_obat_jual_inap
			$notrans_rwtinap=$row['no_trans_inap'];//notrans tbt_rawat_inap
			$noreg=$row['no_reg'];
			$dokter=$row['dokter'];
			$klinik='';
			$sumber=$row['sumber'];
			$tujuan=$row['tujuan'];
			$idObat=$row['id_obat'];
			$id_harga=$row['id_harga'];	
			$expired=$row['expired'];
			$jumlah_sisa=$row['jumlah'];
			$hrg=$row['hrg'];
			$total=$row['total'];
			$jml_retur=$row['jml_retur'];
			$alasan=$row['alasan'];
			
			if($this->getViewState('modeInput')=='modeCm')
			{	
				$noTrans = $this->ambilTxt($this->DDtrans);
				
			}
			elseif($this->getViewState('modeInput')=='modeTrans')
			{
				$noTrans = $this->notrans->Text;
				
			}	
			
			$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_trans='$notrans' AND cm='$cm' AND id_obat='$idObat' AND id_harga = '$id_harga' AND expired = '$expired' AND st_racik = '0' AND st_imunisasi = '0' ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$r_item = $row['r_item'];
				$r_racik = $row['r_racik'];
				$bungkus_racik = $row['bungkus_racik'];
			}
					
			//update tbt_obat_jual_inap
			$total_real = $hrg * $jumlah_sisa;
			
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInap)->st_asuransi;
			if($stAsuransi == '1')
			{
				$hrg_bulat = $hrg;
				$total = $total_real + $r_item + $r_racik + $bungkus_racik;
				$totalRetur = $hrg * $jml_retur;
			}
			else
			{
				$hrg_bulat = $this->bulatkan($hrg);
				$total = $this->bulatkan($total_real + $r_item + $r_racik + $bungkus_racik);			
				$totalRetur = $hrg_bulat * $jml_retur;
			}
			
			if($jumlah_sisa > 0) //jika masih ada jumlah sisa
			{
				$sql="UPDATE 
						tbt_obat_jual_inap 
					SET 
						jumlah = '$jumlah_sisa',
						total = '$total',
						total_real = '$total_real'  
					WHERE 
						no_trans = '$notrans'  ";	
				
				$update=$this->queryAction($sql,'C');
			}
			else //jika diretur semua jumlah nya
			{
				//jika ada nilai r_item / r_racik / bungkus_racik
				if($r_item > 0 )
				{
					//cek jika masih ada obat lain selain obat yg diretur
					$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_trans_inap = '$notrans_rwtinap' AND id_obat='$idObat' AND id_harga = '$id_harga' AND expired <> '$expired' ";
					$arr=$this->queryAction($sql,'S');
					if(count($arr) > 0)
					{
						foreach($arr as $row)
						{
							$noTransUpdate = $row['no_trans'];
							$noTransInapUpdate = $row['no_trans_inap'];
							$r_itemUpdate = $row['r_item'] + $r_item;
							
							$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
							$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInapUpdate)->st_asuransi;
							if($stAsuransi == '1')
							{
								$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
							}
							else
							{
								$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
							}
							
						}
						
						$sql="UPDATE 
								tbt_obat_jual_inap 
							SET 
								r_item = '$r_item',
								total = '$totalUpdate',
								total_real = '$totalRealUpdate'  
							WHERE 
								no_trans = '$noTransUpdate' ";						
						$update=$this->queryAction($sql,'C');
						
						$sql = "DELETE FROM tbt_obat_jual_inap WHERE no_trans = '$notrans'";		
						$this->queryAction($sql,'C');
					}
					else
					{
						$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_trans_inap = '$notrans_rwtinap' AND id_obat='$idObat' AND id_harga <> '$id_harga' ";
						$arr=$this->queryAction($sql,'S');
						if(count($arr) > 0)
						{
							foreach($arr as $row)
							{
								$noTransUpdate = $row['no_trans'];
								$noTransInapUpdate = $row['no_trans_inap'];
								$r_itemUpdate = $row['r_item'] + $r_item;
								
								$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
								$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInapUpdate)->st_asuransi;
								if($stAsuransi == '1')
								{
									$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
								}
								else
								{
									$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
								}
							}
							
							$sql="UPDATE 
								tbt_obat_jual_inap 
							SET 
								r_item = '$r_item',
								total = '$totalUpdate',
								total_real = '$totalRealUpdate'  
							WHERE 
								no_trans = '$noTransUpdate' ";						
							$update=$this->queryAction($sql,'C');
							
							$sql = "DELETE FROM tbt_obat_jual_inap WHERE no_trans = '$notrans'";		
							$this->queryAction($sql,'C');
						}
						else
						{
							$sql="SELECT * FROM tbt_obat_jual_inap WHERE no_trans_inap = '$notrans_rwtinap' AND id_obat <> '$idObat' ";
							$arr=$this->queryAction($sql,'S');
							if(count($arr) > 0)
							{
								foreach($arr as $row)
								{
									$noTransUpdate = $row['no_trans'];
									$noTransInapUpdate = $row['no_trans_inap'];
									$r_itemUpdate = $row['r_item'] + $r_item;
									
									$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
									$stAsuransi = RwtInapRecord::finder()->findByPk($noTransInapUpdate)->st_asuransi;
									if($stAsuransi == '1')
									{
										$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									}
									else
									{
										$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
									}
								}
								
								$sql="UPDATE 
									tbt_obat_jual_inap 
								SET 
									r_item = '$r_item',
									total = '$totalUpdate',
									total_real = '$totalRealUpdate'  
								WHERE 
									no_trans = '$noTransUpdate' ";					
								$update=$this->queryAction($sql,'C');
								
								$sql = "DELETE FROM tbt_obat_jual_inap WHERE no_trans = '$notrans'";		
								$this->queryAction($sql,'C');
							}
						}
					}
				}
				
			}
			
			//UPDATE tbt_stok_lain
			$sqlStok="SELECT * FROM tbt_stok_lain WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";
			$arrStok=$this->queryAction($sqlStok,'S');
			foreach($arrStok as $rowStok)
			{
				$jumlah = $jml_retur + $rowStok['jumlah'];
				//update tbt_stok_lain		
				$sql="UPDATE tbt_stok_lain SET jumlah = '$jumlah' WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";				
				$this->queryAction($sql,'C');
			}
			
			
			//insert tbt_obat_jual_inap_retur				
			$newRetur= new ObatJualInapReturRecord();
			$newRetur->no_trans=$noTransRetur;
			$newRetur->no_trans_inap=$notrans_rwtinap;
			$newRetur->no_reg = $noreg;
			$newRetur->cm=$cm;			
			$newRetur->dokter=$dokter;
			//$newRetur->klinik=$klinik;						
			$newRetur->tgl=date('y-m-d');
			$newRetur->wkt=date('G:i:s');
			$newRetur->operator=$operator;			
			$newRetur->sumber=$sumber;
			$newRetur->tujuan=$tujuan;
			$newRetur->id_obat=$idObat;
			$newRetur->id_harga=$id_harga;
			$newRetur->expired=$expired;
			$newRetur->jumlah_awal=$jml_retur+$jumlah_sisa;
			$newRetur->jumlah=$jml_retur;
			$newRetur->hrg=$hrg_bulat;
			$newRetur->total = $totalRetur;
			$newRetur->flag='0';
			$newRetur->alasan=$alasan;
			
			$newRetur->Save();	
		}
			
		//delete temp tabel
		$this->queryAction($this->getViewState('nmTable'),'D');
		
		//cetak kwitansi
		$this->Response->redirect($this->Service->constructUrl('Apotik.CetakReturObatInap',
		array(
			'noTransRetur'=>$noTransRetur,
			'cm'=>$cm
			)));
	}
}
?>
