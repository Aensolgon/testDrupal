<?php

namespace Drupal\ex_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Наследуемся от базового класса Form API
 * @see \Drupal\Core\Form\FormBase
 */
class ExForm extends FormBase
{

  // метод, который отвечает за саму форму - кнопки, поля
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ваше имя'),
      '#description' => $this->t('Имя не должно содержать цифр'),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ваша фамилия'),
      '#description' => $this->t('Фамилия не должна содержать цифр'),
      '#required' => TRUE,
    ];

    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Тема'),
      '#required' => TRUE,
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#attributes' => [
        'style' => 'width: 54%'
      ],
      '#title' => $this->t('Сообщение'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#description' => $this->t('example@gmail.com'),
      '#required' => TRUE,
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Отправить форму'),
    ];

    return $form;
  }

  // метод, который будет возвращать название формы
  public function getFormId()
  {
    return 'ex_form';
  }

  // ф-я валидации
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $email = $form_state->getValue('email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('email', $this->t('Некорректный email %email.', ['%email' => $email]));
    }
  }

  // действия по сабмиту
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

    $email = $form_state->getValue('email');
    $firstname = $form_state->getValue('first_name');
    $lastname = $form_state->getValue('last_name');


    $url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/" . $email . "/?hapikey=9a06ee58-98b9-43d8-830f-a8e1b5deea19";

    $data = array(
      'properties' => [
        [
          'property' => 'firstname',
          'value' => $firstname
        ],
        [
          'property' => 'lastname',
          'value' => $lastname
        ]
      ]
    );

    $json = json_encode($data, true);

    $response = \Drupal::httpClient()->post($url . '&_format=hal_json', [
      'headers' => [
        'Content-Type' => 'application/json'
      ],
      'body' => $json
    ]);
    if ($response->getStatusCode() == '200') {
      drupal_set_message(t('Успешно отправлено! %email.', ['%email' => $email]));
    }

  }

}
