<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $node = \Drupal::routeMatch()->getParameter('node');

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t('We will send updates to the email address you provide.'),
      '#required' => true,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $node == null ? 0 : $node->id(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $submitted_email = $form_state->getValue('email');
    if (!(\Drupal::service('email.validator')->isValid($submitted_email))) {
      $form_state->setErrorByName('email', $this->t(
        'It appears that %mail is not a valid email. Please try again.',
        ['%mail' => $submitted_email]
      ));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uid = \Drupal::currentUser()->id();
    $nid = $form_state->getValue('nid');
    $email = $form_state->getValue('email');
    $timestamp = \Drupal::time()->getRequestTime();

    try {
      $query = \Drupal::database()->insert('rsvplist');
      $query->fields(['uid', 'nid', 'mail', 'created']);
      $query->values([$uid, $nid, $email, $timestamp]);
      $query->execute();
      $this->messenger()->addMessage(t(
        'Thank you for your RSVP, you are on the list for the event.'
      ));
    } catch (\Exception $e) {
      $this->messenger()->addError(t(
        'Unable to save RSVP settings at this time due to database error.
         Please try again.'
      ));
    }
  }
}
