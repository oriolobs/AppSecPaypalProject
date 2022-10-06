# PwPay

PwPay is a internal payments system available for all the students of the campus.

## Introduction

You are going to develop a PayPal like web application that is going to allow the students of the campus to send and request money from each other. Inside the application, the students are going to have a virtual wallet where they will be able to charge money coming from a fake bank account.

## Prerequisites

In order to be able to develop this exercise, you are going to need a local environment suited with:

1. Web server (Apache or Nginx)
2. PHP 7.4
3. MySQL
4. Composer
5. Git

Our recommendation is to use the Docker set up that we've using in class.

For those of you who cannot use Docker, our second recommendation is to use the Homestead virtual machine.

Finally, if Homestead is not for you either, you can always use a XAMPP like distribution. Just remember to use the proper version of PHP to avoid having problems with your team mates.

## Sections

PwPay is going to be composed by the following sections:

1. Landing
2. Sign Up
3. Sign In
4. Profile
5. Dashboard
6. Load Money
7. Send Money
8. Request Money
9. Transactions

### Landing

This section describes the characteristics of the landing page of the application.

| URL | Method |
| --- | ------ |
| /   | GET    |

This page is the only one that does not require users to be authenticated. You need to implement a simple landing page similar to the one you can find in the home page of [PayPal](https://www.paypal.com/es/home) where you explain the main characteristics and functionalities of PwPay.

For this section, you will need to define a base template that is going to be used across all the pages of the application. This template **must** contain at least the following blocks:

1. head - contains the title and the meta information of the page
2. styles - loads all the required CSS
3. header - contains the navigation menu
4. content
5. footer

Feel free to add as much blocks as you consider necessary.

### Sign Up

This section describes the process of registering a new user into the system.

| URL                      | Method |
| ------------------------ | ------ |
| /sign-up                 | GET    |
| /sign-up                 | POST   |
| /activate?token=12341234 | GET    |

If the user is not logged in, he must see a Sign Up link in the navigation menu of the header.

When a user access to the **/sign-up** URL you need to display the registration form. The information of the form must be sent to the same URL using a **POST** method. The registration form must contain the following inputs:

1. email - required
2. password - required
3. birthday - required
4. phone - optional

When the application receives a **POST** request to the **/sign-up** URL it must validate the information received from the form and register the user only if all the validations has passed. Check below the requirements for each one of the fields:

1. email: It must be a valid email address. Only emails from the domain **@salle.url.edu** can be used.
2. password: It must contain more than 5 characters. It must contain both upper and lower case letters. It must contain numbers. It must be stored using a hash algorithm.
3. birthday: It must be a valid date. Only users of legal age (more than 18 years) can be registered.
4. phone: It must follow the [Spanish numbering plan](https://en.wikipedia.org/wiki/Telephone_numbers_in_Spain).

If there is any error, you need to display again the form keeping all the information introduced by the user and showing all the errors below the corresponding inputs.

When a user is registered you need to send an email with an activation link. The activation link has to contain a previously generated token that will be used to identify and activate the user. Once the user's account is activated, the user should receive 20€ as a welcome give that are going to be loaded into the user's virtual wallet.

The token must be send as a query parameter in the URL (check the routing definition). Once a token is used, it must be invalidated. If a users try to use again the same token (by visiting the same link again), an error must be displayed.

### Activation email

To send an email using PHP you have multiple options. The easiest one is to use the internal [mail](https://www.php.net/manual/en/function.mail.php). On the other hand, you can look for some good libraries available at Packagist like for example [PHPMailer](https://github.com/PHPMailer/PHPMailer). To use it, you just need to install the package using Composer. In addition to installing the package, you will have yo configure an SMTP server to send you emails. Again, there are a lot of options to do it like for example:

1. [SMTPBucket](https://www.smtpbucket.com/)
2. [Sendgrid](https://sendgrid.com/)
3. [Mailgun](https://www.mailgun.com/).

Below you can find an example using PHPMailer and SMTPBucket:

```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
	//Server settings
	$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
	mail->isSMTP();                                            // Send using SMTP
	$mail->Host       = 'mail.smtpbucket.com';                    // Set the SMTP server to send through
	$mail->Port       = 8025;                                    // TCP port to connect to

	//Recipients
	$mail->setFrom('g1@pw.com', 'Mailer');
	$mail->addReplyTo('g1@pw.com', 'Information');
	$mail->addCC('g1@pw.com');
	$mail->addBCC('g1@pw.com');

	// Content
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = 'Here is the subject';
	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	$mail->send();
	echo 'Message has been sent';
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
```

### Sign In

This section describes the process of logging and log out users.

| URL                      | Method |
| ------------------------ | ------ |
| /sign-in                 | GET    |
| /sign-in                 | POST   |
| /logout                  | POST   |

If the user is not logged in, he must see a Sign In link in the navigation menu of the header.

If the user is logged in, he must see a Sign Out button in the navigation menu of the header.

When a user access to the **/sign-in** URL you need to display the login form. The information of the form must be sent to the same URL using a **POST** method. The login form must contain the following inputs:

1. email - required
2. password - required

When the application receives a **POST** request to the **/sign-in** URL it must validate the information received from the form and try to log in the user if all the validations has passed. The validations of the inputs must be exactly the same as in the registration.

If there is any error or the user does not exist, you need to display again the form keeping all the information introduced by the user and display a generic error.

**Note:** Remember that only users with an active account must be able to log in.

After logging a user in, you need to redirect the user to his dashboard described in the section 5 of this document. Also, one the user is logged in, you need to display his profile image (you will need to have a default profile) in the navigation menu. This image must be a link to the user's profile page described in the following section.

Finally, if the user clicks on the button Sign Out, you need to logout the user from the system and redirect him to the landing page.

**Note:** To implement the Sing Out, you will have to use an invisible form. The action of this form is going to be **/logout** with a **POST** method.

### Profile

This section describes the process of checking and updating the user's personal information.

| URL                      | Method |
| ------------------------ | ------ |
| /profile                 | GET    |
| /profile                 | POST   |
| /profile/security        | GET    |
| /profile/security        | POST   |

If a user try to access to one of these URLs manually without being logged in, the application must redirect him to the login page with a warning message.

When a logged user access to the URL **/profile**, you need to display a form containing the following inputs:

1. email
2. birthday
3. phone
4. profile_picture

The email, birthday and phone must be filled with the current information stored in the database. The **email address and the birthday cannot be updated** so the inputs must be disabled.

The new input **profile_picture** must allow the users to upload a profile picture. The requirements of the image are listed below:

1. It size of the image must be less than 1MB.
2. Only **png** images are allowed.
3. The image dimensions must be **400x400**. If the image is greater than the allowed dimensions it must be cropped.
4. You need to generate a unique name for the image.

When the form is submitted, you need to validate the **phone** and **profile_picture**. If there is any error, you need to display them below the corresponding input.

**Note:** All the images must be stored inside an **uploads** folder inside the public folder of the server in order to be able to display them.

Below the form, you need to display a link named **Change password** pointing to the URL **/profile/security**.

When a logged user access to the URL **/profile/security**, you need to display a reset password form containing the following inputs:

1. old_password
2. new_password
3. confirm_password

When the form is submitted, you need to do the following validations:

1. The **old_password** must match the current password stored in the database.
2. The **new_password** format must be the same used in the registration form
3. The **confirm_password** must match the value introduced in the **new_password**

If there is any error, you need to display again the form (all the inputs must be empty) and display a generic error. If all the validations has passed, the password of the user must be updated accordingly and you need to display a success message below the form.

**Note:** Remember to store the password using the same hashing algorithm than in the registration.

### Dashboard

This section describes the characteristics and requirements of the dashboard that the users are going to use to check the current balance of his virtual wallet and to see the historical of all his transactions.

| URL                      | Method |
| ------------------------ | ------ |
| /account/summary         | GET    |

If a user try to access to the dashboard without being logged in, the application must redirect him to the login page.

When a logged user access to the URL **/account/summary**, you need to display a dashboard containing the following information:

1. The current balance of the account (expressed in Euro)
2. A list containing the latest 5 transactions

Below the list you need to display two links in form of buttons. One to send money and another one to request money from a user.

The transactions are explained in detail in the following section.

### Load money

This section describes the process of adding a bank account and loading money into the virtual wallet of a user.

| URL                                 | Method |
| ----------------------------------- | ------ |
| /account/bank-account               | GET    |
| /account/bank-account               | POST   |
| /account/bank-account/load          | POST   |

If a user try to access to one of these URLs manually without being logged in, the application must redirect him to the login page with a warning message.

When a user access to the URL **/account/bank-account**, it can happen two things:

1. If the user does not any bank account registered, you need to display a form with the following fields:

* owner_name: the name of the owner of the bank account.
* iban: the international bank account number. You must use [this library](https://packagist.org/packages/jschaedl/iban-validation) to validate the IBAN.

The form must be submitted to the same URL **/account/bank-account** using a POST method. When the form is submitted, you need to validate the IBAN using the provided library. If the IBAN is not valid, you need to display again the form with a proper error message. If everything is OK, you need to add/register the bank account to the user and reload the page to show the information updated.

2. If the user has a bank account registered, you need to display the first 6 digits of the IBAN and a little form to load money into the user's virtual wallet. The form must contain only one input called **amount** and must be submitted to the URL button is pressed, the amount of money should be sent to the URL **/account/bank-account/load** using a POST method. When the form is submitted, you need to validate that the amount is a positive decimal number. If the amount is OK, you need to add it to the user's virtual wallet. If the amount is not OK, you need to display a gain the form with a proper error message.

### Send Money

This section describes the process of sending money to a user.

| URL                      | Method |
| ------------------------ | ------ |
| /account/money/send      | GET    |
| /account/money/send      | POST   |

If a user try to access to this section without being logged in, the application must redirect him to the login page.

If a logged user access to the URL **/account/money/send** you need to display a form containing the following inputs:

1. send_money_to
2. amount

The form information must be sent to the same URL using a **POST** method.

The requirements of the form fields are described below:

1. send_money_to: It must be a valid email.
2. amount: It must be a valid decimal number. It must be positive.

Once the form is submitted, you need to do the following validations:

1. Validate the format of the information
2. Check if the user has enough money to fulfil the specified amount
3. Check if the user that is going to receive the money exists (remember that only active users can receive or send money)

If there is any error, you need to display the form again.

If the transfer is finished successfully, you need to redirect the user to his dashboard with a success message.

### Request money

This section describes the process of requesting money from a user and also the process of fulfilling/accepting a request.

| URL                                 | Method |
| ----------------------------------- | ------ |
| /account/money/requests             | GET    |
| /account/money/requests             | POST   |
| /account/money/requests/pending     | GET    |
| /account/money/requests/{id}/accept | GET    |

If a user try to access to one of these URLs without being logged in, the application must redirect him to the login page.

If a logged user access to the URL **/account/money/requests** you need to display a form containing the following inputs:

1. request_money_from
2. amount

The form information must be sent to the same URL using a **POST** method.

The requirements of the form fields are described below:

1. request_money_from: It must be a valid email.
2. amount: It must be a valid decimal number. It must be positive.

Once the form is submitted, you need to do the following validations:

1. Validate the format of the information
2. Check if the user that is going to receive the request exists (remember that only active users can receive or send money)

If there is any error, you need to display the form again.

If the request is finished successfully, you need to redirect the user to his dashboard with a success message.

If a logged user access to the URL **/account/money/requests/pending** you need to display a list containing all the requests that have been done to the user and haven't been paid/accepted (the requests that have already been paid should not appear on that list).

For each request in the list, you need to display a link that will allow the user to accept the request. The link must follow the pattern specified in the routing table **/account/money/requests/{id}/accept**.

When the user clicks on a link, you need to do the following validations:

1. Check if the user is the one to whom the request is assigned to
2. Check if the user has enough money to fulfil the request

If there is any error, you need to redirect the user to the URL **/account/money/requests/pending** with an error message.

Otherwise, you need to transfer the money from the user's account to the requester's account and mark the request as accepted/paid.

### Transactions

This section describes the types of transactions that the system must register and be aware of.

| URL                      | Method |
| ------------------------ | ------ |
| /account/transactions    | GET    |

If a user try to access to this URL without being logged in, the application must redirect him to the login page.

If a logged user access to the URL **/account/transactions** you need to display a list containing all the registered transactions for that user. Newest transactions must appear first on the list.

The system should be aware and register the following transactions:

1. Send money to a user
2. Request money from a user
3. Charge money to the account

Every time one of this actions takes place in the application, you need to register it accordingly.

## Requirements

1. Use Slim as the underlying framework.
2. Use Composer to manage all the dependencies of your application.
3. Use a CSS framework to stylize the application.
4. Use MySQL as the database management system.
5. Use Git to collaborate with your team mates.
6. All the code must be uploaded to the private BitBucket repository that has been assigned to your team.
7. Each member of the team must collaborate to the project with at least 10 commits. We are going to validate this information, so keep it in mind.

## Delivery

Because you are using Git, and because we want to make this exercise as much real as possible, you are going to use annotated tags in order to release new versions of your application. You can check the official [Git documentation](https://git-scm.com/book/en/v2/Git-Basics-Tagging) to how to create and properly use tags.

This exercise is going to be delivered in three phases. As you may have noticed, this exercise is compound of 9 different sections and that they are ordered sequentially. Every two weeks, you will need to deliver a new release/version of your application containing the next three sections. You can check here the dates with all the expected deliveries.

1. **v1.0.0** on April 27 - Sections from 1 to 3
2. **v2.0.0** on May 11 - Sections from 4 to 6
3. **v3.0.0** on May 25 - All sections

The first two deliveries are only going to be considered for AC purposes. The last delivery, **v.3.0.0** is the one we are going to use to evaluate the exercise and give you the final score. This means that you can skip the first two releases, but keep in mind that you are going to sacrifice the proportional AC score.

## Evaluation

1. To evaluate the exercise, we are going use the release **v.3.0.0** of your repository.
2. On June, **all the teams** that have delivered the final release on time, will have to take an interview with the teachers.
3. In this interview we are going to validate that each team member have worked and collaborated as expected on the exercise.
4. Those team members that do not pass the interview, will have to take a small test also on June.
5. Those of you who suspend the test will need to take it again on July.

In case you do not have to take the test, you can calculate the final grade you are going to have using the following formula:

`20% AC + 80% PwPay`

In case you have to take the validation test, you can calculate the final grade you are going to have using the following formula:

`20% AC + 30% test + 50% PwPay`

**Note:** Keep in mind that for the interview, each team member must know about the whole exercise, not only about the sections that you have collaborated with.

### v1.0.0

To score the first release v1.0.0, we are going to distribute the points as follows:

1. Landing - 2p
2. Sign Up - 4p
3. Sign In - 3p
4. Extra (Code quality, design...) - 1p

### v2.0.0

To score the first release v2.0.0, we are going to distribute the points as follows:

1. Profile - 4p
2. Dashboard - 2p
3. Load Money - 3p
4. Extra (Code quality, design...) - 1p
