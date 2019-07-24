<?php
namespace Drupal\intern\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FirstForm extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state){
        $form['title'] = [
            '#type' => 'textfield',
            '#title' => 'First Form',
            '#description' => 'Here is the title for our first form',
            '#required' => TRUE,
        ];

        $form['actions'] = [
            '#type' => 'actions',
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
        $title = $form_state->getValue('title');
        // $wufirst = $wutangNameGenerator();
        // $wulast = $wutangNameGenerator();
        drupal_set_message('You submitted the form with title: '.$title);
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        $title = $form_state->getValue('title');
        if(empty($title)) {
            $form_state->setErrorByName('title', 'The title must exist.');
        }
    }
}