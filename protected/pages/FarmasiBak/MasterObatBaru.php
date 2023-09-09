<?php
class MasterObatBaru extends SimakConf
{
 	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		
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
			
			$this->RBkelObat->DataSource=ObatKelompokRecord::finder()->findAll($criteria);
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
			
			$this->bindGrid();
		}
	} 	 					
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $sender->DataSource=$this->getDummyData($token);
        $sender->dataBind();                                                                                                     
    }
	
	public function getDummyData($token) 
	{
		$sql = "SELECT kode,nama FROM tbm_obat WHERE nama LIKE '$token%' GROUP BY kode ORDER BY nama ASC "; 			
		$arr = $this->queryAction($sql,'S');
		
		return $arr;
    }
	
	public function panelRender($sender, $param)
	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(ObatRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function nextPoin()
    {  
		$sql="SELECT kode FROM tbm_obat ORDER BY kode DESC";
		$no = ObatRecord::finder()->findBySql($sql);
			
		if($no==NULL)//jika kosong bikin ndiri
		{	
			$urut='00001';
			$urut=$codeAwal.$urut;
		}else
		{
			$no1=intval(substr($no->getColumnValue('kode'),-5,5));
			if ($no1==99999)
			{
				$urut='00001';
			}else
			{
				$urut=$no1+1;
				$urut=substr('00000',-5,5-strlen($urut)).$urut;					
			}
		}
		//$this->ID->Text=$urut;
		return $urut;
		//$this->RBkelObat->focus();
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
			
			$item->idTujuan->Value = $id_tujuan;
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
		if($this->DDJenisBrg->SelectedValue != '')
		{
			if($this->DDJenisBrg->SelectedValue=='01')
			{			
				$this->RBtipeObat->Enabled=true;
			}
			else
			{
				$this->RBtipeObat->Enabled=false;
			}		
			
			$this->nextPoin();
		}
		else
		{
			$this->ID->Text = '';	
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
			$nama = trim(strtolower($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_obat WHERE LOWER(nama) = '$nama'";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Obat/Alkes dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
						modal: true
					}});');	
						
				$this->Page->CallbackClient->focus($this->nama);
			}
			else
			{
				$ObatRecord=new ObatRecord;	
				$kodeObat = $this->nextPoin();			
				$ObatRecord->tipe=TPropertyValue::ensureString($this->collectSelectionListResult($this->RBtipeObat));
				$ObatRecord->kode=ucwords($kodeObat);
				//$ObatRecord->kel_obat=TPropertyValue::ensureString($this->collectSelectionListResult($this->RBkelObat));
				$ObatRecord->kel_obat=$this->RBkelObat->SelectedValue;
				$ObatRecord->kel_margin = $this->DDkelMargin->SelectedValue; 
				$ObatRecord->kel_standard = $this->RBstandar->SelectedValue; 
				//$ObatRecord->nama=mysql_real_escape_string(ucwords($this->nama->Text));
				$ObatRecord->nama = trim(ucwords($this->nama->Text));
				$ObatRecord->gol=TPropertyValue::ensureString($this->DDGol->SelectedValue);
				$ObatRecord->klasifikasi=TPropertyValue::ensureString($this->DDKlas->SelectedValue);
				$ObatRecord->derivat=TPropertyValue::ensureString($this->DDDerivat->SelectedValue);
				$ObatRecord->produsen=TPropertyValue::ensureString($this->DDProd->SelectedValue);
				$ObatRecord->pbf=TPropertyValue::ensureString($this->DDPbf->SelectedValue);
				$ObatRecord->satuan=TPropertyValue::ensureString($this->DDSat->SelectedValue);
				$ObatRecord->jml_satuan_besar=$this->satuanBesar->Text;
				$ObatRecord->kategori=$this->DDJenisBrg->SelectedValue;
				$ObatRecord->persentase_dokter=floatval($this->fee->Text);
				$ObatRecord->st='0';
				$ObatRecord->save(); 
				
				//UPDATE jml min, toleransi, max obat
				foreach($this->Repeater->Items as $item)
				{
					$kode = $kodeObat;
					$nmFieldMin = $item->nmFieldMin->Value;
					$nmFieldTol = $item->nmFieldTol->Value;
					$nmFieldMax = $item->nmFieldMax->Value;
					
					$jmlMin = $item->jmlMin->Text;			
					$toleransi = $item->toleransi->Text;			
					$jmlMax = $item->jmlMax->Text;	
					
					$sql = "UPDATE tbm_obat SET $nmFieldMin = '$jmlMin', $nmFieldTol = '$toleransi', $nmFieldMax = '$jmlMax'  WHERE kode='$kode'";						
					$this->queryAction($sql,'C');
					
					if(!HrgObatRecord::finder()->findByKode($kodeObat))			
					{
						$tglNow = date('Y-m-d');
						$HrgObatRecord = new HrgObatRecord;					
						$HrgObatRecord->kode=$kodeObat;
						$HrgObatRecord->sumber='01';
						$HrgObatRecord->hrg_netto=0;
						$HrgObatRecord->hrg_ppn=0;
						$HrgObatRecord->hrg_netto_disc='0';
						$HrgObatRecord->hrg_ppn_disc='0';
						$HrgObatRecord->tgl=$tglNow;
						// saves to the database via Active Record mechanism
						$HrgObatRecord->save();
					}
				
					
					if(!StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$kodeObat,$item->idTujuan->Value))//searching stok			
					{
						
						$sql = "SELECT id FROM tbt_obat_harga WHERE kode = '$kodeObat' ORDER BY id desc";
						$id_harga = HrgObatRecord::finder()->findBySql($sql);
						$idHarga = $id_harga->id;
					
						// populates a UserRecord object with user inputs
						$StokRecord=new StokLainRecord;	
						$StokRecord->id_obat=$kodeObat;
						$StokRecord->jumlah='0';
						$StokRecord->sumber='01';
						$StokRecord->id_harga=$idHarga;					
						$StokRecord->tujuan=$item->idTujuan->Value;
						// saves to the database via Active Record mechanism
						$StokRecord->save();
					}
				}		
				
				//Return to the origin page				
				// redirects the browser to the homepage
				$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
				//$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat',
				//array(
				//	'offsetEdit'=>ceil($this->getViewState('offset')/10)
				//	)));
			}	
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent(); alert(\'Pastikan data semua data telah di isi !\')');
		}		
	}
	
		
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));		
	}
}
?>
