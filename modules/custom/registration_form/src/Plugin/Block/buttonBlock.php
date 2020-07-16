<?php

namespace Drupal\registration_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\system_test\Controller;

/**
 *Provides a 'demomoduleBlock' set socket set_socket_block
 *
 *@Block(
 *  id = "buttonBlock",
 *  admin_label = @translation("button Block"),
 *)
 */
class buttonBlock extends BlockBase {
  public function build() {
    $form = \Drupal::formbuilder()->getForm('Drupal\registration_form\Form\Formbutton');
    return $form;
  }

protected function blockAccess(AccountInterface $account) {
  $nid = \Drupal::routeMatch()->getParameter('node');
    $node = \Drupal\node\Entity\Node::load($nid->id());
    $date = $node->get('field_deadline_to_apply')->getValue()['0']['value'];
  //  $date = $node->get('field_deadline_to_apply')->getValue();

// $current = DrupalDateTime::createFromTimestamp(date());
  //$today = Drupal\system_test\Controller::getCurrentDate();
  $current = getdate([0]);
  if($date < $current){
    return AccessResult::forbidden();
  }
  else {
    return parent::blockAccess($account);
  }
}
}
?>
