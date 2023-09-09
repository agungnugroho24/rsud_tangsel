<?php
class masterBarangBaru extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			
			$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
			$this->DDsubHabisPakai->dataBind();	
			
			$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
			$this->DDsubBergerak->dataBind();	
			
			$this->DDVen->DataSource=AssetProdusenRecord::finder()->findAll();
			$this->DDVen->dataBind();	
			
			$this->DDDist->DataSource=AssetDistributorRecord::finder()->findAll();
			$this->DDDist->dataBind();
							
			$this->nmBarang->focus();					
		}
	}	
	
	public function modeJenisChanged($sender, $param)
	{
		$jnsBrg = $this->collectSelectionResult($this->modeJenis);
		if($jnsBrg == '1')
		{
			$this->hbsPakaiCtrl->Visible = true;
			$this->TdkHbsPakaiCtrl->Visible = false;			
		}
		elseif($jnsBrg == '2')
		{
			$this->hbsPakaiCtrl->Visible = false;
			$this->TdkHbsPakaiCtrl->Visible = true;			
		}
		
		$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
		$this->DDsubHabisPakai->dataBind();	
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
			
		$this->modeTdkHabisPakai->SelectedIndex = -1 ;
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->tdkBergerakCtrl->Visible = false;
		$this->bergerakCtrl->Visible = false;
		$this->nonMedisCtrl->Visible = false;
	}
	
	public function modeTdkHabisPakaiChanged($sender, $param)
	{
		$subTdkHabis = $this->collectSelectionResult($this->modeTdkHabisPakai);
		if($subTdkHabis == '1')
		{
			$this->bergerakCtrl->Visible = true;
			$this->tdkBergerakCtrl->Visible = false;			
		}
		elseif($subTdkHabis == '2')
		{
			$this->bergerakCtrl->Visible = false;
			$this->tdkBergerakCtrl->Visible = true;			
		}
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
		
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->nonMedisCtrl->Visible = false;
	}
	
	public function modeTdkBergerakChanged($sender, $param)
	{
		$subTdkbergerak = $this->collectSelectionResult($this->modeTdkBergerak);
		if($subTdkbergerak == '1')
		{			
			$this->nmBarang->focus();
			$this->nmBarang->Text = '';
			$this->nonMedisCtrl->Visible = false;			
		}
		elseif($subTdkbergerak == '2')
		{			
			$this->nonMedisCtrl->Visible = true;			
		}
		
		$this->modeNonMedis->SelectedIndex = -1 ;
	}
	
	public function modeNonMedisChanged($sender, $param)
	{
		$nonMedis = $this->collectSelectionResult($this->modeNonMedis);
		if($nonMedis == '1')
		{
			//$this->nonElektrikalCtrl->Visible = false;			
		}
		elseif($nonMedis == '2')
		{
			//$this->elektrikalCtrl->Visible = false;
			//$this->nonElektrikalCtrl->Visible = true;			
		}
		$this->nmBarang->focus();
		$this->nmBarang->Text = '';
	}
	
	
	public function fileUploaded($sender)
    {
        if($sender->HasFile) //jika foto barang disertakan
        {
			if($sender->FileSize >204800) //check ukuran file foto
			{
				$this->Result->Text="File Gagal Diupload, ukuran file tidak boleh lebih dari 200 Kb";
			}
			elseif($sender->FileType != 'image/jpeg' && $sender->FileType != 'image/jpg' && $sender->FileType != 'image/gif' && $sender->FileType != 'image/png') //check tipe file foto
			{
				$this->Result->Text="File Gagal Diupload, jenis file harus jpeg, jpg, gif atau png.";
			}
			else
			{		
				// Open the uploaded file
			   $file = fopen($sender->LocalName, "rb");
			
			   // Read in the uploaded file
			   $fileContents = fread($file, filesize($sender->LocalName));
				
				$this->insertDataBarang();
				
				$newFoto=new AssetFotoBarangRecord;
				$newFoto->id_brg=$this->getViewState('idBrg');
				$newFoto->nama_file=$sender->FileName;
				$newFoto->foto=$fileContents;			
				$newFoto->tipe=$sender->FileType;	
				$newFoto->save();
				
				$this->clearViewState('idBrg');
				
				//$this->batalClicked();
				$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));
			}
        }
		else //jika foto barang tidak disertakan
		{
			$this->insertDataBarang();
			//$this->batalClicked();
			$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));	
		}
    }
	
	public function insertDataBarang()
	{
		$idBrg=$this->numUrut('tbm_asset_barang',AssetBarangRecord::finder(),'4');
		$this->setViewState('idBrg',$idBrg);		
		
		$newBarang=new AssetBarangRecord();			
		$newBarang->id=$idBrg;
		$newBarang->nama=$this->nmBarang->Text;	
		$newBarang->prod=$this->DDVen->SelectedValue;
		$newBarang->dist=$this->DDDist->SelectedValue;
		//$newBarang->penyusutan=$this->penyusutan->Text;
		$newBarang->ket=$this->ket->Text;
		
		$kelHabisPakai = $this->collectSelectionResult($this->modeJenis);
		if($kelHabisPakai == '1') //barang habis pakai
		{		
			$newBarang->st_kel_habis_pakai=$kelHabisPakai;
			$newBarang->st_sub_kel_habis_pakai=$this->DDsubHabisPakai->SelectedValue;
		}
		elseif($kelHabisPakai == '2') //barang tidak habis pakai
		{
			$newBarang->st_kel_habis_pakai=$kelHabisPakai;		
			
			$cekBergerak = $this->collectSelectionResult($this->modeTdkHabisPakai);
			if($cekBergerak == '1') // barang habis pakai - bergerak
			{
				$newBarang->st_bergerak=$cekBergerak;	
				$newBarang->st_sub_bergerak=$this->DDsubBergerak->SelectedValue;				
			}
			elseif($cekBergerak == '2') // barang habis pakai - tidak bergerak
			{
				$newBarang->st_bergerak=$cekBergerak;
				
				$cekMedis = $this->collectSelectionResult($this->modeTdkBergerak);
				if($cekMedis == '1') //barang habis pakai - tidak bergerak - medis
				{			
					$newBarang->st_medis=$cekMedis;		
				}
				elseif($cekMedis == '2') //barang habis pakai - tidak bergerak - non medis
				{			
					$newBarang->st_medis=$cekMedis;	
					
					$cekElektrik = $this->collectSelectionResult($this->modeNonMedis);
					if($cekElektrik == '1') //barang habis pakai - tidak bergerak - non medis - elektrikal
					{
						$newBarang->st_elektrik=$cekElektrik;			
					}
					elseif($cekElektrik == '2') //barang habis pakai - tidak bergerak - non medis - non elektrikal
					{
						$newBarang->st_elektrik=$cekElektrik;		
					}		
				}
			}
		}		
		
		$newBarang->save();	
	}
		
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
			$this->fileUploaded($this->aplod);
		}	
	}
	
	public function batalClicked()
	{		
		//$this->Response->reload();		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
