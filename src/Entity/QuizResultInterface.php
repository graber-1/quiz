<?php

namespace Drupal\quiz\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Quiz result entities.
 *
 * @ingroup quiz
 */
interface QuizResultInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Quiz result name.
   *
   * @return string
   *   Name of the Quiz result.
   */
  public function getName();

  /**
   * Sets the Quiz result name.
   *
   * @param string $name
   *   The Quiz result name.
   *
   * @return \Drupal\quiz\Entity\QuizResultInterface
   *   The called Quiz result entity.
   */
  public function setName($name);

  /**
   * Gets the Quiz result creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Quiz result.
   */
  public function getCreatedTime();

  /**
   * Sets the Quiz result creation timestamp.
   *
   * @param int $timestamp
   *   The Quiz result creation timestamp.
   *
   * @return \Drupal\quiz\Entity\QuizResultInterface
   *   The called Quiz result entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Quiz result published status indicator.
   *
   * Unpublished Quiz result are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Quiz result is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Quiz result.
   *
   * @param bool $published
   *   TRUE to set this Quiz result to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\quiz\Entity\QuizResultInterface
   *   The called Quiz result entity.
   */
  public function setPublished($published);

  /**
   * Gets the Quiz result revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Quiz result revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quiz\Entity\QuizResultInterface
   *   The called Quiz result entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Quiz result revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Quiz result revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quiz\Entity\QuizResultInterface
   *   The called Quiz result entity.
   */
  public function setRevisionUserId($uid);

}
