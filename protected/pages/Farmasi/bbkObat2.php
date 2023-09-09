<?php
class bbkObat2 extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->form2->Visible=false;
			$this->cetakBtn->Enabled=false;
			
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();				
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
						
			$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat2',array('goto'=>'1')));
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
	}
	
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' ){
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;			
			
			$this->DDnmObat->DataSource=StokGudangRecord::finder()->findAll();
			$this->DDnmObat->dataBind(); 	
			$this->DDnmObat->Enabled=false;
			$this->clearViewState('sumber');
			//$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		elseif($this->DDSumMaster->SelectedValue == '01' ){
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;			
			
			$sumber=$this->DDSumMaster->SelectedValue;
			$cariData=StokGudangRecord::finder()->findAll('sumber = ?', $sumber);
			if(!$cariData)
			{
				$this->DDnmObat->DataSource=StokGudangRecord::finder()->findAll('sumber = ?', $sumber);
				$this->DDnmObat->dataBind(); 	
				//$this->DDnmObat->Text="Belum Ada Stok untuk barang ini";
				$this->DDnmObat->Enabled=false;
			}
			else
			{
				$sqlNmObat="SELECT tbm_obat.nama,tbm_obat.kode 
						FROM tbt_stok_gudang 
						INNER JOIN tbm_obat ON (tbt_stok_gudang.id_obat = tbm_obat.kode)
						WHERE tbt_stok_gudang.sumber = '$sumber'";
				$this->DDnmObat->DataSource=$this->queryAction($sqlNmObat,'S');
				$this->DDnmObat->dataBind(); 	
				$this->DDnmObat->Enabled=true;	
				
				$this->clearViewState('sumber');
				$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			}
					
		}		
		else
		{
			if($this->DDSumSekunder->SelectedValue == '' )
			{
				$this->DDnmObat->DataSource=StokGudangRecord::finder()->findAll();
				$this->DDnmObat->dataBind(); 	
				$this->DDnmObat->Enabled=false;
			}
			else
			{
				$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
				$sqlNmObat="SELECT tbm_obat.nama 
						FROM tbt_stok_gudang 
						INNER JOIN tbm_obat ON (tbt_stok_gudang.id_obat = tbm_obat.kode)
						WHERE tbt_stok_gudang.sumber = '$sumber'";
				$this->DDnmObat->DataSource=$this->queryAction($sqlNmObat,'S');
				$this->DDnmObat->dataBind(); 	
				$this->DDnmObat->Enabled=true;
				
				if($this->getViewState('sumber'))
				{
					$sumber = substr($this->getViewState('sumber'),0,2);				
					$sumber .=	$this->DDSumSekunder->SelectedValue;	
					$this->setViewState('sumber',$sumber);		
				}	
			}		
	
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
	}
	
	protected function DDSumSekunderChanged()
	{
		if($this->DDSumMaster->SelectedValue == '' ){
			$this->DDnmObat->DataSource=StokGudangRecord::finder()->findAll();
			$this->DDnmObat->dataBind(); 	
			$this->DDnmObat->Enabled=false;
			//$this->clearViewState('sumber');
			//$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
			$sqlNmObat="SELECT tbm_obat.nama,tbm_obat.kode 
						FROM tbt_stok_gudang 
						INNER JOIN tbm_obat ON (tbt_stok_gudang.id_obat = tbm_obat.kode)
						WHERE tbt_stok_gudang.sumber = '$sumber'";
			$this->DDnmObat->DataSource=$this->queryAction($sqlNmObat,'S');
			$this->DDnmObat->dataBind(); 	
			$this->DDnmObat->Enabled=true;
			
			if($this->getViewState('sumber'))
			{
				$sumber = substr($this->getViewState('sumber'),0,2);				
				$sumber .=	$this->DDSumSekunder->SelectedValue;	
				$this->setViewState('sumber',$sumber);		
			}	
		}		
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
		$sumber = $this->getViewState('sumber');
		$idObat=$this->DDnmObat->SelectedValue;
		$sql="SELECT * FROM tbt_stok_gudang WHERE id_obat='$idObat' AND sumber='$sumber'";
		$jmlGudang=StokGudangRecord::finder()->findBySql($sql);   
		$param->IsValid=($this->jmlAmbil->Text <= $jmlGudang->getColumnValue('jumlah'));
		
    }
	
	protected function ambilClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$this->cetakBtn->Enabled=true;
			$id_obat= $this->DDnmObat->SelectedValue;;
			$sumber = $this->getViewState('sumber');
			$jumlah=$this->jmlAmbil->Text;
			/*
			$sql="SELECT * FROM tbt_stok_gudang WHERE id_obat='$idObat' AND sumber='$sumber'";
			$jmlGudtemp=StokGudangRecord::finder()->findBySql($sql)->jumlah - $this->jmlAmbil->Text;   
			
			$this->setViewState('jmlGudtemp',$this->DDSumMaster->SelectedValue);
			*/	
			if (!$this->getViewState('nmTable'))
			{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 									 
										 id_obat VARCHAR(4) NOT NULL,
										 sumber VARCHAR(30) NOT NULL,				
										 jumlah INT(11) NOT NULL,				
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...					
			
			$sql="INSERT INTO $nmTable (id_obat,sumber,jumlah) VALUES ('$id_obat','$sumber','$jumlah')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();				
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql="INSERT INTO $nmTable (id_obat,sumber,jumlah) VALUES ('$id_obat','$sumber','$jumlah')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->UserGrid->dataBind();						
			}	
			
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
				
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();	
			$this->DDSumSekunder->Enabled=false;
					
			$this->DDnmObat->DataSource=StokGudangRecord::finder()->findAll();
			$this->DDnmObat->dataBind();
			$this->DDnmObat->Enabled=false;
			
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
		$nmTable=$this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{	
			$idObat=$row['id_obat'];
			$sumber=$row['sumber'];
			$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$idObat' AND sumber='$sumber' AND tujuan='$tujuan'";
			$data=StokLainRecord::finder()->findBySql($sql);
			if($data===NULL)
			{
				//data baru untuk tbt_stok_lain
				$dataStokLain=new StokLainRecord;
				$dataStokLain->id_obat=$row['id_obat'];
				$dataStokLain->sumber=$row['sumber'];
				$dataStokLain->tujuan=$tujuan;
				$dataStokLain->jumlah=$row['jumlah'];
						
				//save ke tbt_stok_lain
				$dataStokLain->save();
			}
			else
			{
				//update jumlah di tbt_stok_lain
				$data->jumlah=$row['jumlah'] + $data->getColumnValue('jumlah');
				$data->save();
			}
			
			$nobbk=$this->numCounter(BbkObatRecord::finder());
			//data baru untuk tbt_bbk_barang
			$dataBbkObat=new BbkObatRecord;
			//$dataBbkObat->no_bbk=$this->numCounter('tbt_bbk_barang',BbkObatRecord::finder(),'50');
			$dataBbkObat->no_bbk=$nobbk;
			$dataBbkObat->kode_obat=$row['id_obat'];
			$dataBbkObat->jumlah=$row['jumlah'];
			$dataBbkObat->tgl=date("Y-m-d");
			$dataBbkObat->petugas=$this->nmPetugas->Text;
			$dataBbkObat->penerima=$this->nmPenerima->Text;
			$dataBbkObat->tujuan=$tujuan;
			$dataBbkObat->sumber=$row['sumber'];
			$dataBbkObat->keterangan=$this->ket->Text;
			
			//save ke tbt_bbk_barang
			$dataBbkObat->save();
			
			//Mengurangi stok barang di gudang
			$sql="SELECT * FROM tbt_stok_gudang WHERE id_obat='$idObat' AND sumber='$sumber'";
			$data=StokGudangRecord::finder()->findBySql($sql);
			$data->jumlah=$data->getColumnValue('jumlah') - $row['jumlah'];
			
			//update jumlah di tbt_stok_gudang
			$data->save();
		}				
		
		//$this->clearViewState('nmTable');//Clear the view state
		//$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat2'));
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakBbkObat',array('nobbk'=>$nobbk,'petugas'=>$this->nmPetugas->Text,'tujuan'=>$tujuan,'penerima'=>$this->nmPenerima->Text,'table'=>$this->getViewState('nmTable'))));
	}
	
	public function batalClicked($sender,$param)
	{	
		$this->getViewState('nmTable');
		$this->Response->redirect($this->Service->constructUrl('Farmasi.bbkObat2') . '&purge=1&nmTable=' . $nmTable);
		
		$this->nmPetugas->Text='';
		$this->nmPenerima->Text='';
		$this->ket->Text='';
		$this->jmlAmbil->Text='';
		
		$this->form2->Visible=false;
		$this->cetakBtn->Enabled=false;
			
		$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
		$this->DDTujuan->dataBind();	
			
		$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
		$this->DDSumMaster->dataBind();	
			
		$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
		$this->DDSumSekunder->dataBind();
		
		$this->DDTujuan->Enabled=true;	
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
