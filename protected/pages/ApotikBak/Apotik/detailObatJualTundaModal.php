<?php
class detailObatJualTundaModal extends SimakConf
{	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{			
			if($this->Request['id'] != '')
			{
				$id = $this->Request['id'];
				$jns_pasien = $this->Request['jns_pasien'];
				$no_trans_rawat = $this->Request['no_trans_rawat'];
				$petugas_internal = $this->Request['petugas_internal'];
		
				$data = ObatJualTundaRecord::finder()->findByPk($this->Request['id']);
				
				if($jns_pasien != '4')
				{
					$sql = "SELECT SUM(total) AS jml_tagihan FROM tbt_obat_jual_tunda WHERE no_trans_rawat='$no_trans_rawat' ";
				}
				else
				{
					$sql = "SELECT SUM(total) AS jml_tagihan FROM tbt_obat_jual_tunda WHERE petugas_internal='$petugas_internal' AND st='0' ";
				}
				
				$this->txt2->Text = $data->cm;
				$this->txt3->Text = $data->nama;
				$this->txt4->Text = 'Rp. '.number_format(ObatJualTundaRecord::finder()->findBySql($sql)->jml_tagihan,'2',',','.');
				/*				
				$sql = "SELECT 
							@rownum:=@rownum+1 'no_urut',
							id_obat, 
							hrg, SUM(jumlah) AS jumlah, 
							total,
							p.*	 
						FROM tbt_obat_jual_tunda p , (SELECT @rownum:=0) r
						WHERE 
							jns_pasien='$jns_pasien' ";
				
				$sql4 = "SELECT 
							@rownum:=@rownum+1 'no_urut',
							id_bhp, 
							bhp, 
							p.*	 
						FROM tbt_obat_jual_tunda p , (SELECT @rownum:=0) r
						WHERE 
							jns_pasien='$jns_pasien' ";
				*/
									
				$sql = "SELECT
							id, id_obat, 
							hrg, SUM(jumlah) AS jumlah, 
							total, expired
						FROM tbt_obat_jual_tunda
						WHERE 
							jns_pasien='$jns_pasien' ";
				
				$sql4 = "SELECT 
							id, id_bhp, 
							bhp 
						FROM tbt_obat_jual_tunda
						WHERE 
							jns_pasien='$jns_pasien' ";
												
				if($data->jns_pasien == '0')
				{
					$this->txt->Text = 'Rawat Jalan';
				}	
				elseif($data->jns_pasien == '1' || $data->jns_pasien == '3')
				{
					$this->txt->Text = 'Rawat Inap';
				}	
				elseif($data->jns_pasien == '2')
				{	
					$this->txt->Text = 'Pasien Luar / OTC';	
					$this->txt2->Text = '-';
				}	
				elseif($data->jns_pasien == '4')
				{
					$this->txt->Text = 'Unit Internal';		
					$this->txt2->Text = '-';	
					$this->txt3->Text = UserRecord::finder()->find('nip=?',$data->petugas_internal)->real_name;
				}	
				
				
				if($data->jns_pasien != '4')
				{
					$sql .= " AND no_trans_rawat = '$no_trans_rawat' ";
					$sql4 .= " AND no_trans_rawat = '$no_trans_rawat' ";
				}	
				else
				{
					$sql .= " AND petugas_internal = '$petugas_internal' ";	
					$sql4 .= " AND petugas_internal = '$petugas_internal' ";	
				}
				
				$sql1 = $sql."	AND st_racik = 0 
							AND st_imunisasi = 0 
							AND st=0 
						GROUP BY id_obat,id ";
				
				$sql2 = $sql." AND st_racik = 1
							AND st=0 
						GROUP BY id_obat,id 
						ORDER BY id_kel_racik";		
				
				$sql3 = $sql." AND st_imunisasi = 1
							AND st=0 
						GROUP BY id_obat,id 
						ORDER BY id_kel_imunisasi";
				
				$sql4 = $sql4." AND bhp <> ''
							AND st=0 
						GROUP BY id_obat,id 
						ORDER BY id";
										
				$arr = $this->queryAction($sql1,'S');
				$arr2 = $this->queryAction($sql2,'S');
				$arr3 = $this->queryAction($sql3,'S');
				$arr4 = $this->queryAction($sql4,'S');
								
				if(count($arr) > 0)
				{
					$this->nonRacikanCtrl->Visible = true;
					$this->Repeater->DataSource = $arr;
					$this->Repeater->dataBind();
				}	
				else
				{
					$this->nonRacikanCtrl->Visible = false;
				}	
				
				if(count($arr2) > 0)
				{
					$this->racikanCtrl->Visible = true;
					$this->Repeater2->DataSource = $arr2;
					$this->Repeater2->dataBind();
				}	
				else
				{
					$this->racikanCtrl->Visible = false;
				}	
				
				if(count($arr3) > 0)
				{
					$this->imunisasiCtrl->Visible = true;
					$this->Repeater3->DataSource = $arr3;
					$this->Repeater3->dataBind();
				}	
				else
				{
					$this->imunisasiCtrl->Visible = false;
				}	
				
				if(count($arr4) > 0)
				{
					$this->bhpCtrl->Visible = true;
					$this->Repeater4->DataSource = $arr4;
					$this->Repeater4->dataBind();
				}	
				else
				{
					$this->bhpCtrl->Visible = false;
				}	
				
			}
		}
		
	}	
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$item->no->Text = $item->DataItem['no_urut'];
			$item->nama->Text = ObatRecord::finder()->findByPk($item->DataItem['id_obat'])->nama;
			$item->jml->Text = number_format($item->DataItem['jumlah'],'0',',','.');
			$item->total->Text = number_format($item->DataItem['total'],'2',',','.');
			
			if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
				$item->expired->Text = $this->convertDate($item->DataItem['expired'],'3');
        }
    }
	
	public function repeaterDataBound2($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$item->no->Text = $item->DataItem['no_urut'];
			$item->nama->Text = ObatRecord::finder()->findByPk($item->DataItem['id_obat'])->nama;
			$item->jml->Text = number_format($item->DataItem['jumlah'],'0',',','.');
			$item->total->Text = number_format($item->DataItem['total'],'2',',','.');
			
			if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
				$item->expired->Text = $this->convertDate($item->DataItem['expired'],'3');
        }
    }
	
	public function repeaterDataBound3($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$item->no->Text = $item->DataItem['no_urut'];
			$item->nama->Text = ObatRecord::finder()->findByPk($item->DataItem['id_obat'])->nama;
			$item->jml->Text = number_format($item->DataItem['jumlah'],'0',',','.');
			$item->total->Text = number_format($item->DataItem['total'],'2',',','.');
			
			if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
				$item->expired->Text = $this->convertDate($item->DataItem['expired'],'3');
        }
    }
	
	public function repeaterDataBound4($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$item->no->Text = $item->DataItem['no_urut'];
			$item->nama->Text = ObatBhpRecord::finder()->findByPk($item->DataItem['id_bhp'])->nama;
			//$item->jml->Text = number_format($item->DataItem['jumlah'],'0',',','.');
			$item->total->Text = number_format($item->DataItem['bhp'],'2',',','.');
        }
    }
	
	
	public function editNonRacikBtnClicked($sender,$param)
    {
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		
		$id = $sender->CommandParameter;
		$data = ObatJualTundaRecord::finder()->findByPk($id);
		$hrg = $data->hrg;
		$jmlAwal = $data->jumlah;
		$jmlEdit = $this->Repeater->Items[$index]->jmlEdit->Text;
		$this->Repeater->Items[$index]->jmlEdit->Display = 'Dynamic';
		
		/*
		if($jmlEdit != '' && $jmlEdit <= $jmlAwal)
		{
			$jml = $jmlAwal - $jmlEdit;
			$totalReal = $jml * $hrg;
			$total = $this->bulatkan($totalReal);
			
			$data->jumlah = $jml;
			$data->total_real = $totalReal;
			$data->total = $total;
			$data->save();	
			
			$this->Repeater->Items[$index]->jml->Text = $jml;
			$this->Repeater->Items[$index]->jmlEdit->Text = '';
		}
		else
		{
			$this->Repeater->Items[$index]->jmlEdit->Text = '';
		}
		*/
	}
	
	public function updateClicked($sender,$param)
    {
		foreach($this->Repeater->Items as $item) 
		{
			$id = $item->id->Value;
			$jml = abs($item->jml->Text);
			
			$data = ObatJualTundaRecord::finder()->findByPk($id);
			$idObat = $data->id_obat;
			$tujuan = $data->tujuan;
			$jnsPasien = $data->tujuan;
			$jmlAwal = $data->jumlah;
			$hrg = $data->hrg;
			
			if($jml != '')
			{
				$totalReal = $jml * $hrg;
				$total = $this->bulatkan($totalReal);
				
				$data->jumlah = $jml;
				$data->total_real = $totalReal;
				$data->total = $total;
				$data->save();	
				
				
				$item->total->Text = 'Rp. '.number_format($total,'2',',','.');
				$subtotal += $total; 
			}
		}
		
		foreach($this->Repeater2->Items as $item) 
		{
			$id = $item->id->Value;
			$jml = abs($item->jml->Text);
			
			$data = ObatJualTundaRecord::finder()->findByPk($id);
			$idObat = $data->id_obat;
			$tujuan = $data->tujuan;
			$jnsPasien = $data->tujuan;
			$jmlAwal = $data->jumlah;
			$hrg = $data->hrg;
			
			if($jml != '')
			{
				$totalReal = $jml * $hrg;
				$total = $this->bulatkan($totalReal);
				
				$data->jumlah = $jml;
				$data->total_real = $totalReal;
				$data->total = $total;
				$data->save();	
				
				
				$item->total->Text = 'Rp. '.number_format($total,'2',',','.');
				$subtotal += $total; 
			}
		}
		
		foreach($this->Repeater3->Items as $item) 
		{
			$id = $item->id->Value;
			$jml = abs($item->jml->Text);
			
			$data = ObatJualTundaRecord::finder()->findByPk($id);
			$idObat = $data->id_obat;
			$tujuan = $data->tujuan;
			$jnsPasien = $data->tujuan;
			$jmlAwal = $data->jumlah;
			$hrg = $data->hrg;
			
			if($jml != '')
			{
				$totalReal = $jml * $hrg;
				$total = $this->bulatkan($totalReal);
				
				$data->jumlah = $jml;
				$data->total_real = $totalReal;
				$data->total = $total;
				$data->save();	
				
				
				$item->total->Text = 'Rp. '.number_format($total,'2',',','.');
				$subtotal += $total; 
			}
		}
		
		$id = $this->Request['id'];
		$jns_pasien = $this->Request['jns_pasien'];
		$no_trans_rawat = $this->Request['no_trans_rawat'];
		$petugas_internal = $this->Request['petugas_internal'];

		$data = ObatJualTundaRecord::finder()->findByPk($this->Request['id']);
		
		if($jns_pasien != '4')
		{
			$sql = "SELECT SUM(total) AS jml_tagihan, SUM(jumlah) AS jumlah FROM tbt_obat_jual_tunda WHERE no_trans_rawat='$no_trans_rawat' ";
		}
		else
		{
			$sql = "SELECT SUM(total) AS jml_tagihan, SUM(jumlah) AS jumlah FROM tbt_obat_jual_tunda WHERE petugas_internal='$petugas_internal' AND st='0' ";
		}
		
		$this->txt4->Text = 'Rp. '.number_format(ObatJualTundaRecord::finder()->findBySql($sql)->jml_tagihan,'2',',','.');
		
		if(ObatJualTundaRecord::finder()->findBySql($sql)->jumlah < 1)
			$this->cetakBtn->Enabled = false;
		else
			$this->cetakBtn->Enabled = true;
	}
	
	public function updateClicked2($sender,$param)
    {
		foreach($this->Repeater->Items as $item) 
		{
			$id = $item->id->Value;
			$jml = $item->jml->Text;
			
			$data = ObatJualTundaRecord::finder()->findByPk($id);
			$idObat = $data->id_obat;
			$tujuan = $data->tujuan;
			$jnsPasien = $data->tujuan;
			$jmlAwal = $data->jumlah;
			
			$desc = $this->cekStok($jml,$idObat,$tujuan,$jnsPasien);	
			if($desc != '')
			{
				if((intval(abs($jml)) || $jml == '0') && $jml != '')
				{
					$totalReal = $jml * $hrg;
					$total = $this->bulatkan($totalReal);
					
					$data->jumlah = $jml;
					$data->total_real = $totalReal;
					$data->total = $total;
					$data->save();	
					
					$item->jml->Text = $jml;
					$item->total->Text = 'Rp. '.number_format($total,'2',',','.');
					$subtotal += $total; 
				}
			}
			else
			{
				$data->jumlah = $jmlAwal;
				$data->save();
				
				$item->jml->Text = $desc;
				$item->jml->Text = $jmlAwal;
			}
		}
		
		foreach($this->Repeater2->Items as $item) 
		{
			$id = $item->id->Value;
			$jml = $item->jml->Text;
			
			if((intval(abs($jml)) || $jml == '0') && $jml != '')
			{
				$data = ObatJualTundaRecord::finder()->findByPk($id);
				$hrg = $data->hrg;
			
				$totalReal = $jml * $hrg;
				$total = $this->bulatkan($totalReal);
				
				$data->jumlah = $jml;
				$data->total_real = $totalReal;
				$data->total = $total;
				$data->save();	
				
				$item->jml->Text = $jml;
				$item->total->Text = $total;
				$item->total->Text = 'Rp. '.number_format($total,'2',',','.');
				$subtotal2 += $total;
			}
		}
		
		$id = $this->Request['id'];
		$jns_pasien = $this->Request['jns_pasien'];
		$no_trans_rawat = $this->Request['no_trans_rawat'];
		$petugas_internal = $this->Request['petugas_internal'];

		$data = ObatJualTundaRecord::finder()->findByPk($this->Request['id']);
		
		if($jns_pasien != '4')
		{
			$sql = "SELECT SUM(total) AS jml_tagihan, SUM(jumlah) AS jumlah FROM tbt_obat_jual_tunda WHERE no_trans_rawat='$no_trans_rawat' ";
		}
		else
		{
			$sql = "SELECT SUM(total) AS jml_tagihan, SUM(jumlah) AS jumlah FROM tbt_obat_jual_tunda WHERE petugas_internal='$petugas_internal' AND st='0' ";
		}
		
		$this->txt4->Text = 'Rp. '.number_format(ObatJualTundaRecord::finder()->findBySql($sql)->jml_tagihan,'2',',','.');
		
		if(ObatJualTundaRecord::finder()->findBySql($sql)->jumlah < 1)
			$this->cetakBtn->Enabled = false;
		else
			$this->cetakBtn->Enabled = true;
				
	}
	
	public function cekStok($jmlObat,$idBarang,$tujuan,$jnsPasien)
    {		
		$this->setViewState('jmlKekurangan',$jmlObat);	
		
		$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idBarang' AND tujuan = '$tujuan' GROUP BY id";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tmpStok += $row['jumlah'];
		}
		
		if($tmpStok >= $jmlObat)
		{
			//cari jumlah minimal 
			$idTujuan = $tujuan;
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
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($tujuan)->nama;
			
			if(($tmpStok-$jmlObat) < $jmlStokTol)//jika sudah melewati batas toleransi
			{
				//$this->msgStok->Text='<br/><br/>Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' mencapai <b>Batas Toleransi Minimal</b>. <br/>Penambahan Obat Gagal';				
				//$this->jml->Text = '';
				$desc = 'Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' mencapai <b>Batas Toleransi Minimal.';
				return $desc;
			}
			else //belum melewati batas toleransi
			{
				//$sql = "SELECT jumlah, id_harga FROM tbt_stok_lain WHERE id_obat='$idBarang' ORDER BY id_harga";
				$sql = "SELECT jumlah, id_harga, expired FROM tbt_stok_lain WHERE id_obat='$idBarang' AND tujuan = '$tujuan' ORDER BY id_harga DESC"; 
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					if($row['jumlah'] <> 0)
					{
						if($row['jumlah'] > $this->getViewState('jmlKekurangan'))
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_harga = $row['id_harga'];
								$expired = $row['expired'];
								$jmlAmbil = $this->getViewState('jmlKekurangan');
								$this->setViewState('jmlKekurangan','0');
								//$this->makeTmpTbl($id_harga,$jmlAmbil,$expired,$idBarang);
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
								//$this->makeTmpTbl($id_harga,$jmlAmbil,$expired,$idBarang);
							}
						}
					}
				}
				
				$desc = '';
				return $desc;
			
				//cek stok krisis => jika lebih kecil dari jml min di tbm_obat, keluarkan peringatan
				if( ($tmpStok-$jmlObat) < $jmlMinimal)
				{
					//$this->msgStok->Text='Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' sudah mencapai <b>Stok Kritis</b>.';
				}
			}
		}
		else
		{
			$desc = 'Stok obat yang ada tidak cukup!';
			return $desc;			
		}
	}
	
	public function simpanClicked()
	{
		$this->Response->reload();	
	}
	
	public function cetakClicked()
	{			
		if($this->Request['id'] != '')
		{
			$id = $this->Request['id'];
			$jns_pasien = $this->Request['jns_pasien'];
			$no_trans_rawat = $this->Request['no_trans_rawat'];
			$petugas_internal = $this->Request['petugas_internal'];
		}
			
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalDetailCetak(\''.$id.'\',\''.$jns_pasien.'\',\''.$no_trans_rawat.'\',\''.$petugas_internal.'\'); jQuery.FrameDialog.closeDialog();');		
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		//$this->clear();
		//$this->Page->CallbackClient->focus($this->nama);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
		
		//$this->getPage()->getClientScript()->registerEndScript
		//('','window.parent.unmaskContent(); window.parent.modalCallback('.$index.'); jQuery.FrameDialog.closeDialog();');
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalDetailCallback(); jQuery.FrameDialog.closeDialog();');
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.KabAdmin'));		
	}
}
?>