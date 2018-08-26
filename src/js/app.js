/**
 * IMPORTS
 */
import Vue from 'vue';
import Pusher from 'pusher-js';
//import _ from 'lodash';


var version = '0.1.0';
var banner = `
*******************
QUEUE SERVER V${version}
*******************
`;

console.log(banner);


//import QueueTableComponent from './vue/QueueTableComponent.vue';
//Vue.component('queue-table', require('./vue/QueueTableComponent.vue').default );
import QueueTableComponent from './vue/QueueTableComponent.vue'
Vue.component('queue-table', QueueTableComponent);

const app = new Vue({
  el: '#app',
  delimiters: ['${', '}'],
  data: {
    status: {},
    webSocket: null,
    webSocketData: null,
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


  	initWebSocket: function(){
		this.webSocket = new WebSocket('ws://localhost:8080/status');
		var th = this;

		this.webSocket.onopen = function () {
		  th.webSocket.send("status");
		};

		// Log errors
		th.webSocket.onerror = function (error) {
		  console.log('WebSocket Error ' + error);
		};

		// Log messages from the server
		th.webSocket.onmessage = function (e) {
			console.log(e.data);
			//th.jobStatusHash = e.data;

			try{
                th.webSocketData = JSON.parse(e.data);
			}catch(e){
                th.webSocketData = e.data;
			}

            console.log(th.webSocketData);

            //th.updateLogs();


            /*
            if(th.jobStatusHashLast != th.jobStatusHash){
                //Something changed, update the stats!
                th.jobStatusHashLast = th.jobStatusHash;
            }
            */


		  	//console.log('Server: ' + e.data);
        };

  	},

  	//Automatic status update
  	autoUpdate: function(){
	  	this.updateLogs();
	  	//setTimeout(this.autoUpdate, 5000);
  		this.initPusher();
  	},

  	initPusher: function(){
  		var th = this;


        //console.log(data.message);
        //th.$emit("status_updated", data);

  	}
  },
  mounted: function(){

  	this.autoUpdate();

  	this.initWebSocket();
  }
});

