<?php

namespace Drupal\quiz\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\quiz\Entity\QuizResultInterface;

/**
 * Class QuizResultController.
 *
 *  Returns responses for Quiz result routes.
 */
class QuizResultController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Quiz result  revision.
   *
   * @param int $quiz_result_revision
   *   The Quiz result  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($quiz_result_revision) {
    $quiz_result = $this->entityManager()->getStorage('quiz_result')->loadRevision($quiz_result_revision);
    $view_builder = $this->entityManager()->getViewBuilder('quiz_result');

    return $view_builder->view($quiz_result);
  }

  /**
   * Page title callback for a Quiz result  revision.
   *
   * @param int $quiz_result_revision
   *   The Quiz result  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($quiz_result_revision) {
    $quiz_result = $this->entityManager()->getStorage('quiz_result')->loadRevision($quiz_result_revision);
    return $this->t('Revision of %title from %date', ['%title' => $quiz_result->label(), '%date' => format_date($quiz_result->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Quiz result .
   *
   * @param \Drupal\quiz\Entity\QuizResultInterface $quiz_result
   *   A Quiz result  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QuizResultInterface $quiz_result) {
    $account = $this->currentUser();
    $langcode = $quiz_result->language()->getId();
    $langname = $quiz_result->language()->getName();
    $languages = $quiz_result->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $quiz_result_storage = $this->entityManager()->getStorage('quiz_result');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $quiz_result->label()]) : $this->t('Revisions for %title', ['%title' => $quiz_result->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all quiz result revisions") || $account->hasPermission('administer quiz result entities')));
    $delete_permission = (($account->hasPermission("delete all quiz result revisions") || $account->hasPermission('administer quiz result entities')));

    $rows = [];

    $vids = $quiz_result_storage->revisionIds($quiz_result);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quiz\QuizResultInterface $revision */
      $revision = $quiz_result_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $quiz_result->getRevisionId()) {
          $link = $this->l($date, new Url('entity.quiz_result.revision', ['quiz_result' => $quiz_result->id(), 'quiz_result_revision' => $vid]));
        }
        else {
          $link = $quiz_result->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
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
              'url' => Url::fromRoute('entity.quiz_result.revision_revert', ['quiz_result' => $quiz_result->id(), 'quiz_result_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.quiz_result.revision_delete', ['quiz_result' => $quiz_result->id(), 'quiz_result_revision' => $vid]),
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

    $build['quiz_result_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
