var app = new Vue({
  el: '#app',
  delimiters: ['${', '}'],
  data: {
    message: 'Hello Vue!'
  }
});


$(function(){
	console.log("Queue Management - loaded");

	startWebsocket();

});


//Start the websocket listener
function startWebsocket(){
	console.log("Starting Websocket Listener...");
	var connection = new WebSocket('ws://127.0.0.1:8099', 'data');
  

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