<?php

namespace Drupal\quiz;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Quiz question entities.
 *
 * @ingroup quiz
 */
class QuizQuestionListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Quiz question ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\quiz\Entity\QuizQuestion */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.quiz_question.edit_form',
      ['quiz_question' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
