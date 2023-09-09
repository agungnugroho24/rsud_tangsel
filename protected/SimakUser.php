<?php
// Include TDbUserManager.php file which defines TDbUser
Prado::using('System.Security.TDbUserManager');
 
/**
 * BlogUser Class.
 * BlogUser represents the user data that needs to be kept in session.
 * Default implementation keeps username and role information.
 */
class SimakUser extends TDbUser
{
    /**
     * Creates a BlogUser object based on the specified username.
     * This method is required by TDbUser. It checks the database
     * to see if the specified username is there. If so, a BlogUser
     * object is created and initialized.
     * @param string the specified username
     * @return BlogUser the user object, null if username is invalid.
     */
    public function createUser($username)
    {
        // use UserRecord Active Record to look for the specified username
        $userRecord=UserRecord::finder()->findByPk($username);
        if($userRecord instanceof UserRecord) // if found
        {
            $user=new SimakUser($this->Manager);
            $user->Name=$username;  // set username
            $roleTmp=$userRecord->role;
			if($roleTmp == "0")
			{				
				$role = "RO";//Read only user
			}
			else if($roleTmp == "1")
			{	
				$role = "PU";//Power user
			}
			else if($roleTmp == "2")
			{
				$role = "SU";//Super user			
			}
			$user->Roles=$role; // set role
			$Allowing = $userRecord->allow;				
			$user->setState('AppAuth',$Allowing);
			$nip = $userRecord->nip;
			$name = $userRecord->real_name;				
			$user->setState('userName',$name);
			$user->setState('userNip',$nip);
			$userRecord->wkt_log=date('G:i:s');
			$userRecord->tgl_log=date('Y-m-d');
			$userRecord->Save();
            $user->IsGuest=false;   // the user is not a guest
            return $user;
        }
        else
            return null;
    }
 
    /**
     * Checks if the specified (username, password) is valid.
     * This method is required by TDbUser.
     * @param string username
     * @param string password
     * @return boolean whether the username and password are valid.
     */
    public function validateUser($username,$password)
    {
        // use UserRecord Active Record to look for the (username, password) pair.
        $passVal = md5($password);
		return UserRecord::finder()->findBy_username_AND_password($username,$passVal)!==null;		
		
    }
 
    /**
     * @return boolean whether this user is an administrator.
     */
    public function getIsUserName()
    {
        return $this->getState('userName');
    }
	
	public function getIsUserNip()
    {
        return $this->getState('userNip');
    }
	
	public function getIsAppAuth()
    {
        return $this->getState('AppAuth');
    }
	
	public function getIsAdmin()
    {
        return $this->isInRole('SU');
    }
	
	public function getIsPower()
    {
        return $this->isInRole('PU');
    }
	
	public function getIsOrdinary()
    {
        return $this->isInRole('RO');
    }
}
?>