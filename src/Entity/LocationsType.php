<?php

namespace Drupal\custom_migration_mongo\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Location type entity.
 *
 * @ConfigEntityType(
 *   id = "locations_type",
 *   label = @Translation("Location type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_migration_mongo\LocationsTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_migration_mongo\Form\LocationsTypeForm",
 *       "edit" = "Drupal\custom_migration_mongo\Form\LocationsTypeForm",
 *       "delete" = "Drupal\custom_migration_mongo\Form\LocationsTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_migration_mongo\LocationsTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "locations_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "locations",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/locations_type/{locations_type}",
 *     "add-form" = "/admin/structure/locations_type/add",
 *     "edit-form" = "/admin/structure/locations_type/{locations_type}/edit",
 *     "delete-form" = "/admin/structure/locations_type/{locations_type}/delete",
 *     "collection" = "/admin/structure/locations_type"
 *   }
 * )
 */
class LocationsType extends ConfigEntityBundleBase implements LocationsTypeInterface {

  /**
   * The Location type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Location type label.
   *
   * @var string
   */
  protected $label;

}
