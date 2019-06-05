<?php

namespace Drupal\quiz\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\quiz\Service\QuestionTypeManager;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'question_type_widget' widget.
 *
 * @FieldWidget(
 *   id = "question_type_widget",
 *   label = @Translation("Question type widget"),
 *   field_types = {
 *     "quiz_question_type"
 *   }
 * )
 */
class QuestionTypeWidget extends WidgetBase implements ContainerFactoryPluginInterface {


  /**
   * IKO requirements manager.
   *
   * @var \Drupal\quiz\Service\QuestionTypeManager
   */
  protected $quesionTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.quiz_question_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, QuestionTypeManager $quesionTypeManager) {

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->quesionTypeManager = $quesionTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $return = [];

    $type_options = [];
    $type_definitions = $this->quesionTypeManager->getDefinitions();
    foreach ($type_definitions as $id => $definition) {
      $type_options[$id] = $definition['label'];
    }

    $element['question_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Requirement'),
      '#default_value' => $items[$delta]->question_type,
      '#required' => TRUE,
      '#options' => $type_options,
    ];

    return $element;
  }

}
