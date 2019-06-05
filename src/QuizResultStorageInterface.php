<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quiz\Entity\QuizResultInterface;

/**
 * Defines the storage handler class for Quiz result entities.
 *
 * This extends the base storage class, adding required special handling for
 * Quiz result entities.
 *
 * @ingroup quiz
 */
interface QuizResultStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Quiz result revision IDs for a specific Quiz result.
   *
   * @param \Drupal\quiz\Entity\QuizResultInterface $entity
   *   The Quiz result entity.
   *
   * @return int[]
   *   Quiz result revision IDs (in ascending order).
   */
  public function revisionIds(QuizResultInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Quiz result author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Quiz result revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quiz\Entity\QuizResultInterface $entity
   *   The Quiz result entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QuizResultInterface $entity);

  /**
   * Unsets the language for all Quiz result with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
