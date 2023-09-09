<?php

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class fraksiJasmed extends SimakConf
{   
		
	public function onInit($param)
	 {		
		parent::onInit($param);
		/*
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		*/	
	 }	
	 
			
	public function onPreRender($param)
	{				
		parent::onPreRender($param);				
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   
			$noTrans = $this->Request['noTrans'];
			$index = $this->Request['index'];
			$nmTableTmp = $this->Request['nmTableTmp'];
			
			//$this->jml->Text = $nmTableTmp;
			$this->jml->Focus();
			$this->uploadPanel->Display = 'None';
			$this->uploadBtn->Enabled = false;
			
			$this->selesaiBtn->Visible = false;
		}		
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT id FROM $nmTable WHERE nama <> ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) 
				$this->selesaiBtn->Visible = true;
			else
				$this->selesaiBtn->Visible = false;	
			
				
			$sql = "SELECT id FROM $nmTable WHERE nama = ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) 
				$this->uploadBtn->Visible = true;
			else
			{
				$noTrans = $this->Request['noTrans'];
				$index = $this->Request['index'];
		
				$this->uploadBtn->Visible = false;	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','window.parent.maskContent(); window.parent.modalCallback(\''.$index.'\',\''.$nmTable.'\'); jQuery.FrameDialog.closeDialog();');	
			}	
		}
    }		
	
  	public function prosesClicked($sender,$param)
	{	
		if($this->Page->IsValid) 
		{
			$jml = $this->jml->Text;
			
			if(intval($jml) > 0)
			{
				$this->cariPanel->Enabled = false;
				$this->uploadPanel->Display = 'Dynamic';
				$this->uploadBtn->Enabled = true;
				$this->bindData();
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah yang diisikan tidak sesuai !\'); document.all.'.$this->jml->getClientID().'.focus();');
			}	
		}
	}
	
	public function prosesCallback($sender,$param)
	{	
		$this->cariPanel->render($param->getNewWriter());
		$this->uploadPanel->render($param->getNewWriter());
	}
	
	public function repeaterDataBound2($sender,$param)
    {
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			$item->level1Lbl->Text = 'Fraksi '.$item->DataItem['id'];
			
        }
    }
	
	public function prosesPembagianFraksi($sender,$param)
    {
		//$sender->Enabled = false;
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		
		foreach($this->Repeater->Items[$index]->Repeater2->getItems() as $item) {
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        	{
				//$item->persentase2->Text = '1';
				$data[] = array('id'=>$item->persentase2->Text,'nama'=>$item->persentase2->Text);
			}
		}
		
		$this->setViewState('dataFraksiLevel1',$data);		
		//$this->Repeater->Items[$index]->Repeater2->DataSource = $data;
		//$this->Repeater->Items[$index]->Repeater2->dataBind();
	}
	
	public function bindData2($sender,$param)
    {
		$sender->Enabled = false;
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		
		if($this->getViewState('jmlFraksiLevel1'))
		{	
			$jmlFraksiLevel1 = $this->getViewState('jmlFraksiLevel1');
			foreach($jmlFraksiLevel1 as $row)
			{
				for($i=1; $i<=$this->Repeater->Items[$index]->persentase->Text; $i++)
				{
					$data[] = array('id'=>$i,'nama'=>$i);
				}	
			}
		}
		else
		{
			for($i=1; $i<=$this->Repeater->Items[$index]->persentase->Text; $i++)
			{
				$data[] = array('id'=>$i,'nama'=>$i);
			}
			
			$this->setViewState('jmlFraksiLevel1',$data);
		}
		
		
		$this->Repeater->Items[$index]->prosesPembagianFraksiBtn->Enabled = true;
		$this->Repeater->Items[$index]->Repeater2->DataSource = $data;
		$this->Repeater->Items[$index]->Repeater2->dataBind();
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			
			$item->no->Text = $item->DataItem['id'];
			//$item->uploadBtnTrigger->Attributes->onclick = "{$item->foto->getCallbackJavascript()}; return false;";
			//$item->HtmlArea->Text = $item->DataItem['catatan'];
        }
    }
	
	
	public function bindData()
    {
		for($i=1; $i<=$this->jml->Text; $i++)
		{
			$data[]=array('id'=>$i,'nama'=>$i);
		}
		
		$this->Repeater->DataSource = $data;
		$this->Repeater->dataBind();
		
		$this->setViewState('jmlLevel',$data);
	}
	
	
	public function selesaiClicked($sender,$param)
    {
		$noTrans = $this->Request['noTrans'];
		$index = $this->Request['index'];
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT id FROM $nmTable WHERE nama = ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) //jika masih ada file yg kosong, tapi user jadi mengupload untuk file tersebut
			{
				foreach($arr as $row)
				{
					$id = $row['id'];
					
					$sql = "DELETE FROM $nmTable WHERE id = '$id'";
					$this->queryAction($sql,'C');
				}	
			}	
		}
		
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalCallback(\''.$index.'\',\''.$nmTable.'\'); jQuery.FrameDialog.closeDialog();');		
	}
	
	public function batalClicked($sender,$param)
    {
		$this->Response->Reload();	
	}
	
	public function uploadBtnClicked($sender,$param)
    {
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$this->getPage()->getCallbackClient()->click( $item->uploadBtnTrigger->getClientID() );
					break;
				case "AlternatingItem":					
					$this->getPage()->getCallbackClient()->click( $item->uploadBtnTrigger->getClientID() );
					break;
				default:
					break;
			}
		}
		
		$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();');	
	}
	
	public function uploadTempFoto($foto,$result,$tipeFile,$noUrut,$uploadPanel)
    {
		$noTrans = $this->Request['noTrans'];
		
		if($foto->ErrorCode == 2)//kelebihan ukuran
		{
			$result->Text = 'File Gagal Diupload, ukuran file tidak boleh lebih dari 300 kilobyte';
			$uploadPanel->Visible = true;
		}		
		elseif($foto->HasFile) //ada file yg akan di upload
		{
			if($tipeFile == '1') //tipe foto
			{
				if($foto->FileType != 'image/jpeg' && $foto->FileType != 'image/jpg' && $foto->FileType != 'image/gif' && $foto->FileType != 'image/png')
				{
					$result->Text = "Tipe file harus jpeg, jpg, gif atau png.";
					$uploadPanel->Visible = true;
				}	
				else
				{
					$result->Text = $noTrans.'-'.$foto->FileName;
					$nmFile = $noTrans.'-'.$noUrut.'-'.$foto->FileName;
					
					//$url = 'protected\pages\Rad\foto\\'.$nmFile;
					//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$nmFile;
					$url = 'protected/pages/Rad/foto//'.$nmFile;
					$urlTmp ='protected/pages/Rad/foto/tmp//'.$nmFile;
					
					if(file_exists($urlTmp))
					{
						unlink($urlTmp);
					}
					
					$this->setViewState('file'.$noUrut,$foto);
					$this->setViewState('url'.$noUrut,$url);
					$this->setViewState('urlTmp'.$noUrut,$urlTmp);
					$foto->saveAs($urlTmp);
					
					if($this->getViewState('nmTable'))
					{
						$nmTable = $this->getViewState('nmTable');
					
						$sql = "UPDATE $nmTable SET nama = '$nmFile', tipe = '$tipeFile' WHERE id = '$noUrut' ";
						$this->queryAction($sql,'C');
					}
					$uploadPanel->Visible = false;
					//$param->IsValid=true;
					//$this->uploadProses($noUrut,$noTrans,$url,$urlTmp,$nmFile);	
				}
			}
			elseif($tipeFile == '2') //tipe dokumen
			{
				if($foto->FileType != 'application/msword' && $foto->FileType != 'application/pdf')
				{
					$result->Text ="Tipe file harus doc atau pdf.";
					$uploadPanel->Visible = true;
				}	
				else
				{
					$result->Text = $noTrans.'-'.$foto->FileName;
					$nmFile = $noTrans.'-'.$noUrut.'-'.$foto->FileName;
					
					//$url = 'protected\pages\Rad\foto\\'.$nmFile;
					//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$nmFile;
					$url = 'protected/pages/Rad/foto//'.$nmFile;
					$urlTmp ='protected/pages/Rad/foto/tmp//'.$nmFile;
					
					if(file_exists($urlTmp))
					{
						unlink($urlTmp);
					}
					
					$this->setViewState('file'.$noUrut,$foto);
					$this->setViewState('url'.$noUrut,$url);
					$this->setViewState('urlTmp'.$noUrut,$urlTmp);
					$foto->saveAs($urlTmp);
					
					if($this->getViewState('nmTable'))
					{
						$nmTable = $this->getViewState('nmTable');
					
						$sql = "UPDATE $nmTable SET nama = '$nmFile', tipe = '$tipeFile' WHERE id = '$noUrut' ";
						$this->queryAction($sql,'C');
					}
					
					$uploadPanel->Visible = false;
				}
			}
		}
		else
		{
			$result->Text ="Belum ada file yang dipilih !";
		}		
		/*
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT GROUP_CONCAT(id SEPARATOR ', ' ) AS id FROM $nmTable WHERE nama = ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) //jika masih ada file yg belum di upload
			{
				foreach($arr as $row)
				{
					$id = $row['id'];
				}
			
				
			}
			
			$this->getPage()->getClientScript()->registerEndScript('', 'alert(\'tret \')');			
		}	
			*/			
	}	
	
	public function uploadProses($i,$idBuku)
	{
		$data = BiblioRecord::finder()->findByPk($idBuku);
		
		$uploadId = 'foto'.$i;
			
		if($i == '1')
		{
			$field = 'image';
		}
		else
		{
			$field = 'image'.$i;
		}
		
		if($this->getViewState($uploadId) == '1')
		{
			$url = $this->getViewState('url'.$i);
			$urlTmp = $this->getViewState('urlTmp'.$i);				
			copy($urlTmp,$url);				
			unlink($urlTmp);
			$data->$field = $this->getViewState('fileName'.$i);
			$this->setViewState($uploadId,'0');
		}
		
		$data->save();
	}
	
	public function fileUploaded($sender,$param)
    {
		if($this->Page->IsValid) 
			{
			//$item = $sender->getNamingContainer(); 
			//$index = $this->Repeater->DataKeys[$item->getItemIndex()];
			//$this->Repeater->Items[$index]->no->Text .= $this->Repeater->Items[$index]->foto->ToolTip.', ';
			//$this->Repeater->Items[$index]->no->setText($this->Repeater->Items[$index]->foto->ToolTip);
			
			///*
			foreach($this->Repeater->getItems() as $item) {
				switch($item->getItemType()) {
					case "Item":
						if($item->nmValue->Value == '')
						{
							$this->uploadTempFoto($item->foto,$item->result,$item->tipeFile->SelectedValue,$item->no->Text,$item->uploadPanel);
						}
						break;
					case "AlternatingItem":	
						if($item->nmValue->Value == '')
						{
							$this->uploadTempFoto($item->foto,$item->result,$item->tipeFile->SelectedValue,$item->no->Text,$item->uploadPanel);
						}
						break;
					default:
						break;
				}
			}
			//*/
			
			$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();');	
		
		}
	}
	
	public function uploadBtnClicked2($sender,$param)
    {
		if($this->getViewState('nmTable'))
		{	
			$nmTable = $this->getViewState('nmTable');	
			
			$sql = "SELECT * FROM $nmTable WHERE nama <> ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->getPage()->getClientScript()->registerEndScript('','unmaskContent(); alert(\'Ada '.count($arr).' data !\');');
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Upload file belum bisa diproses karena belum ada file yang dimasukan!</p>\',timeout: 4000,dialog:{
						modal: true
					}});');	
			}
		}
	}
	
	
}

?>
