<?php

namespace Drupal\misk_news\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Override;

final class NewsSettingsForm extends ConfigFormBase
{
    #[Override]
    public function getFormId(): string
    {
        return 'misk_news_settings_form';
    }

    #[Override]
    protected function getEditableConfigNames(): array
    {
        return [
            'misk_news.settings',
        ];
    }

    #[Override]
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('misk_news.settings');

        $form['page_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Select a title for the page'),
            '#default_value' => $config->get('page_title') ?? 'All News',
            '#required' => TRUE,
        ];

        $form['header_image'] = [
            '#type' => 'managed_file',
            '#title' => $this->t('Select an image for the page'),
            '#default_value' => $config->get('header_image') ?? [],
            '#upload_location' => 'public://misk_news/',
            '#upload_validators' => [
                'FileExtension' => [
                    'extensions' => 'png jpg jpeg webp',
                ],
            ]
        ];

        $form['items_per_page'] = [
            '#type' => 'number',
            '#title' => $this->t('Select Items Per Page'),
            '#default_value' => $config->get('items_per_page') ?? 2,
            '#min' => 1,
            '#required' => TRUE,

        ];

         $form['sort_criteria'] = [
            '#type' => 'select',
            '#title' => $this->t('Sort Criteria'),
            '#options' => [
                'created' => $this->t('Created Date'),
                'changed' => $this->t('Last Updated Date'),
                'title' => $this->t('Title'),
            ],
            '#default_value' => $config->get('sort_criteria') ?? 'created',
           '#required' => TRUE, 
        ];

        $form['sort_order'] = [
            '#type' => 'select',
            '#title' => $this->t('Select ASC or DESC'),
            '#options' => [
                'ASC' => 'Oldest First',
                'DESC' => 'Newest First',
            ],
            '#default_value' => $config->get('sort_order') ?? 'DESC',
            '#required' => TRUE,
        ];

        return parent::buildForm($form, $form_state);
    }

    #[Override]
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('misk_news.settings');

        $image = $form_state->getValue('header_image');
        if(!empty($image[0])) {
            $file = File::load($image[0]);
            if($file) {
                $file->setPermanent();
                $file->save();
            }
        }

        $config->set('page_title',$form_state->getValue('page_title'))
               ->set('header_image',$image)
               ->set('items_per_page', $form_state->getValue('items_per_page'))
               ->set('sort_criteria', $form_state->getValue('sort_criteria'))
               ->set('sort_order', $form_state->getValue('sort_order'))
               ->save();
      
        return parent::submitForm($form, $form_state);
    }
}