# Introduction
The sowidu API allows you to interact with data though simple REST API.
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
