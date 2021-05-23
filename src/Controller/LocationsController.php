<?php

namespace Drupal\custom_migration_mongo\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\custom_migration_mongo\Entity\LocationsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LocationsController.
 *
 *  Returns responses for Location routes.
 */
class LocationsController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Location revision.
   *
   * @param int $locations_revision
   *   The Location revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($locations_revision) {
    $locations = $this->entityTypeManager()->getStorage('locations')
      ->loadRevision($locations_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('locations');

    return $view_builder->view($locations);
  }

  /**
   * Page title callback for a Location revision.
   *
   * @param int $locations_revision
   *   The Location revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($locations_revision) {
    $locations = $this->entityTypeManager()->getStorage('locations')
      ->loadRevision($locations_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $locations->label(),
      '%date' => $this->dateFormatter->format($locations->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Location.
   *
   * @param \Drupal\custom_migration_mongo\Entity\LocationsInterface $locations
   *   A Location object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(LocationsInterface $locations) {
    $account = $this->currentUser();
    $locations_storage = $this->entityTypeManager()->getStorage('locations');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $locations->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all location revisions") || $account->hasPermission('administer location entities')));
    $delete_permission = (($account->hasPermission("delete all location revisions") || $account->hasPermission('administer location entities')));

    $rows = [];

    $vids = $locations_storage->revisionIds($locations);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\custom_migration_mongo\LocationsInterface $revision */
      $revision = $locations_storage->loadRevision($vid);
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $locations->getRevisionId()) {
          $link = $this->l($date, new Url('entity.locations.revision', [
            'locations' => $locations->id(),
            'locations_revision' => $vid,
          ]));
        }
        else {
          $link = $locations->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => Url::fromRoute('entity.locations.revision_revert', [
                'locations' => $locations->id(),
                'locations_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.locations.revision_delete', [
                'locations' => $locations->id(),
                'locations_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
    }

    $build['locations_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
