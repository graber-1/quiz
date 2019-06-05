<?php

namespace Drupal\quiz\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'question_type' field type.
 *
 * @FieldType(
 *   id = "question_type",
 *   label = @Translation("Question type"),
 *   description = @Translation("Allows question type plugin selection"),
 *   default_widget = "question_type_widget" * )
 */
class QuestionType extends FieldItemBase {

  const MAX_LENGTH = 64;

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => self::MAX_LENGTH,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['question_type'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Question type'))
      ->setDescription(t('Question type plugin ID.'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'question_type' => [
          'type' => 'varchar',
          'length' => self::MAX_LENGTH,
          'binary' => FALSE,
        ],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', [
      'question_type' => [
        'Length' => [
          'max' => self::MAX_LENGTH,
          'maxMessage' => t('Question type: may not be longer than @max characters.', [
            '@max' => self::MAX_LENGTH,
          ]),
        ],
      ],
    ]);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $possible = ['quiz_radios', 'quiz_checkboxes'];
    $values['question_type'] = $possible[rand(0, 1)];
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('question_type')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName() {
    return 'question_type';
  }

}
