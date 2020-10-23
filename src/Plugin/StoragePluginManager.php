<?php

namespace Drupal\build_spec_generator\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Storage plugin manager.
 */
class StoragePluginManager extends DefaultPluginManager {

  /**
   * Constructs StoragePluginManager object.
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
    parent::__construct(
      'Plugin/Storage',
      $namespaces,
      $module_handler,
      'Drupal\build_spec_generator\Plugin\StorageInterface',
      'Drupal\build_spec_generator\Annotation\Storage'
    );
    $this->alterInfo('storage_info');
    $this->setCacheBackend($cache_backend, 'storage_plugins');
  }

}
