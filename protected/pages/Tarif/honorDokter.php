<?php
class honorDokter extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('7');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onInit($param)
	{				
		parent::onInit($param);		
		
		if(!$this->IsPostBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
			$this->DDPoli->DataSource=SpesialisRecord::finder()->findAll();
			$this->DDPoli->dataBind();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
														
			$orderBy=$this->getViewState('orderBy');			
			////$cariByNama=$this->getViewState('cariByNama');			
			$cariPoli=$this->getViewState('cariPoli');
			$cariDokter=$this->getViewState('cariDokter');
			$urutBy=$this->getViewState('urutBy');
			$Company=$this->getViewState('Company');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');		
			
			
		}	
		else
		{
			if($this->getViewState('klinik'))
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND spesialis = ?', '1', $this->DDPoli->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				$this->DDDokter->Enabled=true;
				
				if($this->getViewState('dokter'))
				{
					$this->DDDokter->SelectedValue=$this->getViewState('dokter');
				}
			}
			else
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND spesialis = ?', '1', $this->DDPoli->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				$this->DDDokter->Enabled=false;
			}
			
		
		}	
    }		
	
	public function DDPoliChanged($sender,$param)
	{
		if($this->DDPoli->SelectedValue!='')
		{
			$this->setViewState('klinik',$this->DDPoli->SelectedValue);						
		}
		else
		{
			$this->clearViewState('klinik');
		}
		
		$this->DDDokter->focus();
		$this->clearViewState('dokter');
	}	
	
	public function DDDokterChanged($sender,$param)
	{
		if($this->DDDokter->SelectedValue!='')
		{
			$this->setViewState('dokter',$this->DDDokter->SelectedValue);						
		}
		else
		{
			$this->clearViewState('dokter');
		}
		
		$this->DDberdasarkan->focus();
	}	
			
	public function ChangedDDberdasarkan($sender,$param)
	{
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->bulan->visible=true;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->DDbulan->focus();
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->minggu->visible=true;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tglawal->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->minggu->visible=false;
			$this->hari->visible=true;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tgl->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
		}
		else
		{
			$this->clearViewState('pilihPeriode');			
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;		
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
			
		}
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariBln');
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
		}			
		else
		{
			$this->DDtahun->Enabled=true;
			$this->DDtahun->focus();
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariBln');
			//$this->setViewState('idBulan',$pilih);
			
			$cariBln = $this->DDbulan->SelectedValue;
			$this->setViewState('cariBln',$cariBln);
		}
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		if ($pilih=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
		}			
		else
		{
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			//$this->setViewState('idBulan',$pilih);
			
			$cariThn = $pilih;
			$this->setViewState('cariThn',$cariThn);
		}
		
		
	}
		
	public function checkTgl($sender,$param)
	{
		$pecahTglAwal=explode('-',$this->tglawal->Text);
		$pecahTglAkhir=explode('-',$this->tglakhir->Text);
		$tglAwal=$pecahTglAwal['0'];
		$cariBln=$pecahTglAwal['1'];
		$thnAwal=$pecahTglAwal['2'];
		$tglAkhir=$pecahTglAkhir['0'];
		$cariThn=$pecahTglAkhir['1'];
		$thnAkhir=$pecahTglAkhir['2'];
		
		if($thnAkhir<$thnAwal) 
		{
			$hasil='0';
		}
		else
		{
			if($cariThn<$cariBln) 
			{
				$hasil='0';
			}
			else
			{
				if($tglAkhir<$tglAwal) 
				{
					$hasil='0';
				}
				else
				{
					//jika tgl akhir benar
					//$id_ijin=$this->getViewState('id');
					//$this->Response->redirect($this->Service->constructUrl('Lap'.$id_ijin,array('idIjin'=>$id_ijin)));
					$hasil='1';
				}
			}
		}	
		
		$param->IsValid=($hasil==='1');
	}
	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	
	public function cariClicked()
	{	
		if($this->IsValid)
		{
			$this->rincianCtrl->Visible=true;
			$spesialis=$this->getViewState('klinik');
			$dokter=$this->getViewState('dokter');
			
			//------------------------- Query SubTotal 1 (Rawat Inap) --------------------------------------
			switch ($spesialis) {
				case '02': //spesialis kandungan
					$sqlTarifDktr="SELECT SUM(tarif_obgyn) AS tarif_dokter FROM view_inap_operasi_billing WHERE dktr_obgyn='$dokter' ";
					
					$sqlTarifAssDktr="SELECT SUM(tarif_assobgyn) AS tarif_assdokter FROM view_inap_operasi_billing WHERE ass_obgyn='$dokter '";
					break;

				case '13': //spesialis anestesi
					$sqlTarifDktr="SELECT SUM(tarif_anastesi) AS tarif_dokter FROM view_inap_operasi_billing WHERE dktr_anastesi='$dokter' ";
					
					$sqlTarifAssDktr="SELECT SUM(tarif_assanastesi) AS tarif_assdokter FROM view_inap_operasi_billing WHERE ass_anastesi='$dokter' ";
					break;

				default:
					$sqlTarifDktr="SELECT SUM(tarif_dktr) AS tarif_dokter FROM view_inap_operasi_billing WHERE dktr_utama='$dokter' ";
					
					$sqlTarifAssDktr="SELECT SUM(tarif_assdktr) AS tarif_assdokter FROM view_inap_operasi_billing WHERE ass_dktr='$dokter' ";
			}
			
			//------------------------- Query SubTotal 2 (Rawat Jalan) --------------------------------------
			$sql="SELECT SUM(jasmed_dok) AS jasmed_dok FROM view_lap_jasmed_dokter WHERE dokter='$dokter' ";
			
			
			//------------------------- Periode --------------------------------------
			if($this->getViewState('pilihPeriode')==='1') //harian
			{
				$tgl=$this->ConvertDate($this->tgl->Text,'2');
				$this->clearStatePeriode();
				$this->setViewState('cariTgl',$tgl);
				
				$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				
				$sqlTarifDktr .= "AND view_inap_operasi_billing.tgl = '$tgl' ";
				$sqlTarifAssDktr .= "AND view_inap_operasi_billing.tgl = '$tgl' ";
				$sql .= "AND view_lap_jasmed_dokter.tgl = '$tgl' ";
			}
			elseif($this->getViewState('pilihPeriode')==='2') //mingguan
			{
				$tglawal=$this->ConvertDate($this->tglawal->Text,'2');
				$tglakhir=$this->ConvertDate($this->tglakhir->Text,'2');
				$this->clearStatePeriode();
				$this->setViewState('cariTglAwal',$tglawal);
				$this->setViewState('cariTglAkhir',$tglakhir);
				
				$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				
				$sqlTarifDktr .= "AND view_inap_operasi_billing.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				$sqlTarifAssDktr .= "AND view_inap_operasi_billing.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				$sql .= "AND view_lap_jasmed_dokter.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
			}
			elseif($this->getViewState('pilihPeriode')==='3') //bulanan
			{
				if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
					$bln=$this->DDbulan->SelectedValue;
					$thn=$this->ambilTxt($this->DDtahun);
					$this->clearStatePeriode();
					$this->setViewState('cariBln',$bln);
					$this->setViewState('cariThn',$thn);
					
					$sqlTarifDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
					$sqlTarifAssDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
					$sql .= "AND MONTH (view_lap_jasmed_dokter.tgl)='$bln' AND YEAR(view_lap_jasmed_dokter.tgl)='$thn' ";
				
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
					$bln=date('m');
					$thn=date('Y');
					$this->clearStatePeriode();
					$this->setViewState('cariBln',$bln);
					$this->setViewState('cariThn',$thn);
					
					$sqlTarifDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
					$sqlTarifAssDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
					$sql .= "AND MONTH (view_lap_jasmed_dokter.tgl)='$bln' AND YEAR(view_lap_jasmed_dokter.tgl)='$thn' ";
				}
				
			}
			else //periode tidak dipilih
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				$bln=date('m');
				$thn=date('Y');
				$this->clearStatePeriode();
				$this->setViewState('cariBln',$bln);
				$this->setViewState('cariThn',$thn);
				
				$sqlTarifDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
				$sqlTarifAssDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
				$sql .= "AND MONTH (view_lap_jasmed_dokter.tgl)='$bln' AND YEAR(view_lap_jasmed_dokter.tgl)='$thn' ";
			}
			
			
			
			//------------------------- Hitung SubTotal 1 (Rawat Inap) --------------------------------------
			$arrTarifDktr=$this->queryAction($sqlTarifDktr,'S');
			foreach($arrTarifDktr as $row)
			{
				$TarifDktr=$row['tarif_dokter'];		
			}
			
			$arrTarifAssDktr=$this->queryAction($sqlTarifAssDktr,'S');
			foreach($arrTarifAssDktr as $row)
			{
				$TarifAssDktr=$row['tarif_assdokter'];		
			}
			
			$honorDktrRwtInap=$TarifDktr+$TarifAssDktr;
			
			
			//------------------------- Hitung SubTotal 2 (Rawat Jalan) --------------------------------------			
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jasmedRwtJln=$row['jasmed_dok'];		
			}
			
			$this->subTot1->Text=number_format($honorDktrRwtInap,'2',',','.');
			$this->subTot2->Text=number_format($jasmedRwtJln,'2',',','.');
			
			$total=$honorDktrRwtInap+$jasmedRwtJln;
			$pajak=0.075 * $total;
			$potongan=0.05 * $total; 
			$grandTot=$total - ($pajak + $potongan); 
			
			$this->total->Text=number_format($total,'2',',','.');
			$this->pajak->Text=number_format($pajak,'2',',','.');
			$this->potongan->Text=number_format($potongan,'2',',','.');
			$this->grandTot->Text=number_format($grandTot,'2',',','.');
			$this->terbilang->Text='( '.ucwords($this->terbilang($grandTot)).' )';
			
			$this->cetakBtn->Enabled=true;
			
			$this->setViewState('subTot1',$honorDktrRwtInap);
			$this->setViewState('subTot2',$jasmedRwtJln);
			$this->setViewState('total',$total);
			$this->setViewState('pajak',$pajak);
			$this->setViewState('potongan',$potongan);
			$this->setViewState('grandTot',$grandTot);
		}		
	}
	
	public function clearStatePeriode()
    {	
		$this->clearViewState('cariTgl');
		$this->clearViewState('cariTglAwal');
		$this->clearViewState('cariTglAkhir');
		$this->clearViewState('cariBln');
		$this->clearViewState('cariThn');
	}	
	
	public function batalClicked($sender,$param)
    {		
		
		$this->rincianCtrl->Visible=false;
		
		$this->Response->Reload();
	}
	
	public function cetakClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakHonorDokter',array('spesialis'=>$this->DDPoli->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue,'tgl'=>$this->getViewState('cariTgl'),'tglawal'=>$this->getViewState('cariTglAwal'),'tglakhir'=>$this->getViewState('cariTglAkhir'),'cariBln'=>$this->getViewState('cariBln'),'cariThn'=>$this->getViewState('cariThn'),'periode'=>$this->txtPeriode->Text,'subTot1'=>$this->getViewState('subTot1'),'subTot2'=>$this->getViewState('subTot2'),'total'=>$this->getViewState('total'),'pajak'=>$this->getViewState('pajak'),'potongan'=>$this->getViewState('potongan'),'grandTot'=>$this->getViewState('grandTot'))));
		
		$this->clearStatePeriode();
	}	
		
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
