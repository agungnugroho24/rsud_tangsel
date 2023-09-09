<?php
class MasterStokGudangBaru extends SimakConf
{   	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	

   	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();
			
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();			
			
			//$this->makeTmpTbl();									
			$this->DDTujuan->focus();	
			
		}else{
			$this->DDTujuan->focus();
		}
		
		$purge=$this->Request['purge'];
		$nmTable=$this->Request['nmTable'];
		
		if($purge =='1'	)
		{			
			if(!empty($nmTable))
			{		
				$this->queryAction($nmTable,'D');//Droped the table						
				$this->dtgSomeData->DataSource='';
				$this->dtgSomeData->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			$this->Response->redirect($this->Service->constructUrl('Apotik.MasterStokGudangBaru',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{			
			$this->DDTujuan->Focus();
		}
		
	}		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else
		{
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			$this->DDKlas->Enabled=false;
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}
		$this->cariClicked();
		
	}
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->Enabled=false;
		}	
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		$this->cariClicked();
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();	
		$this->cariClicked();
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		$this->cariClicked();
	}
	
	public function DDTujuanChanged($sender,$param)
	{
		$this->cariClicked();
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
    
	private function makeTmpTbl()
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
			
			$cariNama=$this->getViewState('cariByNama');
			$cariID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariGol=$this->getViewState('cariByGol');
			$cariKlas=$this->getViewState('cariByKlas');
			$cariDerivat=$this->getViewState('cariByDerivat');
			$cariPbf=$this->getViewState('cariByPbf');
			$cariProd=$this->getViewState('cariByProd');
			$cariSat=$this->getViewState('cariBySat');
			//$sumber=$this->getViewState('sumber');			
			$tujuan=$this->getViewState('tujuan');
			$tipe=$this->getViewState('tipe');
			$kategori=$this->getViewState('kategori');
			
            $sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						   c.hrg_ppn AS hrg,
						   c.id AS id_harga,
						    ";
			if($sumber <> '')
			{
				$sql .= "b.jumlah AS jumlah,
						   b.sumber AS sumber ";				
			}else{
				$sql .= "SUM(b.jumlah) AS jumlah,
						   b.sumber AS sumber ";
			}	
			
			$sql .=	" FROM tbm_obat a,							
							tbt_stok_lain b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							b.id_harga=c.id AND 
							b.tujuan='$tujuan' 
							AND a.kode=c.kode ";
							
			/*		   
			//$tujuan=$this->getViewState('tujuan');
			if ($this->getViewState('tujuan') == '1')
			{
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_gudang b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							c.kode=a.kode  ";
			}else{									   
				//$tujuan='2';
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_lain b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							b.id_harga=c.id AND 
							b.tujuan='$tujuan' 
							AND a.kode=c.kode ";				
			}
			*/
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.kode = '$cariID' ";
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";
			
			if($kategori <> '')			
				$sql .= "AND a.kategori = '$kategori' ";		
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
				
			$sql .= " GROUP BY b.id_harga ";			
			
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$kode = $row['kode'];
				$nama = $row['nama'];
				$pbf = $row['pbf'];
				$sat = $row['sat'];
				$hrg = $row['hrg'];
				$id_harga = $row['id_harga'];
				$jumlah = $row['jumlah'];
				$sumber = $row['sumber'];
				$saldo_rp_sistem = $jumlah * $hrg;
				$jml_fisik = '0';
				$saldo_rp_fisik = '0';
				$selisih_jml = 0 - $jumlah;
				$selisih_rp = 0 - $saldo_rp_sistem;
				
				if (!$this->getViewState('nmTable'))
				{
					$nmTable = $this->setNameTable('nmTable');
					$sql="CREATE TABLE
							$nmTable (id INT (11) auto_increment, 
						 kode VARCHAR(5) DEFAULT NULL,
						 nama VARCHAR(255) DEFAULT NULL,
						 pbf VARCHAR(4) DEFAULT NULL,
						 sat VARCHAR(4) DEFAULT NULL,
						 hrg FLOAT(15,2) DEFAULT NULL,
						 id_harga INT(11) DEFAULT NULL,
						 jumlah INT(11) DEFAULT NULL,
						 sumber VARCHAR(4) DEFAULT NULL,
						 saldo_rp_sistem FLOAT DEFAULT NULL,
						 jml_fisik INT(11) DEFAULT NULL,
						 saldo_rp_fisik FLOAT(15,2) DEFAULT NULL,
						 selisih_jml INT(11) DEFAULT NULL,
						 selisih_rp FLOAT(15,2) DEFAULT NULL,		
						 PRIMARY KEY (id)) ENGINE = MEMORY";
						 
					$this->queryAction($sql,'C');//Create new tabel bro...
															
					$sql="INSERT INTO $nmTable (
							kode,
							nama,
							pbf,
							sat,
							hrg,
							id_harga,
							jumlah,
							sumber,
							saldo_rp_sistem,
							jml_fisik,
							saldo_rp_fisik,
							selisih_jml,
							selisih_rp) 
						  VALUES (
							'$kode',
							'$nama',
							'$pbf',
							'$sat',
							'$hrg',
							'$id_harga',
							'$jumlah',
							'$sumber',
							'$saldo_rp_sistem',
							'$jml_fisik',
							'$saldo_rp_fisik',
							'$selisih_jml',
							'$selisih_rp'
							)";
					
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
					$sql="SELECT * FROM $nmTable GROUP BY id_harga ";		
				}
				else
				{
					$nmTable = $this->getViewState('nmTable');
					$sql="INSERT INTO $nmTable (
							kode,
							nama,
							pbf,
							sat,
							hrg,
							id_harga,
							jumlah,
							sumber,
							saldo_rp_sistem,
							jml_fisik,
							saldo_rp_fisik,
							selisih_jml,
							selisih_rp) 
						  VALUES (
							'$kode',
							'$nama',
							'$pbf',
							'$sat',
							'$hrg',
							'$id_harga',
							'$jumlah',
							'$sumber',
							'$saldo_rp_sistem',
							'$jml_fisik',
							'$saldo_rp_fisik',
							'$selisih_jml',
							'$selisih_rp'
							)";
					
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
					$sql="SELECT * FROM $nmTable GROUP BY id_harga ";							
				}	
			}
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
			$this->showSql->Visible=false;
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			if($someDataList->getSomeDataCount($sql) == '0')
			{
				$this->cetakBtn->Enabled = false;
				
				if($this->getViewState('nmTable'))
				{		
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
					$this->clearViewState('nmTable');//Clear the view state	
				}
			}
			else
			{
				$this->cetakBtn->Enabled = true;
			}
			/*
			if ($this->getViewState('sql')) 				
			{
				$this->clearViewState('sql');
			}
			
			$this->setViewState('sql',$sql);
			*/
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
        }
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
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable GROUP BY id_harga ";	
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
			$this->showSql->Visible=false;
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			if($someDataList->getSomeDataCount($sql) == '0')
			{
				$this->cetakBtn->Enabled = false;
				
				if($this->getViewState('nmTable'))
				{		
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
					$this->clearViewState('nmTable');//Clear the view state	
				}
			}
			else
			{
				$this->cetakBtn->Enabled = true;
			}
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
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jml->TextBox->Columns=5;
		   $item->harga->TextBox->Columns=5;		   
		   $item->jml_fisik->TextBox->Columns=5;
        }       
    }
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			 $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
			$this->bindGrid(); 
			
		}	
    }
	
	public function cancelItem($sender,$param)
    {        
		$this->dtgSomeData->EditItemIndex=-1;  
		$this->bindGrid();
    }
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//ObatRecord::finder()->FindByPk($ID);
		$id = $this->dtgSomeData->DataKeys[$item->ItemIndex];
		
		$nmTable = $this->getViewState('nmTable');	
		$sql="SELECT * FROM $nmTable WHERE id = '$id' ";	
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$idHarga = $row['id_harga'];
			$jmlSistem = $row['jumlah'];
			$saldo_rp_sistem = $row['saldo_rp_sistem'];
			$hrg = $row['hrg'];
		}	
		
		$sumberObat = $this->getViewState('sumber');
		
		$tujuan=$this->getViewState('tujuan');
		$jumlah = $item->jml->TextBox->Text;
		$harga = $item->harga->TextBox->Text;
		
		if($harga > 0)
		{
			$hrgNett = $harga/1.1;
		}else{
			$hrgNett = '0';
		}
		
		$sql="UPDATE tbt_obat_harga SET hrg_ppn='$harga', hrg_netto='$hrgNett' WHERE id='$idHarga' AND sumber='01'";
		$this->queryAction($sql,'C');		
		
		$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah' WHERE id_harga='$idHarga' AND  tujuan = '$tujuan'";
		$this->queryAction($sql,'C');
		
		$saldo_rp_sistem = $jumlah * $harga;
		
		$sql="UPDATE $nmTable SET hrg='$harga',jumlah='$jumlah',saldo_rp_sistem='$saldo_rp_sistem' WHERE id = '$id' ";
		$this->queryAction($sql,'C'); 
		
		
		$sql="SELECT * FROM $nmTable WHERE id = '$id' ";	
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$idHarga = $row['id_harga'];
			$jmlSistem = $row['jumlah'];
			$saldo_rp_sistem = $row['saldo_rp_sistem'];
			$hrg = $row['hrg'];
		}	
		
		$jmlFisik = $item->jml_fisik->TextBox->Text;
		$saldo_rp_fisik = $jmlFisik * $hrg;
		$selisih_jml = $jmlFisik - $jmlSistem;
		$selisih_rp = $saldo_rp_fisik - $saldo_rp_sistem;
		
		$sql="UPDATE $nmTable SET 
				jml_fisik='$jmlFisik', 
				saldo_rp_fisik='$saldo_rp_fisik', 
				selisih_jml='$selisih_jml', 
				selisih_rp='$selisih_rp'
			  WHERE id='$id' ";
				
		$this->queryAction($sql,'C');
     	$this->setViewState('sumber',$sumber);	
        $this->dtgSomeData->EditItemIndex=-1;  
		$this->bindGrid();
		
    }
	
    public function deleteButtonClicked($sender,$param)

    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Apotik.MasterObatBaru'));
			
		}	
    }	

		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);	
		$this->bindGrid();
		
	}
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Apotik.MasterObatBaru'));		
	}
	
	public function cariClicked()
	{	
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
		$orderBy=$this->getViewState('orderBy');	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDGol->SelectedValue) {
			$this->setViewState('cariByGol', $this->DDGol->SelectedValue);
		}else{
			$this->clearViewState('cariByGol');	
		}
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}		
		
		$this->makeTmpTbl();
	}
		
	public function DDPbfChanged($sender,$param)
	{			
		$this->cariClicked();
	}
		
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			//$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
		
		$this->cariClicked();
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	
		
		$this->cariClicked();
	}
	
	protected function refreshMe()
	{
		$this->Reload();
	}
	
	public function cetakClicked($sender,$param)
	{
		//$session=new THttpSession;
		//$session->open();
		//$session['cetakStokSql'] = $this->getViewState('sql');
		
		$tujuan = $this->DDTujuan->SelectedValue;
		$jnsBarang = $this->DDJenisBrg->SelectedValue;
		$tipe = $this->collectSelectionResult($this->RBtipeObat);
		$nmTable = $this->getViewState('nmTable');
				
		$this->Response->redirect($this->Service->constructUrl('Apotik.cetakStokGudangBaru',array('tujuan'=>$tujuan,'jnsBarang'=>$jnsBarang,'tipe'=>$tipe,'nmTable'=>$nmTable)));
	}
	
}
?>
