<?php

/**
 * @file
 * Provides an RSVP block to insert into select event nodes.
 */

namespace Drupal\rsvp_list\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides RSVP main block.
 */
#[Block(
  id: 'rsvp_block',
  admin_label: new TranslatableMarkup('RSVP Block'),
)]
class RSVPBlock extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\rsvp_list\Form\RSVPForm');
  }
}
