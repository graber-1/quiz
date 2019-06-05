<?php

namespace Drupal\quiz\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\quiz\Entity\QuizQuestionInterface;

/**
 * Class QuizQuestionController.
 *
 *  Returns responses for Quiz question routes.
 */
class QuizQuestionController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Quiz question  revision.
   *
   * @param int $quiz_question_revision
   *   The Quiz question  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($quiz_question_revision) {
    $quiz_question = $this->entityManager()->getStorage('quiz_question')->loadRevision($quiz_question_revision);
    $view_builder = $this->entityManager()->getViewBuilder('quiz_question');

    return $view_builder->view($quiz_question);
  }

  /**
   * Page title callback for a Quiz question  revision.
   *
   * @param int $quiz_question_revision
   *   The Quiz question  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($quiz_question_revision) {
    $quiz_question = $this->entityManager()->getStorage('quiz_question')->loadRevision($quiz_question_revision);
    return $this->t('Revision of %title from %date', ['%title' => $quiz_question->label(), '%date' => format_date($quiz_question->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Quiz question .
   *
   * @param \Drupal\quiz\Entity\QuizQuestionInterface $quiz_question
   *   A Quiz question  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QuizQuestionInterface $quiz_question) {
    $account = $this->currentUser();
    $langcode = $quiz_question->language()->getId();
    $langname = $quiz_question->language()->getName();
    $languages = $quiz_question->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $quiz_question_storage = $this->entityManager()->getStorage('quiz_question');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $quiz_question->label()]) : $this->t('Revisions for %title', ['%title' => $quiz_question->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all quiz question revisions") || $account->hasPermission('administer quiz question entities')));
    $delete_permission = (($account->hasPermission("delete all quiz question revisions") || $account->hasPermission('administer quiz question entities')));

    $rows = [];

    $vids = $quiz_question_storage->revisionIds($quiz_question);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quiz\QuizQuestionInterface $revision */
      $revision = $quiz_question_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $quiz_question->getRevisionId()) {
          $link = $this->l($date, new Url('entity.quiz_question.revision', ['quiz_question' => $quiz_question->id(), 'quiz_question_revision' => $vid]));
        }
        else {
          $link = $quiz_question->link($date);
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
              'url' => $has_translations ?
              Url::fromRoute('entity.quiz_question.translation_revert', ['quiz_question' => $quiz_question->id(), 'quiz_question_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.quiz_question.revision_revert', ['quiz_question' => $quiz_question->id(), 'quiz_question_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.quiz_question.revision_delete', ['quiz_question' => $quiz_question->id(), 'quiz_question_revision' => $vid]),
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

    $build['quiz_question_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
