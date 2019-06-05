<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Quiz result entity.
 *
 * @see \Drupal\quiz\Entity\QuizResult.
 */
class QuizResultAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\quiz\Entity\QuizResultInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished quiz result entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published quiz result entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit quiz result entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete quiz result entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add quiz result entities');
  }

}
