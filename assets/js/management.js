var app = new Vue({
  el: '#app',
  delimiters: ['${', '}'],
  data: {
    status: {},
    webSocket: null,
    jobStatusHash: null,
    jobStatusHashLast: null,
    jobs: [],
  },
  methods: {
  	updateLogs: function(){
  		var th = this;

		$.getJSON('jobs/status', function(d){
			th.status = d.data;
		});
  	},

  	//Load all jobs with status "working"
  	loadJobs: function(){
  		var th = this;
		$.getJSON('jobs/get/working', function(d){
			th.jobs = d.data;
		});
  	},

  	initWebSocket: function(){
		this.webSocket = new WebSocket('ws://127.0.0.1:8080');
		var th = this;

		this.webSocket.onopen = function () {
		  console.log("websocket.onopen");
		  //connection.send('Ping'); // Send the message 'Ping' to the server
		  th.webSocket.send("status");
		};

		// Log errors
		th.webSocket.onerror = function (error) {
		  console.log('WebSocket Error ' + error);
		};

		// Log messages from the server
		th.webSocket.onmessage = function (e) {
			th.jobStatusHash = e.data;

			if(th.jobStatusHashLast != th.jobStatusHash){
				//Something changed, update the stats!
				th.updateLogs();
				th.jobStatusHashLast = th.jobStatusHash;
			}


		  	//console.log('Server: ' + e.data);
		};


		setInterval(function(){
			th.webSocket.send("status");
		}, 1000);


  	},

  	//Automatic status update
  	autoUpdate: function(){
	  	this.updateLogs();
	  	this.loadJobs();
	  	setTimeout(this.autoUpdate, 5000);
  	}
  },
  mounted: function(){

  	this.autoUpdate();

  	//this.initWebSocket();
  }
});


$(function(){
	console.log("Queue Management - loaded");


	//startWebsocket();

});




//Start the websocket listener
function startWebsocket(){
	console.log("Starting Websocket Listener...");
	WS = new WebSocket('ws://127.0.0.1:8080');
  

	// When the connection is open, send some data to the server
	connection.onopen = function () {
	  console.log("websocket.onopen");
	  //connection.send('Ping'); // Send the message 'Ping' to the server
	  connection.send("COOL");
	};

	// Log errors
	connection.onerror = function (error) {
	  console.log('WebSocket Error ' + error);
	};

	// Log messages from the server
	connection.onmessage = function (e) {
	  console.log('Server: ' + e.data);
	};
}