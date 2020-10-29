<?php

namespace Drupal\build_spec_generator\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines an interface for Build Spec Item plugins.
 */
interface BuildSpecInterface extends PluginInspectionInterface {

  /**
   * Retrieve plugin label from plugin annotation.
   *
   * @return string
   */
  public function label();

  /**
   * Prepare the items that needs to be exported to markdown file.
   *
   * @return array
   */
  public function prepareContent();

}
