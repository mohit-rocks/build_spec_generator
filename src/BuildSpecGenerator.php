<?php

namespace Drupal\build_spec_generator;

use Drupal\build_spec_generator\Plugin\BuildSpecManager;

/**
 * Build spec generator class to handle generating build spec from plugins.
 */
class BuildSpecGenerator {

  /**
   * Drupal\build_spec_generator\Plugin\BuildSpecManager definition.
   *
   * @var \Drupal\build_spec_generator\Plugin\BuildSpecManager
   */
  protected $pluginManagerBuildSpec;

  /**
   * Constructs a new BuildSpecGenerator object.
   *
   * @param BuildSpecManager $plugin_manager_build_spec
   *   BuildSpec manager instance.
   */
  public function __construct(BuildSpecManager $plugin_manager_build_spec) {
    $this->pluginManagerBuildSpec = $plugin_manager_build_spec;
  }

  /**
   * Get the data from all the plugins and pass to file storage service.
   */
  public function generate() {
    $plugins = $this->pluginManagerBuildSpec->getDefinitions();
    foreach ($plugins as $doc_item) {
      $instance = $this->pluginManagerBuildSpec->createInstance($doc_item['id']);

      // Call the service from file storage class.
    }
  }

}
