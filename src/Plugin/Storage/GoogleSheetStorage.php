<?php

namespace Drupal\build_spec_generator\Plugin\Storage;

use Drupal\build_spec_generator\BuildSpecGenerator;
use Drupal\build_spec_generator\Plugin\StoragePluginBase;
use Drupal\Component\FileSecurity\FileSecurity;
use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the storage plugin.
 *
 * @Storage(
 *   id = "google_sheet_storage",
 *   label = @Translation("Google Sheets Storage"),
 *   description = @Translation("Plugin to export build spec files in Google sheet.")
 * )
 */
class GoogleSheetStorage extends StoragePluginBase implements ContainerFactoryPluginInterface {
  /**
   * Drupal\build_spec_generator\BuildSpecGenerator definition.
   *
   * @var \Drupal\build_spec_generator\BuildSpecGenerator
   */
  protected $buildSpecGeneratorGenerator;

  /**
   * Drupal\Core\File\FileSystemInterface definition.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Destination directory to export markdown files.
   *
   * @var string $directory
   */
  public $directory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, BuildSpecGenerator $build_spec_generator, FileSystemInterface $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->buildSpecGeneratorGenerator = $build_spec_generator;
    $this->fileSystem = $file_system;
    $this->directory = Settings::get('build_spec_directory');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('build_spec_generator.generator'),
      $container->get('file_system')
    );
  }

  /**
   * Export the configuration to markdown files.
   */
  public function export() {
    // @todo: Implement business logic to generate and write in google sheets.
  }

}
