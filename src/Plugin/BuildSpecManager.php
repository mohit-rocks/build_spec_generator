<?php

namespace Drupal\build_spec_generator\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Build Spec Item plugin manager.
 */
class BuildSpecManager extends DefaultPluginManager {


  /**
   * Constructs a new BuildSpecManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/BuildSpec', $namespaces, $module_handler, 'Drupal\build_spec_generator\Plugin\BuildSpecInterface', 'Drupal\build_spec_generator\Annotation\BuildSpec');

    $this->alterInfo('build_spec_generator_build_spec_info');
    $this->setCacheBackend($cache_backend, 'build_spec_generator_build_spec_plugins');
  }

}
