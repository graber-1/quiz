<?php

/**
 * @file
 * Contains quiz_result.page.inc.
 *
 * Page callback for Quiz result entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Quiz result templates.
 *
 * Default template: quiz_result.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_quiz_result(array &$variables) {
  // Fetch QuizResult Entity Object.
  $quiz_result = $variables['elements']['#quiz_result'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
