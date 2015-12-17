<?php

///db class from php docs
//Usage examples:
//DB::exec("DELETE FROM Blah");
//foreach( DB::query("SELECT * FROM Blah") as $row){
// print_r($row);
//}


class DB {

    private static $objInstance;

    /*
     * Class Constructor - Create a new database connection if one doesn't exist
     * Set to private so no-one can create a new instance via ' = new DB();'
     */

    private function __construct() {
        
    }

    /*
     * Like the constructor, we make __clone private so nobody can clone the instance
     */

    private function __clone() {
        
    }

    /*
     * Returns DB instance or create initial connection
     * @param
     * @return $objInstance;
     */

    public static function getInstance() {

        if (!self::$objInstance) {
        //    self::$objInstance = new PDO('mysql:androidAHM3Nt31n;us-cdbr-azure-east-c.cloudapp.net', 'b1eff14b89b086', '0603db9a');
          self::$objInstance = new PDO(
    'mysql:host=us-cdbr-azure-east-c.cloudapp.net;dbname=androidAHM3Nt31n',
    'b1eff14b89b086',
    '0603db9a',
    array(
        PDO::MYSQL_ATTR_SSL_KEY    =>'/assets/cert/client-private-azure.pem',
        PDO::MYSQL_ATTR_SSL_CERT=>'/assets/cert/clinet-azure.pem',
        PDO::MYSQL_ATTR_SSL_CA    =>'/assets/cert/to/cleardb.pem'
    )
);
            self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$objInstance;
    }

# end method

    /* Database=androidAHM3Nt31n;Data Source=us-cdbr-azure-east-c.cloudapp.net;User Id=b1eff14b89b086;Password=0603db9a
     * Passes on any static calls to this class onto the singleton PDO instance
     * @param $chrMethod, $arrArguments
     * @return $mix
     */

    final public static function __callStatic($chrMethod, $arrArguments) {

        $objInstance = self::getInstance();

        return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);
    }

# end method
}

?>