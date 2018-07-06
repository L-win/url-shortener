<?php
        
    class Database
    {
        
        private $con;
        
        public function __construct()
        {
            $this -> con = $this -> connect();
        }
        
        private function connect()
        {
            // return mysqli_connect('','','','');
            return mysqli_connect('localhost','root','root','test');
        }
        
        public function get($shortUrl)
        {
            $sql = " SELECT * FROM links WHERE short = '$shortUrl' ";
            $query = mysqli_query( $this->con, $sql );
            return $query;
        }
        
        public function set($originalUrl,$shortUrl)
        {
            $originalUrl = mysqli_escape_string( $this->con, $originalUrl );
            $sql = " INSERT INTO links(original,short) VALUES( '$originalUrl', '$shortUrl' ) ";
            $query = mysqli_query( $this->con, $sql );
        }
        
        public function exists($originalUrl)
        {
            $originalUrl = mysqli_escape_string( $this->con, $originalUrl );
            $sql = " SELECT * FROM links WHERE original = '$originalUrl' ";
            $query = mysqli_query($this->con,$sql);
            return $query;
        }
        
    }
    
    class App
    {
        
        private $db;
        
        public function __construct()
        {
            $this -> db = new Database();
        }
        
        public function boot()
        {
            $array = explode('/',$_SERVER[ 'REQUEST_URI' ]);
            if ( in_array('u',$array) ) {
                $this->runRedirect();
            }
            else {
                return $this->runShortener();
            }
        }
        
        private function runShortener()
        {
            if ( $this->isUrlSet() ) {
                $originalUrl = $this -> isUrlSet();
                // echo $originalUrl;
                $exists = $this->db->exists($originalUrl);
                if ( mysqli_num_rows($exists) == 0 ) {
                    $shortUrl = $this -> makeShort($originalUrl);
                    $this -> db -> set( $originalUrl, $shortUrl );
                    $result = ['short'=>$shortUrl];
                    return $result;
                }
                else {
                    $result = mysqli_fetch_assoc($exists);
                    return $result;
                }
            }
        }
        
        private function isUrlSet()
        {
            if ( isset($_POST['originalUrl'] ) and !empty(isset($_POST['originalUrl']) ) ) {
                $originalUrl = $_POST['originalUrl'];
                $originalUrl = str_replace('http://','',$originalUrl);
                return $originalUrl;
            }
            else {
                return FALSE;
            }
        }
                        
        private function makeShort($originalUrl)
        {
            $shortUrl = substr(md5(microtime()),rand(0,26),5);
            return $shortUrl;
        }
        
        private function runRedirect()
        {
            $uri = $_SERVER[ 'REQUEST_URI' ];
            $route = ltrim( $uri, '/' );
            $route = rtrim( $route, '/' );
            $route = htmlentities( $route );
            $route = explode( '/', $route );
            var_dump($route);
            if ( in_array('u',$route) ) {
                $shortUrl = array_pop($route);
                echo $shortUrl;
                $result = $this->db->get($shortUrl);
                if ( mysqli_num_rows($result) == 1 ) {
                    $originalUrl = mysqli_fetch_assoc($result);
                    $originalUrl = $originalUrl['original'];
                    header('Location: http://'. $originalUrl);
                }
            }
        }
        
    }
    
    