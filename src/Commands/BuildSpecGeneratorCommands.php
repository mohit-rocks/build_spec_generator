<?php

namespace Drupal\build_spec_generator\Commands;

use Drupal\build_spec_generator\FileStorageService;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;
use League\HTMLToMarkdown\HtmlConverter;

/**
 * Drupal command to generate the build spec from teh configuration.
 */
class BuildSpecGeneratorCommands extends DrushCommands {

  use StringTranslationTrait;

  /**
   * Drupal\build_spec_generator\FileStorageService definition.
   *
   * @var \Drupal\build_spec_generator\FileStorageService
   */
  protected $fileStorageService;

  /**
   * Constructs a new FileStorageService object.
   *
   * @param FileStorageService $fileStorageService
   *   File storage service to get and export the markdown files.
   */
  public function __construct(FileStorageService $fileStorageService) {
    parent::__construct();
    $this->fileStorageService = $fileStorageService;
  }

  /**
   * Export build spec in markdown format based on BuildSpec plugins.
   *
   * @usage generate_build_spec
   *   Generate the configuration and export to markdown
   *
   * @command build_spec_generator:generate_build_spec
   * @aliases gen-build-spec
   */
  public function generateBuildSpec() {
    // Perform export operation.
    $this->fileStorageService->export();
  }
}
