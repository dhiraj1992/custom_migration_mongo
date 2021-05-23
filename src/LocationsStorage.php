<?php

namespace Drupal\custom_migration_mongo;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
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
class LocationsStorage extends SqlContentEntityStorage implements LocationsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(LocationsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {locations_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {locations_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
