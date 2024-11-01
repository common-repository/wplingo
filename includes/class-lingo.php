<?php
/**
 * Main Lingo Class (singleton)
 *
 * @package Lingo
 * @since 0.1
 * @access public
 */

class Lingo {

    /**
     * Lingo singleton
     *
     * @var Lingo
     */
    private static $_instance = NULL;
    
    /**
     * Lingo data container
     *
     * @var array
     */
    private $_data = array();
    
    /**
     * Singleton, returns Lingo instance
     * 
     * @return Lingo
     */
    public static function instance() {
        if( self::$_instance === NULL ) {
            self::$_instance = new self;
        }
        
        return self::$_instance;
    }
    
    /**
     * Returns lingo saved data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get( $key, $default = NULL ) {
        if(isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return $default;
        }
    }
    
    /**
     * Sets lingo option
     * 
     * @param string $key
     * @param mixed $data
     */
    public function set( $key, $data ) {
        $this->_data[$key] = $data;
    }
    
}
