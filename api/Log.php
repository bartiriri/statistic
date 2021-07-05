<?php


class Log
{
    /**
     * Session ID
     */
    protected $session;

    /**
     * user ip address
     */
    protected $ip;

    /**
     * User agent info
     */
    protected $userAgent;

    /**
     * site url
     */
    protected $url;

    /**
     * Database Configuration
     */
    protected $dbConfig;

    public function __construct()
    {
        $this->session = $_POST['session'];
        $this->ip = $_POST['ip'];
        $this->userAgent = $_POST['browse'];
        $this->dbConfig = include('c:/xampp/htdocs/statistic/Configuration.php');
        $this->url = $_POST['url'];
    }

    public function exec()
    {
        $connection = $this->connect();
        if($connection != false) // jeżęli udało się nawiązać połączenie z bazą danych
        {

          $isInDB = $this->checkUserInDB($connection);
            //var_dump($isInDB);die;
          if($isInDB === NULL){
            $this->addUserToDB($connection);
          }
            $isInDB = $this->checkUserInDB($connection);
          $this->addLogToDB($connection, $isInDB['id']);
        }
    }


    // function for get connection to database
    private function connect()
    {
        $connection = new mysqli($this->dbConfig['db_connection']['host'],$this->dbConfig['db_connection']['username'],$this->dbConfig['db_connection']['password'],$this->dbConfig['db_connection']['db_name']);
        if($connection-> connect_errno){
            echo 'filed connect to db';
            return false;
        }
        else{
            return $connection;
        }
    }

    private function checkUserInDB($connection)
    {



        $sql = "SELECT id FROM tx_statistic_user WHERE sess_id=?"; // SQL with parameters
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $this->session);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $user = $result->fetch_assoc(); // fetch data
        return $user;
    }
    private function addUserToDB($connection)
    {
        $data = date("Y-m-d");
        $query = $connection->prepare('INSERT INTO tx_statistic_user (ip, sess_id, user_agent, crdate) VALUES(?, ?, ?, ?)');
        $query->bind_param("ssss",$this->ip, $this->session,$this->userAgent,$data);
        $query->execute();

        return true;
    }

    private function addLogToDB($connection, $uid)
    {
        $data = date("Y-m-d");
        $query = $connection->prepare('INSERT INTO tx_statistic_log (uid, url, tstamp) VALUES (?,?,?)');
        $query->bind_param("iss",$uid,$this->url,$data);
        $query->execute();
    }
}

$new = new Log();
$new->exec();
