<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Defines HelloController class.
 */
class HelloController extends ControllerBase
{

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content()
  {

    $config = $this->config("hello_world.settings");
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello @name', ['@name' => $config->get('name')]),
    ];
  }
}
