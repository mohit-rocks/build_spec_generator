<?php

namespace Drupal\build_spec_generator\Plugin\BuildSpec;

use Drupal\build_spec_generator\Annotation\BuildSpec;
use Drupal\build_spec_generator\MarkDownBuilderService;
use Drupal\build_spec_generator\Plugin\BuildSpecBase;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\content_translation\ContentTranslationManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use MaddHatter\MarkdownTable\Builder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides plugin to export node types in markdown table format.
 *
 * @BuildSpec(
 *   id = "node_types",
 *   description = @Translation("Export build specification related to node types."),
 *   label = "NodeTypes",
 *   keys = {
 *     "Name",
 *     "Type",
 *     "Description",
 *     "Help",
 *     "Content Moderation",
 *     "Translatable",
 *     "Comments",
 *     "Meta Tags",
 *     "Pathauto",
 *     "Searchable",
 *     "Layout Builder",
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
   * Table builder object.
   *
   * @var \MaddHatter\MarkdownTable\Builder
   */
  protected $tableBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, RendererInterface $renderer, EntityTypeManagerInterface  $entityTypeManager, ModerationInformationInterface $moderation_information, ContentTranslationManagerInterface $content_translation_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
    $this->moderationInformation = $moderation_information;
    $this->contentTranslationManager = $content_translation_manager;
    $this->tableBuilder = new Builder();
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
   * {@inheritdoc}
   */
  public function prepareContent():string {
    $rows = [];
    $plugin_definitions = $this->getPluginDefinition();

    $this->tableBuilder->headers($plugin_definitions['keys']);
    $this->tableBuilder->align(['L']);

    // Prepare list of all the rows.
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($node_types as $node_type) {
      /** @var \Drupal\node\Entity\NodeType $node_type */
      $row = [];
      $row[] = $node_type->label();
      $row[] = $node_type->id();
      $row[] = $node_type->getDescription();
      $row[] = $node_type->getHelp();
      $row[] = $this->moderationInformation->shouldModerateEntitiesOfBundle($this->entityTypeManager->getDefinition('node'), $node_type->id()) == TRUE ? 'Y': 'N';
      $row[] = $this->contentTranslationManager->isEnabled('node', $node_type->id()) == TRUE ? 'Y': 'N';
      $row[] = 'Comment';
      $row[] = 'Metatags';
      $row[] = 'Pathauto';
      $row[] = 'Searchable';
      $row[] = 'Layout';
      // Move entire row to separate array.
      $rows[] = $row;
    }
    $this->tableBuilder->rows($rows);
    return $this->tableBuilder->render();
  }

}
