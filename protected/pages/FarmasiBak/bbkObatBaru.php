<?php
class bbkObatBaru extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->form2->Visible=false;
			$this->cetakBtn->Enabled=false;
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->DDTujuan->dataBind();			
			$sqlNmObat="SELECT tbm_obat.kode,tbm_obat.nama 
					FROM tbt_stok_lain 
					INNER JOIN tbm_obat ON (tbt_stok_lain.id_obat = tbm_obat.kode)
					WHERE tbt_stok_lain.sumber = '01' AND tbt_stok_lain.tujuan = '2' AND tbt_stok_lain.jumlah > '0'";
			$this->DDnmObat->DataSource=$this->queryAction($sqlNmObat,'S');
			$this->DDnmObat->dataBind();
			$this->DDnmObat->Enabled=true;
		}
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();			
		}
		
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
						
			$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat',array('goto'=>'1')));
		}	
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->DDTujuan->Focus();
		}
	}
	
	protected function prosesClicked()
	{
		//$this->BTproses->Enabled=false;
		$this->DDTujuan->Enabled=false;
		$this->nmPetugas->Enabled=false;
		$this->nmPenerima->Enabled=false;
		$this->ket->Enabled=false;
		$this->form2->Visible=true;	 	
		$this->DDnmObat->Enabled=false;
		$tmp=$this->DDTujuan->SelectedValue;/*
		$this->DDSumMaster->DataSource=DesFarmasiRecord::finder()->find('id <> ?',$tmp);
		$this->DDSumMaster->dataBind();*/
		$sql="SELECT id, nama FROM tbm_destinasi_farmasi WHERE id <> '$tmp' ";
		$this->DDSumMaster->DataSource=$this->queryAction($sql,'S');
		$this->DDSumMaster->dataBind();
		
	}
	
	public function DDSumMasterChanged()
	{
		$tmp=$this->DDSumMaster->SelectedValue;
		$this->DDnmObat->Enabled=true;
		$sql="SELECT 
				  tbm_obat.nama,
				  tbm_obat.kode
				FROM
				  tbt_stok_lain
				  INNER JOIN tbm_obat ON (tbt_stok_lain.id_obat = tbm_obat.kode)
				WHERE
				  tbt_stok_lain.tujuan = '$tmp' AND
				  tbt_stok_lain.jumlah > 0 ";
		$this->DDnmObat->DataSource=$this->queryAction($sql,'S');
		$this->DDnmObat->dataBind();
		//$this->showSql->text=$sql;
	}
	
	protected function DDnmObatChanged()
	{
		if($this->DDnmObat->SelectedValue=='')
		{
			$this->jmlAmbil->Enabled=false;
		}
		else
		{
			//$sumber = $this->getViewState('sumber');
			//$idObat=$this->DDnmObat->SelectedValue;
			$this->jmlAmbil->Text='';
			$this->jmlAmbil->Enabled=true;
			$this->jmlAmbil->focus();
			//$this->jmlAmbil->Text=$this->DDnmObat->SelectedValue.$sumber;
		}		
	}
	
	public function checkJml($sender,$param)
    {    
		$sumber = '01';
		$tmp=$this->DDSumMaster->SelectedValue;
		$idObat=$this->DDnmObat->SelectedValue;
		$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tujuan='$tmp' ";
		$jmlGudang=StokLainRecord::finder()->findBySql($sql);   
		$param->IsValid=($this->jmlAmbil->Text <= $jmlGudang->getColumnValue('jumlah'));
    }
	
	protected function ambilClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$this->cetakBtn->Enabled=true;
			$idObat=$this->DDnmObat->SelectedValue;
			//$sumber = $this->getViewState('sumber');
			//$sumber = '01';
			$tujuan=TPropertyValue::ensureString($this->DDTujuan->SelectedValue);
			$sumber=TPropertyValue::ensureString($this->DDSumMaster->SelectedValue);
			//$this->showSql->text=$sumber.'-'.$tujuan;
			$jumlah=$this->jmlAmbil->Text;
			$this->test->text=$idObat;
			
			if (!$this->getViewState('nmTable'))
			{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 									 
										 kode VARCHAR(5) NOT NULL,
										 sumber VARCHAR(30) NOT NULL,
										 tujuan VARCHAR(30) NOT NULL,				
										 jumlah INT(11) NOT NULL,				
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...					
			
			$sql="INSERT INTO $nmTable (kode,sumber,tujuan,jumlah) VALUES ('$idObat','$sumber','$tujuan','$jumlah')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();				
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql="INSERT INTO $nmTable (kode,sumber,tujuan,jumlah) VALUES ('$idObat','$sumber','$tujuan','$jumlah')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();						
			}
			 	
			$this->DDnmObat->Enabled=true;
			
			$this->jmlAmbil->Text='';
			$this->jmlAmbil->Enabled=false;
		}	
		
	}
	
	public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			$nmTable = $this->getViewState('nmTable');
				
			$sql = "DELETE FROM $nmTable WHERE id='$ID'";
			$this->queryAction($sql,'C');								
						
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();						
		}	
    }	
	
	public function blnRomawi($input)
    {		
		switch ($input) 
		{
			case "01":
				$bln='I';
				break;
			case "02":
				$bln='II';
				break;	
			case "03":
				$bln='II';
				break;	
			case "04":
				$bln='IV';
				break;	
			case "05":
				$bln='V';
				break;	
			case "06":
				$bln='IV';
				break;		
			case "07":
				$bln='IIV';
				break;		
			case "08":
				$bln='IIIV';
				break;		
			case "09":
				$bln='IX';
				break;	
			case "10":
				$bln='X';
				break;	
			case "11":
				$bln='XI';
				break;	
			case "12":
				$bln='XII';
				break;	
		}
		return $bln;
	}
	
	public function numCounter($activeTable)
    {			
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT no_bbk FROM tbt_bbk_barang order by no_bbk desc";
		$no = $find->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$thn=date("Y");
			$bln=$this->blnRomawi(date("m"));			
			$urut='000001';
			$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
		}else{
			$thn=date("Y");
			$bln=$this->blnRomawi(date("m"));
			$urut=intval(substr($no->getColumnValue('no_bbk'),0,6));
			if ($urut==999999)
			{
				$urut=1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
			}else{
				$urut=$urut+1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$nobbk=$urut.'/BBK-'.$bln.'/'.$thn;
			}
		}
		return $nobbk;
	}	
	
	public function cetakClicked($sender,$param)
	{	
		$tujuan=TPropertyValue::ensureString($this->DDTujuan->SelectedValue);	
		$sumMas=TPropertyValue::ensureString($this->DDSumMaster->SelectedValue);
		$nmTable=$this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{	
			$idObat=$row['kode'];
			$sumber=$row['sumber'];
			$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tbt_stok_lain.tujuan='$tujuan'";
			$data=StokLainRecord::finder()->findBySql($sql);
			if($data===NULL)
			{
				//data baru untuk tbt_stok_lain
				$dataStokLain=new StokLainRecord;
				$dataStokLain->id_obat=$row['kode'];
				$dataStokLain->sumber=$row['sumber'];
				$dataStokLain->tujuan=$tujuan;
				$dataStokLain->jumlah=$row['jumlah'];
						
				//save ke tbt_stok_lain
				//$dataStokLain->save();
			}
			else
			{
				//update jumlah di tbt_stok_lain
				$data->jumlah=$row['jumlah'] + $data->getColumnValue('jumlah');
				//$data->save();
			}
			
			$nobbk=$this->numCounter(BbkObatRecord::finder());
			//data baru untuk tbt_bbk_barang
			$dataBbkObat=new BbkObatRecord;
			//$dataBbkObat->no_bbk=$this->numCounter('tbt_bbk_barang',BbkObatRecord::finder(),'50');
			$dataBbkObat->no_bbk=$nobbk;
			$dataBbkObat->kode_obat=$row['kode'];
			$dataBbkObat->jumlah=$row['jumlah'];
			$dataBbkObat->tgl=date("Y-m-d");
			$dataBbkObat->petugas=$this->nmPetugas->Text;
			$dataBbkObat->penerima=$this->nmPenerima->Text;
			$dataBbkObat->tujuan=$tujuan;
			$dataBbkObat->sumber=$row['sumber'];
			$dataBbkObat->keterangan=$this->ket->Text;
			
			//save ke tbt_bbk_barang
			$dataBbkObat->save();
			
			//Mengurangi stok barang di stok yang diambil
			$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='01' AND tbt_stok_lain.tujuan='$sumMas'";
			$data=StokLainRecord::finder()->findBySql($sql);
			$data->jumlah=$data->getColumnValue('jumlah') - $row['jumlah'];
			//update jumlah di tbt_stok_gudang
			$data->save();
		}				
		
		//$this->clearViewState('nmTable');//Clear the view state
		//$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat'));
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakBbkObat',array('nobbk'=>$nobbk,'petugas'=>$this->nmPetugas->Text,'tujuan'=>$tujuan,'penerima'=>$this->nmPenerima->Text,'table'=>$this->getViewState('nmTable'))));
	}
	
	public function batalClicked($sender,$param)
	{	
		$this->getViewState('nmTable');
		$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);
		
		$this->nmPetugas->Text='';
		$this->nmPenerima->Text='';
		$this->ket->Text='';
		$this->jmlAmbil->Text='';
		
		$this->form2->Visible=false;
		$this->cetakBtn->Enabled=false;
			
		$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
		$this->DDTujuan->dataBind();	
		
		$this->DDTujuan->Enabled=true;	
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
