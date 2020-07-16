<?php

namespace Drupal\registration_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *Provides a 'demomoduleBlock' set socket set_socket_block
 *
 *@Block(
 *  id = "registrationformBlock",
 *  admin_label = @translation("registrationformBlock"),
 *)
 */
class registrationformBlock extends BlockBase {
  public function build() {
    $form = \Drupal::formbuilder()->getForm('Drupal\registration_form\Form\registrationformForm');
    return $form;
  }

}
?>
