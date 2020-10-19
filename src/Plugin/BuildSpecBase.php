<?php

namespace Drupal\build_spec_generator\Plugin;

use Drupal\Component\Plugin\PluginBase;

/**
 * Base class for Build Spec Item plugins.
 */
abstract class BuildSpecBase extends PluginBase implements BuildSpecInterface {

  /**
   * {@inheritDoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

}
