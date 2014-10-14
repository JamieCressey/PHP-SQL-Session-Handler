<?php

/**
* A PHP session handler to keep session data within a MySQL database
*
* @author 	Manuel Reinhard <manu@sprain.ch>
* @link		https://github.com/sprain/PHP-MySQL-Session-Handler
*/

class SQLSessionHandler{

    /**
     * a database MySQLi connection resource
     * @var resource
     */
    protected $dbConnection;
    
    
    /**
     * the name of the DB table which handles the sessions
     * @var string
     */
    protected $dbTable;
	


	/**
	 * Set db data if no connection is being injected
	 * @param 	string	$dbHost	
	 * @param	string	$dbUser
	 * @param	string	$dbPassword
	 * @param	string	$dbDatabase
	 */	
	public function setDbDetails($dbHost, $dbUser, $dbPassword, $dbDatabase){

		//create db connection
		$this->dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);
		
		//check connection
		if (mysqli_connect_error()) {
		    throw new Exception('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}//if
			
	}//function
	
	
	
	/**
	 * Inject DB connection from outside
	 * @param 	object	$dbConnection	expects MySQLi object
	 */
	public function setDbConnection($dbConnection){
	
		$this->dbConnection = $dbConnection;
		
	}
	
	
	/**
	 * Inject DB connection from outside
	 * @param 	object	$dbConnection	expects MySQLi object
	 */
	public function setDbTable($dbTable){
	
		$this->dbTable = $dbTable;
		
	}
	

    /**
     * Open the session
     * @return bool
     */
    public function open() {
  
        //delete old session handlers
        $limit = time() - (3600 * 24);
        $sql = sprintf("DELETE FROM %s WHERE timestamp < %s", $this->dbTable, $limit);
        return $this->dbConnection->query($sql);

    }

    /**
     * Close the session
     * @return bool
     */
    public function close() {

        return $this->dbConnection->CloseConnection();

    }

    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id) {

	$sql = "SELECT data FROM $this->dbTable WHERE id = :id";
	$params = array("id"=>$id);
        //$sql = sprintf("SELECT data FROM %s WHERE id = '%s'", $this->dbTable, $this->dbConnection->quote($id));
        if ($result = $this->dbConnection->query($sql, $params)) {
            if ($result->num_rows && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                return $record['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
        
    }
    

    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data) {

	$sql = "REPLACE INTO $this->dbTable VALUES(:id, :data, :timestamp)";
	$params = array("id"=>$id, "data"=>$data, "timestamp"=>time());
        return $this->dbConnection->query($sql, $params);

    }

    /**
     * Destoroy the session
     * @param int session id
     * @return bool
     */
    public function destroy($id) {

	$sql = "DELETE FROM $this->dbTable WHERE `id` = :id";
	$params = array("id"=>$id);
        return $this->dbConnection->query($sql, $params);

	}
	
	

    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public function gc($max) {

        $sql = sprintf("DELETE FROM %s WHERE `timestamp` < '%s'", $this->dbTable, time() - intval($max));
        return $this->dbConnection->query($sql);

    }

}//class
