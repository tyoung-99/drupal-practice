<?php

/**
 * @file
 * Provide sites administrators with a list of all the RSVP List signups so they
 * know who is attending their events
 */

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;

class ReportController extends ControllerBase {

  /**
   * Gets and returns all RSVPs for all Nodes.
   * These are returned in an associative array, with each row containing the
   * username, the node title, and the email of the RSVP.
   *
   * @return array|null;
   */
  protected function load() {
    try {
      $query = \Drupal::database()->select('rsvplist', 'r');

      $query->join('users_field_data', 'u', 'r.uid = u.uid');
      $query->join('node_field_data', 'n', 'r.nid = n.nid');

      $query->addField('u', 'name', 'username');
      $query->addField('n', 'title');
      $query->addField('r', 'mail');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      \Drupal::messenger()->addError(t('Unable to access the database at this
      time. Please try again later.'));
      return null;
    }
  }

  /**
   * Creates the RSVPList report page.
   *
   * @return array
   * Render array for the RSVPList report output.
   */
  public function report() {
    $content = ['message' => [
      '#markup' => t('Below is a list of all Event RSVPs including username,
      email address, and the name of the event they will be attending.'),
    ]];

    $headers = [
      t('Username'),
      t('Event'),
      t('Email'),
    ];

    $table_rows = $this->load();

    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $table_rows,
      '#empty' => t('No entries available.'),
    ];

    $content['#cache']['max-age'] = 0;

    return $content;
  }
}
