<?php

namespace Drupal\build_spec_generator\Plugin\BuildSpec;

use Drupal\build_spec_generator\Annotation\BuildSpec;
use Drupal\build_spec_generator\Plugin\BuildSpecBase;

/**
 * Provides a ham sandwich.
 *
 * @BuildSpec(
 *   id = "node_types",
 *   description = @Translation("Export configurations related to node types."),
 *   keys = {
 *     "name",
 *     "type",
 *     "description",
 *     "help",
 *     "content_moderation",
 *     "layout_builder",
 *     "translatable",
 *     "comments",
 *     "meta_tags",
 *     "pathauto",
 *     "searchable",
 *   }
 * )
 */
class ExampleHamSandwich extends BuildSpecBase {

  /**
   * {inheritdoc}
   */
  public function prepareContent() {
    // TODO: Implement prepareExport() method.
  }

}
