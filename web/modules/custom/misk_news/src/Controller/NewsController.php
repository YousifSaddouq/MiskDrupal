<?php

namespace Drupal\misk_news\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\config\ConfigFactoryInterface;

final class NewsController extends ControllerBase {

public function __construct(private readonly EntityTypeManagerInterface $entityTypeManagerService,
                            private readonly ConfigFactoryInterface $configFactoryObject,)
{
}

public static function create(ContainerInterface $container): static {
    return new static($container->get('entity_type.manager'),
                      $container->get('config.factory'));
}

public function page(): array {
    $config = $this->configFactoryObject->get('misk_news.settings');
    $itemsPerPage = (int) ($config->get('items_per_page') ?? 2);
    $sortOrder = $config->get('sort_order') ?? 'DESC';


    $nodeStorage = $this->entityTypeManagerService->getStorage('node');
    $query = $nodeStorage->getQuery()
                         ->accessCheck(TRUE)
                         ->condition('type', 'news')
                         ->condition('status', 1)
                         ->sort('created', $sortOrder)
                         ->pager($itemsPerPage);

      $nids= $query->execute();          
      $nodes = $nodeStorage->loadMultiple($nids);
      $viewBuilder = $this->entityTypeManagerService->getViewBuilder('node');
      $news = $viewBuilder->viewMultiple($nodes,'news_card');
      return [
        'news' => $news,
        'pager' => [
            '#type' => 'pager',
        ],
        '#cache' => [
             'tags' => $config->getCacheTags(),
        ],
      ];
}



}