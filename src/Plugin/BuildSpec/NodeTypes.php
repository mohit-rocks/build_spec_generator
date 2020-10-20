<?php

namespace Drupal\build_spec_generator\Plugin\BuildSpec;

use Drupal\build_spec_generator\Annotation\BuildSpec;
use Drupal\build_spec_generator\Plugin\BuildSpecBase;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides plugin to export node types in markdown table format.
 *
 * @BuildSpec(
 *   id = "node_types",
 *   description = @Translation("Export configurations related to node types."),
 *   label = "NodeTypes",
 *   keys = {
 *     "name",
 *     "type",
 *     "description",
 *     "help",
 *     "content_moderation",
 *     "layout_builder",
 *     "translatable",
 *     "comments",
 *     "meta_tags",
 *     "pathauto",
 *     "searchable",
 *   }
 * )
 */
class NodeTypes extends BuildSpecBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The moderation information service.
   *
   * @var \Drupal\content_moderation\ModerationInformationInterface
   */
  protected $moderationInformation;

  /**
   * The content translation manager service.
   *
   * @var \Drupal\content_translation\ContentTranslationManagerInterface
   */
  protected $contentTranslationManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, RendererInterface $renderer, EntityTypeManagerInterface  $entityTypeManager, ModerationInformationInterface $moderation_information, ContentTranslationManagerInterface $content_translation_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
    $this->moderationInformation = $moderation_information;
    $this->contentTranslationManager = $content_translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer'),
      $container->get('entity_type.manager'),
      $container->get('content_moderation.moderation_information'),
      $container->get('content_translation.manager')
    );
  }

  /**
   * {inheritdoc}
   */
  public function prepareContent():string {
    $rows = [];
    $headers = [
      ['data' => $this->t('Name')],
      ['data' => $this->t('Type')],
      ['data' => $this->t('Description')],
      ['data' => $this->t('Help')],
      ['data' => $this->t('Content Moderation')],
      ['data' => $this->t('Translatable')],
      ['data' => $this->t('Comments')],
      ['data' => $this->t('Metatags')],
      ['data' => $this->t('Pathauto')],
      ['data' => $this->t('Searchable')],
    ];
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    foreach ($node_types as $node_type) {
      /** @var \Drupal\node\Entity\NodeType $node_type */
      $row = [
        'data' => [],
      ];
      $row['data'][] = $node_type->label();
      $row['data'][] = $node_type->id();
      $row['data'][] = $node_type->getDescription();
      $row['data'][] = $node_type->getHelp();

      $row['data'][] = $this->moderationInformation->shouldModerateEntitiesOfBundle($this->entityTypeManager->getDefinition('node'), $node_type->id()) == TRUE ? 'Y': 'N';
      $row['data'][] = $this->contentTranslationManager->isEnabled('node', $node_type->id()) == TRUE ? 'Y': 'N';
      $row['data'][] = 'Comment Placeholder';
      $row['data'][] = 'Metatags Placeholder';
      $row['data'][] = 'Pathauto Placeholder';
      $row['data'][] = 'Searchable Placeholder';

      $rows[] = $row;
    }
    $nodes = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => $this->t('No blocks available.'),
    ];
    return $this->renderer->renderPlain($nodes);
  }

}
