<?php

namespace Drupal\custom_migration_mongo\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\user\EntityOwnerTrait;
/**
 * Defines the Location entity.
 *
 * @ingroup custom_migration_mongo
 *
 * @ContentEntityType(
 *   id = "locations",
 *   label = @Translation("Location"),
 *   bundle_label = @Translation("Location type"),
 *   handlers = {
 *     "storage" = "Drupal\custom_migration_mongo\LocationsStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\custom_migration_mongo\LocationsListBuilder",
 *     "views_data" = "Drupal\custom_migration_mongo\Entity\LocationsViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\custom_migration_mongo\Form\LocationsForm",
 *       "add" = "Drupal\custom_migration_mongo\Form\LocationsForm",
 *       "edit" = "Drupal\custom_migration_mongo\Form\LocationsForm",
 *       "delete" = "Drupal\custom_migration_mongo\Form\LocationsDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_migration_mongo\LocationsHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\custom_migration_mongo\LocationsAccessControlHandler",
 *   },
 *   base_table = "locations",
 *   revision_table = "locations_revision",
 *   revision_data_table = "locations_field_revision",
 *   translatable = FALSE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer location entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/locations/{locations}",
 *     "add-page" = "/admin/structure/locations/add",
 *     "add-form" = "/admin/structure/locations/add/{locations_type}",
 *     "edit-form" = "/admin/structure/locations/{locations}/edit",
 *     "delete-form" = "/admin/structure/locations/{locations}/delete",
 *     "version-history" = "/admin/structure/locations/{locations}/revisions",
 *     "revision" = "/admin/structure/locations/{locations}/revisions/{locations_revision}/view",
 *     "revision_revert" = "/admin/structure/locations/{locations}/revisions/{locations_revision}/revert",
 *     "revision_delete" = "/admin/structure/locations/{locations}/revisions/{locations_revision}/delete",
 *     "collection" = "/admin/structure/locations",
 *   },
 *   bundle_entity_type = "locations_type",
 *   field_ui_base_route = "entity.locations_type.edit_form"
 * )
 */
class Locations extends EditorialContentEntityBase implements LocationsInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

//    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
//      $translation = $this->getTranslation($langcode);
//
//      // If no owner has been set explicitly, make the anonymous user the owner.
//      if (!$translation->getOwner()) {
//        $translation->setOwnerId(0);
//      }
//    }
//
//    // If no revision author has been set explicitly,
//    // make the location owner the revision author.
//    if (!$this->getRevisionUser()) {
//      $this->setRevisionUserId($this->getOwnerId());
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Location entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Location is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
