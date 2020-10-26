<?php

namespace Drupal\build_spec_generator\Commands;

use Drupal\build_spec_generator\Plugin\StoragePluginManager;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drupal command to generate the build spec from teh configuration.
 */
class BuildSpecGeneratorCommands extends DrushCommands {

  use StringTranslationTrait;

  /**
   * Drupal\build_spec_generator\Plugin\StoragePluginManager definition.
   *
   * @var \Drupal\build_spec_generator\Plugin\StoragePluginManager
   */
  protected $storagePluginManager;

  /**
   * Constructs a new FileStorageService object.
   *
   * @param StoragePluginManager $storage
   *   File storage service to get and export the markdown files.
   */
  public function __construct(StoragePluginManager $storage) {
    parent::__construct();
    $this->storagePluginManager = $storage;
  }

  /**
   * Export build spec in markdown format based on BuildSpec plugins.
   *
   * @usage generate_build_spec
   *   Generate the configuration and export to markdown
   *
   * @command build_spec_generator:generate_build_spec
   * @aliases gen-build-spec
   * @option destination Storage plugin that we want to use. Refer storage plugins for this module. Typically we have file_storage, google_sheets etc.
   */
  public function generateBuildSpec($destination) {
    // Perform export operation.
    if ($destination == NULL) {
      $this->output()->writeln('Destination is required. It should be one of the StoragePlugin Id');
    }
    try {
      /** @var \Drupal\build_spec_generator\Plugin\StoragePluginBase $plugin_instance */
      $plugin_instance = $this->storagePluginManager->createInstance($destination);
      $plugin_instance->export();
    }
    catch (PluginNotFoundException $exception) {
      $this->output()->writeln(t('%plugin not found. Please check the name', ['plugin' => $destination]), OutputInterface::OUTPUT_NORMAL);
    }
  }

  /**
   * @hook interact build_spec_generator:generate_build_spec
   */
  public function interactSiteAliasConvert(InputInterface $input, ConsoleOutputInterface $output)
  {
    if (!$input->getArgument('destination')) {
      $storage_plugins = $this->storagePluginManager->getDefinitions();
      foreach ($storage_plugins as $storage_plugin) {
        $options[] = $storage_plugin['id'];
      }
      $destination = $this->io()->choice('Select destination storage plugin:', drush_map_assoc($options));
      $input->setArgument('destination', $destination);
    }
  }
}
