<?php

namespace Drupal\misk_news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\config\ConfigFactoryInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Component\Datetime\TimeInterface;

final class NewsController extends ControllerBase {

public function __construct(private readonly EntityTypeManagerInterface $entityTypeManagerService,
                            private readonly ConfigFactoryInterface $configFactoryObject,
                            private readonly DateFormatterInterface $dateFormatter,
                            private readonly TimeInterface $time,)
{
}

public static function create(ContainerInterface $container): static {
    return new static($container->get('entity_type.manager'),
                      $container->get('config.factory'),
                      $container->get('date.formatter'),
                      $container->get('datetime.time'));
}

public function page(): array {
    $config = $this->configFactoryObject->get('misk_news.settings');
    $currentDate = $this->dateFormatter->format(
        $this->time->getCurrentTime(),
        'custom',
        'd F Y',
    );

    $pageTitle = $config->get('page_title') ?? 'All News';
    $headerImage = $config->get('header_image') ?? [];
    $itemsPerPage = (int) ($config->get('items_per_page') ?? 2);
    $sortCriteria = $config->get('sort_criteria') ?? 'created';
    $sortOrder = $config->get('sort_order') ?? 'DESC';

    $imageUri = '';

    if(!empty($headerImage)) {
        $file = File::load($headerImage[0]);
        if($file) {
            $imageUri = $file->getFileUri();
        }
    }

    $mediaHeaderProps = [
        'title' => $pageTitle,
        'date' => $currentDate,
    ];
    if(!empty($imageUri)) {
        $mediaHeaderProps['image'] = $imageUri;
    }

    $mediaHeader = [
        '#type' => 'component',
        '#component' => 'misk_theme:media-header',
        '#props' => $mediaHeaderProps,
    ];

    
    
    
    $nodeStorage = $this->entityTypeManagerService->getStorage('node');
    $query = $nodeStorage->getQuery()
                         ->accessCheck(TRUE)
                         ->condition('type', 'news')
                         ->condition('status', 1)
                         ->sort($sortCriteria, $sortOrder)
                         ->pager($itemsPerPage);

      $nids= $query->execute();          
      $nodes = $nodeStorage->loadMultiple($nids);
      $viewBuilder = $this->entityTypeManagerService->getViewBuilder('node');
      $news = [];
       
      foreach($nodes as $node) {
        $news[] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'col-12',
                    'col-md-6',
                    'mb-4',
                ],
            ],
            'content' => $viewBuilder->view($node,'news_card'),
        ];
      }



      return [
        
        'media_header' => $mediaHeader,

        'news' => [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'row',
                    empty($imageUri) ? 'mt-19' : '',
                ],
            ],
            'news' => $news,

        ],
        'pager' => [
            '#type' => 'pager',
        ],
        '#cache' => [
             'tags' => $config->getCacheTags(),
        ],
      ];
}



}