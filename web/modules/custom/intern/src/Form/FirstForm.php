<?php
namespace Drupal\intern\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FirstForm extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        #$node = \Drupal::routeMatch()->getParameter('node');
        #$nid = $node->nid->value;
        $teams = [
            'Lakers' => 'Lakers',
            'Clippers' => 'Clippers',
            'Raptors' => 'Raptors',
        ];

        $form['first_name'] = [
            '#type' => 'textfield',
            '#title' => 'First Name',
            '#required' => TRUE,
        ];

        $form['last_name'] = [
            '#type' => 'textfield',
            '#title' => 'Last Name',
            '#required' => TRUE,
        ];

        $form['username'] = [
            '#type' => 'textfield',
            '#title' => 'Username',
        ];

        $form['teams'] = [
            '#type' => 'select',
            '#title' => 'Favorite Team',
            '#options' => $teams,
            '#required' => True,
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['generate'] = [
            '#name' => 'gen_button',
            '#type' => 'button',
            '#value' => 'Make me a username!',
        ];

        $form['actions']['submit'] = [
            '#name' => 'submit_button',
            '#type' => 'submit',
            '#value' => 'Submit',
        ];

        $input = $form_state->getTriggeringElement();
        $values = $form_state->getValues();
        $our_service = \Drupal::service('intern_service.coffee');

        if (!empty($values) && $input['#name'] == 'gen_button'){
            $first_name = $form_state->getValue('first_name');
            $last_name = $form_state->getValue('last_name');
            $fave_team = $form_state->getValue('teams');
            $result = $our_service->generateUsername($first_name, $last_name, $fave_team);
            $form['username']['#value'] = $result;
        }

        return $form;
    }

    public function getFormId() {
        return 'intern_first_form';
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->setRebuild();
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $first_name = $form_state->getValue('first_name');
        if(empty($first_name)) {
            $form_state->setErrorByName('title', 'The title must exist.');
        }
    }
}

