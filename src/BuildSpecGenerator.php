<?php

namespace Drupal\build_spec_generator;

use Drupal\build_spec_generator\Plugin\BuildSpecManager;
use League\HTMLToMarkdown\HtmlConverter;

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
   * Html to markdown converter class.
   *
   * @var \League\HTMLToMarkdown\HtmlConverter
   */
  protected $converter;

  /**
   * Constructs a new BuildSpecGenerator object.
   *
   * @param BuildSpecManager $plugin_manager_build_spec
   *   BuildSpec manager instance.
   */
  public function __construct(BuildSpecManager $plugin_manager_build_spec) {
    $this->pluginManagerBuildSpec = $plugin_manager_build_spec;
    $this->converter = new HtmlConverter();
  }

  /**
   * Get the data from all the plugins and pass to file storage service.
   *
   * @return
   *   Array of all the configuration's markup text.
   */
  public function generate():array {
    // Attribute will contain array returned from each plugin.
    // Typically each plugin will return [$header, $rows, $alignment]
    $attributes = [];
    $plugins = $this->pluginManagerBuildSpec->getDefinitions();
    foreach ($plugins as $doc_item) {
      $instance = $this->pluginManagerBuildSpec->createInstance($doc_item['id']);
      $content = $instance->prepareContent();
      $attributes[$doc_item['label']] = $content;
    }
    return $attributes;
  }

}
