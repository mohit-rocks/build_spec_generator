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

  /**
   * All the plugins should implement this function and return array of header, footer and alignments.
   *
   * @return array
   */
  abstract public function prepareContent();

}
