<?php
class MasterObatEdit extends SimakConf
{
 	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		/*	
		$sql = "SELECT * FROM tbm_destinasi_farmasi";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlMin = 'min_'.$row['id'];
				$jmlTol = 'tol_'.$row['id'];
				$jmlMax = 'max_'.$row['id'];
				$tujuan = $row['nama'];
				
				$sql = "ALTER TABLE tbm_obat ADD $jmlMin FLOAT DEFAULT '0' COMMENT 'Jumlah Minimal Obat di $tujuan'";
				$this->queryAction($sql,'C');
				
				$sql = "ALTER TABLE tbm_obat ADD $jmlTol FLOAT DEFAULT '0' COMMENT 'Toleransi (%)  dari Jumlah Minimal Obat di $tujuan'";
				$this->queryAction($sql,'C');
				
				$sql = "ALTER TABLE tbm_obat ADD $jmlMax FLOAT DEFAULT '0' COMMENT 'Jumlah Maksimal Obat di $tujuan'";
				$this->queryAction($sql,'C');
				
			}
			
			$tmpAR = new ActiveRecordGen();
 			$tmpAR->executeAR('ObatRecord','tbm_obat','simak');	
			*/
	 }
	 
	public function onPreLoad($param)
	{/*
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		
		if($this->getViewState('offset'))
		{
			$this->clearViewState('offset');	
		}
		*/
		if(!$this->IsPostBack && !$this->IsCallBack)  // if the page is initially requested
		{
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->RBkelObat->DataSource = ObatKelompokRecord::finder()->findAll($criteria);
			$this->RBkelObat->dataBind(); 
			
			$this->DDkelMargin->DataSource = ObatKelompokMarginRecord::finder()->findAll($criteria);
			$this->DDkelMargin->dataBind(); 
						
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll($criteria);
			$this->DDGol->dataBind(); 
			
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll($criteria);
			$this->DDJenisBrg->dataBind(); 
			
			$criteria2 = new TActiveRecordCriteria;												
			$criteria2->OrdersBy['jenis'] = 'asc';
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll($criteria2);
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll($criteria);
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll($criteria);
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll($criteria);
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll($criteria);
			$this->DDSat->dataBind();	
			
			$dataObatRecord=$this->ObatRecord;
			$this->RBtipeObat->SelectedIndex = $dataObatRecord->tipe;
			
			
			$this->ID->Text=$dataObatRecord->kode;
			$this->nama->Text=$dataObatRecord->nama;
			
			 $sql = "SELECT kode, nama					   
					FROM tbm_obat
					WHERE							
						st='0'
					ORDER BY 
						nama ASC ";
			$arr=$this->queryAction($sql,'S');
			$jmlData=1;
			foreach($arr as $row)
			{			
				if($row['kode'] == $this->ID->Text)
				{
					//$this->setViewState('offset',$jmlData);
					$session=$this->Application->getModule('session');		
					$session['offsetEdit'] = $jmlData;
				}		
				$jmlData++;
			}
			
			if(isset($dataObatRecord->kel_margin))
				$this->DDkelMargin->SelectedValue=$dataObatRecord->kel_margin;	
				
			if(isset($dataObatRecord->kel_obat))
				$this->RBkelObat->SelectedValue=$dataObatRecord->kel_obat;	
				
			if(isset($dataObatRecord->kel_standard))
				$this->RBstandar->SelectedValue=$dataObatRecord->kel_standard;		
				
			if($dataObatRecord->gol)
			{
				$this->DDGol->SelectedValue=$dataObatRecord->gol;						
				$this->DDKlas->Enabled = true;
			}
			else
			{
				$this->DDKlas->Enabled = false;
			}
			
			if($dataObatRecord->klasifikasi)
			{
				$this->DDKlas->SelectedValue=$dataObatRecord->klasifikasi;
				$this->DDDerivat->Enabled = true;				
			}
			else
			{
				$this->DDDerivat->Enabled = false;	
			}
			
			
			if($dataObatRecord->derivat)
				$this->DDDerivat->SelectedValue=$dataObatRecord->derivat;				
			if($dataObatRecord->pbf)
				$this->DDPbf->SelectedValue=$dataObatRecord->pbf;				
			if($dataObatRecord->produsen)
				$this->DDProd->SelectedValue=$dataObatRecord->produsen;				
			if($dataObatRecord->satuan)
				$this->DDSat->SelectedValue=$dataObatRecord->satuan;				
			
			if($dataObatRecord->jml_satuan_besar)
				$this->satuanBesar->Text=$dataObatRecord->jml_satuan_besar;

			if($dataObatRecord->kategori)
				$this->DDJenisBrg->SelectedValue=$dataObatRecord->kategori;	
				if ($dataObatRecord->kategori =='01')
				{
					$this->RBtipeObat->enabled=true;
				}else
				{
					$this->RBtipeObat->enabled=false;
				}
			
			$this->fee->Text=$dataObatRecord->persentase_dokter;

			$this->DDJenisBrg->Focus();
			
			$this->bindGrid();
			
		}
	} 	 					
	
	public function panelRender($sender, $param)
	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	private function bindGrid()
    {
	   $sql = "SELECT * FROM tbm_destinasi_farmasi";
				
		$arr = $this->queryAction($sql,'S');
	  
		$this->Repeater->DataSource=$arr;
		$this->Repeater->dataBind();
    }
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$nm_tujuan = $item->DataItem['nama'];
			$id_tujuan = $item->DataItem['id'];
			
			$item->txtTujuan->Text = $nm_tujuan;
			$kode = $this->getViewState('ID');
			
			$item->nmFieldMin->Value = 'min_'.$id_tujuan;
			$item->nmFieldTol->Value = 'tol_'.$id_tujuan;
			$item->nmFieldMax->Value = 'max_'.$id_tujuan;
				
			$sql = "SELECT * FROM tbm_obat WHERE kode='$kode' AND st='0' ";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$item->jmlMin->Text = $row['min_'.$id_tujuan];	
				$item->toleransi->Text = $row['tol_'.$id_tujuan];	
				$item->jmlMax->Text = $row['max_'.$id_tujuan];	
			}
        }
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
		
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			
			if(count(KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol)) > 0)
			{
				$this->DDKlas->dataBind(); 	
				$this->DDKlas->Enabled=true;
			}
			else
			{
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
		
		
		$this->DDKlas->SelectedIndex = -1;
	}
	
	
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			
			if(count(DerivatObatRecord::finder()->findAll('klas_id = ?', $klas)) > 0)
			{
				$this->DDDerivat->dataBind(); 	
				$this->DDDerivat->Enabled=true;
			}
			else
			{
				$this->DDDerivat->Enabled=false;
			}
			
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		
		
		$this->DDDerivat->SelectedIndex = -1;
	}
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}		
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$dataObatRecord=$this->ObatRecord;   
			$dataObatRecord->tipe=TPropertyValue::ensureString($this->collectSelectionListResult($this->RBtipeObat));        
			$dataObatRecord->kode=ucwords($this->ID->Text);
			//$dataObatRecord->kel_obat=TPropertyValue::ensureString($this->collectSelectionListResult($this->RBkelObat)); 
			$dataObatRecord->kel_obat = $this->RBkelObat->SelectedValue; 
			$dataObatRecord->kel_margin = $this->DDkelMargin->SelectedValue; 
			$dataObatRecord->kel_standard = $this->RBstandar->SelectedValue; 
           // $dataObatRecord->nama=mysql_real_escape_string(ucwords($this->nama->Text));
		    $dataObatRecord->nama=ucwords($this->nama->Text);
			$dataObatRecord->kategori=(string)$this->DDJenisBrg->SelectedValue;            
  			$dataObatRecord->gol=(string)$this->DDGol->SelectedValue;            
			$dataObatRecord->klasifikasi=(string)$this->DDKlas->SelectedValue;
			$dataObatRecord->derivat=(string)$this->DDDerivat->SelectedValue;
			$dataObatRecord->produsen=(string)$this->DDProd->SelectedValue;
			$dataObatRecord->pbf=(string)$this->DDPbf->SelectedValue;
			$dataObatRecord->satuan=(string)$this->DDSat->SelectedValue;
			$dataObatRecord->jml_satuan_besar=$this->satuanBesar->Text;
			$dataObatRecord->persentase_dokter=floatval($this->fee->Text);
			$dataObatRecord->save(); 
			
			//UPDATE jml min, toleransi, max obat
			foreach($this->Repeater->Items as $item)
			{
				$kode = $dataObatRecord->kode;
				$nmFieldMin = $item->nmFieldMin->Value;
				$nmFieldTol = $item->nmFieldTol->Value;
				$nmFieldMax = $item->nmFieldMax->Value;
				
				$jmlMin = $item->jmlMin->Text;			
				$toleransi = $item->toleransi->Text;			
				$jmlMax = $item->jmlMax->Text;	
				
				$sql = "UPDATE tbm_obat SET $nmFieldMin = '$jmlMin', $nmFieldTol = '$toleransi', $nmFieldMax = '$jmlMax'  WHERE kode='$kode'";						
				$this->queryAction($sql,'C');
			}	
			
			// saves to the database via Active Record mechanism
           			
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			//$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat',
			//array(
			//	'offsetEdit'=>ceil($this->getViewState('offset')/10)
			//	)));
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent(); alert(\'Pastikan data semua data telah di isi !\')');
		}		
	}
	
		
	protected function getObatRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$ObatRecord=ObatRecord::finder()->findByPk($ID);		
		if(!($ObatRecord instanceof ObatRecord))
			throw new THttpException(500,'id tidak benar.');
		return $ObatRecord;
		
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));		
	}
}
?>
