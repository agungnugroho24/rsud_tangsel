<?php
class InfoLayout extends TTemplateControl
{
	public function onInit($param)
	{
		parent::onInit($param);
	}
		
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		$this->Page->Theme='info';	
	}
		
	public function onLoad($param)
	{
		parent::onLoad($param);	
	}
	
	public function queryAction($sql,$mode)
	{
		
		$conn = new TDbConnection("mysql:host=localhost;dbname=simak_tangsel","simyantu","jackass");		
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
	
}
?>
