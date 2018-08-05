# Queue Server

#### What is this?
The Queue Server is a server that can work on long taking tasks in the background.

#### Requirments
- PHP 7
- MySQL
- Linux server with access via SSH
- Composer

#### Installation
- At first do `composer install` to install all dependencies.
- Now install the database, `TODO: Add schemas for database or make an installer`
- Edit `src/config.php` 

#### Usage
##### Starting the server
`php server.php`


## API
With the built in API you can send jobs to the server, get the status, delete jobs etc.

##### Sending jobs to the server
`POST: /jobs/add`

Use any http request library (eg. Guzzle or curl) to send a POST request to `/jobs/add`. This will store the job in the `queue` database which the queue server will execute. The payload for the request could look like this:

```
{
"priority" : 200,
"context" : "MyCoolApp",
  "command": {
    "type": "http",
    "url": "http://httpbin.org/delay/10",
    "method": "GET",
    "timeout": 20,
    "params": {
      "username": "michael"
    }
},
  "callback_done": {
    "type": "http",
    "url": "http://httpbin.org/get?job=done",
    "method": "GET"
  }
}
```

The job payload is a JSON which has the `command` object. The command can be of type `http` or `exec` - These workers are stored in `src/app/workers`.
In this example the http worker will be executed to do http requests. This could be a file on a server which takes some time to execute. For example this could be a GET request to `sendmails.php?mailing=1234` which will send a lot of emails in the background. 

Once the job is done, you can use the `callback_done` callback to call another URL or execute a system command. This will inform another server that the job is done. Callbacks are not mandatory but can be useful in some cases. 

##### Get the status of all jobs
`GET: /jobs/status`

##### Get the status of a single job
`GET: /jobs/status/12345`




## Info
Project by Michael Milawski
Started: 07.2018
Status: In Development