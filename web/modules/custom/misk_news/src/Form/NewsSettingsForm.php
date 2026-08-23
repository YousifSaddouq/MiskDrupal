<?php

namespace Drupal\misk_news\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
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

        $form['items_per_page'] = [
            '#type' => 'number',
            '#title' => $this->t('Select Items Per Page'),
            '#default_value' => $config->get('items_per_page') ?? 2,
            '#min' => 1,
            '#required' => TRUE,

        ];

        $form['sort_order'] = [
            '#type' => 'select',
            '#title' => 'Select ASC or DESC',
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

        $config->set('items_per_page', $form_state->getValue('items_per_page'))
               ->set('sort_order', $form_state->getValue('sort_order'))
               ->save();
      
        return parent::submitForm($form, $form_state);
    }
}