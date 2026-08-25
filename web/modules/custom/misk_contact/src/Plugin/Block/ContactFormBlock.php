<?php

namespace Drupal\misk_contact\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\misk_contact\Form\ContactForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Block(
    id: 'misk_contact_form_block',
    admin_label: new TranslatableMarkup('Misk Custom Contact Form'),
    category: new TranslatableMarkup('Misk'),
)]

final class ContactFormBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    
    public function __construct(array $configuration, 
                                $plugin_id, 
                                $plugin_definition,
                                private readonly FormBuilderInterface $formBuilder)
    {
         parent::__construct($configuration,
                             $plugin_id, 
                             $plugin_definition);
    }

    public static function create(ContainerInterface $container,
                                  array $configuration,
                                  $plugin_id, 
                                  $plugin_definition,): static {
                                    return new static($configuration,
                                                      $plugin_id,
                                                      $plugin_definition,
                                                      $container->get('form_builder'),);
                                  }
    
    public function build(): array
    {
        return $this->formBuilder->getForm(ContactForm::class);
    }                              
}