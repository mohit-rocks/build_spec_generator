<?php

namespace Drupal\build_spec_generator\Plugin\Storage;

use Drupal\build_spec_generator\BuildSpecGenerator;
use Drupal\build_spec_generator\Client\GoogleClient;
use Drupal\build_spec_generator\Plugin\StoragePluginBase;
use Drupal\Component\FileSecurity\FileSecurity;
use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the storage plugin.
 *
 * @Storage(
 *   id = "google_sheet_storage",
 *   label = @Translation("Google Sheets Storage"),
 *   description = @Translation("Plugin to export build spec files in Google sheet.")
 * )
 */
class GoogleSheetStorage extends StoragePluginBase implements ContainerFactoryPluginInterface {
  /**
   * Drupal\build_spec_generator\BuildSpecGenerator definition.
   *
   * @var \Drupal\build_spec_generator\BuildSpecGenerator
   */
  protected $buildSpecGeneratorGenerator;

  /**
   * Drupal\build_spec_generator\Client\GoogleClient definition.
   *
   * @var GoogleClient
   */
  protected $googleClient;

  /**
   * Spreadsheet id of Google sheet.
   *
   * @var string
   */
  protected $spreadSheetId;

  /**
   * Google Spreadsheet Service.
   *
   * @var \Google_Service_Sheets
   */
  protected $googleSpreadSheetService;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,$plugin_id, $plugin_definition, BuildSpecGenerator $build_spec_generator, GoogleClient $google_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->buildSpecGeneratorGenerator = $build_spec_generator;
    $this->googleClient = $google_client;
    $this->spreadSheetId = Settings::get('google_spreadsheet_id');

    // Get the client and initiate the service.
    $client = $this->googleClient->getClient();
    $this->googleSpreadSheetService = new \Google_Service_Sheets($client);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('build_spec_generator.generator'),
      $container->get('build_spec_generator.google_client')
    );
  }

  /**
   * Export the configuration to markdown files.
   */
  public function export() {
    $config_exports = $this->buildSpecGeneratorGenerator->generate();
    foreach ($config_exports as $config_name => $config_export) {
      // Update data for each tab in Google Sheets.
      // @todo: Review if we can write all the data in single call.
      $this->updateGoogleSheetData($config_name, $config_export);
    }
  }

  /**
   * Write data to Google Spreadsheet.
   *
   * @param string $config_name
   *   Configuration name, that should match with tab name.
   * @param array $config_export
   *   Array of configuration in form of [header, rows, alignment]
   */
  public function updateGoogleSheetData(string $config_name, array $config_export) {
    // Config name works as tab name.
    $range = $config_name;

    // Fetch all the rows for one config export.
    $rows = $config_export['rows'];
    // Add header in the result set.
    array_unshift($rows , $config_export['header']);

    $body = new \Google_Service_Sheets_ValueRange();
    $body->setValues($rows);

    $params = ['valueInputOption' => 'RAW'];
    $this->googleSpreadSheetService->spreadsheets_values->update($this->spreadSheetId, $range, $body, $params);
  }

}
