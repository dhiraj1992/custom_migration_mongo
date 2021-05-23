<?php

namespace Drupal\custom_migration_mongo\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class LocationsTypeForm.
 */
class LocationsTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $locations_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $locations_type->label(),
      '#description' => $this->t("Label for the Location type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $locations_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\custom_migration_mongo\Entity\LocationsType::load',
      ],
      '#disabled' => !$locations_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $locations_type = $this->entity;
    $status = $locations_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Location type.', [
          '%label' => $locations_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Location type.', [
          '%label' => $locations_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($locations_type->toUrl('collection'));
  }

}
