<?php
class AdminRwtJln extends SimakConf
{
    public function onInit($param)
    {
            parent::onInit($param);
            $tmpVar=$this->authApp('11');
            if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
                    $this->Response->redirect($this->Service->constructUrl('login'));
    }	
	 
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{					
			$this->RBjns->Visible=false;
			$this->RBjns->focus();
			$this->RBjns->SelectedValue=1;
			$this->RBjnsChanged($sender,$param);
			
			$this->karcisCtrl->Visible=false;
			
			$this->tdkPanel->Visible=false;
			$this->pdftrPanel->Visible=false;
			
			$this->showSecond->Visible=false;
			$this->showSecondPdftr->Visible=false;
			
			$this->showBayar->Visible=false;
			$this->dtGridCtrl->Display='None';
			$this->showBayarPdftr->Visible=false;
			
			$this->cetakBtn->Enabled=false;//false awalnya
			 
			//$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			//$this->DDKlinik->dataBind();
			$sql = "SELECT * FROM tbm_bhp_tindakan WHERE st = '0' ORDER BY nama ";			
			$this->DDbhp->DataSource = BhpTindakanRecord::finder()->findAllBySql($sql);
			$this->DDbhp->dataBind();
			
			$sql = "SELECT * FROM tbm_bhp_tindakan WHERE st = '1' ORDER BY nama ";			
			$this->DDalat->DataSource = BhpTindakanRecord::finder()->findAllBySql($sql);
			$this->DDalat->dataBind();
			
			$this->nmTindakan->Text='';
			$this->notrans->focus();
			$this->modeUrut->SelectedValue = '2';
		}	
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT *, IF (LENGTH(catatan) > 0,CONCAT(nama, ' (', catatan,')'),nama ) AS nama FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');					
			
			$this->UserGrid->DataSource=$arrData;//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
			
			foreach($arrData as $row)
			{
                            $jml += $row['jml'];
			}
			
			$this->setViewState('tmpJml',$jml);			
			$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			$this->dtGridCtrl->Display='Dynamic';
                        
                        $this->cetakBtn->Enabled="true";
                        $this->batalBtn->Enabled="true";
		}	
		else
		{
			$this->clearViewState('tmpJml');			
			$this->jmlShow->Text='Rp '.number_format(0,2,',','.');
			$this->dtGridCtrl->Display='None';
                        
			$this->UserGrid->DataSource='';//Insert new row in tabel bro...
			$this->UserGrid->dataBind();
		}
  }
	
	public function cmCallback($sender,$param)
        {
		$this->panel->render($param->getNewWriter());
	}
	
	public function checkRegister($sender,$param)
        {
            $ensure = "UPDATE tbm_obat set tipe=0 where tipe=2"; 
            $this->queryAction($ensure,'C');
            $this->DDRacik->Enabled = false;
            $this->Kemasan->Enabled=false;
            $this->jumlah_kemasan->Enabled=false;
            $cm=$this->formatCm($this->notrans->Text);
            $dateNow = date('Y-m-d');
            $this->jkel->Text = $this->cariJkel($cm);
            
            $sql = "SELECT *
                    FROM
                      tbt_rawat_jalan
                    WHERE
                      cm = '$cm' AND 
                      flag = '0' AND 
                      st_alih = '0' AND 
                      TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";

            $sqlAlih = "SELECT *
                        FROM
                          tbt_rawat_jalan
                        WHERE
                          cm = '$cm' AND 
                          flag = '0' AND 
                          st_alih = '1' AND 
                          TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";

            //if(RwtjlnRecord::finder()->findAll('cm = ? AND flag=0 AND st_alih=0 AND tgl_visit=? ',$cm,$dateNow)) 			
            if( RwtjlnRecord::finder()->findBySql($sql) )
            {
                $this->poliCtrl->Visible = true;

                $sql = "SELECT 
                        tbm_poliklinik.id,
                        tbm_poliklinik.nama
                      FROM
                        tbt_rawat_jalan
                        INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
                      WHERE
                        tbt_rawat_jalan.cm = '$cm' 
                        AND tbt_rawat_jalan.flag = 0
                        AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 
                        AND tbt_rawat_jalan.st_alih = 0";

                $arr = $this->queryAction($sql,'S');

                    $this->DDKlinik->DataSource = $arr;
                    $this->DDKlinik->dataBind();
                    $this->DDKlinik->focus();			
                    $this->errMsg->Text='';

                    if(count($arr) == '1')
                    {
                            $idPoli = PoliklinikRecord::finder()->findBySql($sql)->id;				
                            $this->DDKlinik->SelectedValue = $idPoli;
                            $this->DDKlinikChanged();
                    }


            }
            elseif(RwtjlnRecord::finder()->findBySql($sqlAlih))		
            {
                    $this->poliCtrl->Visible=false;
                    $this->showFirst->Visible=true;
                    $this->tdkPanel->Visible=false;
                    $this->showSecond->Visible=false;
                    $this->rspPanel->Visible = false;

                    $this->errMsg->Text = '    
                            <script type="text/javascript">
                                    alert("Pasien dengan No.Rekam Medis '.$cm.' sudah alih status ke Rawat Inap !");
                                    document.all.'.$this->notrans->getClientID().'.focus();
                            </script>';

                    $this->notrans->Text = '';
            }
            else
            {
                    $this->poliCtrl->Visible=false;
                    $this->showFirst->Visible=true;
                    $this->tdkPanel->Visible=false;
                    $this->showSecond->Visible=false;
                    $this->rspPanel->Visible = false;
                    $this->errMsg->Text = '    
                            <script type="text/javascript">
                                    alert("Pasien dengan No.Rekam Medis '.$cm.' belum melakukan pendaftaran!");
                                    document.all.'.$this->notrans->getClientID().'.focus();
                            </script>';

                    $this->notrans->Text = '';
            }
	}
        
        public function suggestNames($sender,$param) {
            // Get the token
            $token=$param->getToken();
            // Sender is the Suggestions repeater
            $sender->DataSource=$this->getDummyData($token);
            $sender->dataBind();          
        }
 
        public function suggestionSelected1($sender,$param) 
        {
            $id = $sender->Suggestions->DataKeys[$param->selectedIndex];

            if($id)
            {
                $this->kodeObat->Text=$id;
                $sql = "SELECT tipe FROM tbm_obat where nama = '". $id ."'";
                $what = $this->queryAction($sql,"S");

                if(sizeof($what) > 0)
                {
                    foreach($what as $row)
                    {
                        $tipe = $row['tipe'];
                    }

                    $this->RBtipeObat->SelectedValue = $tipe;
                    $this->RBtipeObat->Enabled = false;
                }      
            }
            
            else
            {
                    $this->kodeObat->Text = '';
            }
        }
        
    
    public function getDummyData($token){
                $sql = "SELECT nama FROM tbm_obat WHERE tbm_obat.nama LIKE '$token%' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
                $arr = $this->queryAction($sql,'S');
		return $arr;
    }
    
    // -- Function untuk mengambil nilai kemasan
    public function kemasanChanged()
    {
        if($this->Kemasan->SelectedValue == 0)
        {
            $this->jumlah_kemasan->Enabled = false;
        }
        
        else
        {
            $this->jumlah_kemasan->Enabled = true;
            $this->setViewState('kemasan', $this->Kemasan->SelectedValue);
        }
    }
    
        public function tipeRacikChanged()
	{
            $tipeRacik = $this->collectSelectionResult($this->RBtipeRacik);
            $this->setViewState('tipeRacik',$tipeRacik);
            
            // Kalau memang non-racikan
            if($tipeRacik == '0') //Non Racikan
            {
                $this->DDRacik->Enabled = false;
                $this->Kemasan->Enabled = false;
                
               
                $this->Kemasan->SelectedValue = 0;
                $this->jumlah_kemasan->Text = "";
            }
            
            // Kalau merupakan racikan
            else if($tipeRacik == '1') //racikan
            {
                $this->DDRacik->Enabled = true;
                $this->Kemasan->Enabled = true;
                
                // Masukan ke dalam dropdown racikan ke berapa, value yang ada di dalam array
                $this->DDRacik->DataSource = $this->getViewState('arrracikan');
                $this->DDRacik->dataBind();
            }
	}
        
        public function DDRacikChanged($sender,$param)
	{
            // Kalau bukan dengan pilihan -- Silahkan Pilih --
            if($this->DDRacik->SelectedValue != '')
            {
                // Kalau buat racikan baru
                if($this->DDRacik->SelectedValue == 0)//bikin racikan baru
                {
                    // Mengambil view state arr racikan
                    $arrracikan = $this->getViewState('arrracikan');
                    
                    // Kemasan dibuat true supaya bisa input kemasan
                    $this->Kemasan->Enabled=true;
                    $this->jumlah_kemasan->Enabled=true;
                    
                    //$this->Page->CallbackClient->focus($this->DDRacik);
                }
                    
                // Kalau misalnya racikan yang sudah exist
                else if( $this->DDRacik->SelectedValue != 0 )
                {
                    $this->Kemasan->Enabled=true;
                    
                    // Mengambil view state arr racikan
                    $arrracikan = $this->getViewState('arrracikan');
                    
                    foreach( $arrracikan as $row )
                    {
                        if( $row['id'] == $this->DDRacik->SelectedValue )
                        {
                            $this->Kemasan->SelectedValue = $row['id_kemasan'];
                            $this->jumlah_kemasan->Text = $row['jumlah_kemasan'];
                        }
                    }
                    
                    $this->Kemasan->Enabled = false;            // Menfalse kan enabled daripada kemasan
                    $this->jumlah_kemasan->Enabled = false;
                            
                    $this->Page->CallbackClient->focus($this->DDRacik);
                }	
            }
            
            // Kalau kelompok baru
            else
            {
                $this->Page->CallbackClient->focus($this->DDRacik);
            }	
	}
        
        public function checkgen($sender, $param)
        {
            if($this->cariNama->Text != '')
            {
                $name = $this->cariNama->Text;
                $tmp=$this->collectSelectionResult($this->RBtipeObat);
                
                if($name != "")
                {
                    $sql = "SELECT tipe FROM tbm_obat where nama = '". $name ."'";
                    $what = $this->queryAction($sql,"S");
                    
                    // Kalau memang ada tipenya
                    if( sizeof($what) > 0)
                    {
                        foreach($what as $row)
                        {
                            $tipe = $row['tipe'];
                        }
                        
                    
                        $this->RBtipeObat->SelectedValue = $tipe;
                        $this->RBtipeObat->Enabled = false;   
                    }
                    
                    else
                    {
                        $this->RBtipeObat->Enabled = true;
                        $this->RBtipeObat->SelectedValue = $tmp;
                        $this->RBtipeObat->Enabled = false;
                    }
                }
            }
        }

        public function checkTambahkan($sender, $param)
        {
            // Mengambil jumlah obat yang ada
            $jumlahObat = $this->jumlah_obat->Text;
            $namaobat = $this->cariNama->Text;
            $tipeObat = $this->RBtipeObat->SelectedValue;
            
            if($this->RBtipeRacik->SelectedValue != 0){
                if($this->Kemasan->SelectedValue == 0){
                    $this->getPage()->getClientScript()->registerEndScript
                    ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Silakan Pilih jenis Kemasan</p>\',timeout: 4000,dialog:{modal: true,
                    buttons: {
                            "OK": function() {
                                    jQuery( this ).dialog( "close" );
                            }
                    }
                    }});');	
                    $this->Page->CallbackClient->focus($this->Kemasan);
                }
                else if($this->jumlah_kemasan->Text ==""){
                    $this->getPage()->getClientScript()->registerEndScript
                    ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Jumlah Kemasan masih kosong</p>\',timeout: 4000,dialog:{modal: true,
                    buttons: {
                            "OK": function() {
                                    jQuery( this ).dialog( "close" );
                            }
                    }
                    }});');	
                    $this->Page->CallbackClient->focus($this->jumlah_kemasan);
                }

                else if($this->DDRacik->SelectedValue == ""){
                    $this->getPage()->getClientScript()->registerEndScript
                    ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Silakan Pilih Kelompok Racikan</p>\',timeout: 4000,dialog:{modal: true,
                    buttons: {
                            "OK": function() {
                                    jQuery( this ).dialog( "close" );
                            }
                    }
                    }});');	
                    $this->Page->CallbackClient->focus($this->DDRacik);
                }

                // -- Kalau nama obatnya kosong --
                else if( $namaobat == '' )
                {
                    $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Nama Obat Kosong</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                "OK": function() {
                                        jQuery( this ).dialog( "close" );
                                }
                        }
                    }});');	
                }

                // -- Kalau Tidak ada yang dipilih dari radio buttonnya
                else if( $tipeObat == '' )
                {
                    $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Tipe Obat harus ditentukan</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                "OK": function() {
                                        jQuery( this ).dialog( "close" );
                                }
                        }
                    }});');	
                }
                
                else if( is_numeric($jumlahObat) == "" )
                {
                    //$this->errMsgCtrl->Visible = true;
                    $this->getPage()->getClientScript()->registerEndScript
                            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Jumlah Obat harus diisi dengan angka</p>\',timeout: 4000,dialog:{modal: true,
                            buttons: {
                                    "OK": function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                    }});');	

                    $this->jumlah_obat->Text = "";
                }
                
                // -- Kalau bukan sebuah number, return true, akan masuk ke if
                else if( $jumlahObat <= 0 )
                {
                    //$this->errMsgCtrl->Visible = true;
                    $this->getPage()->getClientScript()->registerEndScript
                            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Jumlah Obat tidak boleh Negatif / Dibawah 0</p>\',timeout: 4000,dialog:{modal: true,
                            buttons: {
                                    "OK": function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                    }});');	

                    $this->jumlah_obat->Text = "";
                }

                // -- Kalau melewati semua validasi
                else
                {
                    $this->DDRacik->Enabled = false;
                    // -- Check apakah obat ini ada di dalam database atau tidak
                    $sql = "SELECT *
                        FROM
                        tbm_obat
                        WHERE
                        nama = '".$namaobat."'
                    ";
                    $arr = $this->queryAction($sql, 'S');

                    // -- Kalau memang ada setelah di retrieve, size tdk akan 0, masuk ke dalam tambhkan
                    if( sizeOf($arr) > 0 )
                    {
                        $this->tambah();
                    }

                    // -- Tapi kalau tidak ada, maka harus input kembali
                    else
                    {
                        /*
                        echo "<script type='text/javascript'>
                            if( !confirm('Obat '".$namaobat."' belum terdaftar di dalam database. Apakah yakin mau di input? ) )
                                return false;
                        <script>";*/

                        // -- Menentukan kode obat untuk obat yang baru
                        $sql = "SELECT MAX( kode ) AS k FROM tbm_obat";
                        $arr = $this->queryAction($sql,'S');

                        foreach( $arr as $row )
                            $kodeObat = floatval( $row['k'] );

                        $kodeObat++;

                        $prefix = "000000";


                        $kodeObat = substr($prefix.$kodeObat,-5);

                        $sql = "INSERT INTO
                            tbm_obat ( kode, nama, flag_input_dr, tipe )
                            VALUES( '".$kodeObat."', '".$namaobat."', '1', '".$tipeObat."' )
                        ";
                        $this->queryAction($sql,'C');

                        // -- Insert ke dalam tbt_obat_harga
                        $sql = "INSERT INTO
                           tbt_obat_harga ( kode, sumber, tgl )
                           VALUES( '".$kodeObat."', '01', NOW() )
                        ";
                        $this->queryAction($sql,'C');

                        // -- Mengambil id dari tbt_obat_harga
                        $sql = "SELECT id FROM tbt_obat_harga WHERE kode=" . $kodeObat;
                        $what = $this->queryAction($sql,"S");
                        foreach($what as $row){
                            $idharga = $row['id'];
                        }

                        // -- 
                        $sql = "INSERT INTO
                           tbt_stok_lain ( id_obat, id_harga, sumber, expired )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', NOW() )
                        ";
                        $this->queryAction($sql,'C');
                        $this->tambah();
                    }
                }
            }
            else{
                // -- Kalau nama obatnya kosong --
                if( $namaobat == '' )
                {
                    $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Nama Obat Kosong</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                "OK": function() {
                                        jQuery( this ).dialog( "close" );
                                }
                        }
                    }});');	
                }

                // -- Kalau Tidak ada yang dipilih dari radio buttonnya
                else if( $tipeObat == '' )
                {
                    $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Tipe Obat harus ditentukan</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                "OK": function() {
                                        jQuery( this ).dialog( "close" );
                                }
                        }
                    }});');	
                }

                // -- Kalau bukan sebuah number, return true, akan masuk ke if
               else if( is_numeric($jumlahObat) == "" )
                {
                    //$this->errMsgCtrl->Visible = true;
                    $this->getPage()->getClientScript()->registerEndScript
                            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Jumlah Obat harus diisi dengan angka</p>\',timeout: 4000,dialog:{modal: true,
                            buttons: {
                                    "OK": function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                    }});');	

                    $this->jumlah_obat->Text = "";
                }
                
                // -- Kalau bukan sebuah number, return true, akan masuk ke if
                else if( $jumlahObat <= 0 )
                {
                    //$this->errMsgCtrl->Visible = true;
                    $this->getPage()->getClientScript()->registerEndScript
                            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Jumlah Obat tidak boleh Negatif / Dibawah 0</p>\',timeout: 4000,dialog:{modal: true,
                            buttons: {
                                    "OK": function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                    }});');	

                    $this->jumlah_obat->Text = "";
                }

                // -- Kalau melewati semua validasi
                else
                {
                    $this->DDRacik->Enabled = false;
                    // -- Check apakah obat ini ada di dalam database atau tidak
                    $sql = "SELECT *
                        FROM
                        tbm_obat
                        WHERE
                        nama = '".$namaobat."'
                    ";
                    $arr = $this->queryAction($sql, 'S');

                    // -- Kalau memang ada setelah di retrieve, size tdk akan 0, masuk ke dalam tambhkan
                    if( sizeOf($arr) > 0 )
                    {
                        $this->tambah();
                    }

                    // -- Tapi kalau tidak ada, maka harus input kembali
                    else
                    {
                        /*
                        echo "<script type='text/javascript'>
                            if( !confirm('Obat '".$namaobat."' belum terdaftar di dalam database. Apakah yakin mau di input? ) )
                                return false;
                        <script>";*/

                        // -- Menentukan kode obat untuk obat yang baru
                        $sql = "SELECT MAX( kode ) AS k FROM tbm_obat";
                        $arr = $this->queryAction($sql,'S');

                        foreach( $arr as $row )
                            $kodeObat = floatval( $row['k'] );

                        $kodeObat++;

                        $prefix = "000000";


                        $kodeObat = substr($prefix.$kodeObat,-5);

                        $sql = "INSERT INTO
                            tbm_obat ( kode, nama, flag_input_dr, tipe )
                            VALUES( '".$kodeObat."', '".$namaobat."', '1', '".$tipeObat."' )
                        ";
                        $this->queryAction($sql,'C');

                        // -- Insert ke dalam tbt_obat_harga
                        $sql = "INSERT INTO
                           tbt_obat_harga ( kode, sumber, tgl )
                           VALUES( '".$kodeObat."', '01', NOW() )
                        ";
                        $this->queryAction($sql,'C');

                        // -- Mengambil id dari tbt_obt_harga
                        $sql = "SELECT id FROM tbt_obt_harga WHERE kode=" . $kodeObat;
                        $what = $this->queryAction($sql,"S");
                        foreach($what as $row){
                            $idharga = $row['id'];
                        }

                        // -- 
                        $sql = "INSERT INTO
                           tbt_stok_lain ( id_obat, id_harga, sumber, tgl )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', NOW() )
                        ";
                        $this->queryAction($sql,'C');
                        $this->tambah();
                    }            
                }
            }
        }
        
        public function tambah()
        {
            $this->simpanPanel->Visible = true;
            $cm=$this->formatCm($this->notrans->Text);
            $namaobat = $this->kodeObat->Text;
            $tipeobat = $this->RBtipeObat->SelectedValue;
            $jumlahobat = $this->jumlah_obat->Text;
            $keterangan = $this->keterangan->Text;
            $id_kemasan = $this->Kemasan->SelectedValue;   // Dalam bentuk id_kemasan, bukannya nama
            $jumlahkemasan = $this->jumlah_kemasan->Text;
            $tipe_obat_nama;
            
            // Kalau memang tidak ada kemasannya, maka kemasan = 0
            if($this->Kemasan->SelectedValue == 0)
            {
                $kemasan = "";
            }
            
            if( $tipeobat == 2 || $tipeobat == 0 )
            {
                $tipe_obat_nama = "Generik";					
            }
            
            else
            {
                $tipe_obat_nama = "Non-generik";					
            }
          
            // Kalau non-racikan
            if($this->RBtipeRacik->SelectedValue == 0 )
            {
                $id_kel_racik = 0;
            }
            
            // Kalau bagian Racikan
            else
            {
                // Kalau dia adalah 'buat kelompok baru'
                if($this->DDRacik->SelectedValue =='0')
                {
                    // Mengambil session arrracikan
                    $arrracikan =  $this->getViewState('arrracikan');
                    
                    // Mengambil nama kemasannya berdasarkana id kemasan yang dipilih
                    $sql = "SELECT nama from tbm_kemasan WHERE id='".$id_kemasan."'";
                    $array = $this->queryAction($sql,"S");
                    
                    foreach($array as $row)
                    {
                        $kemasan = $row['nama'];
                    }
                    
                    $i = 0;
                    while( $i < sizeOf($arrracikan) )
                    {
                        $bool_racikan_ada = false;   // Untuk menentukan apakah racikan index $i ada di dala $arrracikan, true = ada, false = tidak ada
                        
                        // Seiring berjalannya loop, maka mengetest apakah index racikan ini ada?
                        foreach( $arrracikan as $row )
                        {
                            // Kalau ada racikan dgn index ke $i
                            if( $row['id'] == $i )
                            {
                                $bool_racikan_ada = true;
                                        
                                // Langsung ke luar, untuk di tambahkan ke dalam arrracikan
                                break;
                            }
                        }
                        
                        // Kalau tidak ada, langsung break, return false
                        if( $bool_racikan_ada == false )
                            break;
                        
                        $i++;
                    }
                    
                    // Kalau memang $i = $arrracikam berarto urutan racikan benar, tidak bolong2. C/ : non-racikan, racikan 1, racikan 2, dll
                    if( $i == sizeOf($arrracikan) )
                    {
                        // Masukan id_kel_racik yang baru berdasarkan sizenya si arrracikan
                        $id_kel_racik = sizeOf($arrracikan);
                    }
                    
                    // Kalau memang $i != $arrracikam berarti urutan racikan tdk benar, bolong2. C/ : non-racika, racikan 2. racikan 1 nya ga ada
                    else
                    {
                        // Memasukan id kel racikan = $i
                        $id_kel_racik = $i;
                    }
                    
                    // Masukan lagi ke dalam array yang ada
                    // -- Kalau Array index ke id_kel_racik kosong
                    if( !isset($arrracikan[$id_kel_racik])  )
                    {
                        $arrracikan[$id_kel_racik] = array( 'id' => $id_kel_racik, 
                                                            'nama_racikan' => 'Racikan '.$id_kel_racik, 
                                                            'jumlah_kemasan' => $jumlahkemasan, 
                                                            'id_kemasan' => $id_kemasan );
                    }
                    
                    // -- Tetapi kalau ada isinya, kita pilih index sizeOf-nya untuk diisi (index terakhir) 
                    else
                    {
                        $arrracikan[sizeOf($arrracikan)] = array( 'id' => $id_kel_racik, 
                                                            'nama_racikan' => 'Racikan '.$id_kel_racik, 
                                                            'jumlah_kemasan' => $jumlahkemasan, 
                                                            'id_kemasan' => $id_kemasan );
                    }
                   
                    // Kemudian, isinya di sort berdasarkan id-nya
                    if( sizeOf($arrracikan) > 0 )
                    {
                        $id_ko_racik = array();
                        foreach ($arrracikan as $key => $row)
                        {
                            $id_ko_racik[$key] = $row['id'];
                        }
                        // Kemudian di sort berdasarkan id_kel_racik
                        array_multisort($id_ko_racik, $arrracikan);
                    }
                    
                   //asort($arrracikan);
                   
                   // Masukan ke dalam viewState lagi
                   $this->setViewState('arrracikan', $arrracikan);
                }
                
                // Kalau bukan 0, berarti dia adalah racikan yang sudah ada (entah 1, atau 2 atau racikan 3, dst)
                else
                {
                    $id_kel_racik =  $this->DDRacik->SelectedValue;
                    
                    $sql = "SELECT nama from tbm_kemasan WHERE id='" . $id_kemasan ."'";
                    $array = $this->queryAction($sql,"S");
                    foreach($array as $row)
                    {
                        $kemasan = $row['nama'];
                    }
                    $this->Kemasan->Enabled = false;
                }
            }
            
            // Kalau ada nama obatnya, tipe obatnya dan jumlah obatnya
            if($namaobat!="" && $tipeobat!="" && $jumlahobat!="")
            {
                $sql = "SELECT nama, kode from tbm_obat WHERE nama='" . $namaobat . "' LIMIT 1";
                $sqlarr = $this->queryAction($sql,"S");
                foreach($sqlarr as $row)
                {
                    $abcde = $row['nama'];
                    $kode = $row['kode'];
                }
                
                if($abcde =="")
                {
                    $namaobat = $this->cariNama->Text;
                }
                
                if($kode != ""){
                    $sql = "SELECT hrg_netto from tbt_obat_harga WHERE kode='" . $kode . "' LIMIT 1";
                    $sqlarr = $this->queryAction($sql,"S");
                    foreach($sqlarr as $row){
                        $hrg = $row['hrg_netto'];
                    }
                }
                else{
                    $hrg = 0;
                }
            
                $this->kodeObat->Text = "";
                $this->cariNama->Text = "";
                $this->jumlah_obat->Text = "";
                $this->keterangan->Text = "";
                $this->RBtipeObat->SelectedValue = "";
                $this->RBtipeObat->Enabled = true;
                $this->RBtipeRacik->SelectedValue = 0;
                $this->Kemasan->SelectedValue= "0";
                $this->jumlah_kemasan->Text = "";
                $this->Kemasan->Enabled= false;
                $this->jumlah_kemasan->Enabled = false;
                
                $this->cetakBtn->Visible=true;
                $this->batalBtn->Visible=true;
                
                $this->cetakBtn->Enabled=true;
                $this->batalBtn->Enabled=true;
                
                // Kalau uda ada viewstatenya, maka dimasukan ke dalam variable $arrResep
                if( $this->getViewState("arrResep") )
                    $arrResep = $this->getViewState("arrResep");

                if($id_kel_racik == 0)
                {
                    $tulisan = "Non-racikan";
                }
                
                else
                {
                    $tulisan = "Racikan ". $id_kel_racik;
                }
            
            // -- menentukan ID temporary di dalam view resep
            $id_temp = 0;       // untuk menyimpan id_arrresep yang digunakan untuk ke dalam viewnya
            if( sizeOf($arrResep) > 0 )
            {
                // Mengambil ID daripada obat yang terakhir
                foreach( $arrResep as $row )
                {
                    if( $row['id'] > $id_temp )
                        $id_temp = $row['id'];
                }
                
                $id_temp++;     // ditambah 1 karena id obat yang baru kan +1
            }
            
            // Ditambahkan value daripada si variable
            $arrResep[ sizeOf($arrResep) ] = array(
                    'id' => $id_temp,
                    'notrans' => '0',
                    'nama_obat' => $namaobat, 
                    'jumlah' => $jumlahobat, 
                    'tipe' => $tipe_obat_nama, 
                    'keterangan'=> $keterangan,
                    'harga' => $hrg,
                    'id_kel_racik' => $id_kel_racik,
                    'kelompok_racik' => $tulisan,
                    'kemasan' => $kemasan,
                    'jumlah_kemasan' =>$jumlahkemasan
                );
            
            // Pasang lagi ke dalam sessionnya
            
            $price = array();
            foreach ($arrResep as $key => $row)
            {
                $price[$key] = $row['id_kel_racik'];
            }
            array_multisort($price, $arrResep);
            $this->setViewState("arrResep", $arrResep );
            
            $this->Temporary_resep->Visible = true;
            
            $this->ObatGrid->DataSource = $this->getViewState("arrResep");
            $this->ObatGrid->dataBind();
            
            $this->DDRacik->DataSource = $this->getViewState("arrracikan");
            $this->DDRacik->dataBind();
            }
        }
               
        public function deleteItem($sender,$param)
        {
            // Ambil ID yang mau di delete
            $id = $this->ObatGrid->DataKeys[$param->Item->ItemIndex];
        
            $this->getPage()->getClientScript()->registerEndScript
            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Apakah Yakin akan menghapus obat ini?</p>\',timeout: 4000,dialog:{modal: true,
            buttons: {
                        "Ya": function() {
                                jQuery( this ).dialog( "close" );
                                deleteObat("'.$id.'");
                        },
                        Tidak: function() {
                                jQuery( this ).dialog( "close" );
                        }
                }
            }});');
        
        }
        
        // - Fungsi untuk mendelete obatnya
        public function deleteObat($sender,$param)
        {
            $id = $param->CallbackParameter->Id;
           
            // -- Retrieve array
            $arrResep = $this->getViewState("arrResep");
            $arrracikan = $this->getViewState("arrracikan");
            
            // Masukan nilai array yang paling besar ke dalam suatu variable
            $obatAkhir = $arrResep[sizeOf($arrResep)-1];
            
            //Kemudian hilangkam bagian terakhir daripada array
            unset( $arrResep[sizeOf($arrResep)-1] );
            
            $validasi = 0;
            $i = 0;
            if( sizeOf($arrResep) != 0)
            {
                foreach( $arrResep as $row )
                {
                   // Kalau id obat = id si obat yang mau di delete
                   if( $row['id'] == $id )
                   {
                       // Bagian obat yang akhir dimasukan ke dalam posisi obat yang mau di delete
                       $arrResep[$i] = $obatAkhir;
                       
                       // -- Kalau nilai no_transnya ada, bukan 0
                       if( $row['notrans'] != '0' )
                       {
                           // -- Delete dari tbt_obat_jual
                           $sql1 = 'DELETE FROM tbt_obat_jual WHERE no_trans = "'.$row['notrans'].'"';
                           $this->queryAction($sql1,'C');
                           $validasi = 1;
                       }
                   }
                   $i++;
                }
            }
            
            // Kalau validasi masih 0, berarto ga ke delete di dalam row. Berarti delete dari dalam database kalau ada
            if( $validasi == 0 && $obatAkhir['notrans'] != '0' )
            {
                // -- Delete dari tbt_obat_jual 
                $sql1 = 'DELETE FROM tbt_obat_jual WHERE no_trans = "'.$obatAkhir['notrans'].'"';
                $this->queryAction($sql1,'C');
            }
            
            // -- Sorting di dalam array
           
            // Kalau memang masih ada di dalam array resep
            if( sizeOf($arrResep) > 0 )
            {
                $id_kel_racik = array();
                foreach ($arrResep as $key => $row)
                {
                    $id_kel_racik[$key] = $row['id_kel_racik'];
                }
                // Kemudian di sort berdasarkan id_kel_racik
                array_multisort($id_kel_racik, $arrResep);
            }
            
            // Masukan lagi ke dalam viewstate
            $this->setViewState("arrResep", $arrResep);
            
            // -- Set lagi apa yang harus di set oleh ObatGrid
            // Kalau array kosong, maka tombol simpan jadi false
            if( sizeOf($arrResep) == 0 )
            {
                $this->cetakBtn->Enabled = false;
                $this->batalBtn->Enabled = false;
                $this->Temporary_resep->Visible = false;
            }
            
            // Kalau array ada isi, maka tombol simpan jadi true
            else
            {
                $this->cetakBtn->Visible = true;
                $this->batalBtn->Visible = true;
                $this->Temporary_resep->Visible = true;
            }
            
            // ----------
            
            // Menghilangkan List Racikan dari dalam tipe obat kalau sudah di delete semua komposisi racikan itu
            $a;
            $i = 0;
            
            // Mendeclare variable boolean yang menyatakan ada di dalam arr resep atau tidak sudah racikan ini
            $bool_racikan_ada = false;   // true -> ada, false -> tidak ada racikan ini
            
            // Step.1 Meretrieve semua data 1 per 1 dari viewstate id_kel_racik yang ada
            foreach( $arrracikan as $row )
            {
                $bool_racikan_ada = false;   // true -> ada, false -> tidak ada racikan ini
                
                // Step.2 Kalau dia itu $i == 0, maksudnya kan non-racikan, ya sudah biarin aja ga usa di cek, langsung continue
                if( $i == 0 )
                {
                    $i++;
                    continue;
                }
                
                // Step.3 mengambil semua nilai arr resep
                foreach( $arrResep as $row1 )
                {
                    // Step.4 Komparasi semua nilai arr_resep dengan id_kel_racik. Kalau ada racikan ini, maka di declare true. lalu langsung break
                    if( $row['id'] == $row1['id_kel_racik'] )
                    {
                        $bool_racikan_ada = true;
                        break;
                    }
                }
                
                // Step. 5 setelah kita mengetahui hasilnya, maka kalo falsem berarti di dalam arr_resep sudah tidak ada racikan ini, 
                // berati kita tahu pada saat $i segini, bagian $arr id_kel_raciknya di delete. Nah kita sekarang break programnya dulu
                if( $bool_racikan_ada == false )
                    break;
                
                $i++;
            }
            
            // Step.6 Kita akan menunset nilai dari si $arridkelracik di dalam index segitu kalau ternyata $bool_racikan_ada == false. 
            // Karena kalau true berarti masih ada komposisi racikan sekian di dalam $arrresep
            if( $bool_racikan_ada == false )
                unset( $arrracikan[$i] );
            
            // -- Sort si arrracikan
            if( sizeOf($arrracikan) > 0 )
            {
                $id_so_racik = array();
                foreach ($arrracikan as $key => $row)
                {
                    $id_so_racik[$key] = $row['id'];
                }
                // Kemudian di sort berdasarkan id_kel_racik
                array_multisort($id_so_racik, $arrracikan);
            }
            
            //asort($arrracikan);
            
            // -- Mengetahui apakah sudah ada harga resep di dalam database atau belum
            $cm = $this->formatCm($this->notrans->Text);
            $arr1 = $this->queryAction("Select no_trans from tbt_rawat_jalan where cm = '" . $cm ."' order by no_trans desc LIMIT 1","S");

            foreach($arr1 as $row)
            {
                $notransjalan = $row['no_trans'];
            }
            
            $eql = "SELECT jasa_resep FROM tbm_jasa_resep_racikan";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $roweql)
            {
                $ritem = $roweql['jasa_resep'];
            } 
             
            $eql = "SELECT jasa_racikan FROM tbm_jasa_resep_racikan";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $roweql)
            {
                $rracik = $roweql['jasa_racikan'];
            }
            
            $sql1 = "SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '".$notransjalan."'";
            $arr1 = $this->queryAction($sql1,'S');

            $bool_ritem = true; // true -> boleh input, false -> ga boleh input
            foreach($arr1 as $row1)
            {
                // Berarti kalau ada yang harga resepnya sudah tercantum
                if( $row1['r_item'] != 0 )
                    $bool_ritem = false;
            }
            
            // Kalau bool_ritem == true, berarti kita harus langsung mengupdate harga resep
            if( $bool_ritem == true )
            {
                // UPDATE si ritem dari resep yang ada di rawat jalan itu menjadi 3000
               $arr = $this->queryAction("SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '".$notransjalan."'","S");
                
                foreach($arr as $row)
                {
                    $q = "UPDATE tbt_obat_jual SET r_item = ".$ritem." WHERE id = ".$row['id'];
                    $sql1 = $this->queryAction($q,"C");
                    
                    // Cukup 1 obat aja makanya habis itu di break
                    break;
                }
            
            }
            
            // Cek apakah setiap resep yang nomor racikannya ini, mempunyai harga 5000 pada rraciknya atau tidak 
            foreach( $arrracikan as $row1 )
            {
                // Kalau misalnya idnya 0, berarti non racikan continue aja
                if( $row1['id'] == 0 )
                    continue;
                
                // Cek apakah sudah ada racikan dengan id ini dengan harga racikannya?
                $sql3 = "SELECT * FROM tbt_obat_jual WHERE no_reg = '".$notransjalan."' AND id_kel_racik = ".$row1['id'];
                $arr3 = $this->queryAction($sql3,'S');
                
                // Di cek 1 1 apakah ada yang harganya 5000, kalau belom ada di insert ke salah 1 row obat yang ada
                $bool_rracik = true;
                foreach($arr3 as $row2)
                {
                    if( $row2['r_racik'] != 0 )
                        $bool_rracik = false;
                }
                
                // Kalau $bool_rracik == true, berarti kita harus langsung mengupdate harga resep
                if( $bool_rracik == true )
                {
                   // Mencari harga kemasan yang ada
                   $q4 = "SELECT hrg FROM tbm_kemasan WHERE id = ".$row1['id_kemasan'];
                   $arr4 = $this->queryAction($q4,'S');
                   
                   // Mengambil bungkus racik dan memasukan ke dalam resep
                   foreach( $arr4 as $row4 )
                   {
                       $bungkus_racik = $row1['jumlah_kemasan']*$row4['hrg'];
                   }
                   
                   // UPDATE si rracik dari resep yang ada di rawat jalan itu menjadi 5000
                   $arr = $this->queryAction("SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '".$notransjalan."' AND id_kel_racik = ".$row1['id'],"S");

                   foreach($arr as $row)
                   {
                       $q = "UPDATE tbt_obat_jual SET r_racik = ".$rracik.", bungkus_racik = ".$bungkus_racik." WHERE id = ".$row['id'];
                       $sql1 = $this->queryAction($q,"C");

                       // Cukup 1 obat aja makanya habis itu di break
                       break;
                   }
                }
            }
            
            $sql1 = "SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '".$notransjalan."'";
            $arr1 = $this->queryAction($sql1,'S');
            
            // Set total untuk semuanya
            foreach($arr1 as $row1)
            {
                // Cari total harganya
                $total = $row1['r_item'] + $row1['r_racik'] + $row1['bungkus_racik'] + $row1['hrg_nett'] + $row1['hrg_ppn'] + $row1['hrg_nett_disc'] + $row1['hrg_ppn_disc'] + $row1['hrg'];                
            
                $sql2 = "UPDATE tbt_obat_jual SET total = ".$total.", total_real = ".$total." WHERE id = '".$row1['id']."'";
                $arr2 = $this->queryAction($sql2,'C');
            }
            
            // -- END -- UPDATE harga
            
            // Step.7 Masukan kembali kedalam view state.
            $this->setViewState('arrracikan', $arrracikan);
            
            // Step.8 Ubah di dalam view Gridnya DDRacik dan Selected Valuenya
            $this->DDRacik->DataSource = $this->getViewState('arrracikan');
            $this->DDRacik->dataBind();

            $this->DDRacik->SelectedValue = 0;
            
            // View lagi ke dalam bagian obatnya
            $this->ObatGrid->EditItemIndex=-1;
            $this->ObatGrid->DataSource=$this->getViewState("arrResep");
            $this->ObatGrid->dataBind();
        }
        
	public function DDKlinikChanged()
	{
		$this->DDDokter->Enabled = true;
		$this->DDDokter->focus();
		//$noTrans = $this->getViewState('noTransJalan');
		$cm=$this->formatCm($this->notrans->Text);
		$idKlinik = $this->DDKlinik->SelectedValue;
		$dateNow = date('Y-m-d');
				
		/*if($idKlinik == '06' || $idKlinik == '11')//IGD / PEMERIKSAAN KESEHATAN
		{
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				   tbd_pegawai,	
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND 
				  tbd_pegawai.kelompok = 1 AND
				  tbt_rawat_jalan.dokter = tbd_pegawai.id AND
				  tbd_pegawai.poliklinik = '01'
				  AND tbt_rawat_jalan.st_alih = 0 ";
		}
		elseif($idKlinik == '15')//HAJI
		{
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				   tbd_pegawai,	
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND 
				  tbd_pegawai.kelompok = 1 AND
				  tbt_rawat_jalan.dokter = tbd_pegawai.id AND
				  (tbd_pegawai.poliklinik = '01' OR tbd_pegawai.spesialis IS NOT NULL OR tbd_pegawai.spesialis <> '')
				  AND tbt_rawat_jalan.st_alih = 0 ";
		}
		else
		{*/
			$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
				  INNER JOIN tbd_pegawai ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
				  AND (tbt_rawat_jalan.dokter = tbd_pegawai.id)
				WHERE
				  tbt_rawat_jalan.cm = '$cm' AND 
				  tbt_rawat_jalan.flag = 0 AND 
				  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND
				  tbd_pegawai.kelompok = 1 AND 
				  FIND_IN_SET('$idKlinik',tbd_pegawai.poliklinik)
				  AND tbt_rawat_jalan.st_alih = 0";
		//}
		
		//$arr = $this->queryAction($sql,'S');
		$arr = $this->dokterList($idKlinik);		
		$this->DDDokter->DataSource = $arr;
		$this->DDDokter->dataBind();
		
		if(count($arr) == '1')
		{
			//$idDokter = PegawaiRecord::finder()->findBySql($sql)->id;
			$this->DDDokter->SelectedIndex = 0;
			$this->DDdokterChanged();
		}
		
		$umur = $this->cariUmur('0',$this->formatCm($this->notrans->Text),$idKlinik);								
		$this->umur->Text = $umur['years'];
	}
	
	public function DDdokterChanged()
        {
            $tglSkrg = date('Y-m-d');
            $cek=$this->formatCm($this->notrans->Text);
            $idKlinik = $this->DDKlinik->SelectedValue;
	
            // Membuat view state arrracikan untuk menyimpan data 
            $arrracikan [sizeOf($arrracikan)]= array("id" => 0, "nama_racikan" => "Buat Kelompok Baru", "jumlah_kemasan" => 0, "id_kemasan" => "0");
            $this->setViewState('arrracikan', $arrracikan);
            
            if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
            {
                $this->tdkPanel->Visible=true;
                $this->pdftrPanel->Visible=false;
                $this->cetakBtn->Visible=true;
                $tmp = $this->formatCm($this->notrans->Text);
                $dateNow = date('Y-m-d');
                $sql = "SELECT b.nama AS cm,						   
                            b.cm AS cr_masuk, 
                            a.no_trans AS no_trans,
                            a.penanggung_jawab AS penanggung_jawab,
                            a.st_asuransi AS st_asuransi,
                            a.dokter AS dokter,
                            c.nama AS wkt_visit,
                            d.nama AS tgl_visit,
                            a.flag
                        FROM tbt_rawat_jalan a, 
                            tbd_pasien b,
                            tbd_pegawai c,
                            tbm_poliklinik d
                        WHERE a.cm='$tmp'
                        AND a.cm=b.cm
                        AND a.dokter=c.id
                        AND a.id_klinik=d.id
                        AND a.flag='0'
                        AND a.id_klinik='$idKlinik'
                        AND a.st_alih='0'
                        AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', a.tgl_visit, a.wkt_visit))) / 3600 <= 24 ";		 		 
			
                $tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
                $this->nama->Text= $tmpPasien->cm;			
                $this->dokter->Text= $tmpPasien->wkt_visit;
                $this->klinik->Text= $tmpPasien->tgl_visit;
                $this->pjPasien->Text= $tmpPasien->penanggung_jawab;
			
                $cm = $tmpPasien->cr_masuk;
                $dokter = $tmpPasien->wkt_visit;
                $klinik = $tmpPasien->tgl_visit;

                $idDokter = RwtjlnRecord::finder()->findBySql($sql)->dokter;
                $stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;

                $this->setViewState('cm',$tmpPasien->cr_masuk);
                $this->setViewState('nama',$tmpPasien->cm);
                $this->setViewState('dokter',$tmpPasien->wkt_visit);
                $this->setViewState('klinik',$tmpPasien->tgl_visit);
                $this->setViewState('noTransJalan',$tmpPasien->no_trans);
                $this->setViewState('notrans',$this->formatCm($this->notrans->Text));
                $this->setViewState('stAsuransi',$stAsuransi);

                $cekRetribusi = KasirPendaftaranRecord::finder()->find('no_trans=?',$tmpPasien->no_trans);
			
                // Check apakah pemabayaran retribusi sudah diselesaikan atau belum
                // -- dibatalkan -- START OF PEMBAYARAN RETRIBUSI COMMENT
                /*
                if($cekRetribusi->st_flag == '0' || !$cekRetribusi)
                {
                    $this->errMsg->Text = '    
                    <script type="text/javascript">
                            alert("Pasien dengan No.Rekam Medis '.$cm.' belum menyelesaikan pembayaran retribusi!");
                            window.location="index.php?page=Pendaftaran.AdminRwtJln"; 
                    </script>';
                }
                
                else
                {
                */
                    $this->showSecond->Visible=true;
                    $this->notrans->Enabled=false;
                    $this->errMsg->Text='';			
                    //$this->id_tindakan->Enabled=true;				
                    $this->DDTindakan->Enabled=true;

                    $this->bindTindakan();

                    $this->tambahBtn->Enabled=true;
                    //$this->id_tindakan->focus();
                    $this->DDTindakan->focus();
                    $this->DDKlinik->Enabled=false;
                    $this->DDDokter->Enabled=false;
                    $this->RBjns->Enabled=false;
				
                    //cek apakah pasein baru atau bukan
                    $sql = "SELECT cm FROM tbd_pasien WHERE cm = '$cm' AND st_cetak_kartupasien = '0' ";
                    $arr = $this->queryAction($sql,'S');
				
                    if(count($arr) > 0)
                    {
                        //jika pasien baru => masukan tindakan2 wajib untuk pasien baru
                        $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE st = '2' ";
                        $arr = $this->queryAction($sql,'S');	

                        foreach($arr as $row)
                        {
                            $this->makeTmpTbl($row['id_tindakan']);
                        }
                    }
				
                    //masukan tindakan2 wajib yang sesuai poli masing
                    if($stAsuransi == '0')
                    {
                        $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter='$idDokter' ";
                        $arr = $this->queryAction($sql,'S');	
                        if(count($arr) > 0)
                        {
                            foreach($arr as $row)
                            {
                                $this->makeTmpTbl($row['id_tindakan']);
                            }
                        }
                        
                        else
                        {
                            $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter IS NULL ";
                            $arr = $this->queryAction($sql,'S');	
                            foreach($arr as $row)
                            {
                                $this->makeTmpTbl($row['id_tindakan']);
                            }
                        }
					
			$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='1' AND st_asuransi='0' ";
                    }
                    
                    elseif($stAsuransi == '1')
                    {
                        $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter='$idDokter' ";
                        $arr = $this->queryAction($sql,'S');	
					
                        if(count($arr) > 0)
                        {
                            foreach($arr as $row)
                            {
                                $this->makeTmpTbl($row['id_tindakan']);
                            }
                        }
                        
                        else
                        {
                            $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='0' AND st_asuransi='0' AND dokter IS NULL ";
                            $arr = $this->queryAction($sql,'S');	
                            foreach($arr as $row)
                            {
                                $this->makeTmpTbl($row['id_tindakan']);
                            }
                        }
					
                        $sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = '$idKlinik' AND st='1' AND st_asuransi='1' ";
                        //$sql = "SELECT id_tindakan FROM tbt_tindakan_wajib_rwtjln WHERE id_poli = ('$idKlinik' AND st='4') OR id_poli = ('$idKlinik' AND st='0') ";
                    }
				
                    $arr = $this->queryAction($sql,'S');	

                    foreach($arr as $row)
                    {
                            $this->makeTmpTbl($row['id_tindakan']);
                    }

                    $umur = $this->cariUmur('0',$this->formatCm($this->notrans->Text),$idKlinik);								
                    $this->umur->Text = $umur['years'];	
		//} -- END OF PEMBAYARAN RETRIBUSI COMMENT
            }
            
            else //jika RadioButton Pendaftaran yang dipilih
            {
                $this->tdkPanel->Visible=false;
                $this->pdftrPanel->Visible=true;
                $this->cetakBtn->Visible=true;
                
                if(PasienRecord::finder()->findAll('cm = ?',$cek)) //jika pasien ditemukan
                {			
                    $tmp = $this->formatCm($this->notrans->Text);
                    $dateNow = date('Y-m-d');
                    $sql = "SELECT b.nama AS cm,						   
                               b.cm AS cr_masuk, 
                               a.no_trans AS no_trans,
                               a.flag
                            FROM tbt_rawat_jalan a, 
                               tbd_pasien b							
                            WHERE a.cm='$tmp'
                            AND a.cm=b.cm
                            AND a.flag='0'
                            AND a.st_alih='0'
                            AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', a.tgl_visit, a.wkt_visit))) / 3600 <= 24 ";					 
				
                    $tmpPasien = RwtjlnRecord::finder()->findBySql($sql);
                    $this->namaPdftr->Text= $tmpPasien->cm;			
                    $this->setViewState('cm',$tmpPasien->cr_masuk);
                    $this->setViewState('nama',$tmpPasien->cm);
                    $this->setViewState('noTransJalan',$tmpPasien->no_trans);			
                    $this->setViewState('notrans',$this->formatCm($this->notrans->Text));
                    $this->showSecondPdftr->Visible=true;
                    $this->notrans->Enabled=false;
                    $this->errMsg->Text='';			
                    $this->DDKlinik->Enabled=false;
                    $this->DDDokter->Enabled=false;
                    $this->noKarcisPdftr->Enabled=false;
                    $this->RBjns->Enabled=false;
                    $this->DDjnsPdftr->focus();
		}
                
                else //jika pasien tidak ditemukan
                {
                    $this->showFirst->Visible=true;
                    $this->pdftrPanel->Visible=false;
                    $this->showSecondPdftr->Visible=false;
                    $this->errMsg->Text='No. Register tidak ada!!';
                    $this->notrans->focus();
                }
            }
            
            $this->rspPanel->Visible = true;
            
            // -- BAGIAN PEMBUATAN RESEP
            
            // - Mengambil data tbt_rawat_jalan dari cm yang ada paling terakhir
            $sql = "SELECT * FROM tbt_rawat_jalan WHERE cm='".$cm."' ORDER BY no_trans DESC LIMIT 0,1" ;
            $result = $this->queryAction($sql, "S");
            
            foreach($result as $row)
            {
                $trns = $row['no_trans'];
            }
            
            // -- Select transaksi obat penjualan, harus order by bungkus racik supaya yang ada harga racikannya di atas2, jadi mau di ambil dan di dimpan dalam view state dahulu
            $sql = "SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln='".$trns."' AND flag=0 AND dokter='".$idDokter."' AND klinik='". $idKlinik ."' ORDER BY bungkus_racik DESC" ;
            $result = $this->queryAction($sql, "S");
            
            $i=0;
             
            $racikan;          // Menyimpan detail racikan yang ada, nantinya dimasukan ke dalam viewstate (idracik, jmlh_kemasan, id_kemasan)
            $jumlahobat_ke;    // Menyimpan nilai dari jumlah kemasan apabila sudah terdapat di dalam suatu database. Hanya untuk view saja
            
            foreach($result as $row)
            {
                $kode = $row['id_obat'];
                $jumlahobat = $row['jumlah'];
                $hrg = $row['hrg_nett'];
                $keterangan = $row['keterangan_obat'];
                $id_kel_racik = $row['id_kel_racik'];
                $id_kemasan = $row['id_kemasan'];
                $total_racikan = $row['bungkus_racik'];
                $notrans = $row['no_trans'];
                
                
                // Kalau id_kemasannya == 0, berarti dia non_racikan, jumlah kemasannya juga 0
                if( $id_kemasan == '0' )
                {
                    $jumlahkemasan = '';
                    $kemasan = '';
                }
                
                // Kalau id_kemasannya ada [OBAT RACIKAN], maka dapatkan jumlah kemasannya
                else
                {
                    
                    // Untuk menentukan apakah racikan ini sudah ada di dalam 
                    $bool_racikan_ada = false;
                    
                    foreach( $arrracikan as $row1 )
                    {
                        // Kalau SUDAH ADA id_kel_racikan di dalam array, maka ya diambil dan dikasih flag true, kemudian di tulis kemasannya dan jumlah kemasannya
                        if( $row1['id'] == $id_kel_racik )
                        {
                            // Berarti di dalam array sudah ada, flag = true
                            $bool_racikan_ada = true;
                            
                            // -- Select nama kemasannya berdasarkan id kemasan yang ada.
                            $sql = "SELECT nama, hrg FROM tbm_kemasan WHERE id='".$row1['id_kemasan']."'";
                            $result = $this->queryAction($sql, "S");

                            foreach($result as $row)
                            {
                                $kemasan = $row['nama'];
                            }
                            
                            $jumlahkemasan = $row1['jumlah_kemasan'];
                        }
                    }
                    
                    // Berarti di dalam arrracikan belum ada yang id_kel_raciknya segini, berarti baru stor lagi.
                    if( $bool_racikan_ada == false )
                    {
                        // -- Select nama kemasannya berdasarkan id kemasan yang ada.
                        $sql = "SELECT nama, hrg FROM tbm_kemasan WHERE id='".$id_kemasan."'";
                        $result = $this->queryAction($sql, "S");

                        foreach($result as $row)
                        {
                            $kemasan = $row['nama'];
                            $jumlahkemasan = $total_racikan/$row['hrg'];
                        }
                        
                        $arrracikan[sizeOf($arrracikan)] = array("id" => $id_kel_racik, "nama_racikan" => "Racikan ".$id_kel_racik, "jumlah_kemasan" => $jumlahkemasan, "id_kemasan" => $id_kemasan);
                        $this->setViewState("arrracikan", $arrracikan);
                    }
                        
                    /*
                    // Kalau masih kosong jumlah_obatkenya, artinya belum disimpan nilai jumlah obatnya
                    if( $jumlahobat_ke[$id_kemasan] == '' )
                    {
                        // -- Select nama kemasannya berdasarkan id kemasan yang ada.
                        $sql = "SELECT nama, hrg FROM tbm_kemasan WHERE id='".$id_kemasan."'";
                        $result = $this->queryAction($sql, "S");

                        foreach($result as $row)
                        {
                            $kemasan = $row['nama'];
                            $jumlahkemasan = $total_racikan/$row['hrg'];
                        }
                        
                        // Sekarang masukan nilainya ke dalam var jumlahobat_ke, jadi kt bisa stor jumlah kemasannya
                        $jumlahobat_ke[$id_kemasan] = $jumlahkemasan;
                        
                        $racikan[sizeOf($racikan)] = array( 'id_racik' => $id_kel_racik, 'jumlah_kemasan' => $jumlahkemasan, 'id_kemasan' => $id_kemasan );
                    }
                    
                    // sekarang kalau sudah ada nilainya
                    else
                    {
                        $jumlahkemasan = $jumlahobat_ke[$id_kemasan];
                    }
                     * 
                     */
                }
                
                // -- Sort si arrracikan
                if( sizeOf($arrracikan) > 0 )
                {
                    $id_so_racik = array();
                    foreach ($arrracikan as $key => $row)
                    {
                        $id_so_racik[$key] = $row['id'];
                    }
                    // Kemudian di sort berdasarkan id_kel_racik
                    array_multisort($id_so_racik, $arrracikan);
                }
                
                //asort($arrracikan);

                // -- Select nama dan tipe obat yang ada.
                $sql = "SELECT nama, tipe FROM tbm_obat WHERE kode='".$kode."'";
                $result = $this->queryAction($sql, "S");
                foreach($result as $row){
                    $namaobat= $row['nama'];
                    $tipe = $row['tipe'];
                }
            
                // -- Mengubah tipe obat 
                if($tipe !="")
                {
                    if($tipe == 1 )
                    {
                        $tipe_obat_nama = "Non-generik";
                    }
                    
                    else
                    {
                        $tipe_obat_nama = "Generik";
                    }
                }
            
                // -- Menentukan nama racikan
                if($id_kel_racik != "")
                {
                    if($id_kel_racik == 0){
                        $tulisan = "Non-racikan";
                    }
                    else{
                        $tulisan = "Racikan ".$id_kel_racik;
                    }
                }
                
                 // -- Masukan ke dalam arr Resep
                $arrResep[sizeOf($arrResep)] = array(
                    'id' => sizeOf($arrResep),
                    'notrans' => $notrans,
                    'nama_obat' => $namaobat, 
                    'jumlah' => $jumlahobat, 
                    'tipe' => $tipe_obat_nama, 
                    'keterangan'=> $keterangan,
                    'harga' => $hrg,
                    'id_kel_racik' => $id_kel_racik,
                    'kelompok_racik' => $tulisan,
                    'kemasan' => $kemasan,
                    'jumlah_kemasan' =>$jumlahkemasan
                );
            }
            
            // Pasang lagi ke dalam sessionnya
            $price = array();
            
            // -- Kalau ada arr resepnya
            if( $arrResep )
            {
                foreach( $arrResep as $key => $row )
                {
                    $price[$key] = $row['id_kel_racik'];
                }
                array_multisort($price, $arrResep);
                $this->setViewState("arrResep", $arrResep );

                $this->ObatGrid->DataSource = $this->getViewState("arrResep");
                $this->ObatGrid->dataBind();
                $this->simpanPanel->Visible = true;
                $this->Temporary_resep->Visible = true;
                
                $this->cetakBtn->Enabled="true";
                $this->batalBtn->Enabled="true";
            }
            
            $no_kel_racik = 0;
            
            // Untuk Test Raymond
            //$arrracikan2[sizeOf($arrracikan2)]= array( 'id'=>'0', 'nama_racikan'=>'buaaattt' );
            //$arrracikan2[sizeOf($arrracikan2)] = array( 'id'=>'1', 'nama_racikan'=>'aabuaaattt' );
            //$this->setViewState("aa", $arrracikan2);
            //$this->DDRacik->DataSource =  $this->etViewState("aa");
            
            
            // Variable yang menyimpan list racikan berdasarkan di dalam resep sudha ada racikan apa
            $this->DDRacik->DataSource =  $this->getViewState("arrracikan");
            $this->DDRacik->dataBind();
            
            $this->DDRacik->SelectedValue = '0';
        }
	
	
	public function bindTindakan()
	{
		$modeUrut = $this->modeUrut->SelectedValue;
		$idKlinik = $this->DDKlinik->SelectedValue;
		  
		$sql="SELECT 
				  tbm_nama_tindakan.id,
				  tbm_nama_tindakan.nama,
				  tbm_tarif_tindakan.biaya1,
				  tbm_tarif_tindakan.biaya2,
				  tbm_tarif_tindakan.biaya3,
				  tbm_tarif_tindakan.biaya4
				FROM
				  tbm_tarif_tindakan
				  INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
				WHERE
				  (tbm_tarif_tindakan.biaya1) > 0
				  AND tbm_nama_tindakan.id_klinik = '$idKlinik'  ";
		
		if($modeUrut == '1') //berdasarkan id
			$sql .= "ORDER BY tbm_nama_tindakan.id ASC";
		else //berdasarkan nama
			$sql .= "ORDER BY tbm_nama_tindakan.nama ASC";
		
		
		//$this->DDTindakan->DataSource=NamaTindakanRecord::finder()->findAll();
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
		foreach($arr as $row)
		{
			$id = $row['id'];
			$nama = $row['nama'];
			$tarif = $row['biaya1'] + $row['biaya2'] + $row['biaya3'] + $row['biaya4'];
			
			$sql2 = "SELECT * FROM tbt_tindakan_wajib_rwtjln WHERE id_tindakan = '$id'";
			
			if($modeUrut == '1') //berdasarkan id
				{
					$namaTxt = $id.' - '.$nama.' - '.number_format($tarif,0,',','.');
				}
				else //berdasarkan nama
				{
					$namaTxt = $nama.' - '.$id.' - '.number_format($tarif,0,',','.');
				}	
				
				$data[]=array('id'=>$id,'nama'=>$namaTxt);
		}
	
		//$this->DDTindakan->DataSource = NamaTindakanRecord::finder()->findAllBySql($sql);
		$this->DDTindakan->DataSource =  $data;
		$this->DDTindakan->dataBind();
	}
	
	
	public function modeUrutChanged($sender,$param)
	{
	
	}
	
	public function icdBtnClicked($sender,$param)
	{
		$id = $sender->CommandParameter;
		$noTransJalan = $this->getViewState('noTransJalan');
		
		if( $this->getViewState('nmTable') )
		{
                    $nmTable = $this->getViewState('nmTable');	
                    $url = "index.php?page=Pendaftaran.ListTindakan&nmTable=".$nmTable."&klinik=".$this->DDKlinik->SelectedValue."&noTransJalan=".$noTransJalan;
		}
                
		else
                    $url = "index.php?page=Pendaftaran.ListTindakan&klinik=".$this->DDKlinik->SelectedValue."&noTransJalan=".$noTransJalan;
			
		$this->getPage()->getClientScript()->registerEndScript('',"tesFrame('$url',jQuery(window).width()-100,jQuery(window).height()-50,'Daftar Tindakan Rawat Jalan')");
        }
	
	public function prosesTambah($sender,$param)
	{
		$id = $param->CallbackParameter->Id;
		
		if($id)
		{
			if(!$this->getViewState('nmTable'))
				$this->setViewState('nmTable',$id);				
			else
				$nmTable = $this->getViewState('nmTable');				
		}
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
	public function makeTmpTbl($item)
    {
		$cm = $this->formatCm($this->notrans->Text);
		$dateNow = date('Y-m-d');
		$idKlinik = $this->DDKlinik->SelectedValue;	
		
		$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  id_klinik = '$idKlinik' AND 
					  flag = '0' AND 
					  st_alih = '0' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
		
		$noTransJalan = RwtjlnRecord::finder()->findBySql($sql)->no_trans;
		$stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;
		//$noTransJalan = RwtjlnRecord::finder()->find('cm = ? AND tgl_visit=? AND id_klinik=?',$cm,$dateNow,$idKlinik)->no_trans;		
		$this->setViewState('noTransJalan',$noTransJalan);
		$this->setViewState('stAsuransi',$stAsuransi);
		
		//cek sudah mendapatkan tindakan rwt jalan sebelumnya
		$sql = "SELECT id_tindakan FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJalan' AND klinik = '$idKlinik' AND id_tindakan='$item'";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) == 0)
		{
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_tdk VARCHAR(4) NOT NULL,
											 bhp FLOAT NOT NULL,
											 alat FLOAT NOT NULL,
											 total float NOT NULL,
											 jml float NOT NULL, 									 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
			}	
			
			
			$sql="SELECT * FROM $nmTable WHERE id_tdk = '$item'";
			$arr = $this->queryAction($sql,'S');			 
			
			if(count($arr) == '0')
			{
				$sql="SELECT 
					  tbm_nama_tindakan.id,
					  tbm_nama_tindakan.nama,
					  (tbm_tarif_tindakan.biaya1) AS total
					FROM
					  tbt_tindakan_wajib_rwtjln
					  INNER JOIN tbm_nama_tindakan ON (tbt_tindakan_wajib_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_tarif_tindakan ON (tbm_nama_tindakan.id = tbm_tarif_tindakan.id) 
							 WHERE tbm_nama_tindakan.id='$item' ";
				
				$arr = $this->queryAction($sql,'S');			 
				foreach($arr as $row)
				{	
					$nama = $row['nama'];
					
					//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH TINDAKAN YANG DIKENAKAN TERDAPAT DALAM TINDAKAN2 YANG DICOVER ASURANSI
					//JIKA YA => TARIF YANG DIPAKAI ADALAH TARIF YANG DICOVER ASURANSI TERSEBUT
					if($stAsuransi == '1')
					{
						$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
						$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
						
						$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$item' AND id_poli='$idKlinik'";
						if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
						{
							$total = ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif;	
						}
						else
						{
							$total = $row['total'];
						}
					}
					else
					{
						$total = $row['total'];
					}
				}
				
				$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
				$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
				
				$jml = $total + $bhp + $alat;
				
				$sql="INSERT INTO $nmTable (nama,bhp,alat,total,jml,id_tdk) VALUES ('$nama','$bhp','$alat','$total','$jml','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...					
			}
			
			
			$sql="SELECT *, IF (LENGTH(catatan) > 0,CONCAT(nama, ' (', catatan,')'),nama ) AS nama FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->UserGrid->DataSource=$arrData;
			$this->UserGrid->dataBind();								
			/*
			if($this->getViewState('tmpJml')){
				$t = (int)$this->getViewState('tmpJml') + $jml;
				$this->setViewState('tmpJml',$t);
			}else{
				$this->setViewState('tmpJml',$jml);
			}	
			
			$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
			*/
			$this->showBayar->Visible=true;
			$this->dtGridCtrl->Display='Dynamic';
			$this->DDTindakan->focus();
			$this->cetakBtn->Enabled=true;
			 $jml = 0;
			 $bhp = 0;
			 $alat = 0;
			 $total = 0;
		}	
	}
	
	public function prosesClicked()
    {	
		if($this->Page->IsValid)
		{
			$this->nmTindakan->Text='';
			$cm=$this->formatCm($this->notrans->Text);
			$dateNow = date('Y-m-d');
			$idKlinik = $this->DDKlinik->SelectedValue;	
			$noTransJalan = $this->getViewState('noTransJalan');
			$item = $this->DDTindakan->SelectedValue;
			$nmTdk = $this->ambilTxt($this->DDTindakan);
			
			//cek sudah mendapatkan tindakan rwt jalan sebelumnya
			$sql = "SELECT id_tindakan FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJalan' AND klinik = '$idKlinik' AND id_tindakan='$item'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question"><b>'.$nmTdk.'</b> sudah diberikan sebelumnya. <br/> Apakah akan melanjutkan penambahan tindakan ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'tidak\');
							}
						}
					}});');	
			}
			else
			{
				$this->prosesClickedTindakan();
			}
		}
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->dtGridCtrl->Display='Dynamic';
			$this->prosesClickedTindakan();	
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
		else
		{
			$this->Page->CallbackClient->focus($this->DDTindakan);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
	}
	
	public function prosesClickedCallback($sender,$param)
    {
		//$this->dtGridCtrl->render($param->getNewWriter());
		$this->btnPanel->render($param->getNewWriter());
	}
	
	public function prosesClickedTindakan()
  {	
		$noTransJalan = $this->getViewState('noTransJalan');
		$idKlinik = $this->DDKlinik->SelectedValue;	
		
		$item=$this->DDTindakan->SelectedValue;
			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
											 nama VARCHAR(30) NOT NULL,
											 id_tdk VARCHAR(4) NOT NULL,
											 bhp FLOAT NOT NULL,
											 alat FLOAT NOT NULL,
											 total float NOT NULL,
											 jml float NOT NULL, 									 
											 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			/*
			$klinik=$this->getViewState('id_klinik');
			if ($klinik=='05')
			{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=20000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Umum','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}else{
				$jml1=5000;								
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Pendaftaran','0','$jml1','$jml1','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...	
				$jml2=50000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Dokter Spesialis','0','$jml2','$jml2','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml3=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('Jasa Rumah Sakit','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$jml4=10000;							
				$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('USG','0','$jml3','$jml3','$item')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
			}
			*/			
			//$item=$this->id_tindakan->Text;
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		$sql="SELECT b.nama AS nama, 
					 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total,
					 a.bhp 
					 FROM tbm_tarif_tindakan a, 
						  tbm_nama_tindakan b 
					 WHERE a.id='$item' AND a.id=b.id";
		
		$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
		
		//$bhp = $this->getViewState('jmlBhp');						
		$nama=$tmpTarif->nama;
		
		//JIKA PASIEN ASURANSI/JAMPER => CEK APAKAH TINDAKAN YANG DIKENAKAN TERDAPAT DALAM TINDAKAN2 YANG DICOVER ASURANSI
		//JIKA YA => TARIF YANG DIPAKAI ADALAH TARIF YANG DICOVER ASURANSI TERSEBUT
		$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->st_asuransi;
		if($stAsuransi == '1')
		{
			$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransJalan)->penjamin;
			$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($noTransJalan)->perus_asuransi;
			
			$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider='$idPerusAsuransi' AND id_tindakan='$item' AND id_poli='$idKlinik'";
			if(ProviderDetailTindakanRecord::finder()->findBySql($sql))
			{
				$total = ProviderDetailTindakanRecord::finder()->findBySql($sql)->tarif;	
			}
			else
			{
				$total = $tmpTarif->total;
			}
		}
		else
		{
			$total = $tmpTarif->total;
		}							
		
		
		$bhp = BhpTindakanRecord::finder()->findByPk($this->DDbhp->SelectedValue)->tarif;
		$alat = BhpTindakanRecord::finder()->findByPk($this->DDalat->SelectedValue)->tarif;
		
		$jml = $total + $bhp + $alat;	
		
		$sql="INSERT INTO $nmTable (nama,bhp,alat,total,jml,id_tdk) VALUES ('$nama','$bhp','$alat','$total','$jml','$item')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT *, IF (LENGTH(catatan) > 0,CONCAT(nama, ' (', catatan,')'),nama ) AS nama FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();
		$this->bayar->Enabled=true;
		$this->bayarBtn->Enabled=true;		
		
		/*
		if($this->getViewState('tmpJml')){
			$t = (int)$this->getViewState('tmpJml') + $jml;
			$this->setViewState('tmpJml',$t);
		}else{
			$this->setViewState('tmpJml',$jml + $total + $bhp + $alat);
		}	
		
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
		*/
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		
		$this->showBayar->Visible=true;
		$this->dtGridCtrl->Display='Dynamic';
		//$this->id_tindakan->focus();
		$this->DDTindakan->focus();	
		$this->DDTindakan->SelectedValue = 'empty';
		$this->DDbhp->SelectedValue = 'empty';
		$this->DDalat->SelectedValue = 'empty';
		
		$this->cetakBtn->Enabled=true;
	}
	
	public function itemCreated($sender,$param)
	{
		$item=$param->Item;
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem' || $item->ItemType==='deleteItem')
		{
			$item->jmlColumn->Text = number_format($item->DataItem['jml'],2,',','.');
			$item->tanggunganColumn->Text = number_format($item->DataItem['tanggungan_asuransi'],2,',','.');
			
			/*$cm = $this->UserGrid->DataKeys[$item->ItemIndex];
			
			$conTgl = $this->ConvertDate($item->DataItem['tgl_lahir'],'3');
			$item->tglColumn->Text = $conTgl;
			
			if($item->DataItem['jns_kelamin'] == '0')
			$item->jnsKelColumn->Text =  "Laki-Laki";
			else
			$item->jnsKelColumn->Text =  "Perempuan";	*/
		}
	}
	
    public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT jml FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['jml'];					
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');	
				
				$sql="SELECT *, IF (LENGTH(catatan) > 0,CONCAT(nama, ' (', catatan,')'),nama ) AS nama FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();		
				$this->bayar->Text='';											
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				
				if(count($arrData)==0)
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
					$this->clearViewState('nmTable');
					$this->clearViewState('tmpJml');
					$this->cetakBtn->Enabled=true;//awalnya false
					$this->showBayar->Visible=false;
					$this->dtGridCtrl->Display='None';
					//$this->id_tindakan->Enabled=true;
					$this->DDTindakan->Enabled=true;
					$this->jmlShow->Text='Rp '.number_format('0',2,',','.');
					$this->sisaByr->Text='';
                                        
                                        $this->cetakBtn->Enabled="false";
                                        $this->batalBtn->Enabled="false";
				}
			//}	
			
		//}	
    }
	
	public function bayarClicked($sender,$param)
    {
		//$this->showSql->text='-'.$this->getViewState('noTransJalan');	
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text='Rp ' . $hitung;
			$this->setViewState('sisa',$hitung);
			//$this->id_tindakan->Enabled=false;
			$this->DDTindakan->Enabled=false;
			
			$this->tambahBtn->Enabled=false;
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->focus();
			$this->cetakBtn->Enabled=false;
		}
	}
	
	public function bayarClickedPdftr($sender,$param)
    {
		if($this->bayarBtnPdftr->Text=='Refresh')
		{
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$this->getViewState('noTransJalan');
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->formatCm($this->notrans->Text));
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('id_klinik');
			$this->clearViewState('id_dokter');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
				
			$this->batalClicked();
		}
		else
		{			
			if($this->bayarPdftr->Text >= $this->getViewState('jmlBayarPdftr'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayarPdftr->Text)-TPropertyValue::ensureFloat($this->getViewState('jmlBayarPdftr'));
				$this->sisaByrPdftr->Text='Rp ' . number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->DDjnsPdftr->Enabled=true;			
				$this->errByrPdftr->Text='';		
			

				$this->cetakBtn->Enabled=true;
				
				$this->DDjnsPdftr->Enabled=false;
				$this->bayarPdftr->Enabled=false;
				$this->bayarBtnPdftr->Text='Refresh';
			}
			else
			{
				$this->errByrPdftr->Text='Jumlah pembayaran kurang';	
				$this->bayarPdftr->focus();
			}
		}		
	}

	
	public function showNotrans($sender,$param)
	{					
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		$this->notrans->Enabled=true;
		$this->notrans->focus();		
	}
	
	
	public function RBjnsChanged($sender,$param)
    {
				
		if($this->RBjns->SelectedValue==1)
		{
			$this->noKarcisPdftr->Text='';
			$this->karcisCtrl->Visible=false;
			
			$this->DDKlinik->Enabled=true;
			$this->DDKlinik->focus();
		}
		elseif($this->RBjns->SelectedValue==2)
		{
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=false;
		
			$this->karcisCtrl->Visible=true;
			$this->noKarcisPdftr->Enabled=true;
			$this->noKarcisPdftr->focus();
			
			//$this->DDKlinik->Enabled=false;
		}
		else
		{
			$this->karcisCtrl->Visible=false;
			$this->DDKlinik->Enabled=false;
		}
	}
	
	public function noKarcisPdftrChanged($sender,$param)
    {   
		if(strlen($this->noKarcisPdftr->Text)!=8)
		{
			$this->errMsgNoKarcis->Text='No. karcis harus 8 digit!';	
		}
		else
		{
			if(KasirPendaftaranRecord::finder()->findAll('no_karcis = ?',$this->noKarcisPdftr->Text)) //jika no_karcis ditemukan
			{	
				$this->errMsgNoKarcis->Text='No. karcis sudah ada!!';
				$this->noKarcisPdftr->focus();
			}
			else //jika no_karcis tidak ditemukan
			{
				$this->errMsgNoKarcis->Text='';
				$this->DDKlinik->Enabled=true;
				$this->DDKlinik->focus();
			}
		}
    }
	
	public function checkRM($sender,$param)
    {   
		if($this->DDTindakan->SelectedValue!='')
		{
			//$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
			//$nmTdk=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);
			$this->nmTindakan->Text = '';
			$this->test->Text= $this->DDTindakan->SelectedValue;
			$nmTdk=TarifTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue);
			if(count($nmTdk)<1)
			{
				$this->nmTindakan->Text='Kode tindakan tidak ada!';
				$this->nmTindakan->ForeColor="#FF0000";
				//$this->id_tindakan->focus();
				$this->DDTindakan->focus();
				
				$this->tambahBtn->Enabled=false;
				$this->nmTindakan->focus();
			}
			else
			{
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->id_tindakan->Text)->nama;
				//$this->nmTindakan->Text='Nama Tindakan : '.NamaTindakanRecord::finder()->findByPk($this->DDTindakan->SelectedValue)->nama;
				$this->nmTindakan->ForeColor="#000000";
				$this->tambahBtn->Enabled=true;
				$this->DDbhp->focus();
			}
		}
		else
		{
			$this->nmTindakan->Text='';
		}		
		
    }
	
	public function DDjnsPdftrChanged($sender,$param)
    {   		
		if($this->DDjnsPdftr->SelectedValue==1)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='12000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==2)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='6000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		elseif($this->DDjnsPdftr->SelectedValue==3)
		{
			$this->showBayarPdftr->Visible=true;
			$jmlBayarPdftr='10000';
			$this->setViewState('jmlBayarPdftr',$jmlBayarPdftr);
			$this->jmlShowPdftr->Text='Rp. '.number_format($jmlBayarPdftr,2,',','.');
		}
		else
		{
			$this->showBayarPdftr->Visible=false;
			$this->jmlShowPdftr->Text='';
			$this->clearViewState('jmlBayarPdftr');
		}		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	/*public function cetakClicked($sender,$param)
        {		
            $sisaByr=$this->getViewState('sisa');
            $jmlTagihan=$this->getViewState('tmpJml');
            $table=$this->getViewState('nmTable');
            $cm=$this->formatCm($this->notrans->Text);

            $nama=$this->getViewState('nama');
            $dokter=$this->getViewState('dokter');
            $klinik=$this->getViewState('klinik');
            $id_dokter=$this->DDDokter->SelectedValue;
            $id_klinik=$this->DDKlinik->SelectedValue;
            $operator=$this->User->IsUserName;
            $nipTmp=$this->User->IsUserNip;
		
            if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
            {
                $jmlBayar=$this->bayar->Text;
                $pjPasien=$this->pjPasien->Text;
			
                $sql="SELECT * FROM $table ORDER BY id";
                $arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
                foreach($arrData as $row)
                {
                    $notrans = $this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan

                    $transRwtJln= new KasirRwtJlnRecord();
                    $transRwtJln->no_trans=$notrans;
                    $transRwtJln->no_trans_rwtjln=$this->getViewState('noTransJalan');
                    $transRwtJln->cm=$cm;
                    $transRwtJln->klinik=$id_klinik;
                    $transRwtJln->dokter=$id_dokter;
                    $transRwtJln->id_tindakan=$row['id_tdk'];
                    $transRwtJln->tgl=date('y-m-d');
                    $transRwtJln->waktu=date('G:i:s');
                    $transRwtJln->operator=$nipTmp;
                    $transRwtJln->bhp=$row['bhp'];
                    $transRwtJln->sewa_alat=$row['alat'];
                    $transRwtJln->tarif=$row['total'];
                    $transRwtJln->total=$row['jml'];
                    $transRwtJln->tanggungan_asuransi=$row['tanggungan_asuransi'];
                    $transRwtJln->disc=$row['disc'];
                    $transRwtJln->st_flag='0';
                    $transRwtJln->disc='0';
                    $transRwtJln->st_kredit='0';
                    $transRwtJln->pengali=$row['pengali'];
                    $transRwtJln->catatan=$row['catatan'];
                    $transRwtJln->Save();			
                }	
			
                //update st_cetak_kartupasien di tbd_pasien
                $sql = "UPDATE tbd_pasien SET st_cetak_kartupasien = '3' WHERE cm = '$cm' AND st_cetak_kartupasien = '1' ";
                $this->queryAction($sql,'C');

                //Update status tbt_rawat_jalan
                /*
                $sql="UPDATE 
                                tbt_rawat_jalan 
                        SET 
                                flag='1' 
                        WHERE 
                                cm='$cm' 
                                AND id_klinik='$id_klinik'
                                AND dokter='$id_dokter'
                                AND flag='0' 
                                AND st_alih='0' ";		
                $this->queryAction($sql,'C');			
                */
                //$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('DDRacikChangedipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'table'=>$table,'pjPasien'=>$pjPasien)));

                //$this->batalClicked();	
                //$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJlnSukses'));
		/*
                $this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
                $this->clearViewState('nmTable');

                $this->errMsg->Text = '    
                <script type="text/javascript">
                        alert("Data Rekam Billing Tindakan Rawat Jalan Telah Masuk Dalam Database.");
                        window.location="index.php?page=Pendaftaran.AdminRwtJln"; 
                </script>';
            }
            
            else //jika RadioButton Pendaftaran yang dipilih
            {	
                $jmlBayar=$this->bayarPdftr->Text;
                $pjPasien=$this->pjPasienPdftr->Text;
                $notrans=$this->numCounter('tbt_kasir_pendaftaran',KasirPendaftaranRecord::finder(),'09');//key '09' adalah konstanta modul untuk Kasir Pendaftaran

                $newRecord= new KasirPendaftaranRecord();
                $newRecord->no_trans=$notrans;
                $newRecord->no_trans_pdftr=$this->getViewState('noTransJalan');			
                $newRecord->klinik=$this->getViewState('id_klinik');
                $newRecord->dokter=$this->getViewState('id_dokter');
                $newRecord->no_karcis=$this->noKarcisPdftr->Text;
                $newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
                $newRecord->tgl=date('y-m-d');
                $newRecord->waktu=date('G:i:s');
                $newRecord->operator=$this->User->IsUserNip;
                $newRecord->tarif=$this->getViewState('jmlBayarPdftr');
                $newRecord->st_flag='0';
                $newRecord->Save();			
						
                $rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->formatCm($this->notrans->Text));
                $rwtJlnTmp->flag='1';
                $rwtJlnTmp->Save();	

                $this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$this->getViewState('jmlBayarPdftr'),'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'jnsPdftr'=>$this->DDjnsPdftr->SelectedValue,'pjPasien'=>$pjPasien)));
            }
                
	}*/
        	
        public function simpanClicked($sender,$param)
        {
            $operator=$this->User->IsUserName;
            $cm=$this->formatCm($this->notrans->Text);
            $idKlinik = $this->DDKlinik->SelectedValue;

            $array = $this->getViewState('arrResep');
            $arrracikan = $this->getViewState('arrracikan');
            
            foreach($array as $row)
            {
                $notransobat[sizeOf($notransobat)] = $row['notrans'];
                $cariNama[sizeOf($cariNama)] = $row['nama_obat'];
                $jumlahobat[sizeOf($jumlahobat)] = $row['jumlah'];
                $tipeobat[sizeOf($tipeobat)] = $row['tipe'];
                $keterangan[sizeOf($keterangan)] = $row['keterangan'];
                $idracik[sizeOf($idracik)] = $row['id_kel_racik'];
                $kemasan[sizeOf($kemasan)] = $row['kemasan'];
                $jumlahkemasan[sizeOf($jumlahkemasan)] = $row['jumlah_kemasan'];
            }
            
            $timezone = date_default_timezone_get();
            date_default_timezone_set($timezone);
            $dateNow = date('Y/m/d H:i:s');
            $timeNow = substr($dateNow,-8);
            $digit = 0;
            $string = '00000';
            $apa = substr($string.$digit, -6);
                
                $aql = "SELECT 
                        tbd_pegawai.id
                      FROM
                        tbt_rawat_jalan
                        INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
                        INNER JOIN tbd_pegawai ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
                        AND (tbt_rawat_jalan.dokter = tbd_pegawai.id)
                      WHERE
                        tbt_rawat_jalan.cm = '$cm' 
                      AND 
                        tbt_rawat_jalan.flag = 0 
                      AND 
                        TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 AND
                        tbd_pegawai.kelompok = 1 
                      AND 
                        FIND_IN_SET('$idKlinik',tbd_pegawai.poliklinik)
                      AND
                        tbt_rawat_jalan.st_alih = 0";
	
		$buset = $this->queryAction($aql,"S");	
                
		foreach($buset as $row)
                {
                    $idDokter = $row['id'];
                }
                
                $arr1 = $this->queryAction("Select nip from tbd_user where real_name = '" . $operator ."'","S");
                foreach($arr1 as $row){
                    $operator = $row['nip'];
                }
                
                $notrans = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
                
                $qwer = $this->queryAction("SELECT no_reg FROM tbt_obat_jual ORDER BY no_reg DESC LIMIT 1","S");
                foreach($qwer as $row){
                    $a = $row['no_reg'];
                }
                
                /*
                // Membuat no_regestrasi yang ada berdasarkan format yang suda ditentukan
                $noreg = substr($dateNow,0,4) . substr($dateNow,5,2) . substr($dateNow,8,2) . $apa;
                if($a > $noreg){
                    $noreg = $a;
                    $noreg++;
                }
                else{
                    $noreg++;
                }
                */
                
                // Mengambil nomor transrwtjaln
                $arr1 = $this->queryAction("Select no_trans from tbt_rawat_jalan where cm = '" . $cm ."' order by no_trans desc LIMIT 1","S");
                foreach($arr1 as $row){
                    $notransjalan = $row['no_trans'];
                }
                
                $pivot = 0;
                        
                // -- Mengetahui apakah sudah ada harga resep di dalam database atua belum
                $sql1 = "SELECT * FROM tbt_obat_jual WHERE no_trans_rwtjln = '".$notransjalan."'";
                $arr1 = $this->queryAction($sql1,'S');
                
                $bool_ritem = true; // true -> boleh input, false -> ga boleh input
                foreach($arr1 as $row1)
                {
                    // Berarti kalau ada yang harga resepnya sudah tercantum
                    if( $row1['r_item'] == 3000 )
                        $bool_ritem = false;
                }
                
                // -- Menginput obat 1 per 1 ke dalam resep
                for( $i = 0 ; $i < sizeOf( $cariNama ) ; $i++ )
                {
                    // -- Mendapatkan id obat berdasarkan nama yang ada --
                    $wql = "SELECT kode FROM tbm_obat where nama = '".$cariNama[$i]."'";
                    $what = $this->queryAction($wql,"S");

                    foreach($what as $row)
                    {
                       $idObat = $row['kode'];
                    }
                    
                    // -- Mendapatkan Harga dari Obat --
                    
                    $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
                    $asdf = $this->queryAction($eql,"S");

                    foreach($asdf as $row)
                    {
                       $idHarga = $row['id'];
                       $hrg_nett = $row['hrg_netto'];
                       $hrg_ppn = $row['hrg_ppn'];
                       $hrg_nett_disc = $row['hrg_netto_disc'];
                       $hrg_ppn_disc = $row['hrg_ppn_disc'];
                    }
                    
                    $ritem;
                    // -- Kalau dia obat yang pertama && belom ada harga resep di dalam resep
                    if($i == 0 && $bool_ritem == true)
                    {
                        $eql = "SELECT jasa_resep FROM tbm_jasa_resep_racikan";
                        $asdf = $this->queryAction($eql,"S");
                        
                        foreach($asdf as $roweql)
                        {
                            $ritem = $roweql['jasa_resep'];
                        }
                    }
                    
                    else
                    {
                        $ritem = 0;
                    }
                    
                    // -- Mengambil expire 
                    $eql = "SELECT expired FROM tbt_stok_lain where id_obat =".$idObat;
                    $qop = $this->queryAction($eql,"S");
                    foreach($qop as $row)
                    {
                       $expire = $row['expired'];
                    }
                    
                    // -- Mengambil harga kemasan
                    $eql = "SELECT id, hrg FROM tbm_kemasan where nama ='". $kemasan[$i] ."'";
                    $asdf = $this->queryAction($eql,"S");

                    foreach($asdf as $row)
                    {
                       $harga_kemasan = $row['hrg'];
                       $id_kemasan = $row['id'];
                    }
                    
                    if ($id_kemasan == "")
                    {
                        $id_kemasan = 0;
                    }
                    
                    $rracik;
                    $idracik_float = floatval($idracik[$i]);
                    
                    // Cek apakah sudah ada racikan dengan id ini dengan harga racikannya?
                    $sql3 = "SELECT * FROM tbt_obat_jual WHERE no_reg = '".$notransjalan."' AND id_kel_racik = ".$idracik_float." AND r_racik <> 0";
                    $arr3 = $this->queryAction($sql3,'S');
                    
                    // Kalau belum ada dan memang sebuah racikan berarti insert sekarang
                    if( sizeOf($arr3) == 0 && $idracik_float != 0 )
                    {
                        $eql = "SELECT jasa_racikan FROM tbm_jasa_resep_racikan";
                        $asdf = $this->queryAction($eql,"S");
                        
                        foreach($asdf as $roweql)
                        {
                            $rracik = $roweql['jasa_racikan'];
                        }

                        // -- Harag bungkus racikan
                        $bungkusracik = $harga_kemasan * $jumlahkemasan[$i];
                    }
                    
                    // Kalau sudah ada, berarti 0
                    else
                    {
                        $rracik = 0;
                        $bungkusracik = 0;
                    }
                    
                    // -- Menentukan status racikan. Kalau non racikan, $st_racik = 0 ( dalam benuk char )
                    if( $idracik_float == 0 )
                        $st_racik = '0';
                    
                    // -- Kalau racikan, $st_racik = 1 ( dalam bentuk char )
                    else
                        $st_racik = '1';
                    
                    // Mengambil total harga
                    $total = $hrg_nett + $hrg_ppn + $hrg_nett_disc + $hrg_ppn_disc + $ritem + $rracik + $bungkusracik;
                    
                    $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
                    $asdf = $this->queryAction($eql,"S");

                    foreach($asdf as $row)
                    {
                       $idHarga = $row['id'];
                       $hrg_nett = $row['hrg_netto'];
                       $hrg_ppn = $row['hrg_ppn'];
                       $hrg_nett_disc = $row['hrg_netto_disc'];
                       $hrg_ppn_disc = $row['hrg_ppn_disc'];
                    }

                    $eql = "SELECT expired FROM tbt_stok_lain where id_obat =".$idObat;
                    $qop = $this->queryAction($eql,"S");
                    foreach($qop as $row)
                    {
                       $expire = $row['expired'];
                    }
                    
                    // Finally insert. Tetapi kalau sudah ada no_trans, berarti sudah ada di db, berarti ga bisa di input.
                    if( $notransobat[$i] == '0' )
                    {
                        $sqljunta = "INSERT INTO 
                                tbt_obat_jual (id_kel_paket, id_bhp, bhp, expired, total, total_real, bungkus_racik, id_kemasan, id_kel_racik, st_racik, r_racik, r_item, keterangan_obat, jumlah, cm, tgl, wkt, no_trans, no_trans_rwtjln, no_reg, sumber, tujuan, flag, operator, klinik, dokter, id_obat, id_harga, hrg_nett, hrg_ppn, hrg_nett_disc, hrg_ppn_disc, operator_kasir)
                                VALUES 
                                (". 0 .",". 0 .",". 0 .", '".$expire."', ".$total.", ".$total.", ".$bungkusracik.", '".$id_kemasan."', ".$idracik[$i].", '".$st_racik."', ".$rracik.", $ritem, '".$keterangan[$i]."', ".$jumlahobat[$i].", '$cm', '$dateNow', '$timeNow', '$notrans', '$notransjalan', '$notransjalan', '01', '14', '0', '$operator', '$idKlinik', '$idDokter', '$idObat', '$idHarga', ".$hrg_nett.", ".$hrg_ppn.", ".$hrg_nett_disc.", ".$hrg_ppn_disc.", NULL)";

                        $this->queryAction($sqljunta, "C");
                        $notrans++;
                    }
                }
                if($this->getViewState('nmTable'))
                {
                    $this->cetakClicked($sender,$param);
                }
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJln'));
	}
        
        public function cetakClicked($sender,$param)
        {
		$sisaByr=$this->getViewState('sisa');
		$jmlTagihan=$this->getViewState('tmpJml');
		$table=$this->getViewState('nmTable');
		$cm=$this->formatCm($this->notrans->Text);
		
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');
		$klinik=$this->getViewState('klinik');
		$id_dokter=$this->DDDokter->SelectedValue;
		$id_klinik=$this->DDKlinik->SelectedValue;
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		if($this->RBjns->SelectedValue==1) //jika RadioButton Tindakan yang dipilih
		{
			$jmlBayar=$this->bayar->Text;
			$pjPasien=$this->pjPasien->Text;
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$notrans = $this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
				
				$transRwtJln= new KasirRwtJlnRecord();
				$transRwtJln->no_trans=$notrans;
				$transRwtJln->no_trans_rwtjln=$this->getViewState('noTransJalan');
				$transRwtJln->cm=$cm;
				$transRwtJln->klinik=$id_klinik;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->id_tindakan=$row['id_tdk'];
				$transRwtJln->tgl=date('y-m-d');
				$transRwtJln->waktu=date('G:i:s');
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->bhp=$row['bhp'];
				$transRwtJln->sewa_alat=$row['alat'];
				$transRwtJln->tarif=$row['total'];
				$transRwtJln->total=$row['jml'];
				$transRwtJln->tanggungan_asuransi=$row['tanggungan_asuransi'];
				$transRwtJln->disc=$row['disc'];
				$transRwtJln->st_flag='0';
				$transRwtJln->disc='0';
				$transRwtJln->st_kredit='0';
				$transRwtJln->pengali=$row['pengali'];
				$transRwtJln->catatan=$row['catatan'];
				$transRwtJln->Save();			
			}	
			
			//update st_cetak_kartupasien di tbd_pasien
			$sql = "UPDATE tbd_pasien SET st_cetak_kartupasien = '3' WHERE cm = '$cm' AND st_cetak_kartupasien = '1' ";
			$this->queryAction($sql,'C');
			
			//Update status tbt_rawat_jalan
			/*
			$sql="UPDATE 
					tbt_rawat_jalan 
				SET 
					flag='1' 
				WHERE 
					cm='$cm' 
					AND id_klinik='$id_klinik'
					AND dokter='$id_dokter'
					AND flag='0' 
					AND st_alih='0' ";		
			$this->queryAction($sql,'C');			
			*/
			//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'table'=>$table,'pjPasien'=>$pjPasien)));
			
			//$this->batalClicked();	
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJlnSukses'));
			
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				alert("Data Rekam Billing Tindakan Rawat Jalan Telah Masuk Dalam Database.");
				window.location="index.php?page=Pendaftaran.AdminRwtJln"; 
			</script>';
		}
		else //jika RadioButton Pendaftaran yang dipilih
		{	
			$jmlBayar=$this->bayarPdftr->Text;
			$pjPasien=$this->pjPasienPdftr->Text;
			$notrans=$this->numCounter('tbt_kasir_pendaftaran',KasirPendaftaranRecord::finder(),'09');//key '09' adalah konstanta modul untuk Kasir Pendaftaran
						
			$newRecord= new KasirPendaftaranRecord();
			$newRecord->no_trans=$notrans;
			$newRecord->no_trans_pdftr=$this->getViewState('noTransJalan');			
			$newRecord->klinik=$this->getViewState('id_klinik');
			$newRecord->dokter=$this->getViewState('id_dokter');
			$newRecord->no_karcis=$this->noKarcisPdftr->Text;
			$newRecord->id_tindakan=$this->DDjnsPdftr->SelectedValue;
			$newRecord->tgl=date('y-m-d');
			$newRecord->waktu=date('G:i:s');
			$newRecord->operator=$this->User->IsUserNip;
			$newRecord->tarif=$this->getViewState('jmlBayarPdftr');
			$newRecord->st_flag='0';
			$newRecord->Save();			
						
			$rwtJlnTmp=RwtjlnRecord::finder()->find('cm = ?',$this->formatCm($this->notrans->Text));
			$rwtJlnTmp->flag='1';
			$rwtJlnTmp->Save();	
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJln',array('tipe'=>$this->RBjns->SelectedValue,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$this->getViewState('jmlBayarPdftr'),'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'jnsPdftr'=>$this->DDjnsPdftr->SelectedValue,'pjPasien'=>$pjPasien)));
		}
	}
        
	public function batalClicked()
        {		
		$this->bayarBtnPdftr->Text='Bayar';
		$this->RBjns->Enabled=true;		
		$this->DDjnsPdftr->Enabled=true;
		$this->DDjnsPdftr->SelectedIndex=-1;
		$this->bayarPdftr->Enabled=true;
		$this->jmlShow->Text='';
		$this->jmlShowPdftr->Text='';
		$this->sisaByr->Text='';
		$this->sisaByrPdftr->Text='';
		$this->nmTindakan->Text='';
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('jmlBayarPdftr');
		$this->notrans->Text ='';
					
		
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->bayarPdftr->Text='';
		
		//$this->id_tindakan->Text='';
		$this->DDTindakan->SelectedValue=NULL;
		$this->noKarcisPdftr->Text='';
		$this->pjPasien->Text='';
		$this->pjPasienPdftr->Text='';	
	
		$this->tdkPanel->Visible=false;
		$this->pdftrPanel->Visible=false;
		$this->showSecond->Visible=false;
		$this->showSecondPdftr->Visible=false;
		$this->showBayar->Visible=false;
		$this->dtGridCtrl->Display='None';
		$this->showBayarPdftr->Visible=false;
		
		$this->karcisCtrl->Visible=false;
		$this->noKarcisPdftr->Text='';
		
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDKlinik->Enabled=false;
		
		$this->DDDokter->Enabled=false;
		$this->DDDokter->SelectedIndex=-1;
		
		$this->RBjns->SelectedValue=1;
		$this->RBjnsChanged($sender,$param);
		
		$this->poliCtrl->Visible = false;
		$this->notrans->Enabled = true;	
		$this->notrans->focus();	
		
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayarPdftr');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
