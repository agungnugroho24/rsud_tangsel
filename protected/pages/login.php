<?php
class login extends TPage
{
    public function onPreRender($param)
	{
		parent::onPreRender($param);
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);		
		if(!$this->IsPostBack){	
			//$this->Username->focus();			
			if(!$this->User->IsGuest)
				$this->Response->redirect($this->Service->constructUrl('Simak'));
		}				
	}
	
    public function validateUser($sender,$param)
    {
		if(trim($this->Username->Text) != '' && trim($this->Password->Text) != '' )
		{
			$authManager=$this->Application->getModule('auth');
			if(!$authManager->login($this->Username->Text,$this->Password->Text))
			{
				$param->IsValid=false;  // tell the validator that validation fails				
				
				$this->msg->Text = '    
				<script type="text/javascript">
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden"> Username atau Password salah</p>\',timeout: 2000});
					jQuery(\'#btn\').show();
					jQuery(\'#loading\').hide();
					jQuery(\'.username\').val(\'\');
					jQuery(\'.password\').val(\'\');
					jQuery(\'.username-label\').animate({ opacity: "0.6" }, "fast");
					jQuery(\'.password-label\').animate({ opacity: "0.6" }, "fast");
					unmaskContent();
				</script>';	
				
				/*
				$this->msg->Text = '    
				<script type="text/javascript">
					jQuery.WsGrowl.show({title: \'Login Gagal!\', content:\'<p> Username atau Password salah</p>\',timeout: 6000,dialog:{
						modal: true,
						buttons: {
							Ok: function() {
								jQuery( this ).dialog( "close" );
								jQuery(\'.username\').val(\'\');
								jQuery(\'.password\').val(\'\');
								jQuery(\'.username-label\').animate({ opacity: "0.6" }, "fast");
								jQuery(\'.password-label\').animate({ opacity: "0.6" }, "fast");
							}
						}
					}});
					jQuery(\'#btn\').show();
					jQuery(\'#loading\').hide();
				</script>';
				*/
				$this->Page->CallbackClient->focus($this->Username); 
			}
		}
    }
 
    public function showDetails($sender,$param)
    {
		$this->msg->Text = '    
			<script type="text/javascript">
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden"> Username atau Password salah</p>\',timeout: 2000});
				jQuery(\'#btn\').show();
				jQuery(\'#loading\').hide();
				unmaskContent();
			</script>';	
	}
	
    public function loginClicked($sender,$param)
    {   
		//nama & pass kosong   
		if(trim($this->Username->Text) == '' && trim($this->Password->Text) == '' )
		{
			$msg = 'Username dan Password belum di isi !';
			$this->msg->Text = '    
			<script type="text/javascript">
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">'.$msg.'</p>\',timeout: 2000,dialog:\'beforeClose\'});
				jQuery(\'#btn\').show();
				jQuery(\'#loading\').hide();
				unmaskContent();
			</script>';	
			$this->Page->CallbackClient->focus($this->Username); 
		}
		elseif(trim($this->Username->Text) != '' && trim($this->Password->Text) == '' ) //pass kosong
		{
			$msg = 'Password belum di isi !';
			$this->msg->Text = '    
			<script type="text/javascript">
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">'.$msg.'</p>\',timeout: 2000,dialog:\'beforeClose\'});
				jQuery(\'#btn\').show();
				jQuery(\'#loading\').hide();
				unmaskContent();
			</script>';
			$this->Page->CallbackClient->focus($this->Password); 
		}
		elseif(trim($this->Username->Text) == '' && trim($this->Password->Text) != '' ) //nama kosong
		{
			$msg = 'Username belum di isi !';
			$this->msg->Text = '    
			<script type="text/javascript">
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">'.$msg.'</p>\',timeout: 2000,dialog:\'beforeClose\'});
				jQuery(\'#btn\').show();
				jQuery(\'#loading\').hide();
				unmaskContent();
			</script>';
			$this->Page->CallbackClient->focus($this->Username); 
		}
		else
		{
			if($this->Page->IsValid)  // all validations succeed
			{   			
				$url=$this->Service->constructUrl('Simak');
				$this->Response->redirect($url);
			}		
		} 
		
    }
	
	public function cekBlank($sender,$param)
    {       
		if(trim($this->Username->Text) == '' && trim($this->Password->Text) == '' )
		{
			$msg = 'Username dan Password belum di isi !';
			$this->passBlankVal->Enabled = false;
		}
		elseif(trim($this->Username->Text) != '' && trim($this->Password->Text) == '' )
		{
			$msg = 'Password belum di isi !';
		}
		else
		{
			$msg = 'Username belum di isi !';
		}
		
		
		
		$this->msg->Text = '';    
		//$this->passBlankVal->Enabled = true;	
    }
	
	public function passwordBlank($sender,$param)
    {       
		$this->msg->Text = '    
		<script type="text/javascript">
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">Password belum di isi !</p>\',timeout: 2000});
		</script>';
    }   
	
	
	
}
?>
