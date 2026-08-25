<?php

namespace Drupal\misk_contact\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Override;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContactForm extends FormBase 
{
    public function __construct(private readonly Connection $database,)
    {
    }

    public static function create(ContainerInterface $container): static {
        return new static($container->get('database'));
    }

    #[Override]
    public function getFormId(): string
    {
         return 'misk_contact_form';
    }

    #[Override]
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['full_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Full Name'),
            '#required' => TRUE,
        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email Address'),
            '#required' => TRUE,
        ];

        $form['age'] = [
            '#type' => 'select',
            '#title' => $this->t('Age'),
            '#options' => [
                '' => $this->t('- Select - '),
                13 => '13',
                14 => '14',
                15 => '15',
                16 => '16',
                17 => '17',
                18 => '18',
                19 => '19',
                20 => '20',
            ],
           '#required' => TRUE, 
        ];

        $form['message'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Message'),
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit'),
        ];

        return $form;


    }

    #[Override]
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->database
             ->insert('misk_contact_submission')
             ->fields([
                'full_name' => $form_state->getValue('full_name'),
                'email' => $form_state->getValue('email'),
                'age' => $form_state->getValue('age'),
                'message' => $form_state->getValue('message'),
                'created' => time(),
                
             ])
             ->execute();

             $this->messenger()->addStatus($this->t('The Form has been successfully submitted.'));
             $form_state->setRedirect('<current>');
    }
}