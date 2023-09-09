<?php
class LapPenerimaanKasirRwtJlnBak2 extends SimakConf
{   
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			/*
			$this->DDPoli->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDPoli->dataBind();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
			
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			*/
			$sql="SELECT real_name, nip, allow FROM tbd_user";
			$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
			foreach($arr as $row)
			{
				$arrApp=array();
				$var=$row['allow'];
				$arrApp = explode(',', $var);
				
				if (in_array('2', $arrApp))
				{
					$data[]=array('nip'=>$row['nip'],'nama'=>$row['real_name']);
				}	
			}
			
			$this->DDKasir->DataSource = $data;
			$this->DDKasir->dataBind();
					
			$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
	}
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	// ---------- datagrid helper functions --------
    
    public function getSortExpression() {
        if ($this->getViewState('sortExpression',null)!==null) {
            return $this->getViewState('sortExpression');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('sortExpression', $this->sortExp);
        return $this->sortExp;
    }

    public function setSortExpression($sort) {
        $this->setViewState('sortExpression',$sort);
    }

    public function getSortDirection() {
        if ($this->getViewState('sortDirection',null)!==null) {
            return $this->getViewState('sortDirection');
        }
        // set default in case there's no 'sortDirection' key in viewstate
        $this->setViewState('sortDirection', $this->sortDir);
        return $this->sortDir;
    }

    public function setSortDirection($sort) {
        $this->setViewState('sortDirection',$sort);
    }

    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
		
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (
						id INT (2) auto_increment,
						tanggal date DEFAULT '0000-00-00',
						operator varchar (9) DEFAULT '0',
						biaya_adm int(11) DEFAULT '0',
						biaya_poli int(11) DEFAULT '0',
						tot_obat int(11) DEFAULT '0',
						tot_lab int(11) DEFAULT '0',
						tot_rad int(11) DEFAULT '0',
						tot_fisio int(11) DEFAULT '0',
						PRIMARY KEY (id)
					) ENGINE = MEMORY";
					
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$sql="SELECT * FROM view_rj_adm";
				$adm=count($this->queryAction($sql,'S'));
				$sql="SELECT * FROM view_rj_poli";
				$poli=count($this->queryAction($sql,'S'));
				$sql="SELECT * FROM view_rj_obat";
				$obat=count($this->queryAction($sql,'S'));
				$sql="SELECT * FROM view_rj_lab";
				$lab=count($this->queryAction($sql,'S'));
				$sql="SELECT * FROM view_rj_rad";
				$rad=count($this->queryAction($sql,'S'));
				$sql="SELECT * FROM view_rj_fisio";
				$fisio=count($this->queryAction($sql,'S'));
				
				//$obat=count($this->queryAction($sql,'S'));
				
				$a="view_rj_adm";$b="view_rj_poli";$c="view_rj_obat";
				$d="view_rj_lab";$e="view_rj_rad";$f="view_rj_fisio";				
				$jmlRec=array($adm,$poli,$obat,$lab,$rad,$fisio);
				$nmRec=array($a,$b,$c,$d,$e,$f);
				$test=array($a=>$adm,$b=>$poli,$c=>$obat,$d=>$lab,$e=>$rad,$f=>$fisio);
				rsort($jmlRec);
				$jml=count($jmlRec);
				$isi=array();			
				
				for ($i=0;$i<=$jml;$i++)
				{
					$key=array_search($jmlRec[$i],$test);
					array_push($isi,$key);
				}
				
				for ($j=0;$j<$jml;$j++)
				{
					if ($j==0)
					{
						$sql2="SELECT COLUMN_NAME
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE COLUMNS.TABLE_SCHEMA = 'simak'
							AND COLUMNS.TABLE_NAME = '$isi[$j]' LIMIT 0,1";
						$search=$this->queryAction($sql2,'S');
						foreach ($search AS $row)
						{
							$col=$row['COLUMN_NAME'];
						}
						
						$sql="INSERT INTO $nmTable ($col,tanggal,operator) (SELECT * FROM $isi[$j])";
						$this->queryAction($sql,'C');
					}else{
						$sql2="SELECT COLUMN_NAME
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE COLUMNS.TABLE_SCHEMA = 'simak'
							AND COLUMNS.TABLE_NAME = '$isi[$j]' LIMIT 0,1";
						$search=$this->queryAction($sql2,'S');
						foreach ($search AS $row)
						{
							$col=$row['COLUMN_NAME'];
						}
						$sql="UPDATE $nmTable,$isi[$j] SET $nmTable.$col=$isi[$j].$col WHERE $nmTable.tanggal=$isi[$j].tgl AND $nmTable.operator=$isi[$j].operator";
						$this->queryAction($sql,'C');
					}
				}
				
				$sql="SELECT * FROM $nmTable ORDER BY id";				
				$arr=$this->queryAction($sql,'R');
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');				
								
				$sql="SELECT * FROM $nmTable ORDER BY id";				
				$arr=$this->queryAction($sql,'R');
			}
			
			$nmTable = $this->getViewState('nmTable');		
			
			$sql="SELECT * FROM $nmTable, tbd_user WHERE $nmTable.operator!='0' AND $nmTable.operator= tbd_user.nip ";
			
			if($this->getViewState('cariByKasir') <> '')	
			{
				$cariKasir=$this->getViewState('cariByKasir');
				$sql .= "AND $nmTable.operator = '$cariKasir' ";		
			}	
			
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND $nmTable.tanggal = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND $nmTable.tanggal BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH ($nmTable.tanggal)='$cariBln' AND YEAR($nmTable.tanggal)='$cariThn' ";
			}
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
        }
    }
    
    // ---------- datagrid page and sort events ---------------
    
    protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
        
        
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
   
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function changePagerPosition($sender,$param)
	{		
		$position='TopAndBottom';		
		$this->UserGrid->PagerStyle->Position=$position;
		$this->UserGrid->PagerStyle->Visible=true;		
	}
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jml->TextBox->Columns=5;
		   //$item->nama->Enabled=false;
		   //$item->sat->Enabled=false;
		   //$item->pbf->Enabled=false;
		   //$item->sumber->Enabled=false;
		   //$item->sumberSekunder->Enabled=false;
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
			
			KasirRwtJlnRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
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
		$orderBy=$this->getViewState('orderBy');	
		
		/*
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);			
		}else{
			$this->clearViewState('cariByNama');	
		}					
		*/
		if($this->DDKasir->SelectedValue) {
			$this->setViewState('cariByKasir',$this->DDKasir->SelectedValue);
		}else{
			$this->clearViewState('cariByKasir');	
		}
		/*		
		if($this->DDPoli->SelectedValue) {
			$this->setViewState('cariPoli',$this->DDPoli->SelectedValue);
		}else{
			$this->clearViewState('cariPoli');	
		}
		
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('cariDokter');	
		}*/
		
		if($this->tgl->Text){
			$this->setViewState('cariTgl',$this->ConvertDate( $this->tgl->Text,2));
		}else{
			$this->clearViewState('cariTgl');	
		}	
		
		if($this->tglawal->Text){
			$this->setViewState('cariTglAwal',$this->ConvertDate( $this->tglawal->Text,2));
		}else{
			$this->clearViewState('cariTglAwal');	
		}	
		
		if($this->tglakhir->Text){
			$this->setViewState('cariTglAkhir',$this->ConvertDate( $this->tglakhir->Text,2));
		}else{
			$this->clearViewState('cariTglAkhir');	
		}	
		
		if($this->getViewState('cariThn')){
			$cariThn = $this->getViewState('cariThn');
		}else{
			$this->clearViewState('cariThn');
		}		
		
		if($this->getViewState('cariBln')){
			$cariBln = $this->getViewState('cariBln');
		}else{
			$this->clearViewState('cariBln');
		}		
		
		
		
		$this->bindGrid();	
		
		$this->dataGrid->Visible=true;
		
		if($this->getViewState('pilihPeriode')==='1')
		{
			$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
		}
		elseif($this->getViewState('pilihPeriode')==='2')
		{
			$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
		}
		elseif($this->getViewState('pilihPeriode')==='3')
		{
			if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
			}
			
		}
		
	}
		
	public function DDKasirChanged($sender,$param)
	{				
		//$this->DDPoli->focus();
		
		$this->cariClicked();
	}
	
	public function DDPoliChanged($sender,$param)
	{				
		//$this->DDDokter->focus();
		
		$this->cariClicked();
	}
	
	public function DDDokterChanged($sender,$param)
	{				
		//$this->DDberdasarkan->focus();
		
		$this->cariClicked();
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->DDUrut->SelectedValue=='05')
		{
			$this->DDKontrak->Enabled=true;
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDKontrak->focus();
		}
		else
		{
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDKontrak->Enabled=false;
			
			//$this->cariClicked();
		}
		$this->cariClicked();						
	}
	
	public function DDKontrakChanged($sender,$param)
	{		
		$this->cariClicked();				
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
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
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
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
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
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$this->cariClicked();
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
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
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
		
		$this->cariClicked();
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
	
	public function cetakClicked()
	{		
			$cariByKasir=$this->getViewState('cariByKasir');			
			$cariPoli=$this->getViewState('cariPoli');		
			$cariDokter=$this->getViewState('cariDokter');	
			$cariTgl=$this->getViewState('cariTgl');	
			$cariTglAwal=$this->getViewState('cariTglAwal');		
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariThn = $this->getViewState('cariThn');
			$cariBln = $this->getViewState('cariBln');
			
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtJln',
			array(
				'kasir'=>$cariByKasir,
				'poli'=>$cariPoli,
				'dokter'=>$cariDokter,
				//'kelompok'=>$this->DDUrut->SelectedValue,
				//'perusahaan'=>$this->DDKontrak->SelectedValue,
				'tgl'=>$cariTgl,
				'tglawal'=>$cariTglAwal,
				'tglakhir'=>$cariTglAkhir,
				'cariBln'=>$cariBln,
				'cariThn'=>$cariThn,
				'periode'=>$this->txtPeriode->Text)));
		
	}		
	protected function refreshMe()
	{
		$this->Reload();
	}
}

/*	
$a="view_rj_adm";$b="view_rj_poli";$c="view_rj_obat";$d="view_rj_lab";$e="view_rj_rad";	
	//masukkan nama view ke dalam array
$jmlRec=array($adm,$poli,$obat,$lab,$rad); // jml record dari tiap view dimasukkan ke array
$nmRec=array($a,$b,$c,$d,$e); // nama view dimasukkan ke array
$test=array($a=>$adm,$b=>$poli,$c=>$obat,$d=>$lab,$e=>$rad); 
	//penggabungan antara nama view dgn juml recnya
rsort($jmlRec); // pensortiran besar ke kecil
$jml=count($jmlRec); //hitug jml isi array
$isi=array(); //initialisasi array 

for ($i=0;$i<=$jml;$i++)
{
	$key=array_search($jmlRec[$i],$test);
		// cocokkan antara hasil pensortiran dgn nama viewnya
	array_push($isi,$key);
		// Masukkan ke dalam array isi
}

for ($j=0;$j<$jml;$j++)
{
	if ($j==0)// jika j=0 maka view pertama masukkan ke dalam nama kolom
	{
		$sql2="SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE COLUMNS.TABLE_SCHEMA = 'simak'
			AND COLUMNS.TABLE_NAME = '$isi[$j]' LIMIT 0,1";
				//select nama view yg ada di db simak
		$search=$this->queryAction($sql2,'S');
		foreach ($search AS $row)
		{
			$col=$row['COLUMN_NAME'];
		}
		
		$sql="INSERT INTO $nmTable ($col,tanggal,operator) (SELECT * FROM $isi[$j])";
		$this->queryAction($sql,'C');
	}else{// selanjutnya, di update
		$sql2="SELECT COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE COLUMNS.TABLE_SCHEMA = 'simak'
			AND COLUMNS.TABLE_NAME = '$isi[$j]' LIMIT 0,1";
		$search=$this->queryAction($sql2,'S');
		foreach ($search AS $row)
		{
			$col=$row['COLUMN_NAME'];
		}
		$sql="UPDATE $nmTable,$isi[$j] SET $nmTable.$col=$isi[$j].$col WHERE $nmTable.tanggal=$isi[$j].tgl AND $nmTable.operator=$isi[$j].operator";
		$this->queryAction($sql,'C');
	}
}
*/
?>
