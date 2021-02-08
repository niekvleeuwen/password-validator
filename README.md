# Password Validator
Validate passwords in a breeze

## How to use

Send a POST request to `/api/validate/` with the following body

```json
{
	"password": "password"
}
```

The response should look like this for `password`.

```json
{
  "result": false,
  "errors": [
    "Password must include at least one number",
    "Password must include at least one uppercase",
    "Password must include at least one symbol",
    "Password found in 3861493 breach(es)."
  ]
}
```