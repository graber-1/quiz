<?php

namespace Drupal\quiz\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Quiz entity.
 *
 * @ingroup quiz
 *
 * @ContentEntityType(
 *   id = "quiz",
 *   label = @Translation("Quiz"),
 *   bundle_label = @Translation("Quiz type"),
 *   handlers = {
 *     "storage" = "Drupal\quiz\QuizStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\quiz\QuizListBuilder",
 *     "views_data" = "Drupal\quiz\Entity\QuizViewsData",
 *     "translation" = "Drupal\quiz\QuizTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\quiz\Form\QuizForm",
 *       "add" = "Drupal\quiz\Form\QuizForm",
 *       "edit" = "Drupal\quiz\Form\QuizForm",
 *       "delete" = "Drupal\quiz\Form\QuizDeleteForm",
 *     },
 *     "access" = "Drupal\quiz\QuizAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\quiz\QuizHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "quiz",
 *   data_table = "quiz_field_data",
 *   revision_table = "quiz_revision",
 *   revision_data_table = "quiz_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer quiz entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/quiz/{quiz}",
 *     "add-page" = "/admin/structure/quiz/add",
 *     "add-form" = "/admin/structure/quiz/add/{quiz_type}",
 *     "edit-form" = "/admin/structure/quiz/{quiz}/edit",
 *     "delete-form" = "/admin/structure/quiz/{quiz}/delete",
 *     "version-history" = "/admin/structure/quiz/{quiz}/revisions",
 *     "revision" = "/admin/structure/quiz/{quiz}/revisions/{quiz_revision}/view",
 *     "revision_revert" = "/admin/structure/quiz/{quiz}/revisions/{quiz_revision}/revert",
 *     "revision_delete" = "/admin/structure/quiz/{quiz}/revisions/{quiz_revision}/delete",
 *     "translation_revert" = "/admin/structure/quiz/{quiz}/revisions/{quiz_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/quiz",
 *   },
 *   bundle_entity_type = "quiz_type",
 *   field_ui_base_route = "entity.quiz_type.edit_form"
 * )
 */
class Quiz extends RevisionableContentEntityBase implements QuizInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the quiz owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Quiz entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Quiz entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Quiz is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    // Quiz - specific fields.
    $fields['questions'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Questions'))
      ->setDescription(t('The ID of the current booking status.'))
      ->setSetting('target_type', 'booking_status')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference_label',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'label' => 'inline',
        'type' => 'options_select',
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['question_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Question count'))
      ->setDescription(t('The number of questions a user must answer when taking the quiz. This number will be shuffled from all the questions assigned to this quiz. If there are not enough questions assigned, all will be used.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'min' => 0,
      ]);

    $fields['time_limit'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Time limit'))
      ->setDescription(t('Maximum duration of the quiz in seconds.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'min' => 0,
      ]);

    $fields['max_attempts'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Maximum attempts'))
      ->setDescription(t('The maximum number of pass attempts a single user can make. Zero for unlimited.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'min' => 0,
      ]);

    $fields['calculation_method'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Result calculation method'))
      ->setDescription(t('The result calculation method. Currently supported: 0 - highest score, 1 - average score.'))
      ->setDefaultValue(0)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number_integer',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'min' => 0,
      ]);

    return $fields;
  }

}
