<?php

/**
 * @file
 * Contains quiz_question.page.inc.
 *
 * Page callback for Quiz question entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Quiz question templates.
 *
 * Default template: quiz_question.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_quiz_question(array &$variables) {
  // Fetch QuizQuestion Entity Object.
  $quiz_question = $variables['elements']['#quiz_question'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
