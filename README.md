# Queue Server

#### What is this?
The Queue Server is a server that can work on long taking tasks in the background. You add a "job" by simply posting a json payload to a URL. Using this queue server you can decouple long taking tasks from the frontend. You can for example use it to send hundreds of E-Mails or create many thumbnails. The use doesn't have to wait for the execution and his browser won't be blocked. After the execution the queue server can inform your application that the job was done by calling a callback URL.

#### Requirments
- PHP 7 (php cli for the server)
- MySQL
- Linux server
- Composer

#### Installation
- At first do `composer install` to install all dependencies.
- Now install the database. The sql dump with an empty database you can find in the `assets/database` folder.
- Edit `src/config.php` and specify your database there, also configure the settings if you wish.
- Copy .htaccess_dist to .htaccess, you can extend the .htaccess if you wish. 

#### Usage
##### Starting the server
Open your terminal and execute the following: `php server.php`.
Make sure that the server is always running, even if you close the terminal. I recommend to use `screen` or `byobu` or simply start the process in the backgroud if you don't care about logs: `php server.php &`

## API
With the built in API you can send jobs to the server, get the status, etc.

##### Sending jobs to the server
`POST: /jobs/add`

Use any http request library (eg. Guzzle or curl) to send a POST request to `/jobs/add`. This will store the job in the `queue` database which the queue server will execute.  For development purposes I recommend using Postman (a free API Client) The payload for the request could look like this:

```json
{
"priority" : 800,
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

The job payload is a JSON.

Each job can have the `priority` value (default = 500), the higher this value the earlier your job will be executed. You can also use a `context` - this is just a string with any name, eg. your app name, or what you are trying to do, eg "send_mailing".

There is also the `command` object. The command can be of type `http` or `exec` - These workers are stored in `src/app/workers`.
In this example the http worker will be executed to do http requests. This could be a file on a server which takes some time to execute. For example this could be a GET request to `sendmails.php?mailing=1234` which will send a lot of emails in the background. 

Once the job is done, you can use the `callback_done` callback to call another URL or execute a system command. This will inform your app that the job is done. Callbacks are not required but can be useful in most cases. 

Here is another example by using the `ExecWorker` which will execute a command on your server, The command can be literaly anything (at least anything that the www user can do). Here I also don't use the `callback_done` callback.:

```json
{
  "command": {
    "type": "exec",
    "cmd": "/home/michael/scripts/somescript.sh"
  }
}
```

##### Get the status of all jobs
`GET: /jobs/status`

Example output
```json
{
    "status": 200,
    "data": {
        "jobs_all": 80,
        "waiting": 0,
        "working": 18,
        "max_threads": 20,
        "free_threads": 2
    }
}
```

##### Get the status of a single job
`GET: /jobs/status/12345`

##### Delete a job
`GET: /jobs/delete/12345`

### Workers
A worker is a module that works on a specific task. 
There are currently 2 workers:
- HttpWorker
- ExecWorker

#### HttpWorker
The HttpWorker is a worker which creates HTTP requests. You can see an example above. You can use the HttpWorker to call a long taking script on any URL. You can also specify the request type (GET = default, POST, PUT, DELETE, etc.)

##### How to pass the job id to the worker?
Sometimes you want to send the auto generated job id to the HttpWorker URL. You can do this by doing following placeholder to the URL:
```json
{
  "command": {
    "type": "http",
    "url": "http://httpbin.org/get?jobid=__JOBID__"
  }
}
```

the `__JOBID__` placeholder will be automatically replaced with the numeric job ID. The worker "knows" then about its own ID so you can control the job inside the worker process. For example you could update the job progress - to do this, you need to know the job id. 

#### ExecWorker
The ExecWorker will execute a system command. For example you can call a bash script, compile an application, deploy your apps.


#### How to test the workers without the queue server?
Simply call `php work.php -j123` in your terminal. This is how the queue server calls each job. You can see the output directly in the console. Every output will also be logged in the queue database in the `output` column.


## Info
Project by Michael Milawski