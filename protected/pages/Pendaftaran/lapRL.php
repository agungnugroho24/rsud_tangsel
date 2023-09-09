<?php
class lapRL extends SimakConf
{   	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{	
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
			
			$this->DDtahun->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun->dataBind();
			
			$this->DDtahun2->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun2->dataBind();
			
			$this->DDtahun3->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun3->dataBind();
		}		
    }		
	
	public function fileActive($sender,$param)
	{
		if($this->collectSelectionResult($this->modeCetak) == '1'){	
			$this->nmFile->Enabled=true;
			$this->nmFile->focus();
			$this->showFile->Visible=true;
		}else{
			$this->nmFile->Enabled=false;
			$this->showFile->Visible=false;
		}	
	}
	
	
	public function showDokter($sender,$param)
	{
		$this->DDDokter->focus();
		if($this->DDKlinik->SelectedValue=='')
		{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');//kelompok pegawai '1' adalah untuk dokter
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled=false;
			$this->clearViewState('klinik');
			$this->clearViewState('dokter');
			
		}else
		{
			$jmlDataDokter=PegawaiRecord::finder()->count('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);
			if($jmlDataDokter > 0)
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				
				$this->DDDokter->Enabled=true;	
			}
			else
			{
				$this->DDDokter->Enabled=false;	
			}
			
			$this->setViewState('klinik',$this->DDKlinik->SelectedValue);
			//$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
		}
	}
	
	public function DDDokterChanged($sender,$param)
	{	
		if($this->DDKlinik->SelectedValue!='')
		{
			$this->setViewState('dokter',$this->DDDokter->SelectedValue);	
			
		}else
		{
			$this->clearViewState('dokter');			
		}		
	}
		
	public function ChangedDDberdasarkan($sender,$param)
	{
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='5')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->DDtahun2->SelectedIndex=-1;
			
			$this->triwulan->visible=true;
			$this->tahun->visible=false;
			$this->bulan->visible=false;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			$this->DDtriwulan->focus();
		}
		elseif ($pilih=='4')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			
			$this->triwulan->visible=false;
			$this->tahun->visible=true;
			$this->bulan->visible=false;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			$this->DDbulan->focus();
		}
		elseif ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->DDtahun2->SelectedIndex=-1;
			
			$this->triwulan->visible=false;
			$this->tahun->visible=false;
			$this->bulan->visible=true;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			//$this->DDbulan->focus();
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->DDtahun2->SelectedIndex=-1;
			
			$this->triwulan->visible=false;
			$this->tahun->visible=false;
			$this->minggu->visible=true;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			$this->tglawal->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun->dataBind();
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->DDtahun2->SelectedIndex=-1;
			
			$this->triwulan->visible=false;
			$this->tahun->visible=false;
			$this->minggu->visible=false;
			$this->hari->visible=true;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			$this->tgl->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun->dataBind();
		}
		else
		{
			$this->clearViewState('pilihPeriode');			
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->DDtahun2->SelectedIndex=-1;
			
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;		
			////$this->clearViewState('idBulan');
			//$this->clearViewState('cariThn2');
			//$this->clearViewState('cariThn');
			//$this->clearViewState('cariBln');
			$this->triwulan->visible=false;
			$this->tahun->visible=false;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun->dataBind();
		}
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		//$pilih=$this->DDbulan->SelectedValue;
		if ($this->DDbulan->SelectedValue=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			//$this->clearViewState('cariBln');
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun->dataBind();
		}			
		else
		{
			$this->DDtahun->Enabled=true;
			$this->DDtahun->focus();
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			//$this->clearViewState('cariBln');
			//$this->setViewState('idBulan',$pilih);
			
			//$cariBln = $this->DDbulan->SelectedValue;
			//$this->setViewState('cariBln',$cariBln);
		}
	}
	
	public function ChangedDDtriwulan($sender,$param)
	{
		//$pilih=$this->DDbulan->SelectedValue;
		if ($this->DDtriwulan->SelectedValue=='')
		{
			$this->DDtahun3->Enabled=false;
			$this->DDtahun3->DataSource=$this->dataTahun(2009,2080);
			$this->DDtahun3->dataBind();
		}			
		else
		{
			$this->DDtahun3->Enabled=true;
			$this->DDtahun3->focus();
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
	
	public function checkRange($sender,$param)
	{	
		if($this->rangeAkhir->Text < $this->rangeAwal->Text) 
		{
			$hasil='0';
		}
		else
		{
			$hasil='1';
		}	
		
		$param->IsValid=($hasil==='1');
	}
	
	public function cetakClicked($sender,$param)
	{	
		if($this->rangeAkhir->Text < $this->rangeAwal->Text)
		{
			$this->chkRange->Visible=true;
			$this->rangeAkhir->focus();
			
		}
		else
		{
	
			if($this->Page->isValid)
			{
				$kdIcd=$this->cariIcd->Text;
				$klinik=$this->getViewState('klinik');
				$dokter=$this->getViewState('dokter');
				$rangeAwal=$this->rangeAwal->Text;
				$rangeAkhir=$this->rangeAkhir->Text;
				
				if($this->getViewState('pilihPeriode')==1)
				{
					$tgl=$this->ConvertDate( $this->tgl->Text,2);
				}
				elseif($this->getViewState('pilihPeriode')==2)
				{
					$tglawal=$this->ConvertDate( $this->tglawal->Text,2);
					$tglakhir=$this->ConvertDate( $this->tglakhir->Text,2);		
				}
				elseif($this->getViewState('pilihPeriode')==3)
				{
					$bln=$this->DDbulan->SelectedValue;
					$tahun=$this->DDtahun->SelectedValue;
				}
				elseif($this->getViewState('pilihPeriode')==4)
				{
					$tahun2=$this->DDtahun2->SelectedValue;	
				}
				elseif($this->getViewState('pilihPeriode')==5)
				{
					//$triwulan=$this->ambilTxt($this->DDtriwulan);
					$triwulan=$this->DDtriwulan->SelectedValue;
					$tahun3=$this->DDtahun3->SelectedValue;
				}
				$nmFile=$this->nmFile->Text;
			
				/*
				if( $stTmp == '0'){ //mode cetak pdf
					$this->Response->redirect($this->Service->constructUrl('pendaftaran.cetakLapRLpdf',array('kdIcd'=>$kdIcd,'klinik'=>$klinik,'dokter'=>$dokter,'tgl'=>$tgl,'tglawal'=>$tglawal,'tglakhir'=>$tglakhir,'bln'=>$bln,'tahun'=>$tahun,'tahun2'=>$tahun2,'periode'=>$this->getViewState('pilihPeriode'))));
				
				}else if( $stTmp == '1'){ //mode cetak xls
				*/
					$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakLapRLxls',array('tipeRawat'=>$this->modeInput->SelectedValue,'st_baru_lama'=>$this->st_baru_lama->SelectedValue,'kdIcd'=>$kdIcd,'klinik'=>$klinik,'dokter'=>$dokter,'tgl'=>$tgl,'tglawal'=>$tglawal,'tglakhir'=>$tglakhir,'bln'=>$bln,'tahun'=>$tahun,'tahun2'=>$tahun2,'triwulan'=>$triwulan,'tahun3'=>$tahun3,'periode'=>$this->getViewState('pilihPeriode'),'file'=>$nmFile,'rangeAwal'=>$rangeAwal,'rangeAkhir'=>$rangeAkhir)));		
				//}	
			
				$this->chkRange->Visible=false;
			}
		}
	}		
	
	protected function refreshMe()
	{
		$this->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
}
?>
