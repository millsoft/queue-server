<template>
<div>


    <table v-if="jobs.length" class="table table-hover head-dark">
        <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Priority</th>
                <th>Context</th>
                <th>Output</th>
                <th>Added</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="job in jobs">
                <td class="icons">
                    <div v-if="job.worker_status == 0"><i class="ti-time"></i></div>
                    <div v-if="job.worker_status == 1" class="icon working"></div>
                    <div v-if="job.worker_status == 3"><i class="ti-check green"></i></div>
                    <div v-if="job.worker_status == 99"><i class="ti-close red"></i></div>
                </td>
                <td>{{job.id}}</td>
                <td>{{job.priority}}</td>
                <td>{{job.context}}</td>
                <td>{{job.output}}</td>
                <td>{{job.time_added}}</td>
                <td>
                    <a class="btn btn-default" @click.prevent="openJob(job)">open</a>
                </td>
            </tr>
        </tbody>
    </table>
    <div v-else>
        No Jobs
    </div>


</div>
</template>

<script>
    export default {
        props: ["jobid", "limit"],
        data () {
            return {
                jobs: [],
                config: {
                    //How many items should be displayed?
                    limit: 2,

                    //Get new data every x seconds
                    autoupdate: 0
                }

            }
        },
        methods: {
            loadJobs: function(){
                var th = this;


                if(this.jobid == 'summary'){
                    $.getJSON('jobs/get/summary', function(d){
                        th.jobs = d.data.jobs;
                    });
                }else{
                    var params = {
                        id: this.jobid,
                        limit: this.config.limit,
                    };

                    $.post('jobs/get', params , function(d){
                        th.jobs = d.data;
                    }, 'json');
                }

            },

            /**
             * Open job in modal
             * @param job
             */
            openJob(job){
                console.log("opening modal");
                this.$root.$emit("job_modal_open", {
                    job: job
                });
            },

            //Auto reload for data
            autoUpdate(){
                if(this.config.autoupdate > 0){
                    setInterval(this.loadJobs, this.config.autoupdate * 1000);
                }
            },

            loadConfig(){
                this.config.limit = this.limit || this.config.limit;
                this.config.autoupdate = this.autoupdate || this.config.autoupdate;
            }

            },

        mounted() {

            var th = this;

            //Load the configuration from props
            this.loadConfig();

            //Load the jobs via ajax
            this.loadJobs();

            //Start the autoupdater
            this.autoUpdate();

            this.$root.$on("status_updated", function(data){
                th.loadJobs();
            });
        }
    }
</script>