<?php

namespace Drupal\build_spec_generator\Client;

use Drupal\Core\Site\Settings;
use Google\Client;

/**
 * GoogleClient service.
 */
class GoogleClient {

  /**
   * Directory that holds credentials.json and token.json files.
   *
   * @var string $directory
   */
  public $directory;

  /**
   * GoogleClient constructor.
   */
  public function __construct() {
    $this->directory = Settings::get('google_credentials_directory');
  }

  /**
   * Get the Google API client.
   */
  public function getClient() {
    $client = new Client();
    $client->setApplicationName('Build Spec Generator');
    $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);

    $file_path = $this->credentialsFilePath();
    $client->setAuthConfig($file_path);
    $client->setAccessType('offline');

    // Load token from previous authentications.
    $token_path = $this->tokenFilePath();
    if (file_exists($token_path)) {
      $token = json_decode(file_get_contents($token_path), TRUE);
      $client->setAccessToken($token);
    }
    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
      // Refresh the token if possible, else fetch a new one.
      if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
      }
      else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
          throw new Exception(join(', ', $accessToken));
        }
      }
      // Save the token to a file.
      if (!file_exists(dirname($token_path))) {
        mkdir(dirname($token_path), 0700, true);
      }
      file_put_contents($token_path, json_encode($client->getAccessToken()));
    }
    return $client;
  }

  /**
   * Get the Google API credentials file path.
   *
   * @return string
   *   Path of credential files from Google API
   */
  public function credentialsFilePath() {
    return $this->directory . '/credentials.json';
  }

  /**
   * Get the token file path.
   *
   * @return string
   *   Path of token file stored by Google API.
   */
  public function tokenFilePath() {
    return $this->directory . '/token.json';
  }

}
