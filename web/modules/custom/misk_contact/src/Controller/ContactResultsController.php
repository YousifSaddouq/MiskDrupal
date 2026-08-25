<?php

namespace Drupal\misk_contact\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DateFormatterInterface;
use Override;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContactResultsController extends ControllerBase
{
    public function __construct(private readonly Connection $database,
                                private readonly DateFormatterInterface $dateFormatter,)
    {
    }

    #[Override]
    public static function create(ContainerInterface $container)
    {
        return new static($container->get('database'),
                          $container->get('date.formatter'),);
    }

    public function page(): array {
        $query = $this->database->select('misk_contact_submission', 'submission');
        $query->fields('submission',[
            'id',
            'full_name',
            'email',
            'age',
            'message',
            'created',
        ]);
        
        $query->orderBy('created','DESC');
        $results = $query->execute()->fetchAll();
        $rows = [];
        foreach($results as $result) {
            $rows[] = [
                'id' => $result->id,
                'full_name' => $result->full_name,
                'email' => $result->email,
                'age' => $result->age,
                'message' => $result->message,
                'created' => $this->dateFormatter->format(
                 (int) $result->created,
                 'custom',
                 'd F Y H:i'
                ),
            ];
        }
       
        return [
            '#type' => 'table',

            '#header' => [
                $this->t('ID'),
                $this->t('Full Name'),
                $this->t('Email'),
                $this->t('Age'),
                $this->t('Message'),
                $this->t('Created'),
            ],

            '#rows' => $rows,
            
            '#empty' => $this->t('No Submissions Yet'),
        ];

    }
}