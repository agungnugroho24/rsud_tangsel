<?php
class Simak2 extends SimakConf
{
    /**
     * Validates whether the username and password are correct.
     * This method responds to the TCustomValidator's OnServerValidate event.
     * @param mixed event sender
     * @param mixed event parameter
     */	
	public function onLoad($param)
	{
		parent::onLoad($param);		
		if(!$this->IsPostBack){	
			$this->pftrnButton->focus();			
		}				
	}
	
    public function queryAction($sql,$mode)
	{
		
		$conn = new TDbConnection("mysql:host=localhost;dbname=simak_cclk","root","");		
		$conn->Persistent=true;
		$conn->Active=true;				
		if($mode == "C")//Use this with INSERT, DELETE and EMPTY operation
		{
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();						
		}
		else if($mode == "S")//Return for select statement
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader->readAll();			
		}		
		else if($mode == "R") //Return set of rows
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader;			
		}
		else if($mode == "D") //Droped table
		{
			$que = "DROP TABLE IF EXISTS " . $sql;
			$comm=$conn->createCommand($que);		
			$dataReader = $comm->query();						
		}
		else if($mode == "E") //Hapus isi table
		{
			$que = "TRUNCATE TABLE " . $sql;
			$comm=$conn->createCommand($que);		
			$dataReader = $comm->query();						
		}
		return $rows;
		$conn->Active=false;				
	}
	
   public function logoutClicked($sender,$param)
    {
        $user=$this->User->Name;
		$sql="SELECT nm_table FROM tbx_destroy WHERE user='$user'";
		$arrData=$this->queryAction($sql,'R');		
		if($arrData != '')
		{
			foreach($arrData as $row) 
			{ 
				$sql = $row['nm_table'];				
				$this->queryAction($sql,'D');
			}
			$sql="DELETE FROM tbx_destroy WHERE user='$user'";
			$this->queryAction($sql,'C');					
		}
		$this->Application->getModule('auth')->logout();
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }  
    
    public function pdftrnClicked()
    {
        $url=$this->Service->constructUrl('Pendaftaran.DaftarBaru');
        $this->Response->redirect($url);
    }
    
    public function kasirClicked()
    {
        $url=$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount');
        $this->Response->redirect($url);
    }
    
    public function rwiClicked()
    {
        $url=$this->Service->constructUrl('Rawat.VisiteDokter');
        $this->Response->redirect($url);
    }
    
    public function apotikClicked()
    {
        $url=$this->Service->constructUrl('Apotik.penjualanObat');
        $this->Response->redirect($url);
    }
    
    public function labClicked()
    {
        $url=$this->Service->constructUrl('Lab.bayarLab');
        $this->Response->redirect($url);
    }
    
    public function radClicked()
    {
        $url=$this->Service->constructUrl('Rad.bayarRad');
        $this->Response->redirect($url);
    }
    
    public function keuClicked()
    {
        $url=$this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtInap');
        $this->Response->redirect($url);
    }
    
    public function kepegClicked()
    {
        $url=$this->Service->constructUrl('Kepegawaian.KepegawaianAdmin');
        $this->Response->redirect($url);
    }
    
    public function systemClicked()
    {
        $url=$this->Service->constructUrl('Admin.AdminFormulir');
        $this->Response->redirect($url);
    }
}
?>
