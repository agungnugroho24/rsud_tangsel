<?php
    
class PembuatanResepInap extends SimakConf
{   
    
    public function onInit($param){		
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
                }
  }
	
	public function cmCallback($sender,$param)
        {
		$this->panel->render($param->getNewWriter());
	}
	
	public function checkRegister($sender,$param)
        {
            //$ensure = "UPDATE tbm_obat set tipe=0 where tipe=2"; 
            //$this->queryAction($ensure,'C');
            $arrracikan [sizeOf($arrracikan)]= array("id" => 0, "nama_racikan" => "Buat Kelompok Baru", "jumlah_kemasan" => 0, "id_kemasan" => "0");
            $this->setViewState('arrracikan', $arrracikan);
            $this->Alat->Enabled=true;
            $this->DDRacik->Enabled = false;
            $this->Kemasan->Enabled=false;
            $this->jumlah_kemasan->Enabled=false;
            $cm=$this->formatCm($this->notrans->Text);
            $dateNow = date('Y-m-d');
            
            $sql = "SELECT *
                    FROM
                      tbt_rawat_inap
                    WHERE
                      cm = '$cm' AND 
                      st_alih = '0'
                    ORDER BY tgl_masuk DESC";
            
            // -- Kayaknya Salah
            // $sql += "AND 
            //          TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_masuk, wkt_masuk))) / 3600 <= 24 ";

            $sqlAlih = "SELECT *
                        FROM
                          tbt_rawat_inap
                        WHERE
                          cm = '$cm' AND 
                          st_alih = '1'
                        ORDER BY tgl_masuk DESC";
            
            // -- Kayaknya salah
            //$sqlAlih = "AND 
            //              TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_masuk, wkt_masuk))) / 3600 <= 24 ";

            if( RwtInapRecord::finder()->findBySql($sql) )
            {
                $this->notrans->enabled=false;
                $this->test();
            }
            
            else
            {
                $this->notrans->Enabled=true;
                $this->rspPanel->Visible = false;
                $this->infoPasien->Visible = false;
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
        }
        
    
    public function getDummyData($token){
        if($this->Alat->SelectedValue == 0 ){
            $sql = "SELECT nama FROM tbm_obat WHERE tbm_obat.nama LIKE '$token%' AND tbm_obat.kategori = '01' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
            $arr = $this->queryAction($sql,'S');
        }
        else if($this->Alat->SelectedValue == 1){
            $sql = "SELECT nama FROM tbm_obat WHERE tbm_obat.nama LIKE '$token%' AND tbm_obat.kategori = '02' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
            $arr = $this->queryAction($sql,'S');
        }
        else{
            $sql = "SELECT nama FROM tbm_obat WHERE tbm_obat.nama LIKE '$token%' AND tbm_obat.kategori = '03' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
            $arr = $this->queryAction($sql,'S');
        }
        return $arr;
    }
    
    public function AlatChanged(){
        if($this->Alat->SelectedValue==1 || $this->Alat->SelectedValue==2){
            $this->RBtipeObat->Enabled=false;
            $this->RBtipeRacik->Enabled=false;
            $this->DDRacik->Enabled=false;
            $this->Kemasan->Enabled=false;
            $this->jumlah_kemasan->Enabled=false;
                        
        }
        else{
            $this->RBtipeObat->Enabled=true;
            $this->RBtipeRacik->SelectedValue="0";
            $this->RBtipeRacik->Enabled=true;
            
        }
        //$this->Page->CallbackClient;
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
                    
                    // Menvalse kan enabled daripada kemasan
                    $this->Kemasan->Enabled = false;
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
                    //if($this->Alat->SelectedValue=="0"){
                        $sql = "SELECT tipe FROM tbm_obat where nama = '". $name ."'";
                        $what = $this->queryAction($sql,"S");
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
                            $this->RBtipeObat->SelectedValue = $tmp;
                            $this->RBtipeObat->Enabled = false;
                        }
                    /*}
                    else{
                        $tipe = "1";
                        $this->RBtipeObat->SelectedValue = $tipe;
                        $this->RBtipeObat->Enabled = false; 
                    }*/
                }
            }
        }

        public function checkTambahkan($sender, $param)
        {
            // Mengambil jumlah obat yang ada
            $jumlahObat = floatval( $this->jumlah_obat->Text );
            $namaobat = $this->cariNama->Text;
            $tipeObat = $this->RBtipeObat->SelectedValue;
            
            if($this->RBtipeRacik->SelectedValue != 0)
            {
                if($this->Kemasan->SelectedValue == 0)
                {
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
                
                else if($this->jumlah_kemasan->Text =="")
                {
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

                else if($this->DDRacik->SelectedValue == "")
                {
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
                else if( $tipeObat == '' && $this->Alat->SelectedValue=="0")
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
                else if( $jumlahObat <= 0 )
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
                        $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Masukan obat baru ini?</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                    "Ya": function() {
                                            jQuery( this ).dialog( "close" );
                                    },
                                    Tidak: function() {
                                            jQuery( this ).dialog( "close" );
                                    }
                            }
                        }});');
                        // -- Menentukan kode obat untuk obat yang baru
                        $sql = "SELECT MAX( kode ) AS k FROM tbm_obat";
                        $arr = $this->queryAction($sql,"S");

                        foreach( $arr as $row )
                            $kodeObat = floatval( $row['k'] );

                        $kodeObat++;

                        $prefix = "000000";


                        $kodeObat = substr($prefix.$kodeObat,-5);

                        if($this->Alat->SelectedValue == 0)
                        {
                            $sql = "INSERT INTO
                            tbm_obat ( kode, nama, flag_input_dr, tipe, kategori )
                            VALUES( '".$kodeObat."', '".$namaobat."', '". $flag."', '".$tipeObat."', '01' )";
                            $this->queryAction($sql,"C");
                        }
                        
                        elseif($this->Alat->SelectedValue == 1)
                        {
                            $sql = "INSERT INTO
                            tbm_obat ( kode, nama, flag_input_dr, tipe, kategori )
                            VALUES( '".$kodeObat."', '".$namaobat."', '". $flag."', '".$tipeObat."', '02' )";
                            $this->queryAction($sql,"C");
                        }

                        else
                        {
                            $sql = "INSERT INTO
                            tbm_obat ( kode, nama, flag_input_dr, tipe, kategori )
                            VALUES( '".$kodeObat."', '".$namaobat."', '". $flag."', '".$tipeObat."', '03' )";
                            $this->queryAction($sql,"C");
                        }

                        // -- Insert ke dalam tbt_obat_harga
                        $sql = "INSERT INTO
                           tbt_obat_harga ( kode, sumber, tgl )
                           VALUES( '".$kodeObat."', '01', NOW() )
                        ";
                        $this->queryAction($sql,"C");

                        // -- Mengambil id dari tbt_obat_harga
                        $sql = "SELECT id FROM tbt_obat_harga WHERE kode=" . $kodeObat;
                        $what = $this->queryAction($sql,"S");
                        foreach($what as $row){
                            $idharga = $row['id'];
                        }

                        // -- 
                        $sql = "INSERT INTO
                           tbt_stok_lain ( id_obat, id_harga, sumber, tujuan, expired )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', '2', '0000-00-00' )
                        ";
                        $this->queryAction($sql,'C');

                        // -- 
                        $sql = "INSERT INTO
                           tbt_stok_lain ( id_obat, id_harga, sumber, tujuan, expired )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', '14', '0000-00-00' )
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
                else if( $tipeObat == ''&& $this->Alat->SelectedValue=="0")
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
                else if( $jumlahObat <= 0 )
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
                           tbt_stok_lain ( id_obat, id_harga, sumber, tujuan, expired )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', '2', '0000-00-00' )
                        ";
                        $this->queryAction($sql,'C');

                        // -- 
                        $sql = "INSERT INTO
                           tbt_stok_lain ( id_obat, id_harga, sumber, tujuan, expired )
                           VALUES( '".$kodeObat."', '". $idharga ."', '01', '14', '0000-00-00' )
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
            $namaobat = $this->cariNama->Text;
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
            
            if( $tipeobat == 0 )
            {
                $tipe_obat_nama = "Generik";					
            }
            
            elseif($tipeobat == 1)
            {
                $tipe_obat_nama = "Non-generik";					
            }
            else{
                $tipe_obat_nama = "";
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
                    // Seiring berjalannya loop, maka mengetest apakah index racikan ini ada?
                    foreach( $arrracikan as $row )
                    {
                        // Kalau memang tidak sama dengan $i, maka
                        if( $row['id'] != $i )
                        {
                            // Langsung ke luar, untuk di tambahkan ke dalam arrracikan
                            break;
                        }
                        
                        $i++;
                    }
                    
                    if( $i == sizeOf($arrracikan) )
                    {
                        // Masukan id_kel_racik yang baru berdasarkan sizenya si arrracikan
                        $id_kel_racik = sizeOf($arrracikan);
                    }
                    
                    else
                    {
                        // Memasukan id kel racikan = $i
                        $id_kel_racik = $i;
                    }
                    
                    // Masukan lagi ke dalam array yang ada
                   $arrracikan[$id_kel_racik] = array( 'id' => $id_kel_racik, 
                                                            'nama_racikan' => 'Racikan '.$id_kel_racik, 
                                                            'jumlah_kemasan' => $jumlahkemasan, 
                                                            'id_kemasan' => $id_kemasan );
                   
                   // di sort berdasarkan idnya
                   asort($arrracikan);
                   
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
            
            $sql = "SELECT nama, kode from tbm_obat WHERE nama='" . $namaobat . "' LIMIT 1";
            $sqlarr = $this->queryAction($sql,"S");
            
            if(sizeof($sqlarr)>0){
                foreach($sqlarr as $row){
                    $abcde = $row['nama'];
                    $kode = $row['kode'];
                }
            }
            
            else{
                $sql = "SELECT nama from tbm_obat_bhp WHERE nama='" . $namaobat . "' LIMIT 1";
                $sqlarr = $this->queryAction($sql,"S");
                foreach($sqlarr as $row){
                    $abcde = $row['nama'];
                }
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
            
            // Kalau uda ada viewstatenya, maka dimasukan ke dalam variable $arrResep
            if( $this->getViewState("arrResep") )
                $arrResep = $this->getViewState("arrResep");
            
            if( $this->getViewState("arrResepAlkes") )
                $arrResep = $this->getViewState("arrResepAlkes");

            if($this->getViewState("arrResepBhp"))
                $arrResepBhp = $this->getViewState("arrResepBhp");

            if($id_kel_racik ==0)
            {
                $tulisan = "Non-racikan";
            }
            else{
                $tulisan = "Racikan ". $id_kel_racik;
            }
            
            // -- Menentukan id temporary di dalam view resep --
            $id_temp = 0;       // untuk menyimpan id_arrresep yang digunakan untuk ke dalam viewnya
            if( $this->Alat->SelectedValue=="0" && sizeOf($arrResep) > 0 )
            {
                // Mengambil ID daripada obat yang terakhir
                foreach( $arrResep as $row )
                {
                    if( $row['id'] > $id_temp )
                        $id_temp = $row['id'];
                }
                $id_temp++;     // ditambah 1 karena id obat yang baru kan +1
            }
            
            else if( $this->Alat->SelectedValue=="1" && sizeOf($arrResepAlkes) > 0 )
            {
                foreach( $arrResepAlkes as $row )
                {
                    if( $row['id_alkes'] > $id_temp )
                        $id_temp = $row['id_alkes'];
                }
                $id_temp++;
            }

            else if( $this->Alat->SelectedValue=="2" && sizeOf($arrResepBhp) > 0 )
            {
                foreach( $arrResepBhp as $row )
                {
                    if( $row['id_bhp'] > $id_temp )
                        $id_temp = $row['id_bhp'];
                }
                $id_temp++;
            }
            
            // -- Penambahan Obat ke Dalam Session --
            if($this->Alat->SelectedValue=="0")
            {
                // Ditambahkan value daripada si variable
                $arrResep[ sizeOf($arrResep) ] = array(
                    'id' => $id_temp,
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
            
            elseif($this->Alat->SelectedValue=="1")
            {
                // Ditambahkan value daripada si variable
                $arrResepAlkes[ sizeOf($arrResepAlkes) ] = array(
                    'id_alkes' => $id_temp,
                    'nama_alkes' => $namaobat,
                    'jumlah_alkes' => $jumlahobat, 
                    'tipe_alkes' => $tipe_obat_nama, 
                    'keterangan'=> $keterangan,
                    'harga' => $hrg
                );
            }

            else
            {
                // Ditambahkan value daripada si variable
                $arrResepBhp[ sizeOf($arrResepBhp) ] = array(
                    'id_bhp' => $id_temp,
                    'nama_bhp' => $namaobat, 
                    'jumlah_bhp' => $jumlahobat, 
                    'tipe_bhp' => $tipe_obat_nama, 
                    'keterangan'=> $keterangan,
                    'harga' => $hrg
                );
            }
            
            /*$price = array();
            foreach ($arrResep as $key => $row)
            {
                $price[$key] = $row['id_kel_racik'];
            }
            array_multisort($price, $arrResep);*/
            $this->setViewState("arrResep", $arrResep );
            $this->setViewState("arrResepAlkes", $arrResepAlkes );
            $this->setViewState("arrResepBhp", $arrResepBhp);
            
            if($this->getViewState("arrResep")!=NULL || $this->getViewState("arrResepAlkes") != NULL || $this->getViewState("arrResepBhp") != NULL)
            {
                $this->Temporary_resep->Visible = true;
                if($this->getViewState("arrResep") != NULL)
                {
                    $this->ResepObat->Visible = true;
                    $this->ObatGrid->DataSource = $this->getViewState("arrResep");
                    $this->ObatGrid->dataBind();
                }

                if($this->getViewState("arrResepAlkes") != NULL)
                {
                    $this->ResepAlkes->Visible = true;
                    $this->alkesGrid->DataSource = $this->getViewState("arrResepAlkes");
                    $this->alkesGrid->dataBind();
                }

                if($this->getViewState("arrResepBhp") != NULL)
                {
                    $this->ResepBHP->Visible = true;
                    $this->bhpGrid->DataSource = $this->getViewState("arrResepBhp");
                    $this->bhpGrid->dataBind();
                }
            }
            
            $this->DDRacik->DataSource = $this->getViewState("arrracikan");
            $this->DDRacik->dataBind();
        }

        public function deleteItem($sender,$param)
        {
            // Ambil ID yang mau di delete
            //if($this->Alat->SelectedValue == 0)
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
        
        /*
         * @function fungsi untuk delete Alat Kesehatan
         * @param $sender, $param
         * @PT. Garuda Solusi Kreatif @Desember 2013
         */
        public function deleteItemAlkes($sender,$param)
        {
            // Ambil ID yang mau di delete
            $id_alkes = $this->alkesGrid->DataKeys[$param->Item->ItemIndex];
        
            $this->getPage()->getClientScript()->registerEndScript
            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Apakah Yakin akan menghapus obat ini?</p>\',timeout: 4000,dialog:{modal: true,
            buttons: {
                        "Ya": function() {
                                jQuery( this ).dialog( "close" );
                                deleteItemAlkes("'.$id_alkes.'");
                        },
                        Tidak: function() {
                                jQuery( this ).dialog( "close" );
                        }
                }
            }});');
        
        }
        
        /*
         * @function fungsi untuk delete obat BHP
         * @param $sender, $param
         * @PT. Garuda Solusi Kreatif @Desember 2013
         */
        public function deleteItemBHP($sender,$param)
        {
            // Ambil ID yang mau di delete
            $id_bhp = $this->bhpGrid->DataKeys[$param->Item->ItemIndex];
        
            $this->getPage()->getClientScript()->registerEndScript
            ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Apakah Yakin akan menghapus obat ini?</p>\',timeout: 4000,dialog:{modal: true,
            buttons: {
                        "Ya": function() {
                                jQuery( this ).dialog( "close" );
                                deleteItemBHP("'.$id_bhp.'");
                        },
                        Tidak: function() {
                                jQuery( this ).dialog( "close" );
                        }
                }
            }});');
        
        }
        
        /*
         *  @function untuk delete obat, lanjutan dari function deleteItem()
         *  @param $sender -> , $param -> 
         *  made by PT. GSK @November 2013
         */
        public function deleteObat($sender,$param)
        {
            $id = $param->CallbackParameter->Id;
           
            // -- Retrieve array
            $arrResep = $this->getViewState("arrResep");
            $arrResepAlkes = $this->getViewState("arrResepAlkes");
            $arrResepBhp = $this->getViewState("arrResepBhp");
            $arrracikan = $this->getViewState("arrracikan");
            
            $obatAkhir = $arrResep[sizeOf($arrResep)-1];    // Masukan nilai array yang paling besar ke dalam suatu variable
            unset( $arrResep[sizeOf($arrResep)-1] );        //Kemudian hilangkam bagian terakhir daripada array
            
            //$validasi = 0;
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
                   }
                   $i++;
                }
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
            if( sizeOf($arrResep) == 0 && sizeOf($arrResepAlkes) == 0 && sizeOf($arrResepBhp) == 0 )
                $this->Temporary_resep->Visible = false;
            
            // Kalau array ada isi, maka tombol simpan jadi true
            else
                $this->Temporary_resep->Visible = true;
            
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
    
    /*
    *  @function untuk delete bhp, lanjutan dari function deleteItemAlkes()
    *  @param $sender, $param
    *  made by PT. GSK @Desember 2013
    */
    public function deleteItemAlkes2($sender,$param)
    {
        $id = $param->CallbackParameter->Id;
        
        $arrResep = $this->getViewState("arrResep");
        $arrResepAlkes = $this->getViewState("arrResepAlkes");
        $arrResepBhp = $this->getViewState("arrResepBhp");
        
        $obatAkhir = $arrResepAlkes[sizeOf($arrResepAlkes)-1];    // Masukan nilai array yang paling besar ke dalam suatu variable
        unset( $arrResepAlkes[sizeOf($arrResepAlkes)-1] );        //Kemudian hilangkam bagian terakhir daripada array

        //$validasi = 0;
        $i = 0;
        if( sizeOf($arrResepAlkes) != 0)
        {
            foreach( $arrResepAlkes as $row )
            {
               // Kalau id obat = id si obat yang mau di delete
               if( $row['id_alkes'] == $id )
               {
                   // Bagian obat yang akhir dimasukan ke dalam posisi obat yang mau di delete
                   $arrResepAlkes[$i] = $obatAkhir;
               }
               $i++;
            }
        }

        // -- Sorting di dalam array
        // Kalau memang masih ada di dalam array resep
        if( sizeOf($arrResepAlkes) > 0 )
        {
            $id_alkes = array();
            foreach ($arrResepAlkes as $key => $row)
            {
                $id_alkes[$key] = $row['id_alkes'];
            }
            // Kemudian di sort berdasarkan id_kel_racik
            array_multisort($id_alkes, $arrResepAlkes);
        }

        // Masukan lagi ke dalam viewstate
        $this->setViewState("arrResepAlkes", $arrResepAlkes);

        // -- Set lagi apa yang harus di set oleh ObatGrid
        // Kalau array kosong, maka tombol simpan jadi false
        if( sizeOf($arrResep) == 0 && sizeOf($arrResepAlkes) == 0 && sizeOf($arrResepBhp) == 0 )
            $this->Temporary_resep->Visible = false;

        // Kalau array ada isi, maka tombol simpan jadi true
        else
            $this->Temporary_resep->Visible = true;
    }
    
    /*
    *  @function untuk delete bhp, lanjutan dari function deleteItemBHP()
    *  @param $sender, $param
    *  made by PT. GSK @November 2013
    */
    public function deleteItemBHP2($sender,$param)
    {
        $id = $param->CallbackParameter->Id;
        
        $arrResep = $this->getViewState("arrResep");
        $arrResepAlkes = $this->getViewState("arrResepAlkes");
        $arrResepBhp = $this->getViewState("arrResepBhp");
        
        $obatAkhir = $arrResepBhp[sizeOf($arrResepBhp)-1];    // Masukan nilai array yang paling besar ke dalam suatu variable
        unset( $arrResepBhp[sizeOf($arrResepBhp)-1] );        //Kemudian hilangkam bagian terakhir daripada array

        //$validasi = 0;
        $i = 0;
        if( sizeOf($arrResepBhp) != 0)
        {
            foreach( $arrResepBhp as $row )
            {
               // Kalau id obat = id si obat yang mau di delete
               if( $row['id_bhp'] == $id )
               {
                   // Bagian obat yang akhir dimasukan ke dalam posisi obat yang mau di delete
                   $arrResepBhp[$i] = $obatAkhir;
               }
               $i++;
            }
        }

        // -- Sorting di dalam array
        // Kalau memang masih ada di dalam array resep
        if( sizeOf($arrResepBhp) > 0 )
        {
            $id_bhp = array();
            foreach ($arrResepBhp as $key => $row)
            {
                $id_bhp[$key] = $row['id_bhp'];
            }
            // Kemudian di sort berdasarkan id_kel_racik
            array_multisort($id_bhp, $arrResepBhp);
        }

        // Masukan lagi ke dalam viewstate
        $this->setViewState("arrResepBhp", $arrResepBhp);

        // -- Set lagi apa yang harus di set oleh ObatGrid
        // Kalau array kosong, maka tombol simpan jadi false
        if( sizeOf($arrResep) == 0 && sizeOf($arrResepAlkes) == 0 && sizeOf($arrResepBhp) == 0 )
            $this->Temporary_resep->Visible = false;

        // Kalau array ada isi, maka tombol simpan jadi true
        else
            $this->Temporary_resep->Visible = true;
        
        // View lagi ke dalam bagian obatnya
        $this->bhpGrid->EditItemIndex=-1;
        $this->bhpGrid->DataSource=$this->getViewState("arrResepBhp");
        $this->bhpGrid->dataBind();
    }
    
    /*
     * @function Fungsi untuk
     * @param NULL
     * Made by PT. GSK @November 2013 
     */
    public function test()
    {
            $cm=$this->formatCm($this->notrans->Text);
            $this->rspPanel->Visible = true;
            $this->infoPasien->Visible = true;
            $arrracikan = $this->getViewState('arrracikan');

            // Raymond : Mengambil record nama dokter untuk dokter yang idnya di dalam record si  pasien
            
            
            // -- BAGIAN PEMBUATAN RESEP
            
            // - Mengambil data tbt_rawat_inap dari cm yang ada paling terakhir
            $sql = "SELECT * FROM tbt_rawat_inap WHERE cm='".$cm."' ORDER BY no_trans DESC LIMIT 0,1" ;
            $result = $this->queryAction($sql, "S");
            
            foreach($result as $row)
            {
                $trns = $row['no_trans'];
                $this->nmDokter->Text= PegawaiRecord::finder()->findByPk($row['dokter'])->nama; 
                $this->noCmTxt2->Text= $row['cm'];
                $this->nmPasien2->Text= PasienRecord::finder()->findByPk($row['cm'])->nama;
                $this->tgl->Text= $this->convertDate($row['tgl_masuk'],'3');

            }
            
            // Variable yang menyimpan list racikan berdasarkan di dalam resep sudha ada racikan apa
            $this->DDRacik->DataSource =  $this->getViewState("arrracikan");
            $this->DDRacik->dataBind();
            
            $this->DDRacik->SelectedValue = '0';
	}	
	
    /*
     * @function memasukan list obat, alkes dan bhp ke dalam database
     * @param $sender, $param
     * Made by PT. Garuda Solusi Kreatif, Desember 2013
     */
    public function simpanClicked($sender,$param)
    {
        $operator=$this->User->IsUserName;
        $cm = $this->formatCm($this->notrans->Text);
        $noreg = $this->numRegister('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
        
        $array = $this->getViewState('arrResep');
        $arrracikan = $this->getViewState('arrracikan');
        $arrResepAlkes = $this->getViewState('arrResepAlkes');
        $arrResepBhp = $this->getViewState('arrResepBhp');
        
        $timezone = date_default_timezone_get();
        date_default_timezone_set($timezone);
        $dateNow = date('Y/m/d H:i:s');
        $timeNow = substr($dateNow,-8);
        
        /*
        if($this->Alat->SelectedValue=="0")
        {
            // Ditambahkan value daripada si variable
            $arrResep[ sizeOf($arrResep) ] = array(
                'id' => $id_temp,
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

        elseif($this->Alat->SelectedValue=="1")
        {
            // Ditambahkan value daripada si variable
            $arrResepAlkes[ sizeOf($arrResepAlkes) ] = array(
                'id_alkes' => $id_temp,
                'nama_alkes' => $namaobat,
                'jumlah_alkes' => $jumlahobat, 
                'tipe_alkes' => $tipe_obat_nama, 
                'keterangan'=> $keterangan,
                'harga' => $hrg
            );
        }

        else
        {
            // Ditambahkan value daripada si variable
            $arrResepBhp[ sizeOf($arrResepBhp) ] = array(
                'id_bhp' => $id_temp,
                'nama_bhp' => $namaobat, 
                'jumlah_bhp' => $jumlahobat, 
                'tipe_bhp' => $tipe_obat_nama, 
                'keterangan'=> $keterangan,
                'harga' => $hrg
            );
        }
        */
            
        $aql = "SELECT * FROM
                tbt_rawat_inap
              WHERE
                cm = '".$cm."'
              AND
                st_alih = 0
              ORDER BY
                tgl_masuk DESC limit 1";
        $buset = $this->queryAction($aql,"S");	

        foreach($buset as $row)
        {
            $idDokter = $row['dokter'];
            $notransinap = $row['no_trans'];
        }

        // Mengambil nama operator
        $arr1 = $this->queryAction("Select nip from tbd_user where real_name = '" . $operator ."'","S");
        foreach($arr1 as $row){
            $operator = $row['nip'];
        }
        
        // -- Mengetahui apakah sudah ada harga resep di dalam database atua belum
        $sql1 = "SELECT * FROM tbt_obat_jual_inap WHERE no_trans_inap = '".$notransinap."'";
        $arr1 = $this->queryAction($sql1,'S');
        
        $i = 0;
        
        if( $array )
        {
        foreach( $array as $row )
        {
            $notrans = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
            
            // -- Mendapatkan id obat berdasarkan nama yang ada --
            //$wql = "SELECT kode FROM tbm_obat where nama = '".$cariNama[$i]."'";
            $wql = "SELECT kode FROM tbm_obat where nama = '".$row['nama_obat']."'";
            $what = $this->queryAction($wql,"S");

            foreach( $what as $row1 )
            {
               $idObat = $row1['kode'];
            }
            
            // -- Mendapatkan Harga dari Obat --
            
            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }
                    
            $ritem;
            
            // -- Kalau dia obat yang pertama && belom ada harga resep di dalam resep
            if($i == 0)
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
            $eql = "SELECT expired FROM tbt_stok_lain where id_obat ='".$idObat."'";
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }
            
            // -- Mengambil harga kemasan
            $eql = "SELECT id, hrg FROM tbm_kemasan where nama ='". $row['kemasan'] ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $harga_kemasan = $row1['hrg'];
               $id_kemasan = $row1['id'];
            }

            if ($id_kemasan == "")
            {
                $harga_kemasan = 0;
                $id_kemasan = "";
            }

            $idracik_float = 0;
            $rracik = 0;
            $bungkusracik = 0;
           
            $idracik_float = floatval($row['id_kel_racik']);

            // Cek apakah sudah ada racikan dengan id ini dengan harga racikannya?
            $sql3 = "SELECT * FROM tbt_obat_jual_inap WHERE no_reg = '".$noreg."' AND id_kel_racik = ".$idracik_float." AND r_racik <> 0";
            $arr3 = $this->queryAction($sql3,'S');

            if( !$arr3 ) // Kalau query berhasil return sesuatu
            {
                $eql = "SELECT jasa_racikan FROM tbm_jasa_resep_racikan";
                $asdf = $this->queryAction($eql,"S");

                foreach($asdf as $roweql)
                {
                    $rracik = $roweql['jasa_racikan'];
                }

                // -- Harga bungkus racikan
                $bungkusracik = $harga_kemasan * $row['jumlah_kemasan'];
            }
                    
            // Mengambil total harga
            $total = $hrg_nett + $hrg_ppn + $hrg_nett_disc + $hrg_ppn_disc + $ritem + $rracik + $bungkusracik;

            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }

            $eql = "SELECT expired FROM tbt_stok_lain where id_obat =".$idObat;
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }

            $sqljunta = "INSERT INTO 
                    tbt_obat_jual_inap (id_kel_paket, id_bhp, bhp, expired, total, total_real, bungkus_racik, id_kemasan, id_kel_racik, r_racik, r_item, keterangan_obat, jumlah, cm, tgl, wkt, no_trans, no_trans_inap, no_reg, sumber, tujuan, flag, st_bayar, operator, dokter, id_obat, id_harga, hrg_nett, hrg_ppn, hrg_nett_disc, hrg_ppn_disc, operator_kasir)
                    VALUES 
                    (". 0 .",". 0 .",". 0 .", '".$expire."', ".$total.", ".$total.", ".$bungkusracik.", '".$id_kemasan."', ".$idracik_float.", ".$rracik.", $ritem, '".$row['keterangan']."', ".$row['jumlah'].", '$cm', '$dateNow', '$timeNow', '$notrans', '$notransinap', '$noreg', '01', '14', '0', '0', '$operator', '$idDokter', '$idObat', '$idHarga', ".$hrg_nett.", ".$hrg_ppn.", ".$hrg_nett_disc.", ".$hrg_ppn_disc.", NULL)";

            $this->queryAction($sqljunta, "C");
            $i++;
        }
        }
        
        if( $arrResepAlkes )
        {
        // Insert ALKES
        foreach( $arrResepAlkes as $row )
        {
            $notrans = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
            
            // -- Mendapatkan id obat berdasarkan nama yang ada --
            //$wql = "SELECT kode FROM tbm_obat where nama = '".$cariNama[$i]."'";
            $wql = "SELECT kode FROM tbm_obat where nama = '".$row['nama_alkes']."'";
            $what = $this->queryAction($wql,"S");

            foreach($what as $row1)
            {
               $idObat = $row1['kode'];
            }
            
            // -- Mendapatkan Harga dari Obat --
            
            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }
                    
            $ritem;
            
            // -- Kalau dia obat yang pertama && belom ada harga resep di dalam resep
            if($i == 0)
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
            $eql = "SELECT expired FROM tbt_stok_lain where id_obat ='".$idObat."'";
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }
            
            $harga_kemasan = 0;
            $id_kemasan = "";
            $rracik = 0;
            $bungkusracik = 0;
                    
            // Mengambil total harga
            $total = $hrg_nett + $hrg_ppn + $hrg_nett_disc + $hrg_ppn_disc + $ritem + $rracik + $bungkusracik;

            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }

            $eql = "SELECT expired FROM tbt_stok_lain where id_obat =".$idObat;
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }

            $sqljunta = "INSERT INTO 
                    tbt_obat_jual_inap (id_kel_paket, id_bhp, bhp, expired, total, total_real, bungkus_racik, id_kemasan, id_kel_racik, r_racik, r_item, keterangan_obat, jumlah, cm, tgl, wkt, no_trans, no_trans_inap, no_reg, sumber, tujuan, flag, st_bayar, operator, dokter, id_obat, id_harga, hrg_nett, hrg_ppn, hrg_nett_disc, hrg_ppn_disc, operator_kasir)
                    VALUES 
                    (". 0 .",". 0 .",". 0 .", '".$expire."', ".$total.", ".$total.", ".$bungkusracik.", '".$id_kemasan."', 0, ".$rracik.", $ritem, '".$row['keterangan']."', ".$row['jumlah_alkes'].", '$cm', '$dateNow', '$timeNow', '$notrans', '$notransinap', '$noreg', '01', '14', '0', '0', '$operator', '$idDokter', '$idObat', '$idHarga', ".$hrg_nett.", ".$hrg_ppn.", ".$hrg_nett_disc.", ".$hrg_ppn_disc.", NULL)";

            $this->queryAction($sqljunta, "C");
            $i++;
        }
        }
        
        if( $arrResepBhp )
        {
        // Insert BHP
        foreach( $arrResepBhp as $row )
        {
            $notrans = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
            
            // -- Mendapatkan id obat berdasarkan nama yang ada --
            //$wql = "SELECT kode FROM tbm_obat where nama = '".$cariNama[$i]."'";
            $wql = "SELECT kode FROM tbm_obat where nama = '".$row['nama_bhp']."'";
            $what = $this->queryAction($wql,"S");

            foreach($what as $row1)
            {
               $idObat = $row1['kode'];
            }
            
            // -- Mendapatkan Harga dari Obat --
            
            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }
                    
            $ritem;
            
            // -- Kalau dia obat yang pertama && belom ada harga resep di dalam resep
            if($i == 0)
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
            $eql = "SELECT expired FROM tbt_stok_lain where id_obat ='".$idObat."'";
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }
            
            $harga_kemasan = 0;
            $id_kemasan = "";
            $rracik = 0;
            $bungkusracik = 0;
                    
            // Mengambil total harga
            $total = $hrg_nett + $hrg_ppn + $hrg_nett_disc + $hrg_ppn_disc + $ritem + $rracik + $bungkusracik;

            $eql = "SELECT id, hrg_netto, hrg_ppn, hrg_netto_disc, hrg_ppn_disc FROM tbt_obat_harga where kode ='". $idObat ."'";
            $asdf = $this->queryAction($eql,"S");

            foreach($asdf as $row1)
            {
               $idHarga = $row1['id'];
               $hrg_nett = $row1['hrg_netto'];
               $hrg_ppn = $row1['hrg_ppn'];
               $hrg_nett_disc = $row1['hrg_netto_disc'];
               $hrg_ppn_disc = $row1['hrg_ppn_disc'];
            }

            $eql = "SELECT expired FROM tbt_stok_lain where id_obat =".$idObat;
            $qop = $this->queryAction($eql,"S");
            foreach($qop as $row1)
            {
               $expire = $row1['expired'];
            }

            $sqljunta = "INSERT INTO 
                    tbt_obat_jual_inap (id_kel_paket, id_bhp, bhp, expired, total, total_real, bungkus_racik, id_kemasan, id_kel_racik, r_racik, r_item, keterangan_obat, jumlah, cm, tgl, wkt, no_trans, no_trans_inap, no_reg, sumber, tujuan, flag, st_bayar, operator, dokter, id_obat, id_harga, hrg_nett, hrg_ppn, hrg_nett_disc, hrg_ppn_disc, operator_kasir)
                    VALUES 
                    (". 0 .",". 0 .",". 0 .", '".$expire."', ".$total.", ".$total.", ".$bungkusracik.", '".$id_kemasan."', 0, ".$rracik.", $ritem, '".$row['keterangan']."', ".$row['jumlah_bhp'].", '$cm', '$dateNow', '$timeNow', '$notrans', '$notransinap', '$noreg', '01', '14', '0', '0', '$operator', '$idDokter', '$idObat', '$idHarga', ".$hrg_nett.", ".$hrg_ppn.", ".$hrg_nett_disc.", ".$hrg_ppn_disc.", NULL)";

            $this->queryAction($sqljunta, "C");
            $i++;
        }
        }
        
        $this->Response->redirect($this->Service->constructUrl('Rawat.PembuatanResepInap'));
    }
}
?>