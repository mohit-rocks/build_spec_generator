<?php

namespace Drupal\build_spec_generator\Plugin\BuildSpec;

use Drupal\build_spec_generator\Annotation\BuildSpec;
use Drupal\build_spec_generator\MarkDownBuilderService;
use Drupal\build_spec_generator\Plugin\BuildSpecBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use MaddHatter\MarkdownTable\Builder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides plugin to export image styles in markdown table format.
 *
 * @BuildSpec(
 *   id = "image_styles",
 *   description = @Translation("Export build specification related to image styles."),
 *   label = "ImageStyles",
 *   keys = {
 *     "Name",
 *     "Machine Name",
 *   }
 * )
 */
class ImageStyles extends BuildSpecBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Table builder object.
   *
   * @var \MaddHatter\MarkdownTable\Builder
   */
  protected $tableBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, EntityTypeManagerInterface  $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
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
      $container->get('entity_type.manager')
    );
  }

  /**
   * {inheritdoc}
   */
  public function prepareContent():string {
    $plugin_definitions = $this->getPluginDefinition();

    $this->tableBuilder->headers($plugin_definitions['keys']);
    $this->tableBuilder->align(['L']);

    // Prepare list of all the image styles.
    $rows = [];
    $image_styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    foreach ($image_styles as $style) {
      /** @var \Drupal\image\Entity\ImageStyle $style */
      $row = [];
      $row[] = $style->label();
      $row[] = $style->id();
      $rows[] = $row;
    }
    $this->tableBuilder->rows($rows);
    return $this->tableBuilder->render();
  }

}
