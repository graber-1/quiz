<?php

namespace Drupal\quiz\Plugin\IkoRequirements;

use Drupal\quiz\QuestionTypes\QuestionTypeBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines radios question type.
 *
 * @QuizQuestionType(
 *   id = "question_type_radios",
 *   label = @Translation("Radios"),
 * )
 */
class Radios extends QuestionTypeBase {

  /**
   * The question configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * {@inheritdoc}
   */
  public function getFormElement(array &$element, FormStateInterface $form_state) {
    $element = [
      '#type' => 'radios',
      '#title' => $this->getConfiguration('title'),
      '#options' => $this->getConfiguration('options'),
      '#required' => $this->getConfiguration('required'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationElement(array &$element, FormStateInterface $form_state) {
    $group_class = 'quiz-radios-group';
    $element['options'] = [
      '#type' => 'table',
      '#caption' => $this->t('Questions'),
      '#header' => [
        $this->t('Question'),
        $this->t('Correct answer'),
        $this->t('Weight'),
      ],
      '#empty' => $this->t('There are no questions defined.'),
      '#tabledrag' => [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => $group_class,
      ],
    ];

    $options = $this->getConfiguration('options');

    foreach ($options as $delta => $label) {
      $element[$delta]['label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Question for @delta', ['@delta' => $delta]),
        '#title_display' => 'invisible',
        '#default_value' => $label
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function calculateScore($values) {
    $result = 1;
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'title' => '',
      'options' => [],
      'required' => FALSE,
      'calculation_method' => 'binary',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
    $this->configuration += $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration($key = NULL) {
    if (isset($key)) {
      if (!isset($this->configuration[$key])) {
        return NULL;
      }
      return $this->configuration[$key];
    }
    return $this->configuration;
  }

}
