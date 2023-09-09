<?php
class DaftarCariDataSearch2 extends SimakConf
{
	public function onInit($param)
	{	
		/*			
		if ($this->Request['action'] == "chatheartbeat") { $this->chatHeartbeat(); } 
		if ($this->Request['action'] == "sendchat") { $this->sendChat(); } 
		if ($this->Request['action'] == "closechat") { $this->closeChat(); } 
		if ($this->Request['action'] == "startchatsession") { $this->startChatSession(); } 
		
		$session=new THttpSession;
		$session->open();
		
		if (!isset($session['chatHistory'])) {
			$session['chatHistory'] = array();	
		}
		
		if (!isset($session['openChatBoxes'])) {
			$session['openChatBoxes'] = array();	
		}
		*/
		
		$halaman = $_POST['halaman'];
		$rp = $_POST['rp'];
		$sortname = $_POST['sortname'];
		$sortorder = $_POST['sortorder'];
		
		if (!$sortname) $sortname = 'cm';
		if (!$sortorder) $sortorder = 'desc';
				if($_POST['query']!=''){
					$where = "WHERE `".$_POST['qtype']."` LIKE '%".$_POST['query']."%' ";
				} else {
					$where ='';
				}
				if($_POST['letter_pressed']!=''){
					$where = "WHERE `".$_POST['qtype']."` LIKE '".$_POST['letter_pressed']."%' ";	
				}
				if($_POST['letter_pressed']=='#'){
					$where = "WHERE `".$_POST['qtype']."` REGEXP '[[:digit:]]' ";
				}
		$sort = "ORDER BY $sortname $sortorder";
		
		if (!$halaman) $halaman = 1;
		if (!$rp) $rp = 10;
		
		$start = (($halaman-1) * $rp);
		
		$limit = "LIMIT $start, $rp";
		
		$sql = "SELECT * FROM tbd_pasien $where GROUP BY cm $sort $limit ";
				
		$result = $this->queryAction($sql,'S');
		//$result = runSQL($sql);
		
		
		$sql2 = "SELECT * FROM tbd_pasien $where GROUP BY cm $sort ";
				
		$total = $this->countRec('cm',$sql2);
		
		/*
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-type: text/x-json");
		$json = "";
		$json .= "{\n";
		$json .= "halaman: $halaman,\n";
		$json .= "total: $total,\n";
		$json .= "rows: [";
		$rc = false;
		
		//while ($row = mysql_fetch_array($result)) 
		foreach($result as $row)
		{
			if ($rc) $json .= ",";
			$json .= "\n{";
			$json .= "id:'".$row['cm']."',";
			$json .= "cell:['".$row['cm']."','".$row['nama']."'";
			$json .= ",'".addslashes($row['nm_penjamin'])."'";
			$json .= ",'".addslashes($row['jkel'])."'";
			$json .= ",'".addslashes($row['tgl_lahir'])."'";
			$json .= ",'".addslashes($row['alamat'])."']";
			$json .= "}";
			$rc = true;
		}
		$json .= "]\n";
		$json .= "}";
		echo $json;
		*/
		
		/*
		$rc = false;
		
		foreach($result as $row)
		{
			if ($rc) $items .= ",";
			$items .= "\n{";
			$items .= "id:'".$row['cm']."',";
			$items .= "cell:['".$row['cm']."','".$row['nama']."'";
			$items .= ",'".addslashes($row['nm_penjamin'])."'";
			$items .= ",'".addslashes($row['jkel'])."'";
			$items .= ",'".addslashes($row['tgl_lahir'])."'";
			$items .= ",'".addslashes($row['alamat'])."']";
			$items .= "}";
			$rc = true;
		}
		$items .= "]\n";
		$items .= "}";
		
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
		header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-type: text/x-json");
		
		?>
		{
        	"halaman": "<?php echo $halaman;?>",
            "total": "<?php echo $total;?>",
            "rows": [
					<?php echo $items;?>
				]
		}
		
		<?php
	
		exit(0);
		*/
		
		$rc = false;
		$items = '';
		
		$i = 1;
		foreach($result as $row)
		{
			//$items .= "\n{";
			
			$items .= <<<EOD
			{
				"id" : '{$row['cm']}',"cell" : ['{$row['cm']}','{$row['nama']}','{$row['tmp_lahir']}','{$row['tgl_lahir']}','{$row['jkel']}','{$row['alamat']}']
			}
EOD;
			//$items .= "}";
			if ($i < $total )
			{
				$items .= ",";
			}
			
			$i++;
		}
		
		
		header('Content-type: application/json');
		?>
		{
				"halaman" : <?php echo $halaman;?>,
                "total" : <?php echo $total;?>,
				"rows" : [ <?php echo $items;?> ]
		}
		
		<?php
	
		exit(0);

		/*
		$json=new JSON();
		$json->add("halaman", $halaman);
		$json->add("total", $total);
		$json->add("rows",$items);
		echo $json->encode();
		 */
		
		
		
    }
	
	public function countRec($fname,$tname) 
	{
		//$sql = "SELECT count($fname) FROM $tname $where";
		$sql = $tname;
		
		$result = $this->queryAction($sql,'S');
		
		$total = count($result); 
		return $total;
		
	}

	public function onLoad($param)
	{
		
		if(!$this->IsPostBack)  // if the halaman is initially requested
		{						
			//$this->frame->FrameUrl = $this->getRequest()->constructUrl('halaman','latihOverlay');		
		}
	} 	 
	
	public function chatHeartbeat()
	{
		$session=new THttpSession;
		$session->open();
		
		$sql = "select * from chat where (chat.to = '".TPropertyValue::ensureString($session['username'])."' AND recd = 0) order by id ASC";
		$query = $this->queryAction($sql,'S');
		$items = '';
	
		$chatbox = array();
		
		//while ($chat = $this->queryAction($sql,'S'))
		foreach($query as $chat)
		{	
			if (!isset($session['openChatBoxes'][$chat['from']]) && isset($session['chatHistory'][$chat['from']]))
			{
				$items = $session['chatHistory'][$chat['from']];
			}
	
			$chat['message'] = $this->sanitize($chat['message']);
	
			$items .= <<<EOD
			{
				"s": "0",
				"f": "{$chat['from']}",
				"m": "{$chat['message']}"
		   	},
EOD;
		
		
			if (!isset($session['chatHistory'][$chat['from']])) 
			{
				$session['chatHistory'][$chat['from']] = '';
			}
		
			$session['chatHistory'][$chat['from']] .= <<<EOD
			{
					"s": "0",
					"f": "{$chat['from']}",
					"m": "{$chat['message']}"
			 },
EOD;
			
			unset($session['tsChatBoxes'][$chat['from']]);
			$session['openChatBoxes'][$chat['from']] = $chat['sent'];
		}
	
		if (!empty($session['openChatBoxes']))
		{
			foreach ($session['openChatBoxes'] as $chatbox => $time) 
			{
				if (!isset($session['tsChatBoxes'][$chatbox]))
				{
					$now = time()-strtotime($time);
					$time = date('g:iA M dS', strtotime($time));
		
					$message = "Sent at $time";
					if ($now > 180) 
					{
						$items .= <<<EOD
						{
							"s": "2",
							"f": "$chatbox",
							"m": "{$message}"
						},
EOD;
	
						if (!isset($session['chatHistory'][$chatbox]))
						{
							$session['chatHistory'][$chatbox] = '';
						}
	
						$session['chatHistory'][$chatbox] .= <<<EOD
						{
							"s": "2",
							"f": "$chatbox",
							"m": "{$message}"
						},
EOD;
		
						$session['tsChatBoxes'][$chatbox] = 1;
					}
				}
			}
		}
		
		
		$sql = "update chat set recd = 1 where chat.to = '".TPropertyValue::ensureString($session['username'])."' and recd = 0";
		$query = $this->queryAction($sql,'C');
	
		if ($items != '')
		{
			$items = substr($items, 0, -1);
		}
		
		/*
		$json=new JSON();
		$json->add("items", $items);
		//$json->add("items", $items, JSON::TYPE_FUNCTION);
		echo $json->encode(); // This would output {"name" : "some_name", "close_func" : Effects.Close}
		 */
		 
		 header('Content-type: application/json');
		?>
		{
				"items": [
					<?php echo $items;?>
				]
		}
		
		<?php
	
		exit(0);
	}


	public function chatBoxSession($chatbox)
	{
		$session=new THttpSession;
		$session->open();
			
		$items = '';
		
		if (isset($session['chatHistory'][$chatbox])) {
			$items = $session['chatHistory'][$chatbox];
		}
	
		return $items;
	}

	public function startChatSession() 
	{
		$session=new THttpSession;
		$session->open();
		
		$items = '';
		if (!empty($session['openChatBoxes'])) {
			foreach ($session['openChatBoxes'] as $chatbox => $void) {
				$items .= $this->chatBoxSession($chatbox);
			}
		}
		
		if ($items != '') {
			$items = substr($items, 0, -1);
		}
		
		/*
		$json=new JSON();
		$json->add("username", $this->User->Name);
		$json->add("items", array($items));
		echo $json->encode(); // This would output {"name" : "some_name", "close_func" : Effects.Close}
		*/
		
		header('Content-type: application/json');
		?>
		{
				"username": "<?php echo $_SESSION['username'];?>",
				"items": [
					<?php echo $items;?>
				]
		}
		
		<?php
	
		exit(0);
	}	
	
	
	public function sendChat()
	{
		$session=new THttpSession;
		$session->open();
		
		$from = $session['username'];
		$to = $this->Request['to'];
		$message = $this->Request['message'];
	
		$session['openChatBoxes'][$this->Request['to']] = date('Y-m-d H:i:s', time());
		
		$messagesan = $this->sanitize($message);
	
		if (!isset($session['chatHistory'][$this->Request['to']])) {
			$session['chatHistory'][$this->Request['to']] = '';
		}
		
		$session['chatHistory'][$this->Request['to']] .= <<<EOD
		{
				"s": "1",
				"f": "{$to}",
				"m": "{$messagesan}";
		   },
EOD;
		
	
		unset($session['tsChatBoxes'][$this->Request['to']]);
		
		$sql = "insert into chat (chat.from,chat.to,message,sent) values ('".TPropertyValue::ensureString($from)."', '".TPropertyValue::ensureString($to)."','".TPropertyValue::ensureString($message)."',NOW())";
		$query = $this->queryAction($sql,'C');
		echo "1";
		exit(0);
	}
	
	public function closeChat() {
	
		$session=new THttpSession;
		$session->open();
		
		unset($session['openChatBoxes'][$this->Request['chatbox']]);
		
		echo "1";
		exit(0);
	}
	
	public function sanitize($text) {
		$text = htmlspecialchars($text, ENT_QUOTES);
		$text = str_replace("\n\r","\n",$text);
		$text = str_replace("\r\n","\n",$text);
		$text = str_replace("\n","<br>",$text);
		return $text;
	}
	
	
}
?>