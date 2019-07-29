<?php
namespace Drupal\intern\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FirstForm extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        #$node = \Drupal::routeMatch()->getParameter('node');
        #$nid = $node->nid->value;
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

        $form['teams'] = [
            '#type' => 'select',
            '#title' => 'Favorite Team',
            '#options' => [
                '1' => 'Lakers',
                '2' => 'Clippers',
                '3' => 'Raptors',
            ],
            '#required' => True,
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['generate'] = [
            '#type' => 'button',
            '#value' => 'Make me a username!',
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Submit',
        ];

        return $form;
    }

    public function getFormId() {
        return 'intern_first_form';
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $title = $form_state->getValue('first_name');
        $input = $form_state->getUserInput();
        drupal_set_message($input['_triggering_element_name']);
        // $wufirst = $wutangNameGenerator();
        // $wulast = $wutangNameGenerator();
        // drupal_set_message('You submitted the form with title: '.$title);
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        // $title = $form_state->getValue('title');
        // if(empty($title)) {
        //     $form_state->setErrorByName('title', 'The title must exist.');
        // }
    }

    public function generateUsername(array &$form, FormStateInterface $form_state) {
        $first_name = $form_state->getValue('first_name');
        $last_name = $form_state->getValue('last_name');

        // $form_state->setValue('');
    }
}

