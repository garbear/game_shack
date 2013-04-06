<?php
/**
 * Property model.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */

App::uses('AppModel', 'Model');

class Property extends AppModel {

    public $hasMany = 'Gamefile';

    public $actsAs = array('Containable');
    
    public $recursive = -1;
}
