<?php
class MasterStokGudang extends SimakConf
{   
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	/**
	* Returns a subset of data.
	* In MySQL database, this can be replaced by LIMIT clause
	* in an SQL select statement.
	* @param integer the starting index of the row
	* @param integer number of rows to be returned
	* @return array subset of data
	*/
	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariGol,$cariKlas,$cariDerivat,$cariPbf,$cariProd,$cariSat,$sumber)
	{
		if($pil == "1")
		{
			$sql = "SELECT 
					  tbm_obat.kode,
					  tbm_obat.nama,
					  tbm_obat.gol,
					  tbm_obat.klasifikasi,
					  tbm_obat.derivat,
					  tbm_obat.pbf,
					  tbm_obat.produsen,
					  tbm_obat.satuan,					  
					  tbt_stok_gudang.jumlah,
					  tbt_stok_gudang.sumber,
					  tbm_golongan_obat.nama AS golongan,
					  tbm_satuan_obat.nama AS satuan,
					  tbm_klasifikasi_obat.jenis AS klasifikasi,
					  tbm_pbf_obat.nama AS PBF,
					  tbm_produsen_obat.nama AS produsen
					FROM
					  tbm_obat
					  INNER JOIN tbt_stok_gudang ON (tbm_obat.kode = tbt_stok_gudang.id_obat)
					  INNER JOIN tbm_golongan_obat ON (tbm_obat.gol = tbm_golongan_obat.id)
					  INNER JOIN tbm_satuan_obat ON (tbm_obat.satuan = tbm_satuan_obat.kode)
					  INNER JOIN tbm_klasifikasi_obat ON (tbm_obat.klasifikasi = tbm_klasifikasi_obat.id)
					  INNER JOIN tbm_pbf_obat ON (tbm_obat.pbf = tbm_pbf_obat.id)
					  INNER JOIN tbm_produsen_obat ON (tbm_obat.produsen = tbm_produsen_obat.id) ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND tbm_obat.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND tbm_obat.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND tbm_obat.kode = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND tbm_obat.gol = '$cariGol' ";
			
			if($cariKlas <> '')			
				$sql .= "AND tbm_obat.klasifikasi = '$cariKlas' ";
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND tbm_obat.derivat = '$cariDerivat' ";				
				$sql .= "INNER JOIN tbm_derivat_obat ON (tbm_obat.derivat = tbm_derivat_obat.id) ";
				
			}
			
			if($cariPbf <> '')			
				$sql .= "AND tbm_obat.pbf = '$cariPbf' ";
			
			if($cariProd <> '')			
				$sql .= "AND tbm_obat.produsen = '$cariProd' ";
			
			if($cariSat <> '')			
				$sql .= "AND tbm_obat.satuan = '$cariSat' ";
			
			if($sumber <> '')
				if(strlen($sumber)>2)
				{
					$sql .= "AND tbt_stok_gudang.sumber = '$sumber' ";
				}
				else
				{
					$sql .= "AND tbt_stok_gudang.sumber LIKE '$sumber%' ";
				}
					
				
			//$sql .= " GROUP BY tbm_obat.kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT 
					  tbm_obat.kode,
					  tbm_obat.nama,
					  tbm_obat.gol,
					  tbm_obat.klasifikasi,
					  tbm_obat.derivat,
					  tbm_obat.pbf,
					  tbm_obat.produsen,
					  tbm_obat.satuan,					  
					  tbt_stok_gudang.jumlah,
					  tbt_stok_gudang.sumber,
					  tbm_golongan_obat.nama AS golongan,
					  tbm_satuan_obat.nama AS satuan,
					  tbm_klasifikasi_obat.jenis AS klasifikasi,
					  tbm_pbf_obat.nama AS PBF,
					  tbm_produsen_obat.nama AS produsen
					FROM
					  tbm_obat
					  INNER JOIN tbt_stok_gudang ON (tbm_obat.kode = tbt_stok_gudang.id_obat)
					  INNER JOIN tbm_golongan_obat ON (tbm_obat.gol = tbm_golongan_obat.id)
					  INNER JOIN tbm_satuan_obat ON (tbm_obat.satuan = tbm_satuan_obat.kode)
					  INNER JOIN tbm_klasifikasi_obat ON (tbm_obat.klasifikasi = tbm_klasifikasi_obat.id)
					  INNER JOIN tbm_pbf_obat ON (tbm_obat.pbf = tbm_pbf_obat.id)
					  INNER JOIN tbm_produsen_obat ON (tbm_obat.produsen = tbm_produsen_obat.id) ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND tbm_obat.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND tbm_obat.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND tbm_obat.kode = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND tbm_golongan_obat.nama = '$cariGol' ";
			
			if($cariKlas <> '')			
				$sql .= "AND tbm_klasifikasi_obat = '$cariKlas' ";
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND tbm_obat.derivat = '$cariDerivat' ";				
				$sql .= "INNER JOIN tbm_derivat_obat ON (tbm_obat.derivat = tbm_derivat_obat.id) ";
				
			}
			
			if($cariPbf <> '')			
				$sql .= "AND tbm_obat.pbf = '$cariPbf' ";
			
			if($cariProd <> '')			
				$sql .= "AND tbm_produsen_obat.nama = '$cariProd' ";
			
			if($cariSat <> '')			
				$sql .= "AND tbm_satuan_obat.nama = '$cariSat' ";
			
			if($sumber <> '')
				if(strlen($sumber)>2)
				{
					$sql .= "AND tbt_stok_gudang.sumber = '$sumber' ";
				}
				else
				{
					$sql .= "AND tbt_stok_gudang.sumber LIKE '$sumber%' ";
				}	
				
			//$sql .= " GROUP BY tbm_obat.kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					
		
		$page=$this->queryAction($sql,'S');
		 
		//$page=ObatRecord::finder()->findAllBySql($sql);
		
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;
		
		/*
		$criteria = new TActiveRecordCriteria;
		$criteria->Limit = $rows;
		$criteria->Offset = $offset;
		$page=KabupatenRecord::finder()->findAll($criteria);
		
		return $page;*/
	}
	
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onPreLoad($param)
	{
		
	}
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{	
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();	
			
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
							
			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');
			$sumber=$this->getViewState('sumber');			
			
			$this->UserGrid->VirtualItemCount=StokGudangRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->ID->focus();		
			
		}		
    }		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			$gol = $this->DDGol->SelectedValue;	
			$cariGol=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			
			if($cariGol!=NULL)
			{
				$this->DDKlas->DataSource=$cariGol;
				$this->DDKlas->dataBind(); 	
				$this->DDKlas->Enabled=true;
				
			}
			else
			{
				$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
				$this->DDKlas->dataBind();
				$this->DDKlas->Enabled=false;
			}
			
			
			
			
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue == '')
		{
			
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if($this->DDKlas->SelectedValue!='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$cariDerivat=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			
			if($cariDerivat!=NULL)
			{
				$this->DDDerivat->DataSource=$cariDerivat;
				$this->DDDerivat->dataBind(); 	
				$this->DDDerivat->Enabled=true;
				
			}
			else
			{
				$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
				$this->DDDerivat->dataBind();
				$this->DDDerivat->Enabled=false;
			}
			
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}
		
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue == '')
		{
			
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function DDPbfChanged($sender,$param)
	{	
		
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue == '')
		{
			
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function DDProdusenChanged($sender,$param)
	{	
		
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue == '')
		{
			
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function DDSatuanChanged($sender,$param)
	{	
		
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue == '')
		{
			
		}
		else
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	/**
     * Paging Control and Properties to specified pages.
     * This method responds to the datagrid's event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');	
		$sumber=$this->getViewState('sumber');	
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		$this->UserGrid->dataBind();		
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
           //$item->jml->TextBox->Columns=40;
		   $item->kode->Enabled=false;	
		   $item->nama->Enabled=false;
		   $item->satuan->Enabled=false;
		   $item->pbf->Enabled=false;
		   $item->sumberMaster->Enabled=false;
		   
		   $item->kode->TextBox->Columns=5;
		   $item->nama->TextBox->Columns=10;
		   $item->satuan->TextBox->Columns=10;
		   $item->pbf->TextBox->Columns=10;
		   $item->sumberMaster->TextBox->Columns=10;
		   $item->jml->TextBox->Columns=7;
        }

       
    }
	
	public function editItem($sender,$param)
    {
		 if ($this->User->IsAdmin)
		{
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;

			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');		
			$sumber=$this->getViewState('sumber');
			$this->UserGrid->VirtualItemCount=StokGudangRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
			// binds the data to interface components
			$this->UserGrid->dataBind();
		}	
    }
	
	public function cancelItem($sender,$param)
    {
        $this->UserGrid->EditItemIndex=-1;
		
       		$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');		
			$sumber=$this->getViewState('sumber');
			$this->UserGrid->VirtualItemCount=StokGudangRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
			// binds the data to interface components
			$this->UserGrid->dataBind();
    }
	
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//ObatRecord::finder()->FindByPk($ID);
		$idObat=$this->UserGrid->DataKeys[$item->ItemIndex];
		$sumberObat=$item->sumberMaster->TextBox->Text;
		
		$update=StokGudangRecord::finder()->find('id_obat = ? AND sumber = ?', $idObat, $sumberObat);
		
		$update->jumlah=$item->jml->TextBox->Text; 
		$update->save(); 
		
      //  $this->update(
       //     $this->UserGrid->DataKeys[$item->ItemIndex],    
       //     $item->jml->TextBox->Text
       //     );
        $this->UserGrid->EditItemIndex=-1;
        
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');		
		$sumber=$this->getViewState('sumber');
		$this->UserGrid->VirtualItemCount=StokGudangRecord::finder()->count();
		// fetches all data account information 
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		// binds the data to interface components
		$this->UserGrid->dataBind();
    }

	

	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		$this->UserGrid->dataBind();
	}

	
	
	/*public function showMe($sender,$param)
	{		
		$this->showUp->DataSource=KabupatenRecord::finder()->findAll();
		$this->showUp->dataBind();
	}*/
 
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');			
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		$this->UserGrid->dataBind();
	}
	
	/**
     * Sorting a specified user record.
     * This method responds to the datagrid's OnSortCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');		
		/*$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);
		$this->UserGrid->dataBind();	
	}
		
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		

		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
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
		
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	
	public function DDSumMasterChanged($sender,$param)
	{	
		$this->clearViewState('sumber');				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;	
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);	
		}
		
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
			
			if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
				if($this->getViewState('sumber')){
					$sumber = $this->getViewState('sumber');
				}else{
					$sumber = '';
				}
			}else{
				$this->clearViewState('sumber');	
			}
					
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
			$this->UserGrid->dataBind();
		
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		$this->clearViewState('sumber');
		
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
		/*
		if($this->DDSumMaster->SelectedValue || $this->DDSumSekunder->SelectedValue) {
			if($this->getViewState('sumber')){
				$sumber = $this->getViewState('sumber');
			}else{
				$sumber = '';
			}
		}else{
			$this->clearViewState('sumber');	
		}
		*/
		if($this->DDSumMaster->SelectedValue != '')
		
		{
			$sumber=$this->DDSumMaster->SelectedValue.$this->DDSumSekunder->SelectedValue;
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
}
?>
