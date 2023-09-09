<?php
class BayarKasirRwtJlnKaryawan extends SimakConf
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
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtJlnKaryawan'));
		}	
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			$this->DDbulan->SelectedValue = date('m');
			$this->detailPanel->Display = 'None';
			$this->jmlPanel->Display = 'None';
			$this->cetakBtn->Enabled = false;
		}
    }
	
	public function firstPanelCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	
	
	public function prosesClicked()
    {
		if($this->IsValid) 
        {
			$cm = $this->formatCm($this->notrans->Text);	
			$bln = $this->DDbulan->SelectedValue;	
			$thn = date('Y');
			
			$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  tbt_rawat_jalan.cm = '$cm'
					  AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					  AND  tbt_rawat_jalan.st_kredit = '1'";
			
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{	
				$this->nama->Text = PasienRecord::finder()->findByPk($cm)->nama;
				$this->firstPanel->Enabled = false;
				$this->detailPanel->Display = 'Dynamic';
				$this->jmlPanel->Display = 'Dynamic';
				
				$this->bindGridRwtJln($cm,$bln,$thn);
				$this->bindGridLab($cm,$bln,$thn);
				$this->bindGridRad($cm,$bln,$thn);
				$this->bindGridFisio($cm,$bln,$thn);
				$this->bindGridApotik($cm,$bln,$thn);
				
				$totalTagihan = 0;
				
				if($this->getViewstate('totalRwtJln'))
				{
					$totalTagihan += $this->getViewstate('totalRwtJln');
				}
				
				if($this->getViewstate('totalLab'))
				{
					$totalTagihan += $this->getViewstate('totalLab');
				}
				
				if($this->getViewstate('totalRad'))
				{
					$totalTagihan += $this->getViewstate('totalRad');
				}
				
				if($this->getViewstate('totalFisio'))
				{
					$totalTagihan += $this->getViewstate('totalFisio');
				}
				
				if($this->getViewstate('totalApotik'))
				{
					$totalTagihan += $this->getViewstate('totalApotik');
				}
				
				$this->setViewState('totalTagihan',$totalTagihan);
				$this->jmlShow->Text = 'Rp. '.number_format($totalTagihan,2,',','.');
				
			}
			else
			{
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("Transaksi No. RM : '.$cm.' pada bulan '.$this->ambilTxt($this->DDbulan).' tidak ditemukan.");
				</script>';	
				$this->msg->Text = '';
				$this->detailPanel->Display = 'None';
				$this->jmlPanel->Display = 'None';
			}
		}
	}
	
	
	public function bindGridRwtJln($cm,$bln,$thn)
    {
		$sql = "select 
				tbm_nama_tindakan.nama AS nama,
				tbm_poliklinik.nama AS nama_klinik,
				tbt_kasir_rwtjln.tgl AS tgl,
				tbt_kasir_rwtjln.total AS total,
				tbt_kasir_rwtjln.st_flag AS flag,
				tbt_rawat_jalan.cm AS cm,
				tbt_rawat_jalan.no_trans AS no_trans,
				tbt_kasir_rwtjln.no_trans AS no_trans_asal,
				tbt_kasir_rwtjln.disc AS disc,
				tbt_kasir_rwtjln.id_tindakan AS id_tindakan 
			  from 
			  	tbt_kasir_rwtjln 
				INNER JOIN tbm_nama_tindakan on (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
				INNER JOIN tbm_poliklinik on (tbt_kasir_rwtjln.klinik = tbm_poliklinik.id) 
				INNER JOIN tbt_rawat_jalan on (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
			  where
				tbt_rawat_jalan.cm = '$cm'
				AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
				AND  tbt_rawat_jalan.st_kredit = '1'
				AND tbt_kasir_rwtjln.st_flag = '0'
				AND tbt_rawat_jalan.st_alih = 0";	
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$totalRwtJln += $row['total']; 
		}
		
		if(count($arr) > 0)
		{
			$this->setViewState('totalRwtJln',$totalRwtJln);
			$this->totalRwtJln->Text = 'Rp. '.number_format($totalRwtJln,2,',','.');
				
			$this->admRwtJlnGrid->DataSource = $arr;
			$this->admRwtJlnGrid->dataBind();
		}
		else
		{
			$this->tdkMsg->Text = 'Tidak Ada Transaksi Tindakan Rawat Jalan';
		}			
	}
	
	public function bindGridLab($cm,$bln,$thn)
    {
		$sql = "SELECT 
				  tbt_lab_penjualan.id,
				  tbt_lab_penjualan.no_trans AS no_reg,
				  tbt_lab_penjualan.tgl,
				  tbt_lab_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_kasir_rwtjln ON (tbt_rawat_jalan.no_trans = tbt_kasir_rwtjln.no_trans_rwtjln)
				  INNER JOIN tbt_lab_penjualan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_lab_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1'					
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_lab_penjualan.id";	
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$totalLab += $row['harga']; 
		}
		
		if(count($arr) > 0)
		{
			$this->setViewState('totalLab',$totalLab);
			$this->totalLab->Text = 'Rp. '.number_format($totalLab,2,',','.');
				
			$this->admLabGrid->DataSource = $arr;
			$this->admLabGrid->dataBind();
		}
		else
		{
			$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
		}			
	}
	
	public function bindGridRad($cm,$bln,$thn)
    {
		$sql = "SELECT 
				  tbt_rad_penjualan.id,
				  tbt_rad_penjualan.no_trans AS no_reg,
				  tbt_rad_penjualan.tgl,
				  tbt_rad_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_kasir_rwtjln ON (tbt_rawat_jalan.no_trans = tbt_kasir_rwtjln.no_trans_rwtjln)
				  INNER JOIN tbt_rad_penjualan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rad_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_rad_penjualan.id";	
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$totalRad += $row['harga']; 
		}
		
		if(count($arr) > 0)
		{
			$this->setViewState('totalRad',$totalRad);
			$this->totalRad->Text = 'Rp. '.number_format($totalRad,2,',','.');
				
			$this->admRadGrid->DataSource = $arr;
			$this->admRadGrid->dataBind();
		}
		else
		{
			$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
		}			
	}
	
	public function bindGridFisio($cm,$bln,$thn)
    {
		$sql = "SELECT 
				  tbt_fisio_penjualan.id,
				  tbt_fisio_penjualan.no_trans AS no_reg,
				  tbt_fisio_penjualan.tgl,
				  tbt_fisio_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_kasir_rwtjln ON (tbt_rawat_jalan.no_trans = tbt_kasir_rwtjln.no_trans_rwtjln)
				  INNER JOIN tbt_fisio_penjualan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_fisio_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_fisio_penjualan.id";	
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$totalFisio += $row['harga']; 
		}
		
		if(count($arr) > 0)
		{
			$this->setViewState('totalFisio',$totalFisio);
			$this->totalFisio->Text = 'Rp. '.number_format($totalFisio,2,',','.');
				
			$this->admFisioGrid->DataSource = $arr;
			$this->admFisioGrid->dataBind();
		}
		else
		{
			$this->fisioMsg->Text = 'Tidak Ada Transaksi Fisio';
		}			
	}
	
	
	public function bindGridApotik($cm,$bln,$thn)
    {
		$sql = "SELECT 
					tbt_obat_jual_karyawan.id, 
					tbt_obat_jual_karyawan.id_obat, 
					tbt_obat_jual_karyawan.jumlah, 
					tbt_obat_jual_karyawan.total, 
					tbt_obat_jual_karyawan.st_racik, 
					tbt_obat_jual_karyawan.tgl, 
					tbt_obat_jual_karyawan.id_kel_racik, 
					tbt_obat_jual_karyawan.st_imunisasi, 
					tbt_obat_jual_karyawan.id_kel_imunisasi
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_obat_jual_karyawan ON (tbt_rawat_jalan.no_trans = tbt_obat_jual_karyawan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_obat_jual_karyawan.id";	
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$totalApotik += $row['total']; 
		}
		
		
		$sqlBhp = "SELECT 
					tbt_obat_jual_karyawan.id, 
					tbt_obat_jual_karyawan.tgl, 
					tbt_obat_jual_karyawan.id_bhp, 
					tbt_obat_jual_karyawan.bhp
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_obat_jual_karyawan ON (tbt_rawat_jalan.no_trans = tbt_obat_jual_karyawan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1'
					AND tbt_rawat_jalan.st_alih = 0	
					AND tbt_obat_jual_karyawan.bhp <> 0
				GROUP BY
				  tbt_obat_jual_karyawan.id";				
				  
		
		$arrBhp = $this->queryAction($sqlBhp,'S');
		if(count($arrBhp) > 0)
		{
			foreach($arrBhp as $rowBhp)
			{	
				$jmlHargaBhp += $rowBhp['bhp'];
			}
		}
		else
		{
			$jmlHargaBhp = 0;
		}
		
		$totalApotik = $totalApotik + $jmlHargaBhp;
		
		if(count($arr) > 0)
		{
		
			$this->setViewState('totalApotik',$totalApotik);
			$this->totalApotik->Text = 'Rp. '.number_format($totalApotik,2,',','.');
				
			$this->apotikRwtJlnGrid->DataSource = $arr;
			$this->apotikRwtJlnGrid->dataBind();
			
			$this->apotikBhpRwtJlnGrid->DataSource=$arrBhp;
			$this->apotikBhpRwtJlnGrid->dataBind();
		}
		else
		{
			$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';
		}		  
	}
	
	
	public function bayarClicked($sender,$param)
    {
		if($this->IsValid)
		{
			if($this->bayar->Text >= $this->getViewState('totalTagihan'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('totalTagihan'));
				$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
							
				$this->bayarBtn->Enabled=false;	
				$this->bayar->Enabled=false;
				$this->cetakBtn->Enabled=true;	
				$this->Page->CallbackClient->focus($this->cetakBtn);
			}
			else
			{
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("Jumlah pembayaran kurang !");
				</script>';	
				$this->msg->Text = '';
				
				$this->Page->CallbackClient->focus($this->bayar);
				$this->cetakBtn->Enabled=false;
				$this->bayarBtn->Enabled=true;	
				$this->bayar->Enabled=true;
			}
		}
		
		$cm = $this->formatCm($this->notrans->Text);	
		$bln = $this->DDbulan->SelectedValue;	
		$thn = date('Y');
		$this->bindGridRwtJln($cm,$bln,$thn);
		$this->bindGridLab($cm,$bln,$thn);
		$this->bindGridRad($cm,$bln,$thn);
		$this->bindGridFisio($cm,$bln,$thn);
		$this->bindGridApotik($cm,$bln,$thn);
	}
	
	
	
	public function cetakClicked($sender,$param)
    {
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('totalTagihan');
		
		$bln = $this->DDbulan->SelectedValue;	
		$thn = date('Y');
		
		$tglNow=date('Y-m-d');
		$wktNow=date('G:i:s');
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$cm=$this->formatCm($this->notrans->Text);
		$nama=$this->nama->Text;
		
		//Update tbt_rawat_jalan st_kredit = 2
		$sql = "UPDATE 
					tbt_rawat_jalan
				SET 
					st_kredit = '2' 
				WHERE 
					tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' 
					AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '1' ";
					
		$this->queryAction($sql,'C');
			
		
		$this->clearViewState('totalTagihan');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnKreditKaryawan',
			array(
				'cm'=>$cm,
				'nama'=>$nama,
				'jmlTagihan'=>$jmlTagihan,
				'sisaByr'=>$sisaByr,
				'jmlBayar'=>$jmlBayar,
				'bln'=>$bln,
				'thn'=>$thn
			)));
	}
	
	public function batalClicked()
    {	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
		
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
