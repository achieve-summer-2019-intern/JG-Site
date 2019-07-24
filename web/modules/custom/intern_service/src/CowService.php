<?php

namespace Drupal\intern_service;

use Drupal\Core\Session\AccountInterface;

class CowService {
    private $currentUser;
    private $sounds = ['moo', 'honk'];

    public function saySomething() {
        return $this->$sounds[array_rand($this->sounds)];
    }

    public function __construct(AccountInterface $currentUser) {
        $this->currentUser = $currentUser;
    }

    public function whoIsYourOwner() {
        return $this->currentUser->getDisplayName();
    }

    public function howDoYouLookLike() {
        return print_r(
            '
           (    )
            (oo)
   )\.-----/(O O)
  # ;       / u
    (  .   |} )
     |/ `.;|/;
     "     " "
     ', FALSE
        );
    }


}