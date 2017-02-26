#HTTP API - working with files on server

## Deploy

The project contains a ready for use Docker (for Mac users, I didn't test it on another OS).
Just do 

```
git clone https://github.com/NikitaBonachev/xsolla-test.git
docker-compose run app
```

Docker will setup a container with Apache and MySQL.
 
###note
 File `config.php` contains settings for databases (dev and test). 
 If you would like to change something in  `config.php` be sure, that `docker-compose.yml` has the same changes.
 
## API Documentation
 I've created a documentation on [Postman](https://documenter.getpostman.com/collection/view/1593302-997f6d66-aa8c-df96-328f-8277a759aee5), you can use examples from it.
 Please, be careful with POST-requests from Postman-examples.  
 
 For example, this request from Postman doesn't work properly
 
 ```
curl --request POST \
  --url 192.168.99.100/files \
  --header 'content-type: multipart/form-data; boundary=---011000010111000001101001' \
  --form 'upload_file=@test.txt'
```
 And this works well
 
  ```
 curl --request POST \
   --url 192.168.99.100/files \
   --header 'content-type: multipart/form-data;' \
   --form 'upload_file=@test.txt'
 ```
### Methods
 
 * Get list of files
 ```
GET /files
```
Return list of files on the server.
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

 * Get meta-data of file
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
* **Update file by ID.**
This method update only content of a file. The name will be the same (may be it's a little bit wrong to use POST here). 
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
* **Update file name by ID.**
Update only the name of file. 
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

Errors has following format 

```
{
  "code": <<ErrorCode>>,
  "message": <<ErrorMessage>>,
  "request": <<Request content if exist>>
}
```
 
## Testing
 
 PHPUnit tests use a local database on your computer, please, write its settings on `config.php` (lines 12-15).
 Then, on terminal from your local machine
  ```
  phpunit --coverage-html log/coverage
 ```
 
 