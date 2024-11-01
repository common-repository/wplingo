<?php

/**
 * Enumerations
 * 
 * Class handles enumerations for WPLingo
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

abstract class TopicStatus
{
    const Closed = 0;
    const Open = 1;
    const Featured = 2;
    
    
    /**
     * Returns array of all topic status
     * 
     * @return array
     */
    static function getAll() {
        return array(
                "Closed" => 0,
                "Open" => 1,
                "Featurd" => 2,
        );
    }

}

