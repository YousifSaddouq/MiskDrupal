<?php

namespace Drupal\misk_helpers\Twig;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MiskTwigExtension extends AbstractExtension {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly DateFormatterInterface $dateFormatter,
  ) {}

  public function getFunctions(): array {
    return [
      new TwigFunction('created_date', [$this, 'getCreatedDate']),
    ];
  }

  public function getCreatedDate(): string {
    $entity = $this->routeMatch->getParameter('canvas_page');

    if (!$entity || !$entity->hasField('created')) {
      return '';
    }

    return $this->dateFormatter->format(
      (int) $entity->get('created')->value,
      'custom',
      'd F Y'
    );
  }

}