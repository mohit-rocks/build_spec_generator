<?php

namespace Drupal\build_spec_generator\Plugin\Storage;

use Drupal\build_spec_generator\BuildSpecGenerator;
use Drupal\build_spec_generator\Plugin\StoragePluginBase;
use Drupal\Component\FileSecurity\FileSecurity;
use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Site\Settings;
use MaddHatter\MarkdownTable\Builder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the storage plugin.
 *
 * @Storage(
 *   id = "file_storage",
 *   label = @Translation("File Storage"),
 *   description = @Translation("Plugin to export build spec files in project repository..")
 * )
 */
class FileStorage extends StoragePluginBase implements ContainerFactoryPluginInterface {
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
    $config_exports = $this->buildSpecGeneratorGenerator->generate();
    $markups = $this->generateMarkups($config_exports);
    // Ensure that directory is writable.
    $this->ensureStorage();
    foreach ($markups as $name => $export) {
      $target = $this->getFilePath($name);
      $status = @file_put_contents($target, $export);
      if ($status === FALSE) {
        throw new StorageException('Failed to write configuration file: ' . $this->getFilePath($name));
      }
    }
    return TRUE;
  }

  /**
   * Returns the file extension used by file storage.
   *
   * @return string
   *   The file extension.
   */
  public static function getFileExtension() {
    return 'md';
  }

  /**
   * Returns the path to the configuration file.
   *
   * @return string
   *   The path to the configuration file.
   */
  public function getFilePath($name) {
    return $this->directory . '/' . $name . '.' . static::getFileExtension();
  }

  /**
   * Check if the directory exists and create it if not.
   */
  protected function ensureStorage() {
    $success = $this->fileSystem->prepareDirectory($this->directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $success = $success && FileSecurity::writeHtaccess($this->directory);
    if (!$success) {
      throw new StorageException('Failed to create markdown storage directory.' . $this->directory);
    }
    return $this;
  }

  /**
   * Generate markup file from each config export.
   *
   * @param $config_exports
   *   Array of config export returned from each plugin.
   *
   * @return array
   *   Rendered markup for each configuration.
   */
  public function generateMarkups($config_exports) {
    $markups = [];
    foreach ($config_exports as $name => $config_export) {
      $table_builder = new Builder();
      $table_builder->headers($config_export['header']);
      $table_builder->rows($config_export['rows']);
      $table_builder->align($config_export['alignment']);
      $markups[$name] = $table_builder->render();
    }
    return $markups;
  }

}
