#HTTP API - working with files on server

HTTP API on Silex.

## Deploy

The project contains a ready for use Docker (for Mac users, I didn't test it on another OS).
Just do:

```
git clone https://github.com/NikitaBonachev/http-api-files.git
cd http-api-files
docker-compose run app
```

Docker will setup a container with Apache 2 and MySQL.
 
###note
 File `config.php` contains configuration for databases (dev and test). 
 If you would like to change something in  `config.php` be sure, that `docker-compose.yml` has the same changes.
 
## API Documentation
 I've created a documentation on [Postman](https://documenter.getpostman.com/collection/view/1593302-997f6d66-aa8c-df96-328f-8277a759aee5), you can use examples from it.
 Please, be careful with POST-requests from Postman-examples.  
 
 For instance, this request from Postman doesn't work properly:
 
 ```
curl --request POST \
  --url 192.168.99.100/files \
  --header 'content-type: multipart/form-data; boundary=---011000010111000001101001' \
  --form 'upload_file=@test.txt'
```
 But this one works well:
 
  ```
 curl --request POST \
   --url 192.168.99.100/files \
   --header 'content-type: multipart/form-data;' \
   --form 'upload_file=@test.txt'
 ```
### Methods
 
* **Get list of files**

```
GET /files
```

Return the list of files on the server.
Example response:
```
 {
   "list": [
     {
       "id": "1",
       "name": "test1.xlsx"
     },
     {
       "id": "2",
       "name": "test2.txt"
     }
   ]
 }
 ```
 
 * **Get one file by ID**
 
```
GET /files/{{id}}
```
Return a file.

 * **Get meta-data of a file**
```
GET /files/{{id}}/meta
```
Return meta-data of file.
Example response:
```
{
    "name": "image.png",
    "size": 60263,
    "mime_type": "image/png",
    "md5": "ee72bc1838604a359927e9f58e886ce1",
    "creation_time": "Sun, 26 Feb 2017 13:28:20 +0500",
    "last_access": "Sun, 26 Feb 2017 16:27:48 +0500",
    "modification_time": "Sun, 26 Feb 2017 13:33:17 +0500"
}
```
* **Create a new file**
```
POST /files
Headers:
Content-Type: multipart/form-data
Body:
upload_file: file
```
Return ID of new file.
Example response:
```
{"id":"3"}
```
* **Update a file by ID.**
This method updates only content of a file. The name will be the same (perhaps it's a little bit wrong to use POST here). 
```
POST /files/{{id}}/content
Headers:
Content-Type: multipart/form-data
Body:
upload_file: file
```
Return ID of file.
Example response:
 ```
{"id":"2"}
```
* **Update a file name by ID.**
Update only the name of the file. 
```
PUT /files/{{id}}/name
Body:
{
    "name" : "new_file_name.txt"
}
```
Return ID of file.
Example response:
 ```
{"id":"1"}
```
* **Delete file by ID.**

```
DELETE /files/{{id}}
```
Return nothing.
 
### Errors

Errors have the following format:

```
{
  "code": <<ErrorCode>>,
  "message": <<ErrorMessage>>,
  "request": <<Request content if exist>>
}
```
 
## Testing
 
 PHPUnit tests use a local database on your computer, please, write its settings on `config.php` (lines 12-15).
 Then, on the terminal from your local machine:
  ```
  phpunit --coverage-html log/coverage
 ```
## Other 
 
 * I know, that passwords and other secrets should not be commited, but I did it for your convenience.
 * I didn't implement authorization, but I would like to do it through token. 
 At branch named `auth` I did a simple Basic Authorization with one user (it's not very interesting and completed).