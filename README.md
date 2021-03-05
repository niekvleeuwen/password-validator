<p align="center"><a href="https://passwordvalidator.niekvanleeuwen.nl" target="_blank"><img src="https://raw.githubusercontent.com/niekvleeuwen/password-validator/master/public/img/logo.svg" width="300"></a></p>

## About Password validator

Password validator is a web application capable of validating passwords against different sources. Several techniques are also used to determine the strength of the password. Some of the functionalities offered are:

- Check if the password has been exposed in data breaches. 
- Calculate the time it takes to brute-force the password.
- Analyze passwords in bulk

## Getting started

Create a API token [here](https://passwordvalidator.niekvanleeuwen.nl). This API token is used to authenticate the request and must be send along with the request as a Bearer token. Now send a POST request to `/api/validate/` with the following body

```json
{
	"password": "password1"
}
```

The response should look like this for `password1`.

```json
{
  "result": false,
  "errors": [
    "Password found in 2427158 breach(es).",
    "Estimated brute force time is 17.41 minutes"
  ]
}
```

