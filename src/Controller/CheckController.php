<?php

namespace Drupal\css_hot_reload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns the filemtime of each requested CSS file.
 */
class CheckController extends ControllerBase {

  public function check(Request $request) {
    $paths_param = $request->query->get('paths', '');
    $paths = $paths_param ? explode(',', $paths_param) : [];

    $result = [];
    foreach ($paths as $path) {
      $path = urldecode($path);
      $clean_path = parse_url($path, PHP_URL_PATH);
      if (!$clean_path) {
        continue;
      }

      $real_path = DRUPAL_ROOT . '/' . ltrim($clean_path, '/');
      if (is_file($real_path)) {
        $result[$path] = filemtime($real_path);
      }
    }

    $response = new JsonResponse($result);
    $response->headers->set('Cache-Control', 'no-store, must-revalidate');
    return $response;
  }

}