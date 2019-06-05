<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quiz\Entity\QuizQuestionInterface;

/**
 * Defines the storage handler class for Quiz question entities.
 *
 * This extends the base storage class, adding required special handling for
 * Quiz question entities.
 *
 * @ingroup quiz
 */
class QuizQuestionStorage extends SqlContentEntityStorage implements QuizQuestionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(QuizQuestionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {quiz_question_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {quiz_question_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(QuizQuestionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {quiz_question_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('quiz_question_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
