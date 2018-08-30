/**
 * IMPORTS
 */
import Vue from 'vue';

var version = '0.1.0';
var banner = `
*******************
QUEUE SERVER V${version}
*******************
`;

console.log(banner);

import QueueTableComponent from './vue/QueueTableComponent.vue';
import QueueModalComponent from './vue/QueueModalComponent.vue';


Vue.component('queue-table', QueueTableComponent);
Vue.component('queue-modal', QueueModalComponent);

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
        updateLogs: function () {
            var th = this;

            $.getJSON('jobs/status', function (d) {
                th.status = d.data;
            });
        },


        initWebSocket: function () {
            var th = this;

            if(typeof WEBSOCKET_URL === "undefined" || WEBSOCKET_URL.length == 0){
                //websocket is not for this installation enabled
                return false;
            }

            try {
                this.webSocket = new WebSocket(WEBSOCKET_URL + '/status');

                this.webSocket.onopen = function () {
                    th.webSocket.send("status");
                };

                // Log errors
                th.webSocket.onerror = function (error) {
                    console.log('WebSocket Error ' + error);
                };

                // Log messages from the server
                th.webSocket.onmessage = function (e) {
                    try {
                        th.webSocketData = JSON.parse(e.data);

                        if(th.webSocketData.event == 'update'){
                            th.status = th.webSocketData.data;
                            th.$root.$emit("status_updated");
                        }

                    } catch (e) {
                        th.webSocketData = e.data;
                    }


                };

            } catch (e) {
                console.log(e);
            }

        },

    },
    mounted: function () {
        this.updateLogs();
        this.initWebSocket();
    }
});

