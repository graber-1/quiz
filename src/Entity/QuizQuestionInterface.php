<?php

namespace Drupal\quiz\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Quiz question entities.
 *
 * @ingroup quiz
 */
interface QuizQuestionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Quiz question name.
   *
   * @return string
   *   Name of the Quiz question.
   */
  public function getName();

  /**
   * Sets the Quiz question name.
   *
   * @param string $name
   *   The Quiz question name.
   *
   * @return \Drupal\quiz\Entity\QuizQuestionInterface
   *   The called Quiz question entity.
   */
  public function setName($name);

  /**
   * Gets the Quiz question creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Quiz question.
   */
  public function getCreatedTime();

  /**
   * Sets the Quiz question creation timestamp.
   *
   * @param int $timestamp
   *   The Quiz question creation timestamp.
   *
   * @return \Drupal\quiz\Entity\QuizQuestionInterface
   *   The called Quiz question entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Quiz question published status indicator.
   *
   * Unpublished Quiz question are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Quiz question is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Quiz question.
   *
   * @param bool $published
   *   TRUE to set this Quiz question to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\quiz\Entity\QuizQuestionInterface
   *   The called Quiz question entity.
   */
  public function setPublished($published);

  /**
   * Gets the Quiz question revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Quiz question revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quiz\Entity\QuizQuestionInterface
   *   The called Quiz question entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Quiz question revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Quiz question revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quiz\Entity\QuizQuestionInterface
   *   The called Quiz question entity.
   */
  public function setRevisionUserId($uid);

}
