<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use GuzzleHttp\Client;

class TestController extends Controller
{
  /**
   * @param Request $request
   * @return mixed
   */
  public function getDataToken(Request $request)
  {

    $client = new Client(
      [
        'verify' => false,
      ]
    );

    try {
      // Permintaan POST ke endpoint
      $response = $client->post('https://sandbox.bni.co.id/api/oauth/token', [
        'headers' => [
          'Content-Type' => 'application/x-www-form-urlencoded',
          'Authorization' => 'Basic MDc0ODVmY2YtY2M0ZC00OGIyLThmZTgtZjc4NjdlN2YyNTU1OmZkMWUzMjliNjc5OTIzMGY5NWU4MjZmOTQ0N2YwZTdjZTRjNmM2NzM1MjAzYzMxZDNkMzllZTcwZmVhYTUwM2Y='
        ],
        'form_params' => [
          'grant_type' => 'client_credentials'
        ],
      ]);


      $statusCode = $response->getStatusCode();
      $body = $response->getBody()->getContents();
      $data = json_decode($body, true);

      echo "Response: \n " . $body . PHP_EOL;
    } catch (\GuzzleHttp\Exception\RequestException $e) {
      echo "HTTP Request failed\n";
      echo $e->getMessage();
    } catch (\Exception $e) {
      echo "An error occurred\n";
      echo $e->getMessage();
    }
  }
}
