<?php

namespace Drupal\quiz\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Quiz Question Type annotation object.
 *
 * Plugin Namespace: Plugin\QuizQuestionTypes.
 *
 * @see \Drupal\quiz\QuizQuestionTypes\QuestionTypeInterface
 * @see \Drupal\quiz\Service\QuestionTypeManager
 * @see \Drupal\quiz\QuizQuestionTypes\QuestionTypeBase
 * @see plugin_api
 *
 * @Annotation
 */
class QuizQuestionType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the Quiz Question Type plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
