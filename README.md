# MODX Lead Form Snippet
## Features

* Contact form
* Server-side validation
* Honeypot spam protection
* External API integration
* Saving leads to database
* Error logging
* CRM-ready structure

## Tech Stack

* MODX Revolution
* PHP
* MySQL
* cURL
* xPDO/PDO database layer

## Installation

### 1. Create database table

Run `install.sql` inside your MODX database.

### 2. Create snippet

Go to:

Elements → Snippets → New Snippet

Create snippet named:

LeadForm

Paste code from:

snippet/LeadForm.php

### 3. Create resource/page

Create a MODX Resource and add:

html
[[!LeadForm]]

<form method="post">
  <input type="text" name="name" placeholder="Name">
  <input type="email" name="email" placeholder="Email">
  <input type="text" name="phone" placeholder="Phone">
  <textarea name="message" placeholder="Message"></textarea>

  <input type="text" name="website" style="display:none">

  <button type="submit" name="lead_submit" value="1">
    Send
  </button>
</form>

### 4. Configure API endpoint

Inside `LeadForm.php` replace:

php
$apiUrl = 'YOUR_WEBHOOK_URL';

with your CRM/API endpoint.

## API Integration

The snippet sends validated form data via POST request in JSON format.

Example payload:

json
{
  "name": "Ivan",
  "email": "test@test.com",
  "phone": "+380955526463",
  "message": "Hello",
  "source": "modx_lead_form"
}

## Logging

Errors are logged using MODX logging system.

Log file:

text
core/cache/logs/error.log

## Notes

The snippet should be called uncached:

html
[[!LeadForm]]

## Possible Improvements

* Full xPDO model
* CRM integration
* Admin panel for leads
* AJAX form submission
* CSRF protection
* Caching layer

## Author

Ivan Vashchuk
