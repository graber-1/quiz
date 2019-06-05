<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface QuizQuestionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Quiz question revision IDs for a specific Quiz question.
   *
   * @param \Drupal\quiz\Entity\QuizQuestionInterface $entity
   *   The Quiz question entity.
   *
   * @return int[]
   *   Quiz question revision IDs (in ascending order).
   */
  public function revisionIds(QuizQuestionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Quiz question author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Quiz question revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quiz\Entity\QuizQuestionInterface $entity
   *   The Quiz question entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QuizQuestionInterface $entity);

  /**
   * Unsets the language for all Quiz question with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
