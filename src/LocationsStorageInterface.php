<?php

namespace Drupal\custom_migration_mongo;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\custom_migration_mongo\Entity\LocationsInterface;

/**
 * Defines the storage handler class for Location entities.
 *
 * This extends the base storage class, adding required special handling for
 * Location entities.
 *
 * @ingroup custom_migration_mongo
 */
interface LocationsStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Location revision IDs for a specific Location.
   *
   * @param \Drupal\custom_migration_mongo\Entity\LocationsInterface $entity
   *   The Location entity.
   *
   * @return int[]
   *   Location revision IDs (in ascending order).
   */
  public function revisionIds(LocationsInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Location author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Location revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
