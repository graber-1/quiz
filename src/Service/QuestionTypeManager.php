<?php

namespace Drupal\quiz\Service;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Component\Plugin\CategorizingPluginManagerInterface;
use Drupal\Core\Plugin\CategorizingPluginManagerTrait;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Defines IKO requirement plugin manager.
 */
class QuestionTypeManager extends DefaultPluginManager implements CategorizingPluginManagerInterface {

  use CategorizingPluginManagerTrait;


  const ALTER_EVENT = 'quiz.get_question_types';

  /**
   * Event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Service constructor.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler to invoke the alter hook with.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher service.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cacheBackend,
    ModuleHandlerInterface $moduleHandler,
    EventDispatcherInterface $eventDispatcher
  ) {
    parent::__construct(
      'Plugin/QuizQuestionTypes',
      $namespaces,
      $moduleHandler,
      'Drupal\quiz\QuizQuestionTypes\QuestionTypeInterface',
      'Drupal\quiz\Annotation\QuizQuestionType'
    );
    $this->alterInfo('quiz_question_types_info');
    $this->setCacheBackend($cacheBackend, 'quiz_question_types_info');
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterDefinitions(&$definitions) {
    // Let other modules change definitions.
    $event = new Event();
    $event->definitions = &$definitions;
    $this->eventDispatcher->dispatch(static::ALTER_EVENT, $event);
  }

}
