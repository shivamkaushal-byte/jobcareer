<?php

namespace Drupal\registration_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;

class EditForm extends FormBase {

  public function getFormId(){
    return 'edit_form';
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state,$uid = NULL,$nid = NULL) {
  //  kint($nid);
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;
     // $node = \Drupal::routeMatch()->getParameter('node');
     // $nid = $node->id();
    // $connection = \Drupal::database();
    // $query = $connection->query("SELECT description,points FROM registration_form WHERE  userid = 1");
    // $result = $query->fetchField();

    $conn = Database::getConnection();
    $result = $conn->select('registration_form', 'r')
            ->fields('r',array( 'description','resume','userid','nid'))
            ->condition('r.userid',$uid,'=')
            ->condition('r.nid',$nid,'=')
            ->execute()->fetchAll();
     //print_r($result);die;
    //kint($result[0]->description);die;
    $file = File::load($result[0]->resume);
    $fid = $result[0]->resume;
//$uri = $file->url();

  // $uri = $file->getValue('uri')->value;
    //kint($uri);
    //dpm($max_bid);
      $form['description'] = [
        '#type'=>'textfield',
        '#title' => t('description'),
        '#required' => TRUE,
        '#default_value' => $result[0]->description,
      ];
      $form['resume'] = [
        '#type' => 'managed_file',
        '#title' => t('upload resume'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
         'file_validate_extensions' => ['pdf'],
        ],
      //  '#required' => TRUE,
        '#default_value' => [$fid],
  ];
      $form['userid'] = [
       '#type' =>'hidden',
       '#title'=>'userid',
       '#value'=>$uid,
     ];
     $form['nid'] = [
       '#type' => 'hidden',
       '#title' =>'nid',
       '#value' =>$nid,
     ];

     $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('update'),
      '#button_type' => 'primary',
  );

  return $form;
}

  public function submitForm(array &$form, FormStateInterface $form_state,$uid = NULL) {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;
    $form_file = $form_state->getValue('resume', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
    $file = File::load($form_file[0]);
   //print_r($file);die;
    $file->setPermanent();
    $file->save();

}
   $conn = Database::getConnection();
   $conn->update('registration_form')
	->fields([
		'description' => $form_state->getValue('description'),
    'resume' => $form_file[0],
	])
  ->condition('userid' ,$uid,'=')
	->execute();

      // $conn = Database::getConnection();
      // $conn->update('registration_form')
      //         ->fields(['description'])
      //         ->condition('userid',$uid,'=')
      //         ->execute();
        drupal_set_message(t('Form updated Successfully'), 'status', TRUE);
       $form_state->setRedirect('<front>');
      //  $form_state['redirect'] = 'home';
  }
}
