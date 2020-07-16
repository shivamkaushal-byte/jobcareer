<?php

namespace Drupal\registration_form\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Cache\Cache;


class registrationformForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'register_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL){
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $uid= $user->get('uid')->value;

    $form['description'] = [
      '#type'=>'textfield',
      '#title' => t('description'),
      '#required' => TRUE,
    ];
    $form['resume'] = [
      '#type' => 'managed_file',
      '#title' => t('upload resume'),
      '#upload_location' => 'public://',
      '#upload_validators' => [
       'file_validate_extensions' => ['pdf'],
      ],
      '#required' => TRUE,
];

    $form['userid'] = [
      '#type' =>'hidden',
      '#title'=>'userid',
      '#value'=>$uid,
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#title' =>'nid',
      '#default_value'=>$nid,
    ];

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('submit'),
      '#button_type' => 'primary',
    );

    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

    $form_file = $form_state->getValue('resume', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
    $file = File::load($form_file[0]);
    $file->setPermanent();
    $file->save();

}

    $conn = Database::getConnection();
    $conn->insert('registration_form')->fields(
      array(
        'description' => $form_state->getValue('description'),
       'resume' => $form_file[0],
       'userid'=>$form_state->getValue('userid'),
       'nid' => $form_state->getValue('nid'),
      )

      )
      ->execute();

      drupal_set_message(t('Form Submitted Successfully'), 'status', TRUE);
       $form_state->setRedirect('<front>');
    }

}
