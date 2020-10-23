<?php

namespace Drupal\build_spec_generator\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for storage plugins.
 */
abstract class StoragePluginBase extends PluginBase implements StorageInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * {@inheritDoc}
   */
  abstract public function export();

}
