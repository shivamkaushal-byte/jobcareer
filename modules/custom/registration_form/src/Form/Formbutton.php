<?php

namespace Drupal\registration_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class Formbutton extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_button';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;


    // $node = \Drupal::routeMatch()->getParameter('node');
    // $nid = $node->id();
    // $userCurrent = \Drupal::currentUser();
    // $user = \Drupal\user\Entity\User::load($userCurrent->id());
    // $uid= $user->get('uid')->value;
    // $node = \Drupal::routeMatch()->getParameter('node');
    // $nid = $node->id();
    // $conn = Database::getConnection();
    // $result = $conn->select('registration_form', 'r')
    //         ->fields('r',array( 'userid','nid'))
    //         ->condition('r.userid',$uid,'=')
    //         ->condition('r.nid',$nid,'=')
    //         ->execute()->fetchAll();
    //         $result->allowRowCount = TRUE;
    //         $count = count($result);
    //  print_r($count);die;

    $form['userid'] = [
     '#type' =>'hidden',
     '#title'=>'userid',
     '#value'=>$uid,
   ];
   $form['nid'] = [
     '#type' => 'text',
     '#title' =>'nid',
     '#value' =>$nid,
   ];

    $form['actions']['submit_1'] = array(
      '#type' => 'submit',
      '#value' => $this->t('apply'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node->id();
  //  kint($node);
    $conn = Database::getConnection();
    $result = $conn->select('registration_form', 'r')
            ->fields('r',array( 'userid','nid'))
            ->condition('r.userid',$uid,'=')
            ->condition('r.nid',$nid,'=')
            ->execute()->fetchAll();
            $result->allowRowCount = TRUE;
            $count = count($result);
    //  print_r($count);die;
  if($count<1){
    $url = \Drupal\Core\Url::fromRoute('registration_form.form')
          ->setRouteParameters(array('nid'=>$nid));
    $form_state->setRedirectUrl($url);
  }
  else{
    
    $url = \Drupal\Core\Url::fromRoute('registration_form.edit')
          ->setRouteParameters(array('uid'=>$uid,'nid'=>$nid));

    $form_state->setRedirectUrl($url);
    drupal_set_message(t('you have already applied for this job'), 'status', TRUE);
  }

  }




}
