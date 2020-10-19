<?php

namespace Drupal\build_spec_generator\Commands;

use Drupal\Core\Site\Settings;
use Drush\Commands\DrushCommands;

/**
 * Drupal command to generate the build spec from teh configuration.
 */
class BuildSpecGeneratorCommands extends DrushCommands {

  /**
   * Export build spec in markdown format based on BuildSpec plugins.
   *
   * @usage generate_build_spec
   *   Generate the configuration and export to markdown
   *
   * @command build_spec_generator:generate_build_spec
   * @aliases gen
   */
  public function generateBuildSpec() {
    // Get destination directory.
    $destination_dir = Settings::get('spec');

    // Perform export operation.
    $preview = $this->export($destination_dir);
  }
}
