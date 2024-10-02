<?php

/**
 * @file
 * Form to RSVP w/ email.
 */

namespace Drupal\rsvp_list\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {
  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'rsvp_list_email_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['email'] = [
      '#type' => 'email',
      '#title' => t('Enter email to RSVP'),
      '#description' => t('Email updates will be sent to this address'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    $node = \Drupal::routeMatch()->getParameter('node');
    $node_id = is_null($node) ? 0 : $node->id();

    $form['node_id'] = [
      '#type' => 'hidden',
      '#value' => $node_id,
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    if (!\Drupal::service('email.validator')->isValid($email)) {
      $form_state->setErrorByName(
        'email',
        t('The email address %mail is not valid. Use the format user@example.com.', [
          '%mail' => $email,
        ])
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $email = $form_state->getValue('email');
      $uid = \Drupal::currentUser()->id();
      $db = \Drupal::service('database');
      $db->insert('rsvp_list')
        ->fields([
          'email' => $email,
          'nid' => $form_state->getValue('node_id'),
          'uid' => $uid,
        ])
        ->execute();
      \Drupal::messenger()->addMessage(
        t('Successfully RSVP\'d with %email.', ['%email' => $email])
      );
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(
        t('Database could not be reached. Please try again later.')
      );
    }
  }
}
