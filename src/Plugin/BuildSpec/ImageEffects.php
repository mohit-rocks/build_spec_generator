<?php

namespace Drupal\build_spec_generator\Plugin\BuildSpec;

use Drupal\build_spec_generator\Annotation\BuildSpec;
use Drupal\build_spec_generator\MarkDownBuilderService;
use Drupal\build_spec_generator\Plugin\BuildSpecBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use MaddHatter\MarkdownTable\Builder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides plugin to export image styles in markdown table format.
 *
 * @BuildSpec(
 *   id = "image_effects",
 *   description = @Translation("Export build specification related to image effects."),
 *   label = "ImageEffects",
 *   keys = {
 *     "Image Style",
 *     "Effect",
 *     "Summary",
 *   }
 * )
 */
class ImageEffects extends BuildSpecBase implements ContainerFactoryPluginInterface {

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
   * Renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, EntityTypeManagerInterface  $entityTypeManager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->renderer = $renderer;
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
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  /**
   * {inheritdoc}
   */
  public function prepareContent():array {
    $plugin_definitions = $this->getPluginDefinition();

    $header = $plugin_definitions['keys'];
    $alignment = ['L'];

    // Prepare list of all the image styles.
    $rows = [];
    $image_styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    foreach ($image_styles as $style) {
      /** @var \Drupal\image\Entity\ImageStyle $style */
      $image_effects = $style->getEffects();
      foreach ($image_effects as $image_effect) {
        /** @var \Drupal\image\ImageEffectInterface $image_effect */
        $row = [];
        $row[] = $style->label();
        $row[] = $image_effect->label();
        $summary = $image_effect->getSummary();
        $row[] = $this->renderer->renderPlain($summary);
        $rows[] = $row;
      }
    }
    return [
      'header' => $header,
      'rows' => $rows,
      'alignment' => $alignment
    ];
  }

}
