<?php

namespace Drupal\custom_migration_mongo\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Location entities.
 */
class LocationsViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
