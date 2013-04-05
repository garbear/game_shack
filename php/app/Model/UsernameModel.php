<?php
/**
 * Username model.
 *
 * @copyright     Copyright (c) 2013 Garrett Brown
 * @link          http://kineticthings.com
 * @license       GPLv2 <http://www.gnu.org/licenses/>
 */

App::uses('AppModel', 'Model');

class Username extends AppModel {

    public $hasAndBelongsToMany = array(
        'Gamefile' => array(
            'className' => 'Gamefile',
            'joinTable' => 'gamefileslinkusernames',
        ),
    );
}
