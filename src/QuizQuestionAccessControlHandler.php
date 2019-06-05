<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Quiz question entity.
 *
 * @see \Drupal\quiz\Entity\QuizQuestion.
 */
class QuizQuestionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\quiz\Entity\QuizQuestionInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished quiz question entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published quiz question entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit quiz question entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete quiz question entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add quiz question entities');
  }

}
