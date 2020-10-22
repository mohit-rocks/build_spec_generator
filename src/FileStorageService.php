<?php

namespace Drupal\build_spec_generator;

use Drupal\Component\FileSecurity\FileSecurity;
use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Site\Settings;

/**
 * File storage service class to store the markdown files in specified folder.
 */
class FileStorageService {

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
   * Constructs a new FileStorageService object.
   *
   * @param \Drupal\build_spec_generator\BuildSpecGenerator $build_spec_generator
   *   Buildspec generator service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   File system service.
   */
  public function __construct(BuildSpecGenerator $build_spec_generator, FileSystemInterface $file_system) {
    $this->buildSpecGeneratorGenerator = $build_spec_generator;
    $this->fileSystem = $file_system;
    $this->directory = Settings::get('build_spec_directory');
  }

  /**
   * Export the configuration to markdown files.
   */
  public function export() {
    $config_exports = $this->buildSpecGeneratorGenerator->generate();
    // Ensure that directory is writable.
    $this->ensureStorage();
    foreach ($config_exports as $name => $export) {
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

}
