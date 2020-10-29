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
 *   id = "fields",
 *   description = @Translation("Export build specification related to fields."),
 *   label = "Fields",
 *   keys = {
 *     "Label",
 *     "Machie Name",
 *     "Description",
 *     "Bundle",
 *     "Type",
 *     "Host Bundle",
 *     "Host Entity",
 *     "Cardinality",
 *     "Required",
 *     "Translatable",
 *   }
 * )
 */
class Fields extends BuildSpecBase implements ContainerFactoryPluginInterface {

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
  public function prepareContent():array {
    $plugin_definitions = $this->getPluginDefinition();

    $header = $plugin_definitions['keys'];
    $alignment = ['L'];

    // Prepare list of all the image styles.
    $rows = [];
    $field_configs = $this->entityTypeManager->getStorage('field_config')->loadMultiple();
    foreach ($field_configs as $field_config) {
      /** @var \Drupal\field\Entity\FieldConfig $field_config */

      $field_storage_config = $this->entityTypeManager->getStorage('field_storage_config')->loadByProperties([
        'field_name' => $field_config->getName(),
      ]);
      /** @var \Drupal\field\Entity\FieldStorageConfig $field_storage_config */
      $field_storage_config = reset($field_storage_config);

      $row = [];
      $row[] = $field_config->label();
      $row[] = $field_config->getName();
      $row[] = $field_config->getDescription();
      $row[] = $field_config->bundle();
      $row[] = $field_config->getType();
      $row[] = $field_config->getTargetBundle();
      $row[] = $field_config->getTargetEntityTypeId();
      $row[] = $field_storage_config->getCardinality();
      $row[] = $field_config->isRequired() == TRUE ? 'Y' : 'N';
      $row[] = $field_config->isTranslatable() == TRUE ? 'Y' : 'N';
      $rows[] = $row;
    }
    return [
      'header' => $header,
      'rows' => $rows,
      'alignment' => $alignment
    ];
  }

}
