<?php

namespace Drupal\build_spec_generator\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Storage plugins to define destination storage for the build spec export.
 *
 * @Annotation
 */
class Storage extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
