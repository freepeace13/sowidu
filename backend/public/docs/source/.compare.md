---
title: Sowidu - API Reference

language_tabs:

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->


# Introduction
The spotlocator API allows you to interact with data though simple REST API.
# Headers
Make sure you have the following content type headers set on every request

`Accept: application/json` `Content-Type: application/json`
# Errors
We use conventional HTTP response codes to indicates the success or failure of an API request.

| Code   | Description                                                                 |
| ------ | --------------------------------------------------------------------------- |
| 200    | Everything is ok.                                                           |
| 422    | The payload has missing required parameters or invalid data was given.      |
| 404    | The request resource could not be found.                                    |
| 401    | No valid API token was given.                                               |
| 500    | Request failed due to an internal error.                                    |
<!-- END_INFO -->

#Authentication
<!-- START_c3fa189a6c95ca36ad6ac4791a873d23 -->
## Login
The `access_token` should be included in `Authorization: Bearer + ACCESS_TOKEN_HERE` header
the next time you make request in authorized endpoint.

### HTTP Request
`POST api/login`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    grant_type | string |  required  | `password`
    client_id | integer |  required  | Valid oauth_client id
    client_secret | string |  required  | Valid oauth_client secret
    username | string |  required  | 
    password | string |  required  | 

<!-- END_c3fa189a6c95ca36ad6ac4791a873d23 -->

<!-- START_5f5fd9da36c7dc3490269969e82a3e3c -->
## Verify user
Use to verify user using the generated verification sent to use email/mobile

### HTTP Request
`POST api/verify/user`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    code | string |  required  | Valid user confirmation_code

<!-- END_5f5fd9da36c7dc3490269969e82a3e3c -->

<!-- START_a71ea0d11e0f96d98e0ccfe42639e0fb -->
## Send verification code
Use to send verification code to user email/mobile

### HTTP Request
`POST api/verify/user/send`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    mobile | string |  optional  | Required if the parameters `email` are not present. Valid user mobile
    email | email |  optional  | Required if the parameters `mobile` are not present. Valid user email

<!-- END_a71ea0d11e0f96d98e0ccfe42639e0fb -->

<!-- START_b7802a3a2092f162a21dc668479801f4 -->
## Reset token

Generate and send verification token to the user email

### HTTP Request
`POST api/password/email`


<!-- END_b7802a3a2092f162a21dc668479801f4 -->

<!-- START_bf3e155848ee0d06c36ae5e96443767b -->
## Login to business
Use to login with business account

### HTTP Request
`POST api/business-login`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    company_id | string |  required  | Valid company id

<!-- END_bf3e155848ee0d06c36ae5e96443767b -->

<!-- START_38739faab575f0f92e8c0eaacedf7f8e -->
## Private account
Use to get the logged-in private account of the user

### HTTP Request
`GET api/account/private`

`HEAD api/account/private`


<!-- END_38739faab575f0f92e8c0eaacedf7f8e -->

<!-- START_b50e49709aac7dba7d3b937e24eaab05 -->
## Business account
Use to get the logged-in business account of the user

### HTTP Request
`GET api/account/business`

`HEAD api/account/business`


<!-- END_b50e49709aac7dba7d3b937e24eaab05 -->

<!-- START_8d9cf7b9bc38a30e2b7f3277f468c32e -->
## Logout business
Logout from business account

### HTTP Request
`GET api/account/business/logout`

`HEAD api/account/business/logout`


<!-- END_8d9cf7b9bc38a30e2b7f3277f468c32e -->

<!-- START_ec36db88cb71323877a14885ac572e5c -->
## Confirm password
Use to confirm private account password

### HTTP Request
`POST api/account/confirm-password`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    password | string |  required  | 

<!-- END_ec36db88cb71323877a14885ac572e5c -->

#Company
<!-- START_b41fce288b735e424afbe5022ac06e41 -->
## List

Get the list logged-in user companies

### HTTP Request
`GET api/companies`

`HEAD api/companies`


<!-- END_b41fce288b735e424afbe5022ac06e41 -->

<!-- START_a242a34f0abd359a9196226970606774 -->
## Create new

Use to create new user company

### HTTP Request
`POST api/companies`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    name | string |  required  | 
    legal_form | string |  required  | Valid legal_form id
    institution_type | string |  required  | Valid institution_type id
    specialization | string |  required  | Valid specialization id
    street | string |  required  | 
    city | string |  required  | 
    zipcode | string |  required  | 
    country | string |  required  | 
    state | string |  required  | 
    terms_accepted | boolean |  required  | 

<!-- END_a242a34f0abd359a9196226970606774 -->

#Company legal forms
<!-- START_47d5aecc8abb774832a9a11a8b775ea7 -->
## List

Get the list of legal forms

### HTTP Request
`GET api/companies/legalforms`

`HEAD api/companies/legalforms`


<!-- END_47d5aecc8abb774832a9a11a8b775ea7 -->

#Company specializations
<!-- START_10fa690e02fa3f9b4f7b1f0b8a96779c -->
## List

Get the list of specializations

### HTTP Request
`GET api/companies/employee/specializations`

`HEAD api/companies/employee/specializations`


<!-- END_10fa690e02fa3f9b4f7b1f0b8a96779c -->

#Company types
<!-- START_abb19c3f6b710d4f3139d7f7c7dff2bf -->
## List

Get the list of institution types

### HTTP Request
`GET api/companies/types`

`HEAD api/companies/types`


<!-- END_abb19c3f6b710d4f3139d7f7c7dff2bf -->

#Contact
<!-- START_42c64d193367fe439a5731134382df92 -->
## Public cards

Get the unowned contact cards and not yet added as contact

### HTTP Request
`GET api/contact/all`

`HEAD api/contact/all`


<!-- END_42c64d193367fe439a5731134382df92 -->

<!-- START_3f70593ef25a8c62817017fca3f16818 -->
## Create company contact

Use to create unverified company contact

### HTTP Request
`POST api/contact/company`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    name | string |  required  | 
    email | email |  required  | 
    legal_form | string |  optional  | Valid legal_form id
    institution_type | string |  optional  | Valid institution_type id
    mobile | string |  optional  | 
    street | string |  optional  | 
    house_number | string |  optional  | 
    city | string |  optional  | 
    zipcode | string |  optional  | 
    country | string |  optional  | 
    state | string |  optional  | 
    avatar | image |  optional  | Must be an image (jpeg, png, bmp, gif, or svg) Allowed mime types: `jpeg`, `png`, `jpg` or `svg` Maximum: `2048`

<!-- END_3f70593ef25a8c62817017fca3f16818 -->

<!-- START_cb2c80ede21aff68ea676444db7bb6dc -->
## Create private contact

Use to create unverified private contact

### HTTP Request
`POST api/contact/person`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    first_name | string |  required  | 
    last_name | string |  required  | 
    email | email |  required  | 
    gender | string |  optional  | `male` or `female`
    street | string |  optional  | 
    house_number | string |  optional  | 
    city | string |  optional  | 
    zipcode | string |  optional  | 
    country | string |  optional  | 
    state | string |  optional  | 
    landline | string |  optional  | 
    mobile | string |  optional  | 
    fax | string |  optional  | 
    avatar | image |  optional  | Must be an image (jpeg, png, bmp, gif, or svg) Allowed mime types: `jpeg`, `png`, `jpg` or `svg` Maximum: `2048`

<!-- END_cb2c80ede21aff68ea676444db7bb6dc -->

#Countries
<!-- START_aafeee2abecc709c2f882dc5236f76c0 -->
## Dialling

Get the countries dialling code

### HTTP Request
`GET api/countries/dialling`

`HEAD api/countries/dialling`


<!-- END_aafeee2abecc709c2f882dc5236f76c0 -->

#Registration
<!-- START_d7b7952e7fdddc07c978c9bdaf757acf -->
## Register

Use to create new account

### HTTP Request
`POST api/register`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    first_name | string |  required  | 
    last_name | string |  required  | 
    username | string |  required  | 
    email | email |  optional  | Required if the parameters `mobile` are not present.
    mobile | string |  optional  | Required if the parameters `email` are not present.
    gender | string |  required  | 
    password | string |  required  | Minimum: `6`

<!-- END_d7b7952e7fdddc07c978c9bdaf757acf -->

