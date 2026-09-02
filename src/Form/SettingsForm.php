<?php

namespace Drupal\css_hot_reload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for CSS Hot Reload.
 *
 * Uses the State API on purpose (not Config): this is a per-environment
 * dev toggle that should never be exported via config sync, which is why
 * the module ships with no config schema.
 */
class SettingsForm extends FormBase {

  protected StateInterface $state;

  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('state'));
  }

  public function getFormId() {
    return 'css_hot_reload_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CSS Hot Reload'),
      '#description' => $this->t('Only enable this locally. Injects a JS poller that swaps changed CSS files without a full page reload.'),
      '#default_value' => $this->state->get('css_hot_reload.enabled', FALSE),
    ];

    $form['interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Poll interval (ms)'),
      '#default_value' => $this->state->get('css_hot_reload.interval', 1000),
      '#min' => 200,
      '#step' => 100,
      '#states' => [
        'visible' => [
          ':input[name="enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state->set('css_hot_reload.enabled', (bool) $form_state->getValue('enabled'));
    $this->state->set('css_hot_reload.interval', (int) $form_state->getValue('interval'));
    $this->messenger()->addStatus($this->t('Settings saved.'));
  }

}