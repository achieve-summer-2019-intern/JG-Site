<?php

namespace Drupal\most_simple\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\most_simple\Entity\MostSimpleEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MostSimpleEntityController.
 *
 *  Returns responses for Most simple entity routes.
 */
class MostSimpleEntityController extends ControllerBase implements ContainerInjectionInterface {


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
   * Constructs a new MostSimpleEntityController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Most simple entity revision.
   *
   * @param int $most_simple_entity_revision
   *   The Most simple entity revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($most_simple_entity_revision) {
    $most_simple_entity = $this->entityTypeManager()->getStorage('most_simple_entity')
      ->loadRevision($most_simple_entity_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('most_simple_entity');

    return $view_builder->view($most_simple_entity);
  }

  /**
   * Page title callback for a Most simple entity revision.
   *
   * @param int $most_simple_entity_revision
   *   The Most simple entity revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($most_simple_entity_revision) {
    $most_simple_entity = $this->entityTypeManager()->getStorage('most_simple_entity')
      ->loadRevision($most_simple_entity_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $most_simple_entity->label(),
      '%date' => $this->dateFormatter->format($most_simple_entity->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Most simple entity.
   *
   * @param \Drupal\most_simple\Entity\MostSimpleEntityInterface $most_simple_entity
   *   A Most simple entity object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(MostSimpleEntityInterface $most_simple_entity) {
    $account = $this->currentUser();
    $most_simple_entity_storage = $this->entityTypeManager()->getStorage('most_simple_entity');

    $langcode = $most_simple_entity->language()->getId();
    $langname = $most_simple_entity->language()->getName();
    $languages = $most_simple_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $most_simple_entity->label()]) : $this->t('Revisions for %title', ['%title' => $most_simple_entity->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all most simple entity revisions") || $account->hasPermission('administer most simple entity entities')));
    $delete_permission = (($account->hasPermission("delete all most simple entity revisions") || $account->hasPermission('administer most simple entity entities')));

    $rows = [];

    $vids = $most_simple_entity_storage->revisionIds($most_simple_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\most_simple\MostSimpleEntityInterface $revision */
      $revision = $most_simple_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $most_simple_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.most_simple_entity.revision', [
            'most_simple_entity' => $most_simple_entity->id(),
            'most_simple_entity_revision' => $vid,
          ]));
        }
        else {
          $link = $most_simple_entity->link($date);
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
              'url' => $has_translations ?
              Url::fromRoute('entity.most_simple_entity.translation_revert', [
                'most_simple_entity' => $most_simple_entity->id(),
                'most_simple_entity_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.most_simple_entity.revision_revert', [
                'most_simple_entity' => $most_simple_entity->id(),
                'most_simple_entity_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.most_simple_entity.revision_delete', [
                'most_simple_entity' => $most_simple_entity->id(),
                'most_simple_entity_revision' => $vid,
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
    }

    $build['most_simple_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
