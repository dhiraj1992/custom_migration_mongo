<?php

namespace Drupal\custom_migration_mongo;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\custom_migration_mongo\Entity\Locations;


/**
 * Provides dynamic permissions for Location of different types.
 *
 * @ingroup custom_migration_mongo
 *
 */
class LocationsPermissions{

  use StringTranslationTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   *   The Locations by bundle permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function generatePermissions() {
    $perms = [];

    foreach (Locations::loadMultiple() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * Returns a list of node permissions for a given node type.
   *
   * @param \Drupal\custom_migration_mongo\Entity\Locations $type
   *   The Locations type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(Locations $type) {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "$type_id create entities" => [
        'title' => $this->t('Create new %type_name entities', $type_params),
      ],
      "$type_id edit own entities" => [
        'title' => $this->t('Edit own %type_name entities', $type_params),
      ],
      "$type_id edit any entities" => [
        'title' => $this->t('Edit any %type_name entities', $type_params),
      ],
      "$type_id delete own entities" => [
        'title' => $this->t('Delete own %type_name entities', $type_params),
      ],
      "$type_id delete any entities" => [
        'title' => $this->t('Delete any %type_name entities', $type_params),
      ],
      "$type_id view revisions" => [
        'title' => $this->t('View %type_name revisions', $type_params),
        'description' => t('To view a revision, you also need permission to view the entity item.'),
      ],
      "$type_id revert revisions" => [
        'title' => $this->t('Revert %type_name revisions', $type_params),
        'description' => t('To revert a revision, you also need permission to edit the entity item.'),
      ],
      "$type_id delete revisions" => [
        'title' => $this->t('Delete %type_name revisions', $type_params),
        'description' => $this->t('To delete a revision, you also need permission to delete the entity item.'),
      ],
    ];
  }

}
