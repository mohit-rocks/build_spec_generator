<?php

namespace Drupal\build_spec_generator\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Build Spec Item item annotation object.
 *
 * @see \Drupal\build_spec_generator\Plugin\BuildSpecManager
 * @see plugin_api
 *
 * @Annotation
 */
class BuildSpec extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
