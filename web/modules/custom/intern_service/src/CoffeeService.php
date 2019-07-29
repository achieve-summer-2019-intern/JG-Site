<?php

namespace Drupal\intern_service;

class CoffeeService {
    public function generateUsername($first_name, $last_name, $fave_team) { 
        $username = $first_name . '4' . $fave_team;
        return $username;
    }
}
