<?php
class latihView extends SimakConf
{
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariCM,$tipeCari,$cariAlamat)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel AS jenkel,
						   a.alamat,
						   a.kecamatan
						   FROM du_pasien a,
						   		tbm_kecamatan b
						  WHERE a.kecamatan<>'kecamatan' ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
						
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
				
			if($cariCM <> '')
				$sql .= "AND a.cm = '$cariCM' ";
			
			$sql .= " GROUP BY a.cm ";
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel AS jenkel,
						   a.alamat,
						   a.kecamatan
						   FROM du_pasien a,
						   		tbm_kecamatan b
						   WHERE a.kecamatan<>'kecamatan' ";
						   
			if($cariNama <> '')
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
			
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
							
			if($cariCM <> '')			
				$sql .= "AND a.cm = '$cariCM' ";
				
			
			
			$sql .= " GROUP BY a.cm ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		
		$page = $sql;
		//$this->showSql->Text=$sql;//show SQL Expression broer!
		//$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;
		
	} 
	
	public function onInit($param)
	{
		parent::onInit($param);
		
		//$session=$this->Application->getModule('session');
		//$bhs=$session['lang'];	
		//$arr=LatihUploadRecord::finder()->findAll();
		$this->Repeater->VirtualItemCount=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat),'S'));
		$this->populateData();	
	}
	/*
	public function onInit($param)
	{
		parent::onInit($param);
		if(!$this->IsPostBack)  // if the page is requested the first time
		{
			// get the total number of posts available
			$this->Repeater->VirtualItemCount=LatihUploadRecord::finder()->count();
			// populates post data into the repeater
			$this->populateData();
		}
	}
	*/
	
	public function pageChanged($sender,$param)
	{
		// change the current page index to the new one
		$this->Repeater->CurrentPageIndex=$param->NewPageIndex;
		// re-populate data into the repeater
		$this->populateData();
	}
	
	protected function populateData()
	{
		$offset=$this->Repeater->CurrentPageIndex*$this->Repeater->PageSize;
		$limit=$this->Repeater->PageSize;
		if($offset+$limit>$this->Repeater->VirtualItemCount)
		{
			$limit=$this->Repeater->VirtualItemCount-$offset;
		}
		
		$this->Repeater->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat),'S');
		$this->Repeater->dataBind();
	}
	/*
	protected function getPosts($offset, $limit)
	{
		$session=$this->Application->getModule('session');
		$bhs=$session['lang'];
		// Construts a query criteria
		$criteria=new TActiveRecordCriteria;
		$criteria->OrdersBy['id']='asc';
		//$criteria->Condition ="bhs= '$bhs'";
		$criteria->Limit=$limit;
		$criteria->Offset=$offset;
		// query for the posts with the above criteria and with author information
		return LatihUploadRecord::finder()->findAll($criteria);
	}
	*/
	
}
?>
