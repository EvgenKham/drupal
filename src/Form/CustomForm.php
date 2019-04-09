<?php

namespace Drupal\form_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomForm extends FormBase {
	
	public $properties = [];
 
	public function getFormId() {
		return 'form_module_custom_form';
	}
 
	public function buildForm(array $form, FormStateInterface $form_state) {
  
		$form['firstname'] = [
			'#type' => 'textfield',
			'#title' => $this -> t('FirstName'),
			'#description' => $this->t('Enter your firstname'),
		];
		
		$form['lastname'] = [
			'#type' => 'textfield',
			'#title' => $this -> t('LastName'),
			'#description' => $this->t('Enter your lastname'),
		];
	
		$form['subject'] = [
			'#type' => 'textfield',
			'#title' => $this -> t('Subject'),
			'#description' => $this->t('Subject'),
		];
		
		$form['message'] = [
			'#type' => 'textarea',
			'#title' => $this -> t('Message'),
			'#description' => $this->t('Message'),
		];
		
		$form['email'] = [
			'#type' => 'email',
			'#title' => $this->t('E-mail'),
			'#description' => $this->t('Enter Your Email.'),
		];

		$form['submit'] = [
			'#type' => 'submit',
			'#value' => $this->t('Submit'),
		];
		
		return $form;
	}
 
	public function validateForm(array &$form, FormStateInterface $form_state) {
		parent::validateForm($form, $form_state);
		$email = $form_state->getValue('email');	
		
		// $start = strripos($email, '@');
		// $domen = substr($email, $start);
		// if (strpos($domen,'.')=== FALSE)
		
		if (strpos($email,'.com')=== FALSE) {
			$form_state->setErrorByName('email', $this->t('Email must finished .com'));
		}
	}
  
	public function submitForm(array &$form, FormStateInterface $form_state) {
	
		$message = $form_state->getValue('message');

		$message = wordwrap($message, 50, "\r\n");

		$subject = $form_state->getValue('subject');

		$result = mail('admin@gmail.com', $subject, $message);

		if($result) {

			\Drupal::logger('my_form')->notice('Mail is sent. E-mail: '.$form_state->getValue('email'));

			drupal_set_message('E-mail is sent!');
		}
	
		$email = $form_state->getValue('email');
		$firstname = $form_state->getValue('firstname');
		$lastname = $form_state->getValue('lastname');

		$url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."/?hapikey=62c6e162-1f3e-40eb-aa07-************";

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
		
		$json = json_encode($data,true);
		
		$request = \Drupal::httpClient()->post($url, NULL, $json);

		$response = \Drupal::httpClient()->post($url.'&_format=hal_json', [
				'headers' => [
				'Content-Type' => 'application/json'
			],
			'body' => $json
		]);
	}
}
