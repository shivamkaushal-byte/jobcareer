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

    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;


    $conn = Database::getConnection();
    $result = $conn->select('registration_form', 'r')
            ->fields('r',array( 'description','resume','userid','nid'))
            ->condition('r.userid',$uid,'=')
            ->condition('r.nid',$nid,'=')
            ->execute()->fetchAll();
    $file = File::load($result[0]->resume);
    $fid = $result[0]->resume;

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
        '#default_value' => [$fid],
  ];
      $form['userid'] = [
       '#type' =>'hidden',
       '#title'=>'userid',
       '#default_value'=>$uid,
     ];
     $form['nid'] = [
       '#type' => 'hidden',
       '#title' =>'nid',
       '#default_value' =>$nid,
     ];

     $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('update'),
      '#button_type' => 'primary',
  );

  return $form;
}

  public function submitForm(array &$form, FormStateInterface $form_state,$uid = NULL) {
    $form_file = $form_state->getValue('resume', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
    $file = File::load($form_file[0]);
    $file->setPermanent();
    $file->save();

}
   $conn = Database::getConnection();
   $conn->update('registration_form')
	->fields([
		'description' => $form_state->getValue('description'),
    'resume' => $form_file[0],
	])
  ->condition('userid' ,$form_state->getValue('userid'),'=')
  ->condition('nid' ,$form_state->getValue('nid'),'=')
	->execute();

    drupal_set_message(t('Form updated Successfully'), 'status', TRUE);
    $form_state->setRedirect('<front>');
  }
}
