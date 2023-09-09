<?php
class pengambilanResep extends SimakConf
{   
    private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 15;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Apotik
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	

	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack)
		{	
            

			//$this->modeActive();
			//$this->noCmPanel->Visible = true;
			//$this->Page->CallbackClient->focus($this->noCM);
			//$this->notrans->Enabled=true;	
			
			//$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			//$this->DDObat->dataBind();
			
			//$this->DDObat->Enabled=true;
		}
		
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{	
					
			$this->Response->redirect($this->Service->constructUrl('Apotik.pengambilanResep'));
			$this->Response->Reload();
		}	
    }
	
	public function modeActive()
	{
            if($this->getViewState('nmTable'))
            {
                $nmTable = $this->getViewState('nmTable');
                $this->queryAction($nmTable ,'D');
            }

            $this->noCmPanel->Visible = true;
            $this->noCM->Text = '';
            $this->noCM->focus();
            $this->noTransPanel->Visible = false;
            $this->modeInputTxt->Text = "No. CM";

            $this->errMsgCtrl->Visible = false;
            //$this->modeTransDeactive();
            //$this->modeCmDeactive();
	}
	
        // HERE
	public function cariClicked()
	{
            if($this->IsValid)  // when all validations succeed
            { 
                $this->modeCmActive();
                //$this->checkInput();
                $cm = $this->formatCm($this->noCM->Text);					
                /*		
                $sql ="SELECT 
                                  tbt_obat_jual.no_trans,
                                  tbt_obat_jual.no_reg
                                FROM
                                  tbt_obat_jual
                                WHERE ";
                 *
                 */

                $sql ="SELECT DISTINCT
                        tbt_obat_jual.no_reg
                      FROM
                        tbt_obat_jual
                      WHERE 
                        flag = 1
                      AND
                        flag_ambil_resep = 0
                      AND
                        tbt_obat_jual.cm = '$cm'";

                $arr=$this->queryAction($sql,'S');
                $jmlData=0;

                // -- Retrieve no_reg yang sudah diambil
                foreach($arr as $row)
                {
                        $data .= $row['no_reg'] .',';	// Dimasukan ke dalam 1 string dan dipisahkan memakai koma
                        $jmlData++;                     // Menambahkan nilai dari 'jumlah data'
                }

                // -- Kalau memang ada datanya
                if($jmlData>0)
                {
                        $v = strlen($data) - 1;     // 
                        $var=substr($data,0,$v);    //				
                        $temp = explode(',',$var);  // Memisahkan no-reg masing2.

                        $this->DDtrans->DataSource=$temp;
                        $this->DDtrans->dataBind();
                        $this->errMsgCtrl->Visible = false;
                        $this->noCmPanel->Enabled = false;
                        $this->cariBtn->Enabled = false;
                }

                // -- Kalau tidak ada, dimunculkan warning
                else
                {	
                    //$this->errMsgCtrl->Visible = true;
                    $this->getPage()->getClientScript()->registerEndScript
                            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Data tidak ditemukan atau Pembayaran belum diselesaikan.</p>\',timeout: 4000,dialog:{modal: true,
                            buttons: {
                                    "OK": function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                    }});');	

                    $this->noCM->Text = '';	
                    $this->noCM->focus();
                    $this->modeCmDeactive();
                }
            }
	}
	
	
	
	public function pindahButtonClicked($sender, $param)
	{
		// Ambil parameter dari column yang di edit
        $item=$param->Item;
        $no_trans = $item->IDTransColumn->Text;
		
		$sql = "UPDATE 
					tbt_obat_jual
				SET
					tbt_obat_jual.flag_salinan_resep = 
					CASE 
						WHEN flag_salinan_resep = '0' THEN '1' 
						WHEN flag_salinan_resep = '1' THEN '0' 
					END
					WHERE no_trans = '$no_trans'";
		$arr = $this->queryAction($sql, 'C');
			
		$this->checkInput();	
		
	}
	
	public function pindahButtonKopiClicked($sender, $param)
	{
		// Ambil parameter dari column yang di edit
        $item=$param->Item;
        $no_trans = $item->IDTransColumnKopi->Text;
		
		$sql = "UPDATE 
					tbt_obat_jual
				SET
					tbt_obat_jual.flag_salinan_resep = 
					CASE 
						WHEN flag_salinan_resep = '0' THEN '1' 
						WHEN flag_salinan_resep = '1' THEN '0' 
					END
					WHERE no_trans = '$no_trans'";
		$arr = $this->queryAction($sql, 'C');
			
		$this->checkInput();	
		
	}
	
	public function modeCmActive()
	{
		$this->DDtransCtrl->Visible = true;
		$this->modeTransDeactive();
	}
	
	public function modeCmDeactive()
	{
		$this->DDtransCtrl->Visible = false;
		$this->noCmTxt2->Text = '';
		$this->nmPasien2->Text = '';
		$this->nmDokter->Text = '';
		$this->tgl->Text = '';
	}
	
	public function modeTransActive()
	{
		$this->hasilCariCtrl->Visible = true;
		$this->modeCmDeactive();
				
	}
	
	public function modeTransDeactive()
	{
		$this->hasilCariCtrl->Visible = false;
		//$this->noCmTxt1->Text = '';
		//$this->nmPasien1->Text = '';
	}
	
	public function DDtransChanged()
	{
                // -- Kalau yang dipilih bukanlah tulisan "-- Silahkan Pilih --"
		if($this->DDtrans->SelectedValue!='')
		{
			$this->checkInput();
		}
	}
	
	public function lock()
	{
		$this->noCM->Enabled = false;			
		$this->cariBtn->Enabled = false;	
		$this->DDtrans->Enabled = false;	
	}
	
	public function unlock()
	{
		$this->noCM->Enabled = true;		
		$this->cariBtn->Enabled = true;	
		//$this->DDtrans->Enabled = true;	
	}
	
        /*
         * Fungsi untuk mengecek apakah obat dgn jumlah ini masih ada di dalam apotik atau tidak
         * @param : id_obat -> id obat dr yg mau kita check, jumlah -> jumlah obat yang mau kt cari
         * @return : true -> kalau stok mencukupi, false -> kalau stok tidak mencukupi
         */
        private function checkStokApotik( $id_obat, $jumlah )
        {
             $sql ="SELECT jumlah
                    FROM tbt_stok_lain
                    WHERE id_obat = '$id_obat'
                    AND tujuan = '14'";
             $arr = $this->queryAction($sql,'S');
             $stok;
             
             foreach( $arr as $row )
             {
                 $stok = $row['jumlah'];
             }
             
             // Kalau jumlah lebih besar atau sama dengan yang di request, return true
             if( $stok >= $jumlah )
                 return true;
             
             // Kalu jumlahnya lebih kecil
             else
                 return false;
        }
   
        
	public function checkInput()  // fungsi yang ada querynya buat nentuin ke salinan resep apa ngga
        {
            $this->DDResepTebus->Visible = false;
            $this->DDResepKopi->Visible = false;
			$this->DDResepGanti->Visible = false;
            
            // valid if the username is not found in the database
           if(ObatJualRecord::finder()->find('cm = ?', $this->formatCm($this->noCM->Text)))
           {			
                $no_trans = $this->ambilTxt($this->DDtrans);
           }
           
           else
           {
                $this->noCM->Focus();
           }
                
           $sql ="SELECT 
                    tbt_obat_jual.no_trans,
                    tbt_obat_jual.no_trans_rwtjln,
                    tbt_obat_jual.no_reg,
                    tbt_obat_jual.cm,
                    tbt_obat_jual.dokter,
                    tbt_obat_jual.klinik,
                    tbt_obat_jual.keterangan_obat,
                    tbt_obat_jual.tgl,
                    tbt_obat_jual.id_obat,
                    tbt_obat_jual.id_kel_racik,
					tbt_obat_jual.id_trans_pengganti,
                    tbt_obat_jual.id_harga,
                    tbt_obat_jual.id_kemasan,
                    tbt_obat_jual.bungkus_racik,
                    tbt_obat_jual.flag_ambil_resep,
                    tbt_obat_jual.flag_salinan_resep,
                    tbt_obat_jual.expired,
                    tbt_obat_jual.jumlah,
                    tbt_obat_jual.hrg,
                    tbt_obat_jual.total
                  FROM
                    tbt_obat_jual
                  WHERE
                    tbt_obat_jual.no_reg = '$no_trans'";
           
                   
                if( $arr = $this->queryAction($sql,'S')) //jika pengecekan no_trans di tbt_obat_jual suskes
		{
                    $this->hasilCariCtrl->Visible = true;
                    $this->errMsgCtrl->Visible = false;
                    $this->DDtrans->Enabled = false;
                    
                    // Raymond : Mengambil record dripada table tbt_obat_jual
                    $tmpPasien = ObatJualRecord::finder()->findBySql($sql);

                    // Raymond : Mengambil record nama dokter untuk dokter yang idnya di dalam record si  pasien
                    $this->nmDokter->Text= PegawaiRecord::finder()->findByPk($tmpPasien->dokter)->nama;	
                    $this->noCmTxt2->Text= $tmpPasien->cm;
                    $this->nmPasien2->Text= PasienRecord::finder()->findByPk($tmpPasien->cm)->nama;
                    $this->tgl->Text= $this->convertDate($tmpPasien->tgl,'3');
					
					
                    
                    $arr_result_tebus = Array();
                    $arr_result_kopi = Array();
					$arr_result_ganti = Array();
                    
                    foreach( $arr as $row )
                    {
                        $id_kel_racik;
						
                        $nama_obat = ObatRecord::finder()->findByPk($row['id_obat'])->nama;
                        $tipe_obat = ObatRecord::finder()->findByPk($row['id_obat'])->tipe;
                        $satuan_obat_kode = ObatRecord::finder()->findByPk($row['id_obat'])->satuan;
                        

                        // Menentukan apakah stok obatnya masih ada atau tidak
                        $stok_available = $this->checkStokApotik( $row['id_obat'], $row['jumlah'] );
                        


                        // Menentukan kelompok racikan obat
                        if( $row['id_kel_racik'] == 0 )
                            $id_kel_racik = "Non-Racikan";

                        else
                            $id_kel_racik = "Racikan ".$row['id_kel_racik'];
                        
                        if($row['id_kemasan'] == 0)
                        {
                            $jmlKemasan = "-";
                            $nama_kemasan = "-";
                        }
                            
                        else
                        {
                            $nama_kemasan = KemasanRecord::finder()->findByPk($row['id_kemasan'])->nama;
                            $harga = KemasanRecord::finder()->findByPk($row['id_kemasan'])->hrg;
                            if($harga != 0)
                                $jmlKemasan = $row['bungkus_racik'] / $harga;
                            else
                                $jmlKemasan = "-";
                        } 

                        // Menentukan id obat
						
                        if( $tipe_obat == 0 )
                            $tipe_obat = "Generik";

                        else
                            $tipe_obat = "Non-Generik";
						
						$sql2 = "SELECT id_obat FROM tbt_obat_jual WHERE no_trans ='".$row['id_trans_pengganti']."'";
						$arr2 = $this->queryAction($sql2,'S');
						foreach($arr2 as $row2)
						{
							$id_obat = $row2['id_obat'];
							$nama_obat_pengganti = ObatRecord::finder()->findByPk($id_obat)->nama;
						}

                        $satuan_obat = SatuanObatRecord::finder()->findByPk($satuan_obat_kode)->nama;
							
                        
                        if( $row['flag_salinan_resep'] == '0' && $row['id_trans_pengganti']== '0' && $row['flag_ambil_resep']=='0')
                        {
                            $arr_result_tebus[sizeof($arr_result_tebus)] = array(
                                'id_transaksi' => $row['no_trans'],
                                'nama_obat' => $nama_obat,
                                'jumlah_obat' => $row['jumlah'],
                                'tipe_obat' => $tipe_obat,
                                'kelompok_racikan' => $id_kel_racik,
                                'jumlah_bungkus' => $jmlKemasan,
                                'nama_kemasan' => $nama_kemasan,
                                'keterangan' => $row['keterangan_obat'],
                                'total_harga' => $row['total']
                            );
                        }
                        
                        else if($row['flag_salinan_resep'] != '0' && $row['id_trans_pengganti']=='0')
                        {
                             $arr_result_kopi[sizeof($arr_result_kopi)] = array(
                                'id_transaksi' => $row['no_trans'],
                                'nama_obat' => $nama_obat,
                                'jumlah_obat' => $row['jumlah'],
                                'tipe_obat' => $tipe_obat,
                                'kelompok_racikan' => $id_kel_racik,
                                'jumlah_bungkus' => $jmlKemasan,
                                'nama_kemasan' => $nama_kemasan,
                                'keterangan' => $row['keterangan_obat'],
                                'total_harga' => $row['hrg']
                            );
                        }
						
						else if($row['id_trans_pengganti'] != '0' && $row['flag_ambil_resep']=='0')
						{
							$arr_result_ganti[sizeof($arr_result_ganti)] = array(
								'id_transaksi' => $row['no_trans'],
                                'nama_obat' => $nama_obat,
                                'jumlah_obat' => $row['jumlah'],
                                'tipe_obat' => $tipe_obat,
                                'kelompok_racikan' => $id_kel_racik,
                                'jumlah_bungkus' => $jmlKemasan,
                                'nama_kemasan' => $nama_kemasan,
                                'keterangan' => $row['keterangan_obat'],
                                'id_trans_obat_pengganti' => $row['id_trans_pengganti'],
								'obat_pengganti' => $nama_obat_pengganti
							);
						}
                    }
                    
                    if( $arr_result_tebus != NULL )
                    {
                        $this->DDResepTebus->Visible = true;
                        $this->UserGrid->DataSource = $arr_result_tebus;
                        $this->UserGrid->dataBind();
                        $this->setViewState('datagrid1', $this->UserGrid->DataSource);
                    }
                    


                    if( $arr_result_kopi != NULL )
                    {
                        $this->DDResepKopi->Visible = true;
						$this->salinanResepBtn->Enabled = true;
                        $this->UserGrid2->DataSource = $arr_result_kopi;
                        $this->UserGrid2->dataBind();
                        $this->setViewState('datagrid2', $this->UserGrid2->DataSource);  
                    }
					
					if( $arr_result_ganti != NULL )
					{
						$this->DDResepGanti->Visible = true;
                        $datagrid3 = $param->Item->Container;
                        $this->UserGrid3->DataSource = $arr_result_ganti;
                        $this->UserGrid3->dataBind();
                        $this->setViewState('datagrid3', $this->UserGrid3->DataSource); 
					}

                         
                    
                }
                
		else //jika pengecekan no_trans di tbt_obat_jual gagal => no_trans tidak ada atau pembayaran belum dilakukan
		{
			$this->hasilCariCtrl->Visible = false;
			//$this->errMsgCtrl->Visible = true;
			
			$this->getPage()->getClientScript()->registerEndScript
				('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Data tidak ditemukan atau Pembayaran belum diselesaikan.</p>\',timeout: 4000,dialog:{modal: true,
				buttons: {
				"OK": function() {
					jQuery( this ).dialog( "close" );
				}
			}
			}});');	
		}
    }
    
    // Fungsi yang digunakan untuk penebusan resep yang ada
    public function penebusanResep()
    {
        $this->alertTebus->Text = '    
            <script type="text/javascript">
                    alert("Sukses! Resep sudah ditebus!");
            </script>';

        // Mengambil nomor trans yang ada
        $no_trans = $this->ambilTxt($this->DDtrans);
        
        $sql = "SELECT id, id_obat, jumlah
                FROM tbt_obat_jual
                WHERE no_reg = ".$no_trans."
                AND flag_salinan_resep = '0'
                AND id_trans_pengganti = '0'";
        $arr = $this->queryAction($sql,'S');
        
        foreach( $arr as $row )
        {
            // Menngetahui stok gudang obat dahulu yang ada di dalam Apotik
            $sql = "SELECT jumlah FROM tbt_stok_lain WHERE tujuan = '14' AND id_obat = '".$row['id_obat']."'";
            $arr = $this->queryAction($sql,'S');
            
            foreach( $arr as $row1 )
            {
                $jumlah_obat = $row1['jumlah'];
            }
            
            // Kalau memang stok obatnya di gudang lebih banyak daripada stok yang mau di ambil
            if( $jumlah_obat > $row['jumlah'] )
            {
                // Mengurangi jumlah yang ada di dalam gudang.
                $sql = "UPDATE tbt_stok_lain SET jumlah = jumlah-".$row['jumlah']." WHERE id_obat = '".$row['id_obat']."' AND tujuan = '14'";
                $this->queryAction($sql,'C');
            }
            
            // Kalau memang stok obatnya di gudang lebih sedikit daripada yang di minta
            else
            {
                // Mengurangi jumlah yang ada di dalam gudang.
                $sql = "UPDATE tbt_stok_lain SET jumlah = 0 WHERE id_obat = '".$row['id_obat']."' AND tujuan = '14'";
                $this->queryAction($sql,'C');
            }
            
            // Kalau memang obatnya bisa ditebus.
            $sql ="UPDATE tbt_obat_jual SET flag_ambil_resep = '1' WHERE id = '".$row['id']."'";
            $this->queryAction($sql,'C');
        }
        
        $this->checkInput();
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


    // get data and bind it to datagrid
	private function bindGrid() // Hashfi: untuk menampilkan data resep ke Grid (BUKAN SALINAN RESEP!!!)
    {
        $this->pageSize = $this->UserGrid->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				$sql="SELECT * FROM $nmTable WHERE id_obat <> '00000'";
				
				//$this->showSql->Text = $sql;
				$data = $someDataList->getSomeDataPage($sql);
				$this->UserGrid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
				//$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);
				
				//if($someDataList->getSomeDataCount($sql) > 0)
				//	$this->gridPanel->Display = 'Dynamic';
				//else
					//$this->gridPanel->Display = 'None';
				
				//$this->setViewState('sql',$sql);
				//$this->sqlData->Text=$sql;//Show sql syntax    			
				
				if($this->UserGrid->VirtualItemCount > 0 )
				{
					$this->UserGrid->DataSource = $data;
					$this->UserGrid->DataBind();	
					//$this->prosesBtn->Enabled = true;
					
					foreach($data as $row)
					{
						$totRetur += $row['jml_retur'] ;
						
					}
					
					if($totRetur == 0)
						$this->cetakBtn->Enabled=false;
					else
						$this->cetakBtn->Enabled=true;
						
					
				}
				else
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
					$this->clearViewState('nmTable');//Clear the view state		
					
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();	
					//$this->prosesBtn->Enabled = false;
				}
				
			}
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid->DataSource = $session["SomeData"];
            $this->UserGrid->DataBind();	
			$this->clearViewState('sql');
        }
    }
	
	//BindGrid untuk data salinan resep
	
	private function bindGridCopy() // Hashfi: untuk menampilkan data SALINAN RESEP ke Grid
    {
        $this->pageSize = $this->UserGrid2->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid2->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				$sql="SELECT * FROM $nmTable WHERE id_obat= '00000'";
				
				//$this->showSql->Text = $sql;
				$data = $someDataList->getSomeDataPage($sql);
				$this->UserGrid2->VirtualItemCount = $someDataList->getSomeDataCount($sql);
				//$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);
				
				//if($someDataList->getSomeDataCount($sql) > 0)
				//	$this->gridPanel->Display = 'Dynamic';
				//else
					//$this->gridPanel->Display = 'None';
				
				//$this->setViewState('sql',$sql);
				//$this->sqlData->Text=$sql;//Show sql syntax    			
				
				if($this->UserGrid2->VirtualItemCount > 0 )
				{
					$this->UserGrid2->DataSource = $data;
					$this->UserGrid2->DataBind();	
					//$this->prosesBtn->Enabled = true;
					
					foreach($data as $row)
					{
						$totRetur += $row['jml_retur'] ;
					}
					
					if($totRetur == 0)
						$this->cetakBtn->Enabled=false;
					else
						$this->cetakBtn->Enabled=true;
				}
				else
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
					$this->clearViewState('nmTable');//Clear the view state		
					
					$this->UserGrid2->DataSource='';
					$this->UserGrid2->dataBind();	
					//$this->prosesBtn->Enabled = false;
				}
			}
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid2->DataSource = $session["SomeData"];
            $this->UserGrid2->DataBind();	
			$this->clearViewState('sql');		
        }
    }
	
    private function bindGridCopy2() // Hashfi: untuk menampilkan data Obat yang digantikan ke Grid
    {
        $this->pageSize = $this->UserGrid3->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid3->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
            
            if($this->getViewState('nmTable'))
            {
                $nmTable = $this->getViewState('nmTable');
                $sql="SELECT * FROM $nmTable WHERE id_obat= '00000'";
                
                //$this->showSql->Text = $sql;
                $data = $someDataList->getSomeDataPage($sql);
                $this->UserGrid3->VirtualItemCount = $someDataList->getSomeDataCount($sql);
                //$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);
                
                //if($someDataList->getSomeDataCount($sql) > 0)
                //  $this->gridPanel->Display = 'Dynamic';
                //else
                    //$this->gridPanel->Display = 'None';
                
                //$this->setViewState('sql',$sql);
                //$this->sqlData->Text=$sql;//Show sql syntax               
                
                if($this->UserGrid3->VirtualItemCount > 0 )
                {
                    $this->UserGrid3->DataSource = $data;
                    $this->UserGrid3->DataBind();   
                    //$this->prosesBtn->Enabled = true;
                    
                    foreach($data as $row)
                    {
                        $totRetur += $row['jml_retur'] ;
                    }
                    
                    if($totRetur == 0)
                        $this->cetakBtn->Enabled=false;
                    else
                        $this->cetakBtn->Enabled=true;
                }
                else
                {
                    $this->queryAction($this->getViewState('nmTable'),'D');//Droped the table       
                    $this->clearViewState('nmTable');//Clear the view state     
                    
                    $this->UserGrid3->DataSource='';
                    $this->UserGrid3->dataBind();   
                    //$this->prosesBtn->Enabled = false;
                }
            }
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid3->DataSource = $session["SomeData"];
            $this->UserGrid3->DataBind();   
            $this->clearViewState('sql');       
        }
    }

	// ---------- datagrid page and sort events ---------------    
    protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_PageIndexChanged_Copy($sender,$param) // Salinan Resep
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid2->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridCopy();
    }

    protected function dtgSomeData_PageIndexChanged_Copy2($sender,$param) // Salinan Resep
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid3->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridCopy2();
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
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid->CurrentPageIndex = 0;
        $this->bindGrid();
    }
	
	
	protected function dtgSomeData_SortCommand_Copy($sender,$param) // Salinan Resep
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
            // clear data in session because we need to refresh it from db
            $session = $this->getSession();
            $session->remove("SomeData");

            $this->UserGrid2->CurrentPageIndex = 0;
            $this->bindGridCopy();
        }

    protected function dtgSomeData_SortCommand_Copy2($sender,$param) // Salinan Resep
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
            // clear data in session because we need to refresh it from db
            $session = $this->getSession();
            $session->remove("SomeData");

            $this->UserGrid3->CurrentPageIndex = 0;
            $this->bindGridCopy2();
        }
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		}
		else
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}
			
		
		$page=$this->queryAction($sql,'S');		 
		return $page;
		
	}	
	
	protected function dtgSomeData_ItemCreated($sender,$param)
        {
            $item=$param->Item;
            
			if($item->ItemType==='EditItem')
            {
                
            }     
	   
            if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
            {
		
            }
        }
	
	public function gantiObat($sender,$param)
	{  
            //$this->UserGrid2->Visible = false;
            //$this->UserGrid3->Visible = false;
            $no_trans = $sender->CommandParameter;
            //$noTransJalan = $this->getViewState('noTransJalan');
            //$this->getPage()->getClientScript()->registerEndScript('',"alert('testes')");
            
            
            //$no_trans = $this->ambilTxt($this->DDNotrans);
            $url = "?page=Apotik.gantiObat&notrans=".$no_trans;
            $this->getPage()->getClientScript()->registerEndScript('ganti1',"tesFrame('$url',650,500,'PENGGANTIAN OBAT')");
	}
        
        // Fungsi untuk mencetak obat yang ada dalam bentuk PDF
        public function cetakSalinanResep()
        {
            $no_trans = $this->ambilTxt($this->DDtrans);
            //$this->Response->redirect($this->Service->constructUrl('Apotik.cetakKopiResep',array('noreg'=>$no_trans)));

            $url="?page=Apotik.cetakKopiResep&noreg=".$no_trans;
            $this->getPage()->getClientScript()->registerEndScript('js2', "window.open('".$url."','_blank')");
            $this->checkInput();
        }
	
	public function editItem($sender,$param)
        {   
            $this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
            $no_trans = $this->ambilTxt($this->DDtrans);
        
            $sql = "SELECT 
                    tbt_obat_jual.no_trans,
                    tbt_obat_jual.no_trans_rwtjln,
                    tbt_obat_jual.no_reg,
                    tbt_obat_jual.cm,
                    tbt_obat_jual.dokter,
                    tbt_obat_jual.klinik,
                    tbt_obat_jual.keterangan_obat,
                    tbt_obat_jual.tgl,
                    tbt_obat_jual.id_obat,
                    tbt_obat_jual.id_kel_racik,
                    tbt_obat_jual.id_harga,
                    tbt_obat_jual.flag_salinan_resep,
                    tbt_obat_jual.expired,
                    tbt_obat_jual.jumlah,
                    tbt_obat_jual.hrg,
                    tbt_obat_jual.total
                  FROM
                    tbt_obat_jual
                  WHERE
                    flag_ambil_resep = '0'
                  AND
                    tbt_obat_jual.no_reg = '$no_trans'";
                   
            $arr = $this->queryAction($sql,'S');
            $arr_result = Array();
            
            foreach( $arr as $row )
            {
                $id_kel_racik;
                
                $nama_obat = ObatRecord::finder()->findByPk($row['id_obat'])->nama;
                $tipe_obat = ObatRecord::finder()->findByPk($row['id_obat'])->tipe;

                // Menentukan apakah stok obatnya masih ada atau tidak
                $stok_available = $this->checkStokApotik( $row['id_obat'], $row['jumlah'] );

                // Menentukan kelompok racikan obat
                if( $row['id_kel_racik'] == 0 )
                    $id_kel_racik = "Non-Racikan";

                else
                    $id_kel_racik = "Racikan ".$row['id_kel_racik'];

                // Menentukan id obat
                if( $tipe_obat == 0 )
                    $tipe_obat = "Generik";

                else
                    $tipe_obat = "Non-Generik";
                
                if( $row['flag_salinan_resep'] == '0')
                {
                    $arr_result_tebus[sizeof($arr_result_tebus)] = array(
                        'id_transaksi' => $row['no_trans'],
                        'nama_obat' => $nama_obat,
                        'jumlah_obat' => $row['jumlah'],
                        'tipe_obat' => $tipe_obat,
                        'kelompok_racikan' => $id_kel_racik,
                        'keterangan' => $row['keterangan_obat'],
                        'total_harga' => $row['total']
                    );
                }
            }
            
            $this->UserGrid->DataSource = $arr_result_tebus;
            $this->UserGrid->dataBind();
            
            //$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
            //$this->UserGrid->DataSource=$arr_result_tebusS;
            //$this->DataGrid->dataBind();
            
		//if ($this->User->IsAdmin)
		//{
			/*$limit=$this->UserGrid->PageSize;		
			$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
			
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();*/
		//	$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
        	//$this->bindGrid();
		//}	
    }
	
	public function cancelItem($sender,$param)
        {
            $this->UserGrid->EditItemIndex=-1;
            
            $no_trans = $this->ambilTxt($this->DDtrans);
        
            $sql = "SELECT 
                    tbt_obat_jual.no_trans,
                    tbt_obat_jual.no_trans_rwtjln,
                    tbt_obat_jual.no_reg,
                    tbt_obat_jual.cm,
                    tbt_obat_jual.dokter,
                    tbt_obat_jual.klinik,
                    tbt_obat_jual.keterangan_obat,
                    tbt_obat_jual.tgl,
                    tbt_obat_jual.id_obat,
                    tbt_obat_jual.id_kel_racik,
                    tbt_obat_jual.id_harga,
					tbt_obat_jual.flag_salinan_resep,
                    tbt_obat_jual.expired,
                    tbt_obat_jual.jumlah,
                    tbt_obat_jual.hrg,
                    tbt_obat_jual.total
                  FROM
                    tbt_obat_jual
                  WHERE
                    flag_ambil_resep = '0'
                  AND
                    tbt_obat_jual.no_reg = '$no_trans'";
                   
            $arr = $this->queryAction($sql,'S');
            $arr_result_tebus = Array();
            
            foreach( $arr as $row )
            {
                $id_kel_racik;
                			
                $nama_obat = ObatRecord::finder()->findByPk($row['id_obat'])->nama;
                $tipe_obat = ObatRecord::finder()->findByPk($row['id_obat'])->tipe;

                // Menentukan apakah stok obatnya masih ada atau tidak
                $stok_available = $this->checkStokApotik( $row['id_obat'], $row['jumlah'] );

                // Menentukan kelompok racikan obat
                if( $row['id_kel_racik'] == 0 )
                    $id_kel_racik = "Non-Racikan";

                else
                    $id_kel_racik = "Racikan ".$row['id_kel_racik'];

                // Menentukan id obat
                if( $tipe_obat == 0 )
                    $tipe_obat = "Generik";

                else
                    $tipe_obat = "Non-Generik";
                
                if( $row['flag_salinan_resep'] == '0' )
                {
                    $arr_result_tebus[sizeof($arr_result_tebus)] = array(
                        'id_transaksi' => $row['no_trans'],
                        'nama_obat' => $nama_obat,
                        'jumlah_obat' => $row['jumlah'],
                        'tipe_obat' => $tipe_obat,
                        'kelompok_racikan' => $id_kel_racik,
                        'keterangan' => $row['keterangan_obat'],
                        'total_harga' => $row['total']
                    );
                }
            }
            
            $this->UserGrid->DataSource=$arr_result_tebus;
            $this->UserGrid->dataBind();
        }
        
        /*private function updateObat( $no_trans, $nama_obat, $jumlah_obat, $keterangan )
        {
			$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04'); //nomor transaksi 
			$noReg = $this->numRegister('tbt_obat_jual',ObatJualRecord::finder(),'04'); //nomor register atau nomor obat
			$sql = "INSERT INTO
					tbt_obat_jual
					VALUES
					tbt_obat_jual.no_trans = '$notransTmp',
					tbt_obat_jual.no_trans_rwtjln = 
					
                    tbt_obat_jual.no_reg = ";
					
			$arr = $this->queryAction($sql,'S');
			
			
			$ObatJual = new ObatJualRecord();
			
			foreach($arr as $row)
			{
				$ObatJual->no_trans = $notransTmp;
				$ObatJual->no_trans_rwtjln = $noTransRwtJln;						
				$ObatJual->cm = $cm;
				$ObatJual->no_reg = $noTrans;
				$ObatJual->dokter=$row['dokter'];
				$ObatJual->sumber='01';
				$ObatJual->tujuan=$row['tujuan'];
				$ObatJual->klinik = $row['klinik'];
				$ObatJual->id_obat=$row['id_obat'];
				$ObatJual->id_harga=$row['id_harga'];
				$ObatJual->tgl=$row['tgl'];
				$ObatJual->wkt=date('G:i:s');
				$ObatJual->operator=$operator;
				$ObatJual->hrg_nett=$row['hrg_nett'];
				$ObatJual->hrg_ppn=$row['hrg_ppn'];
				$ObatJual->hrg_nett_disc=$row['hrg_nett_disc'];
				$ObatJual->hrg_ppn_disc=$row['hrg_ppn_disc'];
				$ObatJual->hrg=$row['hrg'];
				$ObatJual->jumlah=$row['jml'];
				$ObatJual->total=$row['total'];
				$ObatJual->total_real=$row['total_real'];
				$ObatJual->flag='0';
				$ObatJual->st_obat_khusus=$row['st_obat_khusus'];
				$ObatJual->st_racik=$row['st_racik'];
				$ObatJual->id_kel_racik=$row['id_kel_racik'];
				$ObatJual->r_item=$row['r_item'];
				$ObatJual->r_racik=$row['r_racik'];
				$ObatJual->bungkus_racik=$row['bungkus_racik'];
				$ObatJual->id_kemasan=$row['id_kemasan'];
				$ObatJual->st_imunisasi=$row['st_imunisasi'];
				$ObatJual->id_kel_imunisasi=$row['id_kel_imunisasi'];
				$ObatJual->id_bhp=$row['id_bhp'];
				$ObatJual->bhp=$row['bhp'];
				$ObatJual->expired=$row['expired'];
				$ObatJual->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
				$ObatJual->st_paket=$row['st_paket'];
				$ObatJual->id_kel_paket=$row['id_kel_paket'];
				$ObatJual->jml_paket=$row['jml_paket'];
				$ObatJual->Save();
			}
			
            $sql = "UPDATE
                    tbt_obat_jual
                    SET
                    nama_obat = '".$nama_obat."',
                    jumlah = ".$jumlah_obat.",
                    keterangan_obat = '".$keterangan."'
                    WHERE
                    no_trans = '".$no_trans."'";
            
            $arr = $this->queryAction($sql,'C');
			
        }*/
        
        // Fungsi untuk pada saat save Item dilakukan
	public function saveItem($sender,$param)
        {
            // Ambil parameter dari column yang di edit
            $item=$param->Item;
            $no_trans = $this->ambilTxt($this->DDtrans);
            
            // Memanggil fungsi yang bisa mengupdate
            $this->updateObat
            (
                $item->IDTransColumn->TextBox->Text,
                $item->NamaObatColumn->TextBox->Text,
                $item->JumlahObatColumn->TextBox->Text,
                $item->KeteranganColumn->TextBox->Text
            );
            
            $sql = "SELECT 
                    tbt_obat_jual.no_trans,
                    tbt_obat_jual.no_trans_rwtjln,
                    tbt_obat_jual.no_reg,
                    tbt_obat_jual.cm,
                    tbt_obat_jual.dokter,
                    tbt_obat_jual.klinik,
                    tbt_obat_jual.keterangan_obat,
                    tbt_obat_jual.tgl,
                    tbt_obat_jual.id_obat,
                    tbt_obat_jual.id_kel_racik,
                    tbt_obat_jual.id_harga,
                    tbt_obat_jual.expired,
                    tbt_obat_jual.jumlah,
                    tbt_obat_jual.hrg,
                    tbt_obat_jual.total
                  FROM
                    tbt_obat_jual
                  WHERE
                    flag_ambil_resep = '0'
                  AND
                    tbt_obat_jual.no_reg = '$no_trans'";
                   
            $arr = $this->queryAction($sql,'S');
            $arr_result_tebus = Array();
            
            foreach( $arr as $row )
            {
                $id_kel_racik;
                $tipe_obat;
                
                $nama_obat = ObatRecord::finder()->findByPk($row['id_obat'])->nama;
                $tipe_obat = ObatRecord::finder()->findByPk($row['id_obat'])->tipe;

                // Menentukan apakah stok obatnya masih ada atau tidak
                $stok_available = $this->checkStokApotik( $row['id_obat'], $row['jumlah'] );

                // Menentukan kelompok racikan obat
                if( $row['id_kel_racik'] == 0 )
                    $id_kel_racik = "Non-Racikan";

                else
                    $id_kel_racik = "Racikan ".$row['id_kel_racik'];

                // Menentukan id obat
                if( $tipe_obat == 0 )
                    $tipe_obat = "Generik";

                else
                    $tipe_obat = "Non-Generik";
                
                if( $row['id_obat'] != '0' && $stok_available == true )
                {
                    $arr_result_tebus[sizeof($arr_result_tebus)] = array(
                        'id_transaksi' => $row['no_trans'],
                        'nama_obat' => $row['nama_obat'],
                        'jumlah_obat' => $row['jumlah'],
                        'tipe_obat' => $tipe_obat,
                        'kelompok_racikan' => $id_kel_racik,
                        'keterangan' => $row['keterangan_obat'],
                        'total_harga' => $row['total']
                    );
                }
            }
            
            $this->UserGrid->EditItemIndex=-1;
            $this->UserGrid->DataSource = $arr_result_tebus;
            $this->UserGrid->dataBind();
        }
	
	public function batalClicked()
        {
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->Reload();
	}
		
	public function checkRegister($sender,$param)
        {			
		$nofak=$this->notrans->text;
		$nocm=$this->noCM->text;
		$sql="SELECT 
                        tbm_obat.kode AS kode,
                        tbm_obat.nama AS nama,
                        tbt_obat_jual.jumlah,
                        tbt_obat_jual.cm,
                        tbt_obat_jual.no_trans
                      FROM
                        tbt_obat_jual
                        INNER JOIN tbm_obat ON (tbt_obat_jual.id_obat = tbm_obat.kode)
                      WHERE 
                        tbt_obat_jual.no_reg = '$nofak' AND
                        tbt_obat_jual.cm = '$nocm' ";
                
		$arr=$this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;
		}
    }
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function hapusClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
			
		$noTrans = $this->ambilTxt($this->DDtrans);				
		
		
				
		$sql="SELECT * FROM tbt_obat_jual WHERE no_reg = '$noTrans'";
				if($this->getViewState('modeTrans') == '1')
					$sql .= " AND tbt_obat_jual.flag = 1 ";
				
				if($this->getViewState('modeTrans') == '2')
					$sql .= " AND tbt_obat_jual.flag = 0 ";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			//$jmlAwal=$row['jumlah'];
			//$hrg=$row['hrg'];
			
			$cm=$row['cm'];
			$notrans=$row['no_trans']; //notrans tbt_obat_jual
			$notrans_rwtjln=$row['no_trans_rwtjln'];//notrans tbt_rawat_jalan
			$dokter=$row['dokter'];
			$klinik=$row['klinik'];
			$sumber=$row['sumber'];
			$tujuan=$row['tujuan'];
			$idObat=$row['id_obat'];
			$jumlah=$row['jumlah'];	
			$id_harga=$row['id_harga'];	
			$expired=$row['expired'];	
			
			$sqlStok="SELECT * FROM tbt_stok_lain WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";
			$arrStok=$this->queryAction($sqlStok,'S');
			foreach($arrStok as $rowStok)
			{
				$jumlah = $jumlah + $rowStok['jumlah'];
				//update tbt_stok_lain		
				$sql="UPDATE tbt_stok_lain SET jumlah = '$jumlah' WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired' ";	
				
				$update=$this->queryAction($sql,'C');
			}		
		}
		
		$sql = "DELETE FROM tbt_obat_jual WHERE no_reg = '$noTrans'";		
		$this->queryAction($sql,'C');
		
		$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Transaksi penjualan obat rawat jalan dengan no. resep '.$noTrans.' Telah Dihapus dari database.</p>\',timeout: 200000,dialog:{
			modal: true,
			buttons: {
						"OK": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							reloadpage();
						}
					}
		}});');	
					
		//$this->Response->Reload();
	}
	
	public function reloadpage($sender,$param)
    {  
        $this->checkInput();
		//$this->getPage()->getClientScript()->registerEndScript('reload', 'location.reload(true);');
        $this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
        //if(!$this->getViewState('datagridAll'))
            //$this->checkInput();
		
		//$this->hasilCariCtrl->Response->Reload();
	}
	
	/*public function reloadTable($sender,$param)
	{
		$arr_obat = Array();
		$arr_salinan = Array();
		$arr_ganti = Array();
		
		$this->UserGrid->DataSource = $arr_obat;
        $this->UserGrid->dataBind();
		
		$this->UserGrid2->DataSource = $arr_salinan;
		$this->UserGrid2->dataBind();
		
		$this->UserGrid3->DataSource = $arr_ganti;
		$this->UserGrid3->dataBind();
	
		$this->checkInput();
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}*/
	
	public function cetakClicked($sender,$param)
        {
		$nmTable = $this->getViewState('nmTable');
		$operator=$this->User->IsUserNip;
		$noTransRetur = $this->numCounter('tbt_obat_retur_jalan',ObatReturJalanRecord::finder(),'20');
		
		$sql="SELECT * FROM $nmTable WHERE jml_retur<>'0'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			//$jmlAwal=$row['jumlah'];
			//$hrg=$row['hrg'];
			
			$cm=$row['cm'];
			$notrans=$row['no_trans']; //notrans tbt_obat_jual
			$notrans_rwtjln=$row['no_trans_rwtjln'];//notrans tbt_rawat_jalan
			$noreg=$row['no_reg'];
			$dokter=$row['dokter'];
			$klinik=$row['klinik'];
			$sumber=$row['sumber'];
			$tujuan=$row['tujuan'];
			$idObat=$row['id_obat'];
			$id_harga=$row['id_harga'];	
			$expired=$row['expired'];
			$jumlah_sisa=$row['jumlah'];
			$hrg=$row['hrg'];
			$total=$row['total'];
			$jml_retur=$row['jml_retur'];
			$alasan=$row['alasan'];
			
				
			$noTrans = $this->ambilTxt($this->DDtrans);
				
			
				
			
			$sql="SELECT * FROM tbt_obat_jual WHERE no_trans='$notrans' AND cm='$cm' AND id_obat='$idObat' AND id_harga = '$id_harga' AND expired = '$expired' AND st_racik = '0' AND st_imunisasi = '0' ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$r_item = $row['r_item'];
				$r_racik = $row['r_racik'];
				$bungkus_racik = $row['bungkus_racik'];
			}
					
			//update tbt_obat_jual
			$total_real = $hrg * $jumlah_sisa;
			
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJln)->st_asuransi;
			if($stAsuransi == '1')
			{
				$hrg_bulat = $hrg;
				$total = $total_real + $r_item + $r_racik + $bungkus_racik;
				$totalRetur = $hrg * $jml_retur;
			}
			else
			{
				$hrg_bulat = $this->bulatkan($hrg);
				$total = $this->bulatkan($total_real + $r_item + $r_racik + $bungkus_racik);			
				$totalRetur = $hrg_bulat * $jml_retur;
			}
			
			if($jumlah_sisa > 0) //jika masih ada jumlah sisa
			{
				$sql="UPDATE 
						tbt_obat_jual 
					SET 
						jumlah = '$jumlah_sisa',
						total = '$total',
						total_real = '$total_real'  
					WHERE 
						no_trans = '$notrans'  ";	
				
				$update=$this->queryAction($sql,'C');
			}
			else //jika diretur semua jumlah nya
			{	
				//jika ada nilai r_item / r_racik / bungkus_racik
				if($r_item > 0 )
				{
					//cek jika masih ada obat lain selain obat yg diretur
					$sql="SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '$notrans_rwtjln' AND id_obat='$idObat' AND id_harga = '$id_harga' AND expired <> '$expired' ";
					$arr=$this->queryAction($sql,'S');
					if(count($arr) > 0)
					{
						foreach($arr as $row)
						{
							$noTransUpdate = $row['no_trans'];
							$noTransJlnUpdate = $row['no_trans_rwtjln'];
							$r_itemUpdate = $row['r_item'] + $r_item;
							
							$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
							$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJlnUpdate)->st_asuransi;
							if($stAsuransi == '1')
							{
								$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
							}
							else
							{
								$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
							}
							
						}
						
						$sql="UPDATE 
								tbt_obat_jual 
							SET 
								r_item = '$r_item',
								total = '$totalUpdate',
								total_real = '$totalRealUpdate'  
							WHERE 
								no_trans = '$noTransUpdate' ";						
						$update=$this->queryAction($sql,'C');
						
						$sql = "DELETE FROM tbt_obat_jual WHERE no_trans = '$notrans'";		
						$this->queryAction($sql,'C');
					}
					else
					{
						$sql="SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '$notrans_rwtjln' AND id_obat='$idObat' AND id_harga <> '$id_harga' ";
						$arr=$this->queryAction($sql,'S');
						if(count($arr) > 0)
						{
							foreach($arr as $row)
							{
								$noTransUpdate = $row['no_trans'];
								$noTransJlnUpdate = $row['no_trans_rwtjln'];
								$r_itemUpdate = $row['r_item'] + $r_item;
								
								$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
								$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJlnUpdate)->st_asuransi;
								if($stAsuransi == '1')
								{
									$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
								}
								else
								{
									$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
								}
							}
							
							$sql="UPDATE 
								tbt_obat_jual 
							SET 
								r_item = '$r_item',
								total = '$totalUpdate',
								total_real = '$totalRealUpdate'  
							WHERE 
								no_trans = '$noTransUpdate' ";						
							$update=$this->queryAction($sql,'C');
							
							$sql = "DELETE FROM tbt_obat_jual WHERE no_trans = '$notrans'";		
							$this->queryAction($sql,'C');
						}
						else
						{
							$sql="SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '$notrans_rwtjln' AND id_obat <> '$idObat' ";
							$arr=$this->queryAction($sql,'S');
							if(count($arr) > 0)
							{
								foreach($arr as $row)
								{
									$noTransUpdate = $row['no_trans'];
									$noTransJlnUpdate = $row['no_trans_rwtjln'];
									$r_itemUpdate = $row['r_item'] + $r_item;
									
									$totalRealUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									
									$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJlnUpdate)->st_asuransi;
									if($stAsuransi == '1')
									{
										$totalUpdate = $row['total_real'] + $r_item + $r_racik + $bungkus_racik;
									}
									else
									{
										$totalUpdate = $this->bulatkan($row['total_real'] + $r_item + $r_racik + $bungkus_racik);	
									}
								}
								
								$sql="UPDATE 
									tbt_obat_jual 
								SET 
									r_item = '$r_item',
									total = '$totalUpdate',
									total_real = '$totalRealUpdate'  
								WHERE 
									no_trans = '$noTransUpdate' ";					
								$update=$this->queryAction($sql,'C');
								
								$sql = "DELETE FROM tbt_obat_jual WHERE no_trans = '$notrans'";		
								$this->queryAction($sql,'C');
							}
							else
							{
								$sql = "DELETE FROM tbt_obat_jual WHERE no_trans = '$notrans'";		
								$this->queryAction($sql,'C');	
							}
						}
					}
				}
				else
				{
					$sql = "DELETE FROM tbt_obat_jual WHERE no_trans = '$notrans'";		
					$this->queryAction($sql,'C');
				}
			}
			
			//UPDATE tbt_stok_lain
			$sqlStok="SELECT * FROM tbt_stok_lain WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";
			$arrStok=$this->queryAction($sqlStok,'S');
			foreach($arrStok as $rowStok)
			{
				$jumlah = $jml_retur + $rowStok['jumlah'];
				//update tbt_stok_lain		
				$sql="UPDATE tbt_stok_lain SET jumlah = '$jumlah' WHERE sumber = '$sumber' AND tujuan = '$tujuan' AND id_obat = '$idObat' AND id_harga = '$id_harga' AND expired = '$expired'";				
				$this->queryAction($sql,'C');
			}
			
			
			//insert tbt_obat_retur_jalan				
			$newRetur= new ObatReturJalanRecord();
			$newRetur->no_trans=$noTransRetur;
			$newRetur->no_trans_rwtjln=$notrans_rwtjln;
			$newRetur->no_reg = $noreg;
			$newRetur->cm=$cm;			
			$newRetur->dokter=$dokter;
			$newRetur->klinik=$klinik;						
			$newRetur->tgl=date('y-m-d');
			$newRetur->wkt=date('G:i:s');
			$newRetur->operator=$operator;			
			$newRetur->sumber=$sumber;
			$newRetur->tujuan=$tujuan;
			$newRetur->id_obat=$idObat;
			$newRetur->id_harga=$id_harga;
			$newRetur->expired=$expired;
			$newRetur->jumlah_awal=$jml_retur+$jumlah_sisa;
			$newRetur->jumlah=$jml_retur;
			$newRetur->hrg=$hrg_bulat;
			$newRetur->total = $totalRetur;
			$newRetur->flag='0';
			$newRetur->alasan=$alasan;
			
			$newRetur->Save();	
		}
			
		//delete temp tabel
		$this->queryAction($this->getViewState('nmTable'),'D');
		
		//cetak kwitansi
		$this->Response->redirect($this->Service->constructUrl('Apotik.cetakReturObatPasien',
		array(
			'noTransRetur'=>$noTransRetur,
			'cm'=>$cm
			)));
	}
}
?>
