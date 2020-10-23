<?php

namespace Drupal\build_spec_generator\Plugin;

/**
 * Interface for storage plugins.
 */
interface StorageInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * All the plugins should implement this method to export content.
   *
   * Typically content destinations can be file storage, Google Drive sheet,
   * JIRA Page etc.
   */
  public function export();

}
